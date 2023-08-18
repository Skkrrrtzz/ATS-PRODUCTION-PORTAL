<?php
$conn = mysqli_connect("localhost", "root", "") or die("Could not connect");
mysqli_select_db($conn, "ewip") or die("Could not connect to database");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['update'])) {
        // Handle update operation
        $id = $_POST['id'];
        $name = $_POST['name'];
        $username = $_POST['username'];
        $password = $_POST['pass'];
        $department = $_POST['dept'];
        $role = $_POST['role'];

        // Hash the new password
        $hashedPassword = password_hash($password, PASSWORD_BCRYPT);

        $sql = "UPDATE `user` SET `emp_name`= ?, `username`= ?,`password` =?, `hashed_password`= ?, `department`= ?, `role`= ? WHERE user_ID= ?";
        $stmt = mysqli_prepare($conn, $sql);

        if ($stmt) {
            mysqli_stmt_bind_param($stmt, 'ssssssi', $name, $username,  $password, $hashedPassword, $department, $role, $id);
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

        $sql = "DELETE FROM user WHERE user_ID = ?";
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
        $addName = $_POST['user'];
        $addEmp_ID = $_POST['emp_id'];
        $addPass = $_POST['pass'];
        $addDept = $_POST['dept'];
        $addRole = $_POST['role'];

        // Check if username already exists
        $checkSql = "SELECT COUNT(*) FROM user WHERE username = ?";
        $checkStmt = mysqli_prepare($conn, $checkSql);
        mysqli_stmt_bind_param($checkStmt, 's', $addEmp_ID);
        mysqli_stmt_execute($checkStmt);
        mysqli_stmt_bind_result($checkStmt, $count);
        mysqli_stmt_fetch($checkStmt);
        mysqli_stmt_close($checkStmt);

        if ($count > 0) {
            // Username already exists, display an error message or perform necessary actions
            echo 'Username is already exists!';
        } else {
            // Username does not exist, proceed with the insert operation

            // Hash the password
            $hashedPassword = password_hash($addPass, PASSWORD_DEFAULT);

            $sql = "INSERT INTO user (emp_name, username, password, hashed_password, department, role) VALUES (?, ?, ?, ?, ?, ?)";
            $stmt = mysqli_prepare($conn, $sql);

            if ($stmt) {
                mysqli_stmt_bind_param($stmt, 'ssssss', $addName, $addEmp_ID, $addPass, $hashedPassword, $addDept, $addRole);
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
    } elseif (isset($_POST['multipleDel'])) {
        $selected = $_POST['ids'];

        // Prepare the DELETE statement
        $sql = "DELETE FROM user WHERE user_ID = ?";
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
    $sql = "SELECT * FROM user";
    $result = mysqli_query($conn, $sql);

    // Fetch data as an associative array
    $data = mysqli_fetch_all($result, MYSQLI_ASSOC);

    // Close the database connection
    mysqli_close($conn);

    // Return data as JSON response
    echo json_encode($data);
}
