<?php include '../ATS_Prod_Header.php'; ?>
<?php include '../PROD_navbar.php';

if (!isset($_SESSION['Emp_ID'])) {
    header('location:ATS_Prod_Home.php');
    exit();
}
$name = $_SESSION['Name'];
$dept = $_SESSION['Department'];
$emp_id = $_SESSION['Emp_ID'];

// function getDefaultDate($fieldName)
// {
//     return isset($_GET[$fieldName]) ? $_GET[$fieldName] : date('Y-m-d');
// }

// $defaultDateFrom = getDefaultDate('datefrom');
// $defaultDateTo = getDefaultDate('dateto');
// $datefrom = $defaultDateFrom;
// $dateto = $defaultDateTo;
?>

<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cable Performance Summary</title>
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
            <label for="datefrom"><b>FROM:</b></label>
            <input align="center" type="date" name="datefrom" id="datefrom" value="">
            <label for="dateto"><b>TO:</b></label>
            <input type="date" name="dateto" id="dateto" value="">
            <!-- <button class="btn btn-secondary btn-sm mb-1" id="filter" name="filter">View</button> -->
            <button class="btn btn-secondary btn-sm mb-1" id="filter" name="filter">View</button>
            <button class="btn btn-secondary btn-sm mb-1" onclick='myApp.printTable()'>Print/Save</button>
            <button class="btn btn-success btn-sm mb-1" id="btnExport" onclick="javascript:xport.toCSV('efficiencysummary');">Export to Excel</button>
            <!--<button id="Export">Export to Excel</button><br>-->
            <!-- <div id="spinner">
                <span id="loading-text">Loading...</span>
                <div class="spinner-border" role="status" id="spinner" aria-hidden="true">
                </div>
            </div> -->
            <div class="table-responsive" id="cable_eff">
                <table class="table-sm" id="efficiencysummary" border="1">
                    <tr>
                        <td style='text-align:center' colspan='32' width='100%'>
                            <h4 style="background-color: #ADD8E6; font-weight: bold">
                                <bold>CABLE ASSY EFFICIENCY PER EMPLOYEE</bold>
                            </h4>
                            <?php
                            // echo "FROM: $datefrom TO: $dateto";

                            ?>
                        </td>
                    </tr>
                    <thead>
                        <tr>
                            <th style="background-color:gray" colspan="3">Name</th>
                            <th style="background-color:gray" colspan="3">Standard</th>
                            <th style="background-color:gray" colspan="2">Actual</th>
                            <th style="background-color:gray" colspan="3">Efficiency</th>
                            <th style="background-color:gray" colspan="3">Attendance</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr></tr>
                    </tbody>

                    <tr style="background-color:#ADD8E6">
                        <td colspan='3'>OVERALL: </td>
                        <td colspan='3'> &nbsp;hrs.</td>
                        <td colspan='2'> &nbsp;hrs.</td>
                        <td colspan='3'> %</td>
                        <td colspan='3'> %</td>
                    </tr>
                </table>
            </div>


            <div class="table-responsive" id="detailed">
                <table class="table-sm" border="1">
                    <tr>
                        <th colspan="14">DETAILED CABLE ASSY EFFICIENCY</th>
                    </tr>
                    <thead>
                        <tr>
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
                            <th>EFF</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr></tr>
                    </tbody>
                </table>
            </div>
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
    // $(document).ready(function() {
    //     $("#filter").on("click", function() {
    //         fetchData();
    //     });
    // });

    function fetchData() {
        var datefrom = document.getElementById('datefrom').value;
        var dateto = document.getElementById('dateto').value;

        $.ajax({
            type: "POST",
            url: "data_handler.php",
            data: {
                datefrom: datefrom,
                dateto: dateto
            },
            dataType: "json",
            success: function(response) {
                var table = document.getElementById('efficiencysummary');

                // Clear existing rows
                $('#efficiencysummary tbody').empty();

                // Populate the header row
                var headerRow = table.insertRow(1);
                var headers = ['Name', 'Standard', 'Actual', 'Efficiency', 'Attendance'];
                for (var i = 0; i < headers.length; i++) {
                    var cell = headerRow.insertCell(i);
                    cell.style.backgroundColor = 'gray';
                    cell.colSpan = i === 0 ? 3 : 3; // Adjust colspan for different headers
                    cell.innerHTML = headers[i];
                }

                // Populate the data rows
                for (var i = 0; i < response.names.length; i++) {
                    var newRow = table.insertRow(table.rows.length - 1);

                    var nameCell = newRow.insertCell(0);
                    nameCell.innerHTML = response.names[i].name;

                    var stdTimeCell = newRow.insertCell(1);
                    stdTimeCell.innerHTML = response.names[i].total_std_time;

                    var actualTimeCell = newRow.insertCell(2);
                    actualTimeCell.innerHTML = response.names[i].total_actual_time;

                    var efficiencyCell = newRow.insertCell(3);
                    efficiencyCell.innerHTML = response.names[i].efficiency_percentage + "%";

                    var attendanceCell = newRow.insertCell(4);
                    attendanceCell.innerHTML = response.names[i].total_present;
                }
            },
            error: function(jqXHR, textStatus, errorThrown) {
                console.log("Error:", errorThrown);
            }
        });
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


    function checkAndSave() {
        var totalEffValue = <?php echo $total_eff; ?>;
        var targetValue = 98;

        var currentTime = new Date();
        var currentHour = currentTime.getHours();

        if (currentHour >= 14 && currentHour < 17 && totalEffValue >= targetValue) {
            dataURLPromise.then(function(dataURL) {
                // Send AJAX request to insert data and send email
                $.ajax({
                    type: "POST",
                    url: "../Efficiency_Checking.php",
                    data: {
                        efficiency: totalEffValue,
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
                                    showConfirmButton: false,
                                    timer: 3000
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
            clearInterval(intervalId);
        }
    }

    // Start the interval and store the interval ID
    intervalId = setInterval(checkAndSave, 3000); // 3 seconds

    // Call saveCharts and store the promise in dataURLPromise
    dataURLPromise = saveCharts('cable_eff');
</script>

</html>