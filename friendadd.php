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
            <li class="nav-item"><a class="nav-link" href="friendlist.php">Friend List</a></li>
            <li class="nav-item"><a class="nav-link" href="logout.php">Log Out</a></li>
            <li class="nav-item"><a class="nav-link" href="about.php">About</a></li>
        </ul>
    </nav>

    <div id="add" class="container">
        <?php
        session_start();
        require_once 'settings.php';

        $db_conn = mysqli_connect($host, $user, $pswd, $dbnm) or die("Connection failed: " . mysqli_connect_error());

        if (!$db_conn) {
            die("Failed to connect to database: " . $db_conn->connect_error . "<br>");
        }
        $sessionUserEmail = $_SESSION['email'];
        $message = "";

        $sqlGetFriendID = "SELECT friend_id, profile_name FROM friends WHERE friend_email = '$sessionUserEmail'";
        $sessionFriendIDQuery = mysqli_query($db_conn, $sqlGetFriendID);
        $row = mysqli_fetch_assoc($sessionFriendIDQuery);
        $sessionFriendID = $row['friend_id'];
        $sessionProfileName = $row['profile_name'];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $addFriendID = $_POST['friend_id'];
            $sqlAddFriend = "INSERT INTO myfriends (friend_id1, friend_id2) VALUES ($sessionFriendID, $addFriendID)";
            $sqlUpdateFriendNum1 = "UPDATE friends SET num_of_friends = num_of_friends + 1 WHERE friend_id = '$sessionFriendID'";
            $sqlUpdateFriendNum2 = "UPDATE friends SET num_of_friends = num_of_friends + 1 WHERE friend_id = '$addFriendID'";

            $resultAddFriend = mysqli_query($db_conn, $sqlAddFriend);
            $resultUpdateFriendNum1 = mysqli_query($db_conn, $sqlUpdateFriendNum1);
            $resultUpdateFriendNum2 = mysqli_query($db_conn, $sqlUpdateFriendNum2);

            if ($resultAddFriend) {
                $message = "<p>A new friend is added successfully!</p>";
                if ($resultUpdateFriendNum1 && $resultUpdateFriendNum2) {
                    $message = "<p>Friend number is updated successfully!</p>";
                }
            } else {
                $message = "<p>Failed to add a new friend!</p>";
            }
        }

        $sqlGetNewFriendName = "SELECT DISTINCT friends.friend_id, friends.profile_name
                        FROM friends
                        LEFT JOIN myfriends
                        ON (myfriends.friend_id1 = friends.friend_id OR myfriends.friend_id2 = friends.friend_id)
                        WHERE friends.friend_id != '$sessionFriendID' 
                        AND ((myfriends.friend_id1 IS NULL AND myfriends.friend_id2 IS NULL) 
                            OR (myfriends.friend_id1 != '$sessionFriendID' AND myfriends.friend_id2 != '$sessionFriendID')
                            AND friends.friend_id NOT IN (
                                SELECT CASE WHEN friend_id1 = $sessionFriendID
                                THEN friend_id2 ELSE friend_id1 END AS added_friend_id
                                FROM myfriends WHERE friend_id1 = $sessionFriendID OR friend_id2 = $sessionFriendID)
                        )
                        ORDER BY friends.friend_id ASC";

        $resultSelectNewFriend = mysqli_query($db_conn, $sqlGetNewFriendName);
        ?>

        <?php
        if (!empty($message)) {
            echo $message;
            $message = "";
        }

        if (isset($_SESSION['logged_in']) && $_SESSION['logged_in'] == true) {
            $sqlGetFriendNum = "SELECT num_of_friends FROM friends WHERE friend_email = '$sessionUserEmail'";
            $getFriendNumResult = mysqli_query($db_conn, $sqlGetFriendNum);
            $row = mysqli_fetch_assoc($getFriendNumResult);
            $friendTotal = $row['num_of_friends'];

            echo "<p style='text-align:center; font-size:30px;'>Welcome, " . $sessionProfileName . "!</br>";
            echo "Total number of friends is " . $friendTotal . "!</p>";

            if (mysqli_num_rows($resultSelectNewFriend) > 0) {
                echo "<h2 style='text-align:center; margin: 5px;'>People you may know</h2>";
                echo "<table style='margin:auto; width:70%'>";
                echo "<tr>";
                echo "<th style='border: 1px solid;padding: 8px;'>Friend ID</th>";
                echo "<th style='border: 1px solid;padding: 8px;'>Friend Name</th>";
                echo "<th style='border: 1px solid;padding: 8px;'>Action</th>";
                echo "</tr>";

                while ($row = mysqli_fetch_array($resultSelectNewFriend)) {
                    echo "<tr>";
                    echo "<td style='border: 1px solid;padding: 8px;'>" . $row['friend_id'] . "</td>";
                    echo "<td style='border: 1px solid;padding: 8px;'>" . $row['profile_name'] . "</td>";
                    echo "<td style='border: 1px solid;padding: 8px;'>";

                    echo '<form action="friendadd.php" method="POST">';
                    echo '<input type="hidden" name="friend_id" value="' . $row['friend_id'] . '">';
                    echo '<input type="hidden" name="friend_profile_name" value="' . $row['profile_name'] . '">';
                    echo '<button type="submit" name="addfriend" onclick="return confirm(\'Add this friend?\')"> Add friend</button>';
                    echo '</form>';

                    echo "</td>";
                    echo "</tr>";
                }
                echo "</table>";

                mysqli_free_result($resultSelectNewFriend);
                $db_conn->close();
            } else {
                echo "<p>No new friends for you to add.</p>";
            }
        } else {
            header("location:login.php");
        }
        ?>
    </div>
</body>

</html>