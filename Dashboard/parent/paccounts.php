<?php
session_start();
require_once '../../login_feature/db.php';

// Check if the session contains the user ID
if (!isset($_SESSION['user_id'])) {
    $_SESSION['error'] = "You are not logged in.";
    header('Location: /proto/Entrep_prototype/parent_feature/plogin.php');
    exit();
}

// Get the logged-in parent's ID from the session
$id = $_SESSION['user_id'];

// Fetch the parent's record from the database
$stmt = $pdo->prepare("SELECT * FROM parent WHERE id = ?");
$stmt->execute([$id]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

// If no user is found (edge case)
if (!$user) {
    $_SESSION['error'] = "User not found in database.";
    header('Location: /proto/Entrep_prototype/parent_feature/plogin.php');
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Parent Dashboard</title>
    <link rel="stylesheet" href="/proto/Entrep_prototype/Dashboard/styles/styles.css">
    <link rel="stylesheet" href="/proto/Entrep_prototype/bootstrap-5.3.3-dist/css/bootstrap.min.css">
    <style>
        /* Custom CSS for flexbox layout */
        .content {
            display: flex;
            justify-content: space-between;
            gap: 20px;
        }

        .card {
            flex: 1;  /* Allow the cards to share available space */
        }

        .profile-card {
            max-width: 18rem;
        }

        .add-to-profile-card {
            max-width: 24rem;
        }

        /* Add a bit of padding and spacing between cards */
        .card-body {
            padding: 20px;
        }
    </style>
</head>
<body>

<div class="dashboard">
    <!-- Sidebar -->
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
    <div class="main-content" style="width: 85%; float: left;">  
        <div class="topbar">
            <div class="search">
                <input type="text" placeholder="Search . . .">
            </div>
            <div class="user-profile">
                <!-- Profile picture for top bar -->
                <span><?php echo htmlspecialchars($user['firstname']); ?></span>
                <img src="<?php echo $user['profile_picture'] ?: '/proto/Entrep_prototype/images/account.png'; ?>" alt="user" style="width: 40px; height: 40px; object-fit: cover; border-radius: 50%;">
            </div>
        </div>

        <h2 class="text-center mb-4">Profile</h2>
        <p class="text-center">View and Edit your profile.</p>

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

        <!-- Scrollable wrapper -->
        <div class="horizontal-scroll-wrapper" style="overflow-x: auto; padding-bottom: 10px;">
            <div class="d-flex justify-content-between" style="gap: 20px; min-width: 700px;">
                <!-- Profile Card -->
                <div class="card" style="width: 40%;">
                    <img src="<?php echo $user['profile_picture'] ?: '/proto/Entrep_prototype/images/account.png'; ?>"
                         alt="Profile Picture"
                         style="width: 120px; height: 120px; object-fit: cover; border-radius: 50%; margin: 20px auto 10px; display: block;">
                    <div class="card-body text-center">
                        <h5 class="card-title"><?php echo htmlspecialchars($user['firstname'] . ' ' . $user['lastname']); ?></h5>
                        <p class="card-text"><?php echo htmlspecialchars($user['role']); ?></p>
                        <form action="/proto/Entrep_prototype/Dashboard/parent/paccounts_db.php" method="POST" enctype="multipart/form-data">
                            <div class="mb-3">
                                <label for="profile_picture" class="form-label">Change Profile Picture</label>
                                <input type="file" class="form-control" id="profile_picture" name="profile_picture">
                            </div>
                            <button type="submit" class="btn btn-primary">Update Picture</button>
                        </form>
                    </div>
                </div>

                <!-- Profile Details Card -->
                <div class="card" style="width: 60%;">
                    <div class="card-body">
                        <h5 class="card-title">Add to Profile</h5>
                        <form action="/proto/Entrep_prototype/Dashboard/parent/paccounts_db.php" method="POST" enctype="multipart/form-data">
                        <div class="mb-3">
                                <label for="bio" class="form-label">Bio</label>
                                <textarea class="form-control" id="bio" name="bio" rows="2"><?php echo htmlspecialchars($user['bio'] ?? ''); ?></textarea>
                            </div>
                            <div class="mb-3">
                                <label for="phone_number" class="form-label">Phone Number</label>
                                <input type="text" class="form-control" id="phone_number" name="phone_number" value="<?php echo htmlspecialchars($user['phone_number'] ?? ''); ?>">
                            </div>
                            <div class="mb-3">
                                <label for="hobbies" class="form-label">Hobbies</label>
                                <input type="text" class="form-control" id="hobbies" name="hobbies" value="<?php echo htmlspecialchars($user['hobbies'] ?? ''); ?>">
                            </div>
                            <div class="mb-3">
                                <label for="relationship_status" class="form-label">Relationship Status</label>
                                <input type="text" class="form-control" id="relationship_status" name="relationship_status" value="<?php echo htmlspecialchars($user['relationship_status'] ?? ''); ?>">
                            </div>
                            <div class="mb-3">
                                <label for="address" class="form-label">Address</label>
                                <input type="text" class="form-control" id="address" name="address" value="<?php echo htmlspecialchars($user['address'] ?? ''); ?>">
                            </div>
                            <button type="submit" name="update_profile_info" class="btn btn-success">Save Profile Info</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Bootstrap JS -->
        <script src="/proto/Entrep_prototype/bootstrap-5.3.3-dist/js/bootstrap.bundle.min.js"></script>
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
