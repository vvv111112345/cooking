<?php
session_start();
require_once 'config.php';

if (!isset($_SESSION['user_id'])) {
    die("Необходимо авторизоваться!");
}

$action = $_GET['action'] ?? '';
$recipeId = $_GET['recipe_id'] ?? 0;

if ($action === 'add') {
    $stmt = $pdo->prepare("INSERT INTO favorites (user_id, recipe_id) VALUES (?, ?)");
    $stmt->execute([$_SESSION['user_id'], $recipeId]);
    echo "Рецепт добавлен в избранное!";
} elseif ($action === 'remove') {
    $stmt = $pdo->prepare("DELETE FROM favorites WHERE user_id = ? AND recipe_id = ?");
    $stmt->execute([$_SESSION['user_id'], $recipeId]);
    echo "Рецепт удалён из избранного.";
}
?>