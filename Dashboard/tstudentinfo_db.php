<?php 
session_start();
require_once '../login_feature/db.php';
error_log("SESSION DATA: " . print_r($_SESSION, true));

// ========== HANDLE ADD, EDIT ==========
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Common fields
    $student_id   = $_POST['student_id'] ?? '';
    $student      = $_POST['student'] ?? '';
    $parent_name  = $_POST['parent_name'] ?? '';
    $year_level   = $_POST['year_level'] ?? '';
    $teacher_id   = $_POST['teacher_id'] ?? '';
    $section      = $_POST['section'] ?? '';
    $subject      = $_POST['subject'] ?? '';

    error_log("Form Data - Student ID: $student_id, Student: $student, Teacher ID: $teacher_id");

    // EDIT LOGIC
    if (isset($_POST['edit'])) {
        // Validate input
        if (!empty($student_id) && !empty($student)) {
            try {
                $stmt = $pdo->prepare("UPDATE student SET Student = ?, Parent_Name = ?, Year_Level = ?, Teacher_Id = ?, Section = ?, Subject = ? WHERE Student_Id = ?");
                $result = $stmt->execute([
                    $student, $parent_name, $year_level, $teacher_id, $section, $subject, $student_id
                ]);

                $_SESSION[$result ? 'success' : 'error'] = $result ? "Student updated successfully." : "Failed to update student.";
            } catch (PDOException $e) {
                error_log("Edit Error: " . $e->getMessage());
                $_SESSION['error'] = "Database error during update.";
            }
        } else {
            $_SESSION['error'] = "Missing required fields for update.";
        }
    }

    // ADD LOGIC (Only if not editing)
    else {
        if (!empty($student_id) && !empty($student)) {
            $checkStudent = $pdo->prepare("SELECT * FROM student WHERE Student_Id = ?");
            $checkStudent->execute([$student_id]);

            if ($checkStudent->rowCount() > 0) {
                $_SESSION['error'] = "Student ID already exists.";
            } else {
                $checkTeacher = $pdo->prepare("SELECT * FROM teacher WHERE id = ?");
                $checkTeacher->execute([$teacher_id]);

                if ($checkTeacher->rowCount() > 0) {
                    $stmt = $pdo->prepare("INSERT INTO student (Student_Id, Student, Parent_Name, Year_Level, Teacher_Id, Section, Subject) 
                                           VALUES (?, ?, ?, ?, ?, ?, ?)");
                    try {
                        $result = $stmt->execute([ 
                            $student_id, $student, $parent_name, $year_level,
                            $teacher_id, $section, $subject
                        ]);

                        $_SESSION[$result ? 'success' : 'error'] = $result ? "Student added successfully." : "Error adding student.";
                    } catch (PDOException $e) {
                        error_log("Insert error: " . $e->getMessage());
                        $_SESSION['error'] = "An unexpected database error occurred.";
                    }
                } else {
                    $_SESSION['error'] = "Invalid Teacher ID.";
                }
            }
        } else {
            $_SESSION['error'] = "Please fill in the required fields.";
        }
    }

    header("Location: tStudentInfo.php");
    exit();
}

// ========== HANDLE DELETE ==========
if (isset($_GET['delete_id'])) {
    $delete_id = $_GET['delete_id'];
    try {
        $stmt = $pdo->prepare("DELETE FROM student WHERE Student_Id = ?");
        $stmt->execute([$delete_id]);
        $_SESSION['success'] = "Student deleted successfully.";
    } catch (PDOException $e) {
        error_log("Delete error: " . $e->getMessage());
        $_SESSION['error'] = "Cannot delete student: dependent records may exist.";
    }

    header("Location: tStudentInfo.php");
    exit();
}
?>
