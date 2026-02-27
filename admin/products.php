<?php
session_start();
include("../config/db.php");

/* ADMIN ACCESS ONLY */
if(!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin'){
    header("Location: ../index.php");
    exit;
}
?>

<!DOCTYPE html>
<html>
<head>
<title>Admin | Product Stock</title>
<meta name="viewport" content="width=device-width, initial-scale=1">

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">

<style>
body{background:#f4f6f9;font-family:'Segoe UI',sans-serif;}
.sidebar{background:#111;min-height:100vh;padding:20px;}
.sidebar a{color:#bbb;text-decoration:none;display:block;padding:12px;border-radius:8px;}
.sidebar a:hover,.active{background:red;color:#fff;}
.topbar{background:#fff;padding:15px;box-shadow:0 2px 10px rgba(0,0,0,.08);}
.card{border-radius:15px;}
.low-stock{background:#dc3545;}
.ok-stock{background:#198754;}
</style>
</head>

<body>

<div class="container-fluid">
<div class="row">

<!-- SIDEBAR -->
<div class="col-md-2 sidebar d-none d-md-block">
    <h5 class="text-white mb-4">Meta EV</h5>
    <a href="dashboard.php">Dashboard</a>
    <a class="active">Products</a>
    <a href="../logout.php">Logout</a>
    
</div>

<!-- MAIN -->
<div class="col-md-10 ms-auto">

<div class="topbar d-flex justify-content-between align-items-center">
    <h5>Product Inventory (View Only)</h5>
    <span class="text-muted">Admin Panel</span>
</div>

<!-- PRODUCT TABLE -->
<div class="card mt-4 p-3">
<h5 class="mb-3">All Products & Stock</h5>

<div class="table-responsive">
<table class="table table-hover align-middle">
<thead class="table-light">
<tr>
<th>#</th>
<th>Product</th>
<th>Category</th>
<th>Model</th>
<th>Price</th>
<th>Stock</th>
<th>Added By</th>
<th>Created At</th>
</tr>
</thead>
<tbody>

<?php
$i=1;
$res = $conn->query("SELECT * FROM products ORDER BY created_at DESC");
while($row = $res->fetch_assoc()):
?>
<tr>
<td><?= $i++ ?></td>
<td><?= htmlspecialchars($row['product_name']) ?></td>
<td><?= htmlspecialchars($row['category']) ?></td>
<td><?= htmlspecialchars($row['model_no']) ?></td>
<td>₹<?= number_format($row['price'],2) ?></td>
<td>
<span class="badge <?= $row['quantity'] < 5 ? 'low-stock':'ok-stock' ?>">
<?= $row['quantity'] ?>
</span>
</td>
<td><?= htmlspecialchars($row['added_by']) ?></td>
<td><?= date("d M Y", strtotime($row['created_at'])) ?></td>
</tr>
<?php endwhile; ?>

</tbody>
</table>
</div>
</div>

</div>
</div>
</div>

</body>
</html>