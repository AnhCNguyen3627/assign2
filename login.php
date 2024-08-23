<?php
session_start();
require_once("settings.php");

$db_conn = mysqli_connect($host, $user, $pswd, $dbnm) or die("Connection failed: " . mysqli_connect_error());

if (!$db_conn) {
    die("Failed to connect to database: " . $db_conn->connect_error . "<br>");
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['email']) && isset($_POST['password'])) {
        $userEmail = $_POST['email'];
        $userPassword = $_POST['password'];
        $errors = array();

        if (!filter_var($userEmail, FILTER_VALIDATE_EMAIL)) {
            $errors[] = "<span style='color: red;'>Invalid email address format.</span>";
        }

        if (!preg_match('/^[a-zA-Z0-9]+$/', $userPassword)) {
            $errors[] = "<span style='color: red;'>Password must contain only letters and numbers.</span>";
        }

        $emailQuery = "SELECT * FROM friends WHERE friend_email = '$userEmail'";
        $emailResult = mysqli_query($db_conn, $emailQuery);

        if (mysqli_num_rows($emailResult) === 0) {
            $errors[] = "<span style='color: red;'>Email address does not exist.</span>";
        }

        $passwordQuery = "SELECT * FROM friends WHERE friend_email = '$userEmail' AND password = '$userPassword'";
        $passwordResult = mysqli_query($db_conn, $passwordQuery);

        if (mysqli_num_rows($passwordResult) === 0) {
            $errors[] = "<span style='color: red;'>Incorrect password.</span>";
        }

        if (empty($errors)) {
            $_SESSION['email'] = $userEmail;
            $_SESSION['logged_in'] = true;

            header('location:friendlist.php');
        }
        mysqli_close($db_conn);
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta name="description" content="Web application development" />
    <meta name="keywords" content="PHP" />
    <meta name="author" content="NguyenCongAnh" />
    <link href="style.css" rel="stylesheet" type="text/css" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css"
        integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous" />
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <title>My Friends System</title>
</head>

<body>
    <nav class="navbar navbar-expand-lg">
        <h1>My Friends System</h1>
        <ul class="navbar-nav ms-auto">
            <li class="nav-item"><a class="nav-link" href="index.php">Home Page</a></li>
        </ul>
    </nav>

    <div id="login" class="container">
        <h1>Log in Page</h1>
        <br>

        <?php
        if (!empty($errors)):
            foreach ($errors as $error):
                echo "<p>" . $error . "</p>";
            endforeach;
        endif;
        ?>

        <form method="post" action="login.php">
            <label for="email">Email address</label><br>
            <input type="email" id="email" name="email" value="<?php echo isset($userEmail) ? $userEmail : ''; ?>"
                required><br><br>

            <label for="password">Password</label><br>
            <input type="password" id="password" name="password" required><br><br>

            <input type="submit" value="Log In">
            <input type="reset" value="Clear">
        </form>
        <br>
        <p>Don't have any account yet? <a href="signup.php">Sign Up Now</a></p>
        <p><a href="index.php">Return to Home page</a></p>
    </div>
</body>

</html>