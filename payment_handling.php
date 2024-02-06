<!-- payment_handling.php -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="payment.css">
    <title>Payment Handling</title>
</head>
<body>
    <div class="payment-container">
        <h1>Payment Handling</h1>

        <!-- Display Account Balance -->
        <?php
        // Replace with your actual database credentials
        $servername = "localhost";
        $username = "root";
        $password = "";
        $dbname = "motorcycle_shop";

        // Create connection
        $conn = new mysqli($servername, $username, $password, $dbname);

        // Check connection
        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }

        // Fetch and display account balance
        $accountBalanceQuery = "SELECT * FROM account_balance";
        $balanceResult = $conn->query($accountBalanceQuery);

        if ($balanceResult->num_rows > 0) {
            $row = $balanceResult->fetch_assoc();
            echo "<p>Account Balance: $" . $row["balance"] . "</p>";
        } else {
            echo "<p>Account balance not available.</p>";
        }
        ?>

        <!-- Display Orders -->
        <h2>Order List</h2>
        <table>
            <tr>
                <th>Product</th>
                <th>Quantity</th>
                <th>Total Price</th>
                <th>Paid</th>
                <th>Pay for Goods</th>
            </tr>
            <?php
            $totalAmount = 0; // Initialize total amount

            // Fetch and display orders
            $ordersQuery = "SELECT * FROM orders";
            $ordersResult = $conn->query($ordersQuery);

            if ($ordersResult->num_rows > 0) {
                while ($row = $ordersResult->fetch_assoc()) {
                    echo "<tr id='row_" . $row["order_id"] . "'>";
                    echo "<td>" . $row["product_name"] . "</td>";
                    echo "<td>" . $row["quantity"] . "</td>";
                    echo "<td>$" . $row["total_price"] . "</td>";
                    echo "<td>" . ($row["paid"] ? "Yes" : "No") . "</td>";

                    // Check if the product has been paid for
                    if (!$row["paid"]) {
                        // Display Pay button only if the product hasn't been paid for
                        echo "<td><button id='payButton_" . $row["order_id"] . "' onclick='payForGoods(\"" . $row["product_name"] . "\", " . $row["total_price"] . ", " . $row["order_id"] . ")'>Pay</button></td>";
                    } else {
                        // Display Paid text if the product has been paid for
                        echo "<td>Paid</td>";
                    }

                    echo "</tr>";

                    // Accumulate total amount
                    $totalAmount += $row["total_price"];

                    // Initialize paid status for each product
                    echo "<script>paidStatus['" . $row["product_name"] . "'] = " . ($row["paid"] ? 'true' : 'false') . ";</script>";
                }
            } else {
                echo "<tr><td colspan='5'>No orders available.</td></tr>";
            }
            ?>
        </table>

        <!-- Display Total Amount -->
        <p>Total Amount: $<?php echo $totalAmount; ?></p>

        <!-- Payment Form -->
        <?php
// Check if any goods have been selected for payment
if ($totalAmount > 0) {
    // Allow payment if goods are selected
    echo '
        <h2>Make Payment</h2>
        <form method="post" action="">
            <label for="amount">Amount:</label>
            <input type="text" id="amount" name="amount" required>
            <button type="submit" name="make_payment">Make Payment</button>
        </form>';
} else {
    // Display a message if no goods are selected
    echo '<p>Kindly select goods to pay for.</p>';
}
?>

        <?php
        // Handle form submission for making payment
        if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["make_payment"])) {
            $amount = $_POST["amount"];
            $productName = $_GET["product"]; // Get the product name from the URL parameter

            // Check if the account balance is sufficient
            $balanceCheckQuery = "SELECT * FROM account_balance WHERE balance >= $amount";
            $balanceCheckResult = $conn->query($balanceCheckQuery);

            if ($balanceCheckResult->num_rows > 0) {
                // Deduct the amount from the account balance
                $deductBalanceQuery = "UPDATE account_balance SET balance = balance - $amount";
                if ($conn->query($deductBalanceQuery) === TRUE) {
                    echo "<p>Payment successful!</p>";

                    // Update the "paid" status in the orders table for the specific product
                    $updatePaidStatusQuery = "UPDATE orders SET paid = 1 WHERE product_name = '$productName'";
                    if ($conn->query($updatePaidStatusQuery) === TRUE) {
                        echo "<p>Paid status updated!</p>";
                    } else {
                        echo "<p>Error updating paid status: " . $conn->error . "</p>";
                    }

                    // Add JavaScript code to refresh the page only once
                    echo "<script>window.location.href = window.location.href;</script>";
                    exit(); // Stop further execution to prevent endless refresh
                } else {
                    echo "<p>Error updating account balance: " . $conn->error . "</p>";
                }
            } else {
                echo "<p>Insufficient funds. Payment failed.</p>";
            }
        }
        ?>

        <a href="dashboard.php" class="dashboard-button">Back to Dashboard</a>
    </div>

    <script>
        // Initialize an object to store paid status for each product
        var paidStatus = {};

        function payForGoods(productName, price, rowId) {
            var amountInput = document.getElementById("amount");
            var currentAmount = parseFloat(amountInput.value) || 0; // Get the current amount and convert it to a float
            var newAmount = currentAmount + price; // Add the price of the selected goods
            amountInput.value = newAmount.toFixed(2);  // Set the amount input field to the updated total amount

            // Update form action to include the product name
            var form = document.querySelector('form');
            form.action = 'payment_handling.php?product=' + encodeURIComponent(productName);

            // Disable the clicked button to prevent multiple clicks
            var button = document.getElementById('payButton_' + rowId);
            button.setAttribute('disabled', 'true');
            button.innerHTML = 'Paid'; // Optionally, change the button text to indicate it has been paid

            // Update paid status for the product
            paidStatus[productName] = true;
        }
    </script>
</body>
</html>
