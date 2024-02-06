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

// Process signup form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $userID = $_POST["userID"];
    $password = password_hash($_POST["password"], PASSWORD_DEFAULT);

    // Check if the user already exists
    $check_query = "SELECT * FROM users WHERE userID = '$userID'";
    $result = $conn->query($check_query);

    if ($result->num_rows > 0) {
        echo "User already exists. Already have an account?";
        echo '<br><button onclick="location.href=\'login.html\'">Login</button>';
        echo '<button onclick="location.href=\'#\'">Exit</button>';
    } else {
        // Insert user data into the database
        $insert_query = "INSERT INTO users (userID, password) VALUES ('$userID', '$password')";

        if ($conn->query($insert_query) === TRUE) {
            echo "User registered successfully!";
            
            // Redirect to the purchase dashboard
            header("Location: dashboard.php");
            exit();
        } else {
            echo "Error: " . $insert_query . "<br>" . $conn->error;
        }
    }
}

// Close the database connection
$conn->close();
?>
