<?php
session_start();
include "../db.php";

if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit();
}

// Handle add admin
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['add_admin'])) {
    $full_name = mysqli_real_escape_string($conn, $_POST['full_name']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = $_POST['password'];
    $phone = mysqli_real_escape_string($conn, $_POST['phone'] ?? '');
    $status = $_POST['status'] ?? 'active';
    
    // Check if email already exists
    $check = mysqli_query($conn, "SELECT id FROM admins WHERE email='$email'");
    if (mysqli_num_rows($check) > 0) {
        $error = "Email already exists";
    } else {
        $hash = password_hash($password, PASSWORD_DEFAULT);
        $sql = "INSERT INTO admins (full_name, email, password, phone, status) VALUES ('$full_name', '$email', '$hash', '$phone', '$status')";
        if (mysqli_query($conn, $sql)) {
            $_SESSION['success_message'] = "Admin added successfully!";
            header("Location: manage-admins.php");
            exit();
        } else {
            $error = "Failed to add admin";
        }
    }
}

// Handle update admin
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update_admin'])) {
    $id = intval($_POST['id']);
    $full_name = mysqli_real_escape_string($conn, $_POST['full_name']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $phone = mysqli_real_escape_string($conn, $_POST['phone'] ?? '');
    $status = $_POST['status'] ?? 'active';
    
    // Check if email already exists for another admin
    $check = mysqli_query($conn, "SELECT id FROM admins WHERE email='$email' AND id != '$id'");
    if (mysqli_num_rows($check) > 0) {
        $error = "Email already exists for another admin";
    } else {
        $sql = "UPDATE admins SET full_name='$full_name', email='$email', phone='$phone', status='$status' WHERE id='$id'";
        if (mysqli_query($conn, $sql)) {
            $_SESSION['success_message'] = "Admin updated successfully!";
            header("Location: manage-admins.php");
            exit();
        } else {
            $error = "Failed to update admin";
        }
    }
}

// Handle delete admin
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    // Prevent deleting yourself
    if ($id == $_SESSION['admin_id']) {
        $_SESSION['error_message'] = "You cannot delete your own account";
        header("Location: manage-admins.php");
        exit();
    } else {
        mysqli_query($conn, "DELETE FROM admins WHERE id='$id'");
        $_SESSION['success_message'] = "Admin deleted successfully!";
        header("Location: manage-admins.php");
        exit();
    }
}

// Handle password reset
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['reset_password'])) {
    $id = intval($_POST['id']);
    $new_password = $_POST['new_password'];
    $hash = password_hash($new_password, PASSWORD_DEFAULT);
    mysqli_query($conn, "UPDATE admins SET password='$hash' WHERE id='$id'");
    $_SESSION['success_message'] = "Password reset successfully!";
    header("Location: manage-admins.php");
    exit();
}

// Get all admins
$admins_query = mysqli_query($conn, "SELECT * FROM admins ORDER BY created_at DESC");
$admins = [];
while($row = mysqli_fetch_assoc($admins_query)) {
    $admins[] = $row;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Admins - TravelEase Admin</title>
    <link rel="stylesheet" href="../css/all.min.css">
    <link rel="stylesheet" href="admin-style.css">
    <style>
        .badge-success {
            background: #d4edda;
            color: #155724;
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 500;
        }
        .badge-danger {
            background: #f8d7da;
            color: #721c24;
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 500;
        }
        .btn-sm {
            padding: 5px 10px;
            font-size: 12px;
            border-radius: 4px;
            border: none;
            cursor: pointer;
            text-decoration: none;
            display: inline-block;
            margin: 2px;
        }
        .btn-primary {
            background: #007bff;
            color: white;
        }
        .btn-warning {
            background: #ffc107;
            color: #212529;
        }
        .btn-danger {
            background: #dc3545;
            color: white;
        }
        .btn-secondary {
            background: #6c757d;
            color: white;
        }
        .modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0,0,0,0.5);
            z-index: 1000;
            justify-content: center;
            align-items: center;
        }
        .modal-content {
            background: white;
            padding: 30px;
            border-radius: 10px;
            width: 90%;
            max-width: 500px;
            max-height: 90vh;
            overflow-y: auto;
        }
        .modal-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }
        .modal-close {
            background: none;
            border: none;
            font-size: 24px;
            cursor: pointer;
            color: #666;
        }
    </style>
</head>
<body>
    <div class="admin-wrapper">
        <?php include 'sidebar.php'; ?>

        <main class="admin-main">
            <header class="admin-header">
                <h1><i class="fas fa-users-cog"></i> Manage Admins</h1>
                <div class="header-actions">
                    <span>Welcome, <?php echo htmlspecialchars($_SESSION['admin_name']); ?></span>
                    <a href="../index.php" class="btn-secondary"><i class="fas fa-home"></i> View Site</a>
                </div>
            </header>

            <?php if(isset($_SESSION['success_message'])): ?>
                <div style="background: #d4edda; color: #155724; padding: 15px; border-radius: 6px; margin-bottom: 20px;">
                    <?php echo $_SESSION['success_message']; unset($_SESSION['success_message']); ?>
                </div>
            <?php endif; ?>

            <?php if(isset($_SESSION['error_message'])): ?>
                <div style="background: #f8d7da; color: #721c24; padding: 15px; border-radius: 6px; margin-bottom: 20px;">
                    <?php echo $_SESSION['error_message']; unset($_SESSION['error_message']); ?>
                </div>
            <?php endif; ?>

            <?php if(isset($error)): ?>
                <div style="background: #f8d7da; color: #721c24; padding: 15px; border-radius: 6px; margin-bottom: 20px;">
                    <?php echo htmlspecialchars($error); ?>
                </div>
            <?php endif; ?>

            <!-- Add Admin Form -->
            <div class="content-card" style="margin-bottom: 20px;">
                <h2>Add New Admin</h2>
                <form method="POST" action="">
                    <input type="hidden" name="add_admin" value="1">
                    <div class="form-group">
                        <label>Full Name</label>
                        <input type="text" name="full_name" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label>Email</label>
                        <input type="email" name="email" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label>Password</label>
                        <input type="password" name="password" class="form-control" required minlength="6">
                    </div>
                    <div class="form-group">
                        <label>Phone</label>
                        <input type="text" name="phone" class="form-control">
                    </div>
                    <div class="form-group">
                        <label>Status</label>
                        <select name="status" class="form-control">
                            <option value="active">Active</option>
                            <option value="inactive">Inactive</option>
                        </select>
                    </div>
                    <button type="submit" class="btn-primary">Add Admin</button>
                </form>
            </div>

            <!-- Admins List -->
            <div class="content-card">
                <h2>All Admins</h2>
                <div class="table-responsive">
                    <?php if(count($admins) > 0): ?>
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Phone</th>
                                <th>Status</th>
                                <th>Last Login</th>
                                <th>Created</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach($admins as $admin): ?>
                            <tr>
                                <td><?php echo $admin['id']; ?></td>
                                <td><?php echo htmlspecialchars($admin['full_name']); ?></td>
                                <td><?php echo htmlspecialchars($admin['email']); ?></td>
                                <td><?php echo htmlspecialchars($admin['phone'] ?? '-'); ?></td>
                                <td>
                                    <span class="badge-<?php echo $admin['status'] == 'active' ? 'success' : 'danger'; ?>">
                                        <?php echo ucfirst($admin['status']); ?>
                                    </span>
                                </td>
                                <td><?php echo $admin['last_login'] ? date('M j, Y H:i', strtotime($admin['last_login'])) : 'Never'; ?></td>
                                <td><?php echo date('M j, Y', strtotime($admin['created_at'])); ?></td>
                                <td>
                                    <button class="btn-sm btn-primary" onclick="editAdmin(<?php echo $admin['id']; ?>)">Edit</button>
                                    <button class="btn-sm btn-warning" onclick="resetPassword(<?php echo $admin['id']; ?>)">Reset Password</button>
                                    <?php if($admin['id'] != $_SESSION['admin_id']): ?>
                                    <a href="manage-admins.php?delete=<?php echo $admin['id']; ?>" class="btn-sm btn-danger" onclick="return confirm('Are you sure you want to delete this admin?')">Delete</a>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                    <?php else: ?>
                    <p>No admins found.</p>
                    <?php endif; ?>
                </div>
            </div>
        </main>
    </div>

    <!-- Edit Modal -->
    <div id="editModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h3>Edit Admin</h3>
                <button class="modal-close" onclick="closeEditModal()">&times;</button>
            </div>
            <form method="POST" action="" id="editForm">
                <input type="hidden" name="update_admin" value="1">
                <input type="hidden" name="id" id="edit_id">
                <div class="form-group">
                    <label>Full Name</label>
                    <input type="text" name="full_name" id="edit_name" class="form-control" required>
                </div>
                <div class="form-group">
                    <label>Email</label>
                    <input type="email" name="email" id="edit_email" class="form-control" required>
                </div>
                <div class="form-group">
                    <label>Phone</label>
                    <input type="text" name="phone" id="edit_phone" class="form-control">
                </div>
                <div class="form-group">
                    <label>Status</label>
                    <select name="status" id="edit_status" class="form-control">
                        <option value="active">Active</option>
                        <option value="inactive">Inactive</option>
                    </select>
                </div>
                <button type="submit" class="btn-primary">Update</button>
                <button type="button" class="btn-secondary" onclick="closeEditModal()">Cancel</button>
            </form>
        </div>
    </div>

    <!-- Reset Password Modal -->
    <div id="passwordModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h3>Reset Password</h3>
                <button class="modal-close" onclick="closePasswordModal()">&times;</button>
            </div>
            <form method="POST" action="" id="passwordForm">
                <input type="hidden" name="reset_password" value="1">
                <input type="hidden" name="id" id="password_id">
                <div class="form-group">
                    <label>New Password</label>
                    <input type="password" name="new_password" class="form-control" required minlength="6">
                </div>
                <button type="submit" class="btn-primary">Reset Password</button>
                <button type="button" class="btn-secondary" onclick="closePasswordModal()">Cancel</button>
            </form>
        </div>
    </div>

    <script>
        const adminData = <?php echo json_encode($admins); ?>;
        
        function editAdmin(id) {
            const admin = adminData.find(a => parseInt(a.id) === parseInt(id));
            if (admin) {
                document.getElementById('edit_id').value = admin.id;
                document.getElementById('edit_name').value = admin.full_name;
                document.getElementById('edit_email').value = admin.email;
                document.getElementById('edit_phone').value = admin.phone || '';
                document.getElementById('edit_status').value = admin.status;
                document.getElementById('editModal').style.display = 'flex';
            }
        }

        function resetPassword(id) {
            document.getElementById('password_id').value = id;
            document.getElementById('passwordModal').style.display = 'flex';
        }

        function closeEditModal() {
            document.getElementById('editModal').style.display = 'none';
        }

        function closePasswordModal() {
            document.getElementById('passwordModal').style.display = 'none';
        }

        // Close modals when clicking outside
        window.onclick = function(event) {
            const editModal = document.getElementById('editModal');
            const passwordModal = document.getElementById('passwordModal');
            if (event.target == editModal) {
                editModal.style.display = 'none';
            }
            if (event.target == passwordModal) {
                passwordModal.style.display = 'none';
            }
        }
    </script>
</body>
</html>

