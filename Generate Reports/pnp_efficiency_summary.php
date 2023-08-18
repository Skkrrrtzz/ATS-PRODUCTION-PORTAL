<?php include '../ATS_Prod_Header.php'; ?>
<?php include '../PROD_navbar.php'; ?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PNP Efficiency Summary</title>
    <script src="../assets/js/exceljs.min.js"></script>
    <!-- <style>
        .centered-table {
            margin: 0 auto;
            width: 50%;
            /* Adjust the width as per your requirement */
        }
    </style> -->
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
                function calculateDetailedEfficiency($conn, $datefrom, $dateto)
                {
                    $result = [];
                    $sum_detailed_eff = [];
                    // QUERY FOR HEADCOUNT
                    $hc_sql = mysqli_query($conn, "SELECT COUNT(DISTINCT Name) AS total_headcount FROM prod_dtr WHERE Department = 'Prod Main' AND Act_End != '' AND Name != 'TECHNICIAN' AND description != 'INDIRECT ACTIVITY' AND Emp_ID != '11451' AND DATE BETWEEN '$datefrom' AND '$dateto'");
                    $hc_row = mysqli_fetch_assoc($hc_sql);
                    $total_headcount = $hc_row['total_headcount'];

                    // QUERY FOR EMPLOYEES
                    $wosql_data = mysqli_query($conn, "SELECT Name,Qty_Make,SUM(cycle_time) AS cycle_time,SUM(build_percent/100 *Qty_Make) AS build_percent,Stations,description,Part_No,Duration,Prod_Order_No,batch_no,Code,Act_Start,Act_End,remarks,product FROM prod_dtr WHERE DATE BETWEEN '$datefrom' AND '$dateto' AND Qty_Make > 0 AND product = 'PNP' AND Emp_ID !='4444' AND Emp_ID!='11451' AND Labor_Type != 'Prod_Reg_ID' GROUP BY Name,description ORDER BY Name ASC");

                    $array1 = []; // Array to store cycle time values
                    $array2 = []; // Array to store build percent values

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
                        $wo_qty = $row['Qty_Make'];
                        $stations = $row['Stations'];
                        $part_no = $row['Part_No'];
                        $detailed_duration = $row['Duration'];
                        $std = $row['cycle_time'];
                        $bp = $row['build_percent'];

                        $array1[] = $std;
                        $array2[] = $bp;

                        $detailed_std = $std != 0 ? $std : 0;
                        $detailed_actual = number_format(($detailed_duration / 60), 2);
                        $detailed_eff = $bp != 0 ? number_format((($detailed_std * $bp) * 100) / 8, 2) : 0;

                        // Sum $detailed_eff for each unique name
                        if (!isset($sum_detailed_eff[$name])) {
                            $sum_detailed_eff[$name] = $detailed_eff;
                        } else {
                            $sum_detailed_eff[$name] += $detailed_eff;
                        }

                        $STDxOUTPUT = round(array_sum(array_map(function ($a, $b) {
                            return $a * $b;
                        }, $array1, $array2)));
                        $WHxMP = 8 * $total_headcount;
                        $total_efficiency = $WHxMP != 0 ? number_format(($STDxOUTPUT / $WHxMP) * 100, 2) : 0;
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
                            'wo_qty' => $wo_qty,
                            'stations' => $stations,
                            'part_no' => $part_no,
                            'detailed_duration' => $detailed_duration,
                            'build_percent' => $bp,
                            'detailed_std' => $detailed_std,
                            'detailed_actual' => $detailed_actual,
                            'detailed_eff' => $detailed_eff,
                            'WHxMP' => $WHxMP,
                            'STDxOUTPUT' => $STDxOUTPUT,
                            'OVERALLEFF' => $total_efficiency
                        ];
                    }


                    // Create the final result array with unique names and summed detailed efficiency
                    foreach ($sum_detailed_eff as $name => $sum_eff) {
                        $result[] = [
                            'name' => $name,
                            'sum_detailed_eff' => $sum_eff
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
                                <table class="table table-sm table-hover table-striped table-bordered display compact " id="table1">
                                    <thead class="table-secondary">
                                        <tr>
                                            <td colspan="32" class="text-dark bg-light">
                                                <h4 style="background-color: #ADD8E6" class="fw-bold">PNP EFFFICIENCY PER EMPLOYEE</h4>
                                                <h6 class="fw-bold"> FROM: <?php echo $datefrom . " TO: " . $dateto; ?></h6>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th>Name</th>
                                            <th>Efficiency</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        $results = calculateDetailedEfficiency($conn, $datefrom, $dateto);
                                        foreach ($results as $result) {
                                            if (isset($result['sum_detailed_eff'])) {
                                        ?>
                                                <tr class="text-center">
                                                    <td><?php echo $result['name']; ?></td>
                                                    <td class="text-primary fw-bolder"><?php echo round($result['sum_detailed_eff']); ?>%</td>
                                                </tr>
                                        <?php
                                            }
                                        }
                                        ?>
                                    </tbody>
                                </table>
                            </div>
                            <div class="col">
                                <table class="table table-sm table-hover table-striped table-bordered display compact text-center " id="table2">
                                    <thead>
                                        <tr>
                                            <td colspan="32" class="text-dark bg-light">
                                                <h4 style="background-color: #ADD8E6" class="fw-bold">PNP OVERALL EFFICIENCY</h4>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th></th>
                                            <th>STD X OUTPUT</th>
                                            <th>WH X MP</th>
                                            <th>Efficiency</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php // Iterate over the result array to find the WHxMP value
                                        $WHxMP = null;
                                        $OVERALLEFF = null;
                                        $STDxOUTPUT = null;
                                        foreach ($results as $result) {
                                            if (isset($result['WHxMP'])) {
                                                $WHxMP = $result['WHxMP'];
                                            }
                                            if (isset($result['OVERALLEFF'])) {
                                                $OVERALLEFF = $result['OVERALLEFF'];
                                            }
                                            if (isset($result['STDxOUTPUT'])) {
                                                $STDxOUTPUT = $result['STDxOUTPUT'];
                                            }
                                        } ?>
                                        <tr class="text-center">
                                            <td class="fw-bold">OVERALL:</td>
                                            <td><?php echo $STDxOUTPUT; ?></td>
                                            <td><?php echo $WHxMP; ?></td>
                                            <td class="text-primary fw-bolder"><?php echo $OVERALLEFF . "%"; ?></td>
                                        </tr>
                                        <?php
                                        ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    <table class="table table-sm table-hover table-striped table-bordered display compact mt-3 fs-6" id="table3">
                        <thead class="table-secondary">
                            <tr>
                                <td colspan="32" class="text-dark bg-light">
                                    <h4 style="background-color: #ADD8E6" class="fw-bold">PNP DETAILED EFFFICIENCY</h4>
                                </td>
                            </tr>
                            <tr class="">
                                <th>TECHNICIAN</th>
                                <th>MODULE</th>
                                <th>STATION</th>
                                <th>PROD NO.</th>
                                <th>PRODUCT</th>
                                <th>PART NO.</th>
                                <th>SERIAL NO.</th>
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
                        </thead>
                        <tbody>
                            <?php
                            foreach ($results as $result) {
                                if (!isset($result['sum_detailed_eff'])) {
                            ?>
                                    <tr class="text-center">
                                        <td><?php echo $result['name']; ?></td>
                                        <td><?php echo $result['description']; ?></td>
                                        <td><?php echo $result['stations']; ?></td>
                                        <td><?php echo $result['prod_no']; ?></td>
                                        <td><?php echo $result['product']; ?></td>
                                        <td><?php echo $result['part_no']; ?></td>
                                        <td><?php echo $result['batch_no']; ?></td>
                                        <td><?php echo $result['code']; ?></td>
                                        <td><?php echo $result['start']; ?></td>
                                        <td><?php echo $result['end']; ?></td>
                                        <td><?php echo $result['remarks']; ?></td>
                                        <td><?php echo $result['detailed_duration']; ?></td>
                                        <td><?php echo $result['wo_qty']; ?></td>
                                        <td><?php echo number_format($result['build_percent'], 2); ?></td>
                                        <td><?php echo $result['detailed_std']; ?></td>
                                        <td class="text-primary fw-bold"><?php echo round($result['detailed_eff']); ?>%</td>
                                    </tr>
                            <?php
                                }
                            }
                            ?>
                        </tbody>
                    </table>
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
            // function exportTablesToExcel() {
            //     var tables = ['table1', 'table2', 'table3']; // Array of table IDs

            //     var workbook = new ExcelJS.Workbook();

            //     tables.forEach(function(tableID) {
            //         var worksheet = workbook.addWorksheet(tableID);

            //         var table = document.getElementById(tableID);
            //         var rows = table.getElementsByTagName('tr');

            //         for (var i = 0; i < rows.length; i++) {
            //             var row = rows[i];
            //             var cells = row.getElementsByTagName('td');

            //             for (var j = 0; j < cells.length; j++) {
            //                 var cell = cells[j];
            //                 var value = cell.innerText || cell.textContent;

            //                 worksheet.getCell(String.fromCharCode(65 + j) + (i + 1)).value = value;
            //             }
            //         }
            //     });

            //     workbook.xlsx.writeBuffer().then(function(buffer) {
            //         var blob = new Blob([buffer], {
            //             type: 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'
            //         });
            //         var url = URL.createObjectURL(blob);
            //         var link = document.createElement('a');
            //         link.href = url;
            //         link.download = 'tables.xlsx';
            //         link.click();
            //         URL.revokeObjectURL(url);
            //     });
            // }
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
</center>

</html>