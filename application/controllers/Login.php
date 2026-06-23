<?php
session_start();

// Hardcoded for now — later replace with a DB query
$valid_user = "admin";
$valid_pass = "1234";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST["username"]);
    $password = trim($_POST["password"]);

    if ($username === $valid_user && $password === $valid_pass) {
        $_SESSION["user"] = $username;         // store user in session
        header("Location: dashboard.php");     // redirect to dashboard
        exit();
    } else {
        $error = "Invalid username or password.";
    }
}
?>