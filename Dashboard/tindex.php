<?php 
session_start();
require_once '../login_feature/db.php';

// Fetch attendance data
$teacherId = $_SESSION['user_id'];
$stmt = $pdo->prepare("SELECT * FROM attendance_reports WHERE Teacher_Id = ?");
$stmt->execute([$teacherId]);
$attendanceData = [];
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $attendanceData[$row['section']][] = $row;
}

// Fetch student data
$studentStmt = $pdo->prepare("SELECT Section, Student_Id, Student FROM student WHERE Teacher_Id = ? ORDER BY Section");
$studentStmt->execute([$teacherId]);
$students = [];
while ($row = $studentStmt->fetch(PDO::FETCH_ASSOC)) {
    $students[$row['Section']][] = [
        'id' => $row['Student_Id'],
        'name' => $row['Student']
    ];
}
if (!isset($_SESSION['user_id'])) {
  $_SESSION['error'] = "You are not logged in.";
  header('Location: /proto/Entrep_prototype/login_feature/tlogin.php');
  exit();
}

$teacherId = $_SESSION['user_id'];

$stmt = $pdo->prepare("SELECT * FROM teacher WHERE id = ?");
$stmt->execute([$teacherId]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user) {
  $_SESSION['error'] = "User not found in database.";
  header('Location: /proto/Entrep_prototype/login_feature/tlogin.php');
  exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <link rel="stylesheet" href="../bootstrap-5.3.3-dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="styles/styles.css">
    <style>
        .progress-bar {
            font-size: 12px;
            padding: 5px;
        }
        .card {
            border: 1px solid #ddd;
            margin-bottom: 20px;
        }
        .d-none {
            display: none;
        }
        .show {
            display: block;
        }
       
    </style>
</head>
<body>
    <div class="dashboard">
       <!-- SIDEBAR -->  
    <div class="sidebar" style="width: 15%; float: left;">    <div class="heads text-center">
        
        <div style="font-weight: bold; font-size: 18px;">DASHBOARD</div>
        <img class="logo" src="/proto/Entrep_prototype/images/logo1.png" alt="logo">
    </div>
    <ul class="menu d-flex flex-column align-items-start gap-3 mt-4 ps-3">
        <li class="d-flex align-items-center gap-2" style="margin: 0px auto;">
            <a href="tindex.php" class="active">
                <img src="../images/index.png" alt="Dashboard" style="width: 40px; height: 40px;" class="hover-icon">
            </a>
            
        </li>
        <li class="d-flex align-items-center gap-2" style="margin: 0px auto;">
            <a href="tStudentInfo.php">
                <img src="../images/info.png" alt="Student Info" style="width: 40px; height: 40px;" class="hover-icon">
            </a>
            
        </li>
        <li class="d-flex align-items-center gap-2" style="margin: 0px auto;">
            <a href="treports.php">
                <img src="../images/reports.png" alt="Reports" style="width: 40px; height: 40px;" class="hover-icon">
            </a>
            
        </li>
        <li class="d-flex align-items-center gap-2"style="margin: 0px auto;">
            <a href="taccounts.php">
                <img src="../images/profile.png" alt="Accounts" style="width: 40px; height: 40px;" class="hover-icon">
            </a>
           
        </li>
        <li class="d-flex align-items-center gap-2" style="margin: 0px auto;">
            <a href="tsettings.php">
                <img src="../images/settings.png" alt="Settings" style="width: 40px; height: 40px;" class="hover-icon">
            </a>
            
        </li>
        <li class="d-flex align-items-center gap-2" style="margin: 0px auto;">
            <a href="tlogout.php">
                <img src="../images/out.png" alt="Log out" style="width: 40px; height: 40px;" class="hover-icon">
            </a>
            
        </li>
    </ul>
</div>


    <!-- MAIN CONTENT -->
    <div class="main-content" style="width: 85%; float: left;">   
            <div class="topbar">
            <div class="search">
                <input type="text" placeholder="Search . . .">
            </div>
            <div class="user-profile">
                <span><?php echo htmlspecialchars($user['firstname']); ?></span>
                <img src="<?php echo $user['profile_picture'] ?: '../images/account.png'; ?>" alt="user" style="width: 40px; height: 40px; object-fit: cover; border-radius: 50%;">

            </div>
            </div>

            <div class="content">
                <h4>Dashboard - Attendance Overview</h4>

                <div class="alert alert-success d-none" id="syncedAlert">
                    Attendance data synced successfully!
                </div>

                <div class="row">
                    <!-- Legend Column -->
                    <div class="col-md-4 mb-3">
                        <div class="card p-3">
                            <h6 class="mb-2">Legend</h6>
                            <div class="d-flex align-items-center mb-1">
                                <div class="me-2" style="width: 20px; height: 20px; background-color: #dc3545; border-radius: 3px;"></div>
                                <span>Absent</span>
                            </div>
                            <div class="d-flex align-items-center mb-1">
                                <div class="me-2" style="width: 20px; height: 20px; background-color: #28a745; border-radius: 3px;"></div>
                                <span>Present</span>
                            </div>
                            <div class="d-flex align-items-center">
                                <div class="me-2" style="width: 20px; height: 20px; background-color: #ffc107; border-radius: 3px;"></div>
                                <span>Tardiness</span>
                            </div>
                        </div>

                        <!-- Sync Button Image -->
                        <div class="text-center">
                            <img id="syncBtn" src="../images/sync.png" alt="Sync Data" style="cursor: pointer; width: 20%; max-width: 150px;  background-color: lightblue; " class="btn custom-btn mt-3 rounded-circle">
                        </div>
                    </div>

                    <!-- Summary Column -->
                    <div class="col-md-8">
                        <div id="summaryContainer"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        window.onload = function () {
            loadDashboardData();
            document.getElementById('syncBtn').addEventListener('click', function () {
                loadDashboardData();
                showSyncedMessage();
            });
        };

        function loadDashboardData() {
            const sectionData = <?php echo json_encode($students); ?>;
            const attendanceData = <?php echo json_encode($attendanceData); ?>;

            const summaryContainer = document.getElementById('summaryContainer');
            summaryContainer.innerHTML = '';

            Object.keys(sectionData).forEach(sectionName => {
                const report = attendanceData[sectionName] || [];
                const totalStudents = sectionData[sectionName].length;
                let totalAbsent = 0;
                let totalPresent = 0;
                let totalTardiness = 0;

                report.forEach(attendance => {
                    totalAbsent += parseInt(attendance.absent) || 0;
                    totalPresent += parseInt(attendance.present) || 0;
                    totalTardiness += parseInt(attendance.tardiness) || 0;
                });

                const absentPercent = totalStudents > 0 ? (totalAbsent / totalStudents) * 100 : 0;
                const presentPercent = totalStudents > 0 ? (totalPresent / totalStudents) * 100 : 0;
                const tardinessPercent = totalStudents > 0 ? (totalTardiness / totalStudents) * 100 : 0;

                const sectionSummary = document.createElement('div');
                sectionSummary.classList.add('card', 'p-3', 'mb-3');

                sectionSummary.innerHTML = `
                    <h5>${sectionName}</h5>
                    <p><strong>Total Students:</strong> ${totalStudents}</p>
                    <p><strong>Total Absents:</strong> ${totalAbsent}</p>
                    <p><strong>Total Presents:</strong> ${totalPresent}</p>
                    <p><strong>Total Tardiness:</strong> ${totalTardiness}</p>
                    <div class="progress">
                        <div class="progress-bar bg-danger" role="progressbar" style="width: ${absentPercent}%" aria-valuenow="${absentPercent}" aria-valuemin="0" aria-valuemax="100">Absent (${absentPercent.toFixed(1)}%)</div>
                        <div class="progress-bar bg-success" role="progressbar" style="width: ${presentPercent}%" aria-valuenow="${presentPercent}" aria-valuemin="0" aria-valuemax="100">Present (${presentPercent.toFixed(1)}%)</div>
                        <div class="progress-bar bg-warning" role="progressbar" style="width: ${tardinessPercent}%" aria-valuenow="${tardinessPercent}" aria-valuemin="0" aria-valuemax="100">Tardiness (${tardinessPercent.toFixed(1)}%)</div>
                    </div>
                `;

                summaryContainer.appendChild(sectionSummary);
            });
        }

        function showSyncedMessage() {
            const alertBox = document.getElementById('syncedAlert');
            alertBox.classList.remove('d-none');
            alertBox.classList.add('show');

            setTimeout(() => {
                alertBox.classList.remove('show');
                alertBox.classList.add('d-none');
            }, 2000);
        }
    </script>
    <script>
  // Wait for the page to load
  document.addEventListener('DOMContentLoaded', function () {
    const alerts = document.querySelectorAll('.fade-alert');
    alerts.forEach(alert => {
      setTimeout(() => {
        alert.style.transition = 'opacity 0.5s ease';
        alert.style.opacity = '0';
        setTimeout(() => {
          alert.remove(); // Remove it from the DOM after fade
        }, 500); // Wait for fade-out animation
      }, 2000); // Visible for 2 seconds
    });
  });
</script>
</body>
</html>
