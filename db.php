<?php
$host = "localhost";
$user = "root";
$password = "";
$dbname = "bsc_project";  // Replace this with your actual DB name

$conn = new mysqli($host, $user, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
