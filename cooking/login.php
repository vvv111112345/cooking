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

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Вход | Кулинарный сайт</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <div class="auth-container">
        <div class="auth-card">
            <div class="auth-header">
                <h2><i class="fas fa-sign-in-alt"></i> Вход в систему</h2>
                <p>Введите свои учетные данные</p>
            </div>
            
            <?php if (isset($error)): ?>
                <div class="alert alert-danger">
                    <i class="fas fa-exclamation-circle"></i> <?= $error ?>
                </div>
            <?php endif; ?>
            
            <form method="POST" class="auth-form">
                <div class="form-group">
                    <label for="username"><i class="fas fa-user"></i> Логин</label>
                    <input type="text" id="username" name="username" class="form-control" placeholder="Введите ваш логин" required>
                </div>
                
                <div class="form-group">
                    <label for="password"><i class="fas fa-lock"></i> Пароль</label>
                    <input type="password" id="password" name="password" class="form-control" placeholder="Введите ваш пароль" required>
                </div>
                
                <div class="form-group form-check">
                    <input type="checkbox" id="remember" name="remember" class="form-check-input">
                    <label for="remember" class="form-check-label">Запомнить меня</label>
                </div>
                
                <button type="submit" class="btn btn-primary btn-block">
                    <i class="fas fa-sign-in-alt"></i> Войти
                </button>
                
                <div class="auth-footer">
                    <a href="register.php" class="auth-link">
                        <i class="fas fa-user-plus"></i> Регистрация
                    </a>
                    <a href="forgot_password.php" class="auth-link">
                        <i class="fas fa-key"></i> Забыли пароль?
                    </a>
                </div>
            </form>
        </div>
    </div>
</body>
</html>