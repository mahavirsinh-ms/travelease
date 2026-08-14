<?php
/**
 * TravelEASE PostgreSQL compatibility layer.
 * The application was originally written for mysqli/MySQL.
 * This file keeps the existing mysqli-style API while using PDO/PostgreSQL,
 * so the rest of the application can run on Render PostgreSQL with minimal changes.
 */

class TravelEaseResult {
    private array $rows = [];
    private int $index = 0;

    public function __construct(?PDOStatement $stmt = null) {
        if ($stmt) {
            $this->rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        }
    }

    public function fetch_assoc(): ?array {
        if ($this->index >= count($this->rows)) return null;
        return $this->rows[$this->index++];
    }

    public function num_rows(): int {
        return count($this->rows);
    }
}

class TravelEaseStmt {
    private PDO $pdo;
    private string $sql;
    private ?PDOStatement $stmt = null;
    private array $refs = [];

    public function __construct(PDO $pdo, string $sql) {
        $this->pdo = $pdo;
        $this->sql = travelEaseNormalizeSql($sql);
    }

    public function bind_param(string $types, &...$vars): bool {
        $this->refs = [];
        foreach ($vars as $i => &$var) {
            $this->refs[] =& $var;
        }
        return true;
    }

    public function execute(): bool {
        $this->stmt = $this->pdo->prepare($this->sql);
        foreach ($this->refs as $i => &$value) {
            $this->stmt->bindValue($i + 1, $value);
        }
        return $this->stmt->execute();
    }

    public function get_result(): TravelEaseResult {
        return new TravelEaseResult($this->stmt);
    }

    public function close(): bool {
        $this->stmt = null;
        return true;
    }
}

class TravelEaseConnection {
    public PDO $pdo;
    public int $insert_id = 0;

    public function __construct() {
        $host = getenv('DB_HOST') ?: 'localhost';
        $port = getenv('DB_PORT') ?: '5432';
        $dbname = getenv('DB_NAME') ?: 'travelease1';
        $user = getenv('DB_USER') ?: '';
        $password = getenv('DB_PASSWORD') ?: '';

        try {
            $this->pdo = new PDO(
                "pgsql:host={$host};port={$port};dbname={$dbname}",
                $user,
                $password,
                [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES => true,
                ]
            );
        } catch (Throwable $e) {
            http_response_code(500);
            die('Database connection failed. Check the Render PostgreSQL environment variables.');
        }
    }

    public function escape(string $value): string {
        return substr($this->pdo->quote($value), 1, -1);
    }
}

function travelEaseNormalizeSql(string $sql): string {
    // MySQL date arithmetic used by the admin reports/bookings pages.
    $sql = preg_replace_callback(
        "/DATE_SUB\\(NOW\\(\\),\\s*INTERVAL\\s+(\\d+)\\s+(DAY|MONTH|YEAR|HOUR)\\)/i",
        function ($m) {
            $unit = strtolower($m[2]);
            return "(NOW() - INTERVAL '{$m[1]} {$unit}')";
        },
        $sql
    );

    // TIMESTAMPDIFF(HOUR, start, NOW()) -> PostgreSQL epoch hours.
    $sql = preg_replace_callback(
        "/TIMESTAMPDIFF\\(\\s*HOUR\\s*,\\s*([^,]+)\\s*,\\s*NOW\\(\\)\\s*\\)/i",
        function ($m) {
            return "(EXTRACT(EPOCH FROM (NOW() - {$m[1]})) / 3600)";
        },
        $sql
    );

    // A few MySQL expressions are harmlessly translated here for compatibility.
    $sql = preg_replace('/\\bIFNULL\\s*\\(/i', 'COALESCE(', $sql);
    $sql = str_replace('`', '', $sql);

    return $sql;
}

function mysqli_connect($host = null, $user = null, $password = null, $database = null, $port = 5432) {
    return new TravelEaseConnection();
}

function mysqli_set_charset($conn, $charset): bool { return true; }

function mysqli_query($conn, string $sql) {
    try {
        $stmt = $conn->pdo->query(travelEaseNormalizeSql($sql));
        if (preg_match('/^\\s*(SELECT|SHOW|WITH|TABLE)\\b/i', $sql)) {
            return new TravelEaseResult($stmt);
        }
        if (preg_match('/^\\s*INSERT\\b/i', $sql)) {
            $conn->insert_id = (int)$conn->pdo->lastInsertId();
        }
        return true;
    } catch (Throwable $e) {
        error_log('Database query failed: ' . $e->getMessage() . ' SQL: ' . $sql);
        return false;
    }
}

function mysqli_fetch_assoc($result) {
    if ($result instanceof TravelEaseResult) return $result->fetch_assoc();
    return null;
}

function mysqli_num_rows($result): int {
    return ($result instanceof TravelEaseResult) ? $result->num_rows() : 0;
}

function mysqli_real_escape_string($conn, string $value): string {
    return $conn->escape($value);
}

function mysqli_insert_id($conn): int {
    return $conn->insert_id;
}

function mysqli_error($conn): string { return 'PostgreSQL query failed; see Render logs for details.'; }

function mysqli_prepare($conn, string $sql) {
    return new TravelEaseStmt($conn->pdo, $sql);
}

function mysqli_stmt_bind_param($stmt, string $types, &...$vars): bool {
    return $stmt->bind_param($types, ...$vars);
}

function mysqli_stmt_execute($stmt): bool { return $stmt->execute(); }
function mysqli_stmt_close($stmt): bool { return $stmt->close(); }

$conn = mysqli_connect(
    getenv('DB_HOST'),
    getenv('DB_USER'),
    getenv('DB_PASSWORD'),
    getenv('DB_NAME'),
    getenv('DB_PORT') ?: 5432
);
?>
