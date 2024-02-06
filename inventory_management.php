<!-- inventory_management.php -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="inventory.css">
    <title>Inventory Management</title>
</head>
<body>
    <div class="dashboard-container">
        <h1>Inventory Management</h1>

        <!-- Goods Received Form -->
        <h2>Add Goods to Inventory</h2>
        <form method="post" action="">
            <label for="goods_name">Goods Name:</label>
            <input type="text" id="goods_name" name="goods_name" required><br>

            <label for="goods_id">Goods ID:</label>
            <input type="text" id="goods_id" name="goods_id" required><br>

            <label for="price">Price:</label>
            <input type="text" id="price" name="price" required><br>

            <label for="quantity">Quantity:</label>
            <input type="text" id="quantity" name="quantity" required><br>

            <button type="submit" name="add_to_inventory">Add to Inventory</button>
        </form>

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

        // Handle form submission for adding to inventory
        if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["add_to_inventory"])) {
            // Retrieve form data
            $goodsName = $_POST["goods_name"];
            $goodsID = $_POST["goods_id"];
            $price = $_POST["price"];
            $quantity = $_POST["quantity"];

            // Insert data into the inventory table
            $insertQuery = "INSERT INTO inventory (goods_name, goods_id, price, quantity) VALUES ('$goodsName', '$goodsID', $price, $quantity)";
            if ($conn->query($insertQuery) === TRUE) {
                echo "<p>Goods added to inventory successfully!</p>";
            } else {
                echo "<p>Error adding goods to inventory: " . $conn->error . "</p>";
            }
        }
        ?>

        <!-- Display Inventory -->
        <h2>Current Inventory</h2>
        <table>
            <tr>
                <th>Goods Name</th>
                <th>Goods ID</th>
                <th>Price</th>
                <th>Quantity</th>
            </tr>
            <?php
            // Fetch and display inventory
            $inventoryQuery = "SELECT * FROM inventory";
            $inventoryResult = $conn->query($inventoryQuery);

            if ($inventoryResult->num_rows > 0) {
                while ($row = $inventoryResult->fetch_assoc()) {
                    echo "<tr>";
                    echo "<td>" . $row["goods_name"] . "</td>";
                    echo "<td>" . $row["goods_id"] . "</td>";
                    echo "<td>$" . $row["price"] . "</td>";
                    echo "<td>" . $row["quantity"] . "</td>";
                    echo "</tr>";
                }
            } else {
                echo "<tr><td colspan='4'>No inventory available.</td></tr>";
            }
            ?>
        </table>

        <a href="dashboard.php" class="dashboard-button">Back to Dashboard</a>
    </div>
</body>
</html>
