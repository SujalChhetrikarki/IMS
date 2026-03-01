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

        $check = mysqli_query(
            $conn,
            "SELECT id FROM products WHERE model_no = '$model_no'"
        );

        if ($check && mysqli_num_rows($check) > 0) {

            mysqli_query(
                $conn,
                "UPDATE products 
                 SET quantity = quantity + $quantity 
                 WHERE model_no = '$model_no'"
            );

            $msg = "Product already exists. Quantity updated.";

        } else {

            mysqli_query(
                $conn,
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
   FETCH PRODUCTS WITH USER NAME
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

<style>
body{background:#f4f6f9;font-family:'Segoe UI',sans-serif;}
.card{border-radius:15px;}
.low-stock{background:#dc3545;}
.ok-stock{background:#198754;}
</style>
</head>

<body>

<div class="container mt-4">

<a href="dashboard.php" class="btn btn-dark mb-3">⬅ Dashboard</a>

<h4 class="mb-3">Add Product (Manager)</h4>

<?php if (!empty($msg)) { ?>
<div class="alert alert-success"><?= $msg ?></div>
<?php } ?>

<!-- ADD PRODUCT FORM -->
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
    <td><?= date("d M Y", strtotime($row['created_at'])) ?></td>
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