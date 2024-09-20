<?php
require('config.php');

session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php?error=يرجى تسجيل الدخول');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // إضافة طالب جديد
    $name = trim($_POST['name']);
    $phone = trim($_POST['phone']);
    $exam_score = trim($_POST['exam_score']);
    $class = trim($_POST['class']);
    $months_paid = trim($_POST['months_paid']);

    // التحقق من المدخلات
    if (!empty($name) && is_numeric($exam_score)) {
        $stmt = $conn->prepare("INSERT INTO students (name, phone, exam_score, grade, months_paid) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param('ssdss', $name, $phone, $exam_score, $class, $months_paid);
        $stmt->execute();

        header('Location: index.php?succ=تمت الإضافة بنجاح!');
        exit();
    } else {
        $error_message = "الاسم مطلوب ودرجة الامتحان يجب أن تكون رقمًا.";
    }
}
?>

<!DOCTYPE html>
<html lang="ar">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;700&display=swap" rel="stylesheet">
    <title>تسجيل طالب جديد</title>
    <style>
        body {
            background-color: #f8f9fa;
            font-family: 'Cairo', sans-serif;
        }
        .container {
            margin-top: 20px;
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }
    </style>
</head>
<body>
<div class="container">
    <h2>تسجيل طالب جديد</h2>
    
    <?php if (isset($error_message)) echo "<div class='alert alert-danger'>$error_message</div>"; ?>

    <form method="POST">
        <div class="form-group">
            <label for="name">اسم الطالب</label>
            <input type="text" class="form-control" id="name" name="name" required>
        </div>
        <div class="form-group">
            <label for="phone">رقم الهاتف</label>
            <input type="text" class="form-control" id="phone" name="phone">
        </div>
        <div class="form-group">
            <label for="exam_score">درجة الامتحان</label>
            <input type="number" step="0.01" class="form-control" id="exam_score" name="exam_score" required>
        </div>
        <div class="form-group">
            <label for="class">الصف الدراسي</label>
            <input type="text" class="form-control" id="class" name="class" required>
        </div>
        <div class="form-group">
            <label for="months_paid">الشهور المدفوعة</label>
            <input type="text" class="form-control" id="months_paid" name="months_paid" required>
        </div>
        <button type="submit" class="btn btn-primary">تسجيل</button>
    </form>
</div>
</body>
</html>
