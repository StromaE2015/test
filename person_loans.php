<?php
include 'header.php';
require_login();
$db = db();
$person_id = $_GET['id'] ?? null;
if (!$person_id) {
    header('Location: index.php');
    exit;
}
$from = $_GET['from'] ?? date('Y-m-01');
$to = $_GET['to'] ?? date('Y-m-d');
$person = $db->prepare("SELECT * FROM persons WHERE id = ?");
$person->execute([$person_id]);
$person = $person->fetch(PDO::FETCH_ASSOC);
$stmt = $db->prepare("
    SELECT * FROM advances
    WHERE person_id = ? AND date BETWEEN ? AND ?
    ORDER BY date DESC
");
$stmt->execute([$person_id, $from, $to]);
$advances = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <title><?= esc($person['name']) ?>'s Advances</title>
</head>
<body class="bg-light">
<div class="container py-4">
  <h3><?= esc($person['name']) ?>'s Advances</h3>
  <form class="row g-2 mb-4">
    <div class="col-6">
      <input type="date" name="from" class="form-control" value="<?= esc($from) ?>">
    </div>
    <div class="col-6">
      <input type="date" name="to" class="form-control" value="<?= esc($to) ?>">
    </div>
    <input type="hidden" name="id" value="<?= $person_id ?>">
    <div class="col-12">
      <button class="btn btn-primary">Filter</button>
      <a href="index.php" class="btn btn-secondary">Back</a>
    </div>
  </form>
  <?php foreach ($advances as $adv): ?>
    <div class="card mb-3">
      <div class="card-body">
        Amount: <?= number_format($adv['amount'],2) ?><br>
        Date: <?= $adv['date'] ?>
      </div>
    </div>
  <?php endforeach; ?>
  <?php if (!$advances): ?>
    <p>No advances in this period.</p>
  <?php endif; ?>
</div>
<?php include 'footer.php'; ?>