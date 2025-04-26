<?php
include 'header.php';
require_login();

// إنشاء رمز CSRF
if (empty($_SESSION['csrf_token'])) {
  $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Add Category</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container py-4">
  <h3>Add New Category</h3>
  <form action="save_category.php" method="POST">
    <div class="mb-3">
      <label for="name" class="form-label">Category Name</label>
      <input type="text" class="form-control" name="name" id="name" required>
    </div>
    <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
    <button type="submit" class="btn btn-primary">Save</button>
  </form>
</div>
</body>
</html>
