<?php
session_start();
include("../config/db.php");

if(!isset($_SESSION['user_id']) || $_SESSION['role'] != 'manager'){
    header("Location: ../index.php");
    exit;
}

/* STOCK UPDATE */
if(isset($_GET['action'], $_GET['id'])){
    $id = (int)$_GET['id'];

    if($_GET['action'] === 'add'){
        $conn->query("UPDATE products SET quantity = quantity + 1 WHERE id=$id");
    }

    if($_GET['action'] === 'less'){
        $conn->query("UPDATE products SET quantity = GREATEST(quantity - 1, 0) WHERE id=$id");
    }

    header("Location: dashboard.php");
    exit;
}

/* CSV IMPORT */
if(isset($_POST['import_csv']) && $_FILES['csv']['size'] > 0){
    $file = fopen($_FILES['csv']['tmp_name'], "r");
    fgetcsv($file); // skip header

    while(($row = fgetcsv($file)) !== false){
        $stmt = $conn->prepare(
            "INSERT INTO products 
            (product_name, category, model_no, price, quantity, added_by)
            VALUES (?, ?, ?, ?, ?, ?)"
        );
        $stmt->bind_param(
            "sssdis",
            $row[0], $row[1], $row[2], $row[3], $row[4], $_SESSION['user_id']
        );
        $stmt->execute();
    }
    fclose($file);
}
?>

<!DOCTYPE html>
<html>
<head>
<title>Manager Inventory | Meta EV</title>
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
.stock-btn{width:32px;height:32px;}
.low-stock{background:#dc3545;}
.ok-stock{background:#198754;}
</style>
</head>

<body>

<div class="container-fluid">
<div class="row">

<!-- SIDEBAR (hidden on mobile) -->
<div class="col-md-2 sidebar d-none d-md-block">
    <h5 class="text-white mb-4">Meta EV</h5>
    <a class="active">Inventory</a>
    <a href="../logout.php">Logout</a>
</div>

<!-- MAIN -->
<div class="col-md-10 ms-auto">

<div class="topbar d-flex justify-content-between align-items-center">
    <h5>Manager Inventory</h5>
    <span class="text-muted">Logged in as Manager</span>
</div>

<!-- CSV IMPORT -->
<div class="card p-3 mt-4">
<h6>Import Products (CSV)</h6>
<form method="POST" enctype="multipart/form-data" class="row g-2">
    <div class="col-md-6">
        <input type="file" name="csv" class="form-control" required>
    </div>
    <div class="col-md-2">
        <button name="import_csv" class="btn btn-danger w-100">Import</button>
    </div>
</form>
</div>

<!-- INVENTORY TABLE -->
<div class="card mt-4 p-3">
<h5 class="mb-3">Product Inventory</h5>

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
<th>Action</th>
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
<td><?= $row['added_by'] ?></td>
<td>
<a href="?action=add&id=<?= $row['id'] ?>" class="btn btn-success btn-sm stock-btn">+</a>
<a href="?action=less&id=<?= $row['id'] ?>" class="btn btn-warning btn-sm stock-btn">−</a>
</td>
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