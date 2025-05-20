<?php 
session_start();
require_once '../../login_feature/db.php';

if (!isset($_SESSION['user_id'])) {
    $_SESSION['error'] = "You are not logged in.";
    header('Location: /proto/Entrep_prototype/parent_feature/plogin.php');
    exit();
}

$id = $_SESSION['user_id'];

$stmt = $pdo->prepare("SELECT * FROM parent WHERE id = ?");
$stmt->execute([$id]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user) {
    $_SESSION['error'] = "User not found in database.";
    header('Location: /proto/Entrep_prototype/parent_feature/plogin.php');
    exit();

    $parent = $user;  // Use the $user data fetched above
}
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Parent Dashboard</title>
    <link rel="stylesheet" href="/proto/Entrep_prototype/Dashboard/styles/styles.css">
    <link rel="stylesheet" href="/proto/Entrep_prototype/bootstrap-5.3.3-dist/css/bootstrap.min.css">
    <style>
        .logout-confirmation {
            display: none;
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            background: white;
            padding: 30px;
            box-shadow: 0 0 20px rgba(0,0,0,0.3);
            z-index: 10001;
            border-radius: 12px;
            width: 300px;
            text-align: center;
            opacity: 0;
            transition: opacity 0.3s ease;
        }

        .logout-confirmation.show {
            opacity: 1;
        }

        .overlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            height: 100%;
            width: 100%;
            background: rgba(255, 255, 255, 0.2);
            backdrop-filter: blur(6px);
            z-index: 10000;
            transition: opacity 0.3s ease;
        }

        .overlay.show {
            display: block;
        }

        .w-45 {
            width: 45%;
        }
    </style>
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
    <div class="main-content" style="width: 85%; float: left;">  
        <!-- TOPBAR -->
        <div class="topbar">
            <div class="search">
                <input type="text" placeholder="Search . . .">
            </div>
            <div class="user-profile d-flex align-items-center">
                <span class="me-2"><?= htmlspecialchars($user['firstname']) ?></span>
                <img src="<?= !empty($user['profile_picture']) ? $user['profile_picture'] : '/proto/Entrep_prototype/images/account.png' ?>"
                     alt="Profile"
                     style="width: 40px; height: 40px; object-fit: cover; border-radius: 50%;">
            </div>
        </div>

        <!-- CONTENT -->
        <div class="content">
            <div class="containersl">
                <div class="cardsl text-center p-4 shadow-sm border rounded" style="max-width: 400px; margin: 0 auto;">
                    <img src="<?php echo $user['profile_picture'] ?: '/proto/Entrep_prototype/images/account.png'; ?>" alt="Profile Picture" style="width: 100px; height: 100px; object-fit: cover; border-radius: 50%; margin-bottom: 20px;">
                    <h2 class="mb-3">Logging out?</h2>
                    <button type="button" class="btn btn-danger w-100" onclick="showLogoutPopup()">Logout</button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- OVERLAY + CONFIRMATION -->
<div class="overlay" id="overlay"></div>

<div class="logout-confirmation" id="logoutCard">
    <h5>Are you sure you want to log out?</h5>
    <div class="d-flex justify-content-between mt-3">
    <a href="/proto/Entrep_prototype/Dashboard/parent/plogout_db.php" class="btn btn-danger w-45">Yes</a>
     <button class="btn btn-secondary w-45" onclick="hideLogoutPopup()">No</button>
    </div>
</div>

<script>
    function showLogoutPopup() {
        const overlay = document.getElementById('overlay');
        const card = document.getElementById('logoutCard');
        overlay.style.display = 'block';
        card.style.display = 'block';
        setTimeout(() => {
            overlay.classList.add('show');
            card.classList.add('show');
        }, 10); // allow time for transition
    }

    function hideLogoutPopup() {
        const overlay = document.getElementById('overlay');
        const card = document.getElementById('logoutCard');
        overlay.classList.remove('show');
        card.classList.remove('show');
        setTimeout(() => {
            overlay.style.display = 'none';
            card.style.display = 'none';
        }, 300); // wait for transition to finish
    }
</script>

</body>
</html>
