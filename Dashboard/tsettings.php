<?php
session_start();
require_once '../login_feature/db.php';

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
<html>
<head>
    <title>Settings</title>
    <link rel="stylesheet" href="styles/styles.css">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../bootstrap-5.3.3-dist/css/bootstrap.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        /* Content Wrapper */
        .content-wrapper {
            padding-top: 80px; /* Adjust for the original topbar height */
            text-align: center;
        }

        .settings-title {
            font-size: 24px;
            font-weight: bold;
            margin-bottom: 40px;
        }

        .card-container {
            display: flex;
            justify-content: center;
            gap: 30px;
            flex-wrap: wrap;
        }

        .card {
            width: 300px;
        }

        /* Ensure the content area does not overlap the fixed topbar */
        html, body {
                height: 100%;
                margin: 0;
                padding: 0;
                overflow: hidden;
            }

            .dashboard {
                display: flex;
                height: 100vh;
                overflow: hidden;
            }

            .main-content {
                flex-grow: 1;
                display: flex;
                flex-direction: column;
                overflow: hidden;
            }

            .content-wrapper {
                flex-grow: 1;
                overflow-y: auto;
                padding: 20px;
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
        <!-- TOPBAR START -->
        <div class="topbar">
            <div class="search">
                <input type="text" placeholder="Search . . .">
            </div>
            <div class="user-profile">
            <span><?php echo htmlspecialchars($user['firstname']); ?></span>
                <img src="<?php echo $user['profile_picture'] ?: '../images/account.png'; ?>" alt="user" style="width: 40px; height: 40px; object-fit: cover; border-radius: 50%;">
            </div>
        </div>
        <!-- TOPBAR END -->

        <!-- SETTINGS CONTENT -->
        <div class="content-wrapper">
            <div class="settings-title">System Settings</div>

            <!-- Success and Error messages -->
            <?php if (isset($_SESSION['success'])): ?>
                <div class="alert alert-success text-center fade-alert mx-auto" role="alert" style="max-width: 500px;">
                    <?= $_SESSION['success']; unset($_SESSION['success']); ?>
                </div>
            <?php endif; ?>

            <?php if (isset($_SESSION['error'])): ?>
                <div class="alert alert-danger text-center fade-alert mx-auto" role="alert" style="max-width: 500px;">
                    <?= $_SESSION['error']; unset($_SESSION['error']); ?>
                </div>
            <?php endif; ?>

            <div class="card-container">
                <!-- General Settings -->
                <div class="card">
                    <div class="card-header">General Settings</div>
                    <div class="card-body">
                        <form method="POST" action="tsettings_db.php">
                            <div class="mb-3">
                                <label class="form-label">Timezone</label>
                                <select name="timezone" class="form-control">
                                    <option value="UTC">UTC</option>
                                    <option value="PST">Pacific Standard Time (PST)</option>
                                    <option value="EST">Eastern Standard Time (EST)</option>
                                    <!-- Add more timezones as needed -->
                                </select>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Language</label>
                                <select name="language" class="form-control">
                                    <option value="en">English</option>
                                    <option value="es">Spanish</option>
                                    <option value="fr">French</option>
                                    <!-- Add more languages as needed -->
                                </select>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Email</label>
                                <input type="email" name="email" class="form-control" placeholder="example@email.com" required>
                            </div>
                            <input type="hidden" name="save_general" value="1">
                            <button type="submit" name="save_general" class="btn btn-primary w-100">Save</button>
                        </form>
                    </div>
                </div>

                <!-- Account Settings -->
                <div class="card">
                    <div class="card-header">Account Settings</div>
                    <div class="card-body">
                        <form method="POST" action="tsettings_db.php">
                            <div class="mb-3">
                                <label class="form-label">New Password</label>
                                <input type="password" name="new_password" class="form-control" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Confirm Password</label>
                                <input type="password" name="confirm_password" class="form-control" required>
                            </div>
                            <button type="submit" name="change_password" class="btn btn-warning w-100">Update</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <!-- SETTINGS CONTENT END -->
    </div>
    <!-- MAIN CONTENT END -->
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const alerts = document.querySelectorAll('.fade-alert');
        alerts.forEach(alert => {
            setTimeout(() => {
                alert.style.transition = 'opacity 0.5s ease';
                alert.style.opacity = '0';
                setTimeout(() => {
                    alert.remove();
                }, 500);
            }, 2000);
        });
    });

    // Password complexity validation on submit
    document.querySelector('form').addEventListener('submit', function(e) {
      const password = this.querySelector('input[name="password"]').value;
      const regex = /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[\W_]).{8,}$/;
      if (!regex.test(password)) {
        e.preventDefault();
        alert('Password must be at least 8 characters and include uppercase, lowercase, number, and symbol.');
      }
    });
</script>
</body>
</html>
