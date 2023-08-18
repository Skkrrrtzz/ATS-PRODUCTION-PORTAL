<?php
include('connection.php');
$emp_name = $_POST['emp_name'];
$username = $_POST['username'];
$password = $_POST['password'];
$department = $_POST['department'];
$role = $_POST['role'];
$id = $_POST['id'];

$sql = "UPDATE `user` SET `username`= '$username', `emp_name`='$emp_name' ,`password`='$password',`department`='$department', `role`='$role' WHERE user_ID='$id' ";
$query = mysqli_query($con, $sql);
$lastId = mysqli_insert_id($con);
if ($query == true) {

    $data = array(
        'status' => 'true',

    );

    echo json_encode($data);
} else {
    $data = array(
        'status' => 'false',

    );

    echo json_encode($data);
}
