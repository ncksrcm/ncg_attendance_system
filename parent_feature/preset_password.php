<?php

session_start();

require_once '../login_feature/db.php';



    if (!isset($_SESSION['email']) || !isset($_SESSION['reset_code_verified']) || !$_SESSION['reset_code_verified']) {
        $_SESSION['error'] = "No Email Session found; Please try again;";
        header('Location: pforgot-password.php');
        exit();
    }

    if($_SERVER['REQUEST_METHOD'] === 'POST'){

        $newPassword = $_POST['password'];
        $confirmPassword = $_POST['confirm_password'];

        if($newPassword === $confirmPassword){
            //if password is same it is hashed
            $hashedPassword = password_hash($newPassword, PASSWORD_BCRYPT);

            $stmt = $pdo->prepare("UPDATE parent SET password = ? WHERE email= ?");
            $stmt->execute([$hashedPassword, $_SESSION['reset_email']]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            unset($_SESSION['reset_email']);
            unset($_SESSION['reset_code_verified']);

            $_SESSION['success'] = "Your password has been reset successfully";
            header ('Location: plogin.php');
            exit();

        }else{
             $_SESSION['error'] = "Passwords do not match. Please try again.";
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
            
           
            <form action="preset_password.php" method="POST" style="width:50%;  margin: auto; ">
                <div class="mb-3">
                <input  type = "password" class="form-control"  placeholder="Enter New Password" name="password" required>
                </div>
                <div class="mb-3">
                <input  type = "password" class="form-control"  placeholder="Confirm Password" name="confirm_password" required>
                </div>
                <button  class="btn btn-primary w-100" type="submit">Reset</button>
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
            </form> 
        </div>
   </div>
</body>
<html>