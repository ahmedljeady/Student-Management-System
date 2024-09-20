<?php
require('config.php');
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
?>

<!DOCTYPE html>
<html lang="ar">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;700&display=swap" rel="stylesheet">
    <title>تقرير الطالب</title>
    <style>
        body {
            background-color: #f8f9fa;
            display: flex;
            justify-content: center;
            align-items: center;
            font-family: 'Cairo', sans-serif;
            height: 100vh; /* لتوسيع الصفحة لملء الشاشة */
            margin: 0;
        }
        .container {
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            width: 80%; /* يمكن تعديل العرض حسب الحاجة */
            max-width: 600px; /* الحد الأقصى للعرض */
            direction: rtl;
            text-align: center;
        }
        @media print {
            .no-print {
                display: none; /* إخفاء الأزرار عند الطباعة */
            }
        }
    </style>
    <script>
        function printReport() {
            window.print();
        }
    </script>
</head>
<body>
<div class="container">
    <h2 class="text-center">تقرير الطالب</h2>
    <div class="mb-3">
        <strong>ID:</strong> <?= htmlspecialchars($student['id']) ?>
    </div>
    <div class="mb-3">
        <strong>اسم:</strong> <?= htmlspecialchars($student['name']) ?>
    </div>
    <div class="mb-3">
        <strong>رقم الهاتف:</strong> <?= htmlspecialchars($student['phone']) ?>
    </div>
    <div class="mb-3">
        <strong>درجة الامتحان:</strong> <?= htmlspecialchars($student['exam_score']) ?>
    </div>
    <div class="mb-3">
        <strong>الصف الدراسي:</strong> <?= htmlspecialchars($student['grade']) ?>
    </div>
    <div class="mb-3">
        <strong>الشهور المدفوعة:</strong> <?= htmlspecialchars($student['months_paid']) ?>
    </div>
    <div class="mb-3">
        <strong>بتاريخ:</strong> <?= date('Y-m-d') ?>
    </div>

    <div class="no-print">
        <button onclick="printReport()" class="btn btn-primary">طباعة التقرير</button>
        <a href="index.php" class="btn btn-secondary">العودة</a>
    </div>
</div>
</body>
</html>
