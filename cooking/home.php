<?php
require_once 'config.php';
require_once 'functions.php';
session_start();

// Проверка авторизации
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Получаем данные пользователя
$stmt = $pdo->prepare("SELECT username, name FROM users WHERE user_id = ?");
$stmt->execute([$_SESSION['user_id']]);
$user = $stmt->fetch();

// Получаем рецепты пользователя
$recipesStmt = $pdo->prepare("
    SELECT r.*, c.category_name 
    FROM recipes r
    JOIN categories c ON r.category_id = c.category_id
    WHERE r.user_id = ?
    ORDER BY r.created_at DESC
");
$recipesStmt->execute([$_SESSION['user_id']]);
$recipes = $recipesStmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="ru">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Мои рецепты | Кулинарные скитания</title>
  <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;600;700&family=Playfair+Display:wght@700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="styles/home.css">
</head>
<body>
  <div class="dashboard-container">
    <!-- Боковая панель -->
    <aside class="sidebar">
      <div class="user-profile">
        <div class="avatar"><?= strtoupper(substr($user['username'], 0, 1)) ?></div>
        <h2><?= htmlspecialchars($user['name'] ?? $user['username']) ?></h2>
        <p>@<?= htmlspecialchars($user['username']) ?></p>
      </div>
      
      <nav class="main-nav">
        <ul>
          <li class="active"><a href="home.php"><i class="icon-home"></i> Мои рецепты</a></li>
          <li><a href="add_recipe.php"><i class="icon-plus"></i> Добавить рецепт</a></li>
          <li><a href="categories.php"><i class="icon-tag"></i> Категории</a></li>
          <li><a href="profile.php"><i class="icon-user"></i> Профиль</a></li>
          <li><a href="favorites.php"><i class="icon-portion">Избранные рецепты</a></i>
          <li><a href="logout.php"><i class="icon-logout"></i> Выход</a></li>
        </ul>
      </nav>
    </aside>

    <!-- Основное содержимое -->
    <main class="main-content">
      <header class="content-header">
        <h1>Мои рецепты</h1>
        <a href="add_recipe.php" class="btn btn-primary">
          <i class="icon-plus"></i> Новый рецепт
        </a>
      </header>

      <div class="recipes-grid">
        <?php if (count($recipes) > 0): ?>
          <?php foreach ($recipes as $recipe): ?>
            <div class="recipe-card">
              <div class="recipe-image" style="background-image: url('<?= htmlspecialchars($recipe['image_url'] ?? 'images/default-recipe.jpg') ?>')">
                <div class="recipe-category"><?= htmlspecialchars($recipe['category_name']) ?></div>
              </div>
              <div class="recipe-body">
                <h3><?= htmlspecialchars($recipe['recipe_name']) ?></h3>
                <div class="recipe-meta">
                  <span><i class="icon-clock"></i> <?= $recipe['cook_time'] + $recipe['prep_time'] ?> мин</span>
                  <span><i class="icon-portion"></i> <?= $recipe['servings'] ?> порц.</span>
                </div>
                <p class="recipe-description"><?= htmlspecialchars(substr($recipe['description'], 0, 100)) ?>...</p>
                <div class="recipe-actions">
                  <a href="view_recipe.php?id=<?= $recipe['recipe_id'] ?>" class="btn btn-outline">Подробнее</a>
                  <a href="edit_recipe.php?id=<?= $recipe['recipe_id'] ?>" class="btn btn-edit"><i class="icon-edit"></i></a>
                </div>
              </div>
            </div>
          <?php endforeach; ?>
        <?php else: ?>
          <div class="empty-state">
            <img src="images/empty-recipes.svg" alt="Нет рецептов">
            <h3>У вас пока нет рецептов</h3>
            <p>Добавьте свой первый рецепт, нажав кнопку "Новый рецепт"</p>
            <a href="add_recipe.php" class="btn btn-primary">Создать рецепт</a>
          </div>
        <?php endif; ?>
      </div>
    </main>
  </div>
</body>
</html>


