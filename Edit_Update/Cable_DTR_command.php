<?php
$conn = mysqli_connect("localhost", "root", "") or die("Could not connect");
mysqli_select_db($conn, "ewip") or die("Could not connect to database");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['update'])) {
        // // Handle update operation
        // $date = $_POST['date'];
        // $part_no = $_POST['part_no'];
        // $stations = $_POST['stations'];
        // $prod_no = $_POST['prod_no'];
        // $name = $_POST['name'];
        // $emp_id = $_POST['emp_id'];
        // $qty = $_POST['qty_make'];
        // $act_code = $_POST['act_code'];
        $act_start = $_POST['act_start'];
        $act_end = $_POST['act_end'];
        // $wo_status = $_POST['wo_status'];
        // $labor_type = $_POST['labortype'];
        $duration = $_POST['duration'];
        $remarks = $_POST['remarks'];
        $id = $_POST['id'];
        // $activity = $_POST['activity'];

        $sql = "UPDATE `dtr` SET `Duration`= ?,`Act_Start`= ?,`Act_End`= ?, `remarks`= ? WHERE ID= ? ";
        $stmt = mysqli_prepare($conn, $sql);

        if ($stmt) {
            mysqli_stmt_bind_param($stmt, 'ssssi', $duration, $act_start, $act_end, $remarks, $id);
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

        $sql = "DELETE FROM dtr WHERE ID = ?";
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

        $sql = "INSERT INTO dtr (product, part_no, station, Activity, cycle_time, updated_by) VALUES (?, ?, ?, ?, ?, ?)";
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
        $sql = "DELETE FROM dtr WHERE ID = ?";
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
    // Assuming you want to select records for the year 2023
    $year = 2023;

    $sql = "SELECT ID, wo_id, Name, Emp_ID, DATE, Part_No, Prod_Order_No, Qty_Make, Labor_Type, Act_Start, Act_End, Stations, Code, Duration, wo_status, Activity, remarks FROM dtr WHERE Part_No != '' AND Prod_Order_No != '' AND YEAR(DATE) = $year";
    $result = mysqli_query($conn, $sql);

    // Check if the query was successful
    if ($result) {
        // Fetch data as an associative array
        $data = mysqli_fetch_all($result, MYSQLI_ASSOC);

        // Close the database connection
        mysqli_close($conn);

        // Return data as JSON response
        echo json_encode($data);
    } else {
        // If there was an error in the query, you can handle it here
        echo "Error: " . mysqli_error($conn);
    }
}
