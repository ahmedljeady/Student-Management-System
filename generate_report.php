<?php
require('config.php');
require_once __DIR__ . '/vendor/autoload.php'; // تأكد من تضمين autoload لـ mpdf

session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php?error=يرجى تسجيل الدخول');
    exit();
}

if (!isset($_GET['id'])) {
    header('Location: index.php?error=رقم الطالب غير محدد');
    exit();
}

$id = intval($_GET['id']);
$stmt = $conn->prepare("SELECT * FROM students WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    header('Location: index.php?error=لا يوجد طالب بهذا الرقم');
    exit();
}

$student = $result->fetch_assoc();

// إعداد mpdf
$mpdf = new \Mpdf\Mpdf();

// إعداد محتوى PDF
$html = '
<!DOCTYPE html>
<html lang="ar">
<head>
    <meta charset="UTF-8">
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;700&display=swap" rel="stylesheet">
    <style>
        body {
            text-align: center;
            font-family: "Cairo", regular;
            font-size: 14px;
        }
        .container {
            margin: 0 auto;
            width: 80%; /* يمكنك تعديل العرض حسب الحاجة */
        }
        h2 {
            margin-bottom: 20px;
        }
        .mb-3 {
            margin-bottom: 10px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>تقرير الطالب</h2>
        <div class="mb-3"><strong>ID:</strong> ' . htmlspecialchars($student['id']) . '</div>
        <div class="mb-3"><strong>اسم:</strong> ' . htmlspecialchars($student['name']) . '</div>
        <div class="mb-3"><strong>رقم الهاتف:</strong> ' . htmlspecialchars($student['phone']) . '</div>
        <div class="mb-3"><strong>درجة الامتحان:</strong> ' . htmlspecialchars($student['exam_score']) . '</div>
        <div class="mb-3"><strong>الصف الدراسي:</strong> ' . htmlspecialchars($student['grade']) . '</div>
        <div class="mb-3"><strong>الشهور المدفوعة:</strong> ' . htmlspecialchars($student['months_paid']) . '</div>
    </div>
</body>
</html>
';

$mpdf->WriteHTML($html);
$mpdf->Output($student['name'] . '_تقرير.pdf', 'D'); // حفظ باسم الطالب
exit();
