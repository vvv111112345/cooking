<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Добавить рецепт | Кулинарный сайт</title>
    <link rel="stylesheet" href="css/style.css">
    
</head>
<body>
    <div class="container">
        <header>
            <h1>Добавить рецепт</h1>
            <div class="user-panel">
                <a href="index.php" class="btn btn-secondary">На главную</a>
                <a href="logout.php" class="btn btn-danger">Выйти</a>
            </div>
        </header>

        <main>
            <form class="recipe-form" method="post" enctype="multipart/form-data">
                <div class="form-group">
                    <label for="recipe_name">Название рецепта:</label>
                    <input type="text" id="recipe_name" name="recipe_name" class="form-control" required>
                </div>

                <div class="form-group">
                    <label for="category_id">Категория:</label>
                    <select id="category_id" name="category_id" class="form-control" required>
                        <?php foreach ($categories as $category): ?>
                            <option value="<?= $category['category_id'] ?>">
                                <?= htmlspecialchars($category['category_name']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label for="description">Описание:</label>
                    <textarea id="description" name="description" class="form-control"></textarea>
                </div>

                <div class="form-group">
                    <label for="instructions">Инструкции (каждый шаг с новой строки):</label>
                    <textarea id="instructions" name="instructions" class="form-control" required></textarea>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="prep_time">Время подготовки (мин):</label>
                        <input type="number" id="prep_time" name="prep_time" class="form-control" required>
                    </div>

                    <div class="form-group">
                        <label for="cook_time">Время готовки (мин):</label>
                        <input type="number" id="cook_time" name="cook_time" class="form-control" required>
                    </div>

                    <div class="form-group">
                        <label for="servings">Количество порций:</label>
                        <input type="number" id="servings" name="servings" class="form-control" required>
                    </div>
                </div>

                <div class="form-group">
                    <label for="recipe_image">Изображение рецепта:</label>
                    <input type="file" id="recipe_image" name="recipe_image" accept="image/*" class="form-control">
                    <img id="imagePreview" class="image-preview" src="#" alt="Предпросмотр">
                </div>

                <button type="submit" class="btn btn-primary">Добавить рецепт</button>
            </form>
        </main>
    </div>

    <script>
        document.getElementById('recipe_image').addEventListener('change', function(e) {
            const preview = document.getElementById('imagePreview');
            const file = e.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    preview.src = e.target.result;
                    preview.style.display = 'block';
                }
                reader.readAsDataURL(file);
            }
        });
    </script>
</body>
</html>