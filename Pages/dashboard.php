<?php

$host = "localhost";
$user = "root";
$pass = "";
$db = "ims"; // database name

$conn = new mysqli($host, $user, $pass, $db);

// Check connection
if ($conn->connect_error) {
    die("Database connection failed: " . $conn->connect_error);
}

//   TOTAL PRODUCTS
$result = $conn->query("SELECT COUNT(PRODUCT_ID) AS TOTAL FROM PRODUCTS");
$row = $result->fetch_assoc();
$totalproducts = $row['TOTAL'];

//   TOTAL STOCK
$result = $conn->query("SELECT SUM(QUANTITY) AS TOTAL FROM PRODUCTS");
$row = $result->fetch_assoc();
$totalstock = $row['TOTAL'];

//   TOTAL ORDERS
$result = $conn->query("SELECT COUNT(ORDER_ID) AS TOTAL FROM ORDERS");
$row = $result->fetch_assoc();
$totalorders = $row['TOTAL'];

//   LOW STOCK COUNT
$result = $conn->query("
    SELECT COUNT(PRODUCT_ID) AS LOWSTOCKCOUNT
    FROM PRODUCTS
    WHERE QUANTITY <= 10
");
$row = $result->fetch_assoc();
$lowStockCount = $row['LOWSTOCKCOUNT'];

//   TOTAL REVENUE
$result = $conn->query("SELECT SUM(TOTAL_AMOUNT) AS TOTAL FROM ORDERS");
$row = $result->fetch_assoc();
$totalrevenue = $row['TOTAL'];

//   LOW STOCK ITEMS
$resulttotal55 = $conn->query("
    SELECT NAME, QUANTITY, 'Low Stock' AS STATUS
    FROM PRODUCTS
    WHERE QUANTITY BETWEEN 1 AND 10
");

//   OUT OF STOCK ITEMS
$resultOut = $conn->query("
    SELECT NAME, QUANTITY
    FROM PRODUCTS
    WHERE QUANTITY = 0
");

//   STOCK LEVELS (CHART)
$chartResult = $conn->query("SELECT NAME, QUANTITY FROM PRODUCTS");

$productNames = [];
$productStocks = [];

while ($row = $chartResult->fetch_assoc()) {
    $productNames[] = $row['NAME'];
    $productStocks[] = $row['QUANTITY'];
}

//   CATEGORY-WISE PRODUCT COUNT
$resultCatChart = $conn->query("
    SELECT C.CATEGORY_NAME, COUNT(P.PRODUCT_ID) AS TOTAL
    FROM CATEGORIES C
    LEFT JOIN PRODUCTS P ON P.CATEGORY_ID = C.CATEGORY_ID
    GROUP BY C.CATEGORY_NAME
");

$categoryLabels = [];
$categoryCounts = [];

while ($row = $resultCatChart->fetch_assoc()) {
    $categoryLabels[] = $row['CATEGORY_NAME'];
    $categoryCounts[] = $row['TOTAL'];
}

//   RECENT PRODUCTS (LATEST 3)
$resultRecent = $conn->query("
    SELECT NAME, QUANTITY
    FROM PRODUCTS
    ORDER BY PRODUCT_ID DESC
    LIMIT 3
");
?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Inventory Dashboard</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="/IMS/Pages/template.css">
</head>

<body>

    <div class="d-flex">

        <!-- SIDEBAR -->
        <div class="sidebar p-3">
            <h4 class="text-center mb-4">INVENTORY MS</h4>

            <!-- Top links -->
            <div class="sidebar-menu">
                <a href="#" class="active">Dashboard</a>
                <a href="/IMS/Pages/products.php">Products</a>
                <a href="/IMS/Pages/category.php">Categories</a>
                <a href="/IMS/Pages/orders.php">Orders</a>
                <a href="/IMS/Pages/users.php">Users</a>
            </div>

            <!-- Bottom links -->
            <div class="sidebar-bottom">
                <a href="#">Profile</a>
                <a href="/IMS/index.html">Logout</a>
            </div>
        </div>



        <!-- MAIN CONTENT -->
        <div class="flex-grow-1 main-content">

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
                        <div class="card p-3 stat-card stat-products">
                            <p class="text-muted mb-1">Total Products</p>
                            <div class="stat-number">
                                <?php echo $totalproducts; ?>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-3">
                        <div class="card p-3 stat-card stat-stock">
                            <p class="text-muted mb-1">Available Stock</p>
                            <div class="stat-number">
                                <?php echo $totalstock; ?>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-3">
                        <div class="card p-3 stat-card stat-orders">
                            <p class="text-muted mb-1">Orders Today</p>
                            <div class="stat-number">
                                <?php echo $totalorders; ?>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-3">
                        <div class="card p-3 stat-card stat-revenue">
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
                            <div class="d-flex justify-content-center align-items-center" style="height: 200px;">
                                <canvas id="categoryChart" width="180" height="180"></canvas>
                            </div>
                        </div>
                    </div>

                    <!-- STOCK LEVEL CHART -->
                    <div class="col-md-8">
                        <div class="card p-4 h-100">
                            <h5 class="mb-3">Stock Levels by Product</h5>
                            <canvas id="stockChart" height="220"></canvas>
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
                                        while ($row = $resulttotal55->fetch_assoc()) {
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
                                        while ($row = $resultOut->fetch_assoc()) {
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
                                        while ($row = $resultRecent->fetch_assoc()) {
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
            if (qty <= 10) return 'rgba(255, 193, 7, 0.8)';     // Orange
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
                                if (value <= 10) return ' Low Stock (' + value + ')';
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