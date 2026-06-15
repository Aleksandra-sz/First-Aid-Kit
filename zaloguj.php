<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <title>Apteczka</title>
</head>
<body>
<?php
session_start();

$servername = "mysql.agh.edu.pl";
$username = "aszota";
$password = "2aXUCE9r6fnB0UZU";
$dbname = "aszota";

$dbconn = mysqli_connect($servername, $username, $password, $dbname);
mysqli_set_charset($dbconn, "utf8mb4");

if (!$dbconn) {
    die("Błąd połączenia: " . mysqli_connect_error());
}

$user_password = mysqli_real_escape_string($dbconn, $_POST["haslo"]);
$user_email = mysqli_real_escape_string($dbconn, $_POST["email"]);

$query = mysqli_query($dbconn, "SELECT * FROM users WHERE user_email ='$user_email'");


if (mysqli_num_rows($query) > 0) {
    $record = mysqli_fetch_assoc($query);
    $hash = $record["user_passwordhash"];

    
    if (password_verify($user_password, $hash)) {
        $_SESSION["current_user"] = $record["user_id"];
        header("Location:ekran_glowny.php"); // przekierowanie po zalogowaniu
        exit();
    }
}

if (isset($_SESSION["current_user"])) {
    echo "Użytkownik jest zalogowany: " . $_SESSION["current_user"];
    echo "<br>";
    echo "<a href='wyloguj.php'>Wyloguj</a>";
} else {
    echo "Użytkownik nie jest zalogowany";
}
?>

