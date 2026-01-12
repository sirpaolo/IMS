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

// Fetch total products
$sql = "SELECT * FROM PRODUCTS";
$resultall = sqlsrv_query($conn, $sql);

$sql = "SELECT COUNT (PRODUCT_ID) AS TOTAL FROM PRODUCTS";
$resulttotal = sqlsrv_query($conn, $sql);
$resultarray = sqlsrv_fetch_array($resulttotal);
$totalproducts = $resultarray["TOTAL"];

// Fetch total stock
$sql11 = "SELECT * FROM PRODUCTS";
$resultall11 = sqlsrv_query($conn, $sql11);

$sql22 = "SELECT SUM (QUANTITY) AS TOTAL FROM PRODUCTS";
$resulttotal22 = sqlsrv_query($conn, $sql22);
$resultarray22 = sqlsrv_fetch_array($resulttotal22);
$totalstock = $resultarray22["TOTAL"];

// Fetch revenue
$sql33 = "SELECT * FROM PRODUCTS";
$resultall33 = sqlsrv_query($conn, $sql33);

$sql44 = "SELECT SUM (PRICE * QUANTITY) AS TOTAL FROM PRODUCTS";
$resulttotal44 = sqlsrv_query($conn, $sql44);
$resultarray44 = sqlsrv_fetch_array($resulttotal44);
$totalrevenue = $resultarray44["TOTAL"];

// Product status
$sql55 = "
    SELECT 
        NAME,
        QUANTITY,
        CASE
            WHEN QUANTITY = 0 THEN 'Out of Stock'
            WHEN QUANTITY <= 5 THEN 'Low Stock'
        END AS STATUS
    FROM PRODUCTS
    WHERE QUANTITY <= 5
";

$resulttotal55 = sqlsrv_query($conn, $sql55);
?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Inventory Dashboard</title>
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

        .stat-number {
            font-size: 28px;
            font-weight: 700;
        }
    </style>
</head>

<body>

    <div class="d-flex">

        <!-- SIDEBAR -->
        <div class="sidebar p-3">
            <h4 class="text-center mb-4">INVENTORY</h4>

            <a href="#" class="active">Dashboard</a>
            <a href="/IMS/Pages/products.php">Products</a>
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
                    Dashboard
                </span>
                <span class="fw-semibold">
                    Welcome, Admin
                </span>
            </nav>

            <!-- PAGE CONTENT -->
            <div class="content">

                <h3 class="mb-4">Overview</h3>

                <!-- STAT CARDS -->
                <div class="row g-4 mb-4">

                    <div class="col-md-3">
                        <div class="card p-3">
                            <p class="text-muted mb-1">Total Products</p>
                            <div class="stat-number">
                                <?php echo $totalproducts; ?>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-3">
                        <div class="card p-3">
                            <p class="text-muted mb-1">Available Stock</p>
                            <div class="stat-number">
                                <?php echo $totalstock; ?>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-3">
                        <div class="card p-3">
                            <p class="text-muted mb-1">Low Stock Items</p>
                            <div class="stat-number text-danger">8</div>
                        </div>
                    </div>

                    <div class="col-md-3">
                        <div class="card p-3">
                            <p class="text-muted mb-1">Revenue</p>
                            <div class="stat-number">
                                <?php echo $totalrevenue; ?>
                            </div>
                        </div>
                    </div>

                </div>

                <!-- TABLE -->
                <div class="card p-4">
                    <h5 class="mb-3">Recent Products</h5>

                    <div class="table-responsive">
                        <table class="table align-middle">
                            <thead>
                                <tr>
                                    <th>Product Name</th>
                                    <th>Stock</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <?php
                            while ($row = sqlsrv_fetch_array($resulttotal55, SQLSRV_FETCH_ASSOC)) {
                                $data1 = $row["NAME"];
                                $data2 = $row["QUANTITY"];
                                $data3 = $row["STATUS"];

                                echo '<tr>
                                        <td>' . $data1 . '</td>
                                        <td>' . $data2 . '</td>
                                        <td>' . $data3 . '</td>
                                    </tr>';
                            }
                            ?>

                        </table>
                    </div>

                </div>

            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

</body>

</html>