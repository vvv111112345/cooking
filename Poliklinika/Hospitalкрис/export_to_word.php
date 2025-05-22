<?php
require_once 'includes/config.php';
require_once 'includes/db.php';

if(!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header('Location: view_medical_records.php');
    exit;
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
        throw new Exception("Запись не найдена");
    }

    // Генерация Word документа
    header("Content-Type: application/vnd.ms-word");
    header("Content-Disposition: attachment; filename=history_".$record['id'].".doc");
    header("Pragma: no-cache");
    header("Expires: 0");
    
    $html = '<!DOCTYPE html>
    <html>
    <head>
        <meta charset="UTF-8">
        <title>История болезни #'.$record['id'].'</title>
        <style>
            body { font-family: Times New Roman; font-size: 14pt; }
            h1 { text-align: center; }
            .header { text-align: center; margin-bottom: 20px; }
            .section { margin-bottom: 15px; }
            .section-title { font-weight: bold; border-bottom: 1px solid #000; margin-bottom: 5px; }
            table { width: 100%; border-collapse: collapse; }
            table, th, td { border: 1px solid black; }
            th, td { padding: 5px; }
        </style>
    </head>
    <body>
        <div class="header">
            <h1>Детская поликлиника №1</h1>
            <h2>История болезни</h2>
            <p>Дата формирования: '.date('d.m.Y').'</p>
        </div>
        
        <div class="section">
            <div class="section-title">1. Данные пациента</div>
            <table>
                <tr>
                    <td width="30%"><strong>ФИО пациента:</strong></td>
                    <td>'.$record['patient_name'].'</td>
                </tr>
                <tr>
                    <td><strong>Дата рождения:</strong></td>
                    <td>'.date('d.m.Y', strtotime($record['birth_date'])).'</td>
                </tr>
                <tr>
                    <td><strong>Пол:</strong></td>
                    <td>'.$record['gender'].'</td>
                </tr>
            </table>
        </div>
        
        <div class="section">
            <div class="section-title">2. Данные приёма</div>
            <table>
                <tr>
                    <td width="30%"><strong>Дата приёма:</strong></td>
                    <td>'.date('d.m.Y', strtotime($record['visit_date'])).'</td>
                </tr>
                <tr>
                    <td><strong>Лечащий врач:</strong></td>
                    <td>'.$record['doctor_name'].' ('.$record['specialization'].')</td>
                </tr>
            </table>
        </div>
        
        <div class="section">
            <div class="section-title">3. Диагностика</div>
            <p><strong>Симптомы:</strong></p>
            <p>'.nl2br($record['symptoms']).'</p>
            
            <p><strong>Диагноз:</strong></p>
            <p>'.nl2br($record['diagnosis']).'</p>
        </div>
        
        <div class="section">
            <div class="section-title">4. Лечение</div>
            <p>'.nl2br($record['treatment']).'</p>
        </div>';
        
    if(!empty($record['notes'])) {
        $html .= '
        <div class="section">
            <div class="section-title">5. Примечания</div>
            <p>'.nl2br($record['notes']).'</p>
        </div>';
    }
    
    $html .= '
        <div style="margin-top: 50px;">
            <table border="0" style="border: none;">
                <tr>
                    <td width="50%">_________________________<br>Подпись врача</td>
                    <td>_________________________<br>Подпись пациента/родителя</td>
                </tr>
            </table>
        </div>

</body>
    </html>';
    
    echo $html;
    exit;

} catch (Exception $e) {
    $_SESSION['error_message'] = $e->getMessage();
    header('Location: view_medical_records.php');
    exit;
}