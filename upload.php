<?php
include 'header.php';
require_login();
$db = db();
$cats = $db->query("SELECT * FROM categories")->fetchAll();
if($_SERVER['REQUEST_METHOD']==='POST' && isset($_FILES['file'])){
    if(!check_csrf($_POST['csrf'])) die('Invalid CSRF');
    $cat = !empty($_POST['category']) ? (int)$_POST['category'] : null;
    $file = $_FILES['file'];
    $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
    $filename = time() . '_' . basename($file['name']);
    $filepath = 'uploads/' . $filename;
    if(!is_dir('uploads')) mkdir('uploads',0755,true);
    if(move_uploaded_file($file['tmp_name'], $filepath)){
        $stmt = $db->prepare("INSERT INTO files(filename, filepath, filetype, uploaded_at, category_id) VALUES(?,?,?,?,?)");
        $stmt->execute([$file['name'], $filepath, $ext, date('Y-m-d H:i:s'), $cat]);
        header('Location: index.php'); exit;
    } else { $error = 'Upload failed'; }
}
?>
<!DOCTYPE html><html lang="en"><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0/dist/css/bootstrap.min.css" rel="stylesheet"><title>Upload File</title></head>
<body class="d-flex justify-content-center align-items-center vh-100">
<div class="w-100" style="max-width:500px;"><h3 class="text-center mb-4">Upload File</h3>
<?php if(!empty($error)):?><div class="alert alert-danger"><?=esc($error)?></div><?php endif;?>
<form method="post" enctype="multipart/form-data">
<input type="hidden" name="csrf" value="<?=csrf()?>"
<div class="mb-3"><input type="file" name="file" class="form-control" required></div>
<div class="mb-3">
<select name="category" class="form-select"><option value="">None</option>
<?php foreach($cats as $c): ?><option value="<?=$c['id']?>"><?=esc($c['name'])?></option><?php endforeach; ?>
</select></div>
<button name="submit" class="btn btn-primary w-100">Upload</button>
<p class="mt-2 text-center"><a href="index.php">Back</a></p>
</form></div></body></html>