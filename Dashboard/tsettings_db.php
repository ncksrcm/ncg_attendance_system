<?php
session_start();
require_once '../login_feature/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Handle General Settings
    if (isset($_POST['save_general'])) {
        if (isset($_POST['timezone'], $_POST['language'], $_POST['email'])) {
            $timezone = trim($_POST['timezone']);
            $language = trim($_POST['language']);
            $email = trim($_POST['email']); // This is the "email" input from the form

            if ($timezone === '' || $language === '' || $email === '') {
                $_SESSION['error'] = "Please fill in all required fields.";
                header('Location: tsettings.php');
                exit();
            }

            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $_SESSION['error'] = "Invalid email format.";
                header('Location: tsettings.php');
                exit();
            }

            // Get the logged-in teacher's email
            $user_email = $_SESSION['email'];

            $stmt = $pdo->prepare("UPDATE teacher SET timezone = ?, language = ?, email = ? WHERE email = ?");
            $stmt->execute([$timezone, $language, $email, $user_email]);

            // Update session email if changed
            $_SESSION['email'] = $email;

            $_SESSION['success'] = "General settings updated successfully!";
            header('Location: tsettings.php');
            exit();
        } else {
            $_SESSION['error'] = "All fields are required!";
            header('Location: tsettings.php');
            exit();
        }
    }

    // Handle Password Change
    if (isset($_POST['new_password'], $_POST['confirm_password'])) {
        $new_password = $_POST['new_password'];
        $confirm_password = $_POST['confirm_password'];

        if (empty($new_password) || empty($confirm_password)) {
            $_SESSION['error'] = "Please fill in all password fields.";
            header('Location: tsettings.php');
            exit();
        }

        if ($new_password !== $confirm_password) {
            $_SESSION['error'] = "Passwords do not match.";
            header('Location: tsettings.php');
            exit();
        }

        
        // **Add this regex password complexity check here:**
    if (!preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[\W_]).{8,}$/', $new_password)) {
        $_SESSION['error'] = "Password must be at least 8 characters and include uppercase, lowercase, number, and symbol.";
        header('Location: /proto/Entrep_prototype/Dashboard/parent/psettings.php');
        exit();
    }

    // Hash the new password   
        $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
        $user_email = $_SESSION['email'];

        $stmt = $pdo->prepare("UPDATE teacher SET password = ? WHERE email = ?");
        $stmt->execute([$hashed_password, $user_email]);

        $_SESSION['success'] = "Password updated successfully!";
        header('Location: tsettings.php');
        exit();
    }
}
