<?php
require_once 'config.php';

// Включение отображения ошибок для отладки
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Проверка авторизации (с проверкой активной сессии)
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Проверка ID рецепта
if (!isset($_GET['id']) || !ctype_digit($_GET['id'])) {
    header("Location: index.php");
    exit();
}

$recipe_id = (int)$_GET['id'];

// Получение данных рецепта
$stmt = $pdo->prepare("
    SELECT r.*, c.category_name 
    FROM recipes r
    JOIN categories c ON r.category_id = c.category_id
    WHERE r.recipe_id = ?
");
$stmt->execute([$recipe_id]);
$recipe = $stmt->fetch();

if (!$recipe) {
    header("HTTP/1.0 404 Not Found");
    include '404.php';
    exit();
}




// Получение ингредиентов
$ingredients = $pdo->prepare("
    SELECT i.*, ri.quantity
    FROM recipe_ingredients ri
    JOIN ingredients i ON ri.ingredient_id = i.ingredient_id
    WHERE ri.recipe_id = ?
");
$ingredients->execute([$recipe_id]);
$ingredients = $ingredients->fetchAll();

// Получение комментариев
$comments = $pdo->prepare("
    SELECT c.*, u.username
    FROM comments c
    JOIN users u ON c.user_id = u.user_id
    WHERE c.recipe_id = ?
    ORDER BY c.created_at DESC
");
$comments->execute([$recipe_id]);
$comments = $comments->fetchAll();

// Проверка избранного
$isFavorite = false;
if (isset($_SESSION['user_id'])) {
    $stmt = $pdo->prepare("SELECT * FROM favorites WHERE user_id = ? AND recipe_id = ?");
    $stmt->execute([$_SESSION['user_id'], $recipe_id]);
    $isFavorite = $stmt->rowCount() > 0;
}

// Расчет пищевой ценности
$nutrition = $pdo->prepare("
    SELECT 
        SUM((i.calories * ri.quantity / 100)) AS total_calories,
        SUM((i.protein * ri.quantity / 100)) AS total_protein,
        SUM((i.fat * ri.quantity / 100)) AS total_fat,
        SUM((i.carbs * ri.quantity / 100)) AS total_carbs,
        r.servings
    FROM recipes r
    JOIN recipe_ingredients ri ON r.recipe_id = ri.recipe_id
    JOIN ingredients i ON ri.ingredient_id = i.ingredient_id
    WHERE r.recipe_id = ?
    GROUP BY r.recipe_id
");
$nutrition->execute([$recipe_id]);
$nutrition = $nutrition->fetch();

// Обработка добавления комментария
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['comment_text']) && isset($_SESSION['user_id'])) {
    $comment_text = trim($_POST['comment_text']);
    if (!empty($comment_text)) {
        $stmt = $pdo->prepare("INSERT INTO comments (user_id, recipe_id, comment_text) VALUES (?, ?, ?)");
        $stmt->execute([$_SESSION['user_id'], $recipe_id, $comment_text]);
        header("Location: recipe.php?id=" . $recipe_id);
        exit();
    }
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($recipe['recipe_name']) ?> | Кулинарный сайт</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="styles/recipe.css">
</head>
<body>
    <div class="container">
        <header>
            
            <nav>
                <?php if (isset($_SESSION['user_id'])): ?>
                     <?= htmlspecialchars($_SESSION['username']) ?></span>
                    <a href="index.php">На главную</a>
                    <a href="logout.php">Выйти</a>
                <?php else: ?>
                    <a href="login.php">Войти</a>
                    <a href="register.php">Регистрация</a>
                <?php endif; ?>
            </nav>
        </header>

        <main>
            <article class="recipe">
                <div class="recipe-header">
                    <h2><?= htmlspecialchars($recipe['recipe_name']) ?></h2>
                    
                    <?php if (!empty($recipe['image_url'])): ?>
                        <img src="<?= htmlspecialchars($recipe['image_url']) ?>" 
                             alt="<?= htmlspecialchars($recipe['recipe_name']) ?>"
                             class="recipe-image">
                    <?php endif; ?>
                    
                    <div class="recipe-meta">
                        <span><i class="fas fa-clock"></i> <?= ($recipe['prep_time'] + $recipe['cook_time']) ?> мин</span>
                        <span><i class="fas fa-utensils"></i> <?= $recipe['servings'] ?> порций</span>
                        <span><i class="fas fa-layer-group"></i> <?= htmlspecialchars($recipe['category_name']) ?></span>
                    </div>
                    
                    <?php if (!empty($recipe['description'])): ?>
                        <p class="recipe-description"><?= nl2br(htmlspecialchars($recipe['description'])) ?></p>
                    <?php endif; ?>
                    
                    <div class="recipe-actions">
                        <?php if (isset($_SESSION['user_id'])): ?>
                            <button onclick="toggleFavorite(<?= $recipe_id ?>, <?= $isFavorite ? 'true' : 'false' ?>)">
                                <?= $isFavorite ? '★ Удалить из избранного' : '☆ Добавить в избранное' ?>
                            </button>
                        <?php endif; ?>
                        
                        <?php if (isset($_SESSION['user_id']) && $_SESSION['user_id'] == $recipe['user_id']): ?>
                            <a href="edit_recipe.php?id=<?= $recipe_id ?>" class="btn-edit">
                                <i class="fas fa-edit"></i> Редактировать
                            </a>
                        <?php endif; ?>
                    </div>
                </div>
                
                <div class="recipe-details">
                    <section class="ingredients">
                        <h3><i class="fas fa-list"></i> Ингредиенты</h3>
                        <ul>
                            <?php foreach ($ingredients as $ingredient): ?>
                                <li>
                                    <?= htmlspecialchars($ingredient['ingredient_name']) ?> - 
                                    <?= $ingredient['quantity'] ?> 
                                    <?= htmlspecialchars($ingredient['unit'] ?? 'г') ?>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    </section>
                    
                    <section class="instructions">
                        <h3><i class="fas fa-list-ol"></i> Инструкции</h3>
                        <ol>
                            <?php 
                            $steps = array_filter(explode("\n", $recipe['instructions']));
                            foreach ($steps as $step): 
                            ?>
                                <li><?= nl2br(htmlspecialchars(trim($step))) ?></li>
                            <?php endforeach; ?>
                        </ol>
                    </section>
                </div>
                
                <?php if ($nutrition): ?>
                <div class="nutrition-info">
                    <h3><i class="fas fa-chart-pie"></i> Пищевая ценность (на порцию)</h3>
                    <ul>
                        <li>Калории: <?= round($nutrition['total_calories'] / $nutrition['servings']) ?> ккал</li>
                        <li>Белки: <?= round($nutrition['total_protein'] / $nutrition['servings'], 1) ?> г</li>
                        <li>Жиры: <?= round($nutrition['total_fat'] / $nutrition['servings'], 1) ?> г</li>
                        <li>Углеводы: <?= round($nutrition['total_carbs'] / $nutrition['servings'], 1) ?> г</li>
                    </ul>
                </div>
                <?php endif; ?>
            </article>
            
            <section class="comments">
                <h3><i class="fas fa-comments"></i> Комментарии</h3>
                
                <?php if (isset($_SESSION['user_id'])): ?>
                <form method="POST" class="comment-form">
                    <textarea name="comment_text" placeholder="Ваш комментарий..." required></textarea>
                    <button type="submit"><i class="fas fa-paper-plane"></i> Отправить</button>
                </form>
                <?php else: ?>
                <p class="auth-notice">Чтобы оставить комментарий, пожалуйста <a href="login.php">войдите</a> или <a href="register.php">зарегистрируйтесь</a>.</p>
                <?php endif; ?>
                
                <div class="comments-list">
                    <?php if (!empty($comments)): ?>
                        <?php foreach ($comments as $comment): ?>
                        <div class="comment">
                            <div class="comment-header">
                                <span class="comment-author"><?= htmlspecialchars($comment['username']) ?></span>
                                <span class="comment-date"><?= date('d.m.Y H:i', strtotime($comment['created_at'])) ?></span>
                            </div>
                            <div class="comment-text"><?= nl2br(htmlspecialchars($comment['comment_text'])) ?></div>
                        </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <p class="no-comments">Пока нет комментариев. Будьте первым!</p>
                    <?php endif; ?>
                </div>
            </section>
        </main>
        
        <footer>
            <p>&copy; <?= date('Y') ?> Кулинарный сайт. Все права защищены.</p>
        </footer>
    </div>

    <script>
function toggleFavorite(recipeId, isFavorite) {
    fetch(`favorites.php?action=${isFavorite ? 'remove' : 'add'}&recipe_id=${recipeId}`)
        .then(response => response.text())
        .then(data => {
            // Обновляем кнопку без перезагрузки страницы
            const btn = document.querySelector(`button[onclick*="${recipeId}"]`);
            if (btn) {
                btn.innerHTML = isFavorite 
                    ? '☆ Добавить в избранное' 
                    : '★ Удалить из избранного';
                btn.setAttribute('onclick', 
                    `toggleFavorite(${recipeId}, ${!isFavorite})`);
            }
            // Показываем уведомление
            const notification = document.createElement('div');
            notification.className = 'notification';
            notification.textContent = data;
            document.body.appendChild(notification);
            setTimeout(() => notification.remove(), 3000);
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Произошла ошибка');
        });
}
    </script>
</body>
</html>