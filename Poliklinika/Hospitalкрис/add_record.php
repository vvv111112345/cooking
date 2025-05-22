<?php 
require 'db.php';

// Получаем списки для выпадающих меню
$patients = $pdo->query("SELECT id, FIO FROM Пациенты")->fetchAll();
$doctors = $pdo->query("SELECT id, FIO FROM Врачи")->fetchAll();
$operations = $pdo->query("SELECT id, opicanie_opera FROM Операция")->fetchAll();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $diagnoz = $_POST['diagnoz'];
    $data_zabolevania = $_POST['data_zabolevania'];
    $data_thecure = $_POST['data_thecure'];
    $vid_lechenia = $_POST['vid_lechenia'];
    $id_patient = $_POST['id_patient'];
    $id_vrach = $_POST['id_vrach'];
    $id_operaz = $_POST['id_operaz'];
    
    $stmt = $pdo->prepare("INSERT INTO `История болезни` (diagnoz, data_zabolevania, data_thecure, vid_lechenia, id_patient, id_vrach, id_operaz) VALUES (?, ?, ?, ?, ?, ?, ?)");
    $stmt->execute([$diagnoz, $data_zabolevania, $data_thecure, $vid_lechenia, $id_patient, $id_vrach, $id_operaz]);
    
    header("Location: index.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Добавить запись в историю болезни</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <h1>Добавить запись в историю болезни</h1>
        
        <form method="POST">
            <div class="form-group">
                <label>Диагноз:</label>
                <select name="diagnoz" required>
                    <option value="дисфункция трансплантата сердца">дисфункция трансплантата сердца</option>
                    <option value="рак молочных желез">рак молочных желез</option>
                    <option value="острый деструктивный холецистит">острый деструктивный холецистит</option>
                    <option value="Нарушения глотательной функции">Нарушения глотательной функции</option>
                    <option value="кишечная непроходимость">кишечная непроходимость</option>
                </select>
            </div>
            
            <div class="form-group">
                <label>Дата заболевания:</label>
                <input type="date" name="data_zabolevania" required>
            </div>
            
            <div class="form-group">
                <label>Дата лечения:</label>
                <input type="date" name="data_thecure" required>
            </div>
            
            <div class="form-group">
                <label>Вид лечения:</label>
                <select name="vid_lechenia" required>
                    <option value="Медикаментозная терапия">Медикаментозная терапия</option>
                    <option value="Химиотерапия">Химиотерапия</option>
                    <option value="Медикаментозная терапия">Медикаментозная терапия</option>
                    <option value="Физическая терапия">Физическая терапия</option>
                    <option value="Хирургическое вмешательство">Хирургическое вмешательство</option>
                </select>
            </div>
            
            <div class="form-group">
                <label>Пациент:</label>
                <select name="id_patient" required>
                    <?php foreach ($patients as $patient): ?>
                        <option value="<?= $patient['id'] ?>"><?= $patient['FIO'] ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <div class="form-group">
                <label>Врач:</label>
                <select name="id_vrach" required>
                    <?php foreach ($doctors as $doctor): ?>
                        <option value="<?= $doctor['id'] ?>"><?= $doctor['FIO'] ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <div class="form-group">
                <label>Операция:</label>
                <select name="id_operaz" required>
                    <?php foreach ($operations as $operation): ?>
                        <option value="<?= $operation['id'] ?>"><?= substr($operation['opicanie_opera'], 0, 250) ?>...</option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <button type="submit" class="btn">Добавить</button>
            <a href="index.php" class="btn cancel">Отмена</a>
        </form>
    </div>
</body>
</html>