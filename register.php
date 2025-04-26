<?php
include 'functions.php';
if($_SERVER['REQUEST_METHOD'] === 'POST'){
    if(!check_csrf($_POST['csrf'])) die('Invalid CSRF');
    $u = $_POST['username'];
    $p = $_POST['password'];
    $hash = password_hash($p, PASSWORD_DEFAULT);
    $stmt = db()->prepare("INSERT INTO users(username,password) VALUES(?,?)");
    try{
        $stmt->execute([$u, $hash]);
        header('Location: login.php');
        exit;
    } catch(PDOException $e){
        $error = 'Username taken.';
    }
}
?>
<!DOCTYPE html><html lang="en"><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0/dist/css/bootstrap.min.css" rel="stylesheet"><title>Register</title></head>
<body class="d-flex justify-content-center align-items-center vh-100">
<div class="w-100" style="max-width:400px;"><h3 class="text-center mb-4">Register</h3>
<?php if(!empty($error)):?><div class="alert alert-danger"><?=esc($error)?></div><?php endif;?>
<form method="post">
<input type="hidden" name="csrf" value="<?=csrf()?>">
<div class="mb-3"><input class="form-control" name="username" required placeholder="Username"></div>
<div class="mb-3"><input class="form-control" type="password" name="password" required placeholder="Password"></div>
<button class="btn btn-primary w-100">Register</button>
<p class="mt-2 text-center"><a href="login.php">Login</a></p>
</form></div></body></html>