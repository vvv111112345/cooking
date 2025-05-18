<?php
require_once 'config.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

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
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
  <link rel="stylesheet" href="styles/add-recipe.css">
</head>
<body>
  <div class="form-wrapper">
    <div class="form-container">
      <a href="home.php" class="back-button">
        <i class="fas fa-arrow-left"></i> Назад
      </a>
      
      <div class="form-header">
        <h1><i class="fas fa-plus-circle"></i> Добавить новый рецепт</h1>
        <p>Заполните все поля для создания рецепта</p>
      </div>
      
      <form action="add_recipe.php" method="POST" enctype="multipart/form-data" class="recipe-form">
        <div class="form-section">
          <h2><i class="fas fa-info-circle"></i> Основная информация</h2>
          
          <div class="form-group">
            <label for="recipe_name">Название рецепта*</label>
            <input type="text" id="recipe_name" name="recipe_name" required placeholder="Например: Паста Карбонара">
          </div>
          
          <div class="form-row">
            <div class="form-group">
              <label for="category_id">Категория*</label>
              <select id="category_id" name="category_id" required>
                <option value="">Выберите категорию</option>
                <?php foreach ($categories as $category): ?>
                <option value="<?= $category['category_id'] ?>"><?= htmlspecialchars($category['category_name']) ?></option>
                <?php endforeach; ?>
              </select>
            </div>
            
            <div class="form-group">
              <label for="image">Изображение</label>
              <div class="file-upload">
                <label for="image">
                  <i class="fas fa-cloud-upload-alt"></i>
                  <span id="file-name">Выберите файл</span>
                </label>
                <input type="file" id="image" name="image" accept="image/*">
              </div>
            </div>
          </div>
          
          <div class="form-row">
            <div class="form-group">
              <label for="prep_time">Время подготовки (мин)</label>
              <input type="number" id="prep_time" name="prep_time" min="0" placeholder="20">
            </div>
            
            <div class="form-group">
              <label for="cook_time">Время готовки (мин)</label>
              <input type="number" id="cook_time" name="cook_time" min="0" placeholder="30">
            </div>
            
            <div class="form-group">
              <label for="servings">Количество порций*</label>
              <input type="number" id="servings" name="servings" min="1" value="4" required>
            </div>
          </div>
          
          <div class="form-group">
            <label for="description">Краткое описание*</label>
            <textarea id="description" name="description" rows="3" required placeholder="Опишите ваш рецепт..."></textarea>
          </div>
        </div>
        
        <div class="form-section">
          <h2><i class="fas fa-list-ul"></i> Ингредиенты</h2>
          <div id="ingredients-container">
            <div class="ingredient-row">
              <input type="text" name="ingredients[]" placeholder="Название" required>
              <input type="number" name="quantities[]" step="0.01" placeholder="Количество" required>
              <input type="text" name="units[]" placeholder="Ед. измерения (г, мл, шт)">
              <button type="button" class="btn btn-remove" onclick="removeIngredient(this)">
                <i class="fas fa-times"></i>
              </button>
            </div>
          </div>
          <button type="button" class="btn btn-add" onclick="addIngredient()">
            <i class="fas fa-plus"></i> Добавить ингредиент
          </button>
        </div>
        
        <div class="form-section">
          <h2><i class="fas fa-list-ol"></i> Способ приготовления*</h2>
          <div class="form-group">
            <textarea id="instructions" name="instructions" rows="8" required placeholder="Опишите шаги приготовления..."></textarea>
          </div>
        </div>
        
        <div class="form-actions">
          <button type="reset" class="btn btn-secondary">
            <i class="fas fa-undo"></i> Очистить
          </button>
          <button type="submit" class="btn btn-primary">
            <i class="fas fa-save"></i> Сохранить рецепт
          </button>
        </div>
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
      <input type="text" name="units[]" placeholder="Ед. измерения (г, мл, шт)">
      <button type="button" class="btn btn-remove" onclick="removeIngredient(this)">
        <i class="fas fa-times"></i>
      </button>
    `;
    container.appendChild(newRow);
  }
  
  function removeIngredient(btn) {
    if (document.querySelectorAll('.ingredient-row').length > 1) {
      btn.closest('.ingredient-row').remove();
    } else {
      alert('Должен остаться хотя бы один ингредиент');
    }
  }
  
  // Показ имени файла
  document.getElementById('image').addEventListener('change', function(e) {
    const fileName = e.target.files[0] ? e.target.files[0].name : 'Выберите файл';
    document.getElementById('file-name').textContent = fileName;
  });
  </script>
</body>
</html>