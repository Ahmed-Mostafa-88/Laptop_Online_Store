<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FTAM Online Store - Laptops</title>
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
        .laptops-container {
            margin: 50px auto;
            width: 90%;
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
        }
        .laptop-box {
            background-color: #fff;
            padding: 20px;
            border-radius: 5px;
            box-shadow: 0 0 5px rgba(0,0,0,0.1);
            width: 250px;
        }
        .laptop-image {
            width: 100%;
            height: 150px;
            margin-bottom: 10px;
        }
        .laptop-info {
            font-size: 14px;
            line-height: 1.5;
        }
        .laptop-brand {
            font-weight: bold;
        }
        .laptop-price {
            color: #0095ff;
            font-weight: bold;
        }
        .laptop-stock {
            font-style: italic;
        }
        .select-button {
            background-color: #4CAF50;
            color: white;
            padding: 5px 10px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }
        .cart-logout-container {
            display: flex;
            justify-content: flex-end;
            padding: 10px;
        }
        .cart-button, .logout-button {
            background-color: #333;
            color: white;
            padding: 5px 10px;
            border: none;
            border-radius: 5px;
            margin-left: 10px;
            cursor: pointer;
        }
    </style>
</head>
<body>
    <header>
        <div class="cart-logout-container">
            <a href="cart.php" class="cart-button">Cart</a>
            <a href="logout.php" class="logout-button">Logout</a>
        </div>
        <h2>FTAM Online Store</h2>
    </header>
    <div class="laptops-container">
        <?php
            require_once 'connection.php';
            require_once 'add_to_cart.php';

            // Check if customer is logged in
            session_start();
            if (!isset($_SESSION['customer_id'])) {
                header('Location: login.php');
                exit;
            }

            // Connect to the database and fetch laptop data
            $connection = mysqli_connect($host, $username, $password, $database);
            if (!$connection) {
                die("Connection failed: " . mysqli_connect_error());
            }

            $sql = "SELECT * FROM Laptop";
            $result = mysqli_query($connection, $sql);

            while ($laptop = mysqli_fetch_assoc($result)) {
    echo '<div class="laptop-box">';
    echo '<img class="laptop-image" src="' . $laptop['image_url'] . '">';
    echo '<div class="laptop-info">';
    echo '<span class="laptop-brand">' . $laptop['brand'] . '</span> - <span class="laptop-model">' . $laptop['model'] . '</span>';
    echo '<p>' . $laptop['description'] . '</p>';
    echo '<span class="laptop-price">£' . $laptop['price'] . '</span> - <span class="laptop-stock">' . $laptop['stock'] . ' in stock</span>';

    // Check if stock is greater than 0 before displaying the "Select" button
    if ($laptop['stock'] > 0) {
        echo '<button class="select-button" data-laptop-id="' . $laptop['laptop_id'] . '">Select</button>';
    }

    echo '</div>';
    echo '</div>';
}
        ?>

        <script>
            document.addEventListener('DOMContentLoaded', function () {
                const selectButtons = document.querySelectorAll('.select-button');

                selectButtons.forEach(button => {
                    button.addEventListener('click', function () {
                        const laptopId = this.dataset.laptopId;

                        // Check if laptopId is available before making the AJAX request
                        if (laptopId) {
                            // Send the laptop ID to add_to_cart.php using an AJAX request
                            const xhr = new XMLHttpRequest();
                            xhr.onreadystatechange = function () {
                                if (xhr.readyState === 4 && xhr.status === 200) {
                                    // Handle the response (if needed)
                                    console.log(xhr.responseText);
                                }
                            };

                            xhr.open('POST', 'add_to_cart.php', true);
                            xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
                            xhr.send('laptop_id=' + laptopId); // Include laptop_id in the request data
                        } else {
                            console.error('Error: Missing laptop_id in dataset');
                        }
                    });
                });
            });
        </script>
    </div>

    <script>
    document.addEventListener('DOMContentLoaded', function () {
        const selectButtons = document.querySelectorAll('.select-button');

        selectButtons.forEach(button => {
            button.addEventListener('click', function () {
                const laptopId = this.dataset.laptopId;
                const quantityElement = this.closest('.laptop-box').querySelector('.laptop-quantity');
                const stockElement = this.closest('.laptop-box').querySelector('.laptop-stock');

                // Check if the stock is greater than 0 before allowing selection
                const currentStock = parseInt(stockElement.innerText, 10);
               
                if (currentStock > 0) {
                    // Decrease the stock on the page by 1
                    stockElement.innerText = currentStock - 1;

                    // Increase the quantity on the page by 1
                    const currentQuantity = parseInt(quantityElement.innerText, 10);
                    quantityElement.innerText = currentQuantity + 1;

                    // Send the laptop ID to add_to_cart.php using an AJAX request
                    const xhr = new XMLHttpRequest();
                    xhr.onreadystatechange = function () {
                        if (xhr.readyState === 4 && xhr.status === 200) {
                            // Handle the response (if needed)
                            console.log(xhr.responseText);
                        }
                    };

                    xhr.open('POST', 'add_to_cart.php', true);
                    xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
                    xhr.send('laptop_id=' + laptopId); // Include laptop_id in the request data
                } else {
                    // Inform the user that the laptop is out of stock
                    alert('This laptop is out of stock.');
                }
            });
        });
    });
</script>
</body>
</html>
