<?php
$conn = mysqli_connect("localhost", "root", "") or die("Could not connect");
mysqli_select_db($conn, "ewip") or die("Could not connect to database");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['update'])) {
        // Handle update operation
        $id = $_POST['Wo_ID'];
        $updatedWoDate = $_POST['Wo_Date'];
        $updatedPartNo = $_POST['PartNo'];
        $updatedProdNo = $_POST['ProdNo'];
        $updatedDesc = $_POST['Description'];
        $updatedModule = $_POST['Module'];
        $updatedQty = $_POST['Wo_quantity'];
        $updatedStation = $_POST['Station'];
        $updatedCode = $_POST['Code'];
        $updatedStatus = $_POST['Status'];
        $updatedTCD = $_POST['TCD'];
        $updatedACD = $_POST['ACD'];
        $updatedFG = $_POST['FG'];
        $updatedDept = $_POST['Dept'];
        $updatedPlanner = $_POST['Planner'];
        $updatedUpdatedBy = $_POST['Updated_By'];
        $updatedDateUpdated = $_POST['Date_Updated'];
        $updatedRemarks = $_POST['Remarks'];

        $sql = "UPDATE wo SET wo_date = ?, part_no = ?, prod_no = ?, description = ?, module = ?, wo_quantity = ?, for_station = ?, Act_Code = ?, status = ?, TCD = ?, ACD = ?, FG = ?, dept = ?, planner = ?, updated_by = ?, date_updated = ?, remarks = ? WHERE wo_id = ?";
        $stmt = mysqli_prepare($conn, $sql);

        if ($stmt) {
            mysqli_stmt_bind_param($stmt, 'sssssssssssssssssi', $updatedWoDate, $updatedPartNo, $updatedProdNo, $updatedDesc, $updatedModule, $updatedQty, $updatedStation, $updatedCode, $updatedStatus, $updatedTCD, $updatedACD, $updatedFG, $updatedDept, $updatedPlanner, $updatedUpdatedBy, $updatedDateUpdated, $updatedRemarks, $id);
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

        $sql = "DELETE FROM wo WHERE wo_id = ?";
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

        $sql = "INSERT INTO wo (product, part_no, station, Activity, cycle_time, updated_by) VALUES (?, ?, ?, ?, ?, ?)";
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
    // Query to fetch data from prod_dtr table using a prepared statement
    $sql = "SELECT * FROM wo";
    $stmt = mysqli_prepare($conn, $sql);

    // Execute the prepared statement
    mysqli_stmt_execute($stmt);

    // Get the result set
    $result = mysqli_stmt_get_result($stmt);

    // Fetch data as an associative array
    $data = mysqli_fetch_all($result, MYSQLI_ASSOC);

    // Close the statement
    mysqli_stmt_close($stmt);

    // Close the database connection
    mysqli_close($conn);

    // Return data as JSON response
    echo json_encode($data);
}
