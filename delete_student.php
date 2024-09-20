<?php
require('config.php');

session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php?error=يجب عليك تسجيل الدخول');
    exit();
}

if (isset($_GET['id'])) {
    $id = $_GET['id'];
    $stmt = $conn->prepare("DELETE FROM students WHERE id=?");
    $stmt->bind_param('i', $id);
    $stmt->execute();
    header('Location: index.php?succ=Deleted');
    exit();
}
?>
