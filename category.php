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

// Fetch categories
$sql = "SELECT * FROM CATEGORIES";
$result = $conn->query($sql);

if (!$result) {
    die("Query failed: " . $conn->error);
}
?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Categories</title>
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
                    <div class="col-md-6 col-lg-5">

                        <div class="card p-4">

                            <!-- TITLE -->
                            <h2 class="mb-3">Category List</h2>

                            <!-- ACTION + SEARCH -->
                            <div class="d-flex align-items-center gap-3 mb-3">
                                <input type="text" id="categorySearch" class="form-control search-input "
                                    style="max-width: 220px;" placeholder="Search category...">

                                <button class="btn btn-primary" data-bs-toggle="modal"
                                    data-bs-target="#addCategoryModal">
                                    + Add Category
                                </button>
                            </div>

                            <!-- TABLE -->
                            <table class="table align-middle mb-3" id="categoryTable">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Category Name</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php while ($row = $result->fetch_assoc()) { ?>
                                        <tr>
                                            <td><?= $row['CATEGORY_ID'] ?></td>
                                            <td><?= htmlspecialchars($row['CATEGORY_NAME']) ?></td>
                                            <td>
                                                <div class="d-flex gap-2">
                                                    <button class="btn btn-sm btn-outline-warning btn-edit"
                                                        data-bs-toggle="modal" data-bs-target="#editCategoryModal"
                                                        data-id="<?= $row['CATEGORY_ID'] ?>"
                                                        data-name="<?= htmlspecialchars($row['CATEGORY_NAME'], ENT_QUOTES) ?>">
                                                        Edit
                                                    </button>

                                                    <a href="/IMS/Category/DeleteCategory.php?id=<?= $row['CATEGORY_ID'] ?>"
                                                        class="btn btn-sm btn-danger"
                                                        onclick="return confirm('Delete this category?')">
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

    <!-- ADD CATEGORY MODAL -->
    <div class="modal fade" id="addCategoryModal">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <form action="/IMS/Category/AddCategory.php" method="POST">
                    <div class="modal-header">
                        <h5>Add Category</h5>
                        <button class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <label class="form-label">Category Name</label>
                        <input type="text" name="category_name" class="form-control" required>
                    </div>
                    <div class="modal-footer">
                        <button class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button class="btn btn-primary">Add</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- EDIT CATEGORY MODAL -->
    <div class="modal fade" id="editCategoryModal">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <form action="/IMS/Category/EditCategory.php" method="POST">
                    <input type="hidden" name="category_id" id="edit_id">
                    <div class="modal-header">
                        <h5>Edit Category</h5>
                        <button class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <label class="form-label">Category Name</label>
                        <input type="text" name="category_name" id="edit_name" class="form-control" required>
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
        document.getElementById('editCategoryModal')
            .addEventListener('show.bs.modal', function (event) {
                let btn = event.relatedTarget;
                document.getElementById('edit_id').value = btn.getAttribute('data-id');
                document.getElementById('edit_name').value = btn.getAttribute('data-name');
            });
    </script>
    <script>
        document.getElementById('categorySearch').addEventListener('input', function () {
            const value = this.value.toLowerCase();
            const rows = document.querySelectorAll('#categoryTable tbody tr');

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