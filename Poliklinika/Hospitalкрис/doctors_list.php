<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once 'includes/config.php';
require_once 'includes/db.php';

// Проверка авторизации и прав
if(!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    $_SESSION['error_message'] = "Доступ запрещён";
    header('Location: login.php');
    exit;
}

// Обработка формы
if($_SERVER['REQUEST_METHOD'] == 'POST') {
    try {
        $full_name = trim($_POST['full_name']);
        $specialization = trim($_POST['specialization']);
        $phone = trim($_POST['phone']);
        $email = trim($_POST['email']);
        $license_number = trim($_POST['license_number']);

        // Валидация данных
        if(empty($full_name) || empty($specialization) || empty($phone) || empty($email) || empty($license_number)) {
            throw new Exception("Все поля обязательны для заполнения");
        }

        // Проверка email
        if(!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new Exception("Некорректный email");
        }

        // Проверка существующего врача
        $stmt = $pdo->prepare("SELECT id FROM doctors WHERE email = ? OR license_number = ?");
        $stmt->execute([$email, $license_number]);
        
        if($stmt->rowCount() > 0) {
            throw new Exception("Врач с таким email или номером лицензии уже существует");
        }

        // Добавление врача
        $stmt = $pdo->prepare("INSERT INTO doctors (full_name, specialization, phone, email, license_number) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([$full_name, $specialization, $phone, $email, $license_number]);
        
        $_SESSION['success_message'] = "Врач успешно добавлен!";
        
        // Проверка существования файла перед перенаправлением
        $redirect_url = 'doctors_list.php';
        if(!file_exists($redirect_url)) {
            throw new Exception("Файл для перенаправления не найден: ".$redirect_url);
        }
        
        header('Location: '.$redirect_url);
        exit;

    } catch (PDOException $e) {
        $_SESSION['error_message'] = "Ошибка базы данных: " . $e->getMessage();
    } catch (Exception $e) {
        $_SESSION['error_message'] = $e->getMessage();
    }
}

require_once 'includes/header.php';
?>

<h2>Добавление нового врача</h2>

<?php if(isset($_SESSION['error_message'])): ?>
    <div class="alert alert-danger">
        <?php echo $_SESSION['error_message']; unset($_SESSION['error_message']); ?>
    </div>
<?php endif; ?>

<form method="post" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" class="doctor-form">
    <div class="form-group">
        <label for="full_name">ФИО врача:</label>
        <input type="text" id="full_name" name="full_name" required value="<?php echo isset($_POST['full_name']) ? htmlspecialchars($_POST['full_name']) : ''; ?>">
    </div>
    
    <div class="form-group">
        <label for="specialization">Специализация:</label>
        <input type="text" id="specialization" name="specialization" required value="<?php echo isset($_POST['specialization']) ? htmlspecialchars($_POST['specialization']) : ''; ?>">
    </div>
    
    <div class="form-group">
        <label for="phone">Телефон:</label>
        <input type="tel" id="phone" name="phone" required value="<?php echo isset($_POST['phone']) ? htmlspecialchars($_POST['phone']) : ''; ?>">
    </div>
    
    <div class="form-group">
        <label for="email">Email:</label>
        <input type="email" id="email" name="email" required value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>">
    </div>
    
    <div class="form-group">
        <label for="license_number">Номер лицензии:</label>
        <input type="text" id="license_number" name="license_number" required value="<?php echo isset($_POST['license_number']) ? htmlspecialchars($_POST['license_number']) : ''; ?>">
    </div>
    
    <button type="submit" class="btn btn-primary">Добавить врача</button>
    <a href="doctors_list.php" class="btn btn-secondary">Отмена</a>
</form>

<?php
require_once 'includes/footer.php';
?>