<?php
require_once 'config.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Получаем категории для select
$categories = $pdo->query("SELECT * FROM categories")->fetchAll();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Обработка формы...
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Добавить рецепт | Кулинарные скитания</title>
  <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;600;700&family=Playfair+Display:wght@700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="styles/form.css">
</head>
<body>
  <div class="form-container">
    <a href="home.php" class="back-link"><i class="icon-arrow-left"></i> Назад</a>
    
    <div class="form-card">
      <h1>Добавить новый рецепт</h1>
      
      <form action="add_recipe.php" method="POST" enctype="multipart/form-data">
        <div class="form-group">
          <label for="recipe_name">Название рецепта</label>
          <input type="text" id="recipe_name" name="recipe_name" required>
        </div>
        
        <div class="form-row">
          <div class="form-group">
            <label for="category_id">Категория</label>
            <select id="category_id" name="category_id" required>
              <?php foreach ($categories as $category): ?>
              <option value="<?= $category['category_id'] ?>"><?= htmlspecialchars($category['category_name']) ?></option>
              <?php endforeach; ?>
            </select>
          </div>
          
          <div class="form-group">
            <label for="image">Изображение</label>
            <input type="file" id="image" name="image" accept="image/*">
          </div>
        </div>
        
        <div class="form-row">
          <div class="form-group">
            <label for="prep_time">Время подготовки (мин)</label>
            <input type="number" id="prep_time" name="prep_time" min="0">
          </div>
          
          <div class="form-group">
            <label for="cook_time">Время готовки (мин)</label>
            <input type="number" id="cook_time" name="cook_time" min="0">
          </div>
          
          <div class="form-group">
            <label for="servings">Количество порций</label>
            <input type="number" id="servings" name="servings" min="1" value="1">
          </div>
        </div>
        
        <div class="form-group">
          <label for="description">Краткое описание</label>
          <textarea id="description" name="description" rows="3"></textarea>
        </div>
        
        <div class="form-group">
          <label>Ингредиенты</label>
          <div id="ingredients-container">
            <div class="ingredient-row">
              <input type="text" name="ingredients[]" placeholder="Название" required>
              <input type="number" name="quantities[]" step="0.01" placeholder="Количество" required>
              <input type="text" name="units[]" placeholder="Ед. измерения">
              <button type="button" class="btn btn-remove" onclick="removeIngredient(this)">×</button>
            </div>
          </div>
          <button type="button" class="btn btn-add" onclick="addIngredient()">+ Добавить ингредиент</button>
        </div>
        
        <div class="form-group">
          <label for="instructions">Инструкция приготовления</label>
          <textarea id="instructions" name="instructions" rows="8" required></textarea>
        </div>
        
        <button type="submit" class="btn btn-primary">Сохранить рецепт</button>
      </form>
    </div>
  </div>

  <script>
  function addIngredient() {
    const container = document.getElementById('ingredients-container');
    const newRow = document.createElement('div');
    newRow.className = 'ingredient-row';
    newRow.innerHTML = `
      <input type="text" name="ingredients[]" placeholder="Название" required>
      <input type="number" name="quantities[]" step="0.01" placeholder="Количество" required>
      <input type="text" name="units[]" placeholder="Ед. измерения">
      <button type="button" class="btn btn-remove" onclick="removeIngredient(this)">×</button>
    `;
    container.appendChild(newRow);
  }
  
  function removeIngredient(btn) {
    btn.parentElement.remove();
  }
  </script>
</body>
</html>