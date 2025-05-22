<?php
require_once 'includes/config.php';
require_once 'includes/db.php';

if(!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

// Получаем список пациентов и врачей
$patients = $pdo->query("SELECT id, full_name FROM patients ORDER BY full_name")->fetchAll();
$doctors = $pdo->query("SELECT id, full_name, specialization FROM doctors ORDER BY full_name")->fetchAll();

if($_SERVER['REQUEST_METHOD'] == 'POST') {
    $patient_id = $_POST['patient_id'];
    $doctor_id = $_POST['doctor_id'];
    $visit_date = $_POST['visit_date'];
    $symptoms = trim($_POST['symptoms']);
    $diagnosis = trim($_POST['diagnosis']);
    $treatment = trim($_POST['treatment']);
    $notes = trim($_POST['notes']);

    try {
        $stmt = $pdo->prepare("INSERT INTO medical_records (patient_id, doctor_id, visit_date, symptoms, diagnosis, treatment, notes) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([$patient_id, $doctor_id, $visit_date, $symptoms, $diagnosis, $treatment, $notes]);
        
        $_SESSION['success_message'] = "Запись в истории болезни успешно добавлена!";
        header('Location: view_medical_records.php');
        exit;
    } catch (PDOException $e) {
        $_SESSION['error_message'] = "Ошибка при добавлении записи: " . $e->getMessage();
    }
}

require_once 'includes/header.php';
?>

<h2>Добавление записи в историю болезни</h2>

<?php if(isset($_SESSION['error_message'])): ?>
    <div class="alert alert-danger">
        <?php echo $_SESSION['error_message']; unset($_SESSION['error_message']); ?>
    </div>
<?php endif; ?>

<form action="medical_record_form.php" method="post" class="medical-form">
    <div class="form-row">
        <div class="form-group">
            <label for="patient_id">Пациент:</label>
            <select id="patient_id" name="patient_id" required>
                <option value="">Выберите пациента</option>
                <?php foreach($patients as $patient): ?>
                    <option value="<?= $patient['id'] ?>">
                        <?= htmlspecialchars($patient['full_name']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        
        <div class="form-group">
            <label for="doctor_id">Врач:</label>
            <select id="doctor_id" name="doctor_id" required>
                <option value="">Выберите врача</option>
                <?php foreach($doctors as $doctor): ?>
                    <option value="<?= $doctor['id'] ?>">
                        <?= htmlspecialchars($doctor['full_name'] . ' (' . $doctor['specialization'] . ')') ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
    </div>
    
    <div class="form-group">
        <label for="visit_date">Дата приёма:</label>
        <input type="date" id="visit_date" name="visit_date" required value="<?= date('Y-m-d') ?>">
    </div>
    
    <div class="form-group">
        <label for="symptoms">Симптомы:</label>
        <textarea id="symptoms" name="symptoms" rows="3" required></textarea>
    </div>
    
    <div class="form-group">
        <label for="diagnosis">Диагноз:</label>
        <textarea id="diagnosis" name="diagnosis" rows="3" required></textarea>
    </div>
    
    <div class="form-group">
        <label for="treatment">Лечение:</label>
        <textarea id="treatment" name="treatment" rows="3" required></textarea>
    </div>
    
    <div class="form-group">
        <label for="notes">Примечания:</label>
        <textarea id="notes" name="notes" rows="2"></textarea>
    </div>
    
    <button type="submit" class="btn btn-primary">Сохранить запись</button>
    <a href="view_medical_records.php" class="btn btn-secondary">Отмена</a>
</form>

<?php
require_once 'includes/footer.php';
?>