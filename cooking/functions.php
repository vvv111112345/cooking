<?php
function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

function isAdmin() {
    return isset($_SESSION['is_admin']) && $_SESSION['is_admin'];
}

function getRecipe($id) {
    global $pdo;
    try {
        $stmt = $pdo->prepare("SELECT r.*, c.category_name, u.username as author 
                              FROM recipes r
                              JOIN categories c ON r.category_id = c.category_id
                              JOIN users u ON r.user_id = u.user_id
                              WHERE r.recipe_id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch();
    } catch (PDOException $e) {
        error_log("Error fetching recipe: " . $e->getMessage());
        return null;
    }
}

function getRecipeIngredients($recipeId) {
    global $pdo;
    try {
        $stmt = $pdo->prepare("SELECT i.ingredient_name, ri.quantity, i.unit 
                              FROM recipe_ingredients ri
                              JOIN ingredients i ON ri.ingredient_id = i.ingredient_id
                              WHERE ri.recipe_id = ?");
        $stmt->execute([$recipeId]);
        return $stmt->fetchAll();
    } catch (PDOException $e) {
        error_log("Error fetching ingredients: " . $e->getMessage());
        return [];
    }
}

function getComments($recipeId) {
    global $pdo;
    try {
        $stmt = $pdo->prepare("SELECT c.*, u.username 
                              FROM comments c
                              JOIN users u ON c.user_id = u.user_id
                              WHERE c.recipe_id = ?
                              ORDER BY c.created_at DESC");
        $stmt->execute([$recipeId]);
        return $stmt->fetchAll();
    } catch (PDOException $e) {
        error_log("Error fetching comments: " . $e->getMessage());
        return [];
    }
}

function addComment($recipeId, $userId, $text) {
    global $pdo;
    try {
        $stmt = $pdo->prepare("INSERT INTO comments (recipe_id, user_id, comment_text) 
                              VALUES (?, ?, ?)");
        return $stmt->execute([$recipeId, $userId, htmlspecialchars(trim($text))]);
    } catch (PDOException $e) {
        error_log("Error adding comment: " . $e->getMessage());
        return false;
    }
}

function canEditRecipe($user_id, $recipe_id) {
    global $pdo;
    
    $stmt = $pdo->prepare("SELECT user_id FROM recipes WHERE recipe_id = ?");
    $stmt->execute([$recipe_id]);
    $recipe = $stmt->fetch();
    
    return $recipe && ($recipe['user_id'] == $user_id || isAdmin($user_id));
}

function getRecipesByCategory($categoryId) {
    global $pdo;
    try {
        $stmt = $pdo->prepare("SELECT r.*, u.username as author 
                              FROM recipes r
                              JOIN users u ON r.user_id = u.user_id
                              WHERE r.category_id = ?
                              ORDER BY r.recipe_name");
        $stmt->execute([$categoryId]);
        return $stmt->fetchAll();
    } catch (PDOException $e) {
        error_log("Error fetching recipes by category: " . $e->getMessage());
        return [];
    }
}

function validateImage($file) {
    $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
    if (!in_array($file['type'], $allowedTypes)) {
        throw new Exception("Допустимы только изображения JPG, PNG или GIF");
    }
    if ($file['size'] > MAX_FILE_SIZE) {
        throw new Exception("Максимальный размер файла - 2MB");
    }
    return true;
}

function sanitizeFileName($name) {
    $name = preg_replace('/[^a-zA-Z0-9\.\-_]/', '', $name);
    return uniqid() . '_' . $name;
}


function getCategories() {
    global $pdo;
    try {
        $stmt = $pdo->query("SELECT * FROM categories ORDER BY category_name");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        error_log("Error in getCategories: " . $e->getMessage());
        return false;
    }
}

function deleteRecipe($recipe_id) {
    global $pdo;
    
    try {
        $pdo->beginTransaction();
        
        // Удаление изображения
        $stmt = $pdo->prepare("SELECT image_url FROM recipes WHERE recipe_id = ?");
        $stmt->execute([$recipe_id]);
        $image_path = $stmt->fetchColumn();
        
        if ($image_path && file_exists($_SERVER['DOCUMENT_ROOT'].$image_path)) {
            unlink($_SERVER['DOCUMENT_ROOT'].$image_path);
        }
        
        // Удаление ингредиентов
        $pdo->prepare("DELETE FROM recipe_ingredients WHERE recipe_id = ?")->execute([$recipe_id]);
        
        // Удаление комментариев
        $pdo->prepare("DELETE FROM comments WHERE recipe_id = ?")->execute([$recipe_id]);
        
        // Удаление рецепта
        $pdo->prepare("DELETE FROM recipes WHERE recipe_id = ?")->execute([$recipe_id]);
        
        $pdo->commit();
        return true;
    } catch (Exception $e) {
        $pdo->rollBack();
        error_log("Ошибка удаления рецепта: ".$e->getMessage());
        return false;
    }
}

function getUserData(PDO $pdo, int $userId): array {
    $stmt = $pdo->prepare("SELECT username, name, avatar FROM users WHERE user_id = ?");
    $stmt->execute([$userId]);
    return $stmt->fetch() ?: [];
}

function fetchRecipes(PDO $pdo, string $query, array $params): array {
    $stmt = $pdo->prepare($query);
    $stmt->execute($params);
    return $stmt->fetchAll();
}

function toggleFavorite(PDO $pdo, int $userId, int $recipeId): void {
    // Проверяем, есть ли уже рецепт в избранном
    $stmt = $pdo->prepare("SELECT * FROM favorites WHERE user_id = ? AND recipe_id = ?");
    $stmt->execute([$userId, $recipeId]);
    
    if ($stmt->fetch()) {
        // Удаляем из избранного
        $stmt = $pdo->prepare("DELETE FROM favorites WHERE user_id = ? AND recipe_id = ?");
        $stmt->execute([$userId, $recipeId]);
    } else {
        // Добавляем в избранное
        $stmt = $pdo->prepare("INSERT INTO favorites (user_id, recipe_id) VALUES (?, ?)");
        $stmt->execute([$userId, $recipeId]);
    }
}