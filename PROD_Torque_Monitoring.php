<?php include 'ATS_Prod_Header.php'; ?>
<?php include 'PROD_navbar.php'; ?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Torque Monitoring</title>
    <link rel="stylesheet" href="assets/DataTables/datatables.min.css">
    <script src="assets/DataTables/datatables.min.js"></script>
    <script src="assets/js/Chart.js"></script>
    <style>
        #Torque {
            height: 300px;
            width: 100%;
        }

        .dataTables_filter input[type="search"] {
            margin: 5px;
        }

        .dataTables_paginate {
            margin: 10px;
        }
    </style>
</head>

<body>
    <div class="container-fluid">
        <div class="row">
            <div class="col-sm-3">
                <div class="card m-2" style="width: 100%; margin: auto;">
                    <div class="card-header bg-primary-subtle">
                        <h5 class="fw-bold text-center"> Torque Results</h5>
                    </div>
                    <div class="card-body">
                        <canvas class="" id="Torque"></canvas>
                    </div>
                    <div class="card-body">
                        <canvas id="PassFailDoughnut"></canvas>
                    </div>
                </div>
            </div>
            <div class="col-9 mt-1">
                <div class="card">
                    <table class="table table-sm table-striped table-bordered display compact" id="torque-summary">
                        <thead class="table-primary" style="font-size: 14px;">
                            <tr>
                                <th>NO.</th>
                                <th>Date</th>
                                <th>Product</th>
                                <th>Serial NO.</th>
                                <th>Screw Size</th>
                                <th>Trial 1</th>
                                <th>Trial 2</th>
                                <th>Trial 3</th>
                                <th>Checked by</th>
                                <th>ES Borrow</th>
                                <th>ES Return</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $sql_torque_specs = "SELECT torque.Torque_No, torque.Date,torque.Product, torque.Serial_no, torque.Checked_by, torque.Screw_size, torque.Trial_1, torque.Trial_2, torque.Trial_3, torque.ES_Borrow, torque.ES_Returnn, torque_specs.MIN, torque_specs.MAX, CASE WHEN torque.Trial_1 >= torque_specs.MIN AND torque.Trial_1 <= torque_specs.MAX THEN 'Pass' ELSE 'Fail' END AS result_Trial_1, CASE WHEN torque.Trial_2 >= torque_specs.MIN AND torque.Trial_2 <= torque_specs.MAX THEN 'Pass' ELSE 'Fail' END AS result_Trial_2, CASE WHEN torque.Trial_3 >= torque_specs.MIN AND torque.Trial_3 <= torque_specs.MAX THEN 'Pass' ELSE 'Fail' END AS result_Trial_3, SUM(CASE WHEN torque.Trial_3 >= torque_specs.MIN AND torque.Trial_3 <= torque_specs.MAX THEN 1 ELSE 0 END) AS pass_count_Trial_3,SUM(CASE WHEN torque.Trial_3 < torque_specs.MIN OR torque.Trial_3 > torque_specs.MAX THEN 1 ELSE 0 END) AS fail_count_Trial_3 FROM torque JOIN torque_specs ON torque.Serial_no = torque_specs.Serial_Number GROUP BY torque.Torque_No, torque.Date, torque.Serial_no, torque.Checked_by, torque.Screw_size, torque.Trial_1, torque.Trial_2, torque.Trial_3, torque.ES_Borrow, torque.ES_Returnn, torque_specs.MIN, torque_specs.MAX ORDER BY torque.Torque_No";

                            $sql_result = mysqli_query($conn, $sql_torque_specs);

                            if (mysqli_num_rows($sql_result) > 0) {
                                while ($rows = mysqli_fetch_assoc($sql_result)) {
                                    echo "<tr>";
                                    echo "<td>" . $rows['Torque_No'] . "</td>";
                                    echo "<td>" . $rows['Date'] . "</td>";
                                    echo "<td>" . $rows['Product'] . "</td>";
                                    echo "<td>" . $rows['Serial_no'] . "</td>";
                                    echo "<td>" . $rows['Screw_size'] . "</td>";
                                    echo "<td><span class='badge " . ($rows['result_Trial_1'] === 'Pass' ? 'bg-success' : 'bg-danger') . "'>" . $rows['Trial_1'] . "</span></td>";
                                    echo "<td><span class='badge " . ($rows['result_Trial_2'] === 'Pass' ? 'bg-success' : 'bg-danger') . "'>" . $rows['Trial_2'] . "</span></td>";
                                    echo "<td><span class='badge " . ($rows['result_Trial_3'] === 'Pass' ? 'bg-success' : 'bg-danger') . "'>" . $rows['Trial_3'] . "</span></td>";
                                    echo "<td>" . $rows['Checked_by'] . "</td>";
                                    echo "<td>" . $rows['ES_Borrow'] . "</td>";
                                    echo "<td>" . $rows['ES_Returnn'] . "</td>";

                                    if ($rows['ES_Returnn'] == 0) {
                                        echo "<td> <span class='badge text-bg-danger'>Not returned</span> </td>";
                                    } else {
                                        echo "<td> <span class='badge text-bg-success'>Returned</span> </td>";
                                    }

                                    echo "</tr>";

                                    // Store pass and fail counts in the array for each row
                                    $data[] = array(
                                        'date' => $rows['Date'],
                                        'pass' => $rows['pass_count_Trial_3'],
                                        'fail' => $rows['fail_count_Trial_3'],
                                    );
                                }
                            } else {
                                echo "<tr><td colspan='12'>No data found.</td></tr>";
                            }
                            // Initialize an associative array to store pass and fail counts for each date
                            $date_counts = array();
                            $pass_fail_counts = array(
                                'pass' => 0,
                                'fail' => 0
                            );

                            foreach ($data as $counts) {
                                $pass_fail_counts['pass'] += $counts['pass'];
                                $pass_fail_counts['fail'] += $counts['fail'];
                            }

                            $pf_count = json_encode($pass_fail_counts);
                            // Loop through the array to count pass and fail for each date
                            foreach ($data as $entry) {
                                $date = $entry['date'];

                                // If the date is not yet in the $date_counts array, initialize the counts
                                if (!isset($date_counts[$date])) {
                                    $date_counts[$date] = array('pass' => 0, 'fail' => 0);
                                }

                                // Accumulate the pass and fail counts for each date
                                $date_counts[$date]['pass'] += $entry['pass'];
                                $date_counts[$date]['fail'] += $entry['fail'];
                            }

                            // // Output the results for each date (commented out for now)
                            // foreach ($date_counts as $date => $counts) {
                            //     echo "Date: " . $date . PHP_EOL;
                            //     echo "Pass Count: " . $counts['pass'] . PHP_EOL;
                            //     echo "Fail Count: " . $counts['fail'] . PHP_EOL;
                            //     echo PHP_EOL; // Add a newline for readability
                            // }

                            // Convert $date_counts array to JSON
                            $data_json = json_encode($date_counts);
                            // Close the MySQL connection
                            mysqli_close($conn);
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <script>
        $(document).ready(function() {
            var tables = $('#torque-summary').DataTable({
                dom: 'frtip',
                order: [
                    [0, 'desc']
                ],
                columnDefs: [{
                    targets: [1, 2],
                    className: 'fw-bold'
                }]
            });
        });
        // The PHP-generated JSON data from $data_counts variable
        var data = <?php echo $data_json; ?>;

        var ctx = document.getElementById('Torque').getContext('2d');
        var Torque = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: Object.keys(data), // Array of unique dates
                datasets: [{
                        label: 'Pass',
                        backgroundColor: 'rgba(20, 164, 77)',
                        borderColor: 'rgba(20, 164, 77, 1)',
                        borderWidth: 1,
                        data: Object.values(data).map(entry => entry.pass),
                    },
                    {
                        label: 'Fail',
                        backgroundColor: 'rgba(255,74,113,255)',
                        borderColor: 'rgba(255, 99, 132, 1)',
                        borderWidth: 1,
                        data: Object.values(data).map(entry => entry.fail),
                    }
                ]
            },
            options: {
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });
        var pfData = <?php echo $pf_count; ?>;
        var ctx = document.getElementById('PassFailDoughnut').getContext('2d');
        var PassFailDoughnut = new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels: ['Pass', 'Fail'],
                datasets: [{
                    data: [pfData.pass, pfData.fail],
                    backgroundColor: ['rgba(20, 164, 77)', 'rgba(255, 74, 113, 255)'],
                    borderColor: ['rgba(20, 164, 77)', 'rgba(255, 99, 132, 1)'],
                    borderWidth: 1
                }]
            }
        });
    </script>
</body>

</html>