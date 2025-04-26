<?php
include 'header.php';
require_login();
$db = db();
$persons = $db->query("SELECT * FROM persons ORDER BY name")->fetchAll(PDO::FETCH_ASSOC);
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!check_csrf($_POST['csrf'])) die('Invalid CSRF');
    $person_id = (int) $_POST['person_id'];
    $amount = $_POST['amount'];
    $date = $_POST['date'];
    $stmt = $db->prepare("INSERT INTO advances(person_id,amount,date) VALUES(?,?,?)");
    $stmt->execute([$person_id, $amount, $date]);
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
  <title>Add Advance</title>
</head>
<body class="bg-light">
<div class="container py-4">
  <h3>Add Advance</h3>
  <form method="post">
    <input type="hidden" name="csrf" value="<?= csrf() ?>">
    <div class="mb-3">
      <label class="form-label">Person</label>
      <select class="form-select" name="person_id" required>
        <option value="">Select Person</option>
        <?php foreach ($persons as $p): ?>
        <option value="<?= $p['id'] ?>"><?= esc($p['name']) ?></option>
        <?php endforeach; ?>
      </select>
    </div>
    <div class="mb-3">
      <label class="form-label">Amount</label>
      <input class="form-control" type="number" step="0.01" name="amount" required>
    </div>
    <div class="mb-3">
      <label class="form-label">Date</label>
      <input class="form-control" type="date" name="date" value="<?= date('Y-m-d') ?>" required>
    </div>
    <button class="btn btn-primary">Save</button>
    <a href="index.php" class="btn btn-secondary">Cancel</a>
  </form>
</div>
<?php include 'footer.php'; ?>