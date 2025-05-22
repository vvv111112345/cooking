<?php
require_once 'includes/config.php';
require_once 'includes/header.php';

if(!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

if($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Обработка формы
    $full_name = trim($_POST['full_name']);
    $birth_date = trim($_POST['birth_date']);
    $gender = trim($_POST['gender']);
    $address = trim($_POST['address']);
    $phone = trim($_POST['phone']);
    $parent_name = trim($_POST['parent_name']);
    $parent_phone = trim($_POST['parent_phone']);

    try {
        $stmt = $pdo->prepare("INSERT INTO patients (full_name, birth_date, gender, address, phone, parent_name, parent_phone) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([$full_name, $birth_date, $gender, $address, $phone, $parent_name, $parent_phone]);
        
        $_SESSION['success_message'] = "Данные пациента успешно добавлены!";
        header('Location: patient_form.php');
        exit;
    } catch (PDOException $e) {
        $_SESSION['error_message'] = "Ошибка при добавлении данных: " . $e->getMessage();
    }
}
?>

<h2>Данные пациента</h2>

<?php if(isset($_SESSION['success_message'])): ?>
    <div class="alert alert-success">
        <?php echo $_SESSION['success_message']; unset($_SESSION['success_message']); ?>
    </div>
<?php endif; ?>

<?php if(isset($_SESSION['error_message'])): ?>
    <div class="alert alert-danger">
        <?php echo $_SESSION['error_message']; unset($_SESSION['error_message']); ?>
    </div>
<?php endif; ?>

<form action="/patient_form.php" method="post" class="medical-form">
    <div class="form-group">
        <label for="full_name">ФИО ребенка:</label>
        <input type="text" id="full_name" name="full_name" required>
    </div>
    
    <div class="form-row">
        <div class="form-group">
            <label for="birth_date">Дата рождения:</label>
            <input type="date" id="birth_date" name="birth_date" required>
        </div>
        
        <div class="form-group">
            <label for="gender">Пол:</label>
            <select id="gender" name="gender" required>
                <option value="">Выберите пол</option>
                <option value="Мужской">Мужской</option>
                <option value="Женский">Женский</option>
            </select>
        </div>
    </div>
    
    <div class="form-group">
        <label for="address">Адрес:</label>
        <textarea id="address" name="address" rows="2" required></textarea>
    </div>
    
    <div class="form-group">
        <label for="phone">Телефон:</label>
        <input type="tel" id="phone" name="phone" required>
    </div>
    
    <div class="form-group">
        <label for="parent_name">ФИО родителя/опекуна:</label>
        <input type="text" id="parent_name" name="parent_name" required>
    </div>
    
    <div class="form-group">
        <label for="parent_phone">Телефон родителя/опекуна:</label>
        <input type="tel" id="parent_phone" name="parent_phone" required>
    </div>
    
    <button type="submit" class="btn btn-primary">Сохранить данные</button>
</form>

<?php
require_once 'includes/footer.php';
?>
