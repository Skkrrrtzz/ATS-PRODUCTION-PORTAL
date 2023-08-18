<?php include 'ATS_Prod_Header.php' ?>
<?php
function displaySweetAlert($title, $text, $icon)
{
    echo "<script>
            Swal.fire({
                title: '{$title}',
                text: '{$text}',
                icon: '{$icon}',
                showConfirmButton: false,
                timer: 2000
            });
        </script>";
}
// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "ewip";
date_default_timezone_set("Asia/Manila"); // set default timezone

// Create a new PDO instance
try {
    $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
}

// Process form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve form data
    $employeeId = $_POST["emp_id"];

    // Check if the employee ID exists in the database
    $query = "SELECT * FROM user WHERE username = :emp_id";
    $stmt = $conn->prepare($query);
    $stmt->bindParam(':emp_id', $employeeId);
    $stmt->execute();

    if ($stmt->rowCount() > 0) {
        $userRow = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($userRow) {
            $empName = $userRow['emp_name'];
            $dept = $userRow['department'];

            // Check if attendance record already exists for today
            $query = "SELECT * FROM prod_attendance WHERE Emp_ID = :emp_id AND DATE = CURDATE()";
            $stmt = $conn->prepare($query);
            $stmt->bindParam(':emp_id', $employeeId);
            $stmt->execute();

            if ($stmt->rowCount() > 0) {
                displaySweetAlert('You are already Present ' . $employeeId . ' today!', '', 'info');
            } else {
                // Employee ID doesn't have an attendance record for today, proceed to insert
                $query = "INSERT INTO prod_attendance (Name, Emp_ID, Department, DATE, Time_In) VALUES (:emp_name, :emp_id, :dept, CURDATE(), NOW())";
                $stmt = $conn->prepare($query);
                $stmt->bindParam(':emp_name', $empName);
                $stmt->bindParam(':emp_id', $employeeId);
                $stmt->bindParam(':dept', $dept);

                // Execute the query
                if ($stmt->execute()) {
                    displaySweetAlert('Success! You are now Present!', 'Attendance recorded successfully!', 'success');
                } else {
                    displaySweetAlert('Error!', 'Error inserting attendance record.', 'error');
                }
            }
        } else {
            displaySweetAlert('Error!', 'Failed to fetch employee details.', 'error');
        }
    } else {
        displaySweetAlert('Error!', 'Invalid employee ID!', 'error');
    }
}

// Close the database connection
$conn = null;
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Barcode Scanner Form</title>
    <!-- <script>
        window.onload = function() {
            var inputElement = document.getElementById("emp_id");
            inputElement.focus();

            var barcodeInput = inputElement.value;
            var numericValue = barcodeInput.replace(/\D/g, '');
            inputElement.value = numericValue;

            var errorMessage = document.getElementById("error-message");

            if (numericValue.length > 0) {
                errorMessage.style.display = "none";
                document.querySelector("form").submit();
            } else {
                errorMessage.style.display = "block";
            } // Interval in milliseconds (adjust as needed)
        };
    </script> -->
    <style>
        .form-container {
            max-width: 400px;
            margin: 0 auto;
        }
    </style>

</head>

<body>
    <div class="container">
        <div class="form-container">
            <h2>Scan Barcode</h2>
            <form action="" method="POST">
                <input type="number" name="emp_id" id="emp_id" autofocus>
            </form>
            <h4 id="error-message" style="color: red; display: none;">Please enter a valid numeric barcode.</h4>
        </div>
    </div>
    <script>
        document.getElementById("emp_id").addEventListener("input", function() {
            var barcodeInput = this.value;
            var numericValue = barcodeInput.replace(/\D/g, '');
            this.value = numericValue;

            var errorMessage = document.getElementById("error-message");

            if (numericValue.length > 0) {
                errorMessage.style.display = "none";
                document.querySelector("form").submit();
            } else {
                errorMessage.style.display = "block";
            }
        });
    </script>
</body>

</html>