<?php
include 'functions.php';
?>
<!DOCTYPE html><html lang="en"><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0/dist/css/bootstrap.min.css" rel="stylesheet"></head><body>
<nav class="navbar navbar-expand bg-light">
<div class="container-fluid d-flex justify-content-between">
  <a class="navbar-brand">File Manager</a>
  <div>
    <?php if(is_logged()): ?>
      <span class="me-3">Hello, <?=esc($_SESSION['user']['username'])?></span>
      <a href="logout.php" class="btn btn-sm btn-outline-danger">Logout</a>
    <?php endif; ?>
  </div>
</div></nav>
<div class="container py-4">