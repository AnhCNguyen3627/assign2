<?php
session_start();
require_once("settings.php");

$db_conn = mysqli_connect($host, $user, $pswd, $dbnm) or die("Connection failed: " . mysqli_connect_error());

if (!$db_conn) {
    die("Failed to connect to database: " . $db_conn->connect_error . "<br>");
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['email']) && isset($_POST['profile_name']) && isset($_POST['password']) && isset($_POST['confirm_password'])) {
        $userEmail = $_POST['email'];
        $userProfileName = $_POST['profile_name'];
        $userPassword = $_POST['password'];
        $confirmPassword = $_POST['confirm_password'];
        $errors = array();

        if (!filter_var($userEmail, FILTER_VALIDATE_EMAIL)) {
            $errors[] = "<span style='color: red;'>Invalid email address format.</span>";
        }

        if (!preg_match("/^[a-zA-Z ]+$/", $userProfileName)) {
            $errors[] = "<span style='color: red;'>Your profile name must contain only letters.</span>";
        }

        if (empty($userProfileName)) {
            $errors[] = "<span style='color: red;'>Your profile name must not be blank.</span>";
        }

        if (!preg_match("/^[a-zA-Z0-9]+$/", $userPassword)) {
            $errors[] = "<span style='color: red;'>Your password must contain only letters and numbers</span>";
        }

        if ($userPassword != $confirmPassword) {
            $errors[] = "<span style='color: red;'>Your password and confirm password do not match</span>";
        }

        $emailQuery = "SELECT * FROM friends WHERE friend_email = '$userEmail'";
        $emailResult = mysqli_query($db_conn, $emailQuery);

        if (!$emailResult) {
            $errors[] = "<span style='color: red;'>Error executing the query: " . mysqli_error($db_conn) . ".</span>";
        } else {
            if (mysqli_num_rows($emailResult) > 0) {
                $errors[] = "<span style='color: red;'>Email address already exists.</span>";
            }
        }

        if (empty($errors)) {
            $_SESSION['email'] = $userEmailAddress;

            $insertNewUserQuery = "INSERT INTO friends (friend_email, profile_name, password, date_started, num_of_friends) VALUES ('$userEmail', '$userProfileName', '$userPassword', CURDATE(), 0)";

            if (mysqli_query($db_conn, $insertNewUserQuery)) {
                echo "<p>Registration successful.</p>";

                mysqli_close($db_conn);
                header("location:login.php");
            } else {
                $errors[] = "<span style='color: red;'>Error: mysqli_error" . $db_conn->error . ".</span><br>";
            }
        }
        mysqli_close($db_conn);
    } else {
        $errors[] = "<span style='color: red;'>All fields must be filled in.</span>";
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

    <div id="signup" class="container">
        <h1>Registration Page</h1>
        <br>

        <?php
        if (!empty($errors)):
            foreach ($errors as $error):
                echo $error;
            endforeach;
        endif;
        ?>

        <form method="post" action="signup.php">
            <label for="email">Email address</label><br>
            <input type="email" id="email" name="email" value="<?php echo isset($userEmail) ? $userEmail : ''; ?>"
                required><br><br>

            <label for="profile_name">Profile name</label><br>
            <input type="text" id="profile_name" name="profile_name"
                value="<?php echo isset($userProfileName) ? $userProfileName : ''; ?>" required><br><br>

            <label for="password">Password</label><br>
            <input type="password" id="password" name="password" required><br><br>

            <label for="confirm_password">Confirm password</label><br>
            <input type="password" id="confirm_password" name="confirm_password" required><br><br>

            <input type="submit" value="Register">
            <input type="reset" value="Clear">
        </form>
        <br>

        <p>Have an account already? <a href="login.php">Log In Now</a></p>
        <p><a href="index.php">Return to Home page</a></p>
    </div>

</body>

</html>