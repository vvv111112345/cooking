<?php
require_once 'config.php';
require_once 'functions.php';

if (!isLoggedIn()) {
    header("Location: login.php");
    exit();
}

// Получаем категории и проверяем результат
$categories = getCategories();
if ($categories === false) {
    die("Ошибка при загрузке категорий");
}
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Главная | Кулинарные скитания</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <div class="container">
        <header>
            <h1>Кулинарные скитания</h1>
            <div class="user-panel">
                <a href="add_recipe.php" class="btn btn-primary">Добавить рецепт</a>
                <span>Привет, <?= htmlspecialchars($_SESSION['username'] ?? 'Гость') ?></span>
                <a href="logout.php" class="btn btn-danger">Выйти</a>
            </div>
        </header>

        <main>
            <h2>Категории рецептов</h2>
            
            <?php if (!empty($categories)): ?>
                <?php foreach ($categories as $category): ?>
                    <div class="category category-<?= $category['category_id'] ?>">
                        <h3><?= htmlspecialchars($category['category_name']) ?></h3>
                        <?php if (!empty($category['description'])): ?>
                            <p><?= htmlspecialchars($category['description']) ?></p>
                        <?php endif; ?>
                        
                        <h4>Рецепты:</h4>
                        <div class="recipes">
                            <?php 
                            $recipes = getRecipesByCategory($category['category_id']);
                            if (!empty($recipes)): 
                                foreach ($recipes as $recipe): 
                            ?>
                                <div class="recipe-card">
                                    <h5><?= htmlspecialchars($recipe['recipe_name']) ?></h5>
                                    <div class="recipe-actions">
                                        <a href="recipe.php?id=<?= $recipe['recipe_id'] ?>" 
                                           class="btn btn-view">
                                           <i class="fas fa-book-open"></i> Посмотреть
                                        </a>
                                        <?php if (canEditRecipe($_SESSION['user_id'], $recipe['recipe_id'])): ?>
                                            <a href="edit_recipe.php?id=<?= $recipe['recipe_id'] ?>" 
                                               class="btn btn-edit">
                                               <i class="fas fa-edit"></i> Редактировать
                                            </a>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            <?php 
                                endforeach;
                            else: 
                            ?>
                                <p class="no-recipes">В этой категории пока нет рецептов</p>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p class="no-categories">Категории не найдены</p>
            <?php endif; ?>
        </main>

        <footer>
            <p>&copy; <?= date('Y') ?> Кулинарный сайт</p>
        </footer>
    </div>
</body>
</html>