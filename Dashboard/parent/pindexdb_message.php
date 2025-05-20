<?php
session_start();
require_once(__DIR__ . '/../../login_feature/db.php'); // Adjust path if needed

// Get the form data
$message = $_POST['message'] ?? '';
$parent_id = $_POST['parent_id'] ?? '';
$student_id = $_POST['student_id'] ?? '';

// Insert feedback into the database
if ($message && $parent_id && $student_id) {
    $stmt = $pdo->prepare("INSERT INTO feedback (parent_id, student_id, message) VALUES (?, ?, ?)");
    if ($stmt->execute([$parent_id, $student_id, $message])) {
        // Set success message in session
        $_SESSION['feedback_success'] = true;
    } else {
        $_SESSION['feedback_success'] = false;
    }
} else {
    $_SESSION['feedback_success'] = false;
}

// Redirect back to the parent dashboard page
header("Location: /proto/Entrep_prototype/Dashboard/parent/pindex.php");
exit();
