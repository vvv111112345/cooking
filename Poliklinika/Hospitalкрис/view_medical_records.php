<?php
require_once 'includes/config.php';
require_once 'includes/db.php';

// Проверка авторизации
if(!isset($_SESSION['user_id'])) {
    $_SESSION['error_message'] = "Для доступа необходимо авторизоваться";
    header('Location: login.php');
    exit;
}

// Получение записей из базы данных
try {
    $query = "SELECT mr.*, p.full_name as patient_name, d.full_name as doctor_name 
              FROM medical_records mr
              JOIN patients p ON mr.patient_id = p.id
              JOIN doctors d ON mr.doctor_id = d.id
              ORDER BY mr.visit_date DESC";
    
    $records = $pdo->query($query)->fetchAll();
} catch (PDOException $e) {
    $_SESSION['error_message'] = "Ошибка загрузки данных: " . $e->getMessage();
}

require_once 'includes/header.php';
?>

<div class="container">
    <h2><i class="fas fa-file-medical-alt"></i> История болезней</h2>
    
    <?php if(isset($_SESSION['error_message'])): ?>
        <div class="alert alert-danger">
            <?php echo $_SESSION['error_message']; unset($_SESSION['error_message']); ?>
        </div>
    <?php endif; ?>
    
    <div class="mb-3">
        <a href="medical_record_form.php" class="btn btn-primary">
            <i class="fas fa-plus"></i> Новая запись
        </a>
    </div>
    
    <?php if(!empty($records)): ?>
        <div class="table-responsive">
            <table class="table table-bordered table-hover">
                <thead class="thead-dark">
                    <tr>
                        <th>Дата</th>
                        <th>Пациент</th>
                        <th>Врач</th>
                        <th>Диагноз</th>
                        <th>Действия</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($records as $record): ?>
                        <tr>
                            <td><?= date('d.m.Y', strtotime($record['visit_date'])) ?></td>
                            <td><?= htmlspecialchars($record['patient_name']) ?></td>
                            <td><?= htmlspecialchars($record['doctor_name']) ?></td>
                            <td><?= mb_strimwidth(htmlspecialchars($record['diagnosis']), 0, 50, '...') ?>
                            
                                
                                <?php if($_SESSION['role'] == 'admin' || $_SESSION['role'] == 'doctor'): ?>
                                    
                                <?php endif; ?>
                            </td>
                            <!-- В таблице, в колонке "Действия" -->
<td>
    <a href="view_record.php?id=<?= $record['id'] ?>" class="btn btn-sm btn-info" title="Просмотр">
        <i class="fas fa-eye"></i>
    </a>
    <a href="export_to_word.php?id=<?= $record['id'] ?>" class="btn btn-sm btn-success" title="Экспорт в Word">
        <i class="fas fa-file-word"></i>
    </a>
    <button onclick="printRecord(<?= $record['id'] ?>)" class="btn btn-sm btn-primary" title="Печать">
        <i class="fas fa-print"></i>
    </button>
    <?php if($_SESSION['role'] == 'admin' || $_SESSION['role'] == 'doctor'): ?>
        <a href="edit_record.php?id=<?= $record['id'] ?>" class="btn btn-sm btn-warning" title="Редактировать">
            <i class="fas fa-edit"></i>
        </a>
    <?php endif; ?>
</td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php else: ?>
        <div class="alert alert-info">Нет записей в истории болезней</div>
    <?php endif; ?>
</div>

<?php
require_once 'includes/footer.php';
?>