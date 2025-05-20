<?php
session_start();
require_once '../../login_feature/db.php'; // Database connection file

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    $_SESSION['error'] = "You are not logged in.";
    header('Location: /proto/Entrep_prototype/parent_feature/plogin.php');
    exit();
}

$id = $_SESSION['user_id']; // Get the logged-in parent's ID

// Check if the form is submitted for updating profile information
if (isset($_POST['update_profile_info'])) {
    // Get updated profile information from the form
    $bio = $_POST['bio'] ?? '';
    $phone_number = $_POST['phone_number'] ?? '';
    $hobbies = $_POST['hobbies'] ?? '';
    $relationship_status = $_POST['relationship_status'] ?? '';
    $address = $_POST['address'] ?? '';

    // Prepare and execute the update query for profile information
    $stmt = $pdo->prepare("UPDATE parent SET bio = ?, phone_number = ?, hobbies = ?, relationship_status = ?, address = ? WHERE id = ?");
    $stmt->execute([$bio, $phone_number, $hobbies, $relationship_status, $address, $id]);

    // Check if the profile update was successful
    if ($stmt->rowCount()) {
        $_SESSION['success'] = "Profile information updated successfully.";
    } else {
        $_SESSION['error'] = "No changes were made to the profile.";
    }

    header('Location: /proto/Entrep_prototype/Dashboard/parent/paccounts.php');
    exit();
}

// Check if the form is submitted for updating the profile picture
if (isset($_FILES['profile_picture']) && $_FILES['profile_picture']['error'] == 0) {
    $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
    $maxFileSize = 5 * 1024 * 1024; // 5MB

    $fileType = $_FILES['profile_picture']['type'];
    $fileSize = $_FILES['profile_picture']['size'];
    $fileTmpName = $_FILES['profile_picture']['tmp_name'];
    $fileName = basename($_FILES['profile_picture']['name']);
    $fileExt = pathinfo($fileName, PATHINFO_EXTENSION);
    $newFileName = uniqid() . '.' . $fileExt;

    if (!in_array($fileType, $allowedTypes)) {
        $_SESSION['error'] = "Invalid file type. Only JPEG, PNG, and GIF are allowed.";
        header('Location: /proto/Entrep_prototype/Dashboard/parent/paccounts.php');
        exit();
    }

    if ($fileSize > $maxFileSize) {
        $_SESSION['error'] = "File size exceeds the 5MB limit.";
        header('Location: /proto/Entrep_prototype/Dashboard/parent/paccounts.php');
        exit();
    }

    // Correct upload directory and URL path
    $uploadDir = $_SERVER['DOCUMENT_ROOT'] . '/proto/Entrep_prototype/images/profiles/';
    $relativePath = '/proto/Entrep_prototype/images/profiles/' . $newFileName;

    if (move_uploaded_file($fileTmpName, $uploadDir . $newFileName)) {
        // Save only the relative web-accessible path
        $stmt = $pdo->prepare("UPDATE parent SET profile_picture = ? WHERE id = ?");
        $stmt->execute([$relativePath, $id]);

        if ($stmt->rowCount()) {
            $_SESSION['success'] = "Profile picture updated successfully.";
        } else {
            $_SESSION['error'] = "Error updating profile picture.";
        }
    } else {
        $_SESSION['error'] = "Failed to upload the profile picture.";
    }

    header('Location: /proto/Entrep_prototype/Dashboard/parent/paccounts.php');
    exit();
}
?>
