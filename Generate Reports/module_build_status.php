<?php include '../ATS_Prod_Header.php'; ?>
<?php include '../PROD_navbar.php'; ?>
<?php
$name = $_SESSION['Name'];
$dept = $_SESSION['Department'];
$emp_id = $_SESSION['Emp_ID'];
$quantity = "";
$part_no = "";
$prod_no = "";
$description = "";
$build_end = "";
$date_shipped = "";
$for_station = "";
$fa_percentage = "";
$txp_percentage = "";
$cda_percentage = "";
$ac_percentage = "";
$tsl_percentage = "";
$cdm_percentage = "";
$fc_percentage = "";
$ion_percentage = "";
$mtp_percentage = "";
$flip_percentage = "";
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Module Build Status</title>
  <script src="table2excel.js"></script>
</head>


<body>

  <center>
    <div class="pt-2">
      <form method="POST">
        <label for="series_from"><b> FROM JLP#:</b></label>
        <input align="center" type="number" name="series_from" value="">
        <label for="series_to"><b>TO JLP#:</b></label>
        <input type="number" name="series_to" value="">
        <button class="btn btn-secondary btn-sm mb-2" type="submit" name="filter" id="filter">View</button>
        <button class="btn btn-secondary btn-sm mb-2" type='button' onclick='myApp.printTable()'>Print/Save</button>
        <!--<button id="btnExport" onclick="javascript:xport.toCSV('efficiencysummary');">Export to Excel</button>-->
        <button class="btn btn-success btn-sm mb-2" id="Export">Export to Excel</button>
        <?PHP

        $all_efficiency = " ";
        $all_total_present_day = 0;
        $Dept = "";
        $series_from = "";
        $series_to = "";
        $total_work_day = "";
        $total_operator = "";
        $all_attendance_format = "";
        $all_actual_time_format = "";
        $all_standard_time_format = "";

        if (isset($_POST['filter'])) {
          $series_from = $_POST['series_from'];
          $series_to = $_POST['series_to'];
          $Dept = "Prod Main";

        ?>

          <table class="table table-bordered table-sm fw-bold border border-dark-subtle" id="efficiencysummary">
            <thead class="text-center">
              <tr>
                <td colspan='36' width='100%'>
                  <h4 style="background-color: #ADD8E6 ">
                    <bold>JLP BUILD STATUS</bold>
                  </h4>
                  <?php
                  echo "FROM: $series_from TO: $series_to";
                  ?>
                </td>
              </tr>

              <tr class="sticky-top text-bg-primary" id="stickyHeader">
                <th>JLP/SERIES #</th>
                <th>Start Build</th>
                <th>End Build</th>
                <th>Shipped Date</th>
                <th>Aging</th>
                <th>Assembly Status</th>
                <th>FA</th>
                <th>TXP</th>
                <th>CDA</th>
                <th>AC</th>
                <th>TSL</th>
                <th>CDM</th>
                <th>FC</th>
                <th>MTP</th>
                <th>ION</th>
                <th>FLIP</th>
                <th>INT Status</th>
                <th>F-TEST Status</th>
              </tr>


              <?php

              // SELECT AND GET DATES BETWEEN AND BATCH NO. AND DATE RECEIVED
              $batch_sql = mysqli_query($conn, "SELECT batch_no,date_received,dateDiff(now(),date_received) as No_Days  FROM prod_module WHERE module = 'JLP' AND batch_no BETWEEN '$series_from' and '$series_to' AND description!='INDIRECT ACTIVITY' group by batch_no order by batch_no desc");
              while ($row = mysqli_fetch_array($batch_sql)) {
                $batch = $row['batch_no'];
                $date_received = $row['date_received'];
                $age = $row['No_Days'];

                // SELECT AND GET DATA OF BUILD END AND DATE SHIPPED
                $final_sql = mysqli_query($conn, "SELECT build_end,date_shipped FROM prod_module WHERE batch_no = '{$row['batch_no']}' AND description !='JLP GEN 3, MAIN ASSEMBLY' AND work_station ='' ");
                if ($rowIn = mysqli_fetch_array($final_sql)) {
                  $build_end = $rowIn['build_end'];
                  if (empty($build_end) || strtotime($build_end) <= strtotime($date_received)) {
                    $age = floor((strtotime(date("Y-m-d")) - strtotime($date_received)) / 86400);
                  } else {
                    $age = floor((strtotime($build_end) - strtotime($date_received)) / 86400) + 1;
                  }
                  $date_shipped = $rowIn['date_shipped'];
                } else {
                  $build_end = "0000-00-00";
                  $date_shipped = "0000-00-00";
                }

                // SELECTING EVERY MODULES BUILD PERCENTAGE FROM PROD_MODULE
                $fa_sql = mysqli_query($conn, "SELECT CONCAT(FORMAT(build_percent,2),'%') AS BP FROM prod_module WHERE batch_no = '{$row['batch_no']}' AND description ='FRAME ASSY, JLP G3'");
                if ($row_fa = mysqli_fetch_array($fa_sql)) {
                  $fa_percentage = $row_fa['BP'];
                } else {
                  $fa_percentage = "0.00%";
                }

                $txp_sql = mysqli_query($conn, "SELECT CONCAT(FORMAT(build_percent,2),'%') AS BP FROM prod_module WHERE batch_no = '{$row['batch_no']}' AND description IN ('TRAY TRANSPORT ASSY,JLP G3','TRAY TRANSPORT ASSY,JLP G3, HIFORCE')");
                if ($row_txp = mysqli_fetch_array($txp_sql)) {
                  $txp_percentage = $row_txp['BP'];
                } else {
                  $txp_percentage = "0.00%";
                }

                $cda_sql = mysqli_query($conn, "SELECT CONCAT(FORMAT(build_percent,2),'%') AS BP FROM prod_module WHERE batch_no = '{$row['batch_no']}' AND description ='CONVEYOR DRIVE ASSY,JLP G3'");
                if ($row_cda = mysqli_fetch_array($cda_sql)) {
                  $cda_percentage = $row_cda['BP'];
                } else {
                  $cda_percentage = "0.00%";
                }

                $ac_sql = mysqli_query($conn, "SELECT CONCAT(FORMAT(build_percent,2),'%') AS BP FROM prod_module WHERE batch_no = '{$row['batch_no']}' AND description ='ASSY, CONVEYOR JLP-G3'");
                if ($row_ac = mysqli_fetch_array($ac_sql)) {
                  $ac_percentage = $row_ac['BP'];
                } else {
                  $ac_percentage = "0.00%";
                }

                $tsl_sql = mysqli_query($conn, "SELECT CONCAT(FORMAT(build_percent,2),'%') AS BP FROM prod_module WHERE batch_no = '{$row['batch_no']}' AND description ='LIFT ASSY,TRAY STACK-JLP G3'");
                if ($row_tsl = mysqli_fetch_array($tsl_sql)) {
                  $tsl_percentage = $row_tsl['BP'];
                } else {
                  $tsl_percentage = "0.00%";
                }

                $cdm_sql = mysqli_query($conn, "SELECT CONCAT(FORMAT(build_percent,2),'%') AS BP FROM prod_module WHERE batch_no = '{$row['batch_no']}' AND description ='ASSY CART DOCKING MECH-JLP G3'");
                if ($row_cdm = mysqli_fetch_array($cdm_sql)) {
                  $cdm_percentage = $row_cdm['BP'];
                } else {
                  $cdm_percentage = "0.00%";
                }

                $fc_sql = mysqli_query($conn, "SELECT CONCAT(FORMAT(build_percent,2),'%') AS BP FROM prod_module WHERE batch_no = '{$row['batch_no']}' AND description ='ASSY, FACILITY CABINET, JLP-G3'");
                if ($row_fc = mysqli_fetch_array($fc_sql)) {
                  $fc_percentage = $row_fc['BP'];
                } else {
                  $fc_percentage = "0.00%";
                }

                $mtp_sql = mysqli_query($conn, "SELECT CONCAT(FORMAT(build_percent,2),'%') AS BP FROM prod_module WHERE batch_no = '{$row['batch_no']}' AND description='KIT, MANUAL TRAY PLATF, JLP G2'");
                if ($row_mtp = mysqli_fetch_array($mtp_sql)) {
                  $mtp_percentage = $row_mtp['BP'];
                } else {
                  $mtp_percentage = "0.00%";
                }

                $ion_sql = mysqli_query($conn, "SELECT CONCAT(FORMAT(build_percent,2),'%') AS BP FROM prod_module WHERE batch_no = '{$row['batch_no']}' AND description ='KIT, JLP, AIR KNIFE OPTION'");
                if ($row_ion = mysqli_fetch_array($ion_sql)) {
                  $ion_percentage = $row_ion['BP'];
                } else {
                  $ion_percentage = "0.00%";
                }

                $flip_sql = mysqli_query($conn, "SELECT CONCAT(FORMAT(build_percent,2),'%') AS BP FROM prod_module WHERE batch_no = '{$row['batch_no']}' AND description ='KIT, TRAY FLIP MECHANISM, JLP G2'");
                if ($row_flip = mysqli_fetch_array($flip_sql)) {
                  $flip_percentage = $row_flip['BP'];
                } else {
                  $flip_percentage = "0.00%";
                }

                // SELECTING WITH OPTIONS FROM TABLE PROD_MODULE
                $option_mtp_sql = mysqli_query($conn, "SELECT ID FROM prod_module WHERE batch_no = '$batch' AND description='KIT, MANUAL TRAY PLATF, JLP G2' GROUP BY work_station");
                $option_ion_sql = mysqli_query($conn, "SELECT ID FROM prod_module WHERE batch_no = '$batch' AND description='KIT, JLP, AIR KNIFE OPTION' GROUP BY work_station ");
                $option_flip_sql = mysqli_query($conn, "SELECT ID FROM prod_module WHERE batch_no = '$batch' AND description='KIT, TRAY FLIP MECHANISM, JLP G2' GROUP BY work_station");
                $option = mysqli_num_rows($option_mtp_sql) + mysqli_num_rows($option_ion_sql) + mysqli_num_rows($option_flip_sql);

                if ($option > 0) {
                  //$module_count_sql=mysqli_query($conn,"SELECT * FROM prod_module WHERE batch_no = '$batch' GROUP BY work_station ");
                  $module_count = 7;
                  $fi_module_count = $option;
                } else {
                  $module_count = 7;
                  $fi_module_count = 8;
                }

                // GET ALL THE MODULES WITH 100% BUILD FOR STATUS
                $fa_sqli = mysqli_query($conn, "SELECT ID FROM prod_module WHERE batch_no = '$batch' AND description ='FRAME ASSY, JLP G3' AND build_percent='100'");
                $fa_ = mysqli_num_rows($fa_sqli);

                $txp_sqli = mysqli_query($conn, "SELECT ID FROM prod_module WHERE batch_no = '$batch' AND description IN ('TRAY TRANSPORT ASSY,JLP G3','TRAY TRANSPORT ASSY,JLP G3, HIFORCE') AND build_percent='100'");
                $txp_ = mysqli_num_rows($txp_sqli);

                $cda_sqli = mysqli_query($conn, "SELECT ID FROM prod_module WHERE batch_no = '$batch' AND description ='CONVEYOR DRIVE ASSY,JLP G3'AND build_percent='100'");
                $cda_ = mysqli_num_rows($cda_sqli);

                $ac_sqli = mysqli_query($conn, "SELECT ID FROM prod_module WHERE batch_no = '$batch' AND description ='ASSY, CONVEYOR JLP-G3' AND build_percent='100'");
                $ac_ = mysqli_num_rows($ac_sqli);

                $tsl_sqli = mysqli_query($conn, "SELECT ID FROM prod_module WHERE batch_no = '$batch' AND description ='LIFT ASSY,TRAY STACK-JLP G3' AND build_percent='100'");
                $tsl_ = mysqli_num_rows($tsl_sqli);

                $cdm_sqli = mysqli_query($conn, "SELECT ID FROM prod_module WHERE batch_no = '$batch' AND description ='ASSY CART DOCKING MECH-JLP G3' AND build_percent='100'");
                $cdm_ = mysqli_num_rows($cdm_sqli);

                $fc_sqli = mysqli_query($conn, "SELECT ID FROM prod_module WHERE batch_no = '$batch' AND description ='ASSY, FACILITY CABINET, JLP-G3' AND build_percent='100'");
                $fc_ = mysqli_num_rows($fc_sqli);

                $mtp_sqli = mysqli_query($conn, "SELECT ID FROM prod_module WHERE batch_no = '$batch' AND description='KIT, MANUAL TRAY PLATF, JLP G2' AND build_percent='100'");
                $mtp_ = mysqli_num_rows($mtp_sqli);

                $ion_sqli = mysqli_query($conn, "SELECT ID FROM prod_module WHERE batch_no = '$batch' AND description ='KIT, JLP, AIR KNIFE OPTION' AND build_percent='100'");
                $ion_ = mysqli_num_rows($ion_sqli);

                $flip_sqli = mysqli_query($conn, "SELECT ID FROM prod_module WHERE batch_no = '$batch' AND description ='KIT, TRAY FLIP MECHANISM, JLP G2' AND build_percent='100'");
                $flip_ = mysqli_num_rows($flip_sqli);

                /* CALLING EVERY MODULE IN PROD_MODULE TABLE
              $fa_hundred = mysqli_num_rows($hundred_fa_sql);
              $txp_hundred = mysqli_num_rows($hundred_txp_sql);
              $cda_hundred = mysqli_num_rows($hundred_cda_sql);
              $ac_hundred = mysqli_num_rows($hundred_ac_sql);
              $tsl_hundred = mysqli_num_rows($hundred_tsl_sql);
              $cdm_hundred = mysqli_num_rows($hundred_cdm_sql);
              $fc_hundred = mysqli_num_rows($hundred_fc_sql);
              $mtp_hundred = mysqli_num_rows($hundred_mtp_sql);
              $ion_hundred = mysqli_num_rows($hundred_ion_sql);
              $flip_hundred = mysqli_num_rows($hundred_flip_sql);*/

                /*$fa_half = ($fa_fifty / 1) * 50;
        $txp_half = ($txp_fifty / 1) * 50;
        $cda_half = ($cda_fifty / 1) * 50;
        $ac_half = ($ac_fifty / 1) * 50;
        $tsl_half = ($tsl_fifty / 1) * 50;
        $cdm_half = ($cdm_fifty / 1) * 50;
        $fc_half = ($fc_fifty / 1) * 50;
        $mtp_half = ($mtp_fifty / 1) * 50;
        $ion_half = ($ion_fifty / 1) * 50;
        $flip_half = ($flip_fifty / 1) * 50;*/

                // FORMULA FOR PERCENTAGE OF EVERY MODULES MADE
                $fa_full = ($fa_ / 1) * 100;
                $txp_full = ($txp_ / 1) * 100;
                $cda_full = ($cda_ / 1) * 100;
                $ac_full = ($ac_ / 1) * 100;
                $tsl_full = ($tsl_ / 1) * 100;
                $cdm_full = ($cdm_ / 1) * 100;
                $fc_full = ($fc_ / 1) * 100;
                $mtp_full = ($mtp_ / 1) * 100;
                $ion_full = ($ion_ / 1) * 100;
                $flip_full = ($flip_ / 1) * 100;

                /*$fa = $fa_half + $fa_full;
        $txp = $txp_half + $txp_full;
        $cda = $cda_half + $cda_full;
        $ac = $ac_half + $ac_full;
        $tsl = $tsl_half + $tsl_full;
        $cdm = $cdm_half + $cdm_full;
        $fc = $fc_half + $fc_full;
        $mtp = $mtp_half + $mtp_full;
        $ion = $ion_half + $ion_full;
        $flip = $flip_half + $flip_full;*/

                /* WILL EXECUTE THE PERCENTAGE OF EVERY MODULES
              $fa_format = number_format($fa_percentage, 2);
              $txp_format = number_format($txp_percentage, 2);
              $cda_format = number_format($cda_percentage, 2);
              $ac_format = number_format($ac_percentage, 2);
              $tsl_format = number_format($tsl_percentage, 2);
              $cdm_format = number_format($cdm_percentage, 2);
              $fc_format = number_format($fc_percentage, 2);
              $mtp_format = number_format($mtp_percentage, 2);
              $ion_format = number_format($ion_percentage, 2);
              $flip_format = number_format($flip_percentage, 2);*/

                // FORMULA FOR THE AVERAGE MODULES MADE
                if ($module_count > 0) {
                  $ave = ($fa_full + $txp_full + $cda_full + $ac_full + $tsl_full + $cdm_full + $fc_full) / $module_count;
                  $ave_format = number_format($ave, 2);
                } else {
                  $ave = 0;
                }
                // FINAL INT BUILD PERCENTAGE
                $fi_sql = mysqli_query($conn, "SELECT CONCAT(FORMAT(build_percent,2),'%') AS BP  FROM prod_module WHERE batch_no = '{$row['batch_no']}' AND description ='JLP GEN 3, MAIN ASSEMBLY 'GROUP BY work_station");
                if ($row_fi = mysqli_fetch_array($fi_sql)) {
                  $fi_percentage = $row_fi['BP'];
                } else {
                  $fi_percentage = "0.00%";
                }
                // FINAL TEST BUILD PERCENTAGE
                $ft_sql = mysqli_query($conn, "SELECT CONCAT(FORMAT(build_percent,2),'%') AS BP FROM prod_module WHERE batch_no = '{$row['batch_no']}' AND work_station = '' AND description !='JLP GEN 3, MAIN ASSEMBLY' GROUP BY work_station");
                if ($row_ft = mysqli_fetch_array($ft_sql)) {
                  $ft_percentage = $row_ft['BP'];
                } else {
                  $ft_percentage = "0.00%";
                }

                $ft_activity_sql = mysqli_query($conn, "SELECT * FROM prod_dtr WHERE batch_no = '$batch' AND Stations = 'FINAL TEST' GROUP BY Activity");
                $ft_activity_count = mysqli_num_rows($ft_activity_sql);

                $fvi_machine_sql = mysqli_query($conn, "SELECT * FROM prod_module WHERE batch_no = '$batch' AND Stations = 'FVI MAC' GROUP BY batch_no");
                $final_oqa_sql = mysqli_query($conn, "SELECT * FROM prod_module WHERE batch_no = '$batch' AND Stations = 'FINAL OQA' GROUP BY batch_no");
                $pts_si_sql = mysqli_query($conn, "SELECT * FROM prod_module WHERE batch_no = '$batch' AND Stations = 'PTS/ SI RUN' GROUP BY batch_no");
                $crating_sql = mysqli_query($conn, "SELECT * FROM prod_module WHERE batch_no = '$batch' AND Stations = 'CRATING' GROUP BY batch_no");
                $shipped_sql = mysqli_query($conn, "SELECT * FROM prod_module WHERE batch_no = '$batch' AND Stations = 'SHIPPED' GROUP BY batch_no");

                /*$other_count = mysqli_num_rows($fvi_machine_sql) + mysqli_num_rows($final_oqa_sql) + mysqli_num_rows($pts_si_sql) + mysqli_num_rows($crating_sql) + mysqli_num_rows($shipped_sql);
              $fi_other_count = mysqli_num_rows($fvi_machine_sql)  + mysqli_num_rows($final_oqa_sql) + mysqli_num_rows($pts_si_sql) + mysqli_num_rows($crating_sql) + mysqli_num_rows($shipped_sql); 

              $rcol_sql = mysqli_query($conn, "SELECT * FROM prod_module WHERE batch_no = '$batch' AND work_station='WS9' GROUP BY work_station ");
              $lcol_sql = mysqli_query($conn, "SELECT * FROM prod_module WHERE batch_no = '$batch' AND work_station='WS10' GROUP BY work_station ");

              if (mysqli_num_rows($lcol_sql) > 0) {
                $monitor = "Left";
              } else if (mysqli_num_rows($rcol_sql) > 0) {
                $monitor = "Right";
              } else {
                $monitor = " ";
              }*/
                //Echo" <tr>";

                if ($option > 0) {
                  echo "<tr style='background-color:#a6a6a6'>";
                } else {
                  echo " <tr>";
                }
                echo "<td>$batch</td>";
                echo "<td style='color:black'>$date_received</td>";
                echo "<td style='color:black'>$build_end</td>";
                echo "<td style='color:black'>$date_shipped</td>";
                echo "<td style='color:black'>$age</td>";
                echo "<td style='color:blue' >$ave_format%</td>";
                $variables = array(
                  "fa_percentage" => $fa_percentage,
                  "txp_percentage" => $txp_percentage,
                  "cda_percentage" => $cda_percentage,
                  "ac_percentage" => $ac_percentage,
                  "tsl_percentage" => $tsl_percentage,
                  "cdm_percentage" => $cdm_percentage,
                  "fc_percentage" => $fc_percentage,
                  "mtp_percentage" => $mtp_percentage,
                  "ion_percentage" => $ion_percentage,
                  "flip_percentage" => $flip_percentage,
                  "fi_percentage" => $fi_percentage,
                  "ft_percentage" => $ft_percentage
                );

                foreach ($variables as $variable_name => $percentage) {
                  if ($percentage == 100) {
                    echo "<td style='color:black; background-color:#90ee90;'>$percentage</td>";
                  } else {
                    echo "<td style='color:black;'>$percentage</td>";
                  }
                }
              }
              ?>
              <!--
<tr style="background-color:yellow">
<td >OVERALL: </td>
<td> </td>
<td> </td>
<td> </td>
<td> </td>
<td> </td>
<td> </td>
<td> </td>
<td> </td>
<td> </td>
<td> </td>
<td> </td>
<td> </td>
<td> </td>
<td> </td>
<td> </td>
<td> </td>
</tr>
-->
            <?php } ?>

    </div>

</body>
<script>
  document.getElementById('Export').addEventListener('click', function() {
    var table2excel = new Table2Excel();
    table2excel.export(document.querySelectorAll("#efficiencysummary"));
  });
</script>
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
  var navbarDropdownElement = document.querySelector('#navbarDropdownToggle');
  var imageDropdownElement = document.querySelector('#imageDropdownToggle');
  var headerElement = document.getElementById('stickyHeader');

  navbarDropdownElement.addEventListener('show.bs.dropdown', function() {
    headerElement.classList.remove('sticky-top');
  });

  navbarDropdownElement.addEventListener('hide.bs.dropdown', function() {
    headerElement.classList.add('sticky-top');
  });

  imageDropdownElement.addEventListener('show.bs.dropdown', function() {
    headerElement.classList.remove('sticky-top');
  });

  imageDropdownElement.addEventListener('hide.bs.dropdown', function() {
    headerElement.classList.add('sticky-top');
  });
</script>

</html>