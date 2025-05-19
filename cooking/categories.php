<?php
require_once 'config.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Получаем все категории
$categories = $pdo->query("SELECT * FROM categories ORDER BY category_name")->fetchAll();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Обработка добавления/удаления категорий
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
  <meta charset="UTF-8">
  <title>Категории | Кулинарные скитания</title>
  <!-- Подключение стилей -->
   <link rel="stylesheet" href="styles/categories.css">
</head>
<body>
  <div class="dashboard-container">
    <!-- Боковая панель (как в home.php) -->
    
    <main class="main-content">
      <header class="content-header">
        <h1>Управление категориями</h1>
        <button id="addCategoryBtn" class="btn btn-primary">
          <i class="icon-plus"></i> Новая категория
        </button>
      </header>

      <div class="categories-list">
        <?php foreach ($categories as $category): ?>
        <div class="category-card">
          <h3><?= htmlspecialchars($category['category_name']) ?></h3>
          <p><?= htmlspecialchars($category['description'] ?? '') ?></p>
          <div class="category-actions">
            <button class="btn btn-edit" data-id="<?= $category['category_id'] ?>">
              <i class="icon-edit"></i>
            </button>
            <button class="btn btn-danger" data-id="<?= $category['category_id'] ?>">
              <i class="icon-delete"></i>
            </button>
          </div>
        </div>
        <?php endforeach; ?>
      </div>
    </main>
  </div>

  <!-- Модальное окно добавления -->
  <div id="categoryModal" class="modal">
    <div class="modal-content">
      <span class="close">&times;</span>
      <h2>Новая категория</h2>
      <form id="categoryForm" method="POST">
        <div class="form-group">
          <label for="category_name">Название</label>
          <input type="text" id="category_name" name="category_name" required>
        </div>
        <div class="form-group">
          <label for="description">Описание</label>
          <textarea id="description" name="description" rows="3"></textarea>
        </div>
        <button type="submit" class="btn btn-primary">Сохранить</button>
      </form>
    </div>
  </div>

  <script>
  // Логика модального окна и AJAX-запросов
  </script>
</body>
</html>