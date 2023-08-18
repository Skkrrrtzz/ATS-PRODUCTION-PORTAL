<?php include '../ATS_Prod_Header.php'; ?>
<?php include '../PROD_navbar.php'; ?>
<?php
//session_start();


if (!isset($_SESSION['Emp_ID'])) {
  header('location:ATS_Prod_Home.php');
  exit();
}
$name = $_SESSION['Name'];
$dept = $_SESSION['Department'];
$emp_id = $_SESSION['Emp_ID'];
$dbconnect = mysqli_connect('localhost', 'root', '', 'ewip');

?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Production Order Status</title>
  <style>
    table {
      border-collapse: collapse;
      width: 100%;
      float: center;

    }

    td {
      text-align: center;
      padding: 8px;
      font-size: 12px
    }

    th {
      text-align: center;
      padding: 8px;
      background-color: Gray;
      color: white;
    }

    tr:nth-child(even) {
      background-color: #f2f2f2;
    }

    .btn {
      background-color: lightgreen;
      border: none;
      color: white;
      padding: 12px 30px;
      font-size: 16px;
      cursor: pointer;
    }

    /* Darker background on mouse-over */
    .btn:hover {
      background-color: green;
    }
  </style>


</head>


<body>
  <center>
    <div class="mt-2">
      <table id="po_tcd">
        <tr>
          <th>PO No.</th>
          <th>Product</th>
          <th>Batch No.</th>
          <th>WS</th>
          <th>Part No.</th>
          <th>Description</th>
          <th>Start Date</th>
          <th>Station</th>
          <th>Updated by</th>
          <th>Date Updated</th>
          <th>Status</th>
        </tr>
        <?php

        if (isset($_GET['pageno'])) {
          $pageno = $_GET['pageno'];
        } else {
          $pageno = 1;
        }
        $no_of_records_per_page = 50;
        $offset = ($pageno - 1) * $no_of_records_per_page;

        // Check connection
        if (mysqli_connect_errno()) {
          echo "Failed to connect to MySQL: " . mysqli_connect_error();
          die();
        }

        $total_pages_sql = "SELECT COUNT(*) From prod_module";
        $result = mysqli_query($dbconnect, $total_pages_sql);
        $total_rows = mysqli_fetch_array($result)[0];
        $total_pages = ceil($total_rows / $no_of_records_per_page);


        ?>

        <form method="POST">
          <label for="series_from"><b> FROM JLP#:</b></label>
          <input align="center" type="number" name="series_from" value="">
          <label for="series_to"><b>TO JLP#:</b></label>
          <input type="number" name="series_to" value="">
          <input type="submit" id="filter" name="filter" value="View">
          <input type='button' value='Print/Save' onclick='myApp.printTable()' />
          <button id="btnExport" onclick="javascript:xport.toCSV('po_tcd');">Export to Excel</button> <br>
          <!--STATION: <input align = 'Left' type='text'  id='myInputname'  onkeyup='myFunction()' placeholder='Search Station Name' title='Station' > -->
          <?PHP
          $series_from = "";
          $series_to = "";
          if (isset($_POST['filter'])) {
            $series_from = $_POST['series_from'];
            $series_to = $_POST['series_to'];
            $Dept = "Prod Main";

            $wosql = mysqli_query($dbconnect, "SELECT * From prod_module WHERE batch_no between '$series_from' and '$series_to' 
    ORDER BY module asc, batch_no desc,work_station asc ");
            while ($row = mysqli_fetch_array($wosql)) {
          ?>
              <tr style="text-align:center">
                <td style="font-size:12px;"><?php echo $row['Prod_Order_No']; ?></td>
                <td><?php echo $row['module']; ?></td>
                <td><?php echo $row['batch_no']; ?></td>
                <td><?php echo $row['work_station']; ?></td>
                <td><?php echo $row['Part_No']; ?></td>
                <td><?php echo $row['description']; ?></td>
                <td><?php echo $row['date_received']; ?></td>
                <td><?php echo $row['Stations']; ?></td>
                <td><?php echo $row['Name']; ?></td>
                <td><?php echo $row['date_updated']; ?></td>
                <td><?php echo '<span style="color: ' . ($row['wo_status'] == 'IDLE' ? 'green' : 'orange') . '">' . $row['wo_status'] . '</span>'; ?></td>
              </tr>
          <?php }
          } ?>

      </table>
      <!--  <ul class="pagination">
<li><a href="?pageno=1">First</a></li>
<li class="<?php if ($pageno <= 1) {
              echo 'disabled';
            } ?>">
    <a href="<?php if ($pageno <= 1) {
                echo '#';
              } else {
                echo "?pageno=" . ($pageno - 1);
              } ?>">Prev</a>
</li>
<li class="<?php if ($pageno >= $total_pages) {
              echo 'disabled';
            } ?>">
    <a href="<?php if ($pageno >= $total_pages) {
                echo '#';
              } else {
                echo "?pageno=" . ($pageno + 1);
              } ?>">Next</a>
</li>
<li><a href="?pageno=<?php echo $total_pages; ?>">Last</a></li>
</ul>

  </div>
 

    -->
      <script language="javascript">
        function myFunction() {
          var input, filter, table, tr, td, i, txtValue;
          input = document.getElementById("myInputname");

          filter = input.value.toUpperCase();
          table = document.getElementById("po_tcd");
          tr = table.getElementsByTagName("tr");

          for (i = 0; i < tr.length; i++) {
            td = tr[i].getElementsByTagName("td")[7];
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

    </div>
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

</html>