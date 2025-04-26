<?php
include 'functions.php';
require_login();
$db = db();
$stmt = $db->prepare("SELECT * FROM files WHERE id = ?");
$stmt->execute([$_GET['id']]);
$file = $stmt->fetch();
if($file){
    header('Content-Description: File Transfer');
    header('Content-Type: application/octet-stream');
    header('Content-Disposition: attachment; filename="'.basename($file['filename']).'"');
    readfile($file['filepath']);
    exit;
}
header('Location: index.php');
?>