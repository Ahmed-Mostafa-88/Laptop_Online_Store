<?php
require_once 'connection.php'; // Include connection file

// Initialize error message
$register_error = "";

// Check if form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = mysqli_real_escape_string($connection, $_POST["username"]);
    $password = mysqli_real_escape_string($connection, $_POST["password"]);
    $email = mysqli_real_escape_string($connection, $_POST["email"]);
    $phone_number = mysqli_real_escape_string($connection, $_POST["phone_number"]);
    $street = mysqli_real_escape_string($connection, $_POST["street"]);
    $city = mysqli_real_escape_string($connection, $_POST["city"]);
    $country = mysqli_real_escape_string($connection, $_POST["country"]);
    $building = mysqli_real_escape_string($connection, $_POST["building"]);

    // Check if username or email already exists
    $sql_check = "SELECT * FROM Customer WHERE username = ? OR email = ?";
    $stmt_check = mysqli_prepare($connection, $sql_check);
    mysqli_stmt_bind_param($stmt_check, "ss", $username, $email);
    mysqli_stmt_execute($stmt_check);
    $result_check = mysqli_stmt_get_result($stmt_check);

    if (mysqli_num_rows($result_check) > 0) {
        $register_error = "Username or email already exists";
        echo '<script type="text/javascript">';
        echo 'alert("' . $register_error . '");';
        echo '</script>';
    } else {
        // Insert new customer into database
        $sql_insert = "INSERT INTO Customer (username, password, email, phone_number, street, city, country, building) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt_insert = mysqli_prepare($connection, $sql_insert);
        mysqli_stmt_bind_param($stmt_insert, "ssssssss", $username, $password, $email, $phone_number, $street, $city, $country, $building);
        mysqli_stmt_execute($stmt_insert);

        if (mysqli_stmt_affected_rows($stmt_insert) > 0) {
            // Registration successful, redirect to login page
            $register_error = "Registration successful! Please login to continue.";
            header('Location: login.php');
        } else {
            $register_error = "Error registering. Please try again.";
        }
    }

    // Close statements and connection
    mysqli_stmt_close($stmt_check);
    mysqli_stmt_close($stmt_insert);
    mysqli_close($connection);
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>
    <style>
        body {
            background-image: url("path/to/laptop_background.jpg");
            background-size: cover;
            font-family: Arial, sans-serif;
        }
        .register-box {
            margin: 100px auto;
            width: 350px;
            padding: 20px;
            background-color: #b5e2ff;
            border-radius: 5px;
        }
        input[type="text"], input[type="password"], input[type="email"] {
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
    <div class="register-box">
        <h2>Register</h2>
        <form action="register.php" method="post">
            <input type="text" name="username" placeholder="Username" required>
            <input type="password" name="password" placeholder="Password" required>
            <input type="email" name="email" placeholder="Email" required>
            <input type="text" name="phone_number" placeholder="Phone Number">
            <input type="text" name="street" placeholder="Street Address" required>
            <input type="text" name="city" placeholder="City" required>
            <input type="text" name="country" placeholder="Country" required>
            <input type="text" name="building" placeholder="Building Number "required>
            <button type="submit">Register</button>
        </form>
        <p>Already have an account? <a href="login.php">Login Here</a></p>
    </div>
</body>
</html>