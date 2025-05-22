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

// Получаем данные записи для редактирования, если передан ID
$record = null;
if(isset($_GET['id'])) {
    $record_id = $_GET['id'];
    $stmt = $pdo->prepare("SELECT * FROM medical_records WHERE id = ?");
    $stmt->execute([$record_id]);
    $record = $stmt->fetch();
    
    if(!$record) {
        $_SESSION['error_message'] = "Запись в истории болезни не найдена!";
        header('Location: view_medical_records.php');
        exit;
    }
}

if($_SERVER['REQUEST_METHOD'] == 'POST') {
    $record_id = $_POST['record_id'];
    $patient_id = $_POST['patient_id'];
    $doctor_id = $_POST['doctor_id'];
    $visit_date = $_POST['visit_date'];
    $symptoms = trim($_POST['symptoms']);
    $diagnosis = trim($_POST['diagnosis']);
    $treatment = trim($_POST['treatment']);
    $notes = trim($_POST['notes']);

    try {
        $stmt = $pdo->prepare("UPDATE medical_records SET patient_id = ?, doctor_id = ?, visit_date = ?, symptoms = ?, diagnosis = ?, treatment = ?, notes = ? WHERE id = ?");
        $stmt->execute([$patient_id, $doctor_id, $visit_date, $symptoms, $diagnosis, $treatment, $notes, $record_id]);
        
        $_SESSION['success_message'] = "Запись в истории болезни успешно обновлена!";
        header('Location: view_medical_records.php');
        exit;
    } catch (PDOException $e) {
        $_SESSION['error_message'] = "Ошибка при обновлении записи: " . $e->getMessage();
    }
}

require_once 'includes/header.php';
?>

<h2><?= isset($record) ? 'Редактирование' : 'Добавление' ?> записи в историю болезни</h2>

<?php if(isset($_SESSION['error_message'])): ?>
    <div class="alert alert-danger">
        <?php echo $_SESSION['error_message']; unset($_SESSION['error_message']); ?>
    </div>
<?php endif; ?>

<form action="edit_record.php" method="post" class="medical-form">
    <?php if(isset($record)): ?>
        <input type="hidden" name="record_id" value="<?= $record['id'] ?>">
    <?php endif; ?>
    
    <div class="form-row">
        <div class="form-group">
            <label for="patient_id">Пациент:</label>
            <select id="patient_id" name="patient_id" required>
                <option value="">Выберите пациента</option>
                <?php foreach($patients as $patient): ?>
                    <option value="<?= $patient['id'] ?>" <?= (isset($record) && $record['patient_id'] == $patient['id']) ? 'selected' : '' ?>>
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
                    <option value="<?= $doctor['id'] ?>" <?= (isset($record) && $record['doctor_id'] == $doctor['id']) ? 'selected' : '' ?>>
                        <?= htmlspecialchars($doctor['full_name'] . ' (' . $doctor['specialization'] . ')') ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
    </div>
    
    <div class="form-group">
        <label for="visit_date">Дата приёма:</label>
        <input type="date" id="visit_date" name="visit_date" required 
               value="<?= isset($record) ? htmlspecialchars($record['visit_date']) : date('Y-m-d') ?>">
    </div>
    
    <div class="form-group">
        <label for="symptoms">Симптомы:</label>
        <textarea id="symptoms" name="symptoms" rows="3" required><?= isset($record) ? htmlspecialchars($record['symptoms']) : '' ?></textarea>
    </div>
    
    <div class="form-group">
        <label for="diagnosis">Диагноз:</label>
        <textarea id="diagnosis" name="diagnosis" rows="3" required><?= isset($record) ? htmlspecialchars($record['diagnosis']) : '' ?></textarea>
    </div>
    
    <div class="form-group">
        <label for="treatment">Лечение:</label>
        <textarea id="treatment" name="treatment" rows="3" required><?= isset($record) ? htmlspecialchars($record['treatment']) : '' ?></textarea>
    </div>
    
    <div class="form-group">
        <label for="notes">Примечания:</label>
        <textarea id="notes" name="notes" rows="2"><?= isset($record) ? htmlspecialchars($record['notes']) : '' ?></textarea>
    </div>
    
    <button type="submit" class="btn btn-primary"><?= isset($record) ? 'Обновить' : 'Сохранить' ?> запись</button>
    <a href="view_medical_records.php" class="btn btn-secondary">Отмена</a>
</form>

<?php
require_once 'includes/footer.php';
?>