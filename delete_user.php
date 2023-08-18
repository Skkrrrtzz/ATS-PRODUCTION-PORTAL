<?php
include('connection.php');

$user_id = $_POST['id'];
$sql = "DELETE FROM user WHERE user_ID='$user_id'";
$delQuery = mysqli_query($con, $sql);
if ($delQuery == true) {
    $data = array(
        'status' => 'success',

    );

    echo json_encode($data);
} else {
    $data = array(
        'status' => 'failed',

    );

    echo json_encode($data);
}
