<?php
require_once 'config.php';

// Если пользователь уже авторизован, перенаправляем на главную
if (isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Добро пожаловать | Кулинарные скитания</title>
    <link rel="stylesheet" href="style.css">
    <style>
      
    </style>
</head>
<body>
    <div class="container">
        <div class="welcome-container">
            <h1>Добро пожаловать на Кулинарный скитания!</h1>
            <p>Откройте для себя мир вкусных рецептов и кулинарных шедевров</p>
            
            <div class="welcome-buttons">
                <a href="login.php" class="btn btn-primary">Войти</a>
                <a href="register.php" class="btn btn-secondary">Регистрация</a>
            </div>
        </div>
    </div>
</body>
</html>