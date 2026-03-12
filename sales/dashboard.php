<?php
session_start();
include("../config/db.php");

/* SALES ACCESS ONLY */
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'sales') {
    header("Location: ../index.php");
    exit;
}

$user_id = $_SESSION['user_id'];
$msg = "";

/* =========================
   STOCK UPDATE
========================= */

if (isset($_GET['action'], $_GET['id'])) {

    $id = (int)$_GET['id'];

    $product = $conn->query("SELECT quantity FROM products WHERE id=$id")->fetch_assoc();

    if($product){

        $old_qty = $product['quantity'];
        $new_qty = $old_qty;

        if ($_GET['action'] === 'add') {
            $conn->query("UPDATE products SET quantity = quantity + 1 WHERE id=$id");
            $new_qty = $old_qty + 1;
        }

        if ($_GET['action'] === 'less') {
            $conn->query("UPDATE products SET quantity = GREATEST(quantity-1,0) WHERE id=$id");
            $new_qty = max($old_qty - 1,0);
        }

        $conn->query("
        INSERT INTO stock_history
        (product_id,old_quantity,new_quantity,edited_by)
        VALUES ($id,$old_qty,$new_qty,$user_id)
        ");
    }

    header("Location: dashboard.php");
    exit;
}


/* =========================
   HANDLE SALE
========================= */

if(isset($_POST['make_sale'])){

$product_id = (int)$_POST['product_id'];
$qty        = (int)$_POST['quantity'];

$product = $conn->query("
SELECT quantity,price 
FROM products 
WHERE id=$product_id
")->fetch_assoc();

if(!$product){
$msg="Invalid product";
}

elseif($qty > $product['quantity']){
$msg="Not enough stock";
}

else{

$conn->query("
UPDATE products
SET quantity = quantity-$qty
WHERE id=$product_id
");

$stmt=$conn->prepare("
INSERT INTO sales
(product_id,quantity,sold_price,sold_by)
VALUES(?,?,?,?)
");

$stmt->bind_param(
"iidi",
$product_id,
$qty,
$product['price'],
$user_id
);

$stmt->execute();

$msg="Sale completed";
}

}
/* =========================
   FETCH PRODUCTS
========================= */

$products=$conn->query("
SELECT id,product_name,model_no,quantity,price
FROM products
ORDER BY product_name
");


/* =========================
   SALES HISTORY
========================= */

$sales=$conn->query("
SELECT 
p.product_name,
s.quantity,
s.sold_price,
s.created_at
FROM sales s
JOIN products p ON p.id=s.product_id
WHERE s.sold_by=$user_id
ORDER BY s.id DESC
");

?>

<!DOCTYPE html>
<html>
<head>

<title>Sales Dashboard</title>

<meta name="viewport" content="width=device-width, initial-scale=1">

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">

<style>

body{
background:#f4f6f9;
font-family:'Segoe UI',sans-serif;
}

/* SIDEBAR */

.sidebar{
background:#111;
min-height:100vh;
padding:20px;
}

.sidebar a{
color:#bbb;
text-decoration:none;
display:block;
padding:12px;
border-radius:8px;
}

.sidebar a:hover,.active{
background:red;
color:#fff;
}

/* TOPBAR */

.topbar{
background:#fff;
padding:15px;
box-shadow:0 2px 10px rgba(0,0,0,.08);
}

/* CARDS */

.card{
border-radius:15px;
}

/* STOCK BADGE */

.low-stock{background:#dc3545;}

.ok-stock{background:#198754;}

.stock-btn{
width:32px;
height:32px;
padding:0;
}

</style>

</head>

<body>

<div class="container-fluid">
<div class="row">

<!-- SIDEBAR -->

<div class="col-md-2 sidebar d-none d-md-block">

<h5 class="text-white mb-4">Meta EV</h5>

<a class="active">Sales Dashboard</a>

<a href="../logout.php">Logout</a>

</div>


<!-- MAIN -->

<div class="col-md-10 ms-auto">

<div class="topbar d-flex justify-content-between">

<h5>Sales Panel</h5>

<span class="text-muted">
Logged in as Sales
</span>

</div>

<?php if($msg): ?>

<div class="alert alert-info mt-3">

<?= $msg ?>

</div>

<?php endif; ?>


<!-- PRODUCT TABLE -->

<div class="card mt-4 p-3">

<h5 class="mb-3">
Products
</h5>

<div class="table-responsive">

<table class="table table-hover align-middle">

<thead class="table-light">

<tr>

<th>#</th>

<th>Product</th>

<th>Model</th>

<th>Stock</th>

<th>Stock Control</th>

<th>Sell</th>

</tr>

</thead>

<tbody>

<?php $i=1; while($row=$products->fetch_assoc()): ?>

<tr>

<td><?= $i++ ?></td>

<td><?= htmlspecialchars($row['product_name']) ?></td>

<td><?= htmlspecialchars($row['model_no']) ?></td>

<td>

<span class="badge <?= $row['quantity']<5?'low-stock':'ok-stock' ?>">

<?= $row['quantity'] ?>

</span>

</td>


<!-- STOCK BUTTONS -->

<td>

<a href="?action=add&id=<?= $row['id'] ?>" class="btn btn-success btn-sm stock-btn">

+

</a>

<a href="?action=less&id=<?= $row['id'] ?>" class="btn btn-warning btn-sm stock-btn">

-

</a>

</td>


<!-- SELL FORM -->

<td>

<?php if($row['quantity']>0): ?>

<form method="POST" class="d-flex gap-2">

<input type="hidden" name="product_id" value="<?= $row['id'] ?>">

<input type="number"

name="quantity"

min="1"

max="<?= $row['quantity'] ?>"

class="form-control form-control-sm"

placeholder="Qty"

required>

<button name="make_sale"

class="btn btn-danger btn-sm">

Sell

</button>

</form>

<?php else: ?>

<span class="text-muted">

Out of stock

</span>

<?php endif; ?>

</td>

</tr>

<?php endwhile; ?>

</tbody>

</table>

</div>

</div>



<!-- SALES HISTORY -->

<div class="card mt-4 p-3">

<h5 class="mb-3">

My Sales History

</h5>

<div class="table-responsive">

<table class="table table-hover">

<thead>

<tr>

<th>Product</th>

<th>Qty</th>

<th>Price</th>

<th>Date</th>

</tr>

</thead>

<tbody>

<?php if($sales && $sales->num_rows>0): ?>

<?php while($s=$sales->fetch_assoc()): ?>

<tr>

<td><?= htmlspecialchars($s['product_name']) ?></td>

<td><?= $s['quantity'] ?></td>

<td>

₹<?= number_format($s['sold_price'],2) ?>

</td>

<td>

<?= date("d M Y",strtotime($s['created_at'])) ?>

</td>

</tr>

<?php endwhile; ?>

<?php else: ?>

<tr>

<td colspan="4" class="text-center text-muted">

No sales yet

</td>

</tr>

<?php endif; ?>
</tbody>

</table>

</div>

</div>
</div>
</div>
</div>

</body>
</html>