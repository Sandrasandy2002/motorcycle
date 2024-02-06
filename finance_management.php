<!-- finance_management.php -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="finance.css">
    <title>Finance Management</title>
</head>
<body>
    <div class="finance-container">
        <h1>Finance Management</h1>

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

        <!-- Manual Finance Entry Form -->
        <h2>Add Finance Manually</h2>
        <form method="post" action="">
            <label for="user">Select User:</label>
            <select id="user" name="user" required>
                <!-- Fetch and display users from the 'users' table -->
                <?php
                $usersQuery = "SELECT * FROM users";
                $usersResult = $conn->query($usersQuery);

                if ($usersResult) {
                    if ($usersResult->num_rows > 0) {
                        while ($user = $usersResult->fetch_assoc()) {
                            echo "<option value='" . $user["id"] . "'>" . $user["userID"] . "</option>";
                        }
                    } else {
                        echo "<option value='' disabled>No users available</option>";
                    }
                } else {
                    echo "<option value='' disabled>Error fetching users: " . $conn->error . "</option>";
                }
                ?>
            </select>
            <label for="password">Password:</label>
            <input type="password" id="password" name="password" required>
            <label for="amount_manual">Amount:</label>
            <input type="text" id="amount_manual" name="amount_manual" required>
            <button type="submit" name="add_finance">Add Finance</button>
        </form>

        <?php
        // Handle form submission for adding finance manually
        if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["add_finance"])) {
            $userId = $_POST["user"];
            $password = $_POST["password"];
            $amount = $_POST["amount_manual"];

            // Check user authorization (replace with your authentication logic)
            $authQuery = "SELECT * FROM users WHERE id = $userId";
            $authResult = $conn->query($authQuery);

            if (!$authResult) {
                echo "<p>Error in authorization query: " . $conn->error . "</p>";
            } else {
                echo "<p>Rows found in authorization query: " . $authResult->num_rows . "</p>";

                if ($authResult->num_rows > 0) {
                    $userData = $authResult->fetch_assoc();
                    // Verify the password
                    if (password_verify($password, $userData["password"])) {
                        // Authorized user, update finance
                        $updateFinanceQuery = "UPDATE account_balance SET balance = balance + $amount";
                        if ($conn->query($updateFinanceQuery) === TRUE) {
                            echo "<p>Finance added successfully!</p>";
                            echo '<script>window.location.href = window.location.href;</script>';
                        } else {
                            echo "<p>Error updating finance: " . $conn->error . "</p>";
                        }
                    } else {
                        echo "<p>Unauthorized user. Finance not added.</p>";
                    }
                } else {
                    echo "<p>User not found. Finance not added.</p>";
                }
            }
        }
        ?>

        <a href="dashboard.php" class="dashboard-button">Back to Dashboard</a>
    </div>
</body>
</html>
