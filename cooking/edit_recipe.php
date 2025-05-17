<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $recipeId ? 'Редактирование' : 'Создание' ?> рецепта | Кулинарный сайт</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="css/edit-recipe.css">
</head>
<body>
    <div class="edit-recipe-container">
        <header class="edit-recipe-header">
            <h1><i class="fas fa-edit"></i> <?= $recipeId ? 'Редактирование рецепта' : 'Создание нового рецепта' ?></h1>
            <a href="<?= $recipeId ? 'recipe.php?id='.$recipeId : 'index.php' ?>" class="btn btn-back">
                <i class="fas fa-arrow-left"></i> Назад
            </a>
        </header>

        <main class="edit-recipe-main">
            <?php if (isset($error)): ?>
                <div class="alert alert-danger">
                    <i class="fas fa-exclamation-circle"></i> <?= htmlspecialchars($error) ?>
                </div>
            <?php endif; ?>

            <form method="post" enctype="multipart/form-data" class="recipe-form">
                <input type="hidden" name="recipe_id" value="<?= $recipeId ?>">
                
                <section class="form-section">
                    <h2><i class="fas fa-info-circle"></i> Основная информация</h2>
                    
                    <div class="form-group">
                        <label for="recipe_name"><i class="fas fa-utensils"></i> Название рецепта *</label>
                        <input type="text" id="recipe_name" name="recipe_name" 
                               value="<?= htmlspecialchars($recipe['recipe_name'] ?? '') ?>" required>
                    </div>
                    
                    <div class="form-row">
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
                        
                        <div class="form-group">
                            <label for="recipe_image"><i class="fas fa-image"></i> Изображение</label>
                            <div class="file-upload-wrapper">
                                <label class="file-upload-label" for="recipe_image">
                                    <i class="fas fa-cloud-upload-alt"></i>
                                    <span class="file-upload-text">Выберите файл</span>
                                </label>
                                <input type="file" id="recipe_image" name="recipe_image" accept="image/*">
                                <small class="file-hint">Допустимые форматы: JPG, PNG, GIF (макс. 2MB)</small>
                                
                                <?php if (!empty($recipe['image_url'])): ?>
                                    <div class="current-image-preview">
                                        <p>Текущее изображение:</p>
                                        <img src="<?= htmlspecialchars($recipe['image_url']) ?>" 
                                             alt="Текущее изображение рецепта">
                                        <label class="remove-image-checkbox">
                                            <input type="checkbox" name="remove_image"> Удалить изображение
                                        </label>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
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
                        <label for="description"><i class="fas fa-align-left"></i> Описание</label>
                        <textarea id="description" name="description" rows="3"><?= htmlspecialchars($recipe['description'] ?? '') ?></textarea>
                    </div>
                </section>
                
                <section class="form-section">
                    <h2><i class="fas fa-list-ul"></i> Ингредиенты</h2>
                    <div id="ingredients-container" class="ingredients-container">
                        <!-- Динамически добавляемые ингредиенты будут здесь -->
                    </div>
                    <button type="button" class="btn btn-add-ingredient" onclick="addIngredient()">
                        <i class="fas fa-plus"></i> Добавить ингредиент
                    </button>
                </section>
                
                <section class="form-section">
                    <h2><i class="fas fa-list-ol"></i> Способ приготовления *</h2>
                    <div class="form-group">
                        <textarea id="instructions" name="instructions" rows="8" required><?= htmlspecialchars($recipe['instructions'] ?? '') ?></textarea>
                        <small class="form-hint">Каждый шаг с новой строки</small>
                    </div>
                </section>
                
                <div class="form-actions">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> <?= $recipeId ? 'Обновить рецепт' : 'Создать рецепт' ?>
                    </button>
                    
                    <?php if ($recipeId): ?>
                        <a href="delete_recipe.php?id=<?= $recipe['recipe_id'] ?>" 
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
    // Добавление ингредиентов
    function addIngredient() {
        const container = document.getElementById('ingredients-container');
        const newRow = document.createElement('div');
        newRow.className = 'ingredient-row';
        newRow.innerHTML = `
            <div class="ingredient-inputs">
                <input type="text" name="ingredients[]" placeholder="Название" required>
                <input type="number" name="quantities[]" step="0.01" placeholder="Количество" required>
                <input type="text" name="units[]" placeholder="Ед. измерения">
            </div>
            <button type="button" class="btn btn-remove" onclick="removeIngredient(this)">
                <i class="fas fa-times"></i>
            </button>
        `;
        container.appendChild(newRow);
    }
    
    // Удаление ингредиента
    function removeIngredient(btn) {
        const rows = document.querySelectorAll('.ingredient-row');
        if (rows.length > 1) {
            btn.closest('.ingredient-row').remove();
        } else {
            alert('Должен остаться хотя бы один ингредиент');
        }
    }
    
    // Показ имени загружаемого файла
    document.getElementById('recipe_image').addEventListener('change', function(e) {
        const fileName = e.target.files[0] ? e.target.files[0].name : 'Выберите файл';
        document.querySelector('.file-upload-text').textContent = fileName;
    });
    
    // Инициализация существующих ингредиентов при редактировании
    document.addEventListener('DOMContentLoaded', function() {
        <?php if (!empty($ingredients)): ?>
            <?php foreach ($ingredients as $ingredient): ?>
                addIngredient();
                const rows = document.querySelectorAll('.ingredient-row');
                const lastRow = rows[rows.length - 1];
                lastRow.querySelector('input[name="ingredients[]"]').value = '<?= addslashes($ingredient['ingredient_name']) ?>';
                lastRow.querySelector('input[name="quantities[]"]').value = '<?= $ingredient['quantity'] ?>';
                lastRow.querySelector('input[name="units[]"]').value = '<?= addslashes($ingredient['unit'] ?? '') ?>';
            <?php endforeach; ?>
        <?php else: ?>
            // Добавляем одну пустую строку по умолчанию
            addIngredient();
        <?php endif; ?>
    });
    </script>
</body>
</html>