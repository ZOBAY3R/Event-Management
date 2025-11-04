<!DOCTYPE html>
<html>
<head>
    <title>Login Page</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f2f2f2;
            text-align: center;
            margin: 0;
            padding: 0;
        }

        h1 {
            background-color: #333;
            color: #fff;
            padding: 20px;
        }

        form {
            background-color: #fff;
            border: 1px solid #ccc;
            padding: 20px;
            width: 300px;
            margin: 0 auto;
        }

        form input {
            width: 90%;
            padding: 10px;
            margin-bottom: 10px;
        }

        input[type="submit"] {
            background-color: #333;
            color: #fff;
            border: none;
            padding: 10px 20px;
            cursor: pointer;
        }

        input[type="submit"]:hover {
            background-color: #555;
        }
        #message {
            color: red;
        }
    </style>
    <script>
        function validateForm() {
            var idOrEmail = document.forms["loginForm"]["id"].value;
            var password = document.forms["loginForm"]["password"].value;

            if (idOrEmail === "" || password === "") {
                alert("Please fill in both ID/Email and password fields");
                return false;
            }

            return true;
        }
    </script>
</head>
<body>
    <h1>Administrator Login</h1>
    <form method="post" action="" name="loginForm" onsubmit="return validateForm()">
        ID or Email: <input type="text" name="id" ><br>
        Password: <input type="password" name="password" ><br>
        <input type="submit" value="Login">
        <center>
        <p><a href="Index">Back To Home</a></p>
        </center>
    </form>

    <div id="message"></div>

    <?php
    if (isset($loginMessage)) {
        echo $loginMessage;
    }
    ?>
</body>
</html>
