<?php
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit;
}

include("../config/db.php");

$msg = "";

/* =========================
   MANUAL ADD PRODUCT
========================= */
if (isset($_POST['add_product'])) {

    $product_name = trim($_POST['product_name'] ?? '');
    $category     = trim($_POST['category'] ?? '');
    $model_no     = trim($_POST['model_no'] ?? '');
    $location     = trim($_POST['location'] ?? '');
    $price        = (float)($_POST['price'] ?? 0);
    $quantity     = (int)($_POST['quantity'] ?? 0);
    $added_by     = $_SESSION['user_id'];

    if ($product_name === '' || $category === '' || $model_no === '' || $location === '') {
        $msg = "All fields are required.";
    } else {

        $check = mysqli_query(
            $conn,
            "SELECT id FROM products WHERE model_no='$model_no'"
        );

        if ($check && mysqli_num_rows($check) > 0) {

            mysqli_query(
                $conn,
                "UPDATE products 
                 SET quantity = quantity + $quantity,
                     location = '$location'
                 WHERE model_no='$model_no'"
            );

            $msg = "Product exists. Stock updated.";

        } else {

            mysqli_query(
                $conn,
                "INSERT INTO products
                (product_name, category, model_no, price, quantity, location, added_by)
                VALUES
                ('$product_name','$category','$model_no','$price','$quantity','$location','$added_by')"
            );

            $msg = "New product added successfully.";
        }
    }
}

/* =========================
   CSV IMPORT
========================= */
if (isset($_POST['import_csv']) && !empty($_FILES['csv_file']['name'])) {

    $file = fopen($_FILES['csv_file']['tmp_name'], "r");
    fgetcsv($file); // skip header

    while (($data = fgetcsv($file, 1000, ",")) !== false) {

        $product_name = trim($data[0] ?? '');
        $category     = trim($data[1] ?? '');
        $model_no     = trim($data[2] ?? '');
        $price        = (float)($data[3] ?? 0);
        $quantity     = (int)($data[4] ?? 0);
        $location     = trim($data[5] ?? '');
        $added_by     = $_SESSION['user_id'];

        if ($model_no === '') continue;

        $check = mysqli_query(
            $conn,
            "SELECT id FROM products WHERE model_no='$model_no'"
        );

        if ($check && mysqli_num_rows($check) > 0) {

            mysqli_query(
                $conn,
                "UPDATE products 
                 SET quantity = quantity + $quantity,
                     location = '$location'
                 WHERE model_no='$model_no'"
            );

        } else {

            mysqli_query(
                $conn,
                "INSERT INTO products
                (product_name, category, model_no, price, quantity, location, added_by)
                VALUES
                ('$product_name','$category','$model_no','$price','$quantity','$location','$added_by')"
            );
        }
    }

    fclose($file);
    $msg = "CSV imported successfully.";
}
?>
<!DOCTYPE html>
<html>
<head>
<title>Add Product | Admin</title>
<meta name="viewport" content="width=device-width, initial-scale=1">

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body style="background:#f4f6f9;">

<div class="container mt-4">

<a href="dashboard.php" class="btn btn-dark mb-3">⬅ Dashboard</a>

<h3 class="mb-3">Add Product</h3>

<?php if (!empty($msg)) { ?>
    <div class="alert alert-success"><?= htmlspecialchars($msg) ?></div>
<?php } ?>

<!-- MANUAL ADD -->
<div class="card p-3 mb-4">
<h5>Manual Add</h5>

<form method="POST">
<div class="row">

<div class="col-md-6">
    <input type="text" name="product_name" class="form-control mb-2" placeholder="Parts Name" required>
</div>

<div class="col-md-6">
    <input type="text" name="category" class="form-control mb-2" placeholder="Category" required>
</div>

<div class="col-md-6">
    <input type="text" name="model_no" class="form-control mb-2" placeholder="Parts Number" required>
</div>

<div class="col-md-6">
    <input type="text" name="location" class="form-control mb-2" placeholder="Location (Rack / Store)" required>
</div>

<div class="col-md-3">
    <input type="number" name="price" class="form-control mb-2" placeholder="Price" required>
</div>

<div class="col-md-3">
    <input type="number" name="quantity" class="form-control mb-2" placeholder="Quantity" required>
</div>

<div class="col-md-6">
    <button type="submit" name="add_product" class="btn btn-success w-100">
        Add Product
    </button>
</div>

</div>
</form>
</div>

<!-- CSV IMPORT -->
<div class="card p-3">
<h5>Import Products via CSV</h5>

<form method="POST" enctype="multipart/form-data">
<div class="row">

<div class="col-md-8">
    <input type="file" name="csv_file" class="form-control" accept=".csv" required>
</div>

<div class="col-md-4">
    <button type="submit" name="import_csv" class="btn btn-primary w-100">
        Import CSV
    </button>
</div>

</div>
</form>

<p class="text-muted mt-2 mb-0">
CSV Format:<br>
<code>product_name,category,model_no,price,quantity,location</code>
</p>
</div>

</div>

</body>
</html>