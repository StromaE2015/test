<?php
include 'functions.php';
if($_SERVER['REQUEST_METHOD'] === 'POST'){
    if(!check_csrf($_POST['csrf'])) die('Invalid CSRF');
    $u = $_POST['username'];
    $p = $_POST['password'];
    $stmt = db()->prepare("SELECT * FROM users WHERE username=?");
    $stmt->execute([$u]);
    $user = $stmt->fetch();
    if($user && password_verify($p, $user['password'])){
        session_regenerate_id();
        $_SESSION['user'] = $user;
        header('Location: index.php');
        exit;
    } else {
        $error = 'Invalid credentials';
    }
}
?>
<!DOCTYPE html><html lang="en"><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0/dist/css/bootstrap.min.css" rel="stylesheet"><title>Login</title></head>
<body class="d-flex justify-content-center align-items-center vh-100">
<div class="w-100" style="max-width:400px;"><h3 class="text-center mb-4">Login</h3>
<?php if(!empty($error)):?><div class="alert alert-danger"><?=esc($error)?></div><?php endif;?>
<form method="post">
<input type="hidden" name="csrf" value="<?=csrf()?>">
<div class="mb-3"><input class="form-control" name="username" required placeholder="Username"></div>
<div class="mb-3"><input class="form-control" type="password" name="password" required placeholder="Password"></div>
<button class="btn btn-primary w-100">Login</button>
<p class="mt-2 text-center"><a href="register.php">Register</a></p>
</form></div></body></html>