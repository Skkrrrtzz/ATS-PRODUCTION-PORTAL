<?php
$conn = mysqli_connect("localhost", "root", "") or die("Could not connect");
mysqli_select_db($conn, "ewip") or die("Could not connect to database");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['update'])) {
        // Handle update operation
        $date = $_POST['date'];
        $part_no = $_POST['part_no'];
        $description = $_POST['description'];
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
        $output = $_POST['output'];
        $duration = $_POST['duration'];
        $cycletime = $_POST['std'];
        $remarks = $_POST['remarks'];
        $id = $_POST['id'];
        $activity = $_POST['activity'];

        $sql = "UPDATE `prod_dtr` SET `DATE`= ?, `Part_No`= ? , `Stations`= ?,`description`= ?, `Prod_Order_No`= ?, `batch_no`= ?,`Name`= ?,`Emp_ID`= ?,`Code`= ?,`Act_Start`= ?,`Act_End`= ?,`wo_status`= ?,`build_percent`= ?,`output`= ?,`Duration`= ?,`cycle_time`= ?,`remarks`= ?, Activity= ? WHERE ID= ? ";
        $stmt = mysqli_prepare($conn, $sql);

        if ($stmt) {
            mysqli_stmt_bind_param($stmt, 'ssssssssssssssssssi', $date, $part_no, $stations, $description, $prod_no, $batch_no, $name, $emp_id, $act_code, $act_start, $act_end, $wo_status, $build_percent, $output, $duration, $cycletime, $remarks, $activity, $id);
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

        $sql = "DELETE FROM prod_dtr WHERE ID = ?";
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
        $date = $_POST['date'];
        $part_no = $_POST['part_no'];
        $description = $_POST['description'];
        $stations = $_POST['stations'];
        $prod_no = $_POST['prod_no'];
        $batch_no = $_POST['batch_no'];
        $name = $_POST['name'];
        $emp_id = $_POST['emp_id'];
        $act_code = $_POST['act_code'];
        $act_start = $_POST['act_start'];
        $act_end = $_POST['act_end'];
        $duration = $_POST['duration'];
        $wo_status = $_POST['wo_status'];
        $build_percent = $_POST['build_percent'];
        $cycletime = $_POST['std'];
        $remarks = $_POST['remarks'];
        $activity = $_POST['activity'];

        $sql = "INSERT INTO prod_dtr (DATE, Part_No, description, Stations, Prod_Order_No, batch_no, Name, Emp_ID, Code, Act_Start, Act_End,Duration, wo_status, build_percent, cycle_time, remarks, Activity) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = mysqli_prepare($conn, $sql);

        if ($stmt) {
            mysqli_stmt_bind_param($stmt, 'sssssssssssssssss', $date, $part_no, $description, $stations, $prod_no, $batch_no, $name, $emp_id, $act_code, $act_start, $act_end, $duration, $wo_status, $build_percent, $cycletime, $remarks, $activity);
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
        $sql = "DELETE FROM prod_dtr WHERE ID = ?";
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
    } elseif (isset($_POST['fetchUsers'])) {
        // Fetch users data from the user table
        $users_query = "SELECT emp_name, username, department FROM user WHERE department = 'Prod Main' AND role = 'technician'";
        $result = mysqli_query($conn, $users_query);

        if ($result) {
            // Fetch data as an associative array
            $users_data = mysqli_fetch_all($result, MYSQLI_ASSOC);

            // Close the database connection
            mysqli_close($conn);

            // Return data as JSON response
            echo json_encode($users_data);
        } else {
            // Close the database connection (in case of error)
            mysqli_close($conn);

            echo json_encode(array('error' => 'Database query failed.'));
        }
    } elseif (isset($_POST['dataType']) && $_POST['dataType'] === 'description') {
        // Fetch data from the database based on your select query
        $query = "SELECT part_no, description FROM bom WHERE prod_type = 'JLP' AND level = '1' ORDER BY work_station DESC";
        $result = mysqli_query($conn, $query);

        if ($result) {
            // Fetch data as an associative array
            $data = mysqli_fetch_all($result, MYSQLI_ASSOC);

            // Close the database connection
            mysqli_close($conn);

            // Return data as JSON response
            echo json_encode($data);
        } else {
            // Close the database connection (in case of error)
            mysqli_close($conn);

            echo json_encode(array('error' => 'Database query failed.'));
        }
    }
} else {
    // Query to fetch data from prod_dtr table
    $sql = "SELECT * FROM prod_dtr";
    $result = mysqli_query($conn, $sql);

    // Fetch data as an associative array
    $data = mysqli_fetch_all($result, MYSQLI_ASSOC);

    // Close the database connection
    mysqli_close($conn);

    // Return data as JSON response
    echo json_encode($data);
}
