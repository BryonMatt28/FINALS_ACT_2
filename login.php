<?php

require_once 'core/dbConfig.php';
require_once 'core/models.php';

$error = ''; 
$debug = ''; // For debugging, can remove after testing

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Check if the user exists in the database
    $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ?");
    $stmt->execute([$username]);
    $user = $stmt->fetch();

    // If the user exists and the password is correct
    if ($user && password_verify($password, $user['password'])) {

        // Set session variables after successful login
        $_SESSION['user_id'] = $user['user_id'];
        $_SESSION['username'] = $user['username']; // Store username in session

        // Debug info for login success (for testing)
        $debug .= "Login successful. User ID: " . $user['id'] . "<br>";

        // Redirect to index.php after login
        header("Location: index.php");
        exit();
    } else {
        // Invalid username or password
        $error = "Invalid username or password";
        $debug .= "Login failed for username: $username<br>"; // Debug info
    }
}

// If user is already logged in, redirect to index.php
if (isset($_SESSION['user_id'])) {
    $debug .= "User already logged in. Redirecting to index.php<br>"; // Debug info
    header("Location: index.php");
    exit();
}

// Debugging: show session data for testing
$debug .= "Session data before login: " . print_r($_SESSION, true) . "<br>"; // Debug info
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Job Application System</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            margin: 0;
            padding: 20px;
            background-color: #f4f4f4;
        }
        .container {
            max-width: 400px;
            margin: 50px auto;
            background-color: #fff;
            padding: 20px;
            border-radius: 5px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        h1 {
            text-align: center;
            color: #333;
        }
        form {
            display: flex;
            flex-direction: column;
        }
        input {
            margin-bottom: 10px;
            padding: 8px;
            border: 1px solid #ddd;
            border-radius: 4px;
        }
        button {
            background-color: #4CAF50;
            color: white;
            padding: 10px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            margin-bottom: 10px;
        }
        button:hover {
            background-color: #45a049;
        }
        .error {
            color: red;
            margin-bottom: 10px;
        }
        .register-link {
            text-align: center;
        }
        .debug {
            background-color: #f0f0f0;
            border: 1px solid #ccc;
            padding: 10px;
            margin-top: 20px;
            font-family: monospace;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Login</h1>
        <?php if ($error): ?>
            <p class="error"><?php echo htmlspecialchars($error); ?></p>
        <?php endif; ?>
        <form method="POST">
            <input type="text" name="username" placeholder="Username" required>
            <input type="password" name="password" placeholder="Password" required>
            <button type="submit">Login</button>
        </form>
        <div class="register-link">
            <p>Don't have an account? <a href="register.php">Register here</a></p>
        </div>

        <!-- Debugging info (remove after testing) -->
        <div class="debug"><?php echo nl2br(htmlspecialchars($debug)); ?></div>
    </div>
</body>
</html>
