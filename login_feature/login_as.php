<?php

session_start();

?>
<!DOCTYPE html>
<html>
    <head>
        <title>Login As</title>
        <link rel="stylesheet" href="/proto/Entrep_prototype/styles/styles.css">
        <meta charset ="UTF-8">
        <meta name="viewport" content="width-device-width, initial-scale=1.0"> 
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    </head>


<body>
   <div class="container">
            <div class="card" style="width:30%; min-height: 400px; margin: 100px auto; ">
            <img src = "/proto/Entrep_prototype/images/logo1.png" style="width: 150px; height: 150px; margin-top: 20px;"alt = "this is the logo">

            <div style="margin-top: 30px;"></div>
            
            <form action="redirect_role.php" method="POST">
                <button  class="btn btn-primary" type="submit" name="role" value="teacher" >Teacher</button><br>
            </br><button  class="btn btn-primary" type="submit" name="role" value="parent">Parent</button>
                
            </form>
        
        </div>
   </div>
</body>
</html>
