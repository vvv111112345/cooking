<?php
require_once 'config.php';

// Если уже авторизован - сразу на главную
if (isset($_SESSION['user_id'])) {
    header("Location: home.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Кулинарный сайт | Главная</title>
    <link rel="stylesheet" href="css/style.css">
    <style>
        .hero {
            text-align: center;
            padding: 80px 20px;
        }
        .hero-buttons {
            display: flex;
            gap: 20px;
            justify-content: center;
            margin-top: 40px;
        }
        @media (max-width: 600px) {
            .hero-buttons {
                flex-direction: column;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <main class="hero">
            <h1>Добро пожаловать на кулинарные скитания!</h1>
            <p>Войдите или зарегистрируйтесь для доступа к рецептам</p>
            
            <div class="hero-buttons">
                <a href="login.php" class="btn btn-primary">Войти</a>
                <a href="register.php" class="btn btn-secondary">Регистрация</a>
            </div>
        </main>
    </div>
</body>
</html>