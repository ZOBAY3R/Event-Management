<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

$successMessage = "";
$errorMessages = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $host = "localhost";
    $username = "root";
    $password = "";
    $database = "event_management";

    $mysqli = new mysqli($host, $username, $password, $database);

    if ($mysqli->connect_error) {
        die("Connection failed: " . $mysqli->connect_error);
    }

    $name = $mysqli->real_escape_string($_POST['name']);
    $email = $mysqli->real_escape_string($_POST['email']);;
    $password = $mysqli->real_escape_string($_POST['password']);
    $cpassword = $mysqli->real_escape_string($_POST['cpassword']);
    $cnumber = $mysqli->real_escape_string($_POST['cnumber']);

    // Server-side validation
    if (empty($name) || empty($email) || empty($password) || empty($cpassword) || empty($cnumber)) {
        $errorMessages[] = "All fields are required.";
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errorMessages[] = "Invalid email format.";
    }

    if (!preg_match("/^01[3-9]\d{8}$/", $cnumber)) {
        $errorMessages[] = "Enter a valid contact number.";
    }

    if ($password !== $cpassword) {
        $errorMessages[] = "Passwords do not match.";
    }

    // Check if email already exists in the database
    $checkEmailQuery = "SELECT * FROM credential WHERE email = '$email'";
    $result = $mysqli->query($checkEmailQuery);

    if ($result->num_rows > 0) {
        $errorMessages[] = "This email is already registered. Please use a different email.";
    }

    if (empty($errorMessages)) {
        // Insert user data into the database without password hashing
        $sql = "INSERT INTO credential (name, email, cnumber, password) VALUES ('$name', '$email', '$cnumber', '$password')";

        if ($mysqli->query($sql) === true) {
            // Send email using PHPMailer
            require 'vendor/autoload.php';
            require("PHPMailer/PHPMailer.php");
            require("PHPMailer/SMTP.php");
            require("PHPMailer/Exception.php");

            $mail = new PHPMailer();

            try {
                //Server settings
                $mail->isSMTP();                                            //Send using SMTP
                $mail->Host       = 'smtp.gmail.com';                     //Set the SMTP server to send through
                $mail->SMTPAuth   = true;                                   //Enable SMTP authentication
                $mail->Username   = 'eventa2zmanagement@gmail.com';                     //SMTP username
                $mail->Password   = 'kawn bptd orqf nmci';                               //SMTP password
                $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;            //Enable implicit TLS encryption
                $mail->Port       = 465;                                    //TCP port to connect to; use 587 if you have set `SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS`

                //Recipients
                $mail->setFrom('eventa2zmanagement@gmail.com', 'A2Z EVENTS');
                $mail->addAddress($email);     //Add a recipient

                //Content
                $mail->isHTML(true);                                  //Set email format to HTML
                $mail->Subject = 'Email verification from A2Z Events';
                $mail->Body    = "Thanks for registration! 
                click the link below to verify the mail address 
                <a href='http://localhost/project/verify.php?email=$email&verifi_code=$verifi_code'> Verify </a>";

                $mail->send();

                $successMessage = "Registration successful! Verification email sent.";
            } catch (Exception $e) {
                $errorMessages[] = "Error sending verification email: " . $mail->ErrorInfo;
            }
        } else {
            $errorMessages[] = "Error: " . $sql . "<br>" . $mysqli->error;
        }
    }

    // Close the database connection
    $mysqli->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <script>
        function validateForm() {
            var name = document.getElementById("name").value;
            var email = document.getElementById("email").value;
            var cnumber = document.getElementById("cnumber").value;
            var password = document.getElementById("password").value;
            var cpassword = document.getElementById("cpassword").value;

            var emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            var cnumberRegex = /^01[3-9]\d{8}$/;

            // Clear previous error messages
            document.getElementById("nameError").innerHTML = "";
            document.getElementById("emailError").innerHTML = "";
            document.getElementById("cnumberError").innerHTML = "";
            document.getElementById("passwordError").innerHTML = "";
            document.getElementById("cpasswordError").innerHTML = "";

            if (name.trim() === "") {
                document.getElementById("nameError").innerHTML = "Please enter your full name.";
                return false;
            }

            if (!emailRegex.test(email)) {
                document.getElementById("emailError").innerHTML = "Invalid email format.";
                return false;
            }

            if (!cnumberRegex.test(cnumber)) {
                document.getElementById("cnumberError").innerHTML = "Enter a valid contact number.";
                return false;
            }

            if (password.trim() === "") {
                document.getElementById("passwordError").innerHTML = "Please enter a password.";
                return false;
            }

            if (cpassword.trim() === "") {
                document.getElementById("cpasswordError").innerHTML = "Please confirm your password.";
                return false;
            }

            if (password !== cpassword) {
                document.getElementById("cpasswordError").innerHTML = "Passwords do not match.";
                return false;
            }

            return true;
        }
    </script>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Signup Page</title>
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
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(30px); }
            to { opacity: 1; transform: translateY(0); }
        }

        h2 {
            text-align: center;
            color: #222;
            margin-bottom: 25px;
            letter-spacing: 1px;
        }

        label {
            display: block;
            font-weight: 600;
            margin-bottom: 6px;
            color: #444;
        }

        input[type="text"],
        input[type="password"],
        input[type="email"] {
            width: 100%;
            padding: 12px;
            border: 1px solid #ddd;
            border-radius: 8px;
            font-size: 15px;
            transition: 0.3s ease;
            margin-bottom: 18px;
        }

        input:focus {
            border-color: #2575fc;
            outline: none;
            box-shadow: 0 0 5px rgba(37, 117, 252, 0.4);
        }

        input[type="checkbox"] {
            margin-right: 8px;
            transform: scale(1.2);
            vertical-align: middle;
        }

        .form-group {
            margin-bottom: 18px;
        }

        /* ✅ Fix checkbox & label alignment */
        .checkbox-group {
            display: flex;
            align-items: center;
            gap: 8px;
            margin-top: 10px;
        }

        .checkbox-group label {
            margin: 0;
            font-weight: 500;
            color: #444;
        }

        .btn {
            display: block;
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

        .warning {
            color: #e74c3c;
            font-size: 14px;
            margin: -10px 0 10px 0;
        }

        .success-message {
            background-color: #2ecc71;
            color: #fff;
            padding: 10px;
            text-align: center;
            border-radius: 8px;
            margin-bottom: 15px;
            font-weight: 500;
        }

        .warning-box {
            background-color: #ffe0e0;
            color: #e74c3c;
            padding: 10px;
            border-radius: 8px;
            text-align: center;
            margin-bottom: 15px;
        }

        .text-center {
            text-align: center;
            margin-top: 15px;
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
        <?php if (!empty($errorMessages)) : ?>
            <div class="warning-box">
                <?php echo implode("<br>", $errorMessages); ?>
            </div>
        <?php endif; ?>

        <?php if (!empty($successMessage)) : ?>
            <div class="success-message">
                <?php echo $successMessage; ?>
            </div>
        <?php endif; ?>

        <h2>Create Account</h2>
        <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="POST" onsubmit="return validateForm()">
            <div class="form-group">
                <label for="name">Full Name</label>
                <input type="text" id="name" name="name" placeholder="Enter your full name">
                <p id="nameError" class="warning"></p>
            </div>

            <div class="form-group">
                <label for="email">Email Address</label>
                <input type="email" id="email" name="email" placeholder="example@email.com">
                <p id="emailError" class="warning"></p>
            </div>

            <div class="form-group">
                <label for="cnumber">Contact Number</label>
                <input type="text" id="cnumber" name="cnumber" pattern="01[3-9]\d{8}" title="Enter a valid contact number." placeholder="01XXXXXXXXX">
                <p id="cnumberError" class="warning"></p>
            </div>

            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" placeholder="Create a password">
                <p id="passwordError" class="warning"></p>
            </div>

            <div class="form-group">
                <label for="cpassword">Confirm Password</label>
                <input type="password" id="cpassword" name="cpassword" placeholder="Re-enter your password">
                <p id="cpasswordError" class="warning"></p>
            </div>

            <!-- ✅ Fixed alignment -->
            <div class="checkbox-group">
                <input type="checkbox" id="agree" name="agree">
                <label for="agree">I agree to the Terms & Conditions</label>
            </div>

            <button type="submit" id="signupButton" class="btn">Sign Up</button>
        </form>

        <div class="text-center">
            <p>Already have an account? <a href="UserLogin.php">Login</a></p>
        </div>
    </div>
</body>
</html>