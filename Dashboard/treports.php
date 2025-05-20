<?php
session_start();
require_once '../login_feature/db.php';

if (!isset($_SESSION['user_id'])) {
    $_SESSION['error'] = "You are not logged in.";
    header('Location: /proto/Entrep_prototype/login_feature/tlogin.php');
    exit();
}

$teacherId = $_SESSION['user_id'];

// Verify teacher exists
$stmtUser = $pdo->prepare("SELECT * FROM teacher WHERE id = ?");
$stmtUser->execute([$teacherId]);
$user = $stmtUser->fetch(PDO::FETCH_ASSOC);

if (!$user) {
    $_SESSION['error'] = "User not found in database.";
    header('Location: /proto/Entrep_prototype/login_feature/tlogin.php');
    exit();
}

// Fetch attendance reports ONLY for students of this teacher by joining student table
$stmt = $pdo->prepare("
    SELECT ar.*
    FROM attendance_reports ar
    INNER JOIN student s ON ar.student_id = s.student_id
    WHERE s.Teacher_Id = ?
    ORDER BY ar.report_date DESC
");
$stmt->execute([$teacherId]);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <title>Reports</title>
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <link rel="stylesheet" href="styles/styles.css" />
    <link rel="stylesheet" href="../bootstrap-5.3.3-dist/css/bootstrap.min.css" />
    <script src="https://unpkg.com/html5-qrcode"></script>
    <style>
        .scrollable-content .table-responsive {
            max-height: 400px;
            overflow-y: auto;
        }
        #qrScannerModal {
            display: none;
            position: fixed;
            top: 0; left: 0;
            width: 100%; height: 100%;
            background-color: rgba(0, 0, 0, 0.7);
            justify-content: center;
            align-items: center;
            z-index: 9999;
        }
        #qrScannerModal > div {
            background: #fff;
            padding: 20px;
            border-radius: 10px;
            text-align: center;
        }
        .btn-img {
            width: 24px;
            height: 24px;
            object-fit: contain;
        }
        .custom-btn {
            padding: 4px 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            border: none;
            background-color: transparent;
        }
        .content-wrapper {
                flex-grow: 1;
                overflow-y: auto;
                padding: 20px;
            }
    </style>
</head>
<body style="overflow-x: hidden;">
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
    <div class="main-content">
        <div class="topbar d-flex justify-content-between align-items-center">
            <div class="search">
                <input type="text" placeholder="Search . . ." />
            </div>
            <div class="user-profile d-flex align-items-center gap-2">
                <span><?= htmlspecialchars($user['firstname']); ?></span>
                <img src="<?= $user['profile_picture'] ?: '../images/account.png'; ?>" alt="user" style="width: 40px; height: 40px; border-radius: 50%; object-fit: cover;" />
            </div>
        </div>

        <?php if (isset($_SESSION['success'])): ?>
            <div class="alert alert-success"><?= $_SESSION['success']; unset($_SESSION['success']); ?></div>
        <?php endif; ?>
        <?php if (isset($_SESSION['error'])): ?>
            <div class="alert alert-danger"><?= $_SESSION['error']; unset($_SESSION['error']); ?></div>
        <?php endif; ?>

        <div class="scrollable-content">
            <div id="qrScannerModal">
                <div>
                    <div id="reader" style="width: 300px; margin: auto;"></div>
                    <button onclick="stopScanner()" class="btn btn-danger mt-3">Cancel</button>
                </div>
            </div>

           <div class="d-flex justify-content-between align-items-center mb-3">
    <h4>Attendance Reports</h4>

    <!-- Right-aligned buttons -->
    <div class="d-flex align-items-center">
        <!-- QR Scan Button -->
        <button class="btn custom-btn me-2" onclick="startQrScan()" title="Scan QR">
            <img src="../images/qr.png" alt="Scan QR" class="btn-img" width="32" height="32" />
        </button>

        <!-- Print Button -->
        <a href="treports_print.php" title="Print Report" target="_blank">
            <img src="../images/printer.png" alt="Print Report" width="32" height="32">
        </a>
    </div>
</div>

            <div class="table-responsive">
                <table class="table table-bordered" id="attendanceTable">
                    <thead>
                        <tr>
                            <th>Student ID</th>
                            <th>Section</th>
                            <th>Absent</th>
                            <th>Present</th>
                            <th>Tardiness</th>
                            <th>Date & Time</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                       <?php while ($row = $stmt->fetch(PDO::FETCH_ASSOC)): ?>
                            <tr>
                            <td>
                                <?= htmlspecialchars($row['student_id']) ?>
                                <input type="hidden" name="student_id" form="form-<?= htmlspecialchars($row['student_id']) ?>" value="<?= htmlspecialchars($row['student_id']) ?>">
                            </td>
                            <td>
                                <?= htmlspecialchars($row['section']) ?>
                                <input type="hidden" name="section" form="form-<?= htmlspecialchars($row['student_id']) ?>" value="<?= htmlspecialchars($row['section']) ?>">
                                <input type="hidden" name="student_name" form="form-<?= htmlspecialchars($row['student_id']) ?>" value="<?= htmlspecialchars($row['student_name']) ?>">
                            </td>
                            <td>
                                <form method="POST" action="treport_db.php" id="form-<?= htmlspecialchars($row['student_id']) ?>">
                                    <input type="number" name="absent" class="form-control" value="<?= (int)$row['absent'] ?>">
                            </td>
                            <td>
                                    <input type="number" name="present" class="form-control" value="<?= (int)$row['present'] ?>">
                            </td>
                            <td>
                                    <input type="number" name="tardiness" class="form-control" value="<?= (int)$row['tardiness'] ?>">
                            </td>
                            <td><?= htmlspecialchars($row['report_date']) ?></td>
                            <td>
                                    <button type="submit" class="custom-btn">
                                        <img src="../images/save.png" class="btn-img" alt="Save" />
                                    </button>
                                </form>
                            </td>
                            </tr>
                            <?php endwhile; ?>

                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script>
let html5QrCode;

function startQrScan() {
    document.getElementById('qrScannerModal').style.display = 'flex';
    html5QrCode = new Html5Qrcode("reader");

    Html5Qrcode.getCameras().then(cameras => {
        if (cameras.length) {
            html5QrCode.start(
                { facingMode: "environment" },
                { fps: 10, qrbox: 250 },
                qrCodeMessage => {
                    const parts = qrCodeMessage.split(' | ');
                    const student_id = parts[0].split(':')[1]?.trim();
                    const student_name = parts[1].split(':')[1]?.trim();
                    const section = parts[2].split(':')[1]?.trim();

                    if (student_id && student_name && section) {
                        addRow(student_id, student_name, section);
                        stopScanner();
                    } else {
                        alert("Invalid QR format.");
                    }
                },
                error => console.log("Scan error:", error)
            );
        }
    }).catch(err => console.log("Camera error:", err));
}

function stopScanner() {
    if (html5QrCode) {
        html5QrCode.stop().then(() => {
            html5QrCode.clear();
            document.getElementById('qrScannerModal').style.display = 'none';
        }).catch(console.error);
    }
}

function addRow(student_id, student_name, section) {
    const tableBody = document.querySelector('#attendanceTable tbody');
    const formId = 'form-' + student_id;

    if (document.getElementById(formId)) {
        alert("This student is already in the table.");
        return;
    }

    const now = new Date();
    const formattedDateTime = now.toLocaleString();

    // Create the row
    const newRow = document.createElement('tr');
    
    // Put the form *inside* the last <td>
    newRow.innerHTML = `
  <td>
    ${student_id}
    <input type="hidden" name="student_id" value="${student_id}">
  </td>
  <td>
    ${section}
    <input type="hidden" name="section" value="${section}">
    <input type="hidden" name="student_name" value="${student_name}">
  </td>
  <td><input type="number" name="absent" class="form-control" value="0" form="${formId}" /></td>
  <td><input type="number" name="present" class="form-control" value="1" form="${formId}" /></td>
  <td><input type="number" name="tardiness" class="form-control" value="0" form="${formId}" /></td>
  <td>${formattedDateTime}</td>
  <td>
    <form method="POST" action="treport_db.php" id="${formId}">
      <button type="submit" class="custom-btn">
          <img src="../images/save.png" class="btn-img" alt="Save" />
      </button>
      <input type="hidden" name="student_id" value="${student_id}">
      <input type="hidden" name="section" value="${section}">
      <input type="hidden" name="student_name" value="${student_name}">
    </form>
  </td>
`;
    tableBody.appendChild(newRow);
}

</script>
<script>
  // Fade out alert after 2 seconds
  setTimeout(() => {
    const alert = document.querySelector('.alert');
    if (alert) {
      alert.style.transition = 'opacity 0.5s ease-out';
      alert.style.opacity = '0';
      setTimeout(() => alert.remove(), 500); // remove from DOM after fade
    }
  }, 2000);
</script>


</body>
</html>
