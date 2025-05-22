<?php
require_once 'includes/config.php';
require_once 'includes/header.php';

if(!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

if(!isset($_GET['patient_id'])) {
    header('Location: view_history.php');
    exit;
}

$patient_id = $_GET['patient_id'];

// Получаем данные пациента
$patient = $pdo->prepare("SELECT * FROM patients WHERE id = ?");
$patient->execute([$patient_id]);
$patient = $patient->fetch();

if(!$patient) {
    $_SESSION['error_message'] = "Пациент не найден";
    header('Location: view_history.php');
    exit;
}

// Получаем все диагнозы пациента
$diagnoses = $pdo->prepare("
    SELECT d.*, dr.full_name as doctor_name, dr.specialization 
    FROM diagnoses d
    JOIN doctors dr ON d.doctor_id = dr.id
    WHERE d.patient_id = ?
    ORDER BY d.diagnosis_date DESC
");
$diagnoses->execute([$patient_id]);
$diagnoses = $diagnoses->fetchAll();
?>

<h2>История болезни пациента: <?php echo htmlspecialchars($patient['full_name']); ?></h2>

<div class="medical-history">
    <div class="history-header">
        <div class="history-logo">
            <h1><i class="fas fa-hospital-alt"></i> Детская поликлиника №1</h1>
            <p>История болезни</p>
        </div>
        <div class="history-date">
            <p>Дата формирования: <?php echo date('d.m.Y'); ?></p>
        </div>
    </div>
    
    <div class="section">
        <h3>1. Данные пациента</h3>
        <div class="patient-info">
            <p><strong>ФИО:</strong> <?php echo htmlspecialchars($patient['full_name']); ?></p>
            <p><strong>Дата рождения:</strong> <?php echo date('d.m.Y', strtotime($patient['birth_date'])); ?></p>
            <p><strong>Пол:</strong> <?php echo htmlspecialchars($patient['gender']); ?></p>
            <p><strong>Адрес:</strong> <?php echo htmlspecialchars($patient['address']); ?></p>
            <p><strong>Телефон:</strong> <?php echo htmlspecialchars($patient['phone']); ?></p>
            <p><strong>Родитель/опекун:</strong> <?php echo htmlspecialchars($patient['parent_name']); ?></p>
            <p><strong>Телефон родителя:</strong> <?php echo htmlspecialchars($patient['parent_phone']); ?></p>
            <p><strong>Дата регистрации:</strong> <?php echo date('d.m.Y H:i', strtotime($patient['registration_date'])); ?></p>
        </div>
    </div>
    
    <div class="section">
        <h3>2. Медицинские данные</h3>
        <?php if(count($diagnoses) > 0): ?>
            <?php foreach($diagnoses as $diagnosis): ?>
                <div class="diagnosis-item">
                    <h4>Диагноз от <?php echo date('d.m.Y', strtotime($diagnosis['diagnosis_date'])); ?></h4>
                    <p><strong>Врач:</strong> <?php echo htmlspecialchars($diagnosis['doctor_name'] . ' (' . htmlspecialchars($diagnosis['specialization']) . ')'; ?></p>
                    <p><strong>Диагноз:</strong> <?php echo nl2br(htmlspecialchars($diagnosis['diagnosis_text'])); ?></p>
                    <p><strong>План лечения:</strong> <?php echo nl2br(htmlspecialchars($diagnosis['treatment_plan'])); ?></p>
                    <?php if(!empty($diagnosis['additional_notes'])): ?>
                        <p><strong>Дополнительные заметки:</strong> <?php echo nl2br(htmlspecialchars($diagnosis['additional_notes'])); ?></p>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p>Нет данных о диагнозах</p>
        <?php endif; ?>
    </div>
    
    <div class="history-footer">
        <div class="signature">
            <p>Главный врач: _________________________</p>
            <p>Дата: <?php echo date('d.m.Y'); ?></p>
        </div>
    </div>
    
    <div class="print-actions">
        <button onclick="window.print()" class="btn btn-primary"><i class="fas fa-print"></i> Печать</button>
        <a href="/view_history.php" class="btn btn-secondary">Назад</a>
    </div>
</div>

<?php
require_once 'includes/footer.php';
?>
