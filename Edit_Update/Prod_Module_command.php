<?php
$conn = mysqli_connect("localhost", "root", "") or die("Could not connect");
mysqli_select_db($conn, "ewip") or die("Could not connect to database");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['update'])) {
        // Handle update operation
        $date = $_POST['date'];
        $part_no = $_POST['part_no'];
        $description = $_POST['description'];
        $product = $_POST['product'];
        $stations = $_POST['stations'];
        $prod_no = $_POST['prod_no'];
        $batch_no = $_POST['batch_no'];
        $name = $_POST['name'];
        $emp_id = $_POST['emp_id'];
        $act_code = $_POST['act_code'];
        $act_start = $_POST['act_start'];
        $act_end = $_POST['act_end'];
        $wo_status = $_POST['wo_status'];
        $build_percent = $_POST['build_percent'];
        $remarks = $_POST['remarks'];
        $id = $_POST['id'];


        $sql = "UPDATE `prod_module` SET `date_updated`= ?, `Part_No`= ? , `Stations`= ?,`description`= ?,`product`= ?, `Prod_Order_No`= ?, `batch_no`= ?,`Name`= ?,`Emp_ID`= ?,`Act_Code`= ?,`act_started`= ?,`act_ended`= ?,`wo_status`= ?,`build_percent`= ?,`remarks`= ? WHERE ID= ? ";
        $stmt = mysqli_prepare($conn, $sql);

        if ($stmt) {
            mysqli_stmt_bind_param($stmt, 'sssssssssssssssi', $date, $part_no, $stations, $description, $product, $prod_no, $batch_no, $name, $emp_id, $act_code, $act_start, $act_end, $wo_status, $build_percent, $remarks, $id);
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

        $sql = "DELETE FROM prod_module WHERE ID = ?";
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

        $sql = "INSERT INTO prod_module (product, part_no, station, Activity, cycle_time, updated_by) VALUES (?, ?, ?, ?, ?, ?)";
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
    } elseif (isset($_POST['multipleDel'])) {
        $selected = $_POST['ids'];

        // Prepare the DELETE statement
        $sql = "DELETE FROM prod_module WHERE ID = ?";
        $stmt = mysqli_prepare($conn, $sql);

        if ($stmt) {
            // Bind the parameter inside the loop and execute the statement for each selected ID
            foreach ($selected as $id) {
                mysqli_stmt_bind_param($stmt, "i", $id);
                mysqli_stmt_execute($stmt);
            }

            // Count the number of affected rows
            $rowsDeleted = mysqli_stmt_affected_rows($stmt);

            echo $rowsDeleted . " rows deleted successfully";

            // Close the statement
            mysqli_stmt_close($stmt);
        } else {
            // Error handling if the statement preparation fails
            echo "Failed to Delete";
        }
    }
} else {
    // Query to fetch data from prod_dtr table
    $sql = "SELECT * FROM prod_module";
    $result = mysqli_query($conn, $sql);

    // Fetch data as an associative array
    $data = mysqli_fetch_all($result, MYSQLI_ASSOC);

    // Close the database connection
    mysqli_close($conn);

    // Return data as JSON response
    echo json_encode($data);
}
