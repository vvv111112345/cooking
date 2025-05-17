<?php
// Подключение к базе данных (скорректируйте параметры!)
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "cooking";

$conn = new mysqli($servername, $username, $password, $dbname);

// Проверка соединения
if ($conn->connect_error) {
    die("Ошибка подключения к базе данных: " . $conn->connect_error);
}


require_once 'config.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if ($_POST['action'] === 'login') {
        // Обработка входа
        $username = trim($_POST['username']);
        $password = $_POST['password'];
        
        $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ?");
        $stmt->execute([$username]);
        $user = $stmt->fetch();
        
        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['user_id'];
            $_SESSION['username'] = $user['username'];
            header("Location: home.php");
            exit();
        } else {
            $_SESSION['error'] = "Неверное имя пользователя или пароль";
            header("Location: login.php");
            exit();
        }
    }
    elseif ($_POST['action'] === 'register') {
        // Обработка регистрации
        $username = trim($_POST['username']);
        $name = trim($_POST['name'] ?? '');
        $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
        $email = null; // Можно оставить NULL или добавить поле в форму
        
        // Проверка существующего пользователя
        $stmt = $pdo->prepare("SELECT user_id FROM users WHERE username = ?");
        $stmt->execute([$username]);
        
        if ($stmt->fetch()) {
            $_SESSION['error'] = "Это имя пользователя уже занято";
            header("Location: register.php");
            exit();
        }
        
        // Создание нового пользователя
        try {
            $stmt = $pdo->prepare("INSERT INTO users (username, name, password, email) VALUES (?, ?, ?, ?)");
            $stmt->execute([$username, $name, $password, $email]);
            
            $_SESSION['success'] = "Регистрация успешна! Теперь вы можете войти";
            header("Location: login.php");
            exit();
        } catch (PDOException $e) {
            $_SESSION['error'] = "Ошибка при регистрации: " . $e->getMessage();
            header("Location: register.php");
            exit();
        }
    }
}

header("Location: index.php");
exit();

