<?php
// Include the connection file
require_once 'connection.php';

// Connect to the database
$connection = mysqli_connect($host, $username, $password, $database);

if (!$connection) {
    die('Database connection failed: ' . mysqli_connect_error());
}

// Retrieve data from the AJAX request
$totalAmount = isset($_POST['total_amount']) ? (float)$_POST['total_amount'] : 0;
$customerId = isset($_POST['customer_id']) ? (int)$_POST['customer_id'] : 0;
$cartId = isset($_POST['cart_id']) ? (int)$_POST['cart_id'] : 0;

// Check if the customer exists
$customerCheckQuery = "SELECT * FROM `customer` WHERE `customer_id` = $customerId";
$customerCheckResult = mysqli_query($connection, $customerCheckQuery);

if (!$customerCheckResult || mysqli_num_rows($customerCheckResult) == 0) {
    // Customer not found
    mysqli_close($connection);
    echo json_encode(['status' => 'error', 'message' => 'Customer not found']);
    exit;
}

// Get the customer's balance
$balanceQuery = "SELECT balance FROM payment WHERE customer_id = $customerId";
$balanceResult = mysqli_query($connection, $balanceQuery);

if ($balanceResult) {
    $balanceRow = mysqli_fetch_assoc($balanceResult);
    $balance = $balanceRow['balance'];

    // Check if the balance is sufficient
    if ($balance >= $totalAmount) {
        // Deduct the total amount from the balance
        $newBalance = $balance - $totalAmount;

        // Update the balance in the payment table
        $updateBalanceQuery = "UPDATE payment SET balance = $newBalance WHERE customer_id = $customerId";
        $updateBalanceResult = mysqli_query($connection, $updateBalanceQuery);

        if (!$updateBalanceResult) {
            mysqli_close($connection);
            echo json_encode(['status' => 'error', 'message' => 'Error updating balance']);
            exit;
        }

        

        // Update the cart status to closed
        $sqlUpdateCartStatus = "UPDATE `Cart` SET `status` = 'closed' WHERE `cart_id` = $cartId";
        mysqli_query($connection, $sqlUpdateCartStatus);

        // Deduct laptop stock 
        $sqlDeductStock = "UPDATE `Laptop` l
                           INNER JOIN `Cart_Item` ci ON l.`laptop_id` = ci.`laptop_id`
                           SET l.`stock` = l.`stock` - ci.`quantity`
                           WHERE ci.`cart_id` = $cartId";
        mysqli_query($connection, $sqlDeductStock);

        // Insert order details into `_order` table
        $sqlInsertOrder = "INSERT INTO `_order` (`customer_id`, `total_amount`) VALUES ($customerId, $totalAmount)";
        $orderResult = mysqli_query($connection, $sqlInsertOrder);

        if (!$orderResult) {
            // Handle the error 
            mysqli_close($connection);
            echo json_encode(['status' => 'error', 'message' => mysqli_error($connection)]);
            exit;
        }

        // Send a success response
        mysqli_close($connection);
        echo json_encode(['status' => 'success', 'message' => 'Payment successful!']);
    } else {
        // Insufficient balance
        mysqli_close($connection);
        echo json_encode(['status' => 'error', 'message' => 'You don\'t have enough money.']);
    }
} else {
    // Error fetching balance
    mysqli_close($connection);
    echo json_encode(['status' => 'error', 'message' => 'Error fetching balance']);
}
?>
