<?php require 'db.php'; ?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Система учета истории болезни</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="style.css">
</head>
<body>
    
    <div class="container">
        <h1 class="flex items-center gap-4">
            <i class="fas fa-clipboard-list icon"></i>
            Панель управления
        </h1>
        
        <div class="card">
            <h2><i class="fas fa-tachometer-alt icon"></i> Быстрые действия</h2>
            
            <div class="flex gap-4" style="flex-wrap: wrap;">
                <a href="add.patient.php" class="btn btn-primary">
                    <i class="fas fa-user-plus icon"></i> Добавить пациента
                </a>
                <a href="add_doctor.php" class="btn btn-primary">
                    <i class="fas fa-user-md icon"></i> Добавить врача
                </a>
                <a href="add_operation.php" class="btn btn-primary">
                    <i class="fas fa-procedures icon"></i> Добавить операцию
                </a>
                <a href="add_record.php" class="btn btn-primary">
                    <i class="fas fa-file-medical icon"></i> Добавить запись
                </a>
                <a href="view_record.php" class="btn btn-primary">
                    <i class="fas fa-file-alt icon"></i> Больничные листы
                </a>
            </div>
        </div>
        
        <div class="card">
            <h2><i class="fas fa-chart-bar icon"></i> Статистика</h2>
            
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1.5rem;">
                <?php
                try {
                    $patients = $pdo->query("SELECT COUNT(*) FROM Пациенты")->fetchColumn();
                    echo '<div class="card">
                        <h3>Пациентов</h3>
                        <p style="font-size: 2rem; font-weight: 700; color: var(--primary);">'.$patients.'</p>
                    </div>';
                } catch (PDOException $e) {
                    echo '<div class="alert alert-danger">
                        <i class="fas fa-exclamation-circle icon"></i> Таблица пациентов не доступна
                    </div>';
                }
                
                try {
                    $doctors = $pdo->query("SELECT COUNT(*) FROM Врачи")->fetchColumn();
                    echo '<div class="card">
                        <h3>Врачей</h3>
                        <p style="font-size: 2rem; font-weight: 700; color: var(--primary);">'.$doctors.'</p>
                    </div>';
                } catch (PDOException $e) {
                    echo '<div class="alert alert-danger">
                        <i class="fas fa-exclamation
                </div>';
                }