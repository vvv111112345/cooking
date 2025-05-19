<?php
require_once 'config.php';
require_once 'functions.php';

if (!isLoggedIn()) {
    header("Location: login.php");
    exit();
}

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: index.php");
    exit();
}

$recipe_id = intval($_GET['id']);
$recipe = getRecipe($recipe_id);

// Проверяем, что пользователь может удалять этот рецепт
if (!$recipe || !canEditRecipe($_SESSION['user_id'], $recipe_id)) {
    header("HTTP/1.0 403 Forbidden");
    exit();
}

// Удаляем изображение, если оно есть
if (!empty($recipe['image_url']) && !filter_var($recipe['image_url'], FILTER_VALIDATE_URL)) {
    $image_path = $_SERVER['DOCUMENT_ROOT'] . parse_url($recipe['image_url'], PHP_URL_PATH);
    if (file_exists($image_path)) {
        unlink($image_path);
    }
}

// Удаляем рецепт из базы данных
$pdo->beginTransaction();
try {
    // Удаляем связанные ингредиенты
    $stmt = $pdo->prepare("DELETE FROM recipe_ingredients WHERE recipe_id = ?");
    $stmt->execute([$recipe_id]);
    
    // Удаляем комментарии
    $stmt = $pdo->prepare("DELETE FROM comments WHERE recipe_id = ?");
    $stmt->execute([$recipe_id]);
    
    // Удаляем сам рецепт
    $stmt = $pdo->prepare("DELETE FROM recipes WHERE recipe_id = ?");
    $stmt->execute([$recipe_id]);
    
    $pdo->commit();
    
    header("Location: index.php?deleted=1");
    exit();
} catch (Exception $e) {
    $pdo->rollBack();
    header("Location: edit_recipe.php?id=$recipe_id&error=1");
    exit();
}
?>