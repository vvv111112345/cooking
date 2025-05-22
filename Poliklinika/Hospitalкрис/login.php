<?php
require_once 'includes/config.php';
require_once 'includes/db.php';

if($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    try {
        $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ?");
        $stmt->execute([$username]);
        $user = $stmt->fetch();

        if($user && password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['role'] = $user['role'];

            header('Location: index.php');
            exit;
        } else {
            $_SESSION['error_message'] = "Неверное имя пользователя или пароль";
        }
    } catch (PDOException $e) {
        $_SESSION['error_message'] = "Ошибка входа: " . $e->getMessage();
    }
}

require_once 'includes/header.php';
?>

<h2>Вход в систему</h2>

<?php if(isset($_SESSION['error_message'])): ?>
    <div class="alert alert-danger">
        <?php echo $_SESSION['error_message']; unset($_SESSION['error_message']); ?>
    </div>
<?php endif; ?>

<form action="login.php" method="post">
    <div class="form-group">
        <label for="username">Имя пользователя:</label>
        <input type="text" id="username" name="username" required>
    </div>

    <div class="form-group">
        <label for="password">Пароль:</label>
        <input type="password" id="password" name="password" required>
    </div>

    <button type="submit" class="btn btn-primary">Войти</button>
</form>

<p>Ещё нет аккаунта? <a href="register.php">Зарегистрируйтесь</a></p>

<?php
require_once 'includes/footer.php';
?>