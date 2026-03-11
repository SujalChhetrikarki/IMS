<?php
session_start();
include("../config/db.php");

/* =========================
   MANAGER ACCESS ONLY
========================= */
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'manager') {
    header("Location: ../index.php");
    exit;
}

$msg = "";

/* =========================
   ADD / UPDATE PRODUCT
========================= */
if (isset($_POST['add_product'])) {

    $product_name = trim($_POST['product_name'] ?? '');
    $category     = trim($_POST['category'] ?? '');
    $model_no     = trim($_POST['model_no'] ?? '');
    $location     = trim($_POST['location'] ?? '');
    $price        = (float)($_POST['price'] ?? 0);
    $quantity     = (int)($_POST['quantity'] ?? 0);
    $added_by     = $_SESSION['user_id'];

    if ($product_name === '' || $category === '' || $model_no === '') {
        $msg = "All fields are required.";
    } else {

        $check = mysqli_query($conn,"SELECT id FROM products WHERE model_no='$model_no'");

        if ($check && mysqli_num_rows($check) > 0) {

            mysqli_query($conn,
                "UPDATE products 
                 SET quantity = quantity + $quantity 
                 WHERE model_no='$model_no'"
            );

            $msg = "Product already exists. Quantity updated.";

        } else {

            mysqli_query($conn,
                "INSERT INTO products
                (product_name, category, model_no, location, price, quantity, added_by)
                VALUES
                ('$product_name','$category','$model_no','$location','$price','$quantity','$added_by')"
            );

            $msg = "Product added successfully.";
        }
    }
}

/* =========================
   FETCH PRODUCTS
========================= */
$sql = "
SELECT 
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
<title>Manager | Products</title>

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

</style>
</head>

<body>

<!-- SIDEBAR -->

<div class="sidebar">

<img src="../assets/images/logo.webp" class="logo">

<a href="dashboard.php">
<i class="fa fa-home"></i> Dashboard
</a>

<a href="products.php" class="active">
<i class="fa fa-box"></i> Products
</a>

<a href="../logout.php">
<i class="fa fa-sign-out-alt"></i> Logout
</a>

</div>

<!-- MAIN CONTENT -->

<div class="main">

<div class="topbar d-flex justify-content-between align-items-center">
<h5>Manager Panel</h5>
<span class="text-muted">Product Management</span>
</div>

<h4 class="mb-3">Add Product</h4>

<?php if (!empty($msg)) { ?>
<div class="alert alert-success"><?= $msg ?></div>
<?php } ?>

<!-- ADD PRODUCT -->

<div class="card p-4 mb-4">

<form method="POST">

<div class="row">

<div class="col-md-6">
<input type="text" name="product_name" class="form-control mb-3" placeholder="Parts Name" required>
</div>

<div class="col-md-6">
<input type="text" name="category" class="form-control mb-3" placeholder="Category" required>
</div>

<div class="col-md-6">
<input type="text" name="model_no" class="form-control mb-3" placeholder="Parts Number (Unique)" required>
</div>

<div class="col-md-6">
<input type="text" name="location" class="form-control mb-3" placeholder="Location">
</div>

<div class="col-md-4">
<input type="number" step="0.01" name="price" class="form-control mb-3" placeholder="Price" required>
</div>

<div class="col-md-4">
<input type="number" name="quantity" class="form-control mb-3" placeholder="Quantity" required>
</div>

<div class="col-md-12">
<button type="submit" name="add_product" class="btn btn-danger w-100">
Add Product
</button>
</div>

</div>

</form>

</div>

<!-- PRODUCT LIST -->

<div class="card p-3">

<h5 class="mb-3">Product List</h5>

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

<td><?= htmlspecialchars($row['added_by_name'] ?? 'N/A') ?></td>

<td><?= date("d M Y",strtotime($row['created_at'])) ?></td>

</tr>

<?php endwhile; else: ?>

<tr>
<td colspan="9" class="text-center text-muted">No products found</td>
</tr>

<?php endif; ?>

</tbody>
</table>

</div>

</div>

</div>

</body>
</html>
