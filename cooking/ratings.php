<?php
session_start();
require_once 'config.php';

if (!isset($_SESSION['user_id'])) {
    die("Оценивать могут только авторизованные пользователи!");
}

$recipeId = $_POST['recipe_id'];
$rating = $_POST['rating'];

$stmt = $pdo->prepare("INSERT INTO ratings (user_id, recipe_id, rating) VALUES (?, ?, ?) 
                        ON DUPLICATE KEY UPDATE rating = ?");
$stmt->execute([$_SESSION['user_id'], $recipeId, $rating, $rating]);

echo "Спасибо за оценку!";
?>