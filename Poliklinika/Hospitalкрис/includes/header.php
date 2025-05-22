<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($page_title) ? $page_title . ' | ' . SITE_NAME : SITE_NAME; ?></title>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700&family=Montserrat:wght@400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body>
    <header class="main-header">
        <div class="container">
            <div class="logo">
                <h1><i class="fas fa-hospital-alt"></i> Детская поликлиника №1</h1>
                <p>Формирование истории болезни</p>
            </div>
            <nav class="main-nav">
                <ul>
                    <li><a href="/index.php"><i class="fas fa-home"></i> Главная</a></li>
                    <?php if(isset($_SESSION['user_id'])): ?>
                        <li><a href="/patient_form.php"><i class="fas fa-user-plus"></i> Добавить пациента</a></li>
                        <li><a href="doctor_form.php"><i class="fas fa-user-md"></i> Добавить врача</a></li>
                        <li><a href="/diagnosis_form.php"><i class="fas fa-notes-medical"></i> Добавить диагноз</a></li>
                        <li>  <a href="view_medical_records.php"> <i class="fas fa-file-medical-alt"></i> История болезни </a></li>
                        <li><a href="/logout.php"><i class="fas fa-sign-out-alt"></i> Выход</a></li>
                    <?php else: ?>
                        <li><a href="/login.php"><i class="fas fa-sign-in-alt"></i> Вход</a></li>
                        <li><a href="/register.php"><i class="fas fa-user-plus"></i> Регистрация</a></li>
                    <?php endif; ?>
                </ul>
            </nav>
        </div>
    </header>
    <main class="container">
