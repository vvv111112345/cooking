<?php
require_once 'config.php';
require_once 'functions.php';
session_start();

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: home.php");
    exit();
}

$recipeId = (int)$_GET['id'];
$userId = isset($_SESSION['user_id']) ? (int)$_SESSION['user_id'] : 0;

try {
    // Получаем данные рецепта
    $stmt = $pdo->prepare("
        SELECT r.*, c.category_name, u.username, u.avatar as author_avatar
        FROM recipes r
        JOIN categories c ON r.category_id = c.category_id
        JOIN users u ON r.user_id = u.user_id
        WHERE r.recipe_id = ?
    ");
    $stmt->execute([$recipeId]);
    $recipe = $stmt->fetch();

    if (!$recipe) {
        header("Location: home.php");
        exit();
    }

    // Получаем ингредиенты (убрана сортировка по ingredient_order)
    $ingredientsStmt = $pdo->prepare("
        SELECT i.*, ri.quantity 
        FROM recipe_ingredients ri
        JOIN ingredients i ON ri.ingredient_id = i.ingredient_id
        WHERE ri.recipe_id = ?
    ");
    $ingredientsStmt->execute([$recipeId]);
    $ingredients = $ingredientsStmt->fetchAll();

    // Получаем данные текущего пользователя
    if ($userId) {
        $userStmt = $pdo->prepare("SELECT username, avatar FROM users WHERE user_id = ?");
        $userStmt->execute([$userId]);
        $currentUser = $userStmt->fetch();
    }
} catch (PDOException $e) {
    die("Ошибка базы данных: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= htmlspecialchars($recipe['recipe_name']) ?> | Кулинарные скитания</title>
  <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;600;700&family=Playfair+Display:wght@700&display=swap" rel="stylesheet">
  <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
  <link rel="stylesheet" href="styles/recipe-view.css">
</head>
<body>
  <div class="dashboard-container">
    <!-- Боковая панель -->
    <?php if ($userId): ?>
    <aside class="sidebar">
      <div class="user-profile">
        <?php if (!empty($currentUser['avatar'])): ?>
          <img src="<?= htmlspecialchars($currentUser['avatar']) ?>" alt="Аватар" class="avatar-img">
        <?php else: ?>
          <div class="avatar"><?= strtoupper(substr($currentUser['username'], 0, 1)) ?></div>
        <?php endif; ?>
        <h2><?= htmlspecialchars($currentUser['username']) ?></h2>
      </div>
      
      <nav class="main-nav">
        <ul>
          <li><a href="home.php"><i class="material-icons">home</i> Мои рецепты</a></li>
          <li><a href="add_recipe.php"><i class="material-icons">add_circle</i> Добавить рецепт</a></li>
          <li><a href="categories.php"><i class="material-icons">category</i> Категории</a></li>
          <li><a href="profile.php"><i class="material-icons">person</i> Профиль</a></li>
          <li><a href="favorites.php"><i class="material-icons">favorite</i> Избранные</a></li>
          <li><a href="logout.php"><i class="material-icons">exit_to_app</i> Выход</a></li>
        </ul>
      </nav>
    </aside>
    <?php endif; ?>

    <!-- Основное содержимое -->
    <main class="main-content recipe-view-content">
      <a href="home.php" class="btn btn-outline back-btn">
        <i class="material-icons">arrow_back</i> Назад к рецептам
      </a>
      
      <article class="recipe-full">
        <header class="recipe-header">
          <div class="recipe-category-badge">
            <?= htmlspecialchars($recipe['category_name']) ?>
          </div>
          <h1><?= htmlspecialchars($recipe['recipe_name']) ?></h1>
          
          <div class="recipe-author">
            <?php if (!empty($recipe['author_avatar'])): ?>
              <img src="<?= htmlspecialchars($recipe['author_avatar']) ?>" alt="Аватар автора" class="author-avatar">
            <?php else: ?>
              <div class="author-avatar-initial">
                <?= strtoupper(substr($recipe['username'], 0, 1)) ?>
              </div>
            <?php endif; ?>
            <span>@<?= htmlspecialchars($recipe['username']) ?></span>
          </div>
          
          <div class="recipe-meta">
            <div class="meta-item">
              <i class="material-icons">access_time</i>
              <span><?= ($recipe['prep_time'] + $recipe['cook_time']) ?> мин</span>
            </div>
            <div class="meta-item">
              <i class="material-icons">restaurant</i>
              <span><?= $recipe['servings'] ?> порций</span>
            </div>
          </div>
        </header>
        
        <div class="recipe-content">
          <div class="recipe-image-container">
            <img src="<?= htmlspecialchars($recipe['image_url'] ?? 'images/default-recipe-large.jpg') ?>" 
                 alt="<?= htmlspecialchars($recipe['recipe_name']) ?>" 
                 class="recipe-main-image">
          </div>
          
          <div class="recipe-details-grid">
            <section class="ingredients-section">
              <h2><i class="material-icons">list_alt</i> Ингредиенты</h2>
              <ul class="ingredients-list">
                <?php foreach ($ingredients as $ingredient): ?>
                <li class="ingredient-item">
                  <span class="ingredient-name"><?= htmlspecialchars($ingredient['ingredient_name']) ?></span>
                  <span class="ingredient-quantity">
                    <?= $ingredient['quantity'] ?> <?= htmlspecialchars($ingredient['unit'] ?? '') ?>
                  </span>
                </li>
                <?php endforeach; ?>
              </ul>
            </section>
            
            <section class="instructions-section">
              <h2><i class="material-icons">menu_book</i> Способ приготовления</h2>
              <div class="instructions-text">
                <?= nl2br(htmlspecialchars($recipe['instructions'])) ?>
              </div>
            </section>
          </div>
        </div>
        
        <?php if ($userId && $userId == $recipe['user_id']): ?>
        <div class="recipe-actions">
          <a href="edit_recipe.php?id=<?= $recipeId ?>" class="btn btn-primary">
            <i class="material-icons">edit</i> Редактировать рецепт
          </a>
        </div>
        <?php endif; ?>
      </article>
    </main>
  </div>
</body>
</html>