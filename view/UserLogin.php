<?php
session_start();

// Database credentials
$servername = "localhost";
$username = "root";
$password = "";
$database = "event_management";

// Create a connection
$conn = new mysqli($servername, $username, $password, $database);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$errors = array();

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $identifier = trim($_POST["identifier"]); // user id, email, or phone
    $password = $_POST["password"];
    $identifierField = '';

    // Detect type
    if (filter_var($identifier, FILTER_VALIDATE_EMAIL)) {
        $identifierField = "email";
    } elseif (preg_match('/^01[3-9]\d{8}$/', $identifier)) {
        $identifierField = "cnumber";
    } else {
        $identifierField = "id";
    }

    if (empty($errors)) {
        $identifier = $conn->real_escape_string($identifier);
        $query = "SELECT id, password, status FROM credential WHERE $identifierField = '$identifier'";
        $result = $conn->query($query);

        if (!$result) {
            $errors[] = "Database query error: " . $conn->error;
        } else {
            if ($result->num_rows == 1) {
                $row = $result->fetch_assoc();

                if ($password === $row["password"]) {
                    if ($row["status"] == 1) {
                        $_SESSION["id"] = $row["id"];
                        header("Location: UserProfile.php");
                        exit;
                    } else {
                        $errors[] = "Account is not active. Please contact support.";
                    }
                } else {
                    $errors[] = "Invalid credentials.";
                }
            } else {
                $errors[] = "Invalid credentials.";
            }
        }
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>User Login</title>
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
        padding: 40px 30px;
        width: 100%;
        max-width: 420px;
        box-shadow: 0 8px 30px rgba(0, 0, 0, 0.2);
        animation: fadeIn 0.7s ease;
        text-align: center;
    }

    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(30px); }
        to { opacity: 1; transform: translateY(0); }
    }

    h2 {
        color: #222;
        margin-bottom: 30px;
        letter-spacing: 1px;
    }

    input[type="text"],
    input[type="password"] {
        width: 100%;
        padding: 14px;
        margin-bottom: 20px;
        border: 1px solid #ddd;
        border-radius: 8px;
        font-size: 15px;
        transition: 0.3s ease;
    }

    input:focus {
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
        box-shadow: 0 4px 15px rgba(106, 17, 203, 0.3);
    }

    .error-messages {
        background-color: #ffe0e0;
        color: #e74c3c;
        padding: 10px;
        border-radius: 8px;
        margin-bottom: 20px;
        font-weight: 500;
    }

    .text-center a {
        color: #2575fc;
        text-decoration: none;
        font-weight: 600;
    }

    .text-center a:hover {
        text-decoration: underline;
    }
</style>
</head>
<body>
<div class="container">
    <h2>User Login</h2>

    <?php if (!empty($errors)) : ?>
        <div class="error-messages">
            <?php foreach ($errors as $error) : ?>
                <p><?php echo $error; ?></p>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <form method="POST" action="<?php echo $_SERVER['PHP_SELF']; ?>">
        <input type="text" name="identifier" placeholder="User ID / Email / Phone" required>
        <input type="password" name="password" placeholder="Enter your password" required>
        <button type="submit" class="btn">Login</button>
    </form>

    <div class="text-center">
        <p><a href="Recover.php">Forgot password?</a></p>
        <p>Don't have an account? <a href="Signup.php">Signup</a></p>
        <p><a href="Home.php">Back To Home</a></p>
    </div>
</div>
</body>
</html>