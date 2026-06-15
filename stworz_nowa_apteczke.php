<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <title>Apteczka</title>
    <link rel="stylesheet" href="widok/styles.css">
</head>
<body>
    <nav class="menu_ekran_glowny">
        <a href='ekran_glowny.php'class="active">Ekran Główny</a>
        <a href='widok/nowa_apteczka.html'>Stwórz nową apteczkę</a>
        <a href='widok/dodaj_apteczke.html'>Dodaj apteczkę do konta</a>
        <a href='wyswietl_moje_apteczki.php'>Moje apteczki</a>
        <a href="wyloguj.php" class="wylogowanie">Wyloguj</a>
    </nav>
</body>
<?php
session_start();
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

$kit_name = mysqli_real_escape_string($dbconn, $_POST['nazwa_apteczki']);
$kit_password = mysqli_real_escape_string($dbconn, $_POST['haslo_apteczki']);
$kit_description = mysqli_real_escape_string($dbconn, $_POST['opis_apteczki']);

$kit_passwordhash = password_hash($kit_password, PASSWORD_DEFAULT);

if (mysqli_query($dbconn, "INSERT INTO kits (kit_name, kit_passwordhash, kit_description) 
    VALUES ('$kit_name', '$kit_passwordhash', '$kit_description')")) {
        // Pobierz ID właśnie dodanej apteczki
    $kit_id = mysqli_insert_id($dbconn);

    // Pobierz ID zalogowanego użytkownika z sesji
    $user_id = $_SESSION['current_user'];

    // Dodaj wpis do tabeli relacyjnej user_kit
    mysqli_query($dbconn, "INSERT INTO user_kits (user_id, kit_id) VALUES ('$user_id', '$kit_id')");
        echo "<div class='udana_rejestracja'>Utworzyłeś swoją apteczkę!</div>";
         echo"<a class='link' href='wyswietl_moje_apteczki.php'>Przejdź do Moje apteczki</a>";
    } else {
    if (mysqli_errno($dbconn) == 1062) {
        // Kod błędu 1062- oznacza, że wystąpił konflikt unikalności
        echo "<div class='nieudana_rejestracja'>Apteczka o takiej nazwie już istnieje! Zmień nazwę.</div>";
        echo"<a class='link' href='widok/nowa_apteczka.html'>Wróc do tworzenia apteczki</a>";
    } else {
        echo "<div class='nieudana_rejestracja'>Nieoczekiwany błąd.</div>";;
    }
}
?>

    

