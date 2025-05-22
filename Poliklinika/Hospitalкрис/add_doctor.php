<?php 
require 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $fio = $_POST['fio'];
    $doljinost = $_POST['doljinost'];
    $ctaj = $_POST['ctaj'];
    $zvanie = $_POST['zvanie'];
    $address = $_POST['address'];
    
    $stmt = $pdo->prepare("INSERT INTO Врачи (FIO, Doljinost, ctaj, zvanie, address) VALUES (?, ?, ?, ?, ?)");
    $stmt->execute([$fio, $doljinost, $ctaj, $zvanie, $address]);
    
    header("Location: index.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Добавить врача</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="style.css">
</head>
<body>
 
    
    <div class="container">
        <div class="card">
            <h1 class="flex items-center gap-4">
                <i class="fas fa-user-md icon"></i>
                Добавить нового врача
            </h1>
            
            <form method="POST">
                <div class="form-group">
                    <label for="fio">ФИО врача</label>
                    <input type="text" id="FIO" name="FIO" placeholder="Иванов Иван Иванович" required>
                </div>
                
                <div class="form-group">
                    <label for="doljinost">Должность</label>
                    <input type="text" id="doljinost" name="doljinost" placeholder="Хирург" required>
                </div>
                
                <div class="form-group">
                    <label for="ctaj">Стаж (лет)</label>
                    <input type="number" id="ctaj" name="ctaj" min="0" max="50" required>
                </div>
                
                <div class="form-group">
                    <label for="zvanie">Звание</label>
                    <input type="text" id="zvanie" name="zvanie" placeholder="Кандидат медицинских наук" required>
                </div>
                
                <div class="form-group">
                    <label for="address">Адрес</label>
                    <input type="text" id="address" name="address" placeholder="ул. Примерная, д. 123" required>
                </div>
                
                <div class="flex gap-4 mt-3">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save icon"></i> Сохранить
                    </button>
                    <a href="index.php" class="btn btn-outline">
                        <i class="fas fa-times icon"></i> Отмена
                    </a>
                </div>
            </form>
        </div>
    </div>
</body>
</html>