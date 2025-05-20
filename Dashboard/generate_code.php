<?php
require_once '../login_feature/db.php';
require '../phpqrcode/qrlib.php'; // Make sure this path is correct

if (isset($_GET['student_id'])) {
    $studentId = $_GET['student_id'];

    // Fetch student info
    $stmt = $pdo->prepare("SELECT Student_Id, Student, Section FROM student WHERE Student_Id = ?");
    $stmt->execute([$studentId]);
    $student = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($student) {
        // Clean and format the data for the QR code
        $qrData = "ID: " . htmlspecialchars($student['Student_Id']) . " | Name: " . htmlspecialchars($student['Student']) . " | Section: " . htmlspecialchars($student['Section']);

        // Set the content type to return a PNG image
        header('Content-Type: image/png');

        // Generate the QR code
        QRcode::png($qrData);

        exit; // End the script after generating the QR code
    } else {
        http_response_code(404);
        echo "Student not found.";
    }
} else {
    http_response_code(400);
    echo "Missing student ID.";
}
?>
