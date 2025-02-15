<?php
include 'connect.php';

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Create database connection
$mysqli = new mysqli('localhost:3307', 'root', ' ', 'crudoperations');

if ($mysqli->connect_errno) {
    echo "Failed to connect: " . $mysqli->connect_error;
    exit();
}

if (isset($_GET['submit'])) {
    $password = mysqli_real_escape_string($mysqli, $_GET['password']);
    $name = mysqli_real_escape_string($mysqli, $_GET['name']);

    // Prepare SQL query with prepared statements
    $stmt = $mysqli->prepare("SELECT * FROM crud WHERE name = ? AND password = ?");
    $stmt->bind_param("ss", $name, $password);
    $stmt->execute();
    $result = $stmt->get_result();

    // Check if user exists
    if (mysqli_num_rows($result) == 1) {
        header("location: display.php");
    } else {
        echo "Invalid credentials!";
    }
}
?>
