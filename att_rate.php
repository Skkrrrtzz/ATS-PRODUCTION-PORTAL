<?php
$conn = mysqli_connect("localhost", "root", "") or die("Could not connect");
mysqli_select_db($conn, "ewip") or die("could not connect database");
// Retrieve the number of technicians and operators
// Calculate the attendance rate for a specific date (daily)
$date = '2023-06-08'; // Enter the desired date

$query = "SELECT 
    COUNT(*) AS total,
    SUM(CASE WHEN Department IN ('Cable Assy') AND Emp_ID NOT IN ('5555', '13640', '12379', '13394', '13351') THEN 1 ELSE 0 END) AS present_operators,
    SUM(CASE WHEN Department IN ('Production Main', 'Prod Main') AND Emp_ID NOT IN ('4444', '13472', '947') THEN 1 ELSE 0 END) AS present_technicians
FROM prod_attendance
WHERE DATE = '$date'";

$result = mysqli_query($conn, $query);
$row = mysqli_fetch_assoc($result);

$totalOperators = $row['total'];
$presentOperators = $row['present_operators'];
$totalTechnicians = $row['total'];
$presentTechnicians = $row['present_technicians'];

$daily_operator_attendance_rate = ($presentOperators / $totalOperators) * 100;
$daily_technician_attendance_rate = ($presentTechnicians / $totalTechnicians) * 100;

// Display the attendance rates
echo "Daily Operator Attendance Rate: $daily_operator_attendance_rate%";
echo "Daily Technician Attendance Rate: $daily_technician_attendance_rate%";
