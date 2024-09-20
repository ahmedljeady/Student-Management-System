<?php
	session_start();

	// قم بإنهاء جلسة المستخدم
	session_unset(); // يحرر جميع متغيرات الجلسة
	session_destroy(); // يدمر الجلسة

	// إعادة توجيه المستخدم إلى صفحة تسجيل الدخول
	header('Location: login.php?succ=تم تسجيل الخروج بنجاح');
	exit();
?>