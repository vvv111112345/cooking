<?php
session_start();
require_once 'config.php';

// Проверка авторизации
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Обработка удаления из избранного
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['remove_recipe'])) {
    $recipe_id = (int)$_POST['remove_recipe'];
    $stmt = $pdo->prepare("DELETE FROM favorites WHERE user_id = ? AND recipe_id = ?");
    $stmt->execute([$_SESSION['user_id'], $recipe_id]);
    
    $_SESSION['flash_message'] = "Рецепт удалён из избранного";
    header("Location: favorites.php");
    exit();
}

// Получение избранных рецептов
$stmt = $pdo->prepare("
    SELECT r.*, c.category_name 
    FROM recipes r
    JOIN favorites f ON r.recipe_id = f.recipe_id
    JOIN categories c ON r.category_id = c.category_id
    WHERE f.user_id = ?
    ORDER BY r.recipe_name
");
$stmt->execute([$_SESSION['user_id']]);
$favorites = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Избранные рецепты | Кулинарный сайт</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="styles/favorites.css">
</head>
<body>
    <header>
        <div class="container">
            <h1><a href="index.php">Кулинарный сайт</a></h1>
            <nav>
                <div class="user-profile">
                    <div class="user-icon">
                        <i class="fas fa-user"></i>
                    </div>
                    <span class="username"><?= htmlspecialchars($_SESSION['username'] ?? '') ?></span>
                </div>
                <a href="index.php">Главная</a>
                <a href="logout.php">Выйти</a>
            </nav>
        </div>
    </header>

    <main class="container">
        <h2><i class="fas fa-heart"></i> Избранные рецепты</h2>
        
        <?php if (isset($_SESSION['flash_message'])): ?>
            <div class="flash-message">
                <?= $_SESSION['flash_message'] ?>
                <?php unset($_SESSION['flash_message']); ?>
            </div>
        <?php endif; ?>

        <?php if (empty($favorites)): ?>
            <div class="empty-state">
                <i class="fas fa-heart-broken"></i>
                <h3>Нет избранных рецептов</h3>
                <p>Вы пока не добавили ни одного рецепта в избранное. Найдите интересные рецепты на главной странице!</p>
                <a href="index.php" class="btn-primary">Перейти к рецептам</a>
            </div>
        <?php else: ?>
            <div class="favorites-grid">
                <?php foreach ($favorites as $recipe): ?>
                    <div class="recipe-card">
                        <?php if (!empty($recipe['image_url'])): ?>
                            <div class="recipe-image">
                                <img src="<?= htmlspecialchars($recipe['image_url']) ?>" alt="<?= htmlspecialchars($recipe['recipe_name']) ?>">
                            </div>
                        <?php endif; ?>
                        
                        <div class="recipe-content">
                            <h3><?= htmlspecialchars($recipe['recipe_name']) ?></h3>
                            
                            <div class="recipe-meta">
                                <span><i class="fas fa-clock"></i> <?= ($recipe['prep_time'] + $recipe['cook_time']) ?> мин</span>
                                <span><i class="fas fa-utensils"></i> <?= $recipe['servings'] ?> порций</span>
                                <span><i class="fas fa-layer-group"></i> <?= htmlspecialchars($recipe['category_name']) ?></span>
                            </div>
                            
                            <div class="recipe-actions">
                                <a href="recipe.php?id=<?= $recipe['recipe_id'] ?>" class="btn-view">
                                    <i class="fas fa-eye"></i> Посмотреть
                                </a>
                                <form method="POST" class="delete-form">
                                    <input type="hidden" name="remove_recipe" value="<?= $recipe['recipe_id'] ?>">
                                    <button type="submit" class="btn-delete">
                                        <i class="fas fa-trash-alt"></i>
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </main>

    <footer>
        <div class="container">
            <p>&copy; <?= date('Y') ?> Кулинарный сайт. Все права защищены.</p>
        </div>
    </footer>

    <script>
    // Подтверждение удаления
    document.querySelectorAll('.delete-form').forEach(form => {
        form.addEventListener('submit', function(e) {
            if (!confirm('Вы уверены, что хотите удалить рецепт из избранного?')) {
                e.preventDefault();
            }
        });
    });
    </script>
</body>
</html>