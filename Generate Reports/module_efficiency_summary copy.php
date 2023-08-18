<?php include '../ATS_Prod_Header.php'; ?>
<?php include '../PROD_navbar.php'; ?>

<?php

if (!isset($_SESSION['Emp_ID'])) {
    header('location:ATS_Prod_Home.php');
    exit();
}
$name = $_SESSION['Name'];
$dept = $_SESSION['Department'];
$emp_id = $_SESSION['Emp_ID'];

?>

<!DOCTYPE html>
<html>

<head>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Module Performance Summary</title>
    <style>
        table {
            border-collapse: collapse;
            width: 75%;
            float: center;
        }

        td {
            text-align: center;
            padding: 8px;
            font-size: 16px;
        }

        th {
            text-align: center;
            padding: 8px;
            background-color: gray;
            color: white;
        }

        tr:nth-child(even) {
            background-color: #f2f2f2;
        }
    </style>
</head>

<body>
    <center>
        <div class="fw-bold mt-2 mb-2">
            <form method="POST">
                <label for="datefrom"><b>FROM:</b></label>
                <input align="center" type="date" name="datefrom" value="">
                <label for="dateto"><b>TO:</b></label>
                <input type="date" name="dateto" value="">
                <button class="btn btn-secondary btn-sm mb-1" id="filter" name="filter">View</button>
                <button class="btn btn-secondary btn-sm mb-1" onclick='myApp.printTable()'>Print/Save</button>
                <!--<button id="btnExport" onclick="javascript:xport.toCSV('efficiencysummary');">Export to Excel</button>-->
                <button type="button" class="btn btn-success btn-sm mb-1" id="Export">Export to Excel</button>

                <div id="spinner">
                    <span id="loading-text">Loading...</span>
                    <div class="spinner-border" role="status" id="spinner" aria-hidden="true">
                    </div>
                </div>
                <?php
                function calculateDetailedEfficiency($conn, $datefrom, $dateto)
                {
                    $result = [];

                    $wosql_data = mysqli_query($conn, "SELECT Name,wo_id,Qty_Make,cycle_time,(build_percent/100) AS build_percent,Stations,description,Part_No,Duration,Prod_Order_No,batch_no,Code,Act_Start,Act_End,remarks,product FROM prod_dtr WHERE DATE BETWEEN '$datefrom' and '$dateto' AND Qty_Make > 0 AND Emp_ID !='4444' AND Emp_ID!='11451' AND Labor_Type != 'Prod_Reg_ID' ORDER BY Name asc");

                    while ($row = mysqli_fetch_array($wosql_data)) {
                        $name = $row['Name'];
                        $description = $row['description'];
                        $prod_no = $row['Prod_Order_No'];
                        $product = $row['product'];
                        $batch_no = $row['batch_no'];
                        $code = $row['Code'];
                        $start = $row['Act_Start'];
                        $end = $row['Act_End'];
                        $remarks = $row['remarks'];
                        $wo_id = $row['wo_id'];
                        $wo_qty = $row['Qty_Make'];
                        $stations = $row['Stations'];
                        $part_no = $row['Part_No'];
                        $detailed_duration = $row['Duration'];
                        $std = $row['cycle_time'];
                        $bp = $row['build_percent'];

                        if ($std != 0) {
                            $detailed_std = $std;
                        } else {
                            $detailed_std = 0;
                        }

                        $detailed_actual = number_format(($detailed_duration / 60), 2);

                        if ($bp != 0) {
                            $detailed_eff = number_format((($detailed_std * $bp * $wo_qty) * 100) / 8, 2);
                        } else {
                            $detailed_eff = 0;
                        }

                        $result[] = [
                            'name' => $name,
                            'description' => $description,
                            'prod_no' => $prod_no,
                            'product' => $product,
                            'batch_no' => $batch_no,
                            'code' => $code,
                            'start' => $start,
                            'end' => $end,
                            'remarks' => $remarks,
                            'wo_id' => $wo_id,
                            'wo_qty' => $wo_qty,
                            'stations' => $stations,
                            'part_no' => $part_no,
                            'detailed_duration' => $detailed_duration,
                            'build_percent' => $bp,
                            'detailed_std' => $detailed_std,
                            'detailed_actual' => $detailed_actual,
                            'detailed_eff' => $detailed_eff
                        ];
                    }

                    return $result;
                }
                $all_efficiency = " ";
                $all_total_present_day = 0;
                $all_attendance = 0;
                $dept = "";
                $datefrom = "";
                $dateto = "";
                $total_work_day = "";
                $total_technicians = "";
                $all_attendance_format = "";
                $all_actual_time_format = "";
                $all_standard_time_format = "";
                $total = 0;
                $WHxMP = 0;
                $detailed_eff = 0;
                $total_efficiency = 0;

                if (isset($_POST['filter'])) {
                    $datefrom = $_POST['datefrom'];
                    $dateto = $_POST['dateto'];
                    $dept = "Prod Main"; ?>
                    <div class="table-responsive">
                        <table class="table-sm" id="efficiencysummary" border="1">
                            <tr>
                                <td style='text-align:center' colspan='32' width='100%'>
                                    <h4 style="background-color: #ADD8E6; font-weight: bold">
                                        <bold>TECHNICIAN EFFICIENCY PER EMPLOYEE</bold>
                                    </h4>
                                    <?php
                                    echo "FROM: $datefrom TO: $dateto";
                                    ?>
                                </td>
                            </tr>
                            <tr>
                                <th style="background-color:gray" colspan="5">Name</th>
                                <th style="background-color:gray" colspan="3">Standard</th>
                                <th style="background-color:gray" colspan="3">Output</th>
                                <th style="background-color:gray" colspan="3">Efficiency</th>
                                <th style="background-color:gray" colspan="3">Attendance</th>
                            </tr>
                            <?php

                            $technician_sql = mysqli_query($conn, "SELECT user_ID, emp_name, username FROM user WHERE Department IN ('Prod Main') AND username NOT IN ('4444') GROUP BY emp_name ORDER BY emp_name");
                            $technicians = mysqli_num_rows($technician_sql);

                            $present_tech_sql = mysqli_query($conn, "SELECT * FROM `prod_attendance` WHERE Department IN ('Prod Main') AND DATE BETWEEN '$datefrom' and '$dateto' AND Emp_ID NOT IN ('4444') ORDER BY `DATE`");
                            $present_technicians = mysqli_num_rows($present_tech_sql);

                            $hc_sql = mysqli_query($conn, "SELECT COUNT(DISTINCT Name) AS total_headcount
                            FROM prod_dtr WHERE Department = 'Prod Main' AND Act_End != '' AND Name != 'TECHNICIAN' AND description != 'INDIRECT ACTIVITY' AND Emp_ID != '11451' AND DATE BETWEEN '$datefrom' AND '$dateto'");
                            $hc_row = mysqli_fetch_assoc($hc_sql);
                            $total_headcount = $hc_row['total_headcount'];

                            $sql = mysqli_query($conn, "SELECT ID, description, Name, Stations,Qty_Make, SUM(CASE WHEN description IN ('FRAME ASSY, JLP G3', 'PANEL ASSY, JLP G3') THEN cycle_time ELSE cycle_time END) AS cycle_time_sum,  SUM(CASE WHEN description IN ('FRAME ASSY, JLP G3', 'PANEL ASSY, JLP G3') THEN build_percent/100 ELSE build_percent/100 END) AS build_percent_sum, SUM(Duration/60) AS Duration FROM prod_dtr WHERE Department ='Prod Main' AND Act_End!='' AND Name != 'TECHNICIAN' AND Emp_ID!='11451' AND DATE BETWEEN '$dateto' and '$datefrom' GROUP BY Name ORDER BY Name");

                            // Create an associative array to store the sum of products for each name
                            $productSumByNames = array();
                            $stdValues = array();
                            $buildValues = array();
                            $result = 0;

                            while ($row = mysqli_fetch_assoc($sql)) {
                                $name = $row['Name'];
                                $qty_make = $row['Qty_Make'];
                                $build = $row['build_percent_sum'];
                                $std = $row['cycle_time_sum'];
                                $description = $row['description'];

                                // If the station is FVI or SUB TEST, set the build percent to 1
                                if ($row['Stations'] == 'FVI MODULE' || $row['Stations'] == 'SUB TEST') {
                                    $std_cycle_time = "2.00";
                                } else {
                                    $std_cycle_time = $std;
                                }
                                // $build_qty = $build * $qty_make;
                                $stdValues[$name] = $std_cycle_time;
                                $buildValues[$name][] = $build;
                                $array1[] = $std_cycle_time;
                                $array2[] = $build;

                                // Calculate the product for the current row
                                $product = $std_cycle_time * $build / 8  * 100;

                                // Check if the name already exists in the associative array
                                if (isset($productSumByNames[$name])) {
                                    // Add the product to the existing sum for that name
                                    $productSumByNames[$name] += $product;
                                } else {
                                    // Initialize the sum for the name
                                    $productSumByNames[$name] = $product;
                                }
                                $total_present_sql = mysqli_query($conn, "SELECT Emp_ID FROM prod_dtr  WHERE OT_day !='Yes' AND Department='$dept' AND Name='$name'  AND DATE BETWEEN '$datefrom' and '$dateto' GROUP BY DATE");
                                $total_present_day = mysqli_num_rows($total_present_sql);
                                if ($total_present_day != 0) {
                                    $total_present_day = "P";
                                } else {
                                    $total_present_day = "A";
                                }

                                $all_total_present_day += mysqli_num_rows($total_present_sql);

                                if ($all_total_present_day != 0) {
                                    $all_attendance = number_format(($all_total_present_day / $technicians) * 100, 2);
                                } else {
                                    $all_attendance = "0";
                                }

                                if ($total_headcount != 0) {
                                    $WHxMP = $total_headcount * 8;
                                } else {
                                    $WHxMP = 0;
                                }


                                $result = round(array_sum(array_map(function ($a, $b) {
                                    return $a * $b;
                                }, $array1, $array2)));


                                if ($WHxMP && $result != 0) {
                                    $total_efficiency = number_format(($result / $WHxMP) * 100, 2);
                                } else {
                                    $total_efficiency = 0;
                                }
                            }
                            foreach ($productSumByNames as $name => $sumProduct) {
                                $sumProduct;
                                $std = $stdValues[$name];
                                $build = $buildValues[$name];
                                $buildSum = array_sum($build);

                                echo "<tr>";
                                echo "<td colspan='5'>$name</td>";
                                echo "<td style='color:grey' colspan='3'>$std hrs.</td>";
                                echo "<td style='color:grey' colspan='3'>$buildSum</td>";
                                echo "<td colspan='3'> " . round($sumProduct) . "%</td>";
                                echo "<td colspan='3'>$total_present_day </td>";
                                echo "</tr>";
                            }
                            ?>
                            <tr style="background-color:#ADD8E6;">
                                <td></td>
                                <td colspan='2'>OVERALL:</td>
                                <td colspan='6'></td>
                                <td colspan='5'></td>
                                <td colspan='5'><?php echo $all_attendance; ?>%</td>

                            </tr>
                            <tr>
                                <td></td>
                                <td colspan='2'>STD X OUTPUT: </td>
                                <td colspan='2'><?php echo $result; ?>&nbsp;</td>
                                <td colspan='5'></td>
                                <td colspan='7'></td>
                            </tr>
                            <tr>
                                <td></td>
                                <td colspan='2'>WH X MP: </td>
                                <td colspan='2'><?php echo $WHxMP; ?>&nbsp;</td>
                                <td colspan='5'></td>
                                <td colspan='7'></td>
                            </tr>
                            <tr style="background-color:#ADD8E6">
                                <td></td>
                                <td colspan='2'>Efficiency: </td>
                                <td colspan='2'><?php echo $total_efficiency; ?>%&nbsp;</td>
                                <td colspan='5'></td>
                                <td colspan='7'></td>
                            </tr>

                    </div>
                    <div>
                        <tr>
                            <th colspan="16">DETAILED MODULE ASSY EFFICIENCY</th>
                        </tr>
                        <tr>
                            <th>TECHNICIAN</th>
                            <th>MODULE</th>
                            <th>STATION</th>
                            <th>PROD NO.</th>
                            <th>PRODUCT</th>
                            <th>PART NO.</th>
                            <th>BATCH NO.</th>
                            <th>ACTIVITY</th>
                            <th>STARTED</th>
                            <th>ENDED</th>
                            <th>REMARKS</th>
                            <th>MINS</th>
                            <th>QTY</th>
                            <th>OUTPUT</th>
                            <th>STD</th>
                            <th>EFF</th>
                        </tr>
                    <?php } ?>
                    <?php
                    $results = calculateDetailedEfficiency($conn, $datefrom, $dateto);
                    foreach ($results as $result) {
                        $row = $result;
                    ?>
                        <tr style="text-align:center">
                            <td><?php echo $result['name']; ?></td>
                            <td style='color:grey'><?php echo $result['description']; ?></td>
                            <td style='color:grey'><?php echo $result['stations']; ?></td>
                            <td style='color:grey'><?php echo $result['prod_no']; ?></td>
                            <td style='color:grey'><?php echo $result['product']; ?></td>
                            <td style='color:grey'><?php echo $result['part_no']; ?></td>
                            <td style='color:grey'><?php echo $result['batch_no']; ?></td>
                            <td style='color:grey'><?php echo $result['code']; ?></td>
                            <td style='color:grey'><?php echo $result['start']; ?></td>
                            <td style='color:grey'><?php echo $result['end']; ?></td>
                            <td style='color:grey'><?php echo $result['remarks']; ?></td>
                            <td style='color:grey'><?php echo $result['detailed_duration']; ?></td>
                            <td style='color:grey'><?php echo $result['wo_qty']; ?></td>
                            <td><?php echo number_format($result['build_percent'] * $result['wo_qty'], 2); ?></td>
                            <td><?php echo $result['detailed_std']; ?></td>
                            <td style='color:blue'><?php echo round($result['detailed_eff']); ?>%</td>
                        </tr>
                    <?php } ?>
                    </table>
            </form>
        </div>
</body>
<script>
    // Hide the spinner by default
    document.getElementById("spinner").style.display = "none";
    const viewBtn = document.getElementById("filter");
    const spinner = document.getElementById("spinner");
    const table = document.getElementById("efficiencysummary");

    viewBtn.addEventListener("click", async () => {
        spinner.style.display = "block";
        table.style.display = "none";

        const datefrom = document.getElementById("datefrom").value;
        const dateto = document.getElementById("dateto").value;
        const url = `cable_efficiency_summary.php?datefrom=${datefrom}&dateto=${dateto}`;

        try {
            const response = await fetch(url);
            const data = await response.text();
            table.innerHTML += data;
        } catch (error) {
            console.error(error);
            alert("Failed to fetch data. Please try again.");
        } finally {
            spinner.style.display = "none";
            table.style.display = "table";
        }
    });
</script>
<script>
    document.getElementById('Export').addEventListener('click', function() {
        var table2excel = new Table2Excel();
        table2excel.export(document.querySelectorAll("#efficiencysummary"));
    });
</script>
<script>
    var myApp = new function() {
        this.printTable = function() {
            var tab = document.getElementById('efficiencysummary');
            var win = window.open('', '', 'height=700,width=700');
            win.document.write(tab.outerHTML);
            win.document.close();
            win.print();
        }
    }
</script>

</html>