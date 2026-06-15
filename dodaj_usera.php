<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <title>Apteczka</title>
    <link rel="stylesheet" href="widok/styles.css">
</head>
<body>
    <nav class="menu_strona_glowna">
        <a href="widok/strona_glowna.html">Strona Główna</a>
        <a href="widok/o_nas.html">O Nas</a>
        <a href="widok/logowanie.html">Zaloguj się</a>
        <a href="widok/rejestracja.html">Zarejestruj się</a>
    </nav>
<?php
header('Content-Type: text/html; charset=utf-8');

$servername = "mysql.agh.edu.pl";
$username = "aszota";
$dbpassword = "2aXUCE9r6fnB0UZU";
$dbname = "aszota";

$dbconn = mysqli_connect($servername, $username, $dbpassword, $dbname);
mysqli_set_charset($dbconn, "utf8mb4");

if (!$dbconn) {
    die("Błąd połączenia: " . mysqli_connect_error());
}

$user_firstname = mysqli_real_escape_string($dbconn, $_POST['imie']);
$user_lastname = mysqli_real_escape_string($dbconn, $_POST['nazwisko']);
$user_email = mysqli_real_escape_string($dbconn, $_POST['email']);
$user_password = mysqli_real_escape_string($dbconn, $_POST['haslo']);

$user_password_hash = password_hash($user_password, PASSWORD_DEFAULT);

if (mysqli_query($dbconn, "INSERT INTO users (user_firstname, user_lastname, user_email, user_passwordhash) 
    VALUES ('$user_firstname', '$user_lastname', '$user_email', '$user_password_hash')")) {
    echo "<div class='udana_rejestracja'>Rejestracja przebiegła poprawnie</div>";
    echo"<a class='link' href='widok/logowanie.html'>Przejdź do logowania</a>"; // p to nowy akapit 
    
} else {
    if (mysqli_errno($dbconn) == 1062) {
        // Kod błędu 1062- oznacza, że wystąpił konflikt unikalności
        echo "<div class='nieudana_rejestracja'>Użytkownik z takim adresem e-mail już istnieje! Spróbuj ponownie.</div>";
        echo "<a class='link' href='widok/rejestracja.html'>Przejdź do rejestracji</a>";
    } else {
        echo "Nieoczekiwany błąd";
    }
}
