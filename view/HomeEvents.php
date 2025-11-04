<?php include 'HomeTopBar.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Events</title>
<style>
    /* ---- Base Styles ---- */
    body {
        font-family: 'Poppins', Arial, sans-serif;
        margin: 0;
        padding: 0;
        background: linear-gradient(135deg, #6a11cb, #2575fc);
        color: #222;
    }

    .event-list {
        max-width: 1200px;
        margin: 40px auto;
        padding: 0 20px;
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
        gap: 25px;
    }

    /* ---- Event Card ---- */
    .event {
        background-color: #fff;
        border-radius: 15px;
        padding: 20px;
        box-shadow: 0 6px 20px rgba(0, 0, 0, 0.15);
        transition: transform 0.3s ease, box-shadow 0.3s ease;
        cursor: pointer;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
    }

    .event:hover {
        transform: translateY(-5px);
        box-shadow: 0 12px 30px rgba(0, 0, 0, 0.25);
    }

    .event h2 {
        font-size: 20px;
        color: #333;
        margin-bottom: 10px;
    }

    .event p {
        font-size: 14px;
        color: #555;
        margin-bottom: 10px;
    }

    .event-details {
        display: none;
        font-size: 13px;
        color: #666;
        margin-top: 10px;
        line-height: 1.5;
    }

    /* Show details on hover */
    .event:hover .event-details {
        display: block;
    }

    /* Top Bar buttons overrides */
    #Login-button, #book-button {
        position: absolute;
        top: 20px;
        color: #fff;
        padding: 8px 15px;
        border-radius: 6px;
        text-decoration: none;
        font-weight: 600;
        transition: 0.3s ease;
    }

    #Login-button { right: 10px; background: linear-gradient(135deg, #2575fc, #6a11cb); }
    #book-button { right: 90px; background: linear-gradient(135deg, #2575fc, #6a11cb); }

    #Login-button:hover, #book-button:hover {
        background: linear-gradient(135deg, #6a11cb, #2575fc);
        transform: scale(1.05);
    }

    /* Responsive adjustments */
    @media (max-width: 600px) {
        .event-list {
            grid-template-columns: 1fr;
            gap: 20px;
        }
    }
</style>
</head>
<body>
    <div class="event-list">
        <?php
        $servername = "localhost";
        $username = "root";
        $password = "";
        $dbname = "event_management";

        $conn = new mysqli($servername, $username, $password, $dbname);
        if ($conn->connect_error) { die("Connection failed: " . $conn->connect_error); }

        $sql = "SELECT event_name, event_date, event_details FROM events ORDER BY event_date DESC";
        $result = $conn->query($sql);

        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                echo '<div class="event">';
                echo '<h2>' . htmlspecialchars($row['event_name']) . '</h2>';
                echo '<p><strong>Date:</strong> ' . htmlspecialchars($row['event_date']) . '</p>';
                echo '<div class="event-details">' . nl2br(htmlspecialchars($row['event_details'])) . '</div>';
                echo '</div>';
            }
        } else {
            echo '<p style="color:#fff; text-align:center; font-size:18px;">No events found.</p>';
        }

        $conn->close();
        ?>
    </div>
<?php include 'footer.php'; ?>
</body>
</html>