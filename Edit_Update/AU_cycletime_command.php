<?php
$conn = mysqli_connect("localhost", "root", "") or die("Could not connect");
mysqli_select_db($conn, "ewip") or die("Could not connect to database");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['update'])) {
        // Handle update operation
        $id = $_POST['id'];
        $updatedProduct = $_POST['product'];
        $updatedPartNo = $_POST['partNo'];
        $updatedStation = $_POST['station'];
        $updatedActivity = $_POST['activity'];
        $updatedStd = $_POST['std'];
        $name = $_POST['name'];
        $date = date('Y-m-d');

        $sql = "UPDATE cable_cycletime SET product = ?, part_no = ?, station = ?, Activity = ?, cycle_time = ?, updated_by = ?, date_updated = ? WHERE ID = ?";
        $stmt = mysqli_prepare($conn, $sql);

        if ($stmt) {
            mysqli_stmt_bind_param($stmt, 'sssssssi', $updatedProduct, $updatedPartNo, $updatedStation, $updatedActivity, $updatedStd, $name, $date, $id);
            $result = mysqli_stmt_execute($stmt);

            if ($result) {
                echo 'success';
            } else {
                echo 'error: ' . mysqli_stmt_error($stmt);
            }
        } else {
            echo 'error: ' . mysqli_error($conn);
        }
    } elseif (isset($_POST['delete'])) {
        // Handle delete operation
        $id = $_POST['id'];

        $sql = "DELETE FROM cable_cycletime WHERE ID = ?";
        $stmt = mysqli_prepare($conn, $sql);

        if ($stmt) {
            mysqli_stmt_bind_param($stmt, 'i', $id);
            $result = mysqli_stmt_execute($stmt);

            if ($result) {
                echo 'success';
            } else {
                echo 'error: ' . mysqli_stmt_error($stmt);
            }
        } else {
            echo 'error: ' . mysqli_error($conn);
        }
    } elseif (isset($_POST['insert'])) {
        // Handle insert operation
        $product = $_POST['product'];
        $partNo = $_POST['partNo'];
        $station = $_POST['station'];
        $activity = $_POST['activity'];
        $std = $_POST['std'];
        $name = $_POST['name'];

        $sql = "INSERT INTO cable_cycletime (product, part_no, station, Activity, cycle_time, updated_by) VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = mysqli_prepare($conn, $sql);

        if ($stmt) {
            mysqli_stmt_bind_param($stmt, 'ssssss', $product, $partNo, $station, $activity, $std, $name);
            $result = mysqli_stmt_execute($stmt);

            if ($result) {
                echo 'success';
            } else {
                echo 'error: ' . mysqli_stmt_error($stmt);
            }
        } else {
            echo 'error: ' . mysqli_error($conn);
        }
    }
} else {
    // Query to fetch data from cable_cycletime table
    $sql = "SELECT * FROM cable_cycletime";
    $result = mysqli_query($conn, $sql);

    // Fetch data as an associative array
    $data = mysqli_fetch_all($result, MYSQLI_ASSOC);

    // Close the database connection
    mysqli_close($conn);

    // Return data as JSON response
    echo json_encode($data);
}
