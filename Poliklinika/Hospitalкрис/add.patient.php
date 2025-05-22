<?php 
require 'db.php';

// Устанавливаем кодировку
header('Content-Type: text/html; charset=utf-8');

// Включаем отображение ошибок
error_reporting(E_ALL);
ini_set('display_errors', 1);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Очищаем и проверяем входные данные
    $fio = trim($_POST['fio'] ?? '');
    $address = trim($_POST['address'] ?? '');
    $gorod = trim($_POST['gorod'] ?? '');
    $vozract = intval($_POST['vozract'] ?? 0);
    $pol = $_POST['pol'] === 'м' ? 'м' : 'ж'; // Защита от неверных значений
    
    try {
        $stmt = $pdo->prepare("INSERT INTO Пациенты (FIO, address, gorod, vozract, pol) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([$fio, $address, $gorod, $vozract, $pol]);
        
        // Перенаправляем после успешного добавления
        header("Location: index.php");
        exit();
    } catch (PDOException $e) {
        $error = "Ошибка базы данных: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Добавить пациента</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <h1>Добавить нового пациента</h1>
        
        <?php if (!empty($error)): ?>
            <div class="error"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>
        
        <form method="POST" action="">
            <div class="form-group">
                <label>ФИО:</label>
                <input type="text" name="fio" required>
            </div>
            
            <div class="form-group">
                <label>Адрес:</label>
                <input type="text" name="address" required>
            </div>
            
            <div class="form-group">
                <label>Город:</label>
                <input type="text" name="gorod" required>
            </div>
            
            <div class="form-group">
                <label>Возраст:</label>
                <input type="number" name="vozract" min="0" max="120" required>
            </div>
            
            <div class="form-group">
                <label>Пол:</label>
                <select name="pol" required>
                    <option value="м">Мужской</option>
                    <option value="ж">Женский</option>
                </select>
            </div>

            <div class="form-group">
                <label>Дата рождения:</label>
                <input type="Date" name="data_rojdeniaya" min="0" max="120" required>
            </div>

            <div class="form-group">
                <label>Место работы:</label>
                <input type="text" name="mesto_rad" min="0" max="120" required>
            </div>

            <div class="form-group">
                <label>Принадлежность:</label>
                <input type="text" name="prinad" min="0" max="120" required>
            </div>
            
            <button type="submit" class="btn">Добавить</button>
            <a href="index.php" class="btn cancel">Отмена</a>
        </form>
    </div>
</body>
</html>