<?php
include("../config/db.php");
session_start();

if(isset($_POST['add'])){
$name = $_POST['name'];
$cat = $_POST['category'];
$model = $_POST['model'];
$price = $_POST['price'];
$qty = $_POST['qty'];
$user = $_SESSION['user_id'];

$conn->query("INSERT INTO products(product_name,category,model_no,price,quantity,added_by)
VALUES('$name','$cat','$model','$price','$qty','$user')");

echo "Product Added!";
}
?>

<form method="POST">
<input name="name" placeholder="Product Name"><br>
<input name="category" placeholder="Category"><br>
<input name="model" placeholder="Model"><br>
<input name="price" placeholder="Price"><br>
<input name="qty" placeholder="Quantity"><br>
<button name="add">Add Product</button>
</form>