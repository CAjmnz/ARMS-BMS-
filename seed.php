<?php 
define('BASEPATH', 'true');

$conn = mysqli_connect('localhost','root','','arms-bms');

$username = 'admin';
$password = password_hash('1234', PASSWORD_DEFAULT);

mysqli_query($conn, "INSERT INTO users (username, password) VALUES ('$username', '$password')");
echo "User Created!";?>