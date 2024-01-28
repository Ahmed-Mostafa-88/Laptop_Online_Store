<?php
require_once 'connection.php'; // Include connection file

// Initialize error message
$login_error = "";

// Check if form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = mysqli_real_escape_string($connection, $_POST["username"]);
    $password = mysqli_real_escape_string($connection, $_POST["password"]);

    // Prepare SQL query
    $sql = "SELECT * FROM Customer WHERE username = ? AND password = ?";

    // Prepare statement
    $stmt = mysqli_prepare($connection, $sql);

    // Bind parameters
    mysqli_stmt_bind_param($stmt, "ss", $username, $password);
    // Execute the statement
    mysqli_stmt_execute($stmt);
    // Get result
    $result = mysqli_stmt_get_result($stmt);

    // Check if username and password match
    if (mysqli_num_rows($result) == 1) {
        // Successful login, start session and redirect to laptops page
        session_start();
        $_SESSION["customer_id"] = mysqli_fetch_assoc($result)["customer_id"]; // Store customer ID in session
        header("Location: laptops.php");
        exit;
    } else {
        $login_error = "login failed.Invalid username or password";
        echo '<script type="text/javascript">';
        echo 'alert("' . $login_error . '");';
        echo '</script>';
    }
}

// Close the connection and statement
mysqli_close($connection);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <style>
        body {
            background-image: url("");
            background-size: cover;
            font-family: Arial, sans-serif;
        }
        .login-box {
            margin: 100px auto;
            width: 300px;
            padding: 20px;
            background-color: #b5e2ff;
            border-radius: 5px;
        }
        input[type="text"], input[type="password"] {
            width: 90%;
            padding: 10px;
            border: 1px solid #ccc;
            margin-bottom: 10px;
        }
        button {
            background-color: #4CAF50;
            color: white;
            padding: 10px 15px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }
        a {
            color: #4CAF50;
            text-decoration: none;
        }
    </style>
</head>
<body>
    <div class="login-box">
        <h2>Login</h2>
        <form action="login.php" method="post">
            <input type="text" name="username" placeholder="Username" required>
            <input type="password" name="password" placeholder="Password" required>
            <button type="submit">Login</button>
        </form>
        <p>Don't have an account? <a href="register.php">Register Here</a></p>
    </div>
</body>
</html>