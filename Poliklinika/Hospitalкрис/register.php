<?php
require_once 'includes/config.php';
require_once 'includes/db.php';

if($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);
    $password_confirm = trim($_POST['password_confirm']);
    $role = 'staff'; // По умолчанию регистрируем как сотрудника

    // Валидация
    if($password !== $password_confirm) {
        $_SESSION['error_message'] = "Пароли не совпадают";
    } else {
        try {
            // Проверка существующего пользователя
            $stmt = $pdo->prepare("SELECT id FROM users WHERE username = ? OR email = ?");
            $stmt->execute([$username, $email]);
            
            if($stmt->rowCount() > 0) {
                $_SESSION['error_message'] = "Пользователь с таким именем или email уже существует";
            } else {
                $hashed_password = password_hash($password, PASSWORD_DEFAULT);
                
                $stmt = $pdo->prepare("INSERT INTO users (username, email, password, role) VALUES (?, ?, ?, ?)");
                $stmt->execute([$username, $email, $hashed_password, $role]);
                
                $_SESSION['success_message'] = "Регистрация успешна! Теперь вы можете войти.";
                header('Location: login.php');
                exit;
            }
        } catch (PDOException $e) {
            $_SESSION['error_message'] = "Ошибка регистрации: " . $e->getMessage();
        }
    }
}

require_once 'includes/header.php';
?>

<h2>Регистрация</h2>

<?php if(isset($_SESSION['error_message'])): ?>
    <div class="alert alert-danger">
        <?php echo $_SESSION['error_message']; unset($_SESSION['error_message']); ?>
    </div>
<?php endif; ?>

<form action="register.php" method="post">
    <div class="form-group">
        <label for="username">Имя пользователя:</label>
        <input type="text" id="username" name="username" required>
    </div>
    
    <div class="form-group">
        <label for="email">Email:</label>
        <input type="email" id="email" name="email" required>
    </div>
    
    <div class="form-group">
        <label for="password">Пароль:</label>
        <input type="password" id="password" name="password" required>
    </div>
    
    <div class="form-group">
        <label for="password_confirm">Подтверждение пароля:</label>
        <input type="password" id="password_confirm" name="password_confirm" required>
    </div>
    
    <button type="submit" class="btn btn-primary">Зарегистрироваться</button>
</form>

<p>Уже есть аккаунт? <a href="login.php">Войдите</a></p>

<?php
require_once 'includes/footer.php';
?>