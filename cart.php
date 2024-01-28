<?php
// Include the connection file
require_once 'connection.php';

// Connect to the database
$connection = mysqli_connect($host, $username, $password, $database);

if (!$connection) {
    die('Database connection failed: ' . mysqli_connect_error());
}

// Check if the user is logged in 
session_start();
if (!isset($_SESSION['customer_id'])) {
    echo 'Error: Customer not logged in';
    exit;
}

// Fetch the cart ID for the logged-in customer
$customerId = $_SESSION['customer_id'];
$sqlCartId = "SELECT cart_id FROM Cart WHERE customer_id = $customerId AND status = 'open'";
$resultCartId = mysqli_query($connection, $sqlCartId);

if (!$resultCartId) {
    die('Error fetching cart ID: ' . mysqli_error($connection));
}

// Check if a cart exists for the customer
if (mysqli_num_rows($resultCartId) > 0) {
    $cart = mysqli_fetch_assoc($resultCartId);
    $cartId = $cart['cart_id'];

    
    // Fetch cart items for the logged-in customer
$sqlCartItems = "SELECT ci.cart_item_id, ci.quantity, ci.total_price, l.* FROM Cart_Item ci
        JOIN Laptop l ON ci.laptop_id = l.laptop_id
        WHERE ci.cart_id = $cartId";
$result = mysqli_query($connection, $sqlCartItems);

if (!$result) {
    die('Error fetching cart items: ' . mysqli_error($connection));
}    
}

// Close the database connection
mysqli_close($connection);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FTAM Online Store - Cart</title>
    <style>
        body {
            background-color: #f5f5f5;
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
        }
        header {
            background-color: #333;
            color: white;
            padding: 10px;
            text-align: left;
        }
        .laptop-button {
            background-color: #4CAF50;
            color: white;
            padding: 5px 10px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            text-decoration: none;
        }
        .laptop-button:hover {
            background-color: #45a049;
        }
        .cart-container {
            margin: 50px auto;
            width: 90%;
        }
        .cart-table {
            border-collapse: collapse;
            width: 100%;
        }
        .cart-table th, td {
            padding: 10px;
            border: 1px solid #ddd;
            text-align: left;
        }
        .laptop-image {
            width: 50px;
            height: 50px;
        }
        .action-button {
            background-color: #ff4444;
            color: white;
            padding: 5px 10px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }
        .checkout-button {
            background-color: #4CAF50;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            margin: 20px auto;
            display: block;
            width: 200px;
            cursor: pointer;
        }
    </style>
</head>
<body>
    <header>
        <h2>FTAM Online Store</h2>
        <button class="laptop-button" onclick="window.location.href='laptops.php'">Laptops</button>
    </header>
    <div class="cart-container">
        
        <?php

$total_amount = 0; // Initialize $total_amount

echo '<h2>Your Cart</h2>';
if (isset($result) && $result !== false) {
    if (mysqli_num_rows($result) > 0) {
        echo '<table>';
        echo '<tr>
                <th>Laptop</th>
                <th>Quantity</th>
                <th>Total Price</th>
                <th>Action</th>
              </tr>'
              ;

        while ($item = mysqli_fetch_assoc($result)) {
            $total_price = $item['quantity'] * $item['price'];
            $total_amount += $total_price;

            echo '<tr>';
            echo '<td><img class="laptop-image" src="' . $item['image_url'] . '"> ' . $item['brand'] . ' - ' . $item['model'] . '</td>';
            echo '<td>' . $item['quantity'] . '</td>';
            echo '<td>£' . number_format($total_price, 2) . '</td>';
            echo '<td>
                    <button class="action-button remove-button" data-laptop-id="' . $item['laptop_id'] . '">Remove</button>
                  </td>';
            echo '</tr>';
        }

        echo '</table>';

        // Display total amount if items exist in the cart
        if (!empty($total_amount)) {
            echo '<p style="text-align: center; font-weight: bold;">Total: £' . number_format($total_amount, 2) . '</p>';
            
            // Note: Payment using credit card only
            echo '<p style="text-align: center; color: #ff4444; font-size: 14px;">Note: Payment using credit card only</p>';
        }
        

        // Checkout button with data attributes for further processing
        echo '<button class="checkout-button"
                    data-total-amount="' . $total_amount . '"
                    data-customer-id="' . $customerId . '"
                    data-cart-id="' . $cartId . '">Checkout</button>';
    } else {
        echo '<p>Your cart is empty.</p>';
    }
} else {
    echo '<p>Your cart is empty.</p>';
}
?>

    </div>

    <script>
    document.addEventListener('DOMContentLoaded', function () {
        const removeButtons = document.querySelectorAll('.remove-button');
        removeButtons.forEach(button => {
            button.addEventListener('click', function () {
                const laptopId = this.dataset.laptopId;

                // Send an AJAX request to remove_from_cart.php
                const xhr = new XMLHttpRequest();
                xhr.onreadystatechange = function () {
                    if (xhr.readyState === 4 && xhr.status === 200) {
                        // Handle the response
                        console.log(xhr.responseText);
                        // Refresh the page or update the cart display
                        location.reload(); 
                    }
                };

                xhr.open('POST', 'remove_from_cart.php', true);
                xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
                xhr.send('laptop_id=' + laptopId);
            });
        });

        const checkoutButton = document.querySelector('.checkout-button');
        checkoutButton.addEventListener('click', function () {
            const totalAmount = this.dataset.totalAmount;
            const customerId = this.dataset.customerId;
            const cartId = this.dataset.cartId;

            // Send an AJAX request for checkout logic to be implemented in checkout.php
            const xhr = new XMLHttpRequest();
            xhr.onreadystatechange = function () {
                if (xhr.readyState === 4 && xhr.status === 200) {
                    // Handle the response
                    console.log(xhr.responseText);
                    // Redirect to confirmation or order processing page
                    location.reload(); 
                }
            };

            xhr.open('POST', 'checkout.php', true);
            xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
            xhr.send('total_amount=' + totalAmount + '&customer_id=' + customerId + '&cart_id=' + cartId);
        });
    });
</script>

</body>
</html>
