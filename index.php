<?php
require('config.php');
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php?error=يرجى تسجيل الدخول');
    exit();
}

function fetchStudents($conn, $search = '') {
    if ($search) {
        $stmt = $conn->prepare("SELECT * FROM students WHERE name LIKE ? OR id LIKE ?");
        $searchParam = "%$search%";
        $stmt->bind_param("ss", $searchParam, $searchParam);
    } else {
        $stmt = $conn->prepare("SELECT * FROM students");
    }
    $stmt->execute();
    return $stmt->get_result();
}

$students = fetchStudents($conn, $_GET['search'] ?? '');

?>

<!DOCTYPE html>
<html lang="ar">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;700&display=swap" rel="stylesheet">
    <title>لوحة التحكم</title>
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
/*            width: max-content;*/
        }
        .welcome {
            text-align: center; 
            direction: rtl;
        }
        @media (max-width: 576px) {
            .welcome h2 {
                font-size: 1.5rem; /* Resize for smaller devices */
            }

        }
    </style>
</head>
<body>

<div class="container">
    <div class="welcome">
        <?php
            $sql = "SELECT * FROM `users` WHERE id='$_SESSION[user_id]'";
            $query = $conn->query($sql);
            $result = mysqli_fetch_assoc($query);
            $username = $result['username'];
        ?>
        <h2>اهلا بك يا  <span style="color: #28a745;"><?php echo htmlspecialchars($username) ?></span></h2>
        <br>
        <a href="logout.php" class="btn btn-danger">تسجيل خروج</a>
    </div>
    <br>
    <h2>إدارة الطلاب</h2>
    <form method="GET" class="mb-4">
        <div class="input-group">
            <input type="text" name="search" class="form-control" value="<?php echo isset($_GET['search']) ? htmlspecialchars($_GET['search']) : ''; ?>" placeholder="بحث عن طالب بواسطة الاسم أو ID">
            <div class="input-group-append">
                <button type="submit" class="btn btn-secondary">بحث</button>
                <a href="add_student.php" class="btn btn-success">إضافة طالب</a>
            </div>
        </div>
    </form>

    <?php if (isset($_GET['error'])) echo "<div class='alert alert-danger'>{$_GET['error']}</div>"; ?>
    <?php if (isset($_GET['succ'])) echo "<div class='alert alert-success'>{$_GET['succ']}</div>"; ?>

    <table class="table table-striped table-responsive">
        <thead>
            <tr>
                <th>ID</th>
                <th>اسم</th>
                <th>رقم الهاتف</th>
                <th>درجة الامتحان</th>
                <th>الصف الدراسي</th>
                <th>الشهور المدفوعة</th>
                <th>العمليات</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = $students->fetch_assoc()): ?>
                <tr>
                    <td><?= htmlspecialchars($row['id']) ?></td>
                    <td><?= htmlspecialchars($row['name']) ?></td>
                    <td><?= htmlspecialchars($row['phone']) ?></td>
                    <td><?= htmlspecialchars($row['exam_score']) ?></td>
                    <td><?= htmlspecialchars($row['grade']) ?></td>
                    <td><?= htmlspecialchars($row['months_paid']) ?></td>
                    <td>
                        <a href="edit_student.php?id=<?= $row['id'] ?>" class="btn btn-warning">تعديل</a>
                        <a href="delete_student.php?id=<?= $row['id'] ?>" class="btn btn-danger">حذف</a>
                        <a href="generate_report.php?id=<?= $row['id'] ?>" class="btn btn-info">تقرير</a>
                    </td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</div>

</body>
</html>
