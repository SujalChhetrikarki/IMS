
<?php
session_start();
include("../config/db.php");

/* =========================
   ADMIN ACCESS ONLY
========================= */
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../index.php");
    exit;
}

/* =========================
   STOCK UPDATE
========================= */
if (isset($_POST['action'], $_POST['product_id'])) {

    $product_id = (int)$_POST['product_id'];

    if ($_POST['action'] === 'add') {
        mysqli_query($conn,
            "UPDATE products SET quantity = quantity + 1 WHERE id = $product_id"
        );
    }

    if ($_POST['action'] === 'less') {
        mysqli_query($conn,
            "UPDATE products 
             SET quantity = CASE 
                WHEN quantity > 0 THEN quantity - 1 
                ELSE 0 
             END
             WHERE id = $product_id"
        );
    }

    header("Location: products.php");
    exit;
}

/* =========================
   FETCH PRODUCTS
========================= */
$sql = "
SELECT 
p.id,
p.product_name,
p.category,
p.model_no,
p.location,
p.price,
p.quantity,
p.created_at,
u.name AS added_by_name
FROM products p
LEFT JOIN users u ON u.id = p.added_by
ORDER BY p.created_at DESC
";

$result = mysqli_query($conn, $sql);
?>

<!DOCTYPE html>
<html>
<head>
<title>Admin | Product Stock</title>

<meta name="viewport" content="width=device-width, initial-scale=1">

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" rel="stylesheet">

<style>

body{
margin:0;
font-family:'Segoe UI',sans-serif;
background:#f4f6f9;
}

/* SIDEBAR */

.sidebar{
height:100vh;
width:250px;
position:fixed;
background:#111;
padding-top:20px;
}

.sidebar .logo{
width:120px;
display:block;
margin:0 auto 25px;
}

.sidebar a{
padding:12px 20px;
display:block;
color:#fff;
text-decoration:none;
font-size:15px;
transition:0.3s;
}

.sidebar a i{
margin-right:10px;
}

.sidebar a:hover{
background:#e60000;
color:#fff;
}

.sidebar a.active{
background:#ff0000;
color:#fff;
}

/* MAIN */

.main{
margin-left:250px;
padding:25px;
}

.topbar{
background:#fff;
padding:15px 20px;
border-radius:10px;
box-shadow:0 2px 10px rgba(0,0,0,0.08);
margin-bottom:20px;
}

.card{
border-radius:12px;
}

.low-stock{
background:#dc3545;
}

.ok-stock{
background:#198754;
}

.stock-btn{
width:32px;
height:32px;
padding:0;
}

</style>
</head>

<body>

<!-- SIDEBAR -->

<div class="sidebar">

<img src="../assets/images/logo.webp" class="logo">

<a href="dashboard.php">
<i class="fa fa-home"></i> Home
</a>

<a href="add_user.php">
<i class="fa fa-user-plus"></i> Add User
</a>

<a href="add_product.php">
<i class="fa fa-box"></i> Add Product
</a>

<a href="products.php" class="active">
<i class="fa fa-box-open"></i> View Products
</a>

<a href="../logout.php">
<i class="fa fa-sign-out-alt"></i> Logout
</a>

</div>

<!-- MAIN CONTENT -->

<div class="main">

<div class="topbar d-flex justify-content-between align-items-center">
<h5>Product Inventory</h5>
<span class="text-muted">Admin Panel</span>
</div>

<div class="card p-3">

<h5 class="mb-3">All Products & Stock</h5>

<div class="table-responsive">

<table class="table table-hover align-middle">

<thead class="table-light">
<tr>
<th>SN</th>
<th>Parts Number</th>
<th>Parts Name</th>
<th>Category</th>
<th>Location</th>
<th>Price</th>
<th>Quantity</th>
<th>Stock Control</th>
<th>Added By</th>
<th>Created At</th>
</tr>
</thead>

<tbody>

<?php
$sn = 1;

if ($result && mysqli_num_rows($result) > 0):

while ($row = mysqli_fetch_assoc($result)):
?>

<tr>

<td><?= $sn++ ?></td>

<td><?= htmlspecialchars($row['model_no']) ?></td>

<td><?= htmlspecialchars($row['product_name']) ?></td>

<td><?= htmlspecialchars($row['category']) ?></td>

<td><?= htmlspecialchars($row['location']) ?></td>

<td>₹<?= number_format($row['price'],2) ?></td>

<td>
<span class="badge <?= $row['quantity'] < 5 ? 'low-stock' : 'ok-stock' ?>">
<?= $row['quantity'] ?>
</span>
</td>

<td>

<form method="POST" class="d-inline">
<input type="hidden" name="product_id" value="<?= $row['id'] ?>">
<input type="hidden" name="action" value="add">
<button class="btn btn-success btn-sm stock-btn">+</button>
</form>

<form method="POST" class="d-inline">
<input type="hidden" name="product_id" value="<?= $row['id'] ?>">
<input type="hidden" name="action" value="less">
<button class="btn btn-warning btn-sm stock-btn">−</button>
</form>

</td>

<td><?= htmlspecialchars($row['added_by_name'] ?? 'N/A') ?></td>

<td><?= date("d M Y",strtotime($row['created_at'])) ?></td>

</tr>

<?php endwhile; else: ?>

<tr>
<td colspan="10" class="text-center text-muted">No products found</td>
</tr>

<?php endif; ?>

</tbody>
</table>

</div>
</div>

</div>

</body>
</html>
