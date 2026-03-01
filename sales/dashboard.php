<?php
session_start();
include("../config/db.php");

/* SALES ACCESS ONLY */
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'sales') {
    header("Location: ../index.php");
    exit;
}
$msg = "";
/* =========================
   HANDLE SALE
========================= */
if (isset($_POST['make_sale'])) {

    $product_id = (int)$_POST['product_id'];
    $qty        = (int)$_POST['quantity'];
    $sold_by    = $_SESSION['user_id'];
    $imageName  = null;

    // Receipt upload
    if (!empty($_FILES['receipt']['name'])) {
        $imageName = time() . "_" . basename($_FILES['receipt']['name']);
        move_uploaded_file(
            $_FILES['receipt']['tmp_name'],
            "uploads/" . $imageName
        );
    }

    $product = $conn->query(
        "SELECT quantity, price FROM products WHERE id=$product_id"
    )->fetch_assoc();

    if (!$product || $qty <= 0) {
        $msg = "Invalid sale.";
    } elseif ($qty > $product['quantity']) {
        $msg = "Not enough stock.";
    } else {

        // Reduce stock
        $conn->query(
            "UPDATE products SET quantity = quantity - $qty WHERE id=$product_id"
        );

        // Insert sale
        $stmt = $conn->prepare(
            "INSERT INTO sales (product_id, quantity, sold_price, sold_by, receipt_image)
             VALUES (?, ?, ?, ?, ?)"
        );
        $stmt->bind_param(
            "iidis",
            $product_id,
            $qty,
            $product['price'],
            $sold_by,
            $imageName
        );
        $stmt->execute();

        $msg = "Sale recorded successfully.";
    }
}

/* =========================
   FETCH PRODUCTS
========================= */
$sql = "
SELECT 
    id,
    product_name,
    model_no,
    quantity,
    price
FROM products
ORDER BY product_name
";
$res = $conn->query($sql);
?>

<!DOCTYPE html>
<html>
<head>
<title>Sales Dashboard</title>
<meta name="viewport" content="width=device-width, initial-scale=1">

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

<style>
body{background:#f4f6f9;}
.sidebar{background:#111;min-height:100vh;padding:20px;}
.sidebar a{color:#bbb;display:block;padding:12px;border-radius:8px;text-decoration:none;}
.sidebar a:hover,.active{background:red;color:#fff;}
.low-stock{background:#dc3545;}
.ok-stock{background:#198754;}
.card{border-radius:15px;}
</style>
</head>

<body>
<div class="container-fluid">
<div class="row">

<!-- SIDEBAR -->
<div class="col-md-2 sidebar d-none d-md-block">
    <h5 class="text-white mb-4">Meta EV</h5>
    <a class="active">Sales</a>
    <a href="../logout.php">Logout</a>
</div>

<!-- MAIN -->
<div class="col-md-10">

<div class="d-flex justify-content-between align-items-center mt-3">
    <h4>Sales Dashboard</h4>
</div>

<?php if($msg): ?>
<div class="alert alert-info mt-2"><?= $msg ?></div>
<?php endif; ?>

<!-- PRODUCT LIST -->
<div class="card mt-3 p-3">
<h5>Sell Product</h5>

<div class="table-responsive">
<table class="table table-hover align-middle">
<thead>
<tr>
    <th>#</th>
    <th>Parts Name</th>
    <th>Parts No</th>
    <th>Stock</th>
    <th>Sell</th>
</tr>
</thead>
<tbody>

<?php $i=1; while($row = $res->fetch_assoc()): ?>
<tr>
    <td><?= $i++ ?></td>
    <td><?= htmlspecialchars($row['product_name']) ?></td>
    <td><?= htmlspecialchars($row['model_no']) ?></td>
    <td>
        <span class="badge <?= $row['quantity'] < 5 ? 'low-stock':'ok-stock' ?>">
            <?= $row['quantity'] ?>
        </span>
    </td>
    <td>
        <?php if($row['quantity'] > 0): ?>
        <form method="POST" enctype="multipart/form-data" class="d-flex gap-2">
            <input type="hidden" name="product_id" value="<?= $row['id'] ?>">
            <input type="number" name="quantity"
                   min="1" max="<?= $row['quantity'] ?>"
                   class="form-control form-control-sm"
                   placeholder="Qty" required>

            <input type="file" name="receipt"
                   accept="image/*" capture="camera"
                   class="form-control form-control-sm" required>

            <button name="make_sale" class="btn btn-danger btn-sm">
                Sell
            </button>
        </form>
        <?php else: ?>
        <span class="text-muted">Out of stock</span>
        <?php endif; ?>
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