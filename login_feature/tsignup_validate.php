<?php

session_start();

require'db.php';

if($_SERVER['REQUEST_METHOD'] === 'POST'){
    $firstname = $_POST['firstname'];
    $lastname = $_POST['lastname'];
    $username = $_POST['username'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $confirm = $_POST['confirm_password'];

     // Password complexity check here:
    if (!preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[\W_]).{8,}$/', $password)) {
        $_SESSION['error'] = "Password must be at least 8 characters and include uppercase, lowercase, number, and symbol.";
        header('Location: psignup.php');
        exit();
    }

    if ($password !== $confirm){
        $_SESSION['error'] = "Password do not match.";
        header('Location: tsignup.php');
        exit();
}

    $stmt = $pdo->prepare("SELECT * FROM teacher WHERE username= ?");
    $stmt->execute([$username]);

    if($stmt->rowCount() > 0){
        $_SESSION['error'] = "Username already exist";
        header('Location: tsignup.php');
        exit();
    }

    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
    $stmt = $pdo->prepare("INSERT INTO teacher (firstname, lastname, username, email, password) VALUES(?, ?, ?, ?, ?)");

    if ($stmt->execute([$firstname,$lastname,$username,$email,$hashedPassword])){

    $_SESSION['success'] = "Your account has been created. You can now login.";
    header('Location: tlogin.php');
    exit();

    }else{
        echo("There is an error");
        exit();
    }
}
