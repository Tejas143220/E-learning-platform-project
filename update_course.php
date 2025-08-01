<?php
session_start();
include 'db.php';

if (!isset($_SESSION['user'])) {
    header("Location: login.html");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    die("Invalid request method.");
}

$course_id = intval($_POST['course_id'] ?? 0);
if ($course_id <= 0) {
    die("Invalid course selected.");
}

// Fetch current course data
$stmt = $conn->prepare("SELECT * FROM courses WHERE id = ?");
$stmt->bind_param("i", $course_id);
$stmt->execute();
$current_course = $stmt->get_result()->fetch_assoc();

if (!$current_course) {
    die("Course not found.");
}

// Prepare new data - update only if provided, else keep old data
$title = trim($_POST['title'] ?? '') ?: $current_course['title'];
$video_url = trim($_POST['video_url'] ?? '') ?: $current_course['video_url'];
$description = trim($_POST['description'] ?? '') ?: $current_course['description'];

// Handle price and is_paid checkbox
$is_paid = isset($_POST['is_paid']) ? 1 : 0;

// Price: if price provided and numeric, else keep old price or 0 if paid is 0
$price_input = trim($_POST['price'] ?? '');
if ($is_paid) {
    $price = is_numeric($price_input) && $price_input >= 0 ? $price_input : $current_course['price'];
    if ($price === null) $price = 0;
} else {
    $price = 0; // free course must have 0 price
}

// Update query
$update_stmt = $conn->prepare("UPDATE courses SET title = ?, video_url = ?, description = ?, is_paid = ?, price = ? WHERE id = ?");
$update_stmt->bind_param("sssiii", $title, $video_url, $description, $is_paid, $price, $course_id);

if ($update_stmt->execute()) {
    header("Location: dashboard.php?msg=update_success");
} else {
    header("Location: dashboard.php?msg=update_failed");
}
exit();
?>
