<?php
session_start();
require 'db.php';

// reCAPTCHA secret key
$secretKey = "6Ldiyz4rAAAAACqWiv5c55_tiO0yGxz4lxFTqbw8";

// Verify reCAPTCHA first
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['g-recaptcha-response'])) {
        $captcha = $_POST['g-recaptcha-response'];

        if (!$captcha) {
            $_SESSION['error'] = "Please complete the CAPTCHA.";
            header("Location: tlogin.php");
            exit();
        }

        // Verify with Google
        $url = 'https://www.google.com/recaptcha/api/siteverify';
        $data = [
            'secret' => $secretKey,
            'response' => $captcha,
            'remoteip' => $_SERVER['REMOTE_ADDR']
        ];

        $options = [
            'http' => [
                'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
                'method'  => 'POST',
                'content' => http_build_query($data),
            ],
        ];

        $context  = stream_context_create($options);
        $result = file_get_contents($url, false, $context);
        $response = json_decode($result);

        if (!$response->success) {
            $_SESSION['error'] = "CAPTCHA verification failed. Please try again.";
            header("Location: tlogin.php");
            exit();
        }
    } else {
        $_SESSION['error'] = "CAPTCHA not submitted.";
        header("Location: tlogin.php");
        exit();
    }

    // If reCAPTCHA passes, proceed with login validation
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';

     if (!preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[\W_]).{8,}$/', $password)) {
    $_SESSION['error'] = "Password must be at least 8 characters and include uppercase, lowercase, number, and symbol.";
    header('Location: plogin.php');
    exit();
}

    // Check if the user is a teacher
    $stmt = $pdo->prepare("SELECT * FROM teacher WHERE username = ?");
    $stmt->execute([$username]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['role'] = 'teacher';
        header('Location: /proto/Entrep_prototype/dashboard/tindex.php');
        exit();
    }

    // Check if the user is a parent
    $stmt = $pdo->prepare("SELECT * FROM parent WHERE username = ?");
    $stmt->execute([$username]);
    $parent = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($parent && password_verify($password, $parent['password'])) {
        $_SESSION['user_id'] = $parent['id'];
        $_SESSION['username'] = $parent['username'];
        $_SESSION['role'] = 'parent';
        $_SESSION['child_id'] = $parent['child_id'];
        header('Location: /proto/Entrep_prototype/dashboard/parent_dashboard.php');
        exit();
    }

    $_SESSION['error'] = "Invalid username or password.";
    header('Location: tlogin.php');
    exit();
} else {
    header('Location: tlogin.php');
    exit();
}
?>
