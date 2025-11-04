<?php include 'HomeTopBar.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ticket Verification</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-image: url('../visuals/images/tkt.jpg'); /* Path to your background image in the "images" folder */
            background-size: cover;
            background-position: center;
            background-attachment: fixed;
            color: #fff;
            text-align: center;
        }

        #Login-button,
        #book-button {
            position: absolute;
            top: 20px;
            background-color: #ff6600;
            color: #fff;
            padding: 5px 10px;
            border-radius: 5px;
            text-decoration: none;
        }

        #Login-button:hover,
        #book-button:hover {
            background-color: #ff9933;
        }

        h2 {
            text-align: center;
        }

        form {
            text-align: center;
            margin-top: 20px;
        }

        label {
            display: block;
            margin-bottom: 10px;
        }

        input {
            padding: 5px;
        }

        button {
            background-color: #ff6600;
            color: #fff;
            padding: 5px 10px;
            border-radius: 5px;
            border: none;
            cursor: pointer;
        }

        button:hover {
            background-color: #ff9933;
        }
    </style>
</head>
<body>
  
    <h2>Ticket Verification</h2>
    <form action="HomeTicketVerify.php" method="post">
        <label for="ticket_id">Enter Ticket ID:</label>
        <input type="text" id="ticket_id" name="ticket_id" required>
        <button type="submit">Search</button>
    </form>

    <?php
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // Replace these database details with your own
        $host = 'localhost';
        $username = 'root';
        $password = '';
        $database = 'event_management';

        // Create a database connection
        $conn = new mysqli($host, $username, $password, $database);

        // Check the connection
        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }

        // Get the ticket ID from the form submission
        $ticketID = $_POST['ticket_id'];

        // Prepare and execute the query
        $query = "SELECT * FROM purchase_info WHERE ticket_id = '$ticketID'";
        $result = $conn->query($query);

        // Check if a matching ticket is found
        if ($result->num_rows > 0) {
            // Fetch the data
            $row = $result->fetch_assoc();
            $ticketQuantity = $row['ticket_quantity'];
            $eventName = $row['event_name'];

            // Display the ticket information
            echo "<h2>Ticket Information</h2>";
            echo "<h3>Ticket Found !!</h3>";
            echo "<p>Ticket Quantity: $ticketQuantity</p>";
            echo "<p>Event: $eventName</p>";
        } else {
            // Display an error message for invalid ticket ID
            echo "<h2>Invalid Ticket ID</h2>";
        }

        // Close the database connection
        $conn->close();
    }
    ?>
<?php include 'footer.php'; ?>
</body>
</html>

