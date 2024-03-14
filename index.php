<?php
session_start();

if (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === true) {
    $battleshipFrameDisplay = 'block';
    $errorMessage = '';
} else {
    $battleshipFrameDisplay = 'none';
    $errorMessage = '';
}


if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $password = $_POST['password'];
    $servername = "localhost";
    $db_username = "root";
    $db_password = "";
    $database = "ships";

    $conn = new mysqli($servername, $db_username, $db_password, $database);

    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    $query = "SELECT * FROM playerdata WHERE username = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        if ($password === $row['password']) {
            $_SESSION['loggedin'] = true;
            $battleshipFrameDisplay = 'block';
            $errorMessage = '';
        } else {
            $errorMessage = 'Incorrect password.';
        }
    } else {
        $errorMessage = 'Username does not exist.';
    }

    $stmt->close();
    $conn->close();
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
    <h1>~Battleship Game~</h1>
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

    <p align="center" id="battleshipFrame"><iframe src="statki.html" width="2000" height="800"></iframe></p>

    <div id="error-message"><?php echo $errorMessage; ?></div>
</body>
</html>
