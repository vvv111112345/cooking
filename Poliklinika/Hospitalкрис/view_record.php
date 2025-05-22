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
    
    $stmt = $pdo->prepare("INSERT INTO История болезни (diagnoz, data_zabolevania, data_thecure, vid_lechenia, id_patient, id_vrach, id_operaz) VALUES (?, ?, ?, ?, ?, ?, ?)");
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
    <title>Лист нетрудоспособности</title>
    <link rel="stylesheet" href="style.css">
    <style>
         .button-container {
            margin-top: 20px;
            display: flex;
            gap: 10px;
        }
        .button {
            padding: 10px 20px;
            background-color: #4CAF50;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
        }
        .button.print {
            background-color:rgb(6, 123, 29);
        }
        .button:hover {
            opacity: 0.8;
        }
        
        @media print {
            .button-container, .no-print {
                display: none !important;
            }
            body {
                font-family: "Times New Roman", Times, serif;
                font-size: 12pt;
            }
            .container {
                width: 100%;
                margin: 0;
                padding: 0;
            }
            h1 {
                font-size: 14pt;
                text-align: center;
                margin-bottom: 20pt;
            }
            .form-group {
                margin-bottom: 10pt;
            }
            input, select {
                border: none;
                border-bottom: 1px solid #000;
                background: transparent;
                padding: 0;
                margin: 0;
                font-family: inherit;
                font-size: inherit;
            }
            select {
                -webkit-appearance: none;
                -moz-appearance: none;
                appearance: none;
                border: none;
                border-bottom: 1px solid #000;
            }
            label {
                font-weight: normal;
            }
            
            }
            input[type="radio"] + label:before {
                content: "○";
                margin-right: 5px;
            }
            input[type="radio"]:checked + label:before {
                content: "●";
            }
        
    </style>
</head>
<body>
    <div class="container">
        <h1>Лист нетрудоспособности</h1>

       <form method="POST">
    <p>
        <label style="display: inline-flex; align-items: center; margin-right: 15px;">
            <input type="checkbox" name="document_type" value="primary" 
                   class="exclusive-checkbox"
                   style="margin: 0 5px 0 0; width: 16px; height: 16px;" 
                   <?= (empty($_POST['document_type']) || $_POST['document_type'] == 'primary' ? 'checked' : '') ?>> 
            Первичный
        </label>
        <label style="display: inline-flex; align-items: center;">
            <input type="checkbox" name="document_type" value="duplicate" 
                   class="exclusive-checkbox"
                   style="margin: 0 5px 0 0; width: 16px; height: 16px;"
                   <?= (!empty($_POST['document_type']) && $_POST['document_type'] == 'duplicate' ? 'checked' : '') ?>>
            Дубликат
        </label>
    </p>
</form>

<script>
document.querySelectorAll('.exclusive-checkbox').forEach(checkbox => {
    checkbox.addEventListener('change', function() {
        if (this.checked) {
            document.querySelectorAll('.exclusive-checkbox').forEach(cb => {
                if (cb !== this) cb.checked = false;
            });
        }
    });
});
</script>

            <div class="form-group">
                <select name="diagnoz" required>
                    <option value="Поликлиника №5">Поликлиника №5</option>
                </select>
                <label>(наименование медицинской организации)</label>
                <select name="diagnoz" required>
                    <option value="450005, республика Башкортостан, город Уфа, Мингажева улица, дом 59.">450005, республика Башкортостан, город Уфа, Мингажева улица, дом 59.</option>
                </select>
                <label>(адрес медицинской организации)</label>
            </div>
            <div class="form-group">
                <label>Дата выдачи:</label>
                <input type="date" name="data_zabolevania" required>
            </div>
            
            <div class="form-group">
                <label>Пациент:</label>
                <select name="id_patient" required>
                    <?php foreach ($patients as $patient): ?>
                        <option value="<?= $patient['id'] ?>"><?= $patient['FIO'] ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <form method="POST">
    <p>
        <label style="display: inline-flex; align-items: center; margin-right: 15px;">
            <input type="checkbox" name="document_type" value="primary" 
                   class="exclusive-checkbox"
                   style="margin: 0 5px 0 0; width: 16px; height: 16px;" 
                   <?= (empty($_POST['document_type']) || $_POST['document_type'] == 'primary' ? 'checked' : '') ?>> 
            Ж
        </label>
        <label style="display: inline-flex; align-items: center;">
            <input type="checkbox" name="document_type" value="duplicate" 
                   class="exclusive-checkbox"
                   style="margin: 0 5px 0 0; width: 16px; height: 16px;"
                   <?= (!empty($_POST['document_type']) && $_POST['document_type'] == 'duplicate' ? 'checked' : '') ?>>
            М
        </label>
    </p>
</form>

<script>
document.querySelectorAll('.exclusive-checkbox').forEach(checkbox => {
    checkbox.addEventListener('change', function() {
        if (this.checked) {
            document.querySelectorAll('.exclusive-checkbox').forEach(cb => {
                if (cb !== this) cb.checked = false;
            });
        }
    });
});
</script>

            <div class="form-group">
                <label>Дата рождения:</label>
<input type="date" name="birth_date" required>
            </div>
            <div class="form-group">
                <label>Причина нетрудоспособности:</label>
                <input type="text" name="reason" required>
                <label>(код)</label>
            </div>
            <form method="POST">
    <p>
        <label style="display: inline-flex; align-items: center; margin-right: 15px;">
            <input type="checkbox" name="document_type" value="primary" 
                   class="exclusive-checkbox"
                   style="margin: 0 5px 0 0; width: 16px; height: 16px;" 
                   <?= (empty($_POST['document_type']) || $_POST['document_type'] == 'primary' ? 'checked' : '') ?>> 
            Основное
        </label>
        <label style="display: inline-flex; align-items: center;">
            <input type="checkbox" name="document_type" value="duplicate" 
                   class="exclusive-checkbox"
                   style="margin: 0 5px 0 0; width: 16px; height: 16px;"
                   <?= (!empty($_POST['document_type']) && $_POST['document_type'] == 'duplicate' ? 'checked' : '') ?>>
            По совместительству
        </label>
    </p>
</form>

<script>
document.querySelectorAll('.exclusive-checkbox').forEach(checkbox => {
    checkbox.addEventListener('change', function() {
        if (this.checked) {
            document.querySelectorAll('.exclusive-checkbox').forEach(cb => {
                if (cb !== this) cb.checked = false;
            });
        }
    });
});
</script>
            <div class="form-group">
                 <select name="mesto rad" required>
                    <option value="студент">студент</option>
                    <option value="МБОУ “Средняя школа № 12”">МБОУ “Средняя школа № 12”</option>
                    <option value="ФГБУ “Научно-исследовательский институт им. Иванова”">ФГБУ “Научно-исследовательский институт им. Иванова”</option>
                    <option value="Министерство финансов Российской Федерации">Министерство финансов Российской Федерации</option>
                    <option value="ПАО “Газпром”">ПАО “Газпром”</option>
                </select>
                <label>(место работы-наименование организации)</label>
            </div>
            

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
             <div class="button-container">
            <button type="submit" class="button print">Добавить</button>
            <a href="index.php" class="button print">Отмена</a>
                <button type="button" class="button print" onclick="window.print()">Печатать</button>
                <a href="index.php" class="button print">На главную</a>
            </div>
        </form>
    </div>

</body>
</html>