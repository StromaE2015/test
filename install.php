<?php
session_start();
if (isset($_POST['submit'])) {
    $host   = $_POST['db_host'];
    $user   = $_POST['db_user'];
    $pass   = $_POST['db_pass'];
    $dbname = $_POST['db_name'];
    try {
        // 1. اتصال وإنشاء قاعدة البيانات والجداول
        $pdo = new PDO("mysql:host=$host", $user, $pass, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
        ]);
        $pdo->exec("
            CREATE DATABASE IF NOT EXISTS `$dbname`
              CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
            USE `$dbname`;
            CREATE TABLE IF NOT EXISTS users(
              id INT AUTO_INCREMENT PRIMARY KEY,
              username VARCHAR(100) UNIQUE NOT NULL,
              password VARCHAR(255) NOT NULL,
              created_at DATETIME DEFAULT CURRENT_TIMESTAMP
            ) ENGINE=InnoDB;
            CREATE TABLE IF NOT EXISTS categories(
              id INT AUTO_INCREMENT PRIMARY KEY,
              name VARCHAR(255) NOT NULL
            ) ENGINE=InnoDB;
            CREATE TABLE IF NOT EXISTS persons (
			  id INT AUTO_INCREMENT PRIMARY KEY,
			  name VARCHAR(100) NOT NULL,
			  phone VARCHAR(20) NOT NULL,
			  photo VARCHAR(255) NULL
			) ENGINE=InnoDB;

			-- جدول السلف
			CREATE TABLE IF NOT EXISTS advances (
			  id INT AUTO_INCREMENT PRIMARY KEY,
			  person_id INT NOT NULL,
			  amount DECIMAL(10,2) NOT NULL,
			  date DATE NOT NULL,
			  FOREIGN KEY(person_id) REFERENCES persons(id) ON DELETE CASCADE
			) ENGINE=InnoDB;
            CREATE TABLE IF NOT EXISTS files(
              id INT AUTO_INCREMENT PRIMARY KEY,
              filename VARCHAR(255) NOT NULL,
              filepath VARCHAR(255) NOT NULL,
              filetype VARCHAR(50) NOT NULL,
              uploaded_at DATETIME NOT NULL,
              category_id INT NULL,
              FOREIGN KEY(category_id)
                REFERENCES categories(id)
                ON DELETE SET NULL
            ) ENGINE=InnoDB;
        ");

        // 2. إنشاء حساب إداري إن وُجد
        if (!empty($_POST['admin_user']) && !empty($_POST['admin_pass'])) {
            $hash = password_hash($_POST['admin_pass'], PASSWORD_DEFAULT);
            $pdo->prepare("INSERT INTO users(username,password) VALUES(?,?)")
                ->execute([$_POST['admin_user'], $hash]);
        }

        // 3. حفظ إعدادات الاتصال
        file_put_contents('config.php',
            "<?php return ['host'=>'$host','user'=>'$user','pass'=>'$pass','db'=>'$dbname'];"
        );

        // 4. إنشاء بنية المجلدات تلقائيًا
        $folders = [
            'uploads',
            'uploads/thumbnails',
            'assets/icons'
        ];
        foreach ($folders as $dir) {
            if (!is_dir($dir)) {
                mkdir($dir, 0755, true);
            }
        }

        // 5. تحويل لصفحة تسجيل الدخول
        header('Location: login.php');
        exit;
    } catch (PDOException $e) {
        $error = $e->getMessage();
    }
}
?>
<!DOCTYPE html>
<html lang="ar">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <link
    href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0/dist/css/bootstrap.min.css"
    rel="stylesheet">
  <title>تثبيت نظام إدارة الملفات</title>
</head>
<body class="d-flex justify-content-center align-items-center vh-100">
  <div class="w-100" style="max-width:400px;">
    <h3 class="text-center mb-4">تثبيت نظام إدارة الملفات</h3>
    <?php if (!empty($error)): ?>
      <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>
    <form method="post">
      <div class="mb-3">
        <input class="form-control" name="db_host" required
               placeholder="DB Host" value="localhost">
      </div>
      <div class="mb-3">
        <input class="form-control" name="db_user" required
               placeholder="DB User">
      </div>
      <div class="mb-3">
        <input class="form-control" type="password"
               name="db_pass" placeholder="DB Password">
      </div>
      <div class="mb-3">
        <input class="form-control" name="db_name" required
               placeholder="DB Name">
      </div>
      <hr>
      <div class="mb-3">
        <input class="form-control" name="admin_user"
               placeholder="Admin Username (اختياري)">
      </div>
      <div class="mb-3">
        <input class="form-control" type="password"
               name="admin_pass" placeholder="Admin Password">
      </div>
      <button name="submit" class="btn btn-primary w-100">
        تثبيت
      </button>
    </form>
  </div>
</body>
</html>
