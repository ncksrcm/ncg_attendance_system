<?php

session_start();

require_once '../login_feature/db.php';

if($_SERVER['REQUEST_METHOD'] === 'POST'){

    $enteredCode = $_POST['code'];
    
    $email = $_SESSION['email'];

    if (!isset($_SESSION['email'])) {
        $_SESSION['error'] = "No Email Session found; Please try again;";
        header('Location: pforgot-password.php');
        exit();
    }

    $stmt = $pdo->prepare("SELECT reset_code FROM parent WHERE email= ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);


    if($user){

        if($enteredCode === $user['reset_code']){
            $_SESSION['reset_email'] = $email;
            $_SESSION['reset_code_verified'] = true;


            header('Location: preset_password.php');
            exit();
        }else{
            $_SESSION['error'] = "No user found with that email";
        }
    }

}

?>
<!DOCTYPE html>
<html>
    <head>
        <title>Enter Code</title>
        <link rel="stylesheet" href="/proto/Entrep_prototype/styles/style.css">
        <meta charset ="UTF-8">
        <meta name="viewport" content="width-device-width, initial-scale=1.0"> 
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    </head>
    
    <body>
   
   <div class="card bg-white" style="width:50%; min-height:400px;  margin: 100px auto; ">
   <img src = "/proto/Entrep_prototype/images/logo1.png" style="width: 150px; height: 150px; margin-top: 20px; margin: 0 auto; "alt = "this is the logo">

            <?php 
             if (isset($_SESSION['error'])) {
            echo '<div class="alert alert-danger fade-alert text-center mx-auto" role="alert" style="max-width: 400px;"> ' . $_SESSION['error'] . '</div>';
             unset($_SESSION['error']);
            }

            if (isset($_SESSION['success'])) {
                echo '<div class="alert alert-success fade-alert text-center mx-auto" role="alert" style="max-width: 400px;"> ' . $_SESSION['success'] . '</div>';
                unset($_SESSION['success']);
                }
            
            ?>
            
           
            <form action="penter_code.php" method="POST" style="width:50%;  margin: auto; ">
            
            <input type = "code" name="code" class="form-control mb-3" style="width: 80%; margin: auto;" placeholder="Enter Code" required>
            <button type="submit" class="btn btn-primary w-100">Verify Code</button>
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
            </script>
        </div>
   </div>
</body>
<html>



