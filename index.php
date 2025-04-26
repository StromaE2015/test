<?php
// index.php (Dashboard with Unified Live Search)
include 'header.php';
require_login();
$db = db();

// Fetch categories, files, persons, advances
$cats = $db->query("SELECT * FROM categories")->fetchAll(PDO::FETCH_ASSOC);
$files = $db->query("SELECT * FROM files ORDER BY filename ASC")->fetchAll(PDO::FETCH_ASSOC);
$persons = $db->query("SELECT * FROM persons ORDER BY name ASC")->fetchAll(PDO::FETCH_ASSOC);
$advances = $db->query("SELECT a.*, p.name, p.photo FROM advances a JOIN persons p ON p.id=a.person_id ORDER BY a.date DESC LIMIT 10")->fetchAll(PDO::FETCH_ASSOC);

function getPreview($file) {
    $ext = strtolower(pathinfo($file['filename'], PATHINFO_EXTENSION));
    if (in_array($ext, ['jpg','jpeg','png','gif'])) {
        return $file['filepath'];
    }
    if ($ext === 'pdf') {
        $nameOnly = pathinfo($file['filepath'], PATHINFO_FILENAME);
        $thumb = 'uploads/thumbnails/' . $nameOnly . '.png';
        return file_exists($thumb) ? $thumb : 'assets/icons/pdf-icon.png';
    }
    return 'assets/icons/file-icon.png';
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <title>Dashboard</title>
  <style>
    .preview-card { position: relative; height: 120px; background-size: cover; background-position: center; border-radius: .5rem; overflow: hidden; box-shadow: 0 2px 6px rgba(0,0,0,0.2); }
    .card-overlay, .advance-overlay, .person-overlay { position: absolute; bottom: 0; left: 0; right: 0; background: rgba(0,0,0,0.5); color: #fff; padding: .4rem; font-size: .9rem; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
    .icon-btn { width:60px;height:60px;display:inline-flex;align-items:center;justify-content:center; border-radius:.5rem;background:#f0f0f0;margin-right:1rem;text-decoration:none;color:#333;font-size:1.5rem; }
    .advance-card, .person-card { position: relative; height:100px; background-size:cover; background-position:center; border-radius:.5rem; overflow:hidden; margin-bottom:1rem; }
    .advance-overlay { background: rgba(0,123,255,0.6); }
    .person-overlay { background: rgba(0,0,0,0.6); }
  </style>
</head>
<body class="bg-light">
<div class="container-fluid py-4">
  <!-- Icon Bar -->
  <div class="mb-3 d-flex align-items-center">
    <a href="add_category.php" class="icon-btn" title="اضافة قسم">📂</a>
    <a href="upload.php" class="icon-btn" title="رفع مستند">💾</a>
    <a href="add_person.php" class="icon-btn" title="اضافة شخص">👤</a>
    <a href="add_advance.php" class="icon-btn" title="اضافة سلفة">💳</a>
    <!-- Unified Search Box -->
    <input id="searchBox" type="text" class="form-control ms-auto" style="max-width:300px;" placeholder="Search...">
  </div>

  <div class="row">
    <!-- Documents Section -->
    <div class="col-lg-8">
      <h5>Documents & Sections</h5>
      <!-- Sections List -->
      <div class="row g-3 mb-4">
        <?php foreach ($cats as $c):
          $pre = $db->prepare("SELECT filepath FROM files WHERE category_id = ? ORDER BY uploaded_at DESC LIMIT 3");
          $pre->execute([$c['id']]);
          $pr = $pre->fetchAll(PDO::FETCH_COLUMN);
          if (count($pr)) {
              $imgs = array_map(fn($p) => "url('" . $p . "')", $pr);
              $style = "background-image: " . implode(', ', $imgs) . "; background-size: 33.33% 100%; background-repeat: no-repeat;";
          } else {
              $style = "background: #ddd;";
          }
        ?>
        <div class="col-6 col-md-4 col-lg-3 doc-card">
          <a href="index.php?category=<?= $c['id'] ?>" class="text-decoration-none">
            <div class="preview-card" style="<?= $style ?>">
              <div class="card-overlay"><?= esc($c['name']) ?></div>
            </div>
          </a>
        </div>
        <?php endforeach; ?>
      </div>
      <!-- Files List -->
      <h6>Files</h6>
      <div class="row g-3" id="docList">
        <?php foreach ($files as $f):
          $bgStyle = "background-image: url('" . getPreview($f) . "')";
        ?>
        <div class="col-6 col-sm-4 col-md-3 col-lg-2 doc-card">
          <div class="preview-card" style="<?= $bgStyle ?>">
            <div class="card-overlay"><?= esc($f['filename']) ?></div>
          </div>
        </div>
        <?php endforeach; ?>
      </div>
    </div>

    <!-- Persons and Advances Section -->
    <div class="col-lg-4">
      <h5>Persons</h5>
      <div id="personsList">
        <?php foreach ($persons as $p):
          $bgStyle = $p['photo'] ? "background-image: url('" . $p['photo'] . "')" : "background: #888";
        ?>
        <div class="person-card mb-2">
          <a href="person_loans.php?id=<?= $p['id'] ?>" class="text-decoration-none">
            <div class="advance-card" style="<?= $bgStyle ?>">
              <div class="person-overlay person-name"><?= esc($p['name']) ?></div>
            </div>
          </a>
        </div>
        <?php endforeach; ?>
      </div>

      <h5 class="mt-4">Recent Advances</h5>
      <?php foreach ($advances as $a):
        $advBg = $a['photo'] ? "background-image: url('" . $a['photo'] . "')" : "background: #555";
      ?>
      <div class="advance-card" style="<?= $advBg ?>">
        <div class="advance-overlay">
          <?= esc($a['name']) ?><br>
          <?= number_format($a['amount'], 2) ?> - <?= esc($a['date']) ?>
        </div>
      </div>
      <?php endforeach; ?>
    </div>
  </div>
</div>
<script>
  document.getElementById('searchBox').addEventListener('keyup', function() {
    const filter = this.value.toLowerCase();
    document.querySelectorAll('.doc-card').forEach(card => {
      const text = card.textContent.toLowerCase();
      card.style.display = text.includes(filter) ? '' : 'none';
    });
    document.querySelectorAll('.person-card').forEach(card => {
      const name = card.querySelector('.person-name').textContent.toLowerCase();
      card.style.display = name.includes(filter) ? '' : 'none';
    });
  });
</script>
</body>
</html>
