<?php 
session_start();
require_once '../login_feature/db.php';

if (!isset($_SESSION['user_id'])) {
  $_SESSION['error'] = "You are not logged in.";
  header('Location: /proto/Entrep_prototype/login_feature/tlogin.php');
  exit();
}

$teacherId = $_SESSION['user_id'];  // <--- Define here BEFORE using

$stmt = $pdo->prepare("SELECT * FROM teacher WHERE id = ?");
$stmt->execute([$teacherId]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user) {
  $_SESSION['error'] = "User not found in database.";
  header('Location: /proto/Entrep_prototype/login_feature/tlogin.php');
  exit();
}

$studentsBySection = [];
try {
    $stmt = $pdo->prepare("SELECT * FROM student WHERE Teacher_Id = ? ORDER BY Section");
    $stmt->execute([$teacherId]);
    while ($row = $stmt->fetch()) {
        $studentsBySection[$row['Section']][] = $row;
    }
} catch (PDOException $e) {
    error_log("Database fetch error: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Student Info</title>
  <link rel="stylesheet" href="styles/styles.css">
  <link rel="stylesheet" href="../bootstrap-5.3.3-dist/css/bootstrap.min.css">
  <style>
    .topbar { margin-bottom: 20px; }
    .input-group-text { width: 40px; display: flex; justify-content: center; }
    .icon-btn { width: 20px; height: 20px; cursor: pointer; margin-right: 8px; }
    .scroll-container {
      max-height: 500px;
      overflow-y: auto;
      scrollbar-width: thin;
      scrollbar-color: #ccc transparent;
      background-color: #f9f9f9;
      padding-right: 10px;
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

      <?php if (isset($_SESSION['error'])): ?>
            <div class="alert alert-danger fade-alert" role="alert">
                <?= $_SESSION['error']; unset($_SESSION['error']); ?>
            </div>
        <?php endif; ?>

        <?php if (isset($_SESSION['success'])): ?>
            <div class="alert alert-success fade-alert" role="alert">
                <?= $_SESSION['success']; unset($_SESSION['success']); ?>
            </div>
        <?php endif; ?>

      <main class="col-md-12 px-md-12">
        <div class="row">
          <div class="col-md-3">
            <div class="card">
              <div class="card-header">ADD STUDENT</div>
              <div class="card-body">
                <form action="tstudentinfo_db.php" method="POST">
                  <div class="mb-3"><input type="text" class="form-control" name="student_id" placeholder="Student ID"></div>
                  <div class="mb-3"><input type="text" class="form-control" name="student" placeholder="Student Name"></div>
                  <div class="mb-3"><input type="text" class="form-control" name="parent_name" placeholder="Parent Name"></div>
                  <div class="mb-3"><input type="text" class="form-control" name="year_level" placeholder="Year Level"></div>
                  <div class="mb-3"><input type="text" class="form-control" name="teacher_id" placeholder="Teacher ID"></div>
                  <div class="mb-3"><input type="text" class="form-control" name="section" placeholder="Section"></div>
                  <div class="mb-3"><input type="text" class="form-control" name="subject" placeholder="Subject"></div>
                  <div class="d-grid gap-2">
                    <button class="btn btn-primary" type="submit">Add Student</button>
                  </div>
                </form>
              </div>
            </div>
          </div>

          <div class="col-md-9">
            <div class="scroll-container">
              <?php foreach ($studentsBySection as $section => $students): ?>
                <div class="card mb-4">
                  <div class="card-header bg-secondary text-white">
                    Section: <?= htmlspecialchars($section) ?>
                  </div>
                  <div class="card-body">
                    <table class="table table-bordered table-striped">
                    <thead>
  <tr>
    <th>Student ID</th>
    <th>Student</th>
    <th>Parent Name</th>
    <th>Year Level</th>
    <th>Teacher ID</th>
    <th>Section</th>
    <th>Subject</th>
    <th>Action</th> <!-- New column -->
  </tr>
</thead>
<tbody>
  <?php foreach ($students as $student): ?>
    <tr>
      <td><?= htmlspecialchars($student['Student_Id']) ?></td>
      <td><?= htmlspecialchars($student['Student']) ?></td>
      <td><?= htmlspecialchars($student['Parent_Name']) ?></td>
      <td><?= htmlspecialchars($student['Year_Level']) ?></td>
      <td><?= htmlspecialchars($student['Teacher_Id']) ?></td>
      <td><?= htmlspecialchars($student['Section']) ?></td>
      <td><?= htmlspecialchars($student['Subject']) ?></td>
      <td>
        <img 
          src="../images/qr-code.png" 
          alt="Show QR" 
          style="width: 24px; cursor: pointer;" 
          onclick="showQRModal('<?= $student['Student_Id'] ?>')">

           <!-- Edit Button -->
            <img src="../images/pencil.png" alt="Edit" style="width: 24px; cursor: pointer;" onclick='showEditModal(<?= json_encode($student) ?>)'>

  <!-- Delete Button -->
            <a href="tstudentinfo_db.php?delete_id=<?= $student['Student_Id'] ?>" onclick="return confirm('Are you sure you want to delete this student?')">
            <img src="../images/delete.png" alt="Delete" style="width: 24px; cursor: pointer;">
            </a>
        </td>
    </tr>
  <?php endforeach; ?>
</tbody>


                    </table>
                  </div>
                </div>
              <?php endforeach; ?>
            </div>
          </div>
        </div>
      </main>
    </div>
  </div>
  <!-- QR Modal -->
<div id="qrModal" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; 
  background-color: rgba(0, 0, 0, 0.5); backdrop-filter: blur(5px); z-index: 1050; justify-content: center; align-items: center;">
  <div style="background: #fff; padding: 20px; border-radius: 10px; position: relative; width: 300px;">
    <span style="position: absolute; top: 10px; right: 15px; cursor: pointer; font-weight: bold;" onclick="hideQRModal()">×</span>
    <h5 class="text-center">Student QR Code</h5>
    <div id="qrContainer" class="text-center mt-3">
      <img id="qrImage" src="" alt="QR Code" style="width: 200px;">
    </div>
  </div>
</div>


  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <script>
    $(document).ready(function () {
      $("#search").on("keyup", function () {
        let value = $(this).val().toLowerCase();
        $("table tbody tr").filter(function () {
          $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1);
        });
      });
    });
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
      }, 2000); // Visible for 2x seconds
    });
  });

  function showQRModal(studentId) {
    const qrImage = document.getElementById('qrImage');
    qrImage.src = 'generate_code.php?student_id=' + encodeURIComponent(studentId);

    const modal = document.getElementById('qrModal');
    modal.style.display = 'flex';
  }

  function hideQRModal() {
    const modal = document.getElementById('qrModal');
    modal.style.display = 'none';
  }
</script>

<script>
function showEditModal(student) {
  document.getElementById('editStudentId').value = student.Student_Id;
  document.getElementById('editStudent').value = student.Student;
  document.getElementById('editParentName').value = student.Parent_Name;
  document.getElementById('editYearLevel').value = student.Year_Level;
  document.getElementById('editTeacherId').value = student.Teacher_Id;
  document.getElementById('editSection').value = student.Section;
  document.getElementById('editSubject').value = student.Subject;
  
  document.getElementById('editModal').style.display = 'flex';
}

function hideEditModal() {
  document.getElementById('editModal').style.display = 'none';
}
</script>

<div id="editModal" class="modal" tabindex="-1" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; 
  background-color: rgba(0,0,0,0.5); backdrop-filter: blur(5px); z-index: 1051; justify-content: center; align-items: center;">
  <div style="background: white; padding: 20px; border-radius: 10px; width: 400px; position: relative;">
    <span onclick="hideEditModal()" style="position: absolute; top: 10px; right: 15px; font-weight: bold; cursor: pointer;">×</span>
    <h5 class="text-center">Edit Student</h5>
    <form method="POST" action="tstudentinfo_db.php">
  <input type="hidden" name="edit" value="1"> <!-- this tells PHP it's an edit -->
  <input type="hidden" name="student_id" id="editStudentId">
      <div class="mb-2"><input type="text" name="student" class="form-control" id="editStudent" placeholder="Student Name"></div>
      <div class="mb-2"><input type="text" name="parent_name" class="form-control" id="editParentName" placeholder="Parent Name"></div>
      <div class="mb-2"><input type="text" name="year_level" class="form-control" id="editYearLevel" placeholder="Year Level"></div>
      <div class="mb-2"><input type="text" name="teacher_id" class="form-control" id="editTeacherId" placeholder="Teacher ID"></div>
      <div class="mb-2"><input type="text" name="section" class="form-control" id="editSection" placeholder="Section"></div>
      <div class="mb-2"><input type="text" name="subject" class="form-control" id="editSubject" placeholder="Subject"></div>
      <div class="text-center"><button type="submit" class="btn btn-success">Save Changes</button></div>
    </form>
  </div>
</div>      
</body>
</html>
