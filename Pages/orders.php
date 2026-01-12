<?php
// MySQL connection
$host = "localhost";
$user = "root";
$pass = "";
$db = "ims"; // database name

$conn = new mysqli($host, $user, $pass, $db);

// Check connection
if ($conn->connect_error) {
    die("Database connection failed: " . $conn->connect_error);
}


//   FETCH ORDERS

$sql = "SELECT * FROM ORDERS ORDER BY ORDER_ID DESC";
$result = $conn->query($sql);

if (!$result) {
    die("Query failed: " . $conn->error);
}


//   FETCH PRODUCTS (ORDER DROPDOWN)
$productSql = "
    SELECT PRODUCT_ID, NAME, PRICE, QUANTITY
    FROM PRODUCTS
    WHERE QUANTITY > 0
";
$productResult = $conn->query($productSql);

if (!$productResult) {
    die("Product query failed: " . $conn->error);
}


//   FETCH ORDERS WITH PRODUCT DETAILS
$sql = "
    SELECT 
        O.ORDER_ID,
        O.CUSTOMER_NAME,
        O.PRODUCT_ID,
        O.ORDER_DATE,
        O.QUANTITY,
        O.TOTAL_AMOUNT,
        O.STATUS,
        P.NAME AS PRODUCT_NAME,
        P.PRICE
    FROM ORDERS O
    JOIN PRODUCTS P ON O.PRODUCT_ID = P.PRODUCT_ID
    ORDER BY O.ORDER_ID DESC
";
$result = $conn->query($sql);

if (!$result) {
    die("Order join query failed: " . $conn->error);
}


//   FETCH PRODUCTS FOR EDIT ORDER
$editProductSql = "
    SELECT PRODUCT_ID, NAME, PRICE
    FROM PRODUCTS
    WHERE QUANTITY > 0
";
$editProductResult = $conn->query($editProductSql);

if (!$editProductResult) {
    die("Edit product query failed: " . $conn->error);
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Orders</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        body {
            background-color: #f4f6f9;
        }

        .sidebar {
            width: 250px;
            min-height: 100vh;
            background: linear-gradient(180deg, #667eea, #764ba2);
        }

        .sidebar a {
            color: #e0e0e0;
            padding: 12px 20px;
            display: block;
            text-decoration: none;
            border-radius: 8px;
            margin-bottom: 5px;
        }

        .sidebar a.active,
        .sidebar a:hover {
            background-color: rgba(255, 255, 255, 0.2);
            color: #fff;
        }

        .content {
            padding: 25px;
        }

        .card {
            border-radius: 15px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.08);
            border: none;
        }
    </style>
</head>

<body>
    <div class="d-flex">

        <!-- SIDEBAR -->
        <div class="sidebar p-3">
            <h4 class="text-center text-white mb-4">INVENTORY MS</h4>
            <a href="/IMS/Pages/dashboard.php">Dashboard</a>
            <a href="/IMS/Pages/products.php">Products</a>
            <a href="/IMS/Pages/category.php">Categories</a>
            <a href="#" class="active">Orders</a>
            <a href="/IMS/Pages/users.php">Users</a>
            <a href="/IMS/index.html">Logout</a>
        </div>

        <!-- MAIN -->
        <div class="flex-grow-1">

            <!-- TOP NAVBAR -->
            <nav class="navbar navbar-light bg-white shadow-sm px-4">
                <span class="navbar-brand mb-0 h5">
                    Orders
                </span>
                <span class="fw-semibold">
                    Welcome, Admin
                </span>
            </nav>

            <div class="content">
                <?php if (isset($_GET['error']) && $_GET['error'] === 'insufficient_stock') { ?>
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <strong>Insufficient Stock!</strong>
                        The quantity you entered exceeds the available stock.
                        Please adjust the order quantity.
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php } ?>


                <!-- ORDERS TABLE -->
                <div class="card p-4">

                    <!-- TITLE -->
                    <h2 class="mb-3">Order List</h2>

                    <!-- SEARCH + ACTION -->
                    <div class="d-flex align-items-center gap-3">
                        <input type="text" id="orderSearch" class="form-control search-input " style="max-width: 220px;"
                            placeholder="Search order...">

                        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addOrderModal">
                            + Add Order
                        </button>
                    </div>

                    <br>

                    <!-- TABLE -->
                    <div class="table-responsive">
                        <table class="table align-middle" id="orderTable">
                            <thead>
                                <tr>
                                    <th>Order ID</th>
                                    <th>Customer</th>
                                    <th>Product</th>
                                    <th>Qty</th>
                                    <th>Date</th>
                                    <th>Total</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>

                            <tbody>
                                <?php while ($row = $result->fetch_assoc()) { ?>
                                    <tr>
                                        <td><?= $row['ORDER_ID'] ?></td>
                                        <td><?= htmlspecialchars($row['CUSTOMER_NAME']) ?></td>
                                        <td><?= htmlspecialchars($row['PRODUCT_NAME']) ?></td>
                                        <td><?= $row['QUANTITY'] ?></td>
                                        <td><?= date('Y-m-d', strtotime($row['ORDER_DATE'])) ?></td>

                                        <td>₱ <?= number_format($row['TOTAL_AMOUNT'], 2) ?></td>
                                        <td>
                                            <?php
                                            $badge = match ($row['STATUS']) {
                                                'Pending' => 'warning',
                                                'Completed' => 'success',
                                                'Cancelled' => 'danger',
                                                default => 'secondary'
                                            };
                                            ?>
                                            <span class="badge bg-<?= $badge ?>">
                                                <?= $row['STATUS'] ?>
                                            </span>
                                        </td>
                                        <td>
                                            <div class="d-flex gap-2">

                                                <!-- EDIT -->
                                                <?php $isCompleted = ($row['STATUS'] === 'Completed'); ?>

                                                <button class="btn btn-sm btn-warning <?= $isCompleted ? 'disabled' : '' ?>"
                                                    <?= $isCompleted ? 'disabled' : '' ?> data-bs-toggle="modal"
                                                    data-bs-target="#editOrderModal" data-id="<?= $row['ORDER_ID'] ?>"
                                                    data-customer="<?= htmlspecialchars($row['CUSTOMER_NAME'], ENT_QUOTES) ?>"
                                                    data-product="<?= htmlspecialchars($row['PRODUCT_NAME'], ENT_QUOTES) ?>"
                                                    data-product-id="<?= $row['PRODUCT_ID'] ?>"
                                                    data-quantity="<?= $row['QUANTITY'] ?>"
                                                    data-status="<?= $row['STATUS'] ?>">
                                                    Edit
                                                </button>



                                                <!-- DELETE -->
                                                <a href="/IMS/Order/DeleteOrder.php?id=<?= $row['ORDER_ID'] ?>"
                                                    class="btn btn-sm btn-danger"
                                                    onclick="return confirm('Delete this order?')">
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

    <!-- ADD ORDER MODAL -->
    <div class="modal fade" id="addOrderModal" tabindex="-1">
        <div class="modal-dialog modal-md modal-dialog-centered">
            <div class="modal-content border-0 rounded-4 shadow-sm">

                <!-- HEADER -->
                <div class="modal-header border-0 pb-0">
                    <h6 class="modal-title fw-semibold">Add Order</h6>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <!-- BODY -->
                <div class="modal-body pt-2">
                    <form action="/IMS/Order/AddOrder.php" method="POST">

                        <div class="row g-3">

                            <!-- Customer -->
                            <div class="col-12">
                                <label class="form-label small text-muted">Customer Name</label>
                                <input type="text" name="customer_name" class="form-control" required>
                            </div>

                            <!-- Product -->
                            <div class="col-12">
                                <label class="form-label small text-muted">Product</label>
                                <select name="product_id" id="productSelect" class="form-select" required>
                                    <option value="">Select product</option>
                                    <?php while ($p = $productResult->fetch_assoc()) { ?>
                                        <option value="<?= $p['PRODUCT_ID'] ?>" data-price="<?= $p['PRICE'] ?>"
                                            data-stock="<?= $p['QUANTITY'] ?>">
                                            <?= htmlspecialchars($p['NAME']) ?>
                                        </option>

                                    <?php } ?>
                                </select>
                            </div>

                            <!-- Price per Unit -->
                            <div class="col-6">
                                <label class="form-label small text-muted">Price per Unit</label>
                                <input type="text" id="price" class="form-control" readonly>
                            </div>

                            <!-- Quantity -->
                            <div class="col-6">
                                <label class="form-label small text-muted">Quantity</label>
                                <input type="number" name="quantity" id="quantity" class="form-control" min="1"
                                    required>

                            </div>

                            <!-- Total -->
                            <div class="col-12">
                                <label class="form-label small text-muted">Total Amount</label>
                                <input type="number" name="total_amount" id="total" class="form-control" step="0.01"
                                    readonly required>
                            </div>

                            <!-- Status -->
                            <div class="col-12">
                                <label class="form-label small text-muted">Status</label>
                                <select name="status" class="form-select" required>
                                    <option value="Pending">Pending</option>
                                    <option value="Completed">Completed</option>
                                </select>
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


    <!-- SEARCH SCRIPT -->
    <script>
        document.getElementById('orderSearch').addEventListener('input', function () {
            const value = this.value.toLowerCase();
            const rows = document.querySelectorAll('#orderTable tbody tr');

            rows.forEach(row => {
                row.style.display = row.innerText.toLowerCase().includes(value) ? '' : 'none';
            });
        });
    </script>

    <script> // Calculate Total Amount
        const productSelect = document.getElementById('productSelect');
        const priceInput = document.getElementById('price');
        const qtyInput = document.getElementById('quantity');
        const totalInput = document.getElementById('total');

        productSelect.addEventListener('change', function () {
            const price = this.options[this.selectedIndex].dataset.price || 0;
            priceInput.value = price;
            calculateTotal();
        });

        qtyInput.addEventListener('input', calculateTotal);

        function calculateTotal() {
            const price = parseFloat(priceInput.value) || 0;
            const qty = parseInt(qtyInput.value) || 0;
            totalInput.value = (price * qty).toFixed(2);
        }
    </script>

    <!-- EDIT ORDER MODAL -->
    <div class="modal fade" id="editOrderModal" tabindex="-1">
        <div class="modal-dialog modal-md modal-dialog-centered">
            <div class="modal-content border-0 rounded-4 shadow-sm">

                <div class="modal-header border-0 pb-0">
                    <h6 class="modal-title fw-semibold">Edit Order</h6>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body pt-2">
                    <form action="/IMS/Order/UpdateOrder.php" method="POST">

                        <input type="hidden" name="order_id" id="edit_order_id">

                        <div class="row g-3">

                            <!-- Customer -->
                            <div class="col-12">
                                <label class="form-label small text-muted">Customer Name</label>
                                <input type="text" name="customer_name" id="edit_customer" class="form-control"
                                    required>
                            </div>

                            <!-- Product -->
                            <div class="col-12">
                                <label class="form-label small text-muted">Product</label>
                                <select name="product_id" id="edit_product" class="form-select" required>
                                    <?php while ($p = $editProductResult->fetch_assoc()) { ?>
                                        <option value="<?= $p['PRODUCT_ID'] ?>" data-price="<?= $p['PRICE'] ?>">
                                            <?= htmlspecialchars($p['NAME']) ?>
                                        </option>
                                    <?php } ?>
                                </select>

                            </div>

                            <!-- Price -->
                            <div class="col-6">
                                <label class="form-label small text-muted">Price</label>
                                <input type="text" id="edit_price" class="form-control" readonly>
                            </div>

                            <!-- Quantity -->
                            <div class="col-6">
                                <label class="form-label small text-muted">Quantity</label>
                                <input type="number" name="quantity" id="edit_quantity" class="form-control" min="1"
                                    required>
                            </div>

                            <!-- Total -->
                            <div class="col-12">
                                <label class="form-label small text-muted">Total</label>
                                <input type="text" id="edit_total" class="form-control" readonly>
                            </div>

                            <!-- Status -->
                            <div class="col-12">
                                <label class="form-label small text-muted">Payment Status</label>
                                <select name="status" id="edit_status" class="form-select" required>
                                    <option value="Pending">Pending</option>
                                    <option value="Completed">Completed</option>
                                    <option value="Cancelled">Cancelled</option>
                                </select>
                            </div>


                        </div>

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

    <script>
        const editModal = document.getElementById('editOrderModal');

        editModal.addEventListener('show.bs.modal', function (event) {
            const btn = event.relatedTarget;

            const orderId = btn.dataset.id;
            const customer = btn.dataset.customer;
            const productId = btn.dataset.productId;
            const quantity = btn.dataset.quantity;

            document.getElementById('edit_order_id').value = orderId;
            document.getElementById('edit_customer').value = customer;
            document.getElementById('edit_product').value = productId;
            document.getElementById('edit_quantity').value = quantity;

            updateEditPrice();
        });

        const editProduct = document.getElementById('edit_product');
        const editQty = document.getElementById('edit_quantity');
        const editPrice = document.getElementById('edit_price');
        const editTotal = document.getElementById('edit_total');

        editProduct.addEventListener('change', updateEditPrice);
        editQty.addEventListener('input', updateEditTotal);

        function updateEditPrice() {
            const price = editProduct.options[editProduct.selectedIndex].dataset.price || 0;
            editPrice.value = price;
            updateEditTotal();
        }

        function updateEditTotal() {
            const price = parseFloat(editPrice.value) || 0;
            const qty = parseInt(editQty.value) || 0;
            editTotal.value = (price * qty).toFixed(2);
        }
    </script>

    <script>
        // Fill data in Edit Order Modal
        document.getElementById('editOrderModal')
            .addEventListener('show.bs.modal', function (event) {

                let button = event.relatedTarget;

                let id = button.getAttribute('data-id');
                let customer = button.getAttribute('data-customer');
                let productId = button.getAttribute('data-product-id');
                let quantity = button.getAttribute('data-quantity');
                let status = button.getAttribute('data-status');

                document.getElementById('edit_order_id').value = id;
                document.getElementById('edit_customer').value = customer;
                document.getElementById('edit_product').value = productId;
                document.getElementById('edit_quantity').value = quantity;
                document.getElementById('edit_status').value = status;

                updateEditPrice();
            });
    </script>

    <script> //max quantity
        const productSelect = document.getElementById('productSelect');
        const qtyInput = document.getElementById('quantity');
        const priceInput = document.getElementById('price');
        const totalInput = document.getElementById('total');

        productSelect.addEventListener('change', function () {
            const selected = this.options[this.selectedIndex];

            const price = selected.getAttribute('data-price') || 0;
            const stock = selected.getAttribute('data-stock') || 0;

            priceInput.value = price;
            qtyInput.max = stock;
            qtyInput.value = 1;

            calculateTotal();
        });

        qtyInput.addEventListener('input', function () {
            if (parseInt(this.value) > parseInt(this.max)) {
                this.value = this.max;
            }
            calculateTotal();
        });

        function calculateTotal() {
            const price = parseFloat(priceInput.value) || 0;
            const qty = parseInt(qtyInput.value) || 0;
            totalInput.value = (price * qty).toFixed(2);
        }
    </script>






    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>