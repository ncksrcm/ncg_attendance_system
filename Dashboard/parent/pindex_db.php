<?php
session_start();
require_once(__DIR__ . '/../../login_feature/db.php');
// Ensure the user is logged in as a parent
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'parent') {
    $_SESSION['error'] = "Unauthorized access.";
    header('Location: /proto/Entrep_prototype/parent_feature/plogin.php');
    exit();
}

$searchTerm = $_GET['search_term'] ?? '';

if (empty($searchTerm)) {
    $_SESSION['error'] = "Please enter a search term.";
    header("Location: /proto/Entrep_prototype/Dashboard/parent/pindex.php");    exit();
}

// Prepare and execute the query to sum attendance by student
$stmt = $pdo->prepare("
    SELECT 
        s.Student_Id, 
        s.Student,
        MIN(s.Section) AS Section,
        MIN(s.Subject) AS Subject,
        COALESCE(SUM(a.absent), 0) AS total_absent,
        COALESCE(SUM(a.present), 0) AS total_present,
        COALESCE(SUM(a.tardiness), 0) AS total_tardiness
    FROM student s
    LEFT JOIN attendance_reports a ON s.Student_Id = a.student_id
    WHERE s.Student_Id LIKE ? OR s.Student LIKE ?
    GROUP BY s.Student_Id, s.Student
    LIMIT 1
");

$stmt->execute(["%$searchTerm%", "%$searchTerm%"]);
$student = $stmt->fetch(PDO::FETCH_ASSOC);

if ($student) {
    $_SESSION['student_data'] = $student;
} else {
    $_SESSION['error'] = "No student found for this search term.";
}

header("Location: /proto/Entrep_prototype/Dashboard/parent/pindex.php");exit();
