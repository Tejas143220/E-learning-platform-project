<?php
include 'db.php';

$title = $_POST['title'];
$description = $_POST['description'];
$is_paid = isset($_POST['is_paid']) ? 1 : 0;
$price = isset($_POST['price']) ? $_POST['price'] : 0;
$video_url = isset($_POST['video_url']) ? $_POST['video_url'] : '';

$stmt = $conn->prepare("INSERT INTO courses (title, description, is_paid, price, video_url) VALUES (?, ?, ?, ?, ?)");
$stmt->bind_param("ssids", $title, $description, $is_paid, $price, $video_url);

if ($stmt->execute()) {
    header("Location: dashboard.php");
} else {
    echo "Error: " . $stmt->error;
}

$stmt->close();
$conn->close();
?>
