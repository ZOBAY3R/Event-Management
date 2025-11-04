<?php include 'HomeTopBar.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>EventX - Your Event Partner</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-image: url('../visuals/images/gal.jpg'); /* Path to your background image in the "images" folder */
            margin: 0;
            padding: 0;
        }

        #Login-button {
            position: absolute;
            top: 20px;
            right: 10px;
            background-color: #ff6600;
            color: #fff;
            padding: 5px 10px;
            border-radius: 5px;
            text-decoration: none;
        }

        #Login-button:hover {
            background-color: #ff9933;
        }

        #book-button {
            position: absolute;
            top: 20px;
            right: 80px;
            background-color: #ff6600;
            color: #fff;
            padding: 5px 10px;
            border-radius: 5px;
            text-decoration: none;
        }

        #book-button:hover {
            background-color: #ff9933;
        }

        /* CSS styles for the gallery */
        .gallery img {
            max-width: 23%; /* Adjust the image size as needed */
            height: auto;
            margin: 10px;
            cursor: pointer; /* Add cursor pointer to indicate the images are clickable */
        }

        /* Styles for the modal (popup) */
        .modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.7);
            justify-content: center;
            align-items: center;
        }

        .modal-content {
            background-color: #fff;
            padding: 20px;
            border-radius: 5px;
            max-width: 80%;
            max-height: 80%;
            overflow: auto;
        }

        .close {
            color: #aaa;
            float: right;
            font-size: 28px;
            font-weight: bold;
            cursor: pointer;
        }

        .gallery img {
            max-width: 23%; /* Adjust the image size as needed */
            height: auto;
            margin: 10px;
        }
    </style>
</head>
<body>

    <div class="gallery">
        <?php
        $dbHost = 'localhost';
        $dbUser = 'root';
        $dbPass = '';
        $dbName = 'event_management';
        $basePath = "../visuals/gallery/"; // Adjust the base path as needed

        try {
            $pdo = new PDO("mysql:host=$dbHost;dbname=$dbName", $dbUser, $dbPass);
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            $stmt = $pdo->prepare("SELECT id, image_path FROM gallery_data");
            $stmt->execute();
            $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

            foreach ($result as $row) {
                $imagePath = $basePath . $row['image_path'];

                // Check if the image file exists
                if (file_exists($imagePath)) {
                    echo '<a href="details.php?id=' . $row['id'] . '">';
                    echo '<img src="' . $imagePath . '" alt="Gallery Image">';
                    echo '</a>';
                } else {
                    echo '<p>Error: Image not found for ID ' . $row['id'] . '</p>';
                }
            }
        } catch (PDOException $e) {
            echo "Error: " . $e->getMessage();
        }
        ?>
    </div>

    <?php include 'footer.php'; ?>
</body>
</html>