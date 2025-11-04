<?php
$servername = "localhost";
$username = "root";
$pass = "";
$dbname = "event_management";

$displaySuccessMessage = false;

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $conn = new mysqli($servername, $username, $pass, $dbname);

    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    $name = $_POST["name"];
    $email = $_POST["email"];
    $contact = $_POST["contact"];
    $description = $_POST["description"];

    $sql = "INSERT INTO complaint (name, email, contact, description) VALUES ('$name', '$email', '$contact', '$description')";

    if ($conn->query($sql) === TRUE) {
        $displaySuccessMessage = true;
    } 

    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Public Query Form</title>
<style>
    body {
        font-family: 'Poppins', Arial, sans-serif;
        background: linear-gradient(135deg, #6a11cb, #2575fc);
        height: 100vh;
        margin: 0;
        display: flex;
        justify-content: center;
        align-items: center;
        color: #333;
    }

    .container {
        background-color: #ffffff;
        border-radius: 15px;
        padding: 35px 30px;
        width: 100%;
        max-width: 450px;
        box-shadow: 0 8px 30px rgba(0,0,0,0.2);
        animation: fadeIn 0.7s ease;
        position: relative;
    }

    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(30px); }
        to { opacity: 1; transform: translateY(0); }
    }

    h2 {
        text-align: center;
        color: #222;
        margin-bottom: 15px;
        letter-spacing: 1px;
    }

    h3 {
        text-align: center;
        color: #555;
        font-weight: 400;
        margin-bottom: 25px;
        opacity: 0.7;
        font-size: 14px;
    }

    label {
        display: block;
        font-weight: 600;
        margin-bottom: 6px;
        color: #444;
    }

    input[type="text"],
    input[type="email"],
    input[type="tel"],
    textarea {
        width: 100%;
        padding: 12px;
        margin-bottom: 20px;
        border: 1px solid #ddd;
        border-radius: 8px;
        font-size: 14px;
        transition: all 0.3s ease;
    }

    input:focus, textarea:focus {
        border-color: #2575fc;
        outline: none;
        box-shadow: 0 0 5px rgba(37,117,252,0.4);
    }

    .btn {
        width: 100%;
        background: linear-gradient(135deg, #2575fc, #6a11cb);
        color: white;
        font-weight: 600;
        padding: 12px;
        border: none;
        border-radius: 8px;
        cursor: pointer;
        transition: all 0.3s ease;
        letter-spacing: 0.5px;
    }

    .btn:hover {
        background: linear-gradient(135deg, #6a11cb, #2575fc);
        transform: scale(1.03);
        box-shadow: 0 4px 15px rgba(106,17,203,0.3);
    }

    .success-message {
        background-color: #4CAF50;
        color: white;
        padding: 12px;
        border-radius: 8px;
        text-align: center;
        margin-bottom: 20px;
        animation: fadeIn 0.5s ease;
    }

    @media(max-width: 500px){
        .container {
            padding: 30px 20px;
        }
    }
</style>
</head>
<body>
<div class="container">
    <h2>Public Query Form</h2>
    <h3>After a successful submission, you'll be contacted by our service center.</h3>

    <?php if($displaySuccessMessage): ?>
        <div class="success-message">Complaint submitted successfully.</div>
    <?php endif; ?>

    <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
        <label for="name">Name</label>
        <input type="text" id="name" name="name" required>

        <label for="email">Email</label>
        <input type="email" id="email" name="email" required>

        <label for="contact">Contact Number</label>
        <input type="tel" id="contact" name="contact" placeholder="01XXXXXXXXX" pattern="01[3-9]\d{8}" required>

        <label for="description">Description</label>
        <textarea id="description" name="description" rows="4" required></textarea>

        <button type="submit" class="btn">Submit</button>
    </form>
</div>
</body>
</html>