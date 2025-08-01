<?php
include 'db.php';

$course_id = $_POST['course_id'];
$conn->query("DELETE FROM courses WHERE id = $course_id");

header("Location: dashboard.php");
exit();
?>
