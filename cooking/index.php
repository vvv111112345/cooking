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
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Кулинарные скитания</title>
  <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;600;700&family=Playfair+Display:wght@700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="styles/welcome.css">
</head>
<body>
  <div class="welcome-container">
    <div class="welcome-card">
      <h1 class="welcome-title">Добро пожаловать на <span>Кулинарные скитания</span></h1>
      <p class="welcome-subtitle">Откройте мир вкусных рецептов и кулинарных экспериментов</p>
      
      <div class="auth-buttons">
        <a href="login.php" class="btn btn-primary">Войти</a>
        <a href="register.php" class="btn btn-secondary">Регистрация</a>
      </div>
      
      <div class="welcome-image">
        <img src="images/cooking-hero.jpg" alt="Кулинарные ингредиенты">
      </div>
    </div>
  </div>
</body>
</html>