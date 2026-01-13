<?php
$host = "localhost";
$user = "ims_user";
$pass = "12345Admin";
$db = "ims";

$conn = new mysqli($host, $user, $pass, $db);

// Check connection
if ($conn->connect_error) {
    die("Database connection failed: " . $conn->connect_error);
}

// Fetch users
$sql = "SELECT * FROM USERS";
$result = $conn->query($sql);

if (!$result) {
    die("Query failed: " . $conn->error);
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Users</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">

    <link rel="stylesheet" href="/IMS/Pages/template.css">
</head>

<body>
    <div class="d-flex">

        <!-- SIDEBAR -->
        <div class="sidebar p-3" id="sidebar">
            <h4 class="text-center mb-4">INVENTORY MS</h4>

            <!-- Top links -->
            <div class="sidebar-menu">
                <a href="#" class="active">
                    <i class="bi bi-speedometer2 me-2"></i>
                    Dashboard
                </a>

                <a href="/IMS/Pages/products.php">
                    <i class="bi bi-box-seam me-2"></i>
                    Products
                </a>

                <a href="/IMS/Pages/category.php">
                    <i class="bi bi-tags me-2"></i>
                    Categories
                </a>

                <a href="/IMS/Pages/orders.php">
                    <i class="bi bi-cart-check me-2"></i>
                    Orders
                </a>

                <a href="/IMS/Pages/users.php">
                    <i class="bi bi-people me-2"></i>
                    Users
                </a>

                <a href="#">
                    <i class="bi bi-person-circle me-2"></i>
                    Profile
                </a>

                <a href="/IMS/index.html">
                    <i class="bi bi-box-arrow-right me-2"></i>
                    Logout
                </a>
            </div>
        </div>

        <!-- MAIN -->
        <div class="flex-grow-1 main-content">

            <!-- TOP NAVBAR -->
            <nav class="navbar navbar-light bg-white shadow-sm px-4">
                <div class="d-flex align-items-center gap-3">
                    <!-- Burger button -->
                    <button class="btn btn-outline-secondary d-md-none" id="sidebarToggle">
                        ☰
                    </button>
                    <span class="navbar-brand mb-0 h5">Dashboard</span>
                </div>

                <span class="fw-semibold">
                    Welcome, Admin
                </span>
            </nav>


            <div class="content">
                <div class="row justify-content-start">
                    <div class="col-md-8 col-lg-7">
                        <div class="card p-4">

                            <!-- TITLE -->
                            <h2 class="mb-3">User List</h2>

                            <!-- ACTION + SEARCH -->
                            <div class="d-flex align-items-center gap-3 mb-3">
                                <input type="text" id="userSearch" class="form-control" style="max-width: 240px;"
                                    placeholder="Search user...">

                                <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addUserModal">
                                    + Add User
                                </button>
                            </div>

                            <!-- TABLE -->
                            <table class="table align-middle mb-3" id="userTable">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Username</th>
                                        <th>Email</th>
                                        <th>Role</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php while ($row = $result->fetch_assoc()) { ?>
                                        <tr>
                                            <td><?= $row['USER_ID'] ?></td>
                                            <td><?= htmlspecialchars($row['USERNAME']) ?></td>
                                            <td><?= htmlspecialchars($row['EMAIL']) ?></td>
                                            <td><?= htmlspecialchars($row['ROLE']) ?></td>
                                            <td>
                                                <div class="d-flex gap-2">
                                                    <button class="btn btn-sm btn-outline-warning btn-edit"
                                                        data-bs-toggle="modal" data-bs-target="#editUserModal"
                                                        data-id="<?= $row['USER_ID'] ?>"
                                                        data-username="<?= htmlspecialchars($row['USERNAME'], ENT_QUOTES) ?>"
                                                        data-email="<?= htmlspecialchars($row['EMAIL'], ENT_QUOTES) ?>"
                                                        data-role="<?= htmlspecialchars($row['ROLE'], ENT_QUOTES) ?>">
                                                        Edit
                                                    </button>

                                                    <a href="/IMS/Users/DeleteUser.php?id=<?= $row['USER_ID'] ?>"
                                                        class="btn btn-sm btn-danger"
                                                        onclick="return confirm('Delete this user?')">
                                                        Delete
                                                    </a>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php } ?>
                                </tbody>
                            </table>

                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>

    <!-- ADD USER MODAL -->
    <div class="modal fade" id="addUserModal">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <form action="/IMS/Users/AddUser.php" method="POST">
                    <div class="modal-header">
                        <h5>Add User</h5>
                        <button class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <label class="form-label">Username</label>
                        <input type="text" name="username" class="form-control" required>

                        <label class="form-label mt-2">Email</label>
                        <input type="email" name="email" class="form-control" required>

                        <label class="form-label mt-2">Role</label>
                        <select name="role" class="form-control" required>
                            <option value="Admin">Admin</option>
                            <option value="Staff">Staff</option>
                        </select>

                        <label class="form-label mt-2">Password</label>
                        <input type="password" name="password" class="form-control" required>
                    </div>
                    <div class="modal-footer">
                        <button class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button class="btn btn-primary">Add</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- EDIT USER MODAL -->
    <div class="modal fade" id="editUserModal">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <form action="/IMS/Users/EditUser.php" method="POST">
                    <input type="hidden" name="user_id" id="edit_id">

                    <div class="modal-header">
                        <h5>Edit User</h5>
                        <button class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <label class="form-label">Username</label>
                        <input type="text" name="username" id="edit_username" class="form-control" required>

                        <label class="form-label mt-2">Email</label>
                        <input type="email" name="email" id="edit_email" class="form-control" required>

                        <label class="form-label mt-2">Role</label>
                        <select name="role" id="edit_role" class="form-control" required>
                            <option value="Admin">Admin</option>
                            <option value="Staff">Staff</option>
                        </select>
                    </div>
                    <div class="modal-footer">
                        <button class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button class="btn btn-warning">Update</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        document.getElementById('editUserModal')
            .addEventListener('show.bs.modal', function (event) {
                let btn = event.relatedTarget;
                document.getElementById('edit_id').value = btn.getAttribute('data-id');
                document.getElementById('edit_username').value = btn.getAttribute('data-username');
                document.getElementById('edit_email').value = btn.getAttribute('data-email');
                document.getElementById('edit_role').value = btn.getAttribute('data-role');
            });
    </script>

    <script>
        document.getElementById('userSearch').addEventListener('input', function () {
            const value = this.value.toLowerCase();
            const rows = document.querySelectorAll('#userTable tbody tr');

            rows.forEach(row => {
                row.style.display = row.innerText.toLowerCase().includes(value) ? '' : 'none';
            });
        });
    </script>

    <script>
        const sidebar = document.getElementById('sidebar');
        const toggleBtn = document.getElementById('sidebarToggle');

        toggleBtn.addEventListener('click', () => {
            sidebar.classList.toggle('show');
        });
    </script>


    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>