<?php

session_start();

require_once(__DIR__ . '/../login_feature/db.php');

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require __DIR__ . '/../vendor/autoload.php';

if($_SERVER['REQUEST_METHOD'] === 'POST'){

    $email =$_POST['email'];

    $stmt = $pdo->prepare("SELECT * FROM parent WHERE email = ?");
    $stmt ->execute ([$email]);
    $user=$stmt->fetch(PDO::FETCH_ASSOC);

    if($user) {
        $reset_code = rand(100000, 999999);

        $update = $pdo->prepare("UPDATE parent SET reset_code = ? WHERE email = ?");
        $update->execute([$reset_code, $email]);

        $_SESSION['email'] = $email;

        $mail = new PHPMailer(true);

        try{
            $mail->isSMTP();
            $mail->Host   = 'smtp.gmail.com';
            $mail->SMTPAuth = true;
            $mail->Username = 'gacscy@gmail.com';
            $mail->Password = 'cfng saav wjog xhbw';
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port = 587;

            $mail->setFrom('gacscy@gmail.com', 'Cyrish Gacasan');
            $mail->addAddress($email, 'THIS IS YOUR CLIENT');

            $mail->isHTML(true);
            $mail->Subject ="Password Reset Code";

            $mail->Body = "
                    <p>Hello, This is your password Reset Code: {$reset_code}</p>
            ";
            $mail->AltBody = "Hello, Use the code below to reset your password: \n\n {$reset_code} \n\n";
            $mail->send();

            $_SESSION['email_sent'] = true;

            $_SESSION['success'] = "A verification code has been sent to your email";
            header('Location: penter_code.php ');
            exit();

        } catch (Exception $e){
            $_SESSION['error'] = "Message could not be sent";
            header('Location: pforgot_password.php');
            exit();

        }

        $_SESSION['success'] = "A verification code has been sent to your email";
        header('Location : penter_code.php ');
        exit();
    } else{
        $_SESSION['error'] = "No user found with that email";
        header('Location: pforgot_password.php');
        exit();
    }

}
?>

<!DOCTYPE html>
<html>
    <head>
        <title>Forgot Password</title>
        <link rel="stylesheet" href="/proto/Entrep_prototype/styles/style.css">
        <meta charset ="UTF-8">
        <meta name="viewport" content="width-device-width, initial-scale=1.0"> 
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    </head>


<body>
   <div class="container">
   <div class="card bg-white" style="width:50%;  min-height: 400px; margin: 100px auto; ">
   <img src = "/proto/Entrep_prototype/images/logo1.png" style="width: 150px; height: 150px; margin: 0 auto; margin-top: 20px;"alt = "this is the logo">
                            
             <?php if (isset($_SESSION['error'])): ?>
                <div class="alert alert-danger fade-alert text-center mx-auto" role="alert" style="max-width: 400px;">
                    <?= $_SESSION['error']; unset($_SESSION['error']); ?>
                </div>
            <?php endif; ?>

            <?php if (isset($_SESSION['success'])): ?>
                <div class="alert alert-success fade-alert text-center mx-auto" role="alert" style="max-width: 400px;">
                    <?= $_SESSION['success']; unset($_SESSION['success']); ?>
                </div>
            <?php endif; ?>

            <form action="pforgot_password.php" method="POST" style="width:50%;  margin: auto; ">
                
                <input type = "email" name="email" class="form-control mb-3" style="width: 80%; margin: auto;" placeholder="Enter your email" required>
                <button type="submit" class="btn btn-primary w-100">Send Verification Code</button>
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
            </form>
        </div>
   </div>
</body>
</html>
