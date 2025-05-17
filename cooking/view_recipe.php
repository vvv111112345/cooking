<?php
require_once 'config.php';
session_start();

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: home.php");
    exit();
}

$recipeId = (int)$_GET['id'];

// Получаем данные рецепта
$stmt = $pdo->prepare("
    SELECT r.*, c.category_name, u.username 
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

// Получаем ингредиенты
$ingredientsStmt = $pdo->prepare("
    SELECT i.*, ri.quantity 
    FROM recipe_ingredients ri
    JOIN ingredients i ON ri.ingredient_id = i.ingredient_id
    WHERE ri.recipe_id = ?
");
$ingredientsStmt->execute([$recipeId]);
$ingredients = $ingredientsStmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="ru">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= htmlspecialchars($recipe['recipe_name']) ?> | Кулинарные скитания</title>
  <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;600;700&family=Playfair+Display:wght@700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="styles/recipe-view.css">
</head>
<body>
  <div class="recipe-container">
    <a href="home.php" class="back-link"><i class="icon-arrow-left"></i> Назад к рецептам</a>
    
    <article class="recipe-full">
      <header class="recipe-header">
        <h1><?= htmlspecialchars($recipe['recipe_name']) ?></h1>
        <div class="recipe-meta">
          <span>Категория: <?= htmlspecialchars($recipe['category_name']) ?></span>
          <span>Автор: <?= htmlspecialchars($recipe['username']) ?></span>
          <span><i class="icon-clock"></i> <?= ($recipe['prep_time'] + $recipe['cook_time']) ?> мин</span>
          <span><i class="icon-portion"></i> <?= $recipe['servings'] ?> порций</span>
        </div>
      </header>
      
      <div class="recipe-content">
        <div class="recipe-image">
          <img src="<?= htmlspecialchars($recipe['image_url'] ?? 'images/default-recipe-large.jpg') ?>" alt="<?= htmlspecialchars($recipe['recipe_name']) ?>">
        </div>
        
        <div class="recipe-details">
          <section class="ingredients-section">
            <h2><i class="icon-ingredients"></i> Ингредиенты</h2>
            <ul class="ingredients-list">
              <?php foreach ($ingredients as $ingredient): ?>
              <li>
                <span class="ingredient-name"><?= htmlspecialchars($ingredient['ingredient_name']) ?></span>
                <span class="ingredient-quantity"><?= $ingredient['quantity'] ?> <?= htmlspecialchars($ingredient['unit'] ?? '') ?></span>
              </li>
              <?php endforeach; ?>
            </ul>
          </section>
          
          <section class="instructions-section">
            <h2><i class="icon-instructions"></i> Способ приготовления</h2>
            <div class="instructions-text">
              <?= nl2br(htmlspecialchars($recipe['instructions'])) ?>
            </div>
          </section>
        </div>
      </div>
      
      <?php if (isset($_SESSION['user_id']) && $_SESSION['user_id'] == $recipe['user_id']): ?>
      <div class="recipe-actions">
        <a href="edit_recipe.php?id=<?= $recipeId ?>" class="btn btn-edit">
          <i class="icon-edit"></i> Редактировать
        </a>
      </div>
      <?php endif; ?>
    </article>
  </div>
</body>
</html>