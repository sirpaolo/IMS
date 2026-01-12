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

// Low stock items count
$sqlLowStock = "
    SELECT COUNT (PRODUCT_ID) AS LOWSTOCKCOUNT
    FROM PRODUCTS
    WHERE QUANTITY <= 5
";
$resultLowStock = sqlsrv_query($conn, $sqlLowStock);
$resultLowStockArray = sqlsrv_fetch_array($resultLowStock);
$lowStockCount = $resultLowStockArray["LOWSTOCKCOUNT"];

// Fetch revenue
$sql33 = "SELECT * FROM PRODUCTS";
$resultall33 = sqlsrv_query($conn, $sql33);

$sql44 = "SELECT SUM (PRICE * QUANTITY) AS TOTAL FROM PRODUCTS";
$resulttotal44 = sqlsrv_query($conn, $sql44);
$resultarray44 = sqlsrv_fetch_array($resulttotal44);
$totalrevenue = $resultarray44["TOTAL"];

// Product status
// Low stock items
$sql55 = "
    SELECT 
        NAME,
        QUANTITY,
        CASE
            WHEN QUANTITY <= 5 THEN 'Low Stock'
        END AS STATUS
    FROM PRODUCTS
    WHERE QUANTITY BETWEEN 1 AND 5
";

$resulttotal55 = sqlsrv_query($conn, $sql55);

// Out of stock items
$sqlOut = "
    SELECT NAME, QUANTITY
    FROM PRODUCTS
    WHERE QUANTITY = 0
";
$resultOut = sqlsrv_query($conn, $sqlOut);


// Fetch stock levels for chart
$chartSql = "SELECT NAME, QUANTITY FROM PRODUCTS";
$chartResult = sqlsrv_query($conn, $chartSql);

$productNames = [];
$productStocks = [];

while ($row = sqlsrv_fetch_array($chartResult, SQLSRV_FETCH_ASSOC)) {
    $productNames[] = $row['NAME'];
    $productStocks[] = $row['QUANTITY'];
}

// Category-wise product count
$sqlCatChart = "
    SELECT C.CATEGORY_NAME, COUNT(P.PRODUCT_ID) AS TOTAL
    FROM CATEGORIES C
    LEFT JOIN PRODUCTS P ON P.CATEGORY_ID = C.CATEGORY_ID
    GROUP BY C.CATEGORY_NAME
";

$resultCatChart = sqlsrv_query($conn, $sqlCatChart);

$categoryLabels = [];
$categoryCounts = [];

while ($row = sqlsrv_fetch_array($resultCatChart, SQLSRV_FETCH_ASSOC)) {
    $categoryLabels[] = $row['CATEGORY_NAME'];
    $categoryCounts[] = $row['TOTAL'];
}

// Recently added products (latest 3)
$sqlRecent = "
    SELECT TOP 3 NAME, QUANTITY
    FROM PRODUCTS
    ORDER BY PRODUCT_ID DESC
";
$resultRecent = sqlsrv_query($conn, $sqlRecent);


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
            <h4 class="text-center mb-4">INVENTORY MS</h4>

            <a href="#" class="active">Dashboard</a>
            <a href="/IMS/Pages/products.php">Products</a>
            <a href="/IMS/Pages/category.php">Categories</a>
            <a href="/IMS/Pages/orders.php">Orders</a>
            <a href="#">Users</a>
            <a href="/IMS/index.html">Logout</a>
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
                            <div class="stat-number text-danger">

                                <?php echo $lowStockCount; ?>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-3">
                        <div class="card p-3">
                            <p class="text-muted mb-1">Revenue</p>
                            <div class="stat-number">
                                ₱ <?php echo $totalrevenue; ?>
                            </div>
                        </div>
                    </div>

                </div>

                <div class="row g-4 mb-4">

                    <!-- CATEGORY WISE PRODUCTS CHART (SMALL) -->
                    <div class="col-md-4">
                        <div class="card p-4 h-100">
                            <h5 class="mb-3">Category-wise Products</h5><br>
                            <div class="d-flex justify-content-center align-items-center" style="height: 220px;">
                                <canvas id="categoryChart" width="180" height="180"></canvas>
                            </div>
                        </div>
                    </div>

                    <!-- STOCK LEVEL CHART -->
                    <div class="col-md-8">
                        <div class="card p-4 h-100">
                            <h5 class="mb-3">Stock Levels by Product</h5>
                            <canvas id="stockChart" height="240"></canvas>
                        </div>
                    </div>

                </div>



                <div class="row g-4">

                    <!-- LOW STOCK ITEMS -->
                    <div class="col-md-4">
                        <div class="card p-4 h-100">
                            <h5 class="mb-3 text-warning">Low Stock Items</h5>

                            <div class="table-responsive">
                                <table class="table align-middle">
                                    <thead>
                                        <tr>
                                            <th>Product Name</th>
                                            <th>Stock</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        while ($row = sqlsrv_fetch_array($resulttotal55, SQLSRV_FETCH_ASSOC)) {
                                            echo '<tr>
                                <td>' . htmlspecialchars($row["NAME"]) . '</td>
                                <td>' . $row["QUANTITY"] . '</td>
                            </tr>';
                                        }
                                        ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <!-- OUT OF STOCK ITEMS -->
                    <div class="col-md-4">
                        <div class="card p-4 h-100">
                            <h5 class="mb-3 text-danger">Out of Stock Items</h5>

                            <div class="table-responsive">
                                <table class="table align-middle">
                                    <thead>
                                        <tr>
                                            <th>Product Name</th>
                                            <th>Stock</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        while ($row = sqlsrv_fetch_array($resultOut, SQLSRV_FETCH_ASSOC)) {
                                            echo '<tr>
                                <td>' . htmlspecialchars($row["NAME"]) . '</td>
                                <td>0</td>
                            </tr>';
                                        }
                                        ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <!-- RECENTLY ADDED PRODUCTS -->
                    <div class="col-md-4">
                        <div class="card p-4 h-100">
                            <h5 class="mb-3 text-primary">Recently Added Products</h5>

                            <div class="table-responsive">
                                <table class="table align-middle">
                                    <thead>
                                        <tr>
                                            <th>Product Name</th>
                                            <th>Stock</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        while ($row = sqlsrv_fetch_array($resultRecent, SQLSRV_FETCH_ASSOC)) {
                                            echo '<tr>
                                <td>' . htmlspecialchars($row["NAME"]) . '</td>
                                <td>' . $row["QUANTITY"] . '</td>
                            </tr>';
                                        }
                                        ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                </div>



            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <script>
        const stockData = <?php echo json_encode($productStocks); ?>;
        const labels = <?php echo json_encode($productNames); ?>;

        // Dynamic colors based on stock level
        const barColors = stockData.map(qty => {
            if (qty === 0) return 'rgba(220, 53, 69, 0.8)';     // Red
            if (qty <= 5) return 'rgba(255, 193, 7, 0.8)';     // Orange
            return 'rgba(40, 167, 69, 0.8)';                   // Green
        });

        const ctx = document.getElementById('stockChart').getContext('2d');

        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Stock Quantity',
                    data: stockData,
                    backgroundColor: barColors,
                    borderRadius: 8
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        callbacks: {
                            label: function (context) {
                                const value = context.raw;
                                if (value === 0) return ' Out of Stock';
                                if (value <= 5) return ' Low Stock (' + value + ')';
                                return ' In Stock (' + value + ')';
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            stepSize: 5
                        }
                    }
                }
            }
        });
    </script>
    <script>
        const catLabels = <?php echo json_encode($categoryLabels); ?>;
        const catData = <?php echo json_encode($categoryCounts); ?>;

        const categoryCtx = document.getElementById('categoryChart').getContext('2d');

        new Chart(categoryCtx, {
            type: 'doughnut',
            data: {
                labels: catLabels,
                datasets: [{
                    data: catData,
                    backgroundColor: [
                        '#667eea',
                        '#764ba2',
                        '#f6ad55',
                        '#48bb78',
                        '#ed64a6',
                        '#4299e1'
                    ],
                    borderWidth: 0
                }]
            },
            options: {
                responsive: true,
                cutout: '65%',
                plugins: {
                    legend: {
                        position: 'bottom'
                    },
                    tooltip: {
                        callbacks: {
                            label: function (context) {
                                return context.label + ': ' + context.raw + ' products';
                            }
                        }
                    }
                }
            }
        });
    </script>





</body>

</html>