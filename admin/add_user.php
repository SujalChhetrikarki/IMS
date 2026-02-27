<?php
include("../config/db.php");

if(isset($_POST['add'])){
$name = $_POST['name'];
$email = $_POST['email'];
$password = md5($_POST['password']);
$role = $_POST['role'];

$conn->query("INSERT INTO users(name,email,password,role)
VALUES('$name','$email','$password','$role')");
echo "User Added!";
}
?>

<form method="POST">
<input name="name" placeholder="Name"><br>
<input name="email" placeholder="Email"><br>
<input name="password" placeholder="Password"><br>

<select name="role">
<option value="manager">Manager</option>
<option value="sales">Sales</option>
</select>

<button name="add">Add User</button>
</form>