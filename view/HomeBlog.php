<?php
include 'HomeTopBar.php';
session_start();

// If the user is not logged in, show a message and stop the page
if (!isset($_SESSION['id'])) {
    echo '
    <div style="
        display: flex;
        justify-content: center;
        align-items: center;
        height: 100vh;
        font-family: Arial, sans-serif;
        background-color: #ffffff;
    ">
        <div style="
            background: linear-gradient(135deg, #ff7e5f, #feb47b);
            color: #fff; 
            padding: 30px; 
            text-align: center; 
            border-radius: 15px;
            box-shadow: 0 6px 20px rgba(0,0,0,0.2);
            max-width: 500px;
        ">
            <h2>You are not logged in!</h2>
            <p>Please <a href="UserLogin.php" style="color:#fff; font-weight:bold; text-decoration:underline;">Login here</a> to access the blog.</p>
        </div>
    </div>
    ';
    exit(); // Stop the rest of the page from loading
}

$id = $_SESSION['id'];

$conn = mysqli_connect("localhost", "root", "", "event_management");
if (!$conn) { die("Connection failed: " . mysqli_connect_error()); }

$query = "SELECT * FROM credential WHERE id = '$id'";
$result = mysqli_query($conn, $query);
if ($row = mysqli_fetch_assoc($result)) {
    $username = $row['username'];
    $email = $row['email'];
}

// Handle new post submission
if (isset($_POST['title']) && isset($_POST['content'])) {
    $title = $conn->real_escape_string($_POST['title']);
    $content = $conn->real_escape_string($_POST['content']);
    $sql = "INSERT INTO posts (posted_by_id, posted_by_username, title, content, status) 
            VALUES ('$id', '$username', '$title', '$content', '1')";
    $conn->query($sql);
}

// Handle new comment submission
if (isset($_POST['post']) && isset($_POST['comment'])) {
    $postId = intval($_POST['post']);
    $comment = $conn->real_escape_string($_POST['comment']);
    $sql = "INSERT INTO comments (posted_by_id, posted_by_username, post_id, comment, status) 
            VALUES ('$id', '$username', '$postId', '$comment', '1')";
    $conn->query($sql);
}

// Handle logout
if (isset($_GET['action']) && $_GET['action'] === 'logout') {
    session_unset();
    session_destroy();
    header("Location: UserLogin.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Simple Blog</title>
<style>
    body {
        font-family: 'Poppins', Arial, sans-serif;
        background-color: #ffffff; /* Page background white */
        margin: 0;
        padding: 0;
        color: #222;
    }

    .container {
        max-width: 900px;
        margin: 40px auto;
        background: linear-gradient(135deg, #ff7e5f, #feb47b); /* Orange gradient box */
        border-radius: 15px;
        padding: 30px;
        box-shadow: 0 6px 25px rgba(0,0,0,0.15);
        color: #fff; /* White text inside the orange box */
    }

    h2, h3 {
        text-align: center;
        color: #fff;
    }

    form {
        margin-bottom: 30px;
    }

    input[type="text"], textarea {
        width: 100%;
        padding: 12px;
        margin-bottom: 15px;
        border: 1px solid #ddd;
        border-radius: 8px;
        font-size: 15px;
        transition: 0.3s ease;
    }

    input[type="text"]:focus, textarea:focus {
        border-color: #ff9933;
        outline: none;
        box-shadow: 0 0 5px rgba(255,153,51,0.4);
    }

    input[type="submit"] {
        background: #fff;
        color: #ff7e5f;
        font-weight: 600;
        padding: 12px 20px;
        border: none;
        border-radius: 8px;
        cursor: pointer;
        transition: all 0.3s ease;
    }

    input[type="submit"]:hover {
        background: #ffe0d6;
        transform: scale(1.03);
        box-shadow: 0 4px 15px rgba(255,126,95,0.3);
    }

    .post {
        border: 1px solid #ffd1c1;
        border-radius: 12px;
        padding: 20px;
        margin-bottom: 20px;
        background: #fff;
        color: #222;
    }

    .post h4 {
        margin-top: 0;
        color: #ff7e5f;
    }

    .comment {
        margin-left: 20px;
        padding: 10px;
        border-left: 3px solid #ff7e5f;
        background: #fff;
        border-radius: 8px;
        margin-bottom: 10px;
        font-size: 14px;
        color: #222;
    }

    .comment form {
        margin-top: 10px;
    }

    a {
        color: #fff;
        text-decoration: underline;
        font-weight: 600;
    }

    .logout-link {
        display: block;
        text-align: center;
        margin-bottom: 20px;
    }
</style>
</head>
<body>
<div class="container">
    <h2>Welcome, <?php echo htmlspecialchars($username); ?>!</h2>
    <div class="logout-link">
        <a href="?action=logout">Logout</a>
    </div>

    <h3>Create a new post:</h3>
    <form action="" method="post">
        <input type="text" name="title" placeholder="Post Title" required>
        <textarea name="content" rows="4" placeholder="Write your content here..." required></textarea>
        <input type="submit" value="Post">
    </form>

    <hr style="border-color: #fff;">

    <h3>Recent Posts:</h3>
    <?php
    $sql = "SELECT * FROM posts WHERE status = 1 ORDER BY id DESC";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $postId = $row['id'];
            $postTitle = htmlspecialchars($row['title']);
            $postContent = nl2br(htmlspecialchars($row['content']));

            echo '<div class="post">';
            echo "<h4>$postTitle</h4>";
            echo "<p>$postContent</p>";

            $commentSql = "SELECT * FROM comments WHERE post_id = $postId AND status = 1";
            $commentResult = $conn->query($commentSql);

            if ($commentResult->num_rows > 0) {
                echo '<h5>Comments:</h5>';
                while ($commentRow = $commentResult->fetch_assoc()) {
                    $commentText = nl2br(htmlspecialchars($commentRow['comment']));
                    echo "<div class='comment'>$commentText</div>";
                }
            } else {
                echo "<p>No comments yet.</p>";
            }

            // Comment form
            echo '<form action="" method="post">';
            echo "<input type='hidden' name='post' value='$postId'>";
            echo '<textarea name="comment" rows="2" placeholder="Add a comment..." required></textarea>';
            echo '<input type="submit" value="Comment">';
            echo '</form>';

            echo '</div>';
        }
    } else {
        echo "<p>No posts yet.</p>";
    }

    $conn->close();
    ?>
</div>
<?php include '../view/footer.php'; ?>
</body>
</html>