<?php
require_once 'config.php'; // Подключение к БД

// Получаем параметры поиска
$searchQuery = $_GET['query'] ?? '';
$categoryId = $_GET['category_id'] ?? '';
$ingredientName = $_GET['ingredient'] ?? '';

// SQL-запрос с фильтрами
$sql = "SELECT r.* FROM recipes r 
        LEFT JOIN recipe_ingredients ri ON r.recipe_id = ri.recipe_id 
        LEFT JOIN ingredients i ON ri.ingredient_id = i.ingredient_id 
        WHERE 1=1";

if (!empty($searchQuery)) {
    $sql .= " AND r.recipe_name LIKE :query";
}
if (!empty($categoryId)) {
    $sql .= " AND r.category_id = :category_id";
}
if (!empty($ingredientName)) {
    $sql .= " AND i.ingredient_name LIKE :ingredient";
}

$stmt = $pdo->prepare($sql);

if (!empty($searchQuery)) {
    $stmt->bindValue(':query', "%$searchQuery%");
}
if (!empty($categoryId)) {
    $stmt->bindValue(':category_id', $categoryId);
}
if (!empty($ingredientName)) {
    $stmt->bindValue(':ingredient', "%$ingredientName%");
}

$stmt->execute();
$recipes = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Вывод результатов
foreach ($recipes as $recipe) {
    echo "<div class='recipe-card'>
            <h3>{$recipe['recipe_name']}</h3>
            <p>{$recipe['description']}</p>
            <a href='recipe.php?id={$recipe['recipe_id']}'>Подробнее</a>
          </div>";
}
?>