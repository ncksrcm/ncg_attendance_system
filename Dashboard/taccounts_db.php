<?php
session_start();
require_once '../login_feature/db.php';

error_reporting(E_ALL);
ini_set('display_errors', 1);

if (!isset($_SESSION['user_id'])) {
    $_SESSION['error'] = "You must be logged in to update your profile.";
    header('Location: tlogin.php');
    exit();
}

$userId = $_SESSION['user_id'];

// Update profile picture
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['profile_picture']) && $_FILES['profile_picture']['error'] === UPLOAD_ERR_OK) {
    $fileTmpPath = $_FILES['profile_picture']['tmp_name'];
    $fileName = $_FILES['profile_picture']['name'];
    $fileSize = $_FILES['profile_picture']['size'];
    $fileType = $_FILES['profile_picture']['type'];

    if ($fileSize > 2 * 1024 * 1024) {
        $_SESSION['error'] = "File is too large.";
        header('Location: taccounts.php');
        exit();
    }

    $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
    if (!in_array($fileType, $allowedTypes)) {
        $_SESSION['error'] = "Invalid file type.";
        header('Location: taccounts.php');
        exit();
    }

    $newFileName = uniqid('profile_') . '.' . pathinfo($fileName, PATHINFO_EXTENSION);
    $uploadDir = '../images/profiles/';
    $uploadFilePath = $uploadDir . $newFileName;

    if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);

    if (move_uploaded_file($fileTmpPath, $uploadFilePath)) {
        $stmt = $pdo->prepare("UPDATE teacher SET profile_picture = ? WHERE id = ?");
        $stmt->execute([$uploadFilePath, $userId]);
        $_SESSION['success'] = "Profile picture updated.";
    } else {
        $_SESSION['error'] = "Failed to upload picture.";
    }

    header('Location: taccounts.php');
    exit();
}

// Update profile info
if (isset($_POST['update_profile_info'])) {
    $bio = $_POST['bio'] ?? '';
    $phone = $_POST['phone_number'] ?? '';
    $hobbies = $_POST['hobbies'] ?? '';
    $status = $_POST['relationship_status'] ?? '';
    $address = $_POST['address'] ?? '';

    $stmt = $pdo->prepare("UPDATE teacher SET bio = ?, phone_number = ?, hobbies = ?, relationship_status = ?, address = ? WHERE id = ?");
    $stmt->execute([$bio, $phone, $hobbies, $status, $address, $userId]);

    $_SESSION['success'] = "Profile info updated successfully.";
    header('Location: taccounts.php');
    exit();
}

$_SESSION['error'] = "Invalid request.";
header('Location: taccounts.php');
exit();
