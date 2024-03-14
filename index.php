<?php
session_start();

// Inicjalizacja zmiennych
$battleshipFrameDisplay = 'none';
$errorMessage = '';

// Sprawdzenie czy użytkownik jest zalogowany
if (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === true) {
    $battleshipFrameDisplay = 'block';
}

// Sprawdzenie czy dane zostały przesłane przez formularz POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Pobranie danych z formularza
    $username = $_POST['username'];
    $password = $_POST['password'];
    $servername = "localhost";
    $db_username = "root";
    $db_password = "";
    $database = "ships";

    // Połączenie z bazą danych
    $conn = mysqli_connect($servername, $db_username, $db_password, $database);

    // Sprawdzenie czy udało się połączyć z bazą danych
    if (!$conn) {
        die("Connection failed: " . mysqli_connect_error());
    }

    // Zabezpieczenie przed atakami SQL injection
    $username = mysqli_real_escape_string($conn, $username);

    // Zapytanie do bazy danych
    $query = "SELECT * FROM playerdata WHERE username = '$username'";
    $result = mysqli_query($conn, $query);

    // Sprawdzenie czy znaleziono użytkownika w bazie danych
    if (mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_assoc($result);
        // Sprawdzenie czy hasło się zgadza
        if ($password === $row['password']) {
            $_SESSION['loggedin'] = true;
            $battleshipFrameDisplay = 'block';
        } else {
            $errorMessage = 'Incorrect password.';
        }
    } else {
        $errorMessage = 'Username does not exist.';
    }

    // Zamknięcie połączenia z bazą danych
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
