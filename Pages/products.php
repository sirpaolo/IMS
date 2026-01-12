<?php
$serverName = "HELIOS";
$connectionOptions = [
    "Database" => "IMS",
    "Uid" => "",
    "PWD" => ""
];
$conn = sqlsrv_connect($serverName, $connectionOptions);
if ($conn == false) {
    die(print_r(sqlsrv_errors(), true));
} else {
    echo "";
}


$sql = "SELECT * FROM PRODUCTS";
$resultall = sqlsrv_query($conn, $sql);

$sql2 = "SELECT COUNT (PRODUCT_ID) AS TOTAL FROM PRODUCTS";
$resulttotal = sqlsrv_query($conn, $sql2);
$resultarray = sqlsrv_fetch_array($resulttotal);
$totalcount = $resultarray["TOTAL"];


?>



<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Products | Inventory</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        body {
            background-color: #f4f6f9;
        }

        /* Sidebar */
        .sidebar {
            width: 250px;
            min-height: 100vh;
            background: linear-gradient(180deg, #667eea, #764ba2);
        }

        .sidebar h4 {
            color: #fff;
            font-weight: 700;
        }

        .sidebar a {
            color: #e0e0e0;
            text-decoration: none;
            padding: 12px 20px;
            display: block;
            border-radius: 8px;
            margin-bottom: 5px;
        }

        .sidebar a:hover,
        .sidebar a.active {
            background-color: rgba(255, 255, 255, 0.2);
            color: #fff;
        }

        /* Content */
        .content {
            padding: 25px;
        }

        .card {
            border: none;
            border-radius: 15px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.08);
        }

        /* Search */
        .search-input {
            max-width: 250px;
        }
    </style>
</head>

<body>

    <div class="d-flex">

        <!-- SIDEBAR -->
        <div class="sidebar p-3">
            <h4 class="text-center mb-4">INVENTORY</h4>

            <a href="dashboard.php">Dashboard</a>
            <a href="products.php" class="active">Products</a>
            <a href="#">Categories</a>
            <a href="#">Stock In</a>
            <a href="#">Stock Out</a>
            <a href="#">Suppliers</a>
            <a href="#">Reports</a>
            <a href="#">Users</a>
            <a href="#">Logout</a>
        </div>

        <!-- MAIN CONTENT -->
        <div class="flex-grow-1">

            <!-- TOP NAVBAR -->
            <nav class="navbar navbar-light bg-white shadow-sm px-4">
                <span class="navbar-brand mb-0 h5">
                    Products
                </span>
                <span class="fw-semibold">
                    Welcome, Admin
                </span>

            </nav>

            <!-- PAGE CONTENT -->
            <div class="content">


                <!-- PRODUCTS TABLE -->
                <div class="card p-4">
                    <h5 class="mb-3">Inventory</h5><br>

                    <div class="d-flex align-items-center gap-3">
                        <input type="text" class="form-control search-input" placeholder="Search product name...">
                        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addProductModal">
                            + Add Product
                        </button>
                    </div><br>

                    <div class="table-responsive">
                        <table class="table align-middle">
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
                                while ($row = sqlsrv_fetch_array($resultall, SQLSRV_FETCH_ASSOC)) {

                                    $data1 = $row["PRODUCT_ID"];
                                    $data2 = $row["NAME"];
                                    $data3 = $row["CATEGORY"];
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
                                        <td>' . $data6 . '</td>
                                        <td>
                                            <div class="d-flex gap-2">

                                                <!-- EDIT BUTTON -->
                                                <button 
                                                    class="btn btn-sm btn-warning"
                                                    data-bs-toggle="modal"
                                                    data-bs-target="#editProductModal"
                                                    data-id="' . $data1 . '"
                                                    data-name="' . htmlspecialchars($data2, ENT_QUOTES) . '"
                                                    data-category="' . htmlspecialchars($data3, ENT_QUOTES) . '"
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
                    <h4>Total number of products:
                        <?php echo $totalcount; ?>
                    </h4>

                </div>

            </div>
        </div>
    </div>

    <!-- ADD PRODUCT MODAL -->
    <div class="modal fade" id="addProductModal" tabindex="-1">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content border-0 rounded-4">

                <!-- MODAL HEADER -->
                <div class="modal-header bg-light">
                    <h5 class="modal-title">Add New Product</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <!-- MODAL BODY -->
                <div class="modal-body p-4">
                    <form action="/IMS/Products/AddProduct.php" method="POST">

                        <div class="row g-3">

                            <!-- Product Name -->
                            <div class="col-md-6">
                                <label class="form-label">Product Name</label>
                                <input type="text" name="name" class="form-control" placeholder="Enter product name"
                                    required>
                            </div>

                            <!-- Category -->
                            <div class="col-md-6">
                                <label class="form-label">Category</label>
                                <input type="text" name="category" class="form-control" placeholder="Enter category"
                                    required>
                            </div>

                            <!-- Description -->
                            <div class="col-12">
                                <label class="form-label">Description</label>
                                <textarea class="form-control" name="description" rows="3"
                                    placeholder="Product description"></textarea>
                            </div>

                            <!-- Quantity -->
                            <div class="col-md-4">
                                <label class="form-label">Quantity</label>
                                <input type="number" name="quantity" class="form-control" min="0" required>
                            </div>

                            <!-- Price -->
                            <div class="col-md-4">
                                <label class="form-label">Price</label>
                                <input type="number" name="price" class="form-control" step="0.01" required>
                            </div>

                            <!-- MODAL FOOTER -->
                            <div class="modal-footer bg-light">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                                    Cancel
                                </button>
                                <button type="submit" class="btn btn-primary">
                                    ADD
                                </button>
                            </div>

                        </div>

                    </form>
                </div>



            </div>
        </div>
    </div>
    <!-- EDIT PRODUCT MODAL -->
    <div class="modal fade" id="editProductModal" tabindex="-1">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content border-0 rounded-4">

                <div class="modal-header bg-light">
                    <h5 class="modal-title">Edit Product</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body p-4">
                    <form action="/IMS/Products/EditProduct.php" method="POST">

                        <input type="hidden" name="product_id" id="edit_id">

                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">Product Name</label>
                                <input type="text" name="name" id="edit_name" class="form-control" required>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Category</label>
                                <input type="text" name="category" id="edit_category" class="form-control" required>
                            </div>

                            <div class="col-12">
                                <label class="form-label">Description</label>
                                <textarea name="description" id="edit_description" class="form-control"
                                    rows="3"></textarea>
                            </div>

                            <div class="col-md-4">
                                <label class="form-label">Quantity</label>
                                <input type="number" name="quantity" id="edit_quantity" class="form-control" required>
                            </div>

                            <div class="col-md-4">
                                <label class="form-label">Price</label>
                                <input type="number" name="price" id="edit_price" class="form-control" step="0.01"
                                    required>
                            </div>
                        </div>

                        <div class="modal-footer bg-light mt-3">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                                Cancel
                            </button>
                            <button type="submit" class="btn btn-warning">
                                Update
                            </button>
                        </div>

                    </form>
                </div>

            </div>
        </div>
    </div>
    <script> //edit product modal
        document.getElementById('editProductModal').addEventListener('show.bs.modal', function (event) {

            // Button that triggered the modal
            let button = event.relatedTarget;

            // Extract data from attributes
            let id = button.getAttribute('data-id');
            let name = button.getAttribute('data-name');
            let category = button.getAttribute('data-category');
            let description = button.getAttribute('data-description');
            let quantity = button.getAttribute('data-quantity');
            let price = button.getAttribute('data-price');

            // Populate the modal fields
            document.getElementById('edit_id').value = id;
            document.getElementById('edit_name').value = name;
            document.getElementById('edit_category').value = category;
            document.getElementById('edit_description').value = description;
            document.getElementById('edit_quantity').value = quantity;
            document.getElementById('edit_price').value = price;
        });
    </script>



    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

</body>

</html>