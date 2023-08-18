<?php include '../ATS_Prod_Header.php'; ?>
<?php include '../PROD_navbar.php';
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PRODUCTION EFFICIENCY</title>
    <script src="../assets/js/exceljs.min.js"></script>
    <style>
        #info-icon {
            color: #000;
            transition: color 0.2s;
        }

        #info-icon:hover {
            color: #fff;
        }
    </style>
</head>

<body>
    <center>
        <div class="table-responsive text-center mt-2 mb-2">
            <form action="" method="POST">
                <label for="datefrom"><b>FROM:</b></label>
                <input align="center" type="date" name="datefrom" value="">
                <label for="dateto"><b>TO:</b></label>
                <input type="date" name="dateto" value="">
                <div class="m-1">
                    <button class="btn btn-secondary btn-sm mb-1" id="filter" name="filter">View</button>
                    <button class="btn btn-secondary btn-sm mb-1" onclick='myApp.printTable()'>Print/Save</button>
                    <button class="btn btn-success btn-sm mb-1" onclick="exportToExcel()">Export to Excel</button>
                </div>

                <div id="spinner">
                    <span id="loading-text">Loading...</span>
                    <div class="spinner-border" role="status" id="spinner" aria-hidden="true">
                    </div>
                </div>
                <?php
                function OVRPRODUCTEFF_MAIN($conn, $datefrom, $dateto)
                {
                    $result = [];
                    // QUERY FOR HEADCOUNT
                    $jlp_hc_sql = mysqli_query($conn, "SELECT COUNT(DISTINCT Name) AS total_headcount FROM prod_dtr WHERE Department = 'Prod Main' AND Act_End != '' AND Name != 'TECHNICIAN' AND product='JLP' AND description != 'INDIRECT ACTIVITY' AND Emp_ID != '11451' AND DATE BETWEEN '$datefrom' AND '$dateto'");
                    $jlp_hc_row = mysqli_fetch_assoc($jlp_hc_sql);
                    echo $total_hc_jlp = $jlp_hc_row['total_headcount'];

                    // QUERY FOR EMPLOYEES
                    $jlp_sql_data = mysqli_query($conn, "SELECT Name,Qty_Make,cycle_time,SUM(build_percent/100 *Qty_Make) AS build_percent,Stations,description,Part_No,Duration,Prod_Order_No,batch_no,Code,Act_Start,Act_End,remarks,product FROM prod_dtr WHERE DATE BETWEEN '$datefrom' AND '$dateto' AND Qty_Make > 0 AND product = 'JLP' AND Emp_ID !='4444' AND Emp_ID!='11451' AND Labor_Type != 'Prod_Reg_ID' GROUP BY Name,description ORDER BY Name ASC");

                    $array1 = []; // Array to store cycle time values
                    $array2 = []; // Array to store build percent values
                    $result_jlp = [];

                    while ($jlp_row = mysqli_fetch_array($jlp_sql_data)) {

                        $jlp_std = $jlp_row['cycle_time'];
                        $jlp = $jlp_row['product'];
                        if ($jlp_row['Stations'] == 'FVI MODULE') {
                            $std_cycle_time = "3.00";
                        } elseif ($jlp_row['Stations'] == 'SUB TEST') {
                            $std_cycle_time = "2.00";
                        } else {
                            $std_cycle_time = $jlp_std;
                        }
                        $jlp_bp = number_format($jlp_row['build_percent'], 2);

                        $array1[] = $std_cycle_time;
                        $array2[] = $jlp_bp;

                        $JLP_STDxOUTPUT = array_sum(array_map(function ($a, $b) {
                            return $a * $b;
                        }, $array1, $array2));
                        $JLP_WHxMP = 8 * $total_hc_jlp;
                        $JLP_TOTALEFF = $JLP_WHxMP != 0 ? number_format(($JLP_STDxOUTPUT / $JLP_WHxMP) * 100, 2) : 0;

                        $result_jlp[] = [
                            'JLP_WHxMP' => $JLP_WHxMP,
                            'JLP_STDxOUTPUT' => $JLP_STDxOUTPUT,
                            'JLP_OVERALLEFF' => $JLP_TOTALEFF,
                            'JLP_TOTALHC' => $total_hc_jlp,
                            'product_jlp' => $jlp
                        ];
                    }
                    $result[] = $result_jlp;
                    // QUERY FOR HEADCOUNT
                    $pnp_hc_sql = mysqli_query($conn, "SELECT COUNT(DISTINCT Name) AS total_headcount FROM prod_dtr WHERE Department = 'Prod Main' AND Act_End != '' AND Name != 'TECHNICIAN' AND product='PNP' AND description != 'INDIRECT ACTIVITY' AND Emp_ID != '11451' AND DATE BETWEEN '$datefrom' AND '$dateto'");
                    $pnp_hc_row = mysqli_fetch_assoc($pnp_hc_sql);
                    $total_hc_pnp = $pnp_hc_row['total_headcount'];

                    // QUERY FOR EMPLOYEES
                    $pnp_sql_data = mysqli_query($conn, "SELECT Name,Qty_Make,cycle_time,SUM(build_percent/100 *Qty_Make) AS build_percent,Stations,description,Part_No,Duration,Prod_Order_No,batch_no,Code,Act_Start,Act_End,remarks,product FROM prod_dtr WHERE DATE BETWEEN '$datefrom' AND '$dateto' AND Qty_Make > 0 AND product = 'PNP' AND Emp_ID !='4444' AND Emp_ID!='11451' AND Labor_Type != 'Prod_Reg_ID' GROUP BY Name,description ORDER BY Name ASC");

                    $array1 = []; // Array to store cycle time values
                    $array2 = []; // Array to store build percent values
                    $result_pnp = [];

                    while ($pnp_row = mysqli_fetch_array($pnp_sql_data)) {

                        $pnp_std = $pnp_row['cycle_time'];
                        $pnp_bp = $pnp_row['build_percent'];
                        $pnp = $pnp_row['product'];

                        $array1[] = $pnp_std;
                        $array2[] = $pnp_bp;

                        $PNP_STDxOUTPUT = array_sum(array_map(function ($a, $b) {
                            return $a * $b;
                        }, $array1, $array2));

                        $PNP_STDxOUTPUT;
                        $PNP_WHxMP = 8 * $total_hc_pnp;
                        $PNP_TOTALEFF = $PNP_WHxMP != 0 ? number_format(($PNP_STDxOUTPUT / $PNP_WHxMP) * 100, 2) : 0;

                        $result_pnp[] = [
                            'PNP_WHxMP' => $PNP_WHxMP,
                            'PNP_STDxOUTPUT' => $PNP_STDxOUTPUT,
                            'PNP_OVERALLEFF' => $PNP_TOTALEFF,
                            'PNP_TOTALHC' => $total_hc_pnp,
                            'product_pnp' => $pnp
                        ];
                    }
                    $result[] = $result_pnp;
                    return $result;
                }
                function OVRPRODUCTEFF_CABLE($conn, $datefrom, $dateto)
                {
                    $result = [];
                    // QUERY FOR HEADCOUNT      
                    $name_result = mysqli_query($conn, "SELECT user_ID,emp_name FROM user WHERE department = 'Cable Assy' AND role = 'operator' AND username NOT IN ('12379','13394','13351','5555') group by emp_name order by emp_name");
                    $total_cable = mysqli_num_rows($name_result);

                    $cable_hc_sql = mysqli_query($conn, "SELECT COUNT(DISTINCT Name) AS total_headcount FROM dtr WHERE Department = 'Cable Assy' AND Act_End != '' AND Name != 'OPERATOR' and Emp_ID NOT IN ('13394','13351') AND DATE BETWEEN '$datefrom' AND '$dateto'");
                    $cable_hc_row = mysqli_fetch_assoc($cable_hc_sql);
                    $total_hc_cable = $cable_hc_row['total_headcount'];

                    // QUERY FOR EMPLOYEES
                    $jlp_sql_data = mysqli_query($conn, "SELECT Name,Qty_Make,cycle_time,SUM(build_percent/100 *Qty_Make) AS build_percent,Stations,description,Part_No,Duration,Prod_Order_No,batch_no,Code,Act_Start,Act_End,remarks,product FROM prod_dtr WHERE DATE BETWEEN '$datefrom' AND '$dateto' AND Qty_Make > 0 AND product = 'JLP' AND Emp_ID !='4444' AND Emp_ID!='11451' AND Labor_Type != 'Prod_Reg_ID' GROUP BY Name,description ORDER BY Name ASC");

                    $array1 = []; // Array to store cycle time values
                    $array2 = []; // Array to store build percent values

                    while ($jlp_row = mysqli_fetch_array($jlp_sql_data)) {

                        $jlp_std = $jlp_row['cycle_time'];
                        $jlp = $jlp_row['product'];

                        if ($jlp_row['Stations'] == 'FVI MODULE') {
                            $std_cycle_time = "3.00";
                        } elseif ($jlp_row['Stations'] == 'SUB TEST') {
                            $std_cycle_time = "2.00";
                        } else {
                            $std_cycle_time = $jlp_std;
                        }
                        $jlp_bp = number_format($jlp_row['build_percent'], 2);

                        $array1[] = $std_cycle_time;
                        $array2[] = $jlp_bp;

                        $JLP_STDxOUTPUT = array_sum(array_map(function ($a, $b) {
                            return $a * $b;
                        }, $array1, $array2));
                        $JLP_WHxMP = 8 * $total_hc_cable;
                        $JLP_TOTALEFF = $JLP_WHxMP != 0 ? number_format(($JLP_STDxOUTPUT / $JLP_WHxMP) * 100, 2) : 0;

                        $result[] = [
                            'JLP_WHxMP' => $JLP_WHxMP,
                            'JLP_STDxOUTPUT' => $JLP_STDxOUTPUT,
                            'JLP_OVERALLEFF' => $JLP_TOTALEFF,
                            'CABLE_TOTALHC' => $total_hc_cable,
                            'product_jlp' => $jlp
                        ];
                    }
                    return $result;
                }

                if (isset($_POST['filter'])) {
                    $datefrom = $_POST['datefrom'];
                    $dateto = $_POST['dateto'];

                ?>
                    <div class="container">
                        <div class="row mx-0">
                            <div class="col">
                                <table class="table table-sm table-hover table-bordered display compact " id="table1">
                                    <thead class="table-secondary">
                                        <tr>
                                            <td colspan="32" class="text-dark bg-light">
                                                <h4 style="background-color: #ADD8E6" class="fw-bold">OVERALL EFFFICIENCY</h4>
                                                <h6 class="fw-bold"> FROM: <?php echo $datefrom . " TO: " . $dateto; ?></h6>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th></th>
                                            <th>Total</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        $results = OVRPRODUCTEFF_MAIN($conn, $datefrom, $dateto);
                                        $result_jlp = $results[0]; // Get JLP results
                                        $result_pnp = $results[1]; // Get PNP results
                                        $TOTALHC_jlp = null;
                                        $WHxMP_jlp = 0;
                                        $OVERALLEFF_jlp = null;
                                        $STDxOUTPUT_jlp = null;
                                        $TOTALHC_pnp = null;
                                        $WHxMP_pnp = 0;
                                        $OVERALLEFF_pnp = null;
                                        $STDxOUTPUT_pnp = null;
                                        // for future purpose (OLB AND JTP)
                                        $STDxOUTPUT_olb = null;
                                        $STDxOUTPUT_jtp = null;
                                        $WHxMP_olb = null;
                                        $WHxMP_jtp = null;

                                        foreach ($result_jlp as $results) {
                                            if (isset($results['JLP_STDxOUTPUT']) && isset($results['JLP_WHxMP']) && isset($results['JLP_TOTALHC']) && isset($results['JLP_OVERALLEFF'])) {
                                                $STDxOUTPUT_jlp = $results['JLP_STDxOUTPUT'];
                                                $WHxMP_jlp = $results['JLP_WHxMP'];
                                                $TOTALHC_jlp = $results['JLP_TOTALHC'];
                                                $OVERALLEFF_jlp = $results['JLP_OVERALLEFF'];
                                            }
                                        }
                                        foreach ($result_pnp as $results) {
                                            if (isset($results['PNP_STDxOUTPUT']) && isset($results['PNP_WHxMP']) && isset($results['PNP_TOTALHC']) && isset($results['PNP_OVERALLEFF'])) {
                                                $STDxOUTPUT_pnp = $results['PNP_STDxOUTPUT'];
                                                $WHxMP_pnp = $results['PNP_WHxMP'];
                                                $TOTALHC_pnp = $results['PNP_TOTALHC'];
                                                $OVERALLEFF_pnp = $results['PNP_OVERALLEFF'];
                                            }
                                        }
                                        $OVR_HC_MAIN = $TOTALHC_jlp + $TOTALHC_pnp;
                                        $OVR_STDxOUTPUT = $STDxOUTPUT_jlp + $STDxOUTPUT_pnp + $STDxOUTPUT_olb + $STDxOUTPUT_jtp;
                                        $OVR_WHxMP = round($WHxMP_jlp + $WHxMP_pnp + $WHxMP_olb + $WHxMP_jtp);

                                        if ($OVR_STDxOUTPUT != 0 && $OVR_WHxMP != 0) {
                                            $OVR_EFF = round($OVR_STDxOUTPUT / $OVR_WHxMP * 100);
                                        } else {
                                            $OVR_EFF = 0;
                                        }
                                        ?>
                                        <tr>
                                            <td>STD X OUTPUT</td>
                                            <td class="fw-bold text-primary"></td>
                                        </tr>
                                        <tr>
                                            <td>WH X MP</td>
                                            <td class="fw-bold text-primary"></td>
                                        </tr>
                                        <tr>
                                            <td class="fw-bold">Efficiency</td>
                                            <td class="fw-bold text-primary"></td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                            <div class="col">
                                <table class="table table-sm table-hover table-bordered display compact text-center " id="table2">
                                    <thead class="table-secondary">
                                        <tr>
                                            <td colspan="32" class="text-dark bg-light">
                                                <h4 style="background-color: #ADD8E6" class="fw-bold">MAIN EFFICIENCY
                                                    <a href="Main_Efficiency_Summary.php?linkTitle=EFFICIENCY SUMMARY" name="update" onclick="event.stopPropagation();">
                                                        <i class="fas fa-info-circle" id="info-icon"></i>
                                                    </a>
                                                </h4>
                                            </td>

                                        </tr>
                                        <tr>
                                            <th></th>
                                            <th>H/C</th>
                                            <th>STD X OUTPUT</th>
                                            <th>WH X MP</th>
                                            <th>Efficiency</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr class="text-center">
                                            <td class="fw-bold">OVERALL:</td>
                                            <td><?php echo $OVR_HC_MAIN; ?></td>
                                            <td><?php echo round($OVR_STDxOUTPUT); ?></td>
                                            <td><?php echo $OVR_WHxMP; ?></td>
                                            <td class="text-primary fw-bolder"><?php echo $OVR_EFF . "%"; ?></td>
                                        </tr>
                                    </tbody>
                                </table>
                                <table class="table table-sm table-hover table-bordered display compact text-center " id="table3">
                                    <thead class="table-secondary">
                                        <tr>
                                            <td colspan="32" class="text-dark bg-light">
                                                <h4 style="background-color: #ADD8E6" class="fw-bold">CABLE EFFICIENCY
                                                    <a href="cable_efficiency_summary.php?linkTitle=CABLE EFFICIENCY SUMMARY" name="update" onclick="event.stopPropagation();">
                                                        <i class="fas fa-info-circle" id="info-icon"></i>
                                                    </a>
                                                </h4>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th></th>
                                            <th>H/C</th>
                                            <th>STD X OUTPUT</th>
                                            <th>WH X MP</th>
                                            <th>Efficiency</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        $_SESSION['datefrom_cable'] = $datefrom;
                                        $_SESSION['dateto_cable'] = $dateto;
                                        $TOTALHC_CABLE = $_SESSION['total_hc_cable'];
                                        $TOTALSTD_CABLE = $_SESSION['total_std_time'];
                                        $TOTALOUTPUT_CABLE = $_SESSION['total_output'];
                                        $OVREFF_CABLE = number_format($TOTALSTD_CABLE, 2) / number_format($TOTALOUTPUT_CABLE, 2) * 100;
                                        ?>
                                        <tr class="text-center">
                                            <td class="fw-bold">OVERALL:</td>
                                            <td><?php echo $TOTALHC_CABLE; ?></td>
                                            <td><?php echo round($TOTALSTD_CABLE); ?></td>
                                            <td><?php echo round($TOTALOUTPUT_CABLE); ?></td>
                                            <td class="text-primary fw-bolder"><?php echo round($OVREFF_CABLE) . "%"; ?></td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                <?php } ?>
            </form>
        </div>

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
            function exportToExcel() {
                // Create a new Workbook
                var workbook = new ExcelJS.Workbook();

                // Add a new Worksheet
                var worksheet = workbook.addWorksheet('Sheet 1');

                // Get the table elements by their IDs
                var table1 = document.getElementById('table1');
                var table2 = document.getElementById('table2');
                var table3 = document.getElementById('table3');

                // Set the starting row index in the Worksheet
                var rowIndex = 1;

                // Export Table 1
                rowIndex = exportTableToWorksheet(table1, worksheet, rowIndex);

                // Export Table 2
                rowIndex = exportTableToWorksheet(table2, worksheet, rowIndex);

                // Export Table 3
                rowIndex = exportTableToWorksheet(table3, worksheet, rowIndex);

                // Save the Workbook as an Excel file
                workbook.xlsx.writeBuffer().then(function(buffer) {
                    var blob = new Blob([buffer], {
                        type: 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'
                    });
                    var url = URL.createObjectURL(blob);
                    var a = document.createElement('a');
                    a.href = url;
                    a.download = 'tables.xlsx';
                    a.click();
                });
            }

            function exportTableToWorksheet(table, worksheet, rowIndex) {
                // Get the table header row
                var headerRow = table.rows[0];

                // Create a new Row in the Worksheet for the header
                var excelHeaderRow = worksheet.getRow(rowIndex);

                // Iterate over each cell in the header row
                for (var j = 0; j < headerRow.cells.length; j++) {
                    var cell = headerRow.cells[j];

                    // Add the cell value to the Excel header row and set the font style to bold
                    var excelCell = excelHeaderRow.getCell(j + 1);
                    excelCell.value = cell.innerText;
                    excelCell.font = {
                        bold: true
                    };
                }

                // Increment the row index for data rows
                rowIndex++;

                // Iterate over each data row in the table
                for (var i = 1; i < table.rows.length; i++) {
                    var row = table.rows[i];

                    // Create a new Row in the Worksheet for data
                    var excelRow = worksheet.getRow(rowIndex);

                    // Iterate over each cell in the data row
                    for (var j = 0; j < row.cells.length; j++) {
                        var cell = row.cells[j];

                        // Add the cell value to the Excel Row
                        excelRow.getCell(j + 1).value = cell.innerText;
                    }

                    // Increment the row index
                    rowIndex++;
                }

                // Add an empty row as a separator between tables
                rowIndex++;
                return rowIndex;
            }
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
</body>

</html>