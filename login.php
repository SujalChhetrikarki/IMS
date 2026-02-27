<?php
session_start();
include("config/db.php");

if(isset($_POST['login'])){

$email = $_POST['email'];
$password = md5($_POST['password']);

$sql = "SELECT * FROM users WHERE email='$email' AND password='$password'";
$result = $conn->query($sql);

if($result->num_rows > 0){
    $user = $result->fetch_assoc();
    $_SESSION['user_id'] = $user['id'];
    $_SESSION['role'] = $user['role'];

    if($user['role']=="admin"){
        header("Location: admin/dashboard.php");
    } elseif($user['role']=="manager"){
        header("Location: manager/dashboard.php");
    } else {
        header("Location: sales/dashboard.php");
    }
} else {
    $error = "Invalid Email or Password!";
}
}
?>

<!DOCTYPE html>
<html>
<head>
<title>Meta EV Inventory Login</title>
<meta name="viewport" content="width=device-width, initial-scale=1">

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

<style>

body {
    background: linear-gradient(135deg, #000000, #1a1a1a);
    min-height: 100vh;
    display: flex;
    align-items: center;
    justify-content: center;
    font-family: 'Segoe UI', sans-serif;
    padding: 15px;
}

.login-card {
    background: #ffffff;
    border-radius: 15px;
    padding: 35px 30px;
    width: 100%;
    max-width: 420px;
    box-shadow: 0 10px 30px rgba(255,0,0,0.25);
    text-align: center;
}

.logo {
    width: 110px;
    margin-bottom: 15px;
}

.company-name {
    font-weight: 600;
    font-size: 20px;
    margin-bottom: 20px;
    color: #111;
}

h3 {
    color: #111;
    margin-bottom: 20px;
}

.form-control {
    border-radius: 8px;
    padding: 10px;
}

.form-control:focus {
    border-color: red;
    box-shadow: 0 0 6px rgba(255,0,0,0.4);
}

.btn-custom {
    background: red;
    border: none;
    border-radius: 8px;
    padding: 10px;
    font-weight: 600;
    color: #fff;
}

.btn-custom:hover {
    background: #cc0000;
}

.error-msg {
    color: red;
    font-size: 14px;
    margin-bottom: 10px;
}

/* Mobile optimization */
@media(max-width: 576px){
    .login-card{
        padding: 25px 20px;
    }
    .logo{
        width: 90px;
    }
}

</style>
</head>

<body>

<div class="login-card">

    <img src="assets/images/logo.webp" class="logo" alt="Meta EV Logo">

    <div class="company-name">Meta EV Inventory System</div>

    <?php if(isset($error)) { ?>
        <div class="error-msg"><?php echo $error; ?></div>
    <?php } ?>

    <form method="POST">
        <input type="email" name="email" class="form-control mb-3" placeholder="Enter Email" required>

        <input type="password" name="password" class="form-control mb-3" placeholder="Enter Password" required>

        <button name="login" class="btn btn-custom w-100">Login</button>
    </form>

</div>

</body>
</html>