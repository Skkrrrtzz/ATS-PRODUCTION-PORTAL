<?php include '../ATS_Prod_Header.php'; ?>
<?php include '../PROD_navbar.php'; ?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>WIP MODULE</title>

</head>

<body>
    <div class="table-responsive text-center">
        <h1>WORK IN PROGRESS</h1>
        <table class="table table-bordered table-hover display compact" id="eclipse2">
            <thead>
                <tr class="table-primary text-center">
                    <th>TECHNICIAN</th>
                    <th>DESCRIPTION</th>
                    <th>PRODUCT</th>
                    <th>PROD NO</th>
                    <th>BATCH</th>
                    <th>QTY</th>
                    <th>ACTIVITY</th>
                    <th>REMARKS</th>
                    <th>STARTED</th>
                    <th>ONGOING(minutes)</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $categories = [
                    'DIRECT LABOR' => [
                        'status' => 'IN-PROCESS',
                        'color' => 'text-success'
                    ],
                    'INDIRECT LABOR' => [
                        'status' => 'INDIRECT',
                        'color' => 'text-warning'
                    ]
                ];

                foreach ($categories as $category => $data) {
                    $status = $data['status'];
                    $color = $data['color'];

                    $query = "SELECT Name, Stations, batch_no, description, Prod_Order_No, product, Act_Start, Qty_Make, Activity, remarks, NOW() as timer
                          FROM prod_dtr
                          WHERE Duration = '' AND Act_Start != '' AND wo_status = '$status'
                          ORDER BY Stations";

                    $result = mysqli_query($conn, $query);

                    echo "<tr class='bg-success-subtle'>
                        <th colspan='10' class='text-center $color bg-secondary-subtle'>$category</th>
                      </tr>";

                    while ($row = mysqli_fetch_array($result)) {
                        $now = $row['timer'];
                        $Start = strtotime($row['Act_Start']);
                        $End = strtotime($now);
                        $Duration = ($End - $Start) / 60;
                        $total = number_format($Duration);

                        echo "<tr class='fw-bold text-center'>";
                        echo "<td>" . $row['Name'] . "</td>";
                        echo "<td>" . $row['description'] . "</td>";
                        echo "<td>" . $row['product'] . "</td>";
                        echo "<td>" . $row['Prod_Order_No'] . "</td>";
                        echo "<td>" . $row['batch_no'] . "</td>";
                        echo "<td>" . $row['Qty_Make'] . "</td>";
                        echo "<td>" . $row['Activity'] . "</td>";
                        echo "<td>" . $row['remarks'] . "</td>";
                        echo "<td>" . $row['Act_Start'] . "</td>";
                        echo "<td>" . $total . "</td>";
                        echo "</tr>";
                    }
                }
                ?>
            </tbody>
        </table>
    </div>
    <script>
        // Function to refresh the table using AJAX
        function refreshTable() {
            $.ajax({
                url: "",
                dataType: "html",
                success: function(data) {
                    // Replace the existing table with the updated one
                    var updatedTable = $(data).find("#eclipse");
                    $("#eclipse").html(updatedTable);
                }
            });
        }
        // Refresh the table every 5 seconds (adjust the interval as needed)
        setInterval(refreshTable, 5000);

        document.getElementById('Export').addEventListener('click', function() {
            var table2excel = new Table2Excel();
            table2excel.export(document.querySelectorAll("#eclipse"));
        });
    </script>
</body>

</html>