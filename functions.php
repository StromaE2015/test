<?php
session_start();
// Database connection
function db(){
    static $pdo;
    if(!$pdo){
        $c = include 'config.php';
        $pdo = new PDO("mysql:host={$c['host']};dbname={$c['db']}", $c['user'], $c['pass'], [PDO::ATTR_ERRMODE=>PDO::ERRMODE_EXCEPTION]);
    }
    return $pdo;
}
// CSRF token
function csrf(){
    if(empty($_SESSION['csrf'])) $_SESSION['csrf'] = bin2hex(random_bytes(32));
    return $_SESSION['csrf'];
}
function check_csrf($token){
    return hash_equals($_SESSION['csrf'] ?? '', $token);
}
// Auth
function is_logged(){
    return !empty($_SESSION['user']);
}
function require_login(){
    if(!is_logged()){
        header('Location: login.php');
        exit;
    }
}
function esc($s){
    return htmlspecialchars($s, ENT_QUOTES, 'UTF-8');
}
?>