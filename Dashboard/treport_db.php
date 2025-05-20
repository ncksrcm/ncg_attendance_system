<?php
session_start();
require_once '../login_feature/db.php';

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// DEBUG: Log all POST values
file_put_contents('debug.txt', print_r($_POST, true));

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get POST data
    $student_id = $_POST['student_id'] ?? null;
    $student_name = $_POST['student_name'] ?? null;
    $section = $_POST['section'] ?? null;
    $absent = isset($_POST['absent']) ? (int)$_POST['absent'] : 0;
    $present = isset($_POST['present']) ? (int)$_POST['present'] : 0;
    $tardiness = isset($_POST['tardiness']) ? (int)$_POST['tardiness'] : 0;
    $total = $absent + $present + $tardiness;
    $report_date = date("Y-m-d H:i:s");

    // Get teacher ID from session
    $teacherId = $_SESSION['user_id'] ?? null;
    if (!$teacherId) {
        file_put_contents('debug.txt', "No teacher ID in session\n", FILE_APPEND);
        $_SESSION['error'] = "Unauthorized. No teacher ID found.";
        header("Location: treports.php");
        exit();
    }

    // Validate required fields
    if (!$student_id || !$student_name || !$section) {
        $debugMessage = "Missing required data. ID={$student_id}, Name={$student_name}, Section={$section}\n";
        file_put_contents('debug.txt', $debugMessage, FILE_APPEND);
        $_SESSION['error'] = "Missing required student data.";
        header("Location: treports.php");
        exit();
    }

    try {
        $stmt = $pdo->prepare("INSERT INTO attendance_reports 
            (student_id, student_name, section, absent, present, tardiness, total, report_date, Teacher_Id)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $success = $stmt->execute([
            $student_id, $student_name, $section, $absent, $present, $tardiness, $total, $report_date, $teacherId
        ]);

        if ($success) {
            $_SESSION['success'] = "Attendance saved successfully.";
        } else {
            $_SESSION['error'] = "Insert failed. Please try again.";
        }

    } catch (PDOException $e) {
        file_put_contents('debug.txt', "PDO Exception: " . $e->getMessage(), FILE_APPEND);
        $_SESSION['error'] = "Database error: " . $e->getMessage();
    }
} else {
    $_SESSION['error'] = "Invalid request method.";
}

// Redirect back to reports page
header("Location: treports.php");
exit();
