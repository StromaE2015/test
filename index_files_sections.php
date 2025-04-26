<?php
// index_files_sections.php
// Ensure prerequisites
if (!isset($db)) {
    include 'header.php';
    require_login();
    $db = db();
}
// Fetch categories and files if not provided by caller
if (!isset($cats)) {
    $cats = $db->query("SELECT * FROM categories")->fetchAll(PDO::FETCH_ASSOC);
}
$selectedCat = $_GET['category'] ?? null;
if (!isset($files)) {
    if ($selectedCat) {
        $stmt = $db->prepare("SELECT * FROM files WHERE category_id = ? ORDER BY filename ASC");
        $stmt->execute([$selectedCat]);
        $files = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } else {
        $files = $db->query("SELECT * FROM files ORDER BY filename ASC")->fetchAll(PDO::FETCH_ASSOC);
    }
}
// Determine category name
$catName = '';
if ($selectedCat) {
    foreach ($cats as $c) {
        if ($c['id'] == $selectedCat) {
            $catName = $c['name'];
            break;
        }
    }
}
?>

<h4 class="mb-3">Sections</h4>
<div class="row g-3 mb-5">
  <?php foreach ($cats as $c):
    $stmt = $db->prepare("SELECT * FROM files WHERE category_id = ? ORDER BY uploaded_at DESC LIMIT 3");
    $stmt->execute([$c['id']]);
    $previews = $stmt->fetchAll(PDO::FETCH_ASSOC);
    if (count($previews)) {
        $bgImages = array_map(function($f) { return "url('" . getPreview($f) . "')"; }, $previews);
        $bgStyle = "background-image: " . implode(', ', $bgImages) . ";"
                 . "background-size: 33.33% 100%; background-repeat: no-repeat;";
    } else {
        $bgStyle = "background: #ddd;";
    }
  ?>
    <div class="col-6 col-md-4 col-lg-3">
      <a href="index.php?category=<?= $c['id'] ?>" class="text-decoration-none">
        <div class="preview-card" style="<?= $bgStyle ?>">
          <div class="card-overlay">
            <?= esc($c['name']) ?> (<?= count($previews) ?>)
          </div>
        </div>
      </a>
    </div>
  <?php endforeach; ?>
</div>

<h4 class="mb-3"><?= $selectedCat ? "Files in '" . esc($catName) . "'" : 'All Files' ?></h4>
<div class="row g-3">
  <?php foreach ($files as $f):
    $preview = getPreview($f);
    $bg      = "background-image: url('$preview')";
  ?>
    <div class="col-6 col-sm-4 col-md-3 col-lg-2">
      <div class="preview-card" style="<?= $bg ?>">
        <div class="card-overlay">
          <?= esc($f['filename']) ?>
        </div>
      </div>
      <div class="d-flex justify-content-between mt-2">
        <a href="download.php?id=<?= $f['id'] ?>" class="btn btn-sm btn-outline-primary">Download</a>
        <a href="delete.php?id=<?= $f['id'] ?>&csrf=<?= csrf() ?>"
           onclick="return confirm('Delete this file?')"
           class="btn btn-sm btn-outline-danger">Delete</a>
      </div>
    </div>
  <?php endforeach; ?>
</div>
