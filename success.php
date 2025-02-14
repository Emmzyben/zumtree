<?php
session_start();
require_once './database/db_config.php';

if (!isset($_GET['data'])) {
    echo "Invalid request.";
    exit;
}

// Decode and validate user data
$userData = json_decode(base64_decode($_GET['data']), true);

if (!$userData || !isset($userData['email'])) {
    echo "Invalid or corrupted user data.";
    exit;
}

$name = htmlspecialchars(trim($userData['name']));
$email = htmlspecialchars(trim($userData['email']));
$password = $userData['password']; 
$location = htmlspecialchars(trim($userData['location']));
$plan = htmlspecialchars(trim($userData['plan']));

try {
    // Ensure hashed password security

    $insertQuery = "INSERT INTO users (name, email, password, location, plan) VALUES (?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($insertQuery);
    $stmt->bind_param("sssss", $name, $email, $password, $location, $plan);

    if ($stmt->execute()) {
        // Set session variables for the new user
        $_SESSION['user_id'] = $conn->insert_id;
        $_SESSION['name'] = $name;
        $_SESSION['email'] = $email;

        // Redirect to the dashboard on success
        header("Location: dashboard.php?success=1");
        exit;
    } else {
        echo "Failed to register user. Error: " . $stmt->error;
    }

    $stmt->close();
} catch (Exception $e) {
    echo "Error processing the request: " . $e->getMessage();
}
?>
