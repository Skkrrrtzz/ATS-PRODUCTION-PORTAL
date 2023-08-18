<?php include '../ATS_Prod_Header.php'; ?>
<?php include '../PROD_navbar.php';

if (!isset($_SESSION['Emp_ID'])) {
    header('location:ATS_Prod_Home.php');
    exit();
}
$name = $_SESSION['Name'];
$dept = $_SESSION['Department'];
$emp_id = $_SESSION['Emp_ID'];

$all_total_present_day = 0;
$Dept = "";
$datefrom = "";
$dateto = "";
$total_eff = 0;
$total_work_day = "";
$all_attendance_format = "";
?>

<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cable Performance Summary</title>
    <style>
        /* table {
            border-collapse: collapse;
            width: 75%;
            float: center;
        } */

        /* td {
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
        } */
    </style>
</head>

<body>
    <center>
        <div class="fw-bold mt-2 mb-2">
            <form method="POST">
                <label for="datefrom"><b>FROM:</b></label>
                <input align="center" type="date" name="datefrom" id="datefrom" value="">
                <label for="dateto"><b>TO:</b></label>
                <input type="date" name="dateto" id="dateto" value="">
                <button class="btn btn-secondary btn-sm mb-1" id="filter" name="filter">View</button>
                <button class="btn btn-secondary btn-sm mb-1" onclick='myApp.printTable()'>Print/Save</button>
                <button class="btn btn-success btn-sm mb-1" id="btnExport" onclick="javascript:xport.toCSV('efficiencysummary');">Export to Excel</button>
                <!--<button id="Export">Export to Excel</button><br>-->
                <!-- <div id="spinner">
                    <span id="loading-text">Loading...</span>
                    <div class="spinner-border" role="status" id="spinner" aria-hidden="true">
                    </div>
                </div> -->
                <div id="spinner-container">
                    <span id="loading-text">Loading...</span>
                    <div class="spinner-border" role="status" id="spinner" aria-hidden="true"></div>
                </div>
                <?php
                if (isset($_POST['filter'])) {
                    $datefrom = $_POST['datefrom'];
                    $dateto = $_POST['dateto'];
                    $Dept = "Cable Assy"; ?>
                    <div class="table-responsive" id="cable_eff">
                        <table class="table table-sm table-striped text-center w-75" id="efficiencysummary" border="2">
                            <thead>
                                <tr>
                                    <td style='text-align:center' colspan='32' width='100%'>
                                        <h4 style="background-color: #ADD8E6; font-weight: bold">
                                            <bold>CABLE ASSY EFFICIENCY PER EMPLOYEE</bold>
                                        </h4>
                                        <?php
                                        echo "FROM: $datefrom TO: $dateto";

                                        $total_wd_sql = mysqli_query($conn, "SELECT ID FROM dtr WHERE OT_day = 'No' AND Department = '$Dept' AND DATE BETWEEN '$datefrom' and '$dateto' GROUP BY DATE");
                                        $total_work_day = mysqli_num_rows($total_wd_sql);
                                        ?>
                                    </td>
                                </tr>
                                <tr class="text-bg-secondary">
                                    <th colspan="3">Name</th>
                                    <th colspan="3">Standard</th>
                                    <th colspan="2">Actual</th>
                                    <th colspan="3">Efficiency</th>
                                    <th colspan="3">Attendance</th>
                                </tr>
                            </thead>

                            <tbody>
                                <?php

                                // Run the query to get the list of names
                                $name_result = mysqli_query($conn, "SELECT user_ID,emp_name FROM user WHERE department = 'Cable Assy' AND role = 'operator' AND username NOT IN ('13394','13351','5555') group by emp_name order by emp_name");
                                $total_headcount = mysqli_num_rows($name_result);
                                // Initialize a variable to hold the total actual time for all names
                                $total_actual_time_all_names = 0;
                                $total_std_time_all_names = 0;
                                $total_qty_all_names = 0;

                                // Initialize an array to hold the total actual time and detailed actual time for each name
                                $name_totals = array();

                                // Loop through the list of names and lookup the corresponding total actual time
                                while ($name_row = mysqli_fetch_assoc($name_result)) {
                                    $name = $name_row['emp_name'];

                                    // CHECKS THE PRESENTS
                                    $total_present_sql = mysqli_query($conn, "SELECT ID FROM dtr WHERE OT_day='No' AND Department='$Dept' AND Name='$name'  AND DATE BETWEEN '$datefrom' and '$dateto' GROUP BY DATE");
                                    $total_present_day = mysqli_num_rows($total_present_sql);

                                    // ATTENDANCE IDENTIFICATION
                                    if ($total_present_day != 0) {
                                        $total_present_day = "P";
                                    } else {
                                        $total_present_day = "A";
                                    }

                                    $all_total_present_day += mysqli_num_rows($total_present_sql);

                                    // ATTENDANCE EFFICIENCY
                                    if ($total_present_day || $total_work_day != 0) {
                                        $all_attendance = ($all_total_present_day / $total_headcount) * 100;
                                        $all_attendance_format = round($all_attendance);
                                    } else {
                                        $all_attendance_format = "0";
                                    }
                                    // GET THE DATA FOR STANDARD TIME 
                                    $sql_result = mysqli_query($conn, "SELECT dtr.Name,dtr.DATE,dtr.Duration AS total_actual_time,dtr.Qty_Make,dtr.Duration/dtr.Qty_Make AS detailed_total_act,dtr.wo_id,dtr.Part_No,dtr.Prod_Order_No,cable_cycletime.cycle_time,(CASE WHEN dtr.Duration/dtr.Qty_Make < cable_cycletime.cycle_time THEN dtr.Duration/dtr.Qty_Make ELSE cable_cycletime.cycle_time END) AS Std_time FROM (SELECT Name,DATE,SUM(Duration) AS Duration,Qty_Make,wo_id,Part_No,Stations,Code,Station_No,Labor_Type,Act_Start,Prod_Order_No FROM dtr WHERE DATE BETWEEN '$datefrom' and '$dateto' AND Name = '$name' AND Emp_ID NOT IN ('13394','13351') AND Qty_Make > 0 AND wo_status = 'IN-PROCESS' AND Stations!='FG TRANSACTION' AND Labor_Type='Reg_DL' AND Department = 'Cable Assy' GROUP BY Name, Prod_Order_No,Stations) AS dtr LEFT JOIN cable_cycletime ON dtr.Part_No = cable_cycletime.Part_No AND cable_cycletime.station=dtr.Stations GROUP BY dtr.Name, dtr.Prod_Order_No,dtr.Stations ORDER BY dtr.Name, dtr.Act_Start ASC");



                                    /* // Run the query and get the result set
                                $result = mysqli_query($conn, "SELECT Name,DATE,total_actual_time,Qty_Make,total_actual_time/Qty_Make AS detailed_total_act,wo_id,Part_No,Prod_Order_No FROM(SELECT Name, DATE, SUM(actual_time) AS total_actual_time,Qty_Make, wo_id, Part_No,Prod_Order_No FROM (SELECT Name, DATE, SUM(Duration) AS actual_time,Qty_Make, wo_id, Part_No, remarks, Code, Station_No, Labor_Type, Act_Start, Prod_Order_No, Duration FROM dtr WHERE DATE BETWEEN '2023-03-06' AND '2023-03-06' AND Name= '$name' AND Qty_Make > 0 AND wo_status ='IN-PROCESS' AND Department='Cable Assy' GROUP BY Name, Prod_Order_No) AS t GROUP BY Name, Prod_Order_No ORDER BY Name, Act_Start ASC) AS subquery"); */

                                    // Initialize a variable to hold the running total and detailed actual time for each Name
                                    $total_actual_time_by_name = 0;
                                    $detailed_actual_time = '';
                                    $total_std_time_by_name = 0;
                                    $total_actual_time = 0;
                                    $QTY_ = 0;


                                    // Loop through the result set and calculate the total actual time and detailed actual time for each Name
                                    while ($row = mysqli_fetch_assoc($sql_result)) {
                                        $name = $row['Name'];
                                        $std_time = $row['Std_time'];
                                        $qty_make = $row['Qty_Make'];
                                        $detailed_actual_time = $row['detailed_total_act'];
                                        $actual_time = $row['total_actual_time'] / 60;
                                        $total_actual_time += $actual_time;
                                        $total_actual_time_by_name += $actual_time;
                                        if ($std_time != 0) {
                                            $total_time = $std_time * $qty_make / 60;
                                        } else {
                                            $total_time = number_format($detailed_actual_time * $qty_make / 60, 2);
                                        }
                                        $total_std_time_by_name += $total_time;
                                        $QTY_ += $qty_make;
                                        //$QTY_ += number_format($std_time * $qty_make / 60, 2);
                                    }
                                    //echo number_format($total_std_time_by_name, 2);
                                    // Total actual_time and detailed_actual_time for each names
                                    $name_totals[$name] = array(
                                        'total_actual_time' => number_format($total_actual_time_by_name, 2),
                                        'total_std_time' => $total_std_time_by_name,
                                        'detailed_actual_time' => rtrim($detailed_actual_time, '; '),
                                        'total_present' => ($total_present_day),
                                        'QTY_MAKE' => $QTY_
                                    );

                                    // Add the actual_time value to the running total for all names
                                    $total_actual_time_all_names += $total_actual_time_by_name;
                                    $total_std_time_all_names += $total_std_time_by_name;
                                }
                                $total_percentage = 0;
                                // Print out the total actual time and detailed actual time for each Name
                                foreach ($name_totals as $name => $totals) {
                                    $total_std = number_format($totals['total_std_time'], 2);
                                    echo "<tr>";
                                    echo "<td colspan='3'>$name</td>";
                                    echo "<td style='color:grey' colspan='3'> $total_std hrs.</td>";
                                    //echo $totals['QTY_MAKE'];
                                    echo "<td style='color:grey' colspan='2'> {$totals['total_actual_time']} hrs.</td>";
                                    if ($totals['total_actual_time'] &&  $total_std != 0) {
                                        if ($totals['total_actual_time'] <  $total_std) {
                                            $percentage = 100;
                                        } else {
                                            $percentage = round(($total_std / $totals['total_actual_time']) * 100, 2);
                                        }
                                    } elseif ($totals['total_actual_time'] > 0) {
                                        $total_std = $totals['total_actual_time'];
                                        $percentage = 100;
                                    } else {
                                        $percentage = 0;
                                    }

                                    echo "<td colspan='3'>" . $percentage . "%</td>";
                                    echo "<td colspan='3'>{$totals['total_present']} </td>";
                                    $Efficiency = round($total_percentage += $percentage, 2);
                                }
                                if ($total_std_time_all_names && $total_actual_time_all_names != 0) {
                                    $total_eff = round(($total_std_time_all_names / $total_actual_time_all_names) * 100, 2);
                                } else {
                                    $total_eff = 0;
                                }

                                ?>
                            </tbody>
                            <tr style="background-color:#ADD8E6">
                                <td colspan='3'>OVERALL: </td>
                                <td colspan='3'> <?php echo number_format($total_std_time_all_names, 2); ?>&nbsp;hrs.</td>
                                <td colspan='2'> <?php echo number_format($total_actual_time_all_names, 2); ?>&nbsp;hrs.</td>
                                <td colspan='3'> <?php echo $total_eff; ?>%</td>
                                <td colspan='3'> <?php echo $all_attendance_format; ?>%</td>
                            </tr>

                        </table>
                    </div>

                    <div class="table-responsive" id="detailed">
                        <table class="table table-sm table-striped w-75 text-center table-responsive-sm" border="2">
                            <thead class="text-bg-secondary">
                                <tr>
                                    <th colspan="14">DETAILED CABLE ASSY EFFICIENCY</th>
                                </tr>
                                <tr class="text-bg-secondary">
                                    <th>OPERATOR</th>
                                    <th>STATION</th>
                                    <th>PROD NO.</th>
                                    <th>PRODUCT</th>
                                    <th>PART NO.</th>
                                    <th>ACTIVITY</th>
                                    <th>STARTED</th>
                                    <th>ENDED</th>
                                    <th>MINS(ACT)</th>
                                    <th>MINS(STD)</th>
                                    <th>QTY</th>
                                    <th>ACTUAL</th>
                                    <th>STD</th>
                                    <!-- <th>EFF</th> -->
                                </tr>
                            </thead>
                        <?php }
                    $wosql_data = mysqli_query($conn, "SELECT Name,wo_id,Qty_Make,Stations,Part_No,Activity,Duration,remarks,SUM(Duration) AS TOTALD,Name,Prod_Order_No,Station_No,Code,Act_Start,Act_End FROM dtr WHERE DATE BETWEEN '$datefrom' and '$dateto' AND Qty_Make > 0 AND wo_status = 'IN-PROCESS' AND Stations!='FG TRANSACTION' AND Labor_Type='Reg_DL' AND Emp_ID NOT IN ('13394','13351') AND Department='$Dept' AND Duration!='' GROUP BY Name,Prod_Order_No,Stations ORDER BY Name,Act_Start asc");

                    while ($row = mysqli_fetch_array($wosql_data)) {
                        $wo_id    = $row['wo_id'];
                        $wo_qty   = $row['Qty_Make'];
                        $stations = $row['Stations'];
                        $part_no = $row['Part_No'];
                        $detailed_duration = $row['TOTALD'];
                        $detailed_actual = $detailed_duration / $wo_qty;


                        $module_sql = mysqli_query($conn, "SELECT module FROM wo WHERE wo_id ='$wo_id'");
                        //  while($_module_row = mysqli_fetch_array( $module_sql)){
                        $_module_row = mysqli_fetch_array($module_sql);
                        $_module_code = $_module_row['module'];
                        if ($_module_code == '18204CH') {
                            $_module = 'PNP';
                        } elseif ($_module_code == '18203CH') {
                            $_module = 'JLP';
                        } elseif ($_module_code == '18207CH') {
                            $_module = 'OLB';
                        } elseif ($_module_code == '0720TN') {
                            $_module = 'TERADYNE';
                        } elseif ($_module_code == '1820CH') {
                            $_module = 'SPARES';
                        } else {
                            $_module = 'JTP';
                        }

                        $standard_time_sql = mysqli_query($conn, "SELECT cycle_time FROM cable_cycletime WHERE station = '$stations' AND product = '$_module' AND part_no='$part_no'");
                        $std_time = mysqli_num_rows($standard_time_sql);
                        $std_row = mysqli_fetch_array($standard_time_sql);
                        $detailed_std = $std_row['cycle_time'];

                        if ($detailed_actual != 0) {
                            if ($detailed_actual < $detailed_std) {
                                $detailed_std = $detailed_actual;
                                $detailed_eff = "100.00";
                            } elseif ($detailed_std == 0) {
                                $detailed_std = $detailed_actual;
                                $detailed_eff = "100.00";
                            } else {
                                $detailed_eff = round(($detailed_std / $detailed_actual) * 100, 2);
                            }
                        } else {
                            $detailed_eff = 0;
                        } ?>
                            <tr style="text-align:center">

                                <td><?php echo $row['Name']; ?></td>
                                <td style='color:grey'><?php echo $row['Stations']; ?>&nbsp;&nbsp;<?php echo $row['Station_No']; ?></td>
                                <td style='color:grey'><?php echo $row['Prod_Order_No']; ?></td>
                                <td style='color:grey'><?php echo $_module; ?></td>
                                <td style='color:grey'><?php echo $row['Part_No']; ?></td>
                                <td style='color:grey'><?php echo $row['Code'] . "-" . $row['Activity']; ?></td>
                                <td style='color:grey'><?php echo $row['Act_Start']; ?></td>
                                <td style='color:grey'><?php echo $row['Act_End']; ?></td>
                                <td style='color:grey'><?php echo number_format($detailed_duration, 2); ?></td>
                                <td><?php echo number_format($detailed_std * $row['Qty_Make'], 2); ?></td>
                                <td style='color:grey'><?php echo $row['Qty_Make']; ?></td>
                                <td><?php echo number_format($detailed_actual, 2); ?></td>
                                <td><?php echo number_format($detailed_std, 2); ?></td>
                                <!-- <td style='color:blue'><?php echo $detailed_eff;  ?>%</td> -->
                            <?php } ?>
                            </tr>
                        </table>
                    </div>
            </form>
        </div>
</body>

<!-- <script>
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
</script> -->
<!--<script> // EXPORT TO XLSX FILE
  document.getElementById('Export').addEventListener('click',function() {
  var table2excel = new Table2Excel();
  table2excel.export(document.querySelectorAll("#efficiencysummary"));
});
</script>-->
<script>
    var xport = {
        _fallbacktoCSV: true,
        toXLS: function(tableId, filename) {
            this._filename = (typeof filename == 'undefined') ? tableId : filename;

            //var ieVersion = this._getMsieVersion();
            //Fallback to CSV for IE & Edge
            if ((this._getMsieVersion() || this._isFirefox()) && this._fallbacktoCSV) {
                return this.toCSV(tableId);
            } else if (this._getMsieVersion() || this._isFirefox()) {
                alert("Not supported browser");
            }

            //Other Browser can download xls
            var htmltable = document.getElementById(tableId);
            var html = htmltable.outerHTML;

            this._downloadAnchor("data:application/vnd.ms-excel" + encodeURIComponent(html), 'xls');
        },
        toCSV: function(tableId, filename) {
            this._filename = (typeof filename === 'undefined') ? tableId : filename;
            // Generate our CSV string from out HTML Table
            var csv = this._tableToCSV(document.getElementById(tableId));
            // Create a CSV Blob
            var blob = new Blob([csv], {
                type: "text/csv"
            });

            // Determine which approach to take for the download
            if (navigator.msSaveOrOpenBlob) {
                // Works for Internet Explorer and Microsoft Edge
                navigator.msSaveOrOpenBlob(blob, this._filename + ".csv");
            } else {
                this._downloadAnchor(URL.createObjectURL(blob), 'csv');
            }
        },
        _getMsieVersion: function() {
            var ua = window.navigator.userAgent;

            var msie = ua.indexOf("MSIE ");
            if (msie > 0) {
                // IE 10 or older => return version number
                return parseInt(ua.substring(msie + 5, ua.indexOf(".", msie)), 10);
            }

            var trident = ua.indexOf("Trident/");
            if (trident > 0) {
                // IE 11 => return version number
                var rv = ua.indexOf("rv:");
                return parseInt(ua.substring(rv + 3, ua.indexOf(".", rv)), 10);
            }

            var edge = ua.indexOf("Edge/");
            if (edge > 0) {
                // Edge (IE 12+) => return version number
                return parseInt(ua.substring(edge + 5, ua.indexOf(".", edge)), 10);
            }

            // other browser
            return false;
        },
        _isFirefox: function() {
            if (navigator.userAgent.indexOf("Firefox") > 0) {
                return 1;
            }

            return 0;
        },
        _downloadAnchor: function(content, ext) {
            var anchor = document.createElement("a");
            anchor.style = "display:none !important";
            anchor.id = "downloadanchor";
            document.body.appendChild(anchor);

            // If the [download] attribute is supported, try to use it

            if ("download" in anchor) {
                anchor.download = this._filename + "." + ext;
            }
            anchor.href = content;
            anchor.click();
            anchor.remove();
        },
        _tableToCSV: function(table) {
            // We'll be co-opting `slice` to create arrays
            var slice = Array.prototype.slice;

            return slice
                .call(table.rows)
                .map(function(row) {
                    return slice
                        .call(row.cells)
                        .map(function(cell) {
                            return '"t"'.replace("t", cell.textContent);
                        })
                        .join(",");
                })
                .join("\r\n");
        }
    };
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

    var intervalId;
    var dataURLPromise = null; // Define a promise to track dataURL

    function saveCharts(divId) {
        return new Promise(function(resolve, reject) {
            var div = document.getElementById(divId);
            html2canvas(div).then(function(canvas) {
                var url = canvas.toDataURL("image/png");
                resolve(url); // Resolve the promise with dataURL
            });
        });
    }
    document.addEventListener("DOMContentLoaded", function() {
        // Hide the spinner by default
        document.getElementById("spinner-container").style.display = "none";

        const viewBtn = document.getElementById("filter");
        const spinnerContainer = document.getElementById("spinner-container");
        const table1 = document.getElementById("efficiencysummary");
        const table2 = document.getElementById("detailed");

        function showloading() {
            spinnerContainer.style.display = "block";
            table1.style.display = "none";
            table2.style.display = "none";
        }

        // Function to handle button click
        function handleButtonClick() {
            showloading();
        }

        // Add an event listener to the button's 'click' event
        viewBtn.addEventListener("click", handleButtonClick);
    });

    // function checkAndSave() {
    //     var totalEffValue = <?php echo $total_eff; ?>;
    //     var selectedDate = new Date("<?php echo date('Y-m-d', strtotime($datefrom)); ?>");
    //     var formattedDate = selectedDate.getFullYear() + '-' + ('0' + (selectedDate.getMonth() + 1)).slice(-2) + '-' + ('0' + selectedDate.getDate()).slice(-2);
    //     var targetValue = 98;
    //     var currentTime = new Date();
    //     var currentHour = currentTime.getHours();

    //     if (currentHour >= 9 && currentHour < 10 && totalEffValue >= targetValue) {
    //         dataURLPromise.then(function(dataURL) {
    //             // Send AJAX request to insert data and send email
    //             $.ajax({
    //                 type: "POST",
    //                 url: "../Efficiency_Checking.php",
    //                 data: {
    //                     efficiency: totalEffValue,
    //                     datefrom: selectedDate,
    //                     dataURL: dataURL
    //                 },
    //                 success: function(response) {
    //                     try {
    //                         var data = JSON.parse(response);
    //                         if (data.status === 'success') {
    //                             Swal.fire({
    //                                 icon: 'success',
    //                                 title: 'Success',
    //                                 text: data.message,
    //                                 toast: true,
    //                                 position: 'top-end',
    //                                 showConfirmButton: false,
    //                                 timer: 3000
    //                             });
    //                         } else if (data.status === 'error') {
    //                             Swal.fire({
    //                                 icon: 'error',
    //                                 title: 'Error',
    //                                 text: data.message,
    //                                 toast: true,
    //                                 position: 'top-end',
    //                                 showConfirmButton: false,
    //                                 timer: 3000
    //                             });
    //                         }
    //                     } catch (error) {
    //                         console.error('Error parsing JSON response:', error);
    //                     }
    //                 },
    //                 error: function(xhr, status, error) {
    //                     console.error(error);
    //                 }
    //             });
    //         });
    //         clearInterval(intervalId);
    //     }
    // }

    function checkAndSave() {
        var totalEffValue = <?php echo $total_eff; ?>;
        var selectedDate = new Date("<?php echo date('Y-m-d', strtotime($datefrom)); ?>");
        var formattedDate = selectedDate.getFullYear() + '-' + ('0' + (selectedDate.getMonth() + 1)).slice(-2) + '-' + ('0' + selectedDate.getDate()).slice(-2);
        var targetValue = 98;
        var currentTime = new Date();
        var currentHour = currentTime.getHours();

        if (currentHour >= 16 && currentHour < 17 && totalEffValue >= targetValue) {
            Swal.fire({
                icon: 'info',
                title: 'Efficiency Target is met: ' + targetValue + '%',
                text: 'Do you want to send the file to email?',
                showCancelButton: true,
                cancelButtonText: 'Cancel',
                confirmButtonText: 'Send Email'
            }).then((result) => {
                if (result.isConfirmed) {
                    // User chose to execute the PHP file
                    dataURLPromise.then(function(dataURL) {
                        // Send AJAX request to insert data
                        $.ajax({
                            type: "POST",
                            url: "../Efficiency_Checking.php",
                            data: {
                                efficiency: totalEffValue,
                                datefrom: formattedDate,
                                dataURL: dataURL
                            },
                            success: function(response) {
                                try {
                                    var data = JSON.parse(response);
                                    if (data.status === 'success') {
                                        Swal.fire({
                                            icon: 'success',
                                            title: 'Success',
                                            text: data.message,
                                            toast: true,
                                            position: 'top-end',
                                            showConfirmButton: false,
                                            timer: 3000
                                        });
                                    } else if (data.status === 'error') {
                                        Swal.fire({
                                            icon: 'error',
                                            title: 'Error',
                                            text: data.message,
                                            toast: true,
                                            position: 'top-end',
                                            showConfirmButton: true
                                        });
                                    }
                                } catch (error) {
                                    console.error('Error parsing JSON response:', error);
                                }
                            },
                            error: function(xhr, status, error) {
                                console.error(error);
                            }
                        });
                    });
                }
            });
            clearInterval(intervalId);
        }
    }
    // Start the interval and store the interval ID
    intervalId = setInterval(checkAndSave, 3000); // 3 seconds

    // Call saveCharts and store the promise in dataURLPromise
    dataURLPromise = saveCharts('cable_eff');
</script>

</html>