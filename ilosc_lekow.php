<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <title>Apteczka</title>
    <link rel="stylesheet" href="widok/styles_moje_apteczki.css">
</head>
<body>
        <nav class="menu_ekran_glowny">
        <a href='ekran_glowny.php'>Ekran Główny</a>
        <a href='widok/nowa_apteczka.html'>Stwórz nową apteczkę</a>
        <a href='widok/dodaj_apteczke.html'>Dodaj apteczkę do konta</a>
        <a href='wyswietl_moje_apteczki.php'>Moje apteczki</a>
        <a href="ilosc_lekow.php?kit_id=<?= htmlspecialchars($_GET['kit_id']) ?>"class="active">Historia ruchów leków</a>
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

if (!isset($_SESSION['current_user'])) {
    header("Location: widok/logowanie.html");
    exit();
}

$kit_id = $_GET['kit_id'] ?? null;

// Pobierz nazwę apteczki
$kit_query = mysqli_query($dbconn, "SELECT kit_name FROM kits WHERE kit_id = '$kit_id'");
$kit_row = mysqli_fetch_assoc($kit_query);
$kit_name = htmlspecialchars($kit_row['kit_name']);

echo "<h5>Historia ruchów leków – apteczka: $kit_name</h5>";

// Pobierz ruchy leków
$query = "
    SELECT mm.*, m.medicine_name, u.user_email
    FROM medicine_movements mm
    JOIN medicines m ON mm.medicine_id = m.medicine_id
    JOIN users u ON mm.user_id = u.user_id
    WHERE mm.kit_id = '$kit_id'
    ORDER BY mm.created_at DESC
";
$result = mysqli_query($dbconn, $query);

if (mysqli_num_rows($result) > 0) {
    echo "<table border='1' cellpadding='8'>
            <tr>
                <th>Data</th>
                <th>Lek</th>
                <th>Ilość</th>
                <th>Typ ruchu</th>
                <th>Użytkownik</th>
            </tr>";
    while ($row = mysqli_fetch_assoc($result)) {
        echo "<tr>
                <td>{$row['created_at']}</td>
                <td>" . htmlspecialchars($row['medicine_name']) . "</td>
                <td>{$row['quantity']}</td>
                <td>{$row['movement_type']}</td>
                <td>" . htmlspecialchars($row['user_email']) . "</td>
              </tr>";
    }
    echo "</table>";
} else {
    echo "<p>Brak zapisanych ruchów leków dla tej apteczki.</p>";
}
?>
<br>
<a href="apteczka.php?kit_id=<?= htmlspecialchars($kit_id) ?>" class="link">← Wróć do apteczki</a>