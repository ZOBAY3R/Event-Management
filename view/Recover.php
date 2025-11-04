<?php

$servername = "localhost";
$username = "root";
$pass = "";
$dbname = "event_management";
$con = new mysqli($servername, $username, $pass, $dbname);

if ($con->connect_error) {
    die("Connection failed: " . $con->connect_error);
}

// Update the path to the correct location
require '../vendor/phpmailer/phpmailer/src/PHPMailer.php';
require '../vendor/phpmailer/phpmailer/src/SMTP.php';
require '../vendor/phpmailer/phpmailer/src/Exception.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

function sendMail($_email, $reset_token)
{
    $mail = new PHPMailer(true);

    try {
        //Server settings
        $mail->isSMTP();                                     //Send using SMTP
        $mail->Host       = 'smtp.gmail.com';                //Set the SMTP server to send through
        $mail->SMTPAuth   = true;                            //Enable SMTP authentication
        $mail->Username   = 'eventa2zmanagement@gmail.com';  //SMTP username
        $mail->Password   = 'kawn bptd orqf nmci';           //SMTP password
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;     //Enable implicit TLS encryption
        $mail->Port       = 465;                             //TCP port to connect to; use 587 if you have set `SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS`

        //Recipients
        $mail->setFrom('eventa2zmanagement@gmail.com', 'A2Z EVENTS');
        $mail->addAddress($_email);                          //Add a recipient

        //Content
        $mail->isHTML(true);                                 //Set email format to HTML
        $mail->Subject = 'Password Reset Link from A2Z Events';
        $mail->Body    = "We got a request from you to reset your password! <br>
          Click the link below : <br>
          <a href='http://localhost/Project/Update_password.php?email=$_email&reset_token=$reset_token'> Reset Password</a>
        ";

        $mail->send();
        return true;
    } catch (Exception $e) {
        return false;
    }
}

if (isset($_POST['sendlink_btn'])) {
    $query = "SELECT * FROM `credential` WHERE `email`='$_POST[email]'";
    $result = mysqli_query($con, $query);
    if ($result) {
        if (mysqli_num_rows($result) == 1) {
            $reset_token = bin2hex(random_bytes(16));
            date_default_timezone_set('Asia/Dhaka');
            $date = date("Y-m-d");
            
            // Set resettokenexpire to be 1 day plus the current date
            $expire_date = date('Y-m-d', strtotime($date . ' + 1 days'));
            
            $query1 = "UPDATE `credential` SET `resettoken`='$reset_token',`resettokenexpire`='$expire_date' WHERE `email`='$_POST[email]'";
            if (mysqli_query($con, $query1) && sendMail($_POST["email"], $reset_token)) {
                echo "
                    <script>
                        alert('Password Reset Link Sent to mail');
                        window.location.href='Recover.php';
                    </script>
                ";
            } else {
                echo "
                    <script>
                        alert('Server Down! try again later');
                        window.location.href='Recover.php';
                    </script>
                ";
            }
        } else {
            echo "
                <script>
                    alert(' Email not Found ');
                    window.location.href='Recover.php';
                </script>
            ";
        }
    } else {
        echo "
            <script>
                alert('Cannot Run Query');
                window.location.href='Recover.php';
            </script>
        ";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Forgot Password</title>
<style>
    /* ---- Base Styles ---- */
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

    label {
        display: block;
        font-weight: 600;
        margin-bottom: 8px;
        text-align: left;
        color: #444;
    }

    input[type="email"] {
        width: 100%;
        padding: 12px;
        margin-bottom: 20px;
        border: 1px solid #ddd;
        border-radius: 8px;
        font-size: 15px;
        transition: 0.3s ease;
    }

    input[type="email"]:focus {
        border-color: #2575fc;
        outline: none;
        box-shadow: 0 0 5px rgba(37, 117, 252, 0.4);
    }

    .btn {
        display: block;
        width: 100%;
        padding: 12px;
        background: linear-gradient(135deg, #2575fc, #6a11cb);
        color: white;
        font-weight: 600;
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

    .info-text {
        margin-top: 15px;
        font-size: 14px;
        color: #555;
    }

    .info-text a {
        color: #2575fc;
        font-weight: 600;
        text-decoration: none;
    }

    .info-text a:hover {
        text-decoration: underline;
    }
</style>
</head>
<body>
<div class="container">
    <h2>Forgot Password</h2>
    <form method="POST">
        <label for="email">Email</label>
        <input type="email" id="email" name="email" placeholder="Enter your email" required>
        <button type="submit" class="btn" name="sendlink_btn">Reset Password</button>
    </form>
    <div class="info-text">
        <p>If the provided email matches an account, a recovery email will be sent.</p>
        <p><a href="UserLogin.php">Back to Login</a></p>
    </div>
</div>
</body>
</html>