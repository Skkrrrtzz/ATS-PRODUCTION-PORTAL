<?php
$conn = mysqli_connect("localhost", "root", "") or die("Could not connect");
mysqli_select_db($conn, "ewip") or die("Could not connect to database");
// Query to fetch data from prod_dtr table
$sql = "SELECT ID,batch_no,product,date_shipped,MAX(Stations) AS Stations,remarks FROM prod_module GROUP BY batch_no,product ASC";
$result = mysqli_query($conn, $sql);

// Fetch data as an associative array
$data = mysqli_fetch_all($result, MYSQLI_ASSOC);

// Close the database connection
mysqli_close($conn);

// Return data as JSON response
echo json_encode($data);
