<?php
session_start();
include("../config/db.php");

if(!isset($_SESSION['role']) || $_SESSION['role']!="admin"){
    header("Location: ../login.php");
    exit();
}

if(isset($_POST['add'])){
    $name = $_POST['name'];
    $email = $_POST['email'];
    $password = md5($_POST['password']);
    $role = $_POST['role'];

    $conn->query("INSERT INTO users(name,email,password,role)
    VALUES('$name','$email','$password','$role')");

    $success = "User Added Successfully!";
}
?>

<!DOCTYPE html>
<html>
<head>
<title>Add User - Meta EV</title>
<meta name="viewport" content="width=device-width, initial-scale=1">

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" rel="stylesheet">

<style>

body{
    margin:0;
    font-family:'Segoe UI',sans-serif;
    background:#f4f6f9;
}

/* Sidebar */
.sidebar{
    height:100vh;
    width:250px;
    position:fixed;
    background:#000;
    padding-top:20px;
}

.sidebar .logo{
    width:120px;
    display:block;
    margin:0 auto 25px;
}

.sidebar a{
    padding:14px 22px;
    display:block;
    color:#fff;
    text-decoration:none;
    background:#000;
    border-left:4px solid transparent;
    transition:0.3s;
}

.sidebar a:hover{
    background:#111;
    border-left:4px solid red;
}

.sidebar a.active{
    background:#111;
    border-left:4px solid red;
}

/* Main */
.main{
    margin-left:250px;
    padding:25px;
}

/* Card */
.form-card{
    background:#fff;
    padding:30px;
    border-radius:12px;
    box-shadow:0 4px 15px rgba(0,0,0,0.08);
}

.btn-custom{
    background:red;
    border:none;
}

.btn-custom:hover{
    background:#cc0000;
}

/* Responsive */
@media(max-width:768px){
    .sidebar{
        width:200px;
    }
    .main{
        margin-left:200px;
    }
}

@media(max-width:576px){
    .sidebar{
        position:relative;
        width:100%;
        height:auto;
    }
    .main{
        margin-left:0;
    }
}

</style>
</head>

<body>

<!-- Sidebar -->
<div class="sidebar">
    <img src="../assets/images/logo.webp" class="logo">

    <a href="dashboard.php"><i class="fa fa-home"></i> Home</a>
    <a href="add_user.php" class="active"><i class="fa fa-user-plus"></i> Add User</a>
    <a href="add_product.php"><i class="fa fa-box"></i> Add Product</a>
    <a href="../login.php"><i class="fa fa-sign-out-alt"></i> Logout</a>
</div>

<!-- Main Content -->
<div class="main">

    <div class="container-fluid">

        <div class="row justify-content-center">
            <div class="col-lg-6 col-md-8 col-sm-12">

                <div class="form-card">

                    <h4 class="mb-4 text-center">Add New User</h4>

                    <?php if(isset($success)) { ?>
                        <div class="alert alert-success">
                            <?php echo $success; ?>
                        </div>
                    <?php } ?>

                    <form method="POST">

                        <div class="mb-3">
                            <label class="form-label">Full Name</label>
                            <input type="text" name="name" class="form-control" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Email Address</label>
                            <input type="email" name="email" class="form-control" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Password</label>
                            <input type="password" name="password" class="form-control" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Select Role</label>
                            <select name="role" class="form-select" required>
                                <option value="">Choose Role</option>
                                <option value="manager">Manager</option>
                                <option value="sales">Sales</option>
                            </select>
                        </div>

                        <button name="add" class="btn btn-custom w-100 text-white">
                            <i class="fa fa-plus"></i> Add User
                        </button>

                    </form>

                </div>

            </div>
        </div>

    </div>

</div>

</body>
</html>