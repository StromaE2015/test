<?php
include 'header.php';
require_login();
$db = db();
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!check_csrf($_POST['csrf'])) die('Invalid CSRF');
    $name = $_POST['name'];
    $phone = $_POST['phone'];
    $photoPath = null;
    if (isset($_FILES['photo']) && $_FILES['photo']['error'] === UPLOAD_ERR_OK) {
        $ext = pathinfo($_FILES['photo']['name'], PATHINFO_EXTENSION);
        $filename = time() . '_' . basename($_FILES['photo']['name']);
        $dir = 'uploads/persons';
        if (!is_dir($dir)) mkdir($dir, 0755, true);
        $target = "$dir/$filename";
        if (move_uploaded_file($_FILES['photo']['tmp_name'], $target)) {
            $photoPath = $target;
        }
    }
    $stmt = $db->prepare("INSERT INTO persons(name,phone,photo) VALUES(?,?,?)");
    $stmt->execute([$name, $phone, $photoPath]);
    header('Location: index.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <title>Add Person</title>
</head>
<body class="bg-light">
<div class="container py-4">
  <h3>Add Person</h3>
  <form method="post" enctype="multipart/form-data">
    <input type="hidden" name="csrf" value="<?= csrf() ?>">
    <div class="mb-3">
      <label class="form-label">Name</label>
      <input class="form-control" name="name" required>
    </div>
    <div class="mb-3">
      <label class="form-label">Phone</label>
      <input class="form-control" name="phone" required>
    </div>
    <div class="mb-3">
      <label class="form-label">Photo (optional)</label>
      <input class="form-control" type="file" name="photo" accept="image/*">
    </div>
    <button class="btn btn-primary">Save</button>
    <a href="index.php" class="btn btn-secondary">Cancel</a>
  </form>
</div>
<?php include 'footer.php'; ?>