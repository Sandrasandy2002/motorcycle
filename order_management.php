<?php
// Establish a connection to the MySQL database
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "motorcycle_shop";

$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
$fetch_user_ids_query = "SELECT DISTINCT userID FROM users";
$user_ids_result = $conn->query($fetch_user_ids_query);
// Fetch orders from the database
$fetch_orders_query = "SELECT * FROM orders";
$orders_result = $conn->query($fetch_orders_query);

// Process order creation form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $user_id = $_POST["user_id"];
    $product_name = $_POST["product_name"];
    $quantity = $_POST["quantity"];
    $total_price = $_POST["total_price"];

    // Insert the new order into the database
    $insert_order_query = "INSERT INTO orders (user_id, product_name, quantity, total_price) 
                           VALUES ('$user_id', '$product_name', $quantity, $total_price)";

if ($conn->query($insert_order_query) === TRUE) {
    // Redirect to the same page after successful form submission
    header("Location: ".$_SERVER['PHP_SELF']);
    exit();
} else {
    echo "Error creating order: " . $conn->error;
}
}

// Close the database connection
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="orders.css">
    <title>Order Management</title>
</head>
<body>
    <div class="order-management-container">
        <h1>Order Management</h1>

        <!-- Order Creation Form -->
        <div class="order-creation-form">
            <h2>Create New Order</h2>
            <form action="" method="post">
                <label for="user_id">User ID:</label>
                <select id="user_id" name="user_id" required>
                    <?php
                    while ($user_row = $user_ids_result->fetch_assoc()) {
                        echo "<option value='" . $user_row["userID"] . "'>" . $user_row["userID"] . "</option>";
                    }
                    ?>
                </select>

                <!--<label for="password">Password:</label>
                <input type="password" id="password" name="password" required>-->

                <label for="product_name">Product Name:</label>
                <input type="text" id="product_name" name="product_name" required>

                <label for="quantity">Quantity:</label>
                <input type="number" id="quantity" name="quantity" required>

                <label for="total_price">Total Price ($):</label>
                <input type="number" id="total_price" name="total_price" step="0.01" required>

                <button type="submit">Create Order</button>
            </form>
        </div>

        <!-- Display Existing Orders -->
        <?php
        if ($orders_result->num_rows > 0) {
            while ($row = $orders_result->fetch_assoc()) {
                echo "<div class='order'>";
                echo "<strong>Order ID:</strong> " . $row["order_id"] . "<br>";
                echo "<strong>User ID:</strong> " . $row["user_id"] . "<br>";
                echo "<strong>Product:</strong> " . $row["product_name"] . "<br>";
                echo "<strong>Quantity:</strong> " . $row["quantity"] . "<br>";
                echo "<strong>Total Price:</strong> $" . $row["total_price"] . "<br>";
                echo "</div>";
            }
        } else {
            echo "<p>No orders available.</p>";
        }
        ?>
        <button onclick="location.reload();" class="dashboard-button">Refresh</button>

        <a href="dashboard.php" class="dashboard-button">Back to Dashboard</a>
    </div>
</body>
</html>