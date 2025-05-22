<?php 
require 'db.php';

$patients = $pdo->query("SELECT id, FIO FROM Пациенты")->fetchAll();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $opicanie_opera = $_POST['opicanie_opera'];
    $data_opera = $_POST['data_opera'];
    $pezyltat_oper = $_POST['pezyltat_oper'];
    $id_patient = $_POST['id_patient'];
    
    $stmt = $pdo->prepare("INSERT INTO Операция (opicanie_opera, data_opera, pezyltat_oper, id_patient) VALUES (?, ?, ?, ?)");
    $stmt->execute([$opicanie_opera, $data_opera, $pezyltat_oper, $id_patient]);
    
    header("Location: index.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Добавить операцию</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <h1>Добавить новую операцию</h1>
        
        <form method="POST">
            <div class="form-group">
                <label>Описание операции:</label>
                <textarea name="opicanie_opera" rows="4" required></textarea>
            </div>
            
            <div class="form-group">
                <label>Дата операции:</label>
                <input type="date" name="data_opera" required>
            </div>
            
            <div class="form-group">
                <label>Результат операции:</label>
                <select name="pezyltat_oper" required>
                    <option value="Ближайшие результаты">Ближайшие результаты</option>
                    <option value="Средний период">Средний период</option>
                    <option value="отдалённый">Отдалённый</option>
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
            
            <button type="submit" class="btn">Добавить</button>
            <a href="index.php" class="btn cancel">Отмена</a>
        </form>
    </div>
</body>
</html>