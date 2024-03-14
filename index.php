<?php
session_start();


$battleshipFrameDisplay = 'none';
$errorMessage = '';


if (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === true) {
    $battleshipFrameDisplay = 'block';
}


if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $username = $_POST['username'];
    $password = $_POST['password'];
    $servername = "localhost";
    $db_username = "root";
    $db_password = "";
    $database = "ships";


    $conn = mysqli_connect($servername, $db_username, $db_password, $database);


    if (!$conn) {
        die("Connection failed: " . mysqli_connect_error());
    }


    $username = mysqli_real_escape_string($conn, $username);
    $query = "SELECT * FROM playerdata WHERE username = '$username'";
    $result = mysqli_query($conn, $query);

    if (mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_assoc($result);


        if ($password === $row['password']) {
            $_SESSION['loggedin'] = true;
            $battleshipFrameDisplay = 'block';
        } else {
            $errorMessage = 'Incorrect password.';
        }
    } else {
        $errorMessage = 'Username does not exist.';
    }


    mysqli_close($conn);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Battleship Game</title>
<style>
    #inputBox, h1 {
        margin-top: 20px;
        text-align: center;
    }
    h1 {
        font-family: "Blackbeard";
    }
    #battleshipFrame {
        display: <?php echo $battleshipFrameDisplay; ?>;
    }
    
</style>
</head>
<body>
    <h1>Battleship Game</h1>
    <div id="inputBox">
        <form action="" method="post">
            <label for="username">Enter username:</label><br>
            <input type="text" id="username" name="username" required><br>
            <label for="password">Enter password:</label><br>
            <input type="password" id="password" name="password" required><br>
            <button type="submit">Unlock</button>
        </form>
        <a href="register.php">Register</a> <a href="logout.php">Logout</a>
    </div>

    <p align="center" id="battleshipFrame">
        <?php
        if ($battleshipFrameDisplay === 'block') {
            header("Location: statki.php");
        }
        ?>
    </p>

    <div id="error-message"><?php echo $errorMessage; ?></div>
</body>
</html>
