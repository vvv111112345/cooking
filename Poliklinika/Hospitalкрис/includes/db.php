<?php
$host = 'localhost';
$dbname = 'detskaya_poliklinika';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    // Альтернативный способ установки режима ошибок (для старых версий PHP)
    $pdo->setAttribute(3, 1); // 3 = ATTR_ERRMODE, 1 = ERRMODE_EXCEPTION
} catch (PDOException $e) {
    die("Ошибка подключения к базе данных: " . $e->getMessage());
}
?>