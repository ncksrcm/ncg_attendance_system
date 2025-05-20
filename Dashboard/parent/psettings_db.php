<?php 
session_start();
require_once '../../login_feature/db.php'; // Database connection

// Enable PDO error reporting for debugging
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    $_SESSION['error'] = "You must be logged in.";
    header('Location: /proto/Entrep_prototype/login_feature/plogin.php');
    exit();
}

$user_id = $_SESSION['user_id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Handle General Settings
    if (isset($_POST['save_general'])) {
        if (isset($_POST['timezone'], $_POST['language'], $_POST['email'])) {
            $timezone = trim($_POST['timezone']);
            $language = trim($_POST['language']);
            $email = trim($_POST['email']);

            if ($timezone === '' || $language === '' || $email === '') {
                $_SESSION['error'] = "Please fill in all required fields.";
                header('Location: /proto/Entrep_prototype/Dashboard/parent/psettings.php');
                exit();
            }

            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $_SESSION['error'] = "Invalid email format.";
                header('Location: /proto/Entrep_prototype/Dashboard/parent/psettings.php');
                exit();
            }

            // Check if the user exists
            $stmt = $pdo->prepare("SELECT * FROM parent WHERE id = ?");
            $stmt->execute([$user_id]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$user) {
                $_SESSION['error'] = "User not found.";
                header('Location: /proto/Entrep_prototype/Dashboard/parent/psettings.php');
                exit();
            }

            // Proceed with update
            try {
                $stmt = $pdo->prepare("UPDATE parent SET timezone = ?, language = ?, email = ? WHERE id = ?");
                $stmt->execute([$timezone, $language, $email, $user_id]);

                $_SESSION['success'] = "General settings updated successfully!";
                $_SESSION['email'] = $email; // Optional: update email in session if used elsewhere
            } catch (PDOException $e) {
                $_SESSION['error'] = "Error updating settings: " . $e->getMessage();
            }

            header('Location: /proto/Entrep_prototype/Dashboard/parent/psettings.php');
            exit();
        } else {
            $_SESSION['error'] = "All fields are required!";
            header('Location: /proto/Entrep_prototype/Dashboard/parent/psettings.php');
            exit();
        }
    }

    // Handle Password Change
    if (isset($_POST['new_password'], $_POST['confirm_password'])) {
        $new_password = $_POST['new_password'];
        $confirm_password = $_POST['confirm_password'];

        if (empty($new_password) || empty($confirm_password)) {
            $_SESSION['error'] = "Please fill in all password fields.";
            header('Location: /proto/Entrep_prototype/Dashboard/parent/psettings.php');
            exit();
        }

        if ($new_password !== $confirm_password) {
            $_SESSION['error'] = "Passwords do not match.";
            header('Location: /proto/Entrep_prototype/Dashboard/parent/psettings.php');
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

        try {
            // Update the password using ID
            $stmt = $pdo->prepare("UPDATE parent SET password = ? WHERE id = ?");
            $stmt->execute([$hashed_password, $user_id]);

            $_SESSION['success'] = "Password updated successfully!";
        } catch (PDOException $e) {
            $_SESSION['error'] = "Error updating password: " . $e->getMessage();
        }

        header('Location: /proto/Entrep_prototype/Dashboard/parent/psettings.php');
        exit();
    }
}
?>
