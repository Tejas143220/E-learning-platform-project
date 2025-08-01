<?php
session_start();
include 'db.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.html");
    exit();
}

// Get user ID from session
$user_id = $_SESSION['user_id'];

// Check if course_id is posted and valid
if (!isset($_POST['course_id']) || empty($_POST['course_id'])) {
    header("Location: dashboard.php?msg=purchase_failed");
    exit();
}

$course_id = (int)$_POST['course_id'];

// Check if course exists and is paid
$stmt = $conn->prepare("SELECT is_paid FROM courses WHERE id = ?");
$stmt->bind_param("i", $course_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    // Course does not exist
    header("Location: dashboard.php?msg=purchase_failed");
    exit();
}

$course = $result->fetch_assoc();
if ($course['is_paid'] == 0) {
    // Course is free, no need to purchase
    header("Location: dashboard.php?msg=already_purchased");
    exit();
}

// Check if user already purchased this course
$stmt = $conn->prepare("SELECT * FROM course_purchases WHERE user_id = ? AND course_id = ?");
$stmt->bind_param("ii", $user_id, $course_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    header("Location: dashboard.php?msg=already_purchased");
    exit();
}

// Insert purchase record
$stmt = $conn->prepare("INSERT INTO course_purchases (user_id, course_id, purchase_date) VALUES (?, ?, NOW())");
$stmt->bind_param("ii", $user_id, $course_id);

if ($stmt->execute()) {
    header("Location: dashboard.php?msg=purchase_success");
} else {
    header("Location: dashboard.php?msg=purchase_failed");
}
exit();
?>
