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
            <li class="nav-item">
                <a class="nav-link" href="signup.php">Sign Up</a>
            </li>
            <li class="nav-item"><a class="nav-link" href="login.php">Log In</a></li>
            <li class="nav-item"><a class="nav-link" href="about.php">About</a></li>
        </ul>
    </nav>

    <div id="indexContent" class="container">
        <h1>Assignment Home Page</h1>
        <p>Name: Nguyen Cong Anh</p>
        <p>Student ID: 103792960 </p>
        <p>Email: <a class="email" href="mailto:103792960@student.swin.edu.au">103792960@student.swin.edu.au</a></p>
        <p>I declare that this assignment is my individual work. I have not worked collaboratively nor have I copied
            from any other student's work or from any other source.</p>

        <?php
        require_once("settings.php");
        $db_conn = mysqli_connect($host, $user, $pswd, $dbnm) or die("Connection failed: " . mysqli_connect_error());

        if (!$db_conn) {
            die("Failed to connect to database: " . $db_conn->connect_error . "<br>");
        }

        $createTable_friends = "CREATE TABLE IF NOT EXISTS friends (
            friend_id INT NOT NULL AUTO_INCREMENT,
            friend_email VARCHAR(50) NOT NULL,
            password VARCHAR(20) NOT NULL,
            profile_name VARCHAR(30) NOT NULL,
            date_started DATE NOT NULL,
            num_of_friends INT UNSIGNED,
            PRIMARY KEY (friend_id)
        )";

        $createTable_myfriends = "CREATE TABLE IF NOT EXISTS myfriends (
            friend_id1 INT NOT NULL,
            friend_id2 INT NOT NULL,
            CHECK (friend_id1 <> friend_id2)
        )";

        if ($db_conn->query($createTable_friends) === TRUE) {
            echo "Table 'friends' has successfully been created.<br>";
        } else {
            echo "Failed to create table: " . $db_conn->error . "<br>";
        }

        if ($db_conn->query($createTable_myfriends) === TRUE) {
            echo "Table 'myfriends' has successfully been created.<br>";
        } else {
            echo "Failed to create table: " . $db_conn->error . "<br>";
        }

        $insertTable_friends = "INSERT INTO friends (friend_email, password, profile_name, date_started, num_of_friends) VALUES
        ('gblackadder0@biglobe.ne.jp', 'password1', 'Gratia Blackadder', '2023-01-12', 3),
        ('kbidgood1@tumblr.com', 'password2', 'Kyle Bidgood', '2022-12-02', 6), 
        ('dfido2@skyrock.com', 'password3', 'Donavon Fido', '2023-03-21', 5), 
        ('lmcgahey3@ca.gov', 'password4', 'Luise McGahey', '2023-02-12', 3), 
        ('warchbutt4@rambler.ru', 'password5', 'Winnie Archbutt', '2023-06-09', 5), 
        ('kfoxwell5@cmu.edu', 'password6', 'Kippy Foxwell', '2023-04-26', 4), 
        ('plittrik6@taobao.com', 'password7', 'Pia Littrik', '2023-01-15', 4), 
        ('mpulver7@msu.edu', 'password8', 'Melesa Pulver', '2023-06-27', 5), 
        ('jwagge8@nbcnews.com', 'password9', 'Jermayne Wagge', '2023-07-21', 2), 
        ('rkunneke9@gov.uk', 'password0', 'Rosalyn Kunneke', '2023-10-05', 3)
        ";

        $insertTable_myfriends = "INSERT INTO myfriends (friend_id1, friend_id2) VALUES
        (7,3), (4,8), (4,6), (2,4), (2,6),
        (10,5), (8,1), (1,5), (7,10), (10,1),
        (6,8), (3,2), (5,7), (8,2), (9,6),
        (2,5), (9,3), (7,2), (8,3), (3,5)
        ";

        $checkTable_friends = "SELECT * FROM friends";
        $checkTable_myfriends = "SELECT * FROM myfriends";
        $friendsResult = $db_conn->query($checkTable_friends);
        $myfriendsResult = $db_conn->query($checkTable_myfriends);

        if (mysqli_num_rows($friendsResult) == 0 || mysqli_num_rows($myfriendsResult) == 0) {
            if ($db_conn->query($insertTable_friends) === TRUE) {
                echo "Data has been successfully inserted into Table 'friends'.<br>";
            } else {
                echo "Failed to insert data into Table 'friends': " . $db_conn->error . "<br>";
            }

            if ($db_conn->query($insertTable_myfriends) === TRUE) {
                echo "Data has been successfully inserted into Table 'myfriends'.<br>";
            } else {
                echo "Failed to insert data into Table 'myfriends': " . $db_conn->error . "<br>";
            }
        } else {
            echo "Table 'friends' and 'myfriends' are not empty<br>";
        }

        $db_conn->close();
        ?>
    </div>
</body>

</html>