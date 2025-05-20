<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $role = $_POST['role'] ?? '';

    if ($role === 'teacher') {
        header('Location: tlogin.php');
        exit();
    } elseif ($role === 'parent') {
        header('Location: /proto/Entrep_prototype/parent_feature/plogin.php');
        exit();
    } else {
        // Default fallback
        header('Location: login_as.php');
        exit();
    }
}