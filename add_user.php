<?php
include('connection.php');
$emp_name = $_POST['emp_name'];
$username = $_POST['username'];
$password = $_POST['password'];
$department = $_POST['department'];
$role = $_POST['role'];

$sql = "INSERT INTO `user` (`emp_name`,`username`,`password`,`department`,`role`) values ('$emp_name', '$username','$password', '$department', '$role' )";
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
