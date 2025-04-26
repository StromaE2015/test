<?php
include 'functions.php';
require_login();
if(!check_csrf($_GET['csrf'] ?? '')) die('Invalid CSRF');
$db = db();
$stmt = $db->prepare("SELECT * FROM files WHERE id = ?");
$stmt->execute([$_GET['id']]);
$file = $stmt->fetch();
if($file){
    @unlink($file['filepath']);
    $db->prepare("DELETE FROM files WHERE id = ?")->execute([$_GET['id']]);
}
header('Location: index.php');
exit;
?>