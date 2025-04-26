<?php
include 'header.php';
require_login();

// التحقق من CSRF token
if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
  die("Invalid CSRF token");
}

// التحقق من البيانات
if (!isset($_POST['name']) || trim($_POST['name']) === '') {
  die("Category name is required.");
}

$name = htmlspecialchars(trim($_POST['name']));

// حفظ في قاعدة البيانات باستخدام Prepared Statement
$db = db();
$stmt = $db->prepare("INSERT INTO categories (name) VALUES (?)");
$stmt->execute([$name]);

header("Location: index.php");
exit;
?>
