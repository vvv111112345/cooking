<?php
require_once 'config.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$userId = (int)$_SESSION['user_id'];

// Исправленный запрос к базе данных
$stmt = $pdo->prepare("SELECT * FROM users WHERE user_id = ?");
$stmt->execute([$userId]);
$user = $stmt->fetch();

if (!$user) {
    $_SESSION['error'] = "Пользователь не найден";
    header("Location: home.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Обработка обновления профиля
    $name = $_POST['name'] ?? '';
    $email = $_POST['email'] ?? '';
    $password = !empty($_POST['password']) ? password_hash($_POST['password'], PASSWORD_DEFAULT) : null;
    
    try {
        if ($password) {
            $stmt = $pdo->prepare("UPDATE users SET name = ?, email = ?, password = ? WHERE user_id = ?");
            $stmt->execute([$name, $email, $password, $userId]);
        } else {
            $stmt = $pdo->prepare("UPDATE users SET name = ?, email = ? WHERE user_id = ?");
            $stmt->execute([$name, $email, $userId]);
        }
        
        $_SESSION['success'] = "Профиль успешно обновлен";
        header("Location: profile.php");
        exit();
    } catch (PDOException $e) {
        $_SESSION['error'] = "Ошибка при обновлении профиля: " . $e->getMessage();
    }
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Мой профиль | Кулинарные скитания</title>
  <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;600;700&family=Playfair+Display:wght@700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="styles/profile.css">
</head>
<body>
  <div class="dashboard-container">
    <!-- Боковая панель (как в home.php) -->
    
    <main class="main-content">
      <header class="content-header">
        <h1>Мой профиль</h1>
      </header>

      <?php if (isset($_SESSION['error'])): ?>
        <div class="alert alert-error">
          <?= $_SESSION['error'] ?>
          <?php unset($_SESSION['error']); ?>
        </div>
      <?php endif; ?>
      
      <?php if (isset($_SESSION['success'])): ?>
        <div class="alert alert-success">
          <?= $_SESSION['success'] ?>
          <?php unset($_SESSION['success']); ?>
        </div>
      <?php endif; ?>

      <div class="profile-card">
        <div class="avatar-large"><?= strtoupper(substr($user['username'], 0, 1)) ?></div>
        
        <form action="profile.php" method="POST" class="profile-form">
          <div class="form-group">
            <label for="username">Логин</label>
            <input type="text" id="username" value="<?= htmlspecialchars($user['username']) ?>" disabled>
          </div>
          
          <div class="form-group">
            <label for="name">Имя</label>
            <input type="text" id="name" name="name" value="<?= htmlspecialchars($user['name'] ?? '') ?>">
          </div>
          
          <div class="form-group">
            <label for="email">Email</label>
            <input type="email" id="email" name="email" value="<?= htmlspecialchars($user['email'] ?? '') ?>">
          </div>
          
          <div class="form-group">
            <label for="password">Новый пароль (оставьте пустым, если не меняется)</label>
            <input type="password" id="password" name="password">
          </div>
          
          <button type="submit" class="btn btn-primary">Сохранить изменения</button>
        </form>
      </div>
    </main>
  </div>
</body>
</html>