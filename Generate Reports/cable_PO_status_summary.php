<?php include('header_login.php'); ?>
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
  <title>Cable PO Status</title>
  <style>
    table {
      border-collapse: collapse;
      width: 50%;
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

  <?php

  /*
if (isset($_GET['pageno'])) {
    $pageno = $_GET['pageno'];
} else {
    $pageno = 1;
}
$no_of_records_per_page = 20;
$offset = ($pageno-1) * $no_of_records_per_page;

$conn=mysqli_connect('localhost','root','','ewip');
// Check connection
if (mysqli_connect_errno()){
    echo "Failed to connect to MySQL: " . mysqli_connect_error();
    die();
}

$total_pages_sql = "SELECT COUNT(*) FROM wo";
$result = mysqli_query($conn,$total_pages_sql);
$total_rows = mysqli_fetch_array($result)[0];
$total_pages = ceil($total_rows / $no_of_records_per_page);

*/
  ?>
  <form method="POST">
    <label for="datefrom"><b>TCD FROM:</b></label>
    <input align="center" type="date" name="datefrom" value="">
    <label for="dateto"><b>TO:</b></label>
    <input type="date" name="dateto" value="">
    <input type="submit" id="filter" name="filter" value="View">
    <input type='button' value='Print/Save' onclick='myApp.printTable()' />
    <button id="btnExport" onclick="javascript:xport.toCSV('po_tcd');">Export to Excel</button> <br>

    <?PHP

    if (isset($_POST['filter'])) {
      $datefrom = $_POST['datefrom'];
      $dateto = $_POST['dateto'];
      $Dept = "Cable Assy";

      $fg_wo_sum_sql = mysqli_query($dbconnect, "SELECT count(wo_id) as fg_count FROM wo WHERE TCD between '$datefrom' and '$dateto' AND FG='Yes' ");
      $fg_row = mysqli_fetch_array($fg_wo_sum_sql);
      $fg = $fg_row['fg_count'];

      $target_wo_sum_sql = mysqli_query($dbconnect, "SELECT count(wo_id) as target_count FROM wo WHERE TCD between '$datefrom' and '$dateto' ");
      $target_row = mysqli_fetch_array($target_wo_sum_sql);
      $target = $target_row['target_count'];

      $remarks_sql = mysqli_query($dbconnect, "SELECT count(wo_id) as wo_count,remarks ,for_station,module FROM wo WHERE TCD between '$datefrom' and '$dateto' GROUP BY remarks ORDER BY remarks asc ");
    ?>
      <?php
      $fg_store_sql = mysqli_query($dbconnect, "SELECT count(wo_id) as wo_count,remarks ,for_station,module FROM wo WHERE TCD between '$datefrom' and '$dateto' AND for_station='FG STORE' GROUP BY module ORDER BY remarks asc ");
      $packaging_sql = mysqli_query($dbconnect, "SELECT count(wo_id) as wo_count,remarks ,for_station,module FROM wo WHERE TCD between '$datefrom' and '$dateto' AND for_station='PACKAGING' GROUP BY module ORDER BY remarks asc ");
      $fg_sql = mysqli_query($dbconnect, "SELECT count(wo_id) as wo_count,remarks ,for_station,module FROM wo WHERE TCD between '$datefrom' and '$dateto' AND for_station='FG TRANSACTION' GROUP BY module ORDER BY remarks asc ");
      $oqa_sql = mysqli_query($dbconnect, "SELECT count(wo_id) as wo_count,remarks ,for_station,module FROM wo WHERE TCD between '$datefrom' and '$dateto' AND for_station='OQA' GROUP BY module ORDER BY remarks asc ");
      $vi_sql = mysqli_query($dbconnect, "SELECT count(wo_id) as wo_count,remarks ,for_station,module FROM wo WHERE TCD between '$datefrom' and '$dateto' AND for_station='VISUAL INSPECTION' GROUP BY module ORDER BY remarks asc ");
      $testing_sql = mysqli_query($dbconnect, "SELECT count(wo_id) as wo_count,remarks ,for_station,module FROM wo WHERE TCD between '$datefrom' and '$dateto' AND for_station='TESTING' GROUP BY module ORDER BY remarks asc ");
      $labelling_sql = mysqli_query($dbconnect, "SELECT count(wo_id) as wo_count,remarks ,for_station,module FROM wo WHERE TCD between '$datefrom' and '$dateto' AND for_station='LABELLING' GROUP BY module ORDER BY remarks asc ");
      $shrinking_sql = mysqli_query($dbconnect, "SELECT count(wo_id) as wo_count,remarks ,for_station,module FROM wo WHERE TCD between '$datefrom' and '$dateto' AND for_station='HEAT SHRINKING' GROUP BY module ORDER BY remarks asc ");
      $assy_sql = mysqli_query($dbconnect, "SELECT count(wo_id) as wo_count,remarks ,for_station,module FROM wo WHERE TCD between '$datefrom' and '$dateto' AND for_station='FINAL ASSEMBLY' GROUP BY module ORDER BY remarks asc ");
      $taping_sql = mysqli_query($dbconnect, "SELECT count(wo_id) as wo_count,remarks ,for_station,module FROM wo WHERE TCD between '$datefrom' and '$dateto' AND for_station='TAPING' GROUP BY module ORDER BY remarks asc ");
      $harnessing_sql = mysqli_query($dbconnect, "SELECT count(wo_id) as wo_count,remarks ,for_station,module FROM wo WHERE TCD between '$datefrom' and '$dateto' AND for_station='WIRE HARNESSING' GROUP BY module ORDER BY remarks asc ");
      $molding_sql = mysqli_query($dbconnect, "SELECT count(wo_id) as wo_count,remarks ,for_station,module FROM wo WHERE TCD between '$datefrom' and '$dateto' AND for_station='MOLDING' GROUP BY module ORDER BY remarks asc ");
      $soldering_sql = mysqli_query($dbconnect, "SELECT count(wo_id) as wo_count,remarks ,for_station,module FROM wo WHERE TCD between '$datefrom' and '$dateto' AND for_station='SOLDERING' GROUP BY module ORDER BY remarks asc ");
      $preblocking_sql = mysqli_query($dbconnect, "SELECT count(wo_id) as wo_count,remarks ,for_station,module FROM wo WHERE TCD between '$datefrom' and '$dateto' AND for_station='PRE-BLOCKING' GROUP BY module ORDER BY remarks asc ");
      $ipqc_sql = mysqli_query($dbconnect, "SELECT count(wo_id) as wo_count,remarks ,for_station,module FROM wo WHERE TCD between '$datefrom' and '$dateto' AND for_station='IPQC' GROUP BY module ORDER BY remarks asc ");
      $crimping_sql = mysqli_query($dbconnect, "SELECT count(wo_id) as wo_count,remarks ,for_station,module FROM wo WHERE TCD between '$datefrom' and '$dateto' AND for_station='TERMINAL CRIMPING' GROUP BY module ORDER BY remarks asc ");
      $stripping_sql = mysqli_query($dbconnect, "SELECT count(wo_id) as wo_count,remarks ,for_station,module FROM wo WHERE TCD between '$datefrom' and '$dateto' AND for_station='WIRE STRIPPING' GROUP BY module ORDER BY remarks asc ");
      $cutting_sql = mysqli_query($dbconnect, "SELECT count(wo_id) as wo_count,remarks ,for_station,module FROM wo WHERE TCD between '$datefrom' and '$dateto' AND for_station='WIRE/TUBE CUTTING' GROUP BY module ORDER BY remarks asc ");
      $kitting_sql = mysqli_query($dbconnect, "SELECT count(wo_id) as wo_count,remarks ,for_station,module FROM wo WHERE TCD between '$datefrom' and '$dateto' AND for_station='PARTS KITTING' GROUP BY module ORDER BY remarks asc ");
      $move_sql = mysqli_query($dbconnect, "SELECT count(wo_id) as wo_count,remarks ,for_station,module FROM wo WHERE TCD between '$datefrom' and '$dateto' AND for_station='MOVED/SHIPPED' GROUP BY module ORDER BY remarks asc ");

      ?>
      <table id="po_tcd">
        <tr>
          <th colspan="4">
            <h3>PO STATUS SUMMARY</h3> <?php echo "RUN DATE:" . date("M-d-Y"); ?><BR>TCD FROM <?php echo $datefrom; ?>&nbsp;&nbsp;TO <?php echo $dateto; ?>
          </th>
        </tr>

        <tr style="text-align:center;background-color:lightgrey;color:blue">
          <td>PARTS KITTING</td>
          <TD>WIRE/TUBE CUTTING</TD>
          <TD>STRIPPING</TD>
          <TD>CRIMPING</TD>
        </tr>
        <TR>
          <td style="font-size:12px;"><?php while ($row = mysqli_fetch_array($kitting_sql)) {
                                        $remarks = $row['remarks'];
                                        $remarks_str = mb_substr($remarks, 0, 20);
                                        $wo_count = $row['wo_count'];
                                        echo "$remarks_str ($wo_count)<br>";
                                      }  ?></td>
          <td style="font-size:12px;"><?php while ($row = mysqli_fetch_array($cutting_sql)) {
                                        $remarks = $row['remarks'];
                                        $remarks_str = mb_substr($remarks, 0, 20);
                                        $wo_count = $row['wo_count'];
                                        echo "$remarks_str ($wo_count)<br>";
                                      }  ?></td>

          <td style="font-size:12px;"><?php while ($row = mysqli_fetch_array($stripping_sql)) {
                                        $remarks = $row['remarks'];
                                        $remarks_str = mb_substr($remarks, 0, 20);
                                        $wo_count = $row['wo_count'];
                                        echo "$remarks_str ($wo_count)<br>";
                                      }  ?></td>

          <td style="font-size:12px;"><?php while ($row = mysqli_fetch_array($crimping_sql)) {
                                        $remarks = $row['remarks'];
                                        $remarks_str = mb_substr($remarks, 0, 20);
                                        $wo_count = $row['wo_count'];
                                        echo "$remarks_str ($wo_count)<br>";
                                      }  ?></td>
        </TR>
        <tr style="text-align:center;background-color:lightgrey;color:blue">
          <td>IPQC</td>
          <TD>PRE-BLOCKING</TD>
          <TD>SOLDERING</TD>
          <TD>MOLDING</TD>
        </tr>
        <TR>
          <td style="font-size:12px;"><?php while ($row = mysqli_fetch_array($ipqc_sql)) {
                                        $remarks = $row['remarks'];
                                        $remarks_str = mb_substr($remarks, 0, 20);
                                        $wo_count = $row['wo_count'];
                                        echo "$remarks_str ($wo_count)<br>";
                                      }  ?></td>
          <td style="font-size:12px;"><?php while ($row = mysqli_fetch_array($preblocking_sql)) {
                                        $remarks = $row['remarks'];
                                        $remarks_str = mb_substr($remarks, 0, 20);
                                        $wo_count = $row['wo_count'];
                                        echo "$remarks_str ($wo_count)<br>";
                                      }  ?></td>

          <td style="font-size:12px;"><?php while ($row = mysqli_fetch_array($soldering_sql)) {
                                        $remarks = $row['remarks'];
                                        $remarks_str = mb_substr($remarks, 0, 20);
                                        $wo_count = $row['wo_count'];
                                        echo "$remarks_str ($wo_count)<br>";
                                      }  ?></td>

          <td style="font-size:12px;"><?php while ($row = mysqli_fetch_array($molding_sql)) {
                                        $remarks = $row['remarks'];
                                        $remarks_str = mb_substr($remarks, 0, 20);
                                        $wo_count = $row['wo_count'];
                                        echo "$remarks_str ($wo_count)<br>";
                                      }  ?></td>
        </TR>
        <tr style="text-align:center;background-color:lightgrey;color:blue">
          <td>WIRE HARNESSING</td>
          <TD>TAPING</TD>
          <TD>FINAL ASSEMBLY</TD>
          <TD>HEAT SHRINKING</TD>
        </tr>

        <TR>
          <td style="font-size:12px;"><?php while ($row = mysqli_fetch_array($harnessing_sql)) {
                                        $remarks = $row['remarks'];
                                        $remarks_str = mb_substr($remarks, 0, 20);
                                        $wo_count = $row['wo_count'];
                                        echo "$remarks_str ($wo_count)<br>";
                                      }  ?></td>
          <td style="font-size:12px;"><?php while ($row = mysqli_fetch_array($taping_sql)) {
                                        $remarks = $row['remarks'];
                                        $remarks_str = mb_substr($remarks, 0, 20);
                                        $wo_count = $row['wo_count'];
                                        echo "$remarks_str ($wo_count)<br>";
                                      }  ?></td>

          <td style="font-size:12px;"><?php while ($row = mysqli_fetch_array($assy_sql)) {
                                        $remarks = $row['remarks'];
                                        $remarks_str = mb_substr($remarks, 0, 20);
                                        $wo_count = $row['wo_count'];
                                        echo "$remarks_str ($wo_count)<br>";
                                      }  ?></td>

          <td style="font-size:12px;"><?php while ($row = mysqli_fetch_array($shrinking_sql)) {
                                        $remarks = $row['remarks'];
                                        $remarks_str = mb_substr($remarks, 0, 20);
                                        $wo_count = $row['wo_count'];
                                        echo "$remarks_str ($wo_count)<br>";
                                      }  ?></td>

        </TR>
        <tr style="text-align:center;background-color:lightgrey;color:blue">
          <td>LABELLING</td>
          <TD>TESTING</TD>
          <TD>VISUAL INSPECTION</TD>
          <TD>OQA</TD>
        </tr>
        <td style="font-size:12px;"><?php while ($row = mysqli_fetch_array($labelling_sql)) {
                                      $remarks = $row['remarks'];
                                      $remarks_str = mb_substr($remarks, 0, 20);
                                      $wo_count = $row['wo_count'];
                                      echo "$remarks_str ($wo_count)<br>";
                                    }  ?></td>
        <td style="font-size:12px;"><?php while ($row = mysqli_fetch_array($testing_sql)) {
                                      $remarks = $row['remarks'];
                                      $remarks_str = mb_substr($remarks, 0, 20);
                                      $wo_count = $row['wo_count'];
                                      echo "$remarks_str ($wo_count)<br>";
                                    }  ?></td>

        <td style="font-size:12px;"><?php while ($row = mysqli_fetch_array($vi_sql)) {
                                      $remarks = $row['remarks'];
                                      $remarks_str = mb_substr($remarks, 0, 20);
                                      $wo_count = $row['wo_count'];
                                      echo "$remarks_str ($wo_count)<br>";
                                    }  ?></td>

        <td style="font-size:12px;"><?php while ($row = mysqli_fetch_array($oqa_sql)) {
                                      $remarks = $row['remarks'];
                                      $remarks_str = mb_substr($remarks, 0, 20);
                                      $wo_count = $row['wo_count'];
                                      echo "$remarks_str ($wo_count)<br>";
                                    }  ?></td>

        <tr style="text-align:center;background-color:lightgrey;color:blue">
          <td>FG TRANSACTION</td>
          <TD>PACKAGING</TD>
          <TD>FINISHED GOOD</TD>
          <TD>MOVED/SHIPPED</TD>
        </tr>
        <TR>
          <td style="font-size:12px;"><?php while ($row = mysqli_fetch_array($fg_sql)) {
                                        $remarks = $row['remarks'];
                                        $remarks_str = mb_substr($remarks, 0, 20);
                                        $wo_count = $row['wo_count'];
                                        echo "$remarks_str ($wo_count)<br>";
                                      }  ?></td>
          <td style="font-size:12px;"><?php while ($row = mysqli_fetch_array($packaging_sql)) {
                                        $remarks = $row['remarks'];
                                        $remarks_str = mb_substr($remarks, 0, 20);
                                        $wo_count = $row['wo_count'];
                                        echo "$remarks_str ($wo_count)<br>";
                                      }  ?></td>

          <td style="font-size:12px;"><?php while ($row = mysqli_fetch_array($fg_store_sql)) {
                                        $remarks = $row['remarks'];
                                        $remarks_str = mb_substr($remarks, 0, 20);
                                        $wo_count = $row['wo_count'];
                                        echo "$remarks_str ($wo_count)<br>";
                                      }  ?></td>

          <TD></TD>
        </tr>
        <tr style="text-align:center;background-color:grey;color:white">
          <td colspan="2"> SUMMARY PER STATION</td>
          <TD colspan="2">PO COUNT</TD>
        </tr>
        <?php
        $station_sql = mysqli_query($dbconnect, "SELECT count(wo_id) as wo_count,remarks ,for_station,module FROM wo WHERE TCD between '$datefrom' and '$dateto' GROUP BY for_station ORDER BY for_station asc ");

        while ($row = mysqli_fetch_array($station_sql)) {

        ?>

          <tr style="text-align:center">


            <td style="font-size:12px;" colspan="2"><?php echo $row['for_station']; ?></td>
            <td colspan="2"><?php echo $row['wo_count']; ?></td>
          </tr>

        <?php } ?>
        <tr style="text-align:right;background-color:lightgrey;color:blue">
          <td colspan="2">Target:</td>
          <td colspan="2"><?php echo $target; ?></td>
        </tr>
        <tr style="text-align:right;background-color:lightgrey;color:blue">
          <td colspan="2">FG:</td>
          <td colspan="2"><?php echo $fg; ?></td>
        </tr>
      </table>


      <?php ?>
    <?php } ?>


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
</ul> -->

    </div>


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
      var tab = document.getElementById('po_tcd');
      var win = window.open('', '', 'height=700,width=700');
      win.document.write(tab.outerHTML);
      win.document.close();
      win.print();
    }
  }
</script>

</html>