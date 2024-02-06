<!-- invoice_management.php -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="invoice.css">
    <title>Invoice Management</title>
</head>
<body>
    <div class="invoice-management-container">
        <h1>Invoice Management</h1>
        <!-- Add your Invoice Management content here -->
        <p>This is the Invoice Management component.</p>

        <!-- Database Connection Code -->
        <?php
        // Replace these with your actual database credentials
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

        // Create the invoices table if it doesn't exist
        $create_invoices_table_query = "CREATE TABLE IF NOT EXISTS invoices (
            invoice_id INT AUTO_INCREMENT PRIMARY KEY,
            receipt_number VARCHAR(20) NOT NULL,
            image_path VARCHAR(255),
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )";

        if ($conn->query($create_invoices_table_query) === TRUE) {
            echo "Invoices table created or already exists.<br>";
        } else {
            echo "Error creating invoices table: " . $conn->error . "<br>";
        }

        // Handle form submission for adding new invoices
        if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["add_invoice"])) {
            $receiptNumber = $_POST["receipt_number"];
            $imagePath = $_POST["image_path"];

            // Insert the new invoice into the database
            $insert_invoice_query = "INSERT INTO invoices (receipt_number, image_path) VALUES ('$receiptNumber', '$imagePath')";

            if ($conn->query($insert_invoice_query) === TRUE) {
                echo "Invoice added successfully!<br>";
            } else {
                echo "Error adding invoice: " . $conn->error . "<br>";
            }
        }

        // Fetch invoices from the database
        $fetch_invoices_query = "SELECT * FROM invoices";
        $invoices_result = $conn->query($fetch_invoices_query);
        
        if ($invoices_result->num_rows > 0) {
            echo "<h2>Existing Invoices</h2>";
            while ($row = $invoices_result->fetch_assoc()) {
                echo "<div class='invoice'>";
                echo "<strong>Invoice ID:</strong> " . $row["invoice_id"] . "<br>";
                echo "<strong>Receipt Number:</strong> " . $row["receipt_number"] . "<br>";
                echo "<strong>Created At:</strong> " . $row["created_at"] . "<br>";
        
                if (!empty($row["image_path"])) {
                    // Construct the correct URL based on your project structure
                    $imageUrl = "/Motorcycle/" . $row["image_path"];
                    echo "<img src='" . $imageUrl . "' alt='Invoice Image' width='100'><br>";
                } else {
                    echo "No image available.<br>";
                }
                

                echo "<form method='post'>";
                echo "<label for='image_path'>Image Path:</label>";
                echo "<input type='text' name='image_path' value='" . $row["image_path"] . "'><br>";
                echo "<input type='hidden' name='invoice_id' value='" . $row["invoice_id"] . "'>";
                echo "<button type='submit' name='update_image_path'>Update Image Path</button>";
                echo "</form>";

                echo "<button onclick='generateInvoiceImage(" . $row["invoice_id"] . ")'>Generate Invoice Image</button>";
                echo "<button onclick='displayImage(\"" . $row["image_path"] . "\", \"" . $row["created_at"] . "\")'>View Invoice</button>";
                echo "</div>";
            }
        } else {
            echo "<p>No invoices available.</p>";
        }
        ?>

        <!-- Form for adding new invoices -->
        <h2>Add New Invoice</h2>
        <form method="post">
            <label for="receipt_number">Receipt Number:</label>
            <input type="text" id="receipt_number" name="receipt_number" required>
            <label for="image_path">Image Path:</label>
            <input type="text" id="image_path" name="image_path">
            <button type="submit" name="add_invoice">Add Invoice</button>
        </form>

        <a href="dashboard.php" class="dashboard-button">Back to Dashboard</a>
    </div>

    <script>
    function generateInvoiceImage(invoiceId) {
        // You can implement the logic to generate the invoice image here
        alert("Generating invoice image for Invoice ID: " + invoiceId);
        // Add your image generation logic or redirection to another page
    }

    function displayImage(imagePath, createdAt) {
        var modal = document.createElement("div");
        modal.style.position = "fixed";
        modal.style.top = "0";
        modal.style.left = "0";
        modal.style.width = "100%";
        modal.style.height = "100%";
        modal.style.backgroundColor = "rgba(0, 0, 0, 0.7)";
        modal.style.display = "flex";
        modal.style.alignItems = "center";
        modal.style.justifyContent = "center";

        var img = document.createElement("img");
        // Use the correct URL format for the image path
        img.src = imagePath;
        img.alt = "Invoice Image";
        img.style.maxWidth = "100%";
        img.style.maxHeight = "100%";
        modal.appendChild(img);

        document.body.appendChild(modal);

        // Close the modal when clicked outside the image
        modal.addEventListener("click", function () {
            document.body.removeChild(modal);
        });

        // Log errors to the console
        img.onerror = function () {
            console.error("Error loading image:", imagePath);
            // You can add additional error handling here
        };
    }
</script>


</body>
</html>
