<?php
require_once 'config.php';

// В начале файла (после require)
if (isset($_SESSION['user_id'])) {
    header("Location: home.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    
    try {
        $stmt = $pdo->prepare("INSERT INTO users (username, password, email) VALUES (?, ?, ?)");
        $stmt->execute([$username, $password, $email]);
        
        $_SESSION['user_id'] = $pdo->lastInsertId();
        $_SESSION['username'] = $username;
        header("Location: index.php");
        exit();
    } catch (PDOException $e) {
        $error = "Ошибка регистрации: " . $e->getMessage();
    }
}
?>
<div class="auth-container">
    <h2 class="auth-title">Регистрация</h2>
    
    <?php if (isset($error)): ?>
        <div class="auth-error">
            <?= htmlspecialchars($error) ?>
        </div>
    <?php endif; ?>
    
<?php
// Начало PHP-кода
?>

<?php
require_once 'config.php';

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
      
      <form action="auth.php" method="POST" class="auth-form">
        <input type="hidden" name="action" value="register">
        
        <div class="form-group">
          <label for="name">Имя</label>
          <input type="text" id="name" name="name" required placeholder="Ваше имя">
        </div>
        
        <div class="form-group">
          <label for="email">Email</label>
          <input type="email" id="email" name="email" required placeholder="ваш@email.com">
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