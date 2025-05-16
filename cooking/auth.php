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

// Функция для безопасной обработки данных (предотвращает SQL-инъекции)
function sanitize_input($data) {
    global $conn;
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $conn->real_escape_string($data);
}

// --------------------------------------------------------
// РЕГИСТРАЦИЯ
// --------------------------------------------------------

if (isset($_POST['register'])) {
    // Получение данных из формы
    $username = sanitize_input($_POST['username']);
    $password = $_POST['password']; // Не хешируем тут, сначала проверка
    $confirm_password = $_POST['confirm_password'];
    $email = sanitize_input($_POST['email']);

    // Проверка на заполненность полей (можно добавить больше проверок)
    if (empty($username) || empty($password) || empty($confirm_password) || empty($email)) {
        $registration_error = "Пожалуйста, заполните все поля.";
    } elseif ($password != $confirm_password) {
        $registration_error = "Пароли не совпадают.";
    } else {

        // Проверка, существует ли пользователь с таким именем или email
        $sql = "SELECT * FROM users WHERE username = '$username' OR email = '$email'";
        $result = $conn->query($sql);

        if ($result->num_rows > 0) {
            $registration_error = "Пользователь с таким именем или email уже существует.";
        } else {
            // Хеширование пароля (ВАЖНО!)
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);

            // SQL-запрос для добавления пользователя в базу данных
            $sql = "INSERT INTO users (username, password, email) VALUES ('$username', '$hashed_password', '$email')";

            if ($conn->query($sql) === TRUE) {
                $registration_success = "Регистрация прошла успешно!  <a href='login.php'>Войти</a>"; // Перенаправить на страницу входа
            } else {
                $registration_error = "Ошибка при регистрации: " . $conn->error;
            }
        }
    }
}

// --------------------------------------------------------
// АВТОРИЗАЦИЯ
// --------------------------------------------------------

if (isset($_POST['login'])) {
    // Получение данных из формы
    $username = sanitize_input($_POST['username']);
    $password = $_POST['password'];

    // Проверка на заполненность полей
    if (empty($username) || empty($password)) {
        $login_error = "Пожалуйста, заполните все поля.";
    } else {
        // SQL-запрос для поиска пользователя в базе данных
        $sql = "SELECT user_id, username, password FROM users WHERE username = '$username'";
        $result = $conn->query($sql);

        if ($result->num_rows == 1) {
            $row = $result->fetch_assoc();

            // Проверка пароля с использованием password_verify()
            if (password_verify($password, $row['password'])) {
                // Аутентификация успешна
                session_start(); // Запуск сессии (важно!)
                $_SESSION['user_id'] = $row['user_id'];
                $_SESSION['username'] = $row['username'];

                // Перенаправление на защищенную страницу (например, главную страницу)
                header("Location: index.php"); // Замените на вашу главную страницу
                exit();
            } else {
                $login_error = "Неверный пароль.";
            }
        } else {
            $login_error = "Пользователь с таким именем не найден.";
        }
    }
}

// --------------------------------------------------------
// ВЫХОД ИЗ СИСТЕМЫ
// --------------------------------------------------------

if (isset($_GET['logout'])) {
    session_start();
    session_unset();
    session_destroy();
    header("Location: login.php"); // Перенаправление на страницу входа
    exit();
}

$conn->close();
?>


