<?php
require_once 'includes/config.php';
require_once 'includes/header.php';

if(!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

// Получаем список пациентов и врачей
$patients = $pdo->query("SELECT id, full_name FROM patients ORDER BY full_name")->fetchAll();
$doctors = $pdo->query("SELECT id, full_name, specialization FROM doctors ORDER BY full_name")->fetchAll();

if($_SERVER['REQUEST_METHOD'] == 'POST') {
    $patient_id = trim($_POST['patient_id']);
    $doctor_id = trim($_POST['doctor_id']);
    $diagnosis_date = trim($_POST['diagnosis_date']);
    $diagnosis_text = trim($_POST['diagnosis_text']);
    $treatment_plan = trim($_POST['treatment_plan']);
    $additional_notes = trim($_POST['additional_notes']);

    try {
        $stmt = $pdo->prepare("INSERT INTO diagnoses (patient_id, doctor_id, diagnosis_date, diagnosis_text, treatment_plan, additional_notes) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->execute([$patient_id, $doctor_id, $diagnosis_date, $diagnosis_text, $treatment_plan, $additional_notes]);
        
        $_SESSION['success_message'] = "Диагноз успешно добавлен!";
        header('Location: diagnosis_form.php');
        exit;
    } catch (PDOException $e) {
        $_SESSION['error_message'] = "Ошибка при добавлении диагноза: " . $e->getMessage();
    }
}
?>

<h2>Данные диагноза</h2>

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

<form action="/diagnosis_form.php" method="post" class="medical-form">
    <div class="form-row">
        <div class="form-group">
            <label for="patient_id">Пациент:</label>
            <select id="patient_id" name="patient_id" required>
                <option value="">Выберите пациента</option>
                <?php foreach($patients as $patient): ?>
                    <option value="<?php echo $patient['id']; ?>">
                        <?php echo htmlspecialchars($patient['full_name']); ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        
        <div class="form-group">
            <label for="doctor_id">Врач:</label>
            <select id="doctor_id" name="doctor_id" required>
                <option value="">Выберите врача</option>
                <?php foreach($doctors as $doctor): ?>
                    <option value="<?php echo $doctor['id']; ?>">
                        <?php echo htmlspecialchars($doctor['full_name'] . ' (' . $doctor['specialization'] . ')'); ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
    </div>
    
    <div class="form-group">
        <label for="diagnosis_date">Дата постановки диагноза:</label>
        <input type="date" id="diagnosis_date" name="diagnosis_date" required>
    </div>
    
    <div class="form-group">
        <label for="diagnosis_text">Диагноз:</label>
        <textarea id="diagnosis_text" name="diagnosis_text" rows="3" required></textarea>
    </div>
    
    <div class="form-group">
        <label for="treatment_plan">План лечения:</label>
        <textarea id="treatment_plan" name="treatment_plan" rows="3" required></textarea>
    </div>
    
    <div class="form-group">
        <label for="additional_notes">Дополнительные заметки:</label>
        <textarea id="additional_notes" name="additional_notes" rows="2"></textarea>
    </div>
    
    <button type="submit" class="btn btn-primary">Сохранить диагноз</button>
</form>

<?php
require_once 'includes/footer.php';
?>
