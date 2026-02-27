<?php
session_start();
if($_SESSION['role']!="admin"){
    header("Location: ../login.php");
    exit();
}

include("../config/db.php"); // database connection

// Manual Add Product
if(isset($_POST['add_product'])){

    $product_no = $_POST['product_no'];
    $product_name = $_POST['product_name'];
    $price = $_POST['price'];
    $quantity = $_POST['quantity'];

    // Check if product number exists
    $check = mysqli_query($conn, "SELECT * FROM products WHERE product_no='$product_no'");

    if(mysqli_num_rows($check) > 0){
        // Update quantity
        mysqli_query($conn, "UPDATE products 
                             SET quantity = quantity + $quantity 
                             WHERE product_no='$product_no'");
        $msg = "Product exists! Quantity updated successfully.";
    } else {
        // Insert new
        mysqli_query($conn, "INSERT INTO products (product_no, product_name, price, quantity) 
                             VALUES ('$product_no','$product_name','$price','$quantity')");
        $msg = "New product added successfully!";
    }
}

// CSV Import
if(isset($_POST['import_csv'])){

    if($_FILES['csv_file']['name']){

        $filename = $_FILES['csv_file']['tmp_name'];
        $file = fopen($filename, "r");

        fgetcsv($file); // Skip header

        while(($data = fgetcsv($file, 1000, ",")) !== FALSE){

            $product_no = $data[0];
            $product_name = $data[1];
            $price = $data[2];
            $quantity = $data[3];

            $check = mysqli_query($conn, "SELECT * FROM products WHERE product_no='$product_no'");

            if(mysqli_num_rows($check) > 0){
                // Update quantity only
                mysqli_query($conn, "UPDATE products 
                                     SET quantity = quantity + $quantity 
                                     WHERE product_no='$product_no'");
            } else {
                // Insert new product
                mysqli_query($conn, "INSERT INTO products (product_no, product_name, price, quantity) 
                                     VALUES ('$product_no','$product_name','$price','$quantity')");
            }
        }

        fclose($file);
        $msg = "CSV Imported Successfully!";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
<title>Add Product</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

<div class="container mt-4">

<a href="dashboard.php" class="btn btn-dark mb-3">🏠 Home</a>

<h3>Add Product</h3>

<?php if(isset($msg)){ ?>
<div class="alert alert-success"><?php echo $msg; ?></div>
<?php } ?>

<form method="POST">
<div class="row">
<div class="col-md-6">
<input type="text" name="product_no" class="form-control mb-2" placeholder="Product No" required>
</div>

<div class="col-md-6">
<input type="text" name="product_name" class="form-control mb-2" placeholder="Product Name" required>
</div>

<div class="col-md-4">
<input type="number" name="price" class="form-control mb-2" placeholder="Price" required>
</div>

<div class="col-md-4">
<input type="number" name="quantity" class="form-control mb-2" placeholder="Quantity" required>
</div>

<div class="col-md-4">
<button type="submit" name="add_product" class="btn btn-success w-100">Add Product</button>
</div>
</div>
</form>

<hr>

<h4>Import Products via CSV</h4>

<form method="POST" enctype="multipart/form-data">
<div class="row">
<div class="col-md-8">
<input type="file" name="csv_file" class="form-control" accept=".csv" required>
</div>
<div class="col-md-4">
<button type="submit" name="import_csv" class="btn btn-primary w-100">Import CSV</button>
</div>
</div>
</form>

</div>

</body>
</html>