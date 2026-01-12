<?php
$serverName = "HELIOS";
$connectionOptions = [
    "Database" => "IMS",
    "Uid" => "",
    "PWD" => ""
];
$conn = sqlsrv_connect($serverName, $connectionOptions);
if (!$conn) {
    die(print_r(sqlsrv_errors(), true));
}

$sql = "SELECT * FROM ORDERS ORDER BY ORDER_ID DESC";
$result = sqlsrv_query($conn, $sql);

// Fetch products for order dropdown
$productSql = "SELECT PRODUCT_ID, NAME, PRICE FROM PRODUCTS WHERE QUANTITY > 0";
$productResult = sqlsrv_query($conn, $productSql);

$sql = "SELECT 
        O.ORDER_ID,
        O.CUSTOMER_NAME,
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
$result = sqlsrv_query($conn, $sql);


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
            <a href="#">Users</a>
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
                                <?php while ($row = sqlsrv_fetch_array($result, SQLSRV_FETCH_ASSOC)) { ?>
                                    <tr>
                                        <td><?= $row['ORDER_ID'] ?></td>
                                        <td><?= htmlspecialchars($row['CUSTOMER_NAME']) ?></td>
                                        <td><?= htmlspecialchars($row['PRODUCT_NAME']) ?></td>
                                        <td><?= $row['QUANTITY'] ?></td>
                                        <td><?= $row['ORDER_DATE']->format('Y-m-d') ?></td>
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
                                                <button class="btn btn-sm btn-warning" data-bs-toggle="modal"
                                                    data-bs-target="#editOrderModal" data-id="<?= $row['ORDER_ID'] ?>"
                                                    data-customer="<?= htmlspecialchars($row['CUSTOMER_NAME'], ENT_QUOTES) ?>"
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
                                    <?php while ($p = sqlsrv_fetch_array($productResult, SQLSRV_FETCH_ASSOC)) { ?>
                                        <option value="<?= $p['PRODUCT_ID'] ?>" data-price="<?= $p['PRICE'] ?>">
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



    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>