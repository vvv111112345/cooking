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

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Регистрация | Кулинарный сайт</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <div class="auth-container">
        <div class="auth-card">
            <div class="auth-header">
                <h2><i class="fas fa-user-plus"></i> Регистрация</h2>
                <p>Создайте новый аккаунт</p>
            </div>
            
            <?php if (isset($error)): ?>
                <div class="alert alert-danger">
                    <i class="fas fa-exclamation-circle"></i> <?= $error ?>
                </div>
            <?php endif; ?>
            
            <form method="POST" class="auth-form">
                <div class="form-group">
                    <label for="username"><i class="fas fa-user"></i> Имя пользователя</label>
                    <input type="text" id="username" name="username" class="form-control" placeholder="Придумайте логин" required>
                </div>
                
                <div class="form-group">
                    <label for="email"><i class="fas fa-envelope"></i> Email</label>
                    <input type="email" id="email" name="email" class="form-control" placeholder="Ваш email" required>
                </div>
                
                <div class="form-group">
                    <label for="password"><i class="fas fa-lock"></i> Пароль</label>
                    <input type="password" id="password" name="password" class="form-control" placeholder="Придумайте пароль" required>
                </div>
                
                <div class="form-group">
                    <label for="confirm_password"><i class="fas fa-lock"></i> Подтвердите пароль</label>
                    <input type="password" id="confirm_password" name="confirm_password" class="form-control" placeholder="Повторите пароль" required>
                </div>
                
                <button type="submit" class="btn btn-primary btn-block">
                    <i class="fas fa-user-plus"></i> Зарегистрироваться
                </button>
                
                <div class="auth-footer text-center">
                    <p>Уже есть аккаунт? <a href="login.php" class="auth-link">Войдите</a></p>
                </div>
            </form>
        </div>
    </div>
</body>
</html>