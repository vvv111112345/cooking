<?php
require_once 'config.php';

if (!isLoggedIn()) {
    header("Location: login.php");
    exit();
}

$recipeId = isset($_GET['id']) ? intval($_GET['id']) : 0;
$recipe = $recipeId ? getRecipe($recipeId) : null;

// Проверка прав доступа
if ($recipeId && !canEditRecipe($_SESSION['user_id'], $recipeId)) {
    header("HTTP/1.0 403 Forbidden");
    die("У вас нет прав для редактирования этого рецепта");
}

$categories = [];
try {
    $stmt = $pdo->query("SELECT * FROM categories");
    $categories = $stmt->fetchAll();
} catch (PDOException $e) {
    error_log("Error fetching categories: " . $e->getMessage());
}

// Обработка формы
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $name = trim($_POST['recipe_name'] ?? '');
        $categoryId = intval($_POST['category_id'] ?? 0);
        $description = trim($_POST['description'] ?? '');
        $instructions = trim($_POST['instructions'] ?? '');
        $prepTime = intval($_POST['prep_time'] ?? 0);
        $cookTime = intval($_POST['cook_time'] ?? 0);
        $servings = intval($_POST['servings'] ?? 1);
        
        // Валидация
        if (empty($name) || $categoryId <= 0 || empty($instructions)) {
            throw new Exception("Заполните обязательные поля");
        }
        
        if ($recipeId) {
            // Обновление рецепта
            $stmt = $pdo->prepare("UPDATE recipes SET 
                recipe_name = ?, 
                category_id = ?, 
                description = ?, 
                instructions = ?, 
                prep_time = ?, 
                cook_time = ?, 
                servings = ? 
                WHERE recipe_id = ?");
            $stmt->execute([
                $name, $categoryId, $description, $instructions, 
                $prepTime, $cookTime, $servings, $recipeId
            ]);
            
            $message = "Рецепт успешно обновлен!";
        } else {
            // Создание нового рецепта
            $stmt = $pdo->prepare("INSERT INTO recipes 
                (recipe_name, category_id, description, instructions, 
                 prep_time, cook_time, servings, user_id) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute([
                $name, $categoryId, $description, $instructions, 
                $prepTime, $cookTime, $servings, $_SESSION['user_id']
            ]);
            
            $recipeId = $pdo->lastInsertId();
            $message = "Рецепт успешно создан!";
        }
        
        // Обработка изображения
        if (isset($_FILES['recipe_image']) && $_FILES['recipe_image']['error'] === UPLOAD_ERR_OK) {
            validateImage($_FILES['recipe_image']);
            
            $extension = pathinfo($_FILES['recipe_image']['name'], PATHINFO_EXTENSION);
            $filename = 'recipe_' . uniqid() . '.' . $extension;
            $targetPath = UPLOAD_DIR . $filename;
            
            if (move_uploaded_file($_FILES['recipe_image']['tmp_name'], $targetPath)) {
                // Обновляем путь в базе данных
                $imageUrl = '/uploads/' . $filename;
                $stmt = $pdo->prepare("UPDATE recipes SET image_url = ? WHERE recipe_id = ?");
                $stmt->execute([$imageUrl, $recipeId]);
            }
        }
        
        $_SESSION['flash_message'] = $message;
        header("Location: recipe.php?id=" . $recipeId);
        exit();
    } catch (Exception $e) {
        $error = $e->getMessage();
    }
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $recipeId ? 'Редактирование' : 'Создание' ?> рецепта | Кулинарный сайт</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <header>
            <h1><?= $recipeId ? 'Редактирование рецепта' : 'Создание нового рецепта' ?></h1>
            <a href="<?= $recipeId ? 'recipe.php?id='.$recipeId : 'index.php' ?>" class="btn">
                <i class="fas fa-arrow-left"></i> Назад
            </a>
        </header>

        <main>
            <?php if (isset($error)): ?>
                <div class="alert alert-danger">
                    <i class="fas fa-exclamation-circle"></i> <?= htmlspecialchars($error) ?>
                </div>
            <?php endif; ?>

            <form method="post" enctype="multipart/form-data" class="recipe-form">
                <input type="hidden" name="recipe_id" value="<?= $recipeId ?>">
                
                <div class="form-group">
                    <label for="recipe_name"><i class="fas fa-utensils"></i> Название рецепта *</label>
                    <input type="text" id="recipe_name" name="recipe_name" 
                           value="<?= htmlspecialchars($recipe['recipe_name'] ?? '') ?>" required>
                </div>
                
                <div class="form-group">
                    <label for="category_id"><i class="fas fa-list"></i> Категория *</label>
                    <select id="category_id" name="category_id" required>
                        <option value="">-- Выберите категорию --</option>
                        <?php foreach ($categories as $category): ?>
                            <option value="<?= $category['category_id'] ?>" 
                                <?= ($recipe['category_id'] ?? '') == $category['category_id'] ? 'selected' : '' ?>>
                                <?= htmlspecialchars($category['category_name']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                // В секции с кнопками добавьте:
                    <div class="recipe-actions">
                        <!-- Кнопка сохранения -->
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Сохранить изменения
                        </button>
                        
                        <!-- Кнопка удаления (добавьте этот код) -->
                        <a href="delete_recipe.php?id=<?= $recipe['recipe_id'] ?>" 
                        class="btn btn-danger" 
                        id="delete-recipe-btn"
                        onclick="return confirmDelete()">
                            <i class="fas fa-trash-alt"></i> Удалить рецепт
                        </a>
                    </div>
                
                <div class="form-group">
                    <label for="description"><i class="fas fa-align-left"></i> Описание</label>
                    <textarea id="description" name="description" rows="3"><?= htmlspecialchars($recipe['description'] ?? '') ?></textarea>
                </div>
                
                <div class="form-group">
                    <label for="instructions"><i class="fas fa-list-ol"></i> Инструкции *</label>
                    <textarea id="instructions" name="instructions" rows="8" required><?= htmlspecialchars($recipe['instructions'] ?? '') ?></textarea>
                    <small class="form-hint">Каждый шаг с новой строки</small>
                </div>
                   </footer>
    </div>


                
                <div class="form-row">
                    <div class="form-group">
                        <label for="prep_time"><i class="fas fa-clock"></i> Время подготовки (мин)</label>
                        <input type="number" id="prep_time" name="prep_time" min="0" 
                               value="<?= htmlspecialchars($recipe['prep_time'] ?? '15') ?>">
                    </div>
                    
                    <div class="form-group">
                        <label for="cook_time"><i class="fas fa-fire"></i> Время готовки (мин)</label>
                        <input type="number" id="cook_time" name="cook_time" min="0" 
                               value="<?= htmlspecialchars($recipe['cook_time'] ?? '30') ?>">
                    </div>
                    
                    <div class="form-group">
                        <label for="servings"><i class="fas fa-users"></i> Количество порций</label>
                        <input type="number" id="servings" name="servings" min="1" 
                               value="<?= htmlspecialchars($recipe['servings'] ?? '2') ?>">
                    </div>
                </div>
                
                <div class="form-group">
                    <label for="recipe_image"><i class="fas fa-image"></i> Изображение рецепта</label>
                    <input type="file" id="recipe_image" name="recipe_image" accept="image/*">
                    <small class="form-hint">Допустимые форматы: JPG, PNG, GIF (макс. 2MB)</small>
                    
                    <?php if (!empty($recipe['image_url'])): ?>
                        <div class="current-image">
                            <p>Текущее изображение:</p>
                            <img src="<?= htmlspecialchars($recipe['image_url']) ?>" 
                                 alt="Текущее изображение рецепта" 
                                 style="max-width: 200px;">
                        </div>
                    <?php endif; ?>
                </div>
                
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> <?= $recipeId ? 'Обновить рецепт' : 'Создать рецепт' ?>
                </button>
            </form>
        </main>
    </div>
    <script>
function confirmDelete() {
    return confirm('Вы точно хотите удалить этот рецепт? Это действие нельзя отменить!');
}

// Альтернативный современный вариант
document.addEventListener('DOMContentLoaded', function() {
    const deleteBtn = document.getElementById('delete-recipe-btn');
    
    if (deleteBtn) {
        deleteBtn.addEventListener('click', function(e) {
            if (!confirm('Вы точно хотите удалить этот рецепт?')) {
                e.preventDefault();
            }
        });
    }
});
</script>
</body>
</html>