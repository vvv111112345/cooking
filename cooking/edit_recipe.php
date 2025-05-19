<?php
session_start();

// Подключение конфигурации с абсолютным путем
require_once __DIR__ . '/config.php';

// Проверка авторизации
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Инициализация переменных
$recipeId = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$recipe = null;
$error = '';
$message = '';

// Загрузка данных рецепта
if ($recipeId > 0) {
    try {
        $stmt = $pdo->prepare("SELECT * FROM recipes WHERE recipe_id = ?");
        $stmt->execute([$recipeId]);
        $recipe = $stmt->fetch(PDO::FETCH_ASSOC);
        
        // Проверка прав доступа
        if ($recipe && $recipe['user_id'] != $_SESSION['user_id']) {
            header("HTTP/1.0 403 Forbidden");
            die("У вас нет прав для редактирования этого рецепта");
        }
    } catch (PDOException $e) {
        error_log("Ошибка при загрузке рецепта: " . $e->getMessage());
        $error = "Ошибка при загрузке данных рецепта";
    }
}

// Загрузка категорий
$categories = [];
try {
    $stmt = $pdo->query("SELECT * FROM categories");
    $categories = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    error_log("Ошибка при загрузке категорий: " . $e->getMessage());
    $error = "Ошибка при загрузке категорий";
}

// Обработка формы
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        // Валидация данных
        $name = trim($_POST['recipe_name'] ?? '');
        $categoryId = (int)($_POST['category_id'] ?? 0);
        $description = trim($_POST['description'] ?? '');
        $instructions = trim($_POST['instructions'] ?? '');
        $prepTime = (int)($_POST['prep_time'] ?? 0);
        $cookTime = (int)($_POST['cook_time'] ?? 0);
        $servings = (int)($_POST['servings'] ?? 1);
        
        if (empty($name) || $categoryId <= 0 || empty($instructions)) {
            throw new Exception("Заполните обязательные поля: название, категория и инструкции");
        }

        // Обработка изображения
        $imageUrl = $recipe['image_url'] ?? '';
        if (isset($_FILES['recipe_image']) && $_FILES['recipe_image']['error'] === UPLOAD_ERR_OK) {
            // Проверка размера файла
            if ($_FILES['recipe_image']['size'] > MAX_FILE_SIZE) {
                throw new Exception("Размер файла превышает допустимый лимит");
            }
            
            // Проверка типа файла
            $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
            $fileType = mime_content_type($_FILES['recipe_image']['tmp_name']);
            if (!in_array($fileType, $allowedTypes)) {
                throw new Exception("Недопустимый тип файла");
            }
            
            // Генерация имени файла
            $extension = pathinfo($_FILES['recipe_image']['name'], PATHINFO_EXTENSION);
            $filename = 'recipe_' . $recipeId . '_' . time() . '.' . $extension;
            $targetPath = UPLOAD_DIR . $filename;
            
            // Сохранение файла
            if (move_uploaded_file($_FILES['recipe_image']['tmp_name'], $targetPath)) {
                $imageUrl = '/uploads/' . $filename;
                
                // Удаление старого изображения
                if (!empty($recipe['image_url'])) {
                    $oldImage = str_replace('/uploads/', UPLOAD_DIR, $recipe['image_url']);
                    if (file_exists($oldImage)) {
                        unlink($oldImage);
                    }
                }
            }
        }

        // Сохранение в БД
        if ($recipeId > 0) {
            // Обновление рецепта
            $stmt = $pdo->prepare("UPDATE recipes SET 
                recipe_name = ?, 
                category_id = ?, 
                description = ?, 
                instructions = ?, 
                prep_time = ?, 
                cook_time = ?, 
                servings = ?,
                image_url = ?
                WHERE recipe_id = ?");
            $stmt->execute([
                $name, $categoryId, $description, $instructions, 
                $prepTime, $cookTime, $servings, $imageUrl, $recipeId
            ]);
            $message = "Рецепт успешно обновлен!";
        } else {
            // Создание рецепта
            $stmt = $pdo->prepare("INSERT INTO recipes 
                (recipe_name, category_id, description, instructions, 
                 prep_time, cook_time, servings, user_id, image_url) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute([
                $name, $categoryId, $description, $instructions, 
                $prepTime, $cookTime, $servings, $_SESSION['user_id'], $imageUrl
            ]);
            $recipeId = $pdo->lastInsertId();
            $message = "Рецепт успешно создан!";
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
    <title><?= $recipeId ? 'Редактирование' : 'Создание' ?> рецепта</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="styles/favorites.css">

</head>
<body>
    <div class="edit-recipe-container">
        <header class="edit-recipe-header">
            <h1><i class="fas fa-<?= $recipeId ? 'edit' : 'plus-circle' ?>"></i> <?= $recipeId ? 'Редактирование рецепта' : 'Создание нового рецепта' ?></h1>
            <a href="<?= $recipeId ? 'recipe.php?id='.$recipeId : 'index.php' ?>" class="btn btn-back">
                <i class="fas fa-arrow-left"></i> Назад
            </a>
        </header>

        <main>
            <?php if (!empty($error)): ?>
                <div class="alert alert-danger">
                    <i class="fas fa-exclamation-circle"></i> <?= htmlspecialchars($error) ?>
                </div>
            <?php endif; ?>

            <form method="post" enctype="multipart/form-data" class="recipe-form">
                <input type="hidden" name="recipe_id" value="<?= $recipeId ?>">
                
                <div class="form-section">
                    <h2><i class="fas fa-info-circle"></i> Основная информация</h2>
                    
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
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label for="prep_time"><i class="fas fa-clock"></i> Время подготовки (мин)</label>
                            <input type="number" id="prep_time" name="prep_time" min="0" 
                                   value="<?= $recipe['prep_time'] ?? 15 ?>">
                        </div>
                        
                        <div class="form-group">
                            <label for="cook_time"><i class="fas fa-fire"></i> Время готовки (мин)</label>
                            <input type="number" id="cook_time" name="cook_time" min="0" 
                                   value="<?= $recipe['cook_time'] ?? 30 ?>">
                        </div>
                        
                        <div class="form-group">
                            <label for="servings"><i class="fas fa-users"></i> Количество порций</label>
                            <input type="number" id="servings" name="servings" min="1" 
                                   value="<?= $recipe['servings'] ?? 2 ?>">
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="description"><i class="fas fa-align-left"></i> Описание</label>
                        <textarea id="description" name="description" rows="3"><?= htmlspecialchars($recipe['description'] ?? '') ?></textarea>
                    </div>
                </div>
                
                <div class="form-section">
                    <h2><i class="fas fa-image"></i> Изображение рецепта</h2>
                    
                    <div class="form-group">
                        <label for="recipe_image"><i class="fas fa-camera"></i> Загрузить изображение</label>
                        <div class="file-upload-wrapper">
                            <label class="file-upload-label">
                                <i class="fas fa-cloud-upload-alt"></i>
                                <span class="file-upload-text">Выберите файл...</span>
                                <input type="file" id="recipe_image" name="recipe_image" accept="image/*" style="display: none;">
                            </label>
                            <small class="file-hint">Допустимые форматы: JPG, PNG, GIF (макс. 2MB)</small>
                        </div>
                        
                        <?php if (!empty($recipe['image_url'])): ?>
                            <div class="current-image-preview">
                                <p>Текущее изображение:</p>
                                <img src="<?= htmlspecialchars($recipe['image_url']) ?>" 
                                     alt="Текущее изображение рецепта">
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
                
                <div class="form-section">
                    <h2><i class="fas fa-list-ol"></i> Способ приготовления *</h2>
                    
                    <div class="form-group">
                        <textarea id="instructions" name="instructions" rows="8" required><?= htmlspecialchars($recipe['instructions'] ?? '') ?></textarea>
                        <small class="form-hint">Каждый шаг с новой строки</small>
                    </div>
                </div>
                
                <div class="form-actions">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> <?= $recipeId ? 'Обновить рецепт' : 'Создать рецепт' ?>
                    </button>
                    
                    <?php if ($recipeId): ?>
                        <a href="delete_recipe.php?id=<?= $recipeId ?>" 
                           class="btn btn-danger" 
                           onclick="return confirm('Вы точно хотите удалить этот рецепт?')">
                            <i class="fas fa-trash-alt"></i> Удалить рецепт
                        </a>
                    <?php endif; ?>
                </div>
            </form>
        </main>
    </div>
    
    <script>
        // Обработка загрузки файла
        document.getElementById('recipe_image').addEventListener('change', function(e) {
            const fileName = e.target.files[0]?.name || 'Выберите файл...';
            document.querySelector('.file-upload-text').textContent = fileName;
        });
    </script>
</body>
</html>