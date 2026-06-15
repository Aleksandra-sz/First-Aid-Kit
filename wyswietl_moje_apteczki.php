<!-- Najpierw kod PHP- bo echo wypisuje informacje na stronie -->
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

$user_id = $_SESSION['current_user']; 

$query = "
    SELECT kits.kit_id, kits.kit_name, kits.kit_description
    FROM kits
    JOIN user_kits ON kits.kit_id = user_kits.kit_id
    WHERE user_kits.user_id = '$user_id'
";

$result = mysqli_query($dbconn, $query);
?>

<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <title>Moje Apteczki</title>
    <link rel="stylesheet" href="widok/styles.css">
</head>
<body>
    <nav class="menu_ekran_glowny">
        <a href='ekran_glowny.php'>Ekran Główny</a>
        <a href='widok/nowa_apteczka.html'>Stwórz nową apteczkę</a>
        <a href='widok/dodaj_apteczke.html'>Dodaj apteczkę do konta</a>
        <a href='wyswietl_moje_apteczki.php' class="active">Moje apteczki</a>
        <a href="wyloguj.php" class="wylogowanie">Wyloguj</a>
    </nav>
        <div class="lista-apteczek">
        <h2>Moje apteczki</h2>
        <ul class="apteczki">
            <?php
            if ($result && mysqli_num_rows($result) > 0) {
                while ($row = mysqli_fetch_assoc($result)) {
                    $kit_id = $row['kit_id'];
                    $kit_name = htmlspecialchars($row['kit_name']);
                    $kit_description = htmlspecialchars($row['kit_description']);

                    echo "<li class='apteczka'>
                            <a class='apteczka-link' href='apteczka.php?kit_id=$kit_id'>$kit_name</a>
                            <p class='apteczka-desc'>$kit_description</p>
                          </li>";
                }
            } else {
                echo "<li class='apteczka'><p class='apteczka-desc'>Nie masz jeszcze przypisanych apteczek.</p></li>";
            }
            ?>
        </ul>
    </div>
</body>
</html>