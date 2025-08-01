<?php
include 'db.php'; // Make sure this has a working $conn mysqli connection

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Check if all required POST data exists
    if (!empty($_POST['username']) && !empty($_POST['email']) && !empty($_POST['password'])) {

        // Clean and assign variables
        $username = trim($_POST['username']);
        $email = trim($_POST['email']);
        $password = $_POST['password'];

        // Hash the password
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        // Prepare statement
        $stmt = $conn->prepare("INSERT INTO users (username, email, password) VALUES (?, ?, ?)");
        if (!$stmt) {
            die("Prepare failed: " . $conn->error);
        }
        $stmt->bind_param("sss", $username, $email, $hashedPassword);

        // Execute and check
        if ($stmt->execute()) {
            // Redirect to login page on success
            header("Location: login.html");
            exit();
        } else {
            echo "Database error: " . $stmt->error;
        }

        $stmt->close();
        $conn->close();

    } else {
        echo "Please fill in all the required fields.";
    }

} else {
    echo "Invalid request method.";
}
?>
