<?php
session_start();
?>
<!DOCTYPE html>
<html>
<head>
    <title>Parent Login</title>
    <link rel="stylesheet" href="/proto/Entrep_prototype/styles/styles.css">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0"> 
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
<div class="container">
    <div class="card" style="width:50%; margin: 100px auto;">
        <div class="text-center">
            <img src="/proto/Entrep_prototype/images/logo1.png" style="width: 150px; height: 150px; margin-top: 20px;" alt="Logo">
        </div>

        <div class="card-body">
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

            <form action="plogin_validate.php" method="POST">
                <input class="form-control mb-3" type="text" name="username" placeholder="Username" required>
                <input class="form-control mb-3" type="password" name="password" placeholder="Password" required>

                  <!-- Google reCAPTCHA -->
                <div class="g-recaptcha" data-sitekey="6Ldiyz4rAAAAAIML_i4-mZoV5ftxQMRWs2kpIsri"></div>
                
                 <button class="btn btn-primary w-100" type="submit">Login</button>
            </form>

            <p class="mt-3">Don't have an account? <a href="psignup.php">Signup</a></p>
            <p>Forgot Password? <a href="pforgot_password.php">Click here!</a></p>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const alerts = document.querySelectorAll('.fade-alert');
    alerts.forEach(alert => {
        setTimeout(() => {
            alert.style.transition = 'opacity 0.5s ease';
            alert.style.opacity = '0';
            setTimeout(() => alert.remove(), 500);
        }, 2000);
    });
});

// Password validation on form submit
document.querySelector('form').addEventListener('submit', function(e) {
    const password = this.querySelector('input[name="password"]').value;
    const regex = /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[\W_]).{8,}$/;
    if (!regex.test(password)) {
        e.preventDefault();
        alert('Password must be at least 8 characters and include uppercase, lowercase, number, and symbol.');
    }
});
</script>

<script src="https://www.google.com/recaptcha/api.js" async defer></script>
</body>
</html>
