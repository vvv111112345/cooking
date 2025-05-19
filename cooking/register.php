<?php
require_once 'config.php';
session_start();

if (isset($_SESSION['user_id'])) {
    header("Location: home.php");
    exit();
}

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $email = $_POST['email'] ?? null;
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    
    if ($password !== $confirm_password) {
        $error = "Пароли не совпадают";
    } else {
        try {
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("INSERT INTO users (username, password, email) VALUES (?, ?, ?)");
            $stmt->execute([$username, $hashed_password, $email]);
            
            $_SESSION['user_id'] = $pdo->lastInsertId();
            $_SESSION['username'] = $username;
            header("Location: home.php");
            exit();
        } catch (PDOException $e) {
            $error = "Ошибка регистрации: " . (strpos($e->getMessage(), 'Duplicate entry') !== false ? 
                   "Пользователь с таким именем уже существует" : "Попробуйте позже");
        }
    }
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Регистрация | Кулинарные скитания</title>
  <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;600;700&family=Playfair+Display:wght@700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="styles/auth.css">
</head>
<body>
  <div class="auth-container">
    <div class="auth-card">
      <div class="auth-header">
        <h1>Регистрация</h1>
        <p>Создайте аккаунт для сохранения рецептов</p>
      </div>
      
      <?php if (!empty($error)): ?>
        <div class="auth-error"><?= htmlspecialchars($error) ?></div>
      <?php endif; ?>
      
      <form method="POST" class="auth-form">
        <div class="form-group">
          <label for="username">Имя пользователя</label>
          <input type="text" id="username" name="username" required placeholder="Ваше имя">
        </div>
        
        <div class="form-group">
          <label for="email">Email (необязательно)</label>
          <input type="email" id="email" name="email" placeholder="ваш@email.com">
        </div>
        
        <div class="form-group">
          <label for="password">Пароль</label>
          <input type="password" id="password" name="password" required placeholder="••••••••">
        </div>
        
        <div class="form-group">
          <label for="confirm_password">Подтвердите пароль</label>
          <input type="password" id="confirm_password" name="confirm_password" required placeholder="••••••••">
        </div>
        
        <button type="submit" class="btn btn-primary">Зарегистрироваться</button>
      </form>
      
      <div class="auth-footer">
        <p>Уже есть аккаунт? <a href="login.php">Войдите</a></p>
      </div>
    </div>
  </div>
</body>
</html>