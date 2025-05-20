<?php
session_start();

// Include the database connection file
require_once(__DIR__ . '/../../login_feature/db.php'); // Adjust this path if needed

// Fetch parent details
$parentId = $_SESSION['user_id'] ?? null;
if ($parentId) {
    $stmt = $pdo->prepare("SELECT firstname, profile_picture FROM parent WHERE id = ?");
    $stmt->execute([$parentId]);
    $parent = $stmt->fetch(PDO::FETCH_ASSOC);
} else {
    $parent = ['firstname' => 'Guest', 'profile_picture' => ''];
}

// Fetch feedback success message
$feedback_success = $_SESSION['feedback_success'] ?? false;
unset($_SESSION['feedback_success']); // Clear after displaying

// Fetch student data
$student = $_SESSION['student_data'] ?? null;
unset($_SESSION['student_data']); // Unset after use
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Parent Dashboard</title>
    <link rel="stylesheet" href="/proto/Entrep_prototype/Dashboard/styles/styles.css">
    <link rel="stylesheet" href="/proto/Entrep_prototype/bootstrap-5.3.3-dist/css/bootstrap.min.css">
</head>
<body>
<div class="dashboard">
    <!-- SIDEBAR -->
    <div class="sidebar" style="width: 15%; float: left;">    <div class="heads text-center">
        <div style="font-weight: bold; font-size: 18px;">PARENT</div>
        <div style="font-weight: bold; font-size: 18px;">DASHBOARD</div>
        <img class="logo" src="/proto/Entrep_prototype/images/logo1.png" alt="logo">
    </div>
    <ul class="menu d-flex flex-column align-items-start gap-3 mt-4 ps-3">
        <li class="d-flex align-items-center gap-2" style="margin: 0px auto;">
            <a href="/proto/Entrep_prototype/Dashboard/parent/pindex.php" class="active">
                <img src="/proto/Entrep_prototype/images/index.png" alt="Dashboard" style="width: 50px; height: 50px;" class="hover-icon">
            </a>
            
        </li>
        <li class="d-flex align-items-center gap-2"style="margin: 0px auto;">
            <a href="/proto/Entrep_prototype/Dashboard/parent/paccounts.php">
                <img src="/proto/Entrep_prototype/images/profile.png" alt="Accounts" style="width: 50px; height: 50px;" class="hover-icon">
            </a>
           
        </li>
        <li class="d-flex align-items-center gap-2" style="margin: 0px auto;">
            <a href="/proto/Entrep_prototype/Dashboard/parent/psettings.php">
                <img src="/proto/Entrep_prototype/images/settings.png" alt="Settings" style="width: 50px; height: 50px;" class="hover-icon">
            </a>
            
        </li>
        <li class="d-flex align-items-center gap-2" style="margin: 0px auto;">
            <a href="/proto/Entrep_prototype/Dashboard/parent/plogout.php">
                <img src="/proto/Entrep_prototype/images/out.png" alt="Log out" style="width: 50px; height: 50px;" class="hover-icon">
            </a>
            
        </li>
    </ul>
</div>


    <!-- MAIN CONTENT -->
    <div class="main-content" style="width: 85%; float: left;">        <!-- TOPBAR -->
        <div class="topbar d-flex justify-content-between align-items-center px-4 py-2">
            <div class="search">
                <form action="/proto/Entrep_prototype/Dashboard/parent/pindex_db.php" method="GET">
                    <input type="text" name="search_term" placeholder="Search by Student ID or Name" required>
                    <button class="btn btn-primary" type="submit">Search</button>
                </form>
            </div>
            <div class="user-profile d-flex align-items-center">
                <span class="me-2"><?= htmlspecialchars($parent['firstname']) ?></span>
                <img src="<?= !empty($parent['profile_picture']) ? $parent['profile_picture'] : '/proto/Entrep_prototype/images/account.png' ?>"
                     alt="Profile"
                     style="width: 40px; height: 40px; object-fit: cover; border-radius: 50%;">
            </div>
        </div>

        <!-- PAGE CONTENT -->
        <div class="content-wrapper mt-4 px-4">
            <?php if ($feedback_success): ?>
                <div class="toast-container position-fixed bottom-0 end-0 p-3" id="feedbackToastContainer">
                    <div id="feedbackToast" class="toast align-items-center text-white bg-success border-0 show" role="alert">
                        <div class="d-flex">
                            <div class="toast-body">
                                Thank you! Your message has been sent.
                            </div>
                            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
                        </div>
                    </div>
                </div>
                <script>
                    setTimeout(() => {
                        document.getElementById('feedbackToast')?.classList.remove('show');
                    }, 2000);
                </script>
            <?php endif; ?>

            <?php if ($student): ?>
                <h2>Student Report for <?= htmlspecialchars($student['Student']) ?></h2>
                <div class="row mt-4">
                    <div class="col-md-6">
                        <table class="table table-bordered">
                            <thead class="table-warning">
                            <tr>
                                <th>Student Name</th>
                                <th>Student ID</th>
                                <th>Section</th>
                                <th>Subject</th>
                                <th>Absent</th>
                                <th>Present</th>
                                <th>Tardiness</th>
                            </tr>
                            </thead>
                            <tbody class="table-light">
                            <tr>
                                <td><?= htmlspecialchars($student['Student']) ?></td>
                                <td><?= htmlspecialchars($student['Student_Id']) ?></td>
                                <td><?= htmlspecialchars($student['Section']) ?></td>
                                <td><?= htmlspecialchars($student['Subject']) ?></td>
                                <td><?= htmlspecialchars($student['total_absent']) ?></td>
                                <td><?= htmlspecialchars($student['total_present']) ?></td>
                                <td><?= htmlspecialchars($student['total_tardiness']) ?></td>
                            </tr>
                            </tbody>
                        </table>
                    </div>

                    <div class="col-md-6">
                        <?php
                        $total = $student['total_absent'] + $student['total_present'] + $student['total_tardiness'];
                        $absent = $total ? ($student['total_absent'] / $total) * 100 : 0;
                        $present = $total ? ($student['total_present'] / $total) * 100 : 0;
                        $tardiness = $total ? ($student['total_tardiness'] / $total) * 100 : 0;
                        ?>
                        <div class="card">
                            <div class="card-body">
                                <h5 class="card-title">Attendance Overview</h5>
                                <div class="progress mb-3">
                                    <div class="progress-bar bg-danger" role="progressbar" style="width: <?= $absent ?>%"></div>
                                </div>
                                <div class="progress mb-3">
                                    <div class="progress-bar bg-success" role="progressbar" style="width: <?= $present ?>%"></div>
                                </div>
                                <div class="progress mb-3">
                                    <div class="progress-bar bg-warning" role="progressbar" style="width: <?= $tardiness ?>%"></div>
                                </div>
                                <div class="mt-3">
                                    <span class="badge bg-danger">Absent</span>
                                    <span class="badge bg-success">Present</span>
                                    <span class="badge bg-warning text-dark">Tardiness</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Feedback Form -->
                    <div class="col-md-6 mt-4">
                        <div class="card">
                            <div class="card-body">
                                <h5 class="card-title">Leave Feedback</h5>
                                <form action="/proto/Entrep_prototype/Dashboard/parent/pindexdb_message.php" method="POST">
                                    <div class="mb-3">
                                        <label for="message" class="form-label">Message</label>
                                        <textarea class="form-control" id="message" name="message" rows="3" required></textarea>
                                    </div>
                                    <input type="hidden" name="parent_id" value="<?= $parentId ?>">
                                    <input type="hidden" name="student_id" value="<?= $student['Student_Id'] ?>">
                                    <button type="submit" class="btn btn-primary">Submit Feedback</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            <?php else: ?>
                <p class="text-muted mt-5">Search for your child's student ID or name to see attendance records.</p>
            <?php endif; ?>
        </div>
    </div>
</div>

<script src="/proto/Entrep_prototype/bootstrap-5.3.3-dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
