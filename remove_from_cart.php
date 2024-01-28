<?php
require_once 'connection.php';

// Check if laptop_id is provided in POST data
if (isset($_POST['laptop_id'])) {
    $laptopId = $_POST['laptop_id'];

    session_start();

    // Check if the user is logged in
    if (!isset($_SESSION['customer_id'])) {
        echo 'User not logged in';
        exit;
    }

    $customerId = $_SESSION['customer_id'];

    // Get the cart_id for the open cart of the logged-in user
    $sqlCartId = "SELECT cart_id FROM Cart WHERE customer_id = $customerId AND status = 'open'";
    $resultCartId = mysqli_query($connection, $sqlCartId);

    if ($resultCartId) {
        $cartIdRow = mysqli_fetch_assoc($resultCartId);
        $cartId = $cartIdRow['cart_id'];

        // Remove the item from Cart_Item
        $sqlRemoveItem = "DELETE FROM Cart_Item WHERE cart_id = $cartId AND laptop_id = $laptopId";
        $resultRemoveItem = mysqli_query($connection, $sqlRemoveItem);

        if ($resultRemoveItem) {
            echo 'Item removed from cart successfully';
        } else {
            echo 'Error removing item from cart';
        }
    } else {
        echo 'Error fetching cart ID';
    }
} else {
    echo 'Laptop ID not provided';
}
?>
