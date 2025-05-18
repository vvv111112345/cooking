<?php
require_once 'config.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$userId = (int)$_SESSION['user_id'];

// Запрос для получения данных пользователя с подсчетом рецептов и подписчиков
$stmt = $pdo->prepare("
    SELECT 
        u.*,
        (SELECT COUNT(*) FROM recipes WHERE user_id = u.user_id) AS recipe_count,
        (SELECT COUNT(*) FROM followers WHERE following_id = u.user_id) AS follower_count
    FROM users u
    WHERE u.user_id = ?
");
$stmt->execute([$userId]);
$user = $stmt->fetch();

if (!$user) {
    $_SESSION['error'] = "Пользователь не найден";
    header("Location: home.php");
    exit();
}

// Обработка формы
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save_profile'])) {
    $name = $_POST['name'] ?? '';
    $email = $_POST['email'] ?? '';
    $password = !empty($_POST['password']) ? password_hash($_POST['password'], PASSWORD_DEFAULT) : null;
    
    // Обработка загрузки аватара
    $avatarPath = $user['avatar'] ?? 'images/default-avatar.jpg';
    
    if (isset($_FILES['avatar']) && $_FILES['avatar']['error'] === UPLOAD_ERR_OK) {
        $uploadDir = 'uploads/avatars/';
        if (!file_exists($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }
        
        $fileExt = strtolower(pathinfo($_FILES['avatar']['name'], PATHINFO_EXTENSION));
        $allowedTypes = ['jpg', 'jpeg', 'png', 'gif'];
        
        if (in_array($fileExt, $allowedTypes)) {
            $fileName = 'avatar_' . $userId . '_' . time() . '.' . $fileExt;
            $targetPath = $uploadDir . $fileName;
            
            if (move_uploaded_file($_FILES['avatar']['tmp_name'], $targetPath)) {
                // Удаляем старый аватар, если он не дефолтный
                if (!empty($user['avatar']) && $user['avatar'] !== 'images/default-avatar.jpg' && file_exists($user['avatar'])) {
                    unlink($user['avatar']);
                }
                $avatarPath = $targetPath;
            }
        }
    }
    
    try {
        if ($password) {
            $stmt = $pdo->prepare("UPDATE users SET name = ?, email = ?, password = ?, avatar = ? WHERE user_id = ?");
            $stmt->execute([$name, $email, $password, $avatarPath, $userId]);
        } else {
            $stmt = $pdo->prepare("UPDATE users SET name = ?, email = ?, avatar = ? WHERE user_id = ?");
            $stmt->execute([$name, $email, $avatarPath, $userId]);
        }
        
        $_SESSION['success'] = "Профиль успешно обновлен";
        header("Location: profile.php");
        exit();
    } catch (PDOException $e) {
        $_SESSION['error'] = "Ошибка при обновлении профиля: " . $e->getMessage();
        header("Location: profile.php");
        exit();
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
    <!-- Боковая панель -->
    
    <div class="container">
        <?php if (isset($_SESSION['error'])): ?>
            <div class="alert alert-danger"><?= $_SESSION['error'] ?></div>
            <?php unset($_SESSION['error']); ?>
        <?php endif; ?>
        
        <?php if (isset($_SESSION['success'])): ?>
            <div class="alert alert-success"><?= $_SESSION['success'] ?></div>
            <?php unset($_SESSION['success']); ?>
        <?php endif; ?>
        
        <div class="profile-header">
            <img src="<?= htmlspecialchars($user['avatar'] ?? 'images/default-avatar.jpg') ?>" alt="Аватар" class="profile-avatar">
            <div class="profile-info">
                <h2><?= htmlspecialchars($user['username']) ?></h2>
                <div class="profile-stats">
                    <div class="profile-stat">
                        <span><?= (int)($user['recipe_count'] ?? 0) ?></span> рецептов
                    </div>
                    <div class="profile-stat">
                        <span><?= (int)($user['follower_count'] ?? 0) ?></span> подписчиков
                    </div>
                </div>
            </div>
        </div>
        
        <div class="edit-profile-form">
            <h2>Редактировать профиль</h2>
            <form method="POST" enctype="multipart/form-data">
                <div class="form-group">
                    <label for="name">Имя</label>
                    <input type="text" id="name" name="name" value="<?= htmlspecialchars($user['name'] ?? '') ?>">
                </div>
                
                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" id="email" name="email" value="<?= htmlspecialchars($user['email'] ?? '') ?>">
                </div>
                
                <div class="form-group">
                    <label for="avatar">Аватар</label>
                    <input type="file" id="avatar" name="avatar" accept="image/*">
                </div>
                
                <div class="form-group">
                    <label for="password">Новый пароль</label>
                    <input type="password" id="password" name="password">
                </div>
                
                <div class="form-actions">
                    <button type="submit" name="save_profile" class="btn btn-primary">Сохранить</button>
                </div>
            </form>
        </div>
        
        <h3 class="section-title">Мои рецепты</h3>
        <div class="user-recipes">
            <?php
            $recipesStmt = $pdo->prepare("SELECT * FROM recipes WHERE user_id = ? ORDER BY created_at DESC");
            $recipesStmt->execute([$userId]);
            $recipes = $recipesStmt->fetchAll();
            
            if (count($recipes) > 0): ?>
                <?php foreach ($recipes as $recipe): ?>
                    <div class="recipe-card">
                        <h3><?= htmlspecialchars($recipe['recipe_name']) ?></h3>
                        <p><?= htmlspecialchars($recipe['description']) ?></p>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p>У вас пока нет рецептов.</p>
            <?php endif; ?>
        </div>
    </div>
  </div>
</body>
</html>