<?php
require_once 'includes/config.php';
require_once 'includes/db.php';

if(!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die("Неверный ID записи");
}

try {
    $stmt = $pdo->prepare("SELECT mr.*, p.full_name as patient_name, p.birth_date, p.gender, 
                          d.full_name as doctor_name, d.specialization
                          FROM medical_records mr
                          JOIN patients p ON mr.patient_id = p.id
                          JOIN doctors d ON mr.doctor_id = d.id
                          WHERE mr.id = ?");
    $stmt->execute([$_GET['id']]);
    $record = $stmt->fetch();
    
    if(!$record) {
        die("Запись не найдена");
    }
} catch (PDOException $e) {
    die("Ошибка базы данных: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Печать истории болезни #<?= $record['id'] ?></title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 12pt; }
        .print-header { text-align: center; margin-bottom: 20px; }
        .print-title { font-size: 16pt; font-weight: bold; }
        .print-section { margin-bottom: 15px; }
        .print-section-title { font-weight: bold; border-bottom: 1px solid #000; margin-bottom: 5px; }
        table.print-table { width: 100%; border-collapse: collapse; margin-bottom: 10px; }
        table.print-table, table.print-table th, table.print-table td { border: 1px solid #000; }
        table.print-table th, table.print-table td { padding: 5px; }
        .print-signature { margin-top: 50px; }
        @media print {
            .no-print { display: none; }
            body { padding: 20px; }
        }
    </style>
</head>
<body>
    <div class="print-header">
        <div class="print-title">Детская поликлиника №1</div>
        <div>История болезни</div>
        <div>Дата формирования: <?= date('d.m.Y') ?></div>
    </div>
    
    <div class="print-section">
        <div class="print-section-title">1. Данные пациента</div>
        <table class="print-table">
            <tr>
                <td width="30%"><strong>ФИО пациента:</strong></td>
                <td><?= htmlspecialchars($record['patient_name']) ?></td>
            </tr>
            <tr>
                <td><strong>Дата рождения:</strong></td>
                <td><?= date('d.m.Y', strtotime($record['birth_date'])) ?></td>
            </tr>
            <tr>
                <td><strong>Пол:</strong></td>
                <td><?= htmlspecialchars($record['gender']) ?></td>
            </tr>
        </table>
    </div>
    
    <div class="print-section">
        <div class="print-section-title">2. Данные приёма</div>
        <table class="print-table">
            <tr>
                <td width="30%"><strong>Дата приёма:</strong></td>
                <td><?= date('d.m.Y', strtotime($record['visit_date'])) ?></td>
            </tr>
            <tr>
                <td><strong>Лечащий врач:</strong></td>
                <td><?= htmlspecialchars($record['doctor_name']) ?> (<?= htmlspecialchars($record['specialization']) ?>)</td>
            </tr>
        </table>
    </div>
    
    <div class="print-section">
        <div class="print-section-title">3. Диагностика</div>
        <p><strong>Симптомы:</strong></p>
        <p><?= nl2br(htmlspecialchars($record['symptoms'])) ?></p>
        
        <p><strong>Диагноз:</strong></p>
        <p><?= nl2br(htmlspecialchars($record['diagnosis'])) ?></p>
    </div>
    
    <div class="print-section">
        <div class="print-section-title">4. Лечение</div>
        <p><?= nl2br(htmlspecialchars($record['treatment'])) ?></p>
    </div>
    
    <?php if(!empty($record['notes'])): ?>
    <div class="print-section">
        <div class="print-section-title">5. Примечания</div>
        <p><?= nl2br(htmlspecialchars($record['notes'])) ?></p>
    </div>
    <?php endif; ?>
    
    <div class="print-signature">
        <table style="width: 100%; border: none;">
            <tr>
                <td width="50%">_________________________<br>Подпись врача</td>


            
<td>_________________________<br>Подпись пациента/родителя</td>
            </tr>
        </table>
    </div>
    
    <div class="no-print" style="margin-top: 20px; text-align: center;">
        <button onclick="window.print()" class="btn btn-primary">
            <i class="fas fa-print"></i> Печать
        </button>
        <button onclick="window.close()" class="btn btn-secondary">
            <i class="fas fa-times"></i> Закрыть
        </button>
    </div>
    
    <script>
    // Автоматическая печать при загрузке (опционально)
    window.onload = function() {
        // Раскомментируйте для автоматической печати
        // setTimeout(function() { window.print(); }, 500);
    };
    </script>
</body>
</html>