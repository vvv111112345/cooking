<?php
require_once 'config.php';

// В начале файла (после require)
if (isset($_SESSION['user_id'])) {
    header("Location: home.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];
    
    $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ?");
    $stmt->execute([$username]);
    $user = $stmt->fetch();
    
    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user_id'] = $user['user_id'];
        $_SESSION['username'] = $user['username'];
        header("Location: index.php");
        exit();
    } else {
        $error = "Неверное имя пользователя или пароль";
    }
}


?>

<div class="auth-container">
    <h2 class="auth-title">Вход в систему</h2>
    
    <?php if (isset($error)): ?>
        <div class="auth-error">
            <?= htmlspecialchars($error) ?>
        </div>
    <?php endif; ?>
    
<?php
// Начало PHP-кода (проверки, обработка формы и т.д.)
?>

<?php
require_once 'config.php';

// Если уже авторизован - редирект
if (isset($_SESSION['user_id'])) {
    header("Location: home.php");
    exit();
}
?>
<?php
require_once 'config.php';

// Если уже авторизован - редирект
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
  <title>Вход | Кулинарные скитания</title>
  <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;600;700&family=Playfair+Display:wght@700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="styles/auth.css">
</head>
<body>
  <div class="auth-container">
    <div class="auth-card">
      <div class="auth-header">
        <h1>Вход</h1>
        <p>Введите свои данные для доступа к рецептам</p>
      </div>
      
      <form action="auth.php" method="POST" class="auth-form">
        <input type="hidden" name="action" value="login">
        
        <div class="form-group">
          <label for="username">Имя пользователя</label>
          <input type="text" id="username" name="username" required placeholder="Ваше имя">
        </div>
        
        <div class="form-group">
          <label for="password">Пароль</label>
          <input type="password" id="password" name="password" required placeholder="••••••••">
        </div>
        
        <button type="submit" class="btn btn-primary">Войти</button>
      </form>
      
      <div class="auth-footer">
        <p>Ещё нет аккаунта? <a href="register.php">Зарегистрируйтесь</a></p>
      </div>
    </div>
  </div>
</body>
</html>