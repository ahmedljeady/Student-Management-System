# هذا المشروع محمي بموجب ترخيص CC BY-NC
# يمنع استخدامه لأغراض تجارية أو ضارة

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
            width: max-content ;
        }
        @media (max-width: 768px) {
            .container {
                margin-top: 20px;
                background: white;
                padding: 20px;
                border-radius: 8px;
                box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
                width: 100%;
            }
            .table{
                display: block;
                width: 100%;
                overflow-x: auto;
            }
        }
    </style>
</head>
<body>

<div class="container">
    <div class="welcome" style="text-align: center; direction: rtl;">
        <font>
            <?php
                $sql = "SELECT * FROM `users` WHERE id='$_SESSION[user_id]'";
                $query = $conn->query($sql);
                $result = mysqli_fetch_assoc($query);
                $username = $result['username'];
            ?>
            <h2>اهلا بك يا  <span style="color: #28a745;"><?php echo $username?></span></h2>
        </font>
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

    <table class="table table-striped">
        <thead>
            <tr>
                <th>ID</th>
                <th>اسم</th>
                <th>رقم الهاتف</th>
                <th>درجة الامتحان</th>
                <th>الصف الدراسي</th>
                <th>الشهور المدفوعة</th>
                <th>العمليات</th>
                <th><button class="btn btn-danger" id="deleteButton">حذف الكل</button></th>
                <script>
                    document.getElementById('deleteButton').addEventListener('click', function() {
                        if (confirm('هل أنت متأكد أنك تريد حذف جميع الطلاب؟')) {
                            window.location.href = 'delete_students.php?action=delete';
                        }
                    });
                </script>
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
                        <a href="#" class="btn btn-success whatsapp-button" 
                           data-id="<?= htmlspecialchars($row['id']) ?>"
                           data-name="<?= htmlspecialchars($row['name']) ?>"
                           data-phone="<?= htmlspecialchars($row['phone']) ?>"
                           data-exam_score="<?= htmlspecialchars($row['exam_score']) ?>"
                           data-grade="<?= htmlspecialchars($row['grade']) ?>"
                           data-months_paid="<?= htmlspecialchars($row['months_paid']) ?>">
                           WhatsApp
                        </a>
                        <a href="report.php?id=<?= $row['id'] ?>" class="btn btn-info">تقرير</a>
                        <!-- <a href="generate_report.php?id=<?= $row['id'] ?>" class="btn btn-info">تقرير</a> -->
                    </td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</div>
<script>
document.querySelectorAll('.whatsapp-button').forEach(button => {
    button.onclick = function() {
        var studentId = this.getAttribute('data-id');
        var studentName = this.getAttribute('data-name');
        var studentPhone = this.getAttribute('data-phone');
        var studentScore = this.getAttribute('data-exam_score');
        var studentGrade = this.getAttribute('data-grade');
        var studentMonthsPaid = this.getAttribute('data-months_paid');

        // إعداد الرسالة لتشمل جميع البيانات
        var message = encodeURIComponent(
            "أهلا وسهلا بحضرتك,\n" +
            "دي بياناتك:\n" +
            "المعرف: " + studentId + "\n" +
            "الاسم: " + studentName + "\n" +
            "الصف: " + studentGrade + "\n" +
            "الدرجات: " + studentScore + "\n" +
            "الأشهر المدفوعة: " + studentMonthsPaid
        );

        var url = "https://wa.me/" + studentPhone + "?text=" + message;

        window.open(url, "_blank");
    };
});

</script>

</body>
</html>
