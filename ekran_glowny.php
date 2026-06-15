<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ekran Główny</title>
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
    <div class="opis-strony">
    <h1>Witaj w Domedi!</h1>
    <p>
       Twoje centrum zarządzania lekami: wygodnie, bezpiecznie i zawsze pod ręką. <br>
        Twórz domowe apteczki, dodawaj leki z bazy, kontroluj terminy ważności i dbaj o zdrowie swojej rodziny z wyprzedzeniem. 
        Z Domedi masz wszystko pod kontrolą– przejrzyście, intuicyjnie z całodobową dostępnością.
    </p>
</div>
</body>
</html>

<?php
session_start();
?>