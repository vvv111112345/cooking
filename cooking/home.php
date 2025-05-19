<?php
require_once 'config.php';
require_once 'functions.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$userId = (int)$_SESSION['user_id'];

// Получаем данные пользователя
$stmt = $pdo->prepare("SELECT username, name, avatar FROM users WHERE user_id = ?");
$stmt->execute([$userId]);
$user = $stmt->fetch();

// Обработка поискового запроса
$searchQuery = $_GET['search'] ?? '';
$categoryFilter = $_GET['category'] ?? '';

// Базовый запрос
$query = "
    SELECT r.*, c.category_name 
    FROM recipes r
    JOIN categories c ON r.category_id = c.category_id
    WHERE r.user_id = ?
";

$params = [$userId];

// Добавляем условия поиска
if (!empty($searchQuery)) {
    $query .= " AND (r.recipe_name LIKE ? OR r.description LIKE ?)";
    $searchParam = "%$searchQuery%";
    array_push($params, $searchParam, $searchParam);
}

// Добавляем фильтр по категории
if (!empty($categoryFilter) && is_numeric($categoryFilter)) {
    $query .= " AND r.category_id = ?";
    array_push($params, $categoryFilter);
}

$query .= " ORDER BY r.created_at DESC";

// Получаем рецепты
$recipesStmt = $pdo->prepare($query);
$recipesStmt->execute($params);
$recipes = $recipesStmt->fetchAll();

// Получаем все категории для фильтра
$categories = $pdo->query("SELECT * FROM categories ORDER BY category_name")->fetchAll();
?>
<!DOCTYPE html>
<html lang="ru">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Мои рецепты | Кулинарные скитания</title>
  <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;600;700&family=Playfair+Display:wght@700&display=swap" rel="stylesheet">
  <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
  <link rel="stylesheet" href="styles/home.css">
  <style>
    /* Временные стили, пока не подключите отдельный CSS файл */
    .material-icons {
      font-size: 18px;
      vertical-align: middle;
    }
  </style>
</head>
<body>
  <div class="dashboard-container">
    <!-- Боковая панель -->
    <aside class="sidebar">
      <div class="user-profile">
        <?php if (!empty($user['avatar'])): ?>
          <img src="<?= htmlspecialchars($user['avatar']) ?>" alt="Аватар" class="avatar-img">
        <?php else: ?>
          <div class="avatar"><?= strtoupper(substr($user['username'], 0, 1)) ?></div>
        <?php endif; ?>
        <h2><?= htmlspecialchars($user['name'] ?? $user['username']) ?></h2>
        <p class="username">@<?= htmlspecialchars($user['username']) ?></p>
      </div>
      
      <nav class="main-nav">
        <ul>
          <li class="active"><a href="home.php"><i class="material-icons">home</i> Мои рецепты</a></li>
          <li><a href="add_recipe.php"><i class="material-icons">add_circle</i> Добавить рецепт</a></li>
          <li><a href="categories.php"><i class="material-icons">category</i> Категории</a></li>
          <li><a href="profile.php"><i class="material-icons">person</i> Профиль</a></li>
          <li><a href="favorites.php"><i class="material-icons">favorite</i> Избранные рецепты</a></li>
          <li><a href="logout.php"><i class="material-icons">exit_to_app</i> Выход</a></li>
        </ul>
      </nav>
    </aside>

    <!-- Основное содержимое -->
    <main class="main-content">
      <header class="content-header">
        <h1>Мои рецепты</h1>
        <div class="header-actions">
          <form method="GET" action="home.php" class="search-form">
            <input type="text" name="search" placeholder="Поиск рецептов..." value="<?= htmlspecialchars($searchQuery) ?>">
            <select name="category">
              <option value="">Все категории</option>
              <?php foreach ($categories as $category): ?>
                <option value="<?= $category['category_id'] ?>" <?= $categoryFilter == $category['category_id'] ? 'selected' : '' ?>>
                  <?= htmlspecialchars($category['category_name']) ?>
                </option>
              <?php endforeach; ?>
            </select>
            <button type="submit" class="btn btn-primary">
              <i class="material-icons">search</i> Найти
            </button>
            <?php if (!empty($searchQuery) || !empty($categoryFilter)): ?>
              <a href="home.php" class="btn btn-outline">
                <i class="material-icons">refresh</i> Сбросить
              </a>
            <?php endif; ?>
          </form>

        </div>
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
                  <span><i class="material-icons">access_time</i> <?= $recipe['cook_time'] + $recipe['prep_time'] ?> мин</span>
                  <span><i class="material-icons">restaurant</i> <?= $recipe['servings'] ?> порц.</span>
                </div>
                <p class="recipe-description"><?= htmlspecialchars(substr($recipe['description'], 0, 100)) ?>...</p>
                <div class="recipe-actions">
                  <a href="view_recipe.php?id=<?= $recipe['recipe_id'] ?>" class="btn btn-outline">
                    <i class="material-icons">visibility</i> Подробнее
                  </a>
                  <a href="edit_recipe.php?id=<?= $recipe['recipe_id'] ?>" class="btn btn-edit">
                    <i class="material-icons">edit</i>
                  </a>
                </div>
              </div>
            </div>
          <?php endforeach; ?>
        <?php else: ?>
          <div class="empty-state">
            <img src="images/empty-recipes.svg" alt="Нет рецептов">
            <h3>Рецепты не найдены</h3>
            <p><?= !empty($searchQuery) ? 'По вашему запросу "'.htmlspecialchars($searchQuery).'" ничего не найдено.' : 'У вас пока нет рецептов.' ?></p>
            <a href="add_recipe.php" class="btn btn-primary">
              <i class="material-icons">add</i> Добавить рецепт
            </a>
            <?php if (!empty($searchQuery)): ?>
              <a href="home.php" class="btn btn-secondary">
                <i class="material-icons">list</i> Показать все рецепты
              </a>
            <?php endif; ?>
          </div>
        <?php endif; ?>
      </div>
    </main>
  </div>
</body>
</html>