<?php
require('config.php');
session_start();

if (isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit();
}

$error = $_GET['error'] ?? null;
$succ = $_GET['succ'] ?? null;

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // تحقق من بيانات الاعتماد
    $stmt = $conn->prepare('SELECT * FROM users WHERE username = ?');
    $stmt->bind_param('s', $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        // تحقق من كلمة المرور
        if ($password === $user['password']) {
            $_SESSION['user_id'] = $user['id'];
            header('Location: index.php');
            exit();
        }
    }
    $error = 'بيانات الاعتماد غير صحيحة';
}
?>

<!DOCTYPE html>
<html lang="ar">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <title>تسجيل الدخول</title>
    <style>
        body {
            background-color: #f8f9fa;
        }
        .container {
            max-width: 400px;
            margin-top: 100px;
            padding: 20px;
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }
        h2 {
            margin-bottom: 20px;
            text-align: center;
        }
    </style>
</head>
<body>
<div class="container">
    <h2>تسجيل الدخول</h2>
    <?php if ($error): ?>
        <div class="alert alert-danger text-center"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>
    <?php if ($succ): ?>
        <div class="alert alert-success text-center"><?= htmlspecialchars($succ) ?></div>
    <?php endif; ?>
    <form method="POST">
        <div class="form-group">
            <label for="username">اسم المستخدم</label>
            <input type="text" class="form-control" id="username" name="username" required>
        </div>
        <div class="form-group">
            <label for="password">كلمة المرور</label>
            <input type="password" class="form-control" id="password" name="password" required>
        </div>
        <button type="submit" class="btn btn-primary btn-block">دخول</button>
    </form>
</div>
</body>
</html>
