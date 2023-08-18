<?php include 'ATS_Prod_Header.php'; ?>
<?php include 'PROD_navbar.php'; ?>
<?php include 'PROD_dashboard.php' ?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PRCO MONITORING</title>
    <link rel="stylesheet" href="assets/DataTables/datatables.min.css">
    <script src="assets/DataTables/datatables.min.js"></script>
</head>

<body>
    <ul class="nav nav-tabs py-1" id="myTab" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link active" id="jlp-tab" data-bs-toggle="tab" data-bs-target="#jlp-tab-pane" type="button" role="tab" aria-controls="jlp-tab-pane" aria-selected="true"><i class="fa-solid fa-table"></i> JLP</button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="jtp-tab" data-bs-toggle="tab" data-bs-target="#jtp-tab-pane" type="button" role="tab" aria-controls="jtp-tab-pane" aria-selected="false"><i class="fa-solid fa-table"></i> JTP</button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="olb-tab" data-bs-toggle="tab" data-bs-target="#olb-tab-pane" type="button" role="tab" aria-controls="olb-tab-pane" aria-selected="false"><i class="fa-solid fa-table"></i> OLB</button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="pnp-tab" data-bs-toggle="tab" data-bs-target="#pnp-tab-pane" type="button" role="tab" aria-controls="pnp-tab-pane" aria-selected="false"><i class="fa-solid fa-table"></i> PNP</button>
        </li>
    </ul>
    <div class="tab-content" id="myTabContent">
        <div class="tab-pane fade show active" id="jlp-tab-pane" role="tabpanel" aria-labelledby="jlp-tab" tabindex="0">
            <div class="table-responsive pt-2">
                <table class="inputs">
                    <tbody>
                        <tr>
                            <td>From:</td>
                            <td><input type="text" id="minJLP" name="mins"></td>
                        </tr>
                        <tr>
                            <td>To:</td>
                            <td><input type="text" id="maxJLP" name="maxs"></td>
                        </tr>
                    </tbody>
                </table>
                <table class="table table-striped table-hover display mt-2" style="width:100%;" id="JLP">
                    <thead class="table-dark text-center">
                        <tr>
                            <th class="text-center">No.</th>
                            <th class="text-center">Product</th>
                            <th class="text-center">Machine Cut in</th>
                            <th class="text-center">PRCO#</th>
                            <th class="text-center">REMARKS</th>
                            <th class="text-center">IMPLEMENTED DATE</th>
                        </tr>
                    </thead>
                    <?php
                    // Retrieve data from the database
                    $JLP_result = mysqli_query($conn, "SELECT * FROM `prco` WHERE product IN ('JLP','MTP','IONIZER','FLIPPER','HIGH FORCE','RCMTP')");

                    while ($row = mysqli_fetch_assoc($JLP_result)) {
                        echo "<tr class='text-center fw-semibold'>";
                        echo "<td>{$row['ID']}</td>";
                        echo "<td>{$row['product']}</td>";
                        echo "<td>{$row['Machine']}</td>";
                        echo "<td>{$row['PRCO #']}</td>";
                        echo "<td>{$row['Remarks']}</td>";
                        echo "<td>{$row['Implemented Date']}</td>";
                        echo "</tr>";
                    }
                    ?>
                </table>
            </div>
        </div>
        <div class="tab-pane fade" id="jtp-tab-pane" role="tabpanel" aria-labelledby="jtp-tab" tabindex="0">
            <div class="table-responsive pt-2">
                <table class="inputs">
                    <tbody>
                        <tr>
                            <td>From:</td>
                            <td><input type="text" id="minJTP" name="min"></td>
                        </tr>
                        <tr>
                            <td>To:</td>
                            <td><input type="text" id="maxJTP" name="max"></td>
                        </tr>
                    </tbody>
                </table>
                <table class="table table-striped table-hover display mt-2" style="width:100%;" id="JTP">
                    <thead class="table-dark">
                        <tr>
                            <th class="text-center">No.</th>
                            <th class="text-center">Product</th>
                            <th class="text-center">Machine Cut in</th>
                            <th class="text-center">PRCO#</th>
                            <th class="text-center">REMARKS</th>
                            <th class="text-center">IMPLEMENTED DATE</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        // Retrieve data from the database
                        $JTP_result = mysqli_query($conn, "SELECT * FROM `prco` WHERE product='JTP'");
                        while ($rows = mysqli_fetch_assoc($JTP_result)) {
                            echo "<tr class='text-center fw-semibold'>";
                            echo "<td>{$rows['ID']}</td>";
                            echo "<td>{$rows['product']}</td>";
                            echo "<td>{$rows['Machine']}</td>";
                            echo "<td>{$rows['PRCO #']}</td>";
                            echo "<td>{$rows['Remarks']}</td>";
                            echo "<td>{$rows['Implemented Date']}</td>";
                            echo "</tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="tab-pane fade" id="olb-tab-pane" role="tabpanel" aria-labelledby="olb-tab" tabindex="0">
            <div class="table-responsive pt-2">
                <table class="inputs">
                    <tbody>
                        <tr>
                            <td>From:</td>
                            <td><input type="text" id="minOLB" name="min"></td>
                        </tr>
                        <tr>
                            <td>To:</td>
                            <td><input type="text" id="maxOLB" name="max"></td>
                        </tr>
                    </tbody>
                </table>
                <table class="table table-striped table-hover display mt-2" style="width:100%;" id="OLB">
                    <thead class="table-dark">
                        <tr>
                            <th class="text-center">No.</th>
                            <th class="text-center">Product</th>
                            <th class="text-center">Machine Cut in</th>
                            <th class="text-center">PRCO#</th>
                            <th class="text-center">REMARKS</th>
                            <th class="text-center">IMPLEMENTED DATE</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        // Retrieve data from the database
                        $OLB_result = mysqli_query($conn, "SELECT * FROM `prco` WHERE product='OLB'");
                        while ($rowss = mysqli_fetch_assoc($OLB_result)) {
                            echo "<tr class='text-center fw-semibold'>";
                            echo "<td>{$rowss['ID']}</td>";
                            echo "<td>{$rowss['product']}</td>";
                            echo "<td>{$rowss['Machine']}</td>";
                            echo "<td>{$rowss['PRCO #']}</td>";
                            echo "<td>{$rowss['Remarks']}</td>";
                            echo "<td>{$rowss['Implemented Date']}</td>";
                            echo "</tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="tab-pane fade" id="pnp-tab-pane" role="tabpanel" aria-labelledby="pnp-tab" tabindex="0">
            <div class="table-responsive pt-2">
                <table class="inputs">
                    <tbody>
                        <tr>
                            <td>From:</td>
                            <td><input type="text" id="minPNP" name="min"></td>
                        </tr>
                        <tr>
                            <td>To:</td>
                            <td><input type="text" id="maxPNP" name="max"></td>
                        </tr>
                    </tbody>
                </table>
                <table class="table table-striped table-hover display mt-2" style="width:100%;" id="PNP">
                    <thead class="table-dark">
                        <tr>
                            <th class="text-center">No.</th>
                            <th class="text-center">Product</th>
                            <th class="text-center">Machine Cut in</th>
                            <th class="text-center">PRCO#</th>
                            <th class="text-center">REMARKS</th>
                            <th class="text-center">IMPLEMENTED DATE</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        // Retrieve data from the database
                        $PNP_result = mysqli_query($conn, "SELECT * FROM `prco`");
                        while ($rowss = mysqli_fetch_assoc($PNP_result)) {
                            echo "<tr class='text-center fw-semibold'>";
                            echo "<td>{$rowss['ID']}</td>";
                            echo "<td>{$rowss['product']}</td>";
                            echo "<td>{$rowss['Machine']}</td>";
                            echo "<td>{$rowss['PRCO #']}</td>";
                            echo "<td>{$rowss['Remarks']}</td>";
                            echo "<td>{$rowss['Implemented Date']}</td>";
                            echo "</tr>";
                        } // Close the database connection
                        mysqli_close($conn);
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <script>
        $(document).ready(function() {
            var tables = $('table.display').DataTable({
                columnDefs: [{
                    targets: [1, 2],
                    className: 'fw-bold'
                }],
                stateSave: true,
                deferRender: true,
            });
        });
        $(document).ready(function() {
            // Define the range search function
            function searchJLP(table, minEl, maxEl, columnIndex) {
                // Custom range filtering function
                $.fn.dataTable.ext.search.push(function(settings, data, dataIndex) {
                    // Don't filter on anything other than "myTable"
                    if (settings.nTable.id !== 'JLP') {
                        return true;
                    }
                    var min = parseInt(minEl.val(), 10);
                    var max = parseInt(maxEl.val(), 10);
                    var columnData = data[columnIndex]; // Get the column data
                    var numericData = columnData.split(" ")[1]; // Extract the number from the column
                    // console.log(numericData);
                    // Check if the numeric data is a valid number
                    if (!isNaN(numericData)) {
                        var batch = parseFloat(numericData);

                        if (
                            (isNaN(min) && isNaN(max)) ||
                            (isNaN(min) && batch <= max) ||
                            (min <= batch && isNaN(max)) ||
                            (min <= batch && batch <= max)
                        ) {
                            return true;
                        }
                    }

                    return false;
                });
            }
            // Define the range search function
            function searchJTP(table, minEl, maxEl, columnIndex) {
                // Custom range filtering function
                $.fn.dataTable.ext.search.push(function(settings, data, dataIndex) {
                    // Don't filter on anything other than "myTable"
                    if (settings.nTable.id !== 'JTP') {
                        return true;
                    }
                    var min = parseInt(minEl.val(), 10);
                    var max = parseInt(maxEl.val(), 10);
                    var columnData = data[columnIndex]; // Get the column data
                    var numericData = columnData.split(" ")[1]; // Extract the number from the column
                    // console.log(numericData);
                    // Check if the numeric data is a valid number
                    if (!isNaN(numericData)) {
                        var batch = parseFloat(numericData);

                        if (
                            (isNaN(min) && isNaN(max)) ||
                            (isNaN(min) && batch <= max) ||
                            (min <= batch && isNaN(max)) ||
                            (min <= batch && batch <= max)
                        ) {
                            return true;
                        }
                    }

                    return false;
                });
            }
            // Define the range search function
            function searchOLB(table, minEl, maxEl, columnIndex) {
                // Custom range filtering function
                $.fn.dataTable.ext.search.push(function(settings, data, dataIndex) {
                    // Don't filter on anything other than "myTable"
                    if (settings.nTable.id !== 'OLB') {
                        return true;
                    }
                    var min = parseInt(minEl.val(), 10);
                    var max = parseInt(maxEl.val(), 10);
                    var columnData = data[columnIndex]; // Get the column data
                    var numericData = columnData.split(" ")[1]; // Extract the number from the column
                    // console.log(numericData);
                    // Check if the numeric data is a valid number
                    if (!isNaN(numericData)) {
                        var batch = parseFloat(numericData);

                        if (
                            (isNaN(min) && isNaN(max)) ||
                            (isNaN(min) && batch <= max) ||
                            (min <= batch && isNaN(max)) ||
                            (min <= batch && batch <= max)
                        ) {
                            return true;
                        }
                    }

                    return false;
                });
            }
            // Define the range search function
            function searchPNP(table, minEl, maxEl, columnIndex) {
                // Custom range filtering function
                $.fn.dataTable.ext.search.push(function(settings, data, dataIndex) {
                    // Don't filter on anything other than "myTable"
                    if (settings.nTable.id !== 'PNP') {
                        return true;
                    }
                    var min = parseInt(minEl.val(), 10);
                    var max = parseInt(maxEl.val(), 10);
                    var columnData = data[columnIndex]; // Get the column data
                    var numericData = columnData.split(" ")[1]; // Extract the number from the column
                    // console.log(numericData);
                    // Check if the numeric data is a valid number
                    if (!isNaN(numericData)) {
                        var batch = parseFloat(numericData);

                        if (
                            (isNaN(min) && isNaN(max)) ||
                            (isNaN(min) && batch <= max) ||
                            (min <= batch && isNaN(max)) ||
                            (min <= batch && batch <= max)
                        ) {
                            return true;
                        }
                    }

                    return false;
                });
            }
            // TABLE JLP
            var tableJLP = $('#JLP').DataTable();
            var minJLP = $('#minJLP');
            var maxJLP = $('#maxJLP');
            searchJLP(tableJLP, minJLP, maxJLP, 2);

            minJLP.on('input', function() {
                tableJLP.draw();
            });
            maxJLP.on('input', function() {
                tableJLP.draw();
            });
            // TABLE JTP
            var tableJTP = $('#JTP').DataTable();
            var minJTP = $('#minJTP');
            var maxJTP = $('#maxJTP');
            searchJTP(tableJTP, minJTP, maxJTP, 2);

            minJTP.on('input', function() {
                tableJTP.draw();
            });
            maxJTP.on('input', function() {
                tableJTP.draw();
            });
            // TABLE OLB
            var tableOLB = $('#OLB').DataTable();
            var minOLB = $('#minOLB');
            var maxOLB = $('#maxOLB');
            searchOLB(tableOLB, minOLB, maxOLB, 2);

            minOLB.on('input', function() {
                tableOLB.draw();
            });
            maxOLB.on('input', function() {
                tableOLB.draw();
            });
            // TABLE PNP
            var tablePNP = $('#PNP').DataTable();
            var minPNP = $('#minPNP');
            var maxPNP = $('#maxPNP');
            searchPNP(tablePNP, minPNP, maxPNP, 2);

            minPNP.on('input', function() {
                tablePNP.draw();
            });
            maxPNP.on('input', function() {
                tablePNP.draw();
            });


        });
    </script>
</body>

</html>