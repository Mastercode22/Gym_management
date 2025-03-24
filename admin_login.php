<?php
session_start();
require 'config.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';

    // Check credentials (replace with your actual admin credentials)
    $admin_username = "admin";
    $admin_password = "admin123"; // Change this to a secure password

    if ($username === $admin_username && $password === $admin_password) {
        $_SESSION['admin_logged_in'] = true;
        header("Location: admin.php");
        exit();
    } else {
        $error = "Invalid username or password!";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login</title>
    <link rel="stylesheet" href="assets/css/bootstrap.min.css">
    <style>
        /* Use your image as full-screen background */
        body {
            background: url('img/joke.jpg') no-repeat center center fixed;
            background-size: cover;
            margin: 0;
            height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
        }
        /* Semi-transparent login container */
        .login-container {
            background: rgba(51, 51, 51, 0.8);
            padding: 20px;
            border-radius: 10px;
            text-align: center;
            width: 300px;
        }
        input {
            width: 100%;
            padding: 10px;
            margin: 10px 0;
            border-radius: 5px;
            border: none;
            background: #444;
            color: white;
        }
        .login-btn {
            width: 100%;
            padding: 10px;
            background: #ff9900;
            color: black;
            border: none;
            cursor: pointer;
        }
        .login-btn:hover {
            background: #ff7700;
        }
       .login-container h2 {
        color:#fff;
        padding:10px;
       }
    </style>
</head>
<body>
<div class="login-container">
    <h2>Admin Login</h2>
    <?php if (isset($error)) { echo "<p style='color: red;'>$error</p>"; } ?>
    <form method="POST">
        <input type="text" name="username" placeholder="Username" required>
        <input type="password" name="password" placeholder="Password" required>
        <button type="submit" class="login-btn">Login</button>
    </form>
</div>
</body>
</html>
