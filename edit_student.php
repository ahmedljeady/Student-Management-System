<?php
require('config.php');

session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php?error=يرجى تسجيل الدخول');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // تعديل بيانات الطالب
    $id = $_POST['id'];
    $name = trim($_POST['name']);
    $phone = trim($_POST['phone']);
    $exam_score = trim($_POST['exam_score']);
    $class = trim($_POST['class']);
    $months_paid = $_POST['months_paid'];

    // التحقق من المدخلات
    if (!empty($name) && is_numeric($exam_score)) {
        $stmt = $conn->prepare("UPDATE students SET name=?, phone=?, exam_score=?, grade=?, months_paid=? WHERE id=?");
        $stmt->bind_param('ssdsss', $name, $phone, $exam_score, $class, $months_paid, $id);
        $stmt->execute();

        header('Location: index.php?succ=تم التعديل بنجاح!');
        exit();
    } else {
        $error_message = "الاسم مطلوب ودرجة الامتحان يجب أن تكون رقمًا.";
    }
}

if (isset($_GET['id'])) {
    $id = $_GET['id'];
    $stmt = $conn->prepare("SELECT * FROM students WHERE id=?");
    $stmt->bind_param('i', $id);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        $student = $result->fetch_assoc();
    }else{
        $error_message = "الطالب غير موجود";
        header("Location: index.php?error= $error_message");
    }
}else{
        $error_message = "الطالب غير موجود";
        header("Location: index.php?error= $error_message");
}
?>

<!DOCTYPE html>
<html lang="ar">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;700&display=swap" rel="stylesheet">
    <title>تعديل بيانات الطالب</title>
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
    <h2>تعديل بيانات الطالب</h2>
    
    <?php if (isset($error_message)) echo "<div class='alert alert-danger'>$error_message</div>"; ?>

    <form method="POST">
        <input type="hidden" name="id" value="<?php echo htmlspecialchars($student['id']); ?>">
        <div class="form-group">
            <label for="name">اسم الطالب</label>
            <input type="text" class="form-control" id="name" name="name" value="<?php echo htmlspecialchars($student['name']); ?>" required>
        </div>
        <div class="form-group">
            <label for="phone">رقم الهاتف</label>
            <input type="text" class="form-control" id="phone" name="phone" value="<?php echo htmlspecialchars($student['phone']); ?>">
        </div>
        <div class="form-group">
            <label for="exam_score">درجة الامتحان</label>
            <input type="number" step="0.01" class="form-control" id="exam_score" name="exam_score" value="<?php echo htmlspecialchars($student['exam_score']); ?>" required>
        </div>
        <div class="form-group">
            <label for="class">الصف الدراسي</label>
            <input type="text" class="form-control" id="class" name="class" value="<?php echo htmlspecialchars($student['grade']); ?>" required>
        </div>
        <div class="form-group">
            <label for="months_paid">الشهور المدفوعة</label>
            <input type="text" class="form-control" id="months_paid" name="months_paid" value="<?php echo htmlspecialchars($student['months_paid']); ?>" required>
        </div>
        <button type="submit" class="btn btn-primary">تعديل</button>
    </form>
</div>
</body>
</html>
