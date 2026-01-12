<?php
// MySQL connection setup
$host = "localhost";
$user = "root";
$pass = "";
$db = "IMS"; // database name

$conn = new mysqli($host, $user, $pass, $db);

// Check connection
if ($conn->connect_error) {
    die("Database connection failed: " . $conn->connect_error);
}

// Get form data
$email = $_POST['email'];
$password = $_POST['password'];

// Prepare SQL (MySQL version)
$sql = "SELECT * FROM USERS WHERE EMAIL = ?";
$stmt = $conn->prepare($sql);

if (!$stmt) {
    die("Prepare failed: " . $conn->error);
}

// Bind parameter
$stmt->bind_param("s", $email);

// Execute
$stmt->execute();

// Get result
$result = $stmt->get_result();
$row = $result->fetch_assoc();

// Check email
if (!$row) {
    echo "Email not found";
    $stmt->close();
    $conn->close();
    exit();
}

// Check password (plain-text comparison – NOT recommended for production)
if ($password === $row['PASSWORD']) {

    // Redirect on success
    header("Location: /IMS/Pages/dashboard.php");
    exit();

} else {
    echo "Incorrect password";
}

// Close connections
$stmt->close();
$conn->close();
?>