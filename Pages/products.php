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


//   FETCH PRODUCTS WITH CATEGORY
$sql = "
    SELECT 
        P.PRODUCT_ID,
        P.NAME,
        P.CATEGORY_ID,
        C.CATEGORY_NAME,
        P.DESCRIPTION,
        P.QUANTITY,
        P.PRICE
    FROM PRODUCTS P
    JOIN CATEGORIES C 
        ON P.CATEGORY_ID = C.CATEGORY_ID
";

$resultall = $conn->query($sql);

//   TOTAL PRODUCT COUNT
$sql2 = "SELECT COUNT(PRODUCT_ID) AS TOTAL FROM PRODUCTS";
$resulttotal = $conn->query($sql2);
$row = $resulttotal->fetch_assoc();
$totalcount = $row['TOTAL'];

//   FETCH CATEGORIES (ID + NAME)
$catSql = "SELECT CATEGORY_ID, CATEGORY_NAME FROM CATEGORIES";
$catResult = $conn->query($catSql);

$categories = [];
while ($cat = $catResult->fetch_assoc()) {
    $categories[] = $cat;
}

//   FETCH CATEGORY NAMES ONLY
$sql3 = "SELECT CATEGORY_NAME FROM CATEGORIES";
$resultsql3 = $conn->query($sql3);

?>



<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Products | Inventory</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">

    <!-- Custom CSS -->
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

        <!-- MAIN CONTENT -->
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


            <!-- PAGE CONTENT -->
            <div class="content">


                <!-- PRODUCTS TABLE -->
                <div class="card p-4">
                    <h2 class="mb-3">Inventory </h2>

                    <div class="d-flex align-items-center gap-3">
                        <input type="text" class="form-control search-input" style="max-width: 220px;"
                            id="productSearch" placeholder="Search product name...">
                        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addProductModal">
                            + Add Product
                        </button>
                    </div>
                    <br>

                    <div class="table-responsive">
                        <table class="table align-middle" id="productsTable">
                            <thead>
                                <tr>
                                    <th>Product ID</th>
                                    <th>Product</th>
                                    <th>Category</th>
                                    <th>Description</th>
                                    <th>Quantity</th>
                                    <th>Price</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                while ($row = $resultall->fetch_assoc()) {

                                    $data1 = $row["PRODUCT_ID"];
                                    $data2 = $row["NAME"];
                                    $data3 = $row["CATEGORY_NAME"];
                                    $data4 = $row["DESCRIPTION"];
                                    $data5 = $row["QUANTITY"];
                                    $data6 = $row["PRICE"];

                                    echo '
                                    <tr>
                                        <td>' . $data1 . '</td>
                                        <td>' . htmlspecialchars($data2) . '</td>
                                        <td>' . htmlspecialchars($data3) . '</td>
                                        <td>' . htmlspecialchars($data4) . '</td>
                                        <td>' . $data5 . '</td>
                                        <td>₱ ' . $data6 . '</td>
                                        <td>
                                            <div class="d-flex gap-2">

                                                <!-- EDIT BUTTON -->
                                                <button 
                                                    class="btn btn-sm btn-outline-warning btn-edit"
                                                    data-bs-toggle="modal"
                                                    data-bs-target="#editProductModal"
                                                    data-id="' . $data1 . '"
                                                    data-name="' . htmlspecialchars($data2, ENT_QUOTES) . '"
                                                    data-category-id="' . $row['CATEGORY_ID'] . '"
                                                    data-description="' . htmlspecialchars($data4, ENT_QUOTES) . '"
                                                    data-quantity="' . $data5 . '"
                                                    data-price="' . $data6 . '">
                                                    Edit
                                                </button>


                                                <!-- DELETE BUTTON -->
                                                <a 
                                                    href="/IMS/Products/DeleteProduct.php?id=' . $data1 . '" 
                                                    class="btn btn-sm btn-danger"
                                                    onclick="return confirm(\'Are you sure you want to delete this product?\')">
                                                    Delete
                                                </a>

                                            </div>
                                        </td>
                                    </tr>';
                                }
                                ?>
                            </tbody>
                        </table>


                    </div>
                    <h5>Total Number of Products:
                        <?php echo $totalcount; ?>
                    </h5>

                </div>

            </div>
        </div>
    </div>

    <!-- ADD PRODUCT MODAL -->
    <div class="modal fade" id="addProductModal" tabindex="-1">
        <div class="modal-dialog modal-md modal-dialog-centered">
            <div class="modal-content border-0 rounded-4 shadow-sm">

                <!-- MODAL HEADER -->
                <div class="modal-header border-0 pb-0">
                    <h6 class="modal-title fw-semibold">Add Product</h6>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <!-- MODAL BODY -->
                <div class="modal-body pt-2">
                    <form action="/IMS/Products/AddProduct.php" method="POST">

                        <div class="row g-3">

                            <!-- Product Name -->
                            <div class="col-12">
                                <label class="form-label small text-muted">Product Name</label>
                                <input type="text" name="name" class="form-control" placeholder="Product name" required>
                            </div>

                            <!-- Category -->
                            <div class="col-12">
                                <label class="form-label small text-muted">Category</label>
                                <select name="category_id" class="form-select" required>
                                    <option value="">Select category</option>
                                    <?php foreach ($categories as $cat): ?>
                                        <option value="<?= $cat['CATEGORY_ID'] ?>">
                                            <?= htmlspecialchars($cat['CATEGORY_NAME']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <!-- Description -->
                            <div class="col-12">
                                <label class="form-label small text-muted">Description</label>
                                <textarea class="form-control" name="description" rows="2"
                                    placeholder="(Optional)"></textarea>
                            </div>

                            <!-- Quantity & Price -->
                            <div class="col-6">
                                <label class="form-label small text-muted">Quantity</label>
                                <input type="number" name="quantity" class="form-control" min="0" placeholder="kg"
                                    required>
                            </div>

                            <div class="col-6">
                                <label class="form-label small text-muted">Price</label>
                                <input type="number" name="price" class="form-control" step="0.01" placeholder="₱"
                                    required>
                            </div>

                        </div>

                        <!-- FOOTER -->
                        <div class="modal-footer border-0 px-0 pt-3">
                            <button type="button" class="btn btn-sm btn-light" data-bs-dismiss="modal">
                                Cancel
                            </button>
                            <button type="submit" class="btn btn-sm btn-primary px-4">
                                Add
                            </button>
                        </div>

                    </form>
                </div>

            </div>
        </div>
    </div>

    <!-- EDIT PRODUCT MODAL -->
    <div class="modal fade" id="editProductModal" tabindex="-1">
        <div class="modal-dialog modal-md modal-dialog-centered">
            <div class="modal-content border-0 rounded-4 shadow-sm">

                <!-- HEADER -->
                <div class="modal-header border-0 pb-0">
                    <h6 class="modal-title fw-semibold">Edit Product</h6>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <!-- BODY -->
                <div class="modal-body pt-2">
                    <form action="/IMS/Products/EditProduct.php" method="POST">

                        <input type="hidden" name="product_id" id="edit_id">

                        <div class="row g-3">

                            <!-- Product Name -->
                            <div class="col-12">
                                <label class="form-label small text-muted">Product Name</label>
                                <input type="text" name="name" id="edit_name" class="form-control" required>
                            </div>

                            <!-- Category -->
                            <div class="col-12">
                                <label class="form-label small text-muted">Category</label>
                                <select name="category_id" id="edit_category" class="form-select" required>
                                    <option value="">Select category</option>
                                    <?php foreach ($categories as $cat): ?>
                                        <option value="<?= $cat['CATEGORY_ID'] ?>">
                                            <?= htmlspecialchars($cat['CATEGORY_NAME']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <!-- Description -->
                            <div class="col-12">
                                <label class="form-label small text-muted">Description</label>
                                <textarea name="description" id="edit_description" class="form-control"
                                    rows="2"></textarea>
                            </div>

                            <!-- Quantity -->
                            <div class="col-6">
                                <label class="form-label small text-muted">Quantity</label>
                                <input type="number" name="quantity" id="edit_quantity" class="form-control" required>
                            </div>

                            <!-- Price -->
                            <div class="col-6">
                                <label class="form-label small text-muted">Price</label>
                                <input type="number" name="price" id="edit_price" class="form-control" step="0.01"
                                    required>
                            </div>

                        </div>

                        <!-- FOOTER -->
                        <div class="modal-footer border-0 px-0 pt-3">
                            <button type="button" class="btn btn-sm btn-light" data-bs-dismiss="modal">
                                Cancel
                            </button>
                            <button type="submit" class="btn btn-sm btn-warning px-4">
                                Update
                            </button>
                        </div>

                    </form>
                </div>

            </div>
        </div>
    </div>


    <script> //Fill data in Edit Modal
        document.getElementById('editProductModal')
            .addEventListener('show.bs.modal', function (event) {

                let button = event.relatedTarget;

                let id = button.getAttribute('data-id');
                let name = button.getAttribute('data-name');
                let categoryId = button.getAttribute('data-category-id');
                let description = button.getAttribute('data-description');
                let quantity = button.getAttribute('data-quantity');
                let price = button.getAttribute('data-price');

                document.getElementById('edit_id').value = id;
                document.getElementById('edit_name').value = name;
                document.getElementById('edit_category').value = categoryId;
                document.getElementById('edit_description').value = description;
                document.getElementById('edit_quantity').value = quantity;
                document.getElementById('edit_price').value = price;
            });
    </script>

    <script> // Search item
        document.getElementById('productSearch').addEventListener('input', function () {
            const searchValue = this.value.toLowerCase();
            const rows = document.querySelectorAll('#productsTable tbody tr');

            rows.forEach(row => {
                const rowText = row.innerText.toLowerCase();
                row.style.display = rowText.includes(searchValue) ? '' : 'none';
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






    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

</body>

</html>