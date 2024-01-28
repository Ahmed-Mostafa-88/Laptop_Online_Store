<?php
require_once 'connection.php';

// Check if the laptop_id is set in POST data
if (isset($_POST['laptop_id'])) {
    // Initialize or resume the session
    session_start();

    // Check if the customer is logged in
    if (!isset($_SESSION['customer_id'])) {
        echo 'Error: User not logged in';
        exit;
    }

    $customerId = $_SESSION['customer_id'];
    $laptopId = $_POST['laptop_id'];

    // Connect to the database
    $connection = mysqli_connect($host, $username, $password, $database);
    if (!$connection) {
        echo 'Error: Database connection failed';
        exit;
    }

    // Check if the customer already has an open cart
    $cartResult = mysqli_query($connection, "SELECT * FROM Cart WHERE customer_id = $customerId AND status = 'open'");
    if (!$cartResult) {
        echo 'Error: ' . mysqli_error($connection);
        exit;
    }

    if (mysqli_num_rows($cartResult) == 0) {
        // If no open cart exists, create a new one
        $dateCreated = date('Y-m-d H:i:s');
        $createCartQuery = "INSERT INTO Cart (customer_id, date_created) VALUES ($customerId, '$dateCreated')";
        $createCartResult = mysqli_query($connection, $createCartQuery);

        if (!$createCartResult) {
            echo 'Error: ' . mysqli_error($connection);
            exit;
        }

        // Retrieve the newly created cart's ID
        $cartId = mysqli_insert_id($connection);
    } else {
        // If an open cart exists, use it
        $cartData = mysqli_fetch_assoc($cartResult);
        $cartId = $cartData['cart_id'];
    }

    // Check if the laptop is already in the cart
    $cartItemResult = mysqli_query($connection, "SELECT * FROM Cart_Item WHERE cart_id = $cartId AND laptop_id = $laptopId");
    if (!$cartItemResult) {
        echo 'Error: ' . mysqli_error($connection);
        exit;
    }

    if (mysqli_num_rows($cartItemResult) == 0) {
        // If the laptop is not in the cart, add a new cart item
        $insertCartItemQuery = "INSERT INTO Cart_Item (cart_id, laptop_id, quantity, total_price) VALUES ($cartId, $laptopId, 1, 0.00)";
        $insertCartItemResult = mysqli_query($connection, $insertCartItemQuery);

        if (!$insertCartItemResult) {
            echo 'Error: ' . mysqli_error($connection);
            exit;
        }
    } else {
        // If the laptop is already in the cart, update the quantity and total price
        $updateCartItemQuery = "UPDATE Cart_Item SET quantity = quantity + 1, total_price = quantity * (SELECT price FROM Laptop WHERE laptop_id = $laptopId) WHERE cart_id = $cartId AND laptop_id = $laptopId";
        $updateCartItemResult = mysqli_query($connection, $updateCartItemQuery);

        if (!$updateCartItemResult) {
            echo 'Error: ' . mysqli_error($connection);
            exit;
        }
    }

    // Close the database connection
    mysqli_close($connection);

    echo 'success';
} else {
    echo 'Error: Missing laptop_id in POST data';
}
?>
