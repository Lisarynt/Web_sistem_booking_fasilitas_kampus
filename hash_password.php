<?php
// filepath: c:\xampp\htdocs\uaswebsite\hash_password.php
$password = 'admin123';
$hashed = password_hash($password, PASSWORD_DEFAULT);
echo "Hashed Password: " . $hashed;
?>