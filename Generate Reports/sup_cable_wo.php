<?php include '../ATS_Prod_Header.php'; ?>
<?php include '../PROD_navbar.php'; ?>
<?php include 'add_wo_command.php'; ?>
<?php
//session_start();

if (!isset($_SESSION['Emp_ID'])) {
  header('location:ATS_Prod_Home.php');
  exit();
}

$name = $_SESSION['Name'];
$dept = $_SESSION['Department'];
$emp_id = $_SESSION['Emp_ID'];


?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Cable PO Status</title>
</head>

<style>
</style>
<script src="../assets/js/table2excel.js"></script>

</head>


<body>
  <center>
    <h1> PRODUCTION ORDER/S</h1>
    <div class="table table-responsive">
      <table class="table table-bordered table-striped" id="po_tcd">
        <thead class="table-dark text-center">
          <tr>
            <th>ID</th>
            <th>Prod Date</th>
            <th>PO No.</th>
            <th>Part No.</th>
            <th>Qty</th>
            <th>Description</th>
            <th>Batch No.</th>
            <th>Module</th>
            <th>Station</th>
            <th>TCD</th>
            <th>ACD</th>
            <th>Updated by</th>
            <th>Date Updated</th>
            <th>Status</th>
            <th>FG</th>
          </tr>
        </thead>

        <form method="POST">
          <label for="datefrom"><b>TCD FROM:</b></label>
          <input align="center" type="date" name="datefrom" value="">
          <label for="dateto"><b>TO:</b></label>
          <input type="date" name="dateto" value="">
          <div class="align-text-center pt-2 pb-2 fw-bold">
            STATION: <input align='Left' type='text' id='myInputname' onkeyup='myFunction()' placeholder='Search Station Name' title='Station'>
            <button class="btn btn-secondary btn-sm" type="submit" id="filter" name="filter">View</button>
            <button class="btn btn-primary btn-sm" type='button' onclick='myApp.printTable()'>Print/Save</button>
            <button class="btn btn-success btn-sm" id="Export">Export to Excel</button>
          </div>
          <!--<button id="btnExport" onclick="javascript:xport.toCSV('po_tcd');">Export to Excel</button> <br>-->
          <?php
          $dbconnect = mysqli_connect('localhost', 'root', '', 'ewip');

          if (isset($_POST['filter'])) {
            $datefrom = $_POST['datefrom'];
            $dateto = $_POST['dateto'];
            $Dept = "Cable Assy";

            $wosql = mysqli_query($dbconnect, "SELECT wo_id,wo_quantity,part_no,prod_no,description,planner,wo_date,updated_by,date_updated,remarks,module,for_station,TCD,ACD,updated_by,status,FG FROM wo WHERE TCD between '$datefrom' and '$dateto' ORDER BY FG asc,for_station desc ");
            while ($row = mysqli_fetch_array($wosql)) {
              $quanity = $row['wo_quantity'];
              $part_no = $row['part_no'];
              $batch_no = $row['prod_no'];
              $description = $row['description'];
              $planner = $row['planner'];
              $wo_date = $row['wo_date'];
              //$date_format= ("M d,Y");
              $updated_by = $row['updated_by'];
              $date_updated = $row['date_updated'];

          ?>
              <tr class="text-center fw-bold">
                <td><?php echo $row['wo_id']; ?></td>
                <td><?php echo $row['wo_date']; ?></td>
                <td><?php echo $row['prod_no']; ?></td>
                <td><?php echo $row['part_no']; ?></td>
                <td><?php echo $row['wo_quantity']; ?></td>
                <td><?php echo $row['description']; ?></td>
                <td><?php echo $row['remarks']; ?></td>
                <td><?php echo $row['module']; ?></td>
                <td><?php echo $row['for_station']; ?></td>
                <td><?php echo $row['TCD']; ?></td>
                <td><?php echo $row['ACD']; ?></td>
                <td><?php echo $row['updated_by']; ?></td>
                <td><?php echo $row['date_updated']; ?></td>
                <td><?php echo $row['status']; ?></td>
                <td><?php echo $row['FG']; ?></td>
              </tr>
          <?php }
          } ?>
        </form>
      </table>
    </div>

    </div>

    <table class="table table-bordered">
      <thead class="bg-secondary text-center">
        <tr>
          <th colspan='8'>ACTIVITY CODE:</th>
        </tr>
      </thead>
      <tr class="bg-light fw-bold" style="font-size:10px">
        <td>101 - MH Parts Kitting</td>
        <td>102 - MH Breaktime</td>
        <td>103 - M Training/Meeting/Seminar</td>
        <td>104 - MH Personal Needs/Trip to Clinic/HR/Finance </td>
        <td>105 - MH 5's Housekeeping</td>
        <td colspan="2">106 - MH Support to other group/Special Project</td>
        <td colspan="">107 - MH Inventory Taking</td>
      </tr>
      <tr class="bg-light fw-bold" style="font-size:10px">
        <td>200 - Manual Wire Cutting</td>
        <td>201 - Auto Wire Cutting</td>
        <td>202 - Wire Stripping/Tube Cutting</td>
        <td>203 - Manual Terminal Crimping</td>
        <td>204 - Auto Terminal Crimping</td>
        <td>205 - Soldering</td>
        <td>206 - Molding</td>
        <td>207 - Wire Harnessing/Final Assembly</td>
      </tr>
      <tr class="bg-light fw-bold" style="font-size:10px">
        <td>209 - Labelling</td>
        <td>210 - Testing</td>
        <td>211 - Visual Inspection</td>
        <td>1004 - OQA</td>
        <td>213 - FG Transaction</td>
        <td> 0000 - OBQ</td>
        <td colspan="2">215 - Packaging</td>
      </tr>
      <tr class="bg-light fw-bold" style="font-size:10px">
        <td></td>
        <td>301 -Rework / Retest</td>
        <td>302 - Parts received checking</td>
        <td>303 - Breaktime</td>
        <td>304 - Wait part </td>
        <td>305 - Doc. Gen'tion(QIF)</td>
        <td colspan="2">306 - Drawing/BOM/MPI/MTI Verification</td>
      </tr>
      <tr class="bg-light fw-bold" style="font-size:10px">
        <td></td>
        <td>307 - Training/Meeting/Seminar</td>
        <td>308 - Facility Downtime</td>
        <td>309 - Personal Needs/Trip to clinic/HR/Finance</td>
        <td>310 - 5's/Housekeeping </td>
        <td>311 - Support to other group</td>
        <td colspan="2">312 - Inventory Taking</td>
      </tr>
    </table>

    <script>
      // EXPORT TO XLSX FILE
      document.getElementById('Export').addEventListener('click', function() {
        var table2excel = new Table2Excel();
        table2excel.export(document.querySelectorAll("#po_tcd"));
      });
    </script>


    <script language="javascript">
      function myFunction() {
        var input, filter, table, tr, td, i, txtValue;
        input = document.getElementById("myInputname");

        filter = input.value.toUpperCase();
        table = document.getElementById("po_tcd");
        tr = table.getElementsByTagName("tr");

        for (i = 0; i < tr.length; i++) {
          td = tr[i].getElementsByTagName("td")[8];
          if (td) {
            txtValue = td.textContent || td.innerText;
            if (txtValue.toUpperCase().indexOf(filter) > -1) {
              tr[i].style.display = "";
            } else {
              tr[i].style.display = "none";
            }
          }
        }
      }
    </script>


</body>

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
        type: "base64"
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

</html>