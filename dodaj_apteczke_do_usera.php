<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <title>Dodaj istniejącą Apteczkę</title>
    <link rel="stylesheet" href="widok/styles.css">
</head>
<body>
    <nav class="menu_ekran_glowny">
        <a href='ekran_glowny.php'>Ekran Główny</a>
        <a href='widok/nowa_apteczka.html'>Stwórz nową apteczkę</a>
        <a href='widok/dodaj_apteczke.html'>Dodaj apteczkę do konta</a>
        <a href='wyswietl_moje_apteczki.php'>Moje apteczki</a>
        <a href="wyloguj.php" class="wylogowanie">Wyloguj</a>
    </nav>
</body>
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

$kit_name = mysqli_real_escape_string($dbconn, $_POST["nazwa_apteczki"]);
$kit_password = mysqli_real_escape_string($dbconn, $_POST["haslo_apteczki"]);

// Sprawdzenie czy apteczka o podanej nazwie istnieje
$query_name = mysqli_query($dbconn, "SELECT * FROM kits WHERE kit_name = '$kit_name'");

if (mysqli_num_rows($query_name) > 0) {
    $record = mysqli_fetch_assoc($query_name);
    $correct_password = $record["kit_passwordhash"];

    // Sprawdzamy czy hasło jest poprawne
    if (password_verify($kit_password, $correct_password)) {
        // Hasło się zgadza, więc dajemy do user_kits
        $kit_id = $record["kit_id"];
        $user_id = $_SESSION['current_user'];
        
        $result = mysqli_query($dbconn, "INSERT INTO user_kits (user_id, kit_id) VALUES ('$user_id', '$kit_id')");
        if ($result) {
            echo "<div class='udana_rejestracja'>Dodano apteczkę $kit_name do Moje apteczki</div>";
            echo "<a class='link' href='wyswietl_moje_apteczki.php'>Zobacz moje apteczki</a>";
        } else {
            echo "<div class='nieudana_rejestracja'>Ta apteczka została już dodana</div>";
            echo "<a class='link' href='wyswietl_moje_apteczki.php'>Zobacz moje apteczki</a>";
            exit();
        }
    } else {
        // Hasło jest błędne
        echo "<div class='nieudana_rejestracja'>Błędne hasło do apteczki o nazwie <strong>$kit_name</strong>.</div>";
        echo "<a class='link' href='widok/dodaj_apteczke.html'>Wróć do dodawania apteczki</a>";
        exit();
    }
} else {
    // Nie znaleziono apteczki o podaej nazwie
    echo "<div class='nieudana_rejestracja'>Nie znaleziono apteczki o podanej nazwie.</div>";
    echo "<a class='link' href='widok/dodaj_apteczke.html'>Wróć do dodawania apteczki</a>";
    exit();
}
?>



       
  