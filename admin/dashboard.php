<?php
session_start();
if(!isset($_SESSION['role']) || $_SESSION['role']!="admin"){
    header("Location: ../login.php");
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
<title>Admin Dashboard - Meta EV</title>
<meta name="viewport" content="width=device-width, initial-scale=1">

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" rel="stylesheet">

<style>

body{
    margin:0;
    font-family: 'Segoe UI', sans-serif;
    background:#f4f6f9;
}

/* Sidebar */
.sidebar{
    height:100vh;
    width:250px;
    position:fixed;
    background:#fff;
    padding-top:20px;
    transition:0.3s;
}

.sidebar .logo{
    width:120px;
    display:block;
    margin:0 auto 20px;
}

.sidebar a{
    padding:12px 20px;
    display:block;
    color:#ccc;
    text-decoration:none;
    font-size:15px;
    transition:0.3s;
}

.sidebar a:hover{
    background:red;
    color:#fff;
}

.sidebar a i{
    margin-right:10px;
}

/* Main Content */
.main{
    margin-left:250px;
    padding:20px;
}

/* Top Navbar */
.topbar{
    background:#fff;
    padding:15px 20px;
    border-radius:10px;
    box-shadow:0 2px 10px rgba(0,0,0,0.1);
    margin-bottom:20px;
}

/* Cards */
.dashboard-card{
    background:#fff;
    padding:20px;
    border-radius:12px;
    box-shadow:0 4px 15px rgba(0,0,0,0.08);
    text-align:center;
    transition:0.3s;
}

.dashboard-card:hover{
    transform:translateY(-5px);
}

.dashboard-card i{
    font-size:30px;
    color:red;
    margin-bottom:10px;
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
    <a href="add_user.php"><i class="fa fa-user-plus"></i> Add User</a>
    <a href="add_product.php"><i class="fa fa-box"></i> Add Product</a>
    <a href="../login.php"><i class="fa fa-sign-out-alt"></i> Logout</a>

</div>

<!-- Main Content -->
<div class="main">

    <div class="topbar">
        <h4>Welcome Admin 👋</h4>
    </div>

    <div class="row g-4">

        <div class="col-md-4">
            <div class="dashboard-card">
                <i class="fa fa-users"></i>
                <h5>Manage Users</h5>
                <p>Add and manage system users</p>
            </div>
        </div>

        <div class="col-md-4">
            <div class="dashboard-card">
                <i class="fa fa-box-open"></i>
                <h5>Manage Products</h5>
                <p>Add and update inventory</p>
            </div>
        </div>

        <div class="col-md-4">
            <div class="dashboard-card">
                <i class="fa fa-chart-line"></i>
                <h5>Reports</h5>
                <p>View system analytics</p>
            </div>
        </div>

    </div>

</div>

</body>
</html>