<?php
session_start();

// Database connection
$servername = "localhost";
$dbname = "login_db";
$dbusername = "root"; // change this to your DB username
$dbpassword = "madhu"; // change this to your DB password

$conn = new mysqli($servername, $dbusername, $dbpassword, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}


// Get the submitted form data
$username = $_POST['username'] ?? '';
$password = $_POST['password'] ?? '';
$role = $_POST['role'] ?? '';

// Prepare and execute the query
$stmt = $conn->prepare("SELECT password, role FROM users WHERE username = ?");
$stmt->bind_param("s", $username);
$stmt->execute();
$stmt->bind_result($db_password, $db_role);
$stmt->fetch();
$stmt->close();

// Validate the credentials
if ($db_password && password_verify($password, $db_password) && $db_role === $role) {
    // Set session variables
    $_SESSION['username'] = $username;
    $_SESSION['role'] = $role;

    // Redirect based on the role
    switch ($role) {
        case 'admin':
            header('Location: admin.html');
            break;
        case 'user':
            header('Location: home.html');
            break;
        case 'driver':
            header('Location: driver.html');
            break;
        default:
            header('Location: login.html');
    }
    exit;
} else {
    // Invalid credentials
    $_SESSION['message'] = 'Invalid username, password, or role.';
    header('Location: login.html');
    exit;
}
$conn->close();
?>
