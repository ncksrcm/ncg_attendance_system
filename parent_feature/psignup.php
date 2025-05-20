<?php

session_start();

?>


<!DOCTYPE html>
<html>
    <head>
        <title>Signup</title>
        <link rel="stylesheet" href="/proto/Entrep_prototype/styles/styles.css">
        <meta charset ="UTF-8">
        <meta name="viewport" content="width-device-width, initial-scale=1.0"> 
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
        <style>
            .card {
                width: 350px; /* Adjust this width to make the card slimmer */
                margin: 0 auto; /* Center the card */
            }
        </style>
    </head>


<body>
   <div class="container">
        <div class="card"style="min-height: 400px;">
        <img src = "/proto/Entrep_prototype/images/logo1.png" style="width: 150px; height: 150px; margin-top: 20px;"alt = "this is the logo">
            <?php if (isset($_SESSION['error'])): ?>

            <div class ="alert alert-danger fade-alert" role="alert"> 
                <?=$_SESSION['error']; unset($_SESSION['error']); ?>

            </div>

            <?php endif;  ?>

          <form action="psignup_validate.php" method="POST">
              <input class="form-control mb-3" type="text" placeholder="Firstname" name="firstname" required>
              <input class="form-control mb-3" type="text" placeholder="Lastname" name="lastname" required>
              <input class="form-control mb-3" type="text" placeholder="Username" name="username" required>
              <input class="form-control mb-3" type="email" placeholder="Email" name="email" required>
              <input class="form-control mb-3" type="password" placeholder="Password" name="password" required>
              <input class="form-control mb-4" type="password" placeholder="Re-Enter Password" name="confirm_password" required>
              <button class="btn btn-primary w-100" type="submit">Signup</button>
          </form>
            <p>Already have an account? <a href="plogin.php">Login</a></p>
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

  document.querySelector('form').addEventListener('submit', function(e) {
      const password = this.querySelector('input[name="password"]').value;
      const regex = /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[\W_]).{8,}$/;
      if (!regex.test(password)) {
          e.preventDefault();
          alert('Password must be at least 8 characters and include uppercase, lowercase, number, and symbol.');
            }
    });
  
</script>
        </div>
   </div>
</body>
</html>