<?php
session_start();
require_once(__DIR__ . '/../login_feature/db.php');

// reCAPTCHA secret key
$secretKey = "6Ldiyz4rAAAAACqWiv5c55_tiO0yGxz4lxFTqbw8";

// Verify reCAPTCHA first
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['g-recaptcha-response'])) {
        $captcha = $_POST['g-recaptcha-response'];

        if (!$captcha) {
            $_SESSION['error'] = "Please complete the CAPTCHA.";
            header("Location: plogin.php");
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
            header("Location: plogin.php");
            exit();
        }
    } else {
        $_SESSION['error'] = "CAPTCHA not submitted.";
        header("Location: plogin.php");
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

    // Check if parent exists
    $stmt = $pdo->prepare("SELECT * FROM `parent` WHERE username = ? LIMIT 1");
    $stmt->execute([$username]);
    $parent = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($parent) {
        if (password_verify($password, $parent['password'])) {
            // Fetch associated student (optional logic)
            $stmt = $pdo->prepare("SELECT * FROM `student` WHERE parent_id IS NULL LIMIT 1");
            $stmt->execute();
            $student = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($student) {
                $_SESSION['user_id'] = $parent['id'];
                $_SESSION['username'] = $parent['username'];
                $_SESSION['role'] = 'parent';
                $_SESSION['child_id'] = $student['Student_Id'];

                // Optional: update student's parent_id
                // $updateStmt = $pdo->prepare("UPDATE student SET parent_id = ? WHERE Student_Id = ?");
                // $updateStmt->execute([$parent['id'], $student['Student_Id']]);

                header('Location: /proto/Entrep_prototype/dashboard/parent/pindex.php');
                exit();
            } else {
                $_SESSION['error'] = "No student records available.";
                header('Location: plogin.php');
                exit();
            }
        } else {
            $_SESSION['error'] = "Invalid password.";
            header('Location: plogin.php');
            exit();
        }
    } else {
        $_SESSION['error'] = "Parent record not found.";
        header('Location: plogin.php');
        exit();
    }
} else {
    header('Location: plogin.php');
    exit();
}
