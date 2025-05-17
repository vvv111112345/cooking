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
    <style>
        :root {
            --primary: #FF6B6B;
            --primary-light: #FF8E8E;
            --primary-dark: #FF4757;
            --secondary: #70A1FF;
            --success: #4CAF50;
            --danger: #F44336;
            --warning: #FF9800;
            --dark: #2D3436;
            --gray: #636E72;
            --light-gray: #DFE6E9;
            --light: #F8F9FA;
            --white: #FFFFFF;
            --border: #E0E0E0;
            --radius: 8px;
            --shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
            color: var(--dark);
            background-color: var(--light);
        }

        .edit-recipe-container {
            max-width: 1000px;
            margin: 0 auto;
            padding: 2rem;
        }

        .edit-recipe-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 2rem;
            padding-bottom: 1rem;
            border-bottom: 1px solid var(--border);
        }

        .edit-recipe-header h1 {
            font-size: 1.8rem;
            color: var(--dark);
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        .btn {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.75rem 1.5rem;
            border-radius: var(--radius);
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            text-decoration: none;
            border: none;
            font-size: 1rem;
        }

        .btn-back {
            background-color: var(--light-gray);
            color: var(--dark);
        }

        .btn-back:hover {
            background-color: #D5DBDB;
        }

        .btn-primary {
            background-color: var(--primary);
            color: var(--white);
        }

        .btn-primary:hover {
            background-color: var(--primary-dark);
            transform: translateY(-2px);
            box-shadow: var(--shadow);
        }

        .btn-danger {
            background-color: var(--danger);
            color: var(--white);
        }

        .btn-danger:hover {
            background-color: #E53935;
            transform: translateY(-2px);
            box-shadow: var(--shadow);
        }

        .alert {
            padding: 1rem;
            margin-bottom: 1.5rem;
            border-radius: var(--radius);
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        .alert-danger {
            background-color: #FFEBEE;
            color: var(--danger);
            border-left: 4px solid var(--danger);
        }

        .form-section {
            background-color: var(--white);
            border-radius: var(--radius);
            box-shadow: var(--shadow);
            padding: 1.5rem;
            margin-bottom: 1.5rem;
        }

        .form-section h2 {
            font-size: 1.3rem;
            margin-bottom: 1.5rem;
            color: var(--dark);
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        .form-group {
            margin-bottom: 1.5rem;
        }

        .form-group label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: 600;
            color: var(--dark);
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .form-group input,
        .form-group select,
        .form-group textarea {
            width: 100%;
            padding: 0.75rem 1rem;
            border: 1px solid var(--border);
            border-radius: var(--radius);
            font-family: inherit;
            font-size: 1rem;
            transition: all 0.3s ease;
        }

        .form-group input:focus,
        .form-group select:focus,
        .form-group textarea:focus {
            outline: none;
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(255, 107, 107, 0.2);
        }

        .form-group textarea {
            min-height: 150px;
            resize: vertical;
        }

        .form-row {
            display: flex;
            gap: 1.5rem;
            margin-bottom: 1.5rem;
        }

        .form-row .form-group {
            flex: 1;
            margin-bottom: 0;
        }

        .form-hint {
            display: block;
            margin-top: 0.5rem;
            font-size: 0.85rem;
            color: var(--gray);
        }

        .file-upload-wrapper {
            margin-top: 0.5rem;
        }

        .file-upload-label {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            padding: 0.75rem 1rem;
            background-color: var(--light);
            border: 1px dashed var(--border);
            border-radius: var(--radius);
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .file-upload-label:hover {
            border-color: var(--primary);
            background-color: rgba(255, 107, 107, 0.05);
        }

        .file-upload-text {
            color: var(--gray);
        }

        .file-hint {
            margin-top: 0.5rem;
            font-size: 0.85rem;
            color: var(--gray);
        }

        .current-image-preview {
            margin-top: 1rem;
            text-align: center;
        }

        .current-image-preview img {
            max-width: 100%;
            max-height: 200px;
            border-radius: var(--radius);
            margin: 0.5rem 0;
            box-shadow: var(--shadow);
        }

        .remove-image-checkbox {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            margin-top: 0.5rem;
            cursor: pointer;
        }

        .ingredients-container {
            margin-bottom: 1rem;
        }

        .ingredient-row {
            display: flex;
            gap: 1rem;
            margin-bottom: 0.75rem;
            align-items: center;
        }

        .ingredient-inputs {
            display: flex;
            gap: 1rem;
            flex: 1;
        }

        .ingredient-inputs input {
            padding: 0.5rem 0.75rem;
        }

        .ingredient-inputs input[type="text"] {
            flex: 2;
        }

        .btn-remove {
            background-color: var(--light);
            color: var(--danger);
            width: 40px;
            height: 40px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 50%;
        }

        .btn-remove:hover {
            background-color: #FFEBEE;
        }

        .btn-add-ingredient {
            background-color: var(--success);
            color: var(--white);
        }

        .btn-add-ingredient:hover {
            background-color: #43A047;
            transform: translateY(-2px);
            box-shadow: var(--shadow);
        }

        .form-actions {
            display: flex;
            justify-content: flex-end;
            gap: 1rem;
            margin-top: 2rem;
        }

        @media (max-width: 768px) {
            .edit-recipe-container {
                padding: 1rem;
            }
            
            .edit-recipe-header {
                flex-direction: column;
                align-items: flex-start;
                gap: 1rem;
            }
            
            .form-row {
                flex-direction: column;
                gap: 1rem;
            }
            
            .ingredient-row {
                flex-direction: column;
                align-items: stretch;
            }
            
            .ingredient-inputs {
                flex-direction: column;
                gap: 0.75rem;
            }
            
            .form-actions {
                flex-direction: column;
            }
            
            .btn {
                width: 100%;
                justify-content: center;
            }
        }
    </style>
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