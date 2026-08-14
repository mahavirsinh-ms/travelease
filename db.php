<?php
/**
 * TravelEASE PostgreSQL compatibility layer.
 *
 * The application was originally written for mysqli/MySQL.
 * This file keeps the existing mysqli-style API while using
 * PDO/PostgreSQL, so the rest of the application can run on
 * Render PostgreSQL with minimal changes.
 */


/* ============================================================
   RESULT CLASS
   ============================================================ */

class TravelEaseResult {
    private array $rows = [];
    private int $index = 0;

    // MySQL compatibility:
    // Supports $result->num_rows
    public int $num_rows = 0;

    public function __construct(?PDOStatement $stmt = null) {
        if ($stmt) {
            $this->rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $this->num_rows = count($this->rows);
        }
    }

    public function fetch_assoc(): ?array {
        if ($this->index >= count($this->rows)) {
            return null;
        }

        return $this->rows[$this->index++];
    }

    // Also supports $result->num_rows()
    public function num_rows(): int {
        return $this->num_rows;
    }

    public function data_seek(int $offset): bool {
        if ($offset < 0 || $offset > count($this->rows)) {
            return false;
        }

        $this->index = $offset;
        return true;
    }
}

/* ============================================================
   PREPARED STATEMENT COMPATIBILITY
   ============================================================ */

class TravelEaseStmt {

    private PDO $pdo;

    private string $sql;

    private ?PDOStatement $stmt = null;

    private array $refs = [];

    private string $last_error = '';


    public function __construct(
        PDO $pdo,
        string $sql
    ) {

        $this->pdo = $pdo;

        $this->sql =
            travelEaseNormalizeSql($sql);
    }


    public function bind_param(
        string $types,
        &...$vars
    ): bool {

        $this->refs = [];

        foreach (
            $vars as $i => &$var
        ) {

            $this->refs[] =& $var;
        }

        return true;
    }


    public function execute(): bool {

        try {

            $this->last_error = '';

            $this->stmt =
                $this->pdo->prepare(
                    $this->sql
                );


            foreach (
                $this->refs as $i => &$value
            ) {

                $this->stmt->bindValue(
                    $i + 1,
                    $value
                );
            }


            return $this->stmt->execute();


        } catch (Throwable $e) {

            $this->last_error =
                $e->getMessage();

            error_log(
                'Prepared statement failed: ' .
                $e->getMessage() .
                ' SQL: ' .
                $this->sql
            );

            return false;
        }
    }


    public function get_result(): TravelEaseResult {

        return new TravelEaseResult(
            $this->stmt
        );
    }


    public function close(): bool {

        $this->stmt = null;

        return true;
    }


    public function error(): string {

        return $this->last_error;
    }
}


/* ============================================================
   DATABASE CONNECTION
   ============================================================ */

class TravelEaseConnection {

    public PDO $pdo;

    public int $insert_id = 0;

    public string $last_error = '';


    public function __construct() {

        $host =
            getenv('DB_HOST')
            ?: 'localhost';

        $port =
            getenv('DB_PORT')
            ?: '5432';

        $dbname =
            getenv('DB_NAME')
            ?: 'travelease1';

        $user =
            getenv('DB_USER')
            ?: '';

        $password =
            getenv('DB_PASSWORD')
            ?: '';


        try {

            $this->pdo = new PDO(

                "pgsql:host={$host};port={$port};dbname={$dbname}",

                $user,

                $password,

                [

                    PDO::ATTR_ERRMODE =>
                        PDO::ERRMODE_EXCEPTION,

                    PDO::ATTR_DEFAULT_FETCH_MODE =>
                        PDO::FETCH_ASSOC,

                    PDO::ATTR_EMULATE_PREPARES =>
                        true

                ]
            );


        } catch (Throwable $e) {

            http_response_code(500);

            error_log(
                'Database connection failed: ' .
                $e->getMessage()
            );

            die(
                'Database connection failed. ' .
                'Check the Render PostgreSQL environment variables.'
            );
        }
    }


    public function begin_transaction(): bool {

        return $this->pdo->beginTransaction();
    }


    public function commit(): bool {

        return $this->pdo->commit();
    }


    public function rollback(): bool {

        return $this->pdo->rollBack();
    }


    /*
     * Compatibility for application pages that still use
     * $conn->prepare() from the original mysqli/MySQL code.
     */

    public function prepare(
        string $sql
    ): TravelEaseStmt {

        return new TravelEaseStmt(
            $this->pdo,
            $sql
        );
    }


    /*
     * Escape values for the old mysqli-style SQL.
     */

    public function escape(
        string $value
    ): string {

        $quoted =
            $this->pdo->quote($value);

        return substr(
            $quoted,
            1,
            -1
        );
    }
}


/* ============================================================
   SQL NORMALIZATION
   ============================================================ */

function travelEaseNormalizeSql(
    string $sql
): string {


    /*
     * --------------------------------------------------------
     * SHOW TABLES LIKE
     * --------------------------------------------------------
     *
     * MySQL:
     *
     * SHOW TABLES LIKE 'flights'
     *
     * PostgreSQL equivalent:
     *
     * information_schema.tables
     */

    if (
        preg_match(
            "/^\s*SHOW\s+TABLES\s+LIKE\s+'([^']+)'\s*$/i",
            trim($sql),
            $m
        )
    ) {

        $table =
            str_replace(
                "'",
                "''",
                $m[1]
            );


        return "
            SELECT
                table_name AS \"Tables_in_db\"
            FROM information_schema.tables
            WHERE
                table_schema = 'public'
                AND table_name LIKE '{$table}'
        ";
    }


    /*
     * --------------------------------------------------------
     * DATE_SUB(NOW(), INTERVAL ...)
     * --------------------------------------------------------
     */

    $sql =
        preg_replace_callback(

            "/DATE_SUB\\(
                NOW\\(\\),
                \\s*INTERVAL\\s+
                (\\d+)\\s+
                (DAY|MONTH|YEAR|HOUR)
            \\)/ix",

            function ($m) {

                $unit =
                    strtolower(
                        $m[2]
                    );

                return
                    "(NOW() - INTERVAL '{$m[1]} {$unit}')";
            },

            $sql
        );


    /*
     * --------------------------------------------------------
     * TIMESTAMPDIFF(HOUR, start, NOW())
     * --------------------------------------------------------
     */

    $sql =
        preg_replace_callback(

            "/TIMESTAMPDIFF\\(
                \\s*HOUR\\s*,
                \\s*([^,]+)\\s*,
                \\s*NOW\\(\\)
            \\)/ix",

            function ($m) {

                return
                    "(EXTRACT(
                        EPOCH FROM
                        (NOW() - {$m[1]})
                    ) / 3600)";
            },

            $sql
        );


    /*
     * --------------------------------------------------------
     * IFNULL -> COALESCE
     * --------------------------------------------------------
     */

    $sql =
        preg_replace(
            '/\bIFNULL\s*\(/i',
            'COALESCE(',
            $sql
        );


    /*
     * --------------------------------------------------------
     * Remove MySQL backticks
     * --------------------------------------------------------
     */

    $sql =
        str_replace(
            '`',
            '',
            $sql
        );


    return $sql;
}


/* ============================================================
   MYSQLI CONNECT
   ============================================================ */

function mysqli_connect(
    $host = null,
    $user = null,
    $password = null,
    $database = null,
    $port = 5432
) {

    return new TravelEaseConnection();
}


/* ============================================================
   MYSQLI SET CHARSET
   ============================================================ */

function mysqli_set_charset(
    $conn,
    $charset
): bool {

    return true;
}


/* ============================================================
   MYSQLI QUERY
   ============================================================ */

function mysqli_query(
    $conn,
    string $sql
) {

    try {

        /*
         * Clear previous error.
         */

        $conn->last_error = '';


        /*
         * Normalize MySQL syntax to PostgreSQL.
         */

        $normalized_sql =
            travelEaseNormalizeSql($sql);


        /*
         * Execute query.
         */

        $stmt =
            $conn->pdo->query(
                $normalized_sql
            );


        /*
         * SELECT / SHOW / WITH queries
         */

        if (
            preg_match(
                '/^\s*(SELECT|SHOW|WITH|TABLE)\b/i',
                $sql
            )
        ) {

            return new TravelEaseResult(
                $stmt
            );
        }


        /*
         * INSERT
         *
         * The original application expects
         * mysqli_insert_id().
         *
         * PostgreSQL does not automatically provide
         * the inserted ID unless the query uses RETURNING.
         *
         * We therefore attempt lastInsertId(), but
         * booking.php also has a fallback lookup.
         */

        if (
            preg_match(
                '/^\s*INSERT\b/i',
                $sql
            )
        ) {

            try {

                $last_id =
                    $conn->pdo->lastInsertId();

                if ($last_id !== false) {

                    $conn->insert_id =
                        (int)$last_id;
                }

            } catch (Throwable $e) {

                /*
                 * Ignore this here.
                 *
                 * booking.php can use its
                 * booking_reference fallback.
                 */

                $conn->insert_id = 0;
            }
        }


        return true;


    } catch (Throwable $e) {


        /*
         * SAVE THE REAL POSTGRESQL ERROR.
         */

        $conn->last_error =
            $e->getMessage();


        /*
         * Log complete information to Render.
         */

        error_log(
            '================================================='
        );

        error_log(
            'TravelEASE PostgreSQL QUERY ERROR'
        );

        error_log(
            'ERROR: ' .
            $e->getMessage()
        );

        error_log(
            'SQL: ' .
            $sql
        );

        error_log(
            'NORMALIZED SQL: ' .
            travelEaseNormalizeSql($sql)
        );

        error_log(
            '================================================='
        );


        return false;
    }
}


/* ============================================================
   MYSQLI FETCH ASSOC
   ============================================================ */

function mysqli_fetch_assoc(
    $result
) {

    if (
        $result instanceof TravelEaseResult
    ) {

        return $result->fetch_assoc();
    }

    return null;
}


/* ============================================================
   MYSQLI NUM ROWS
   ============================================================ */

function mysqli_num_rows(
    $result
): int {

    if (
        $result instanceof TravelEaseResult
    ) {

        return $result->num_rows();
    }

    return 0;
}


/* ============================================================
   MYSQLI REAL ESCAPE STRING
   ============================================================ */

function mysqli_real_escape_string(
    $conn,
    string $value
): string {

    return $conn->escape(
        $value
    );
}


/* ============================================================
   MYSQLI INSERT ID
   ============================================================ */

function mysqli_insert_id(
    $conn
): int {

    return $conn->insert_id;
}


/* ============================================================
   MYSQLI ERROR
   ============================================================ */

function mysqli_error(
    $conn
): string {

    if (
        $conn instanceof TravelEaseConnection &&
        $conn->last_error !== ''
    ) {

        return $conn->last_error;
    }


    return
        'PostgreSQL query failed. ' .
        'No additional error information is available.';
}


/* ============================================================
   MYSQLI CONNECT ERROR
   ============================================================ */

function mysqli_connect_error(): string {

    return
        'PostgreSQL connection failed. ' .
        'Check the Render database environment variables.';
}


/* ============================================================
   TRANSACTIONS
   ============================================================ */

function mysqli_begin_transaction(
    $conn
): bool {

    return $conn->begin_transaction();
}


function mysqli_commit(
    $conn
): bool {

    return $conn->commit();
}


function mysqli_rollback(
    $conn
): bool {

    return $conn->rollback();
}


/* ============================================================
   DATA SEEK
   ============================================================ */

function mysqli_data_seek(
    $result,
    int $offset
): bool {

    if (
        $result instanceof TravelEaseResult
    ) {

        return $result->data_seek(
            $offset
        );
    }

    return false;
}


/* ============================================================
   MYSQLI PREPARE
   ============================================================ */

function mysqli_prepare(
    $conn,
    string $sql
) {

    return new TravelEaseStmt(
        $conn->pdo,
        $sql
    );
}


/* ============================================================
   MYSQLI STMT BIND PARAM
   ============================================================ */

function mysqli_stmt_bind_param(
    $stmt,
    string $types,
    &...$vars
): bool {

    return $stmt->bind_param(
        $types,
        ...$vars
    );
}


/* ============================================================
   MYSQLI STMT EXECUTE
   ============================================================ */

function mysqli_stmt_execute(
    $stmt
): bool {

    return $stmt->execute();
}


/* ============================================================
   MYSQLI STMT CLOSE
   ============================================================ */

function mysqli_stmt_close(
    $stmt
): bool {

    return $stmt->close();
}


/* ============================================================
   CREATE CONNECTION
   ============================================================ */

$conn = mysqli_connect(

    getenv('DB_HOST'),

    getenv('DB_USER'),

    getenv('DB_PASSWORD'),

    getenv('DB_NAME'),

    getenv('DB_PORT') ?: 5432
);

?>
