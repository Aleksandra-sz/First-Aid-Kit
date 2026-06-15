<?php
$kit_id = $_GET['kit_id'];
?>

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
        <a href='wyswietl_moje_apteczki.php'class="active">Moje apteczki</a>
        <a href="ilosc_lekow.php?kit_id=<?= htmlspecialchars($kit_id) ?>">Historia ruchów leków</a>
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
$user_id = $_SESSION['current_user'];
$kit_id = $_GET['kit_id'];
$_POST['kit_id'] = $kit_id;
// Nazwa apteczki
$query = mysqli_query($dbconn, "SELECT kit_name FROM kits WHERE kit_id ='$kit_id'");
$record = mysqli_fetch_assoc($query);

echo "<h5>Apteczka: " . htmlspecialchars($record['kit_name']) . "</h5>";

// Ostrzeżenie o przeterminowanych lekach
$today = date('Y-m-d');
$expired_query = "
    SELECT km.*, m.medicine_name 
    FROM kit_medicines km
    JOIN medicines m ON km.medicine_id = m.medicine_id
    WHERE km.kit_id = '$kit_id' AND km.expiry_date < '$today'
";
$expired_result = mysqli_query($dbconn, $expired_query);

if (mysqli_num_rows($expired_result) > 0) {
    echo "<div style='color: red; border: 2px solid red; padding: 10px; margin-bottom: 15px;'>
            <strong>Uwaga! Masz przeterminowane leki:</strong><ul>";
    while ($row = mysqli_fetch_assoc($expired_result)) {
        echo "<li>
                {$row['medicine_name']} (przeterminowany: {$row['expiry_date']})
                <form method='POST' style='display:inline; margin-left:10px;'>
                    <input type='hidden' name='delete_medicine_id' value='{$row['medicine_id']}'>
                    <input type='submit' name='dispose' value='Utylizuj'>
                </form>
              </li>";
    }
    echo "</ul></div>";
}

// Utylizacja przeterminowanego leku
if (isset($_POST['dispose'])) {

    $medicine_id = $_POST['delete_medicine_id'];
    $quantity_query = "SELECT quantity FROM kit_medicines WHERE kit_id = '$kit_id' AND medicine_id = '$medicine_id'";

    $quantity_result = mysqli_query($dbconn, $quantity_query);
    if (!$quantity_result) {
        echo "<p style='color:red;'>Błąd SELECT: " . mysqli_error($dbconn) . "</p>";
        exit;
    }

    $quantity_row = mysqli_fetch_assoc($quantity_result);
    $quantity = $quantity_row['quantity'] ?? 0;
    $delete = "DELETE FROM kit_medicines WHERE kit_id = '$kit_id' AND medicine_id = '$medicine_id'";

    if (mysqli_query($dbconn, $delete)) {
        if ($quantity>0){
        $add_history = "INSERT INTO medicine_movements (kit_id, medicine_id, user_id, quantity, movement_type, created_at)
                        VALUES ('$kit_id', '$medicine_id', '$user_id', $quantity, 'utylizacja', NOW())";

        $history_result = mysqli_query($dbconn, $add_history);
        }
        if ($history_result) {
            echo "<p style='color:green;'>Lek został zutylizowany.</p>";
        }

    } else {
        echo "<p style='color:red;'>Błąd przy usuwaniu leku: " . mysqli_error($dbconn) . "</p>";
    }
}


// Dodawanie leku
if (isset($_POST['add_medicine'])) {
    $medicine_id = $_POST['medicine_id'];
    $quantity = $_POST['quantity'];
    $price = $_POST['price'];
    $expiry_date = $_POST['expiry_date'];

    $insert = "INSERT INTO kit_medicines (kit_id, medicine_id, quantity, price, expiry_date)
               VALUES ('$kit_id', '$medicine_id', '$quantity', '$price', '$expiry_date')";
    
    if (mysqli_query($dbconn, $insert)) {
        echo "<p style='color:green;'>Lek dodany pomyślnie</p>";
        $add_history = "INSERT INTO medicine_movements (kit_id, medicine_id, user_id, quantity, movement_type, created_at)
                     VALUES ('$kit_id', '$medicine_id', '$user_id', $quantity,'dodanie',   NOW())";
                     
    mysqli_query($dbconn, $add_history);
    } else {
        echo "<p style='color:red;'>Błąd: " . mysqli_error($dbconn) . "</p>";
    }
}

// Zażycie leku
if (isset($_POST['consume'])) {
    $medicine_id = $_POST['medicine_id'];

    // Zmniejszenie ilości o 1 (jeśli ilość > 0)
    $update = "UPDATE kit_medicines 
               SET quantity = quantity - 1 
               WHERE kit_id = '$kit_id' AND medicine_id = '$medicine_id' AND quantity > 0";
    $update_result = mysqli_query($dbconn, $update);

    if (!$update_result) {
        echo "<p style='color:red;'>Błąd UPDATE: " . mysqli_error($dbconn) . "</p>";
        exit;
    }

    // Sprawdzenie nowej ilości
    $check_quantity = "SELECT quantity FROM kit_medicines 
                       WHERE kit_id = '$kit_id' AND medicine_id = '$medicine_id'";
    $quantity_result = mysqli_query($dbconn, $check_quantity);

    if ($quantity_result && $row = mysqli_fetch_assoc($quantity_result)) {
        $new_quantity = $row['quantity'];

        // Jeśli ilość wynosi 0, usuń rekord
        if ($new_quantity == 0) {
            $delete = "DELETE FROM kit_medicines 
                       WHERE kit_id = '$kit_id' AND medicine_id = '$medicine_id'";
            mysqli_query($dbconn, $delete);
        }
    }

    // Dodanie wpisu do historii
    $add_history = "INSERT INTO medicine_movements (
                        kit_id, medicine_id, user_id, quantity, movement_type, created_at
                    ) VALUES (
                        '$kit_id', '$medicine_id', '$user_id', 1, 'przyjęcie', NOW()
                    )";
    mysqli_query($dbconn, $add_history);
}
?>

<!-- Formularz dodawania leku -->
<h3>Dodaj lek do apteczki</h3>
<form method="POST">
    <label for="medicine_id">Wybierz lek:</label>
    <select name="medicine_id" required>
        <?php
        $result = mysqli_query($dbconn, "SELECT medicine_id, medicine_name FROM medicines");
        while ($row = mysqli_fetch_assoc($result)) {
            echo "<option value='{$row['medicine_id']}'>" . htmlspecialchars($row['medicine_name']) . "</option>";
        }
        ?>
    </select><br>

    Ilość: <input type="number" name="quantity" min="1" required><br>
    Cena: <input type="number" step="0.01" name="price"><br>
    Data ważności: <input type="date" name="expiry_date" required><br>
    <input type="submit" name="add_medicine" value="Dodaj lek">
</form>

<!-- Lista leków w apteczce -->
<h3>Leki w tej apteczce:</h3>
<?php
$query = "SELECT km.*, m.medicine_name 
          FROM kit_medicines km
          JOIN medicines m ON km.medicine_id = m.medicine_id
          WHERE km.kit_id = '$kit_id'";
$result = mysqli_query($dbconn, $query);

if (mysqli_num_rows($result) > 0) {
    echo "<ul>";
    while ($row = mysqli_fetch_assoc($result)) {
        // Czerwony kolor jeśli przeterminowany
        $style = ($row['expiry_date'] < $today) ? "style='color:red;'" : "";
        echo "<li $style>
                <strong>{$row['medicine_name']}</strong> | Ilość: {$row['quantity']} | Cena: {$row['price']} zł | Ważność: {$row['expiry_date']}
                <form method='POST' style='display:inline; margin-left:10px;'>
                    <input type='hidden' name='medicine_id' value='{$row['medicine_id']}'>
                    <input type='submit' name='consume' value='Zażyj 1'>
                </form>
            </li>";
    }
    echo "</ul>";
} else {
    echo "<p>Brak leków w tej apteczce.</p>";
}
?>

