<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <title>Apteczka</title>
</head>
<body>
<?php
session_start();
session_unset();
session_destroy();
header("Location: widok/logowanie.html"); 
exit();
?>