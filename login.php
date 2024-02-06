<?php
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $userID = $_POST["userID"];
    $password = $_POST["password"];

    // Database connection (replace these with your database credentials)
    $servername = "localhost";
    $dbusername = "root";
    $dbpassword = "";
    $dbname = "motorcycle_shop";

    $conn = new mysqli($servername, $dbusername, $dbpassword, $dbname);

    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Check user credentials
    $query = "SELECT * FROM users WHERE userID = '$userID'";
    $result = $conn->query($query);

    if ($result->num_rows == 1) {
        $row = $result->fetch_assoc();
        if (password_verify($password, $row["password"])) {
            // Valid login
            $_SESSION["userID"] = $userID;
            header("Location: dashboard.php");
            exit();
        }
    }

    // Invalid login
    $error = "Invalid userID or password";

    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Result</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <div class="container">
        <?php if (isset($error)): ?>
            <p class="error"><?php echo $error; ?></p>
        <?php endif; ?>
        <p>Click <a href="login.html">here</a> to try again.</p>
    </div>
</body>
</html>
