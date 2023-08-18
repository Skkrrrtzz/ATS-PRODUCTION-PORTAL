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
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Cable DTR Summary</title>
</head>


<body>

  <center>
    <div class="mt-1">
      <form method="POST">
        <label for="datefrom"><b>FROM:</b></label>
        <input align="center" type="date" name="datefrom" value="">
        <label for="dateto"><b>TO:</b></label>
        <input type="date" name="dateto" value="">
        <button class="btn btn-secondary btn-sm mb-1" type="submit" id="filter" name="filter">View</button>
        <button class="btn btn-secondary btn-sm mb-1" onclick='myApp.printTable()'>Print/Save</button>
        <button class="btn btn-success btn-sm mb-1" id="btnExport" onclick="javascript:xport.toCSV('dtrsummary');">Export to Excel</button>
        <?php
        if (isset($_POST['filter'])) {
          $datefrom = $_POST['datefrom'];
          $dateto = $_POST['dateto'];
          $Dept = "Cable Assy"; ?>


          <tr>
            <h4 class="fw-bold" style="background-color: #ADD8E6">
              DTR SUMMARY PER EMPLOYEE
            </h4>
            <?php echo "FROM: $datefrom TO: $dateto"; ?>
          </tr>

          <div class="text-center bg-secondary bg-opacity-50 fw-bold">
            <div class="row mx-0">
              <div class="col-md-4 text-start">
                Name
              </div>
              <div class="col-md-4 ms-auto">
                Code
              </div>
              <div class="col text-end">
                Labor Type
              </div>
            </div>
          </div>
          <div class="table-responsive">
            <table class="table table-bordered table-sm w-50 table-striped">
              <thead>
                <tr>
                  <th></th>
                  <th class="bg-primary bg-opacity-50">200<br>MWC</th>
                  <th class="bg-primary bg-opacity-50">201<br>AWC</th>
                  <th class="bg-primary bg-opacity-50">202<br>WS</th>
                  <th class="bg-primary bg-opacity-50">203<br>MTEC</th>
                  <th class="bg-primary bg-opacity-50">204<br>ATEC</th>
                  <th class="bg-primary bg-opacity-50">205<br>SO</th>
                  <th class="bg-primary bg-opacity-50">206<br>MO</th>
                  <th class="bg-primary bg-opacity-50">207<br>WH/FA</th>
                  <th class="bg-primary bg-opacity-50">208<br>HS</th>
                  <th class="bg-primary bg-opacity-50">209<br>LABEL</th>
                  <th class="bg-primary bg-opacity-50">210<br>TEST</th>
                  <th class="bg-primary bg-opacity-50">211<br>VI</th>
                  <th class="bg-primary bg-opacity-50">212<br>OQA</th>
                  <th class="bg-primary bg-opacity-50">213<br>FGT</th>
                  <th class="bg-primary bg-opacity-50">214<br>OBQ</th>
                  <th class="bg-primary bg-opacity-50">215<br>PACK</th>
                  <th class="bg-primary bg-opacity-50">216<br>TC</th>
                  <th class="bg-danger bg-opacity-50">301<br>RW</th>
                  <th class="bg-danger bg-opacity-50">302<br>PRC</th>
                  <th class="bg-danger bg-opacity-50">303<br>BRK</th>
                  <th class="bg-danger bg-opacity-50">304<br>WP</th>
                  <th class="bg-danger bg-opacity-50">305<br>DG</th>
                  <th class="bg-danger bg-opacity-50">306<br>DV</th>
                  <th class="bg-danger bg-opacity-50">307<br>TMS</th>
                  <th class="bg-danger bg-opacity-50">308<br>FD</th>
                  <th class="bg-danger bg-opacity-50">309<br>PN</th>
                  <th class="bg-danger bg-opacity-50">310<br>5s</th>
                  <th class="bg-danger bg-opacity-50">311<br>SP</th>
                  <th class="bg-danger bg-opacity-50">312<br>IT</th>
                  <th class="bg-danger bg-opacity-50">313<br>MS</th>
                  <th class="bg-warning">RDL</th>
                  <th class="bg-warning">RIDL</th>
                  <th class="bg-warning">OTDL</th>
                  <th class="bg-warning">OTIDL</th>
                  <th style="background-color:#CD7F32">TOTAL</th>
                </tr>
              </thead>

            <?php
            $stmt = $conn->prepare("SELECT Name,SUM(Reg_DL) AS DL,SUM(Reg_IDL) AS IDL, SUM(OT_DL) AS ODL,SUM(OT_IDL) AS OIDL,SUM(Code200) AS C200,SUM(Code201) AS C201, SUM(Code202) AS C202,SUM(Code203) AS C203,SUM(Code204) AS C204,SUM(Code205) AS C205,SUM(Code206) AS C206,SUM(Code207) AS C207,SUM(Code208) AS C208,SUM(Code209) AS C209,SUM(Code210) AS C210,SUM(Code211) AS C211,SUM(Code212) AS C212,SUM(Code213) AS C213,SUM(Code214) AS C214,SUM(Code215) AS C215,SUM(Code216) AS C216,SUM(Code301) AS C301,SUM(Code302) AS C302,SUM(Code303) AS C303,SUM(Code304) AS C304,SUM(Code305) AS C305,SUM(Code306) AS C306,SUM(Code307) AS C307,SUM(Code308) AS C308,SUM(Code309) AS C309,SUM(Code310) AS C310,SUM(Code311) AS C311, SUM(Code312) AS C312,SUM(Code313) AS C313,(SUM(Total_DL) + SUM(Total_IDL)) / 60 AS Total FROM dtr WHERE Department = ? AND Name != '' AND DATE BETWEEN ? AND ? GROUP BY Name ORDER BY Name");

            $stmt->bind_param("sss", $Dept, $datefrom, $dateto);
            $stmt->execute();
            $result = $stmt->get_result();

            $includedCodes = [200, 201, 202, 203, 204, 205, 206, 207, 208, 209, 210, 211, 212, 213, 214, 215, 216, 301, 302, 303, 304, 305, 306, 307, 308, 309, 310, 311, 312, 313];

            $stmt = $conn->prepare("SELECT DATE, " . implode(',', array_map(function ($code) {
              return "SUM(Code$code) / 60 as T$code";
            }, $includedCodes)) . ", SUM(Reg_DL) / 60 as T_RDL, SUM(Reg_IDL) / 60 as T_RIDL, SUM(OT_DL) / 60 as T_ODL, SUM(OT_IDL) / 60 as T_OIDL,(SUM(Total_DL) + SUM(Total_IDL)) / 60 as G_Total FROM dtr WHERE Department = ? AND DATE BETWEEN ? AND ? GROUP BY DATE");

            $stmt->bind_param("sss", $Dept, $datefrom, $dateto);
            $stmt->execute();
            $Totalquery = $stmt->get_result();

            $stmt = $conn->prepare("SELECT DATE, " . implode(',', array_map(function ($code) {
              return "SUM(Code$code) as m$code";
            }, $includedCodes)) . ", SUM(Reg_DL) as m_RDL, SUM(Reg_IDL) as m_RIDL, SUM(OT_DL) as m_ODL, SUM(OT_IDL) as m_OIDL, (SUM(Total_DL) + SUM(Total_IDL)) as m_Total FROM dtr WHERE Department = ? AND DATE BETWEEN ? AND ? GROUP BY DATE");

            $stmt->bind_param("sss", $Dept, $datefrom, $dateto);
            $stmt->execute();
            $Totalmins = $stmt->get_result();


            $count = mysqli_num_rows($result);

            if ($count == "0") {
              echo "No data found from $datefrom to $dateto!";
            } else {
              while ($row = mysqli_fetch_array($result)) {
                $Name = $row['Name'];
                $Code200 = $row['C200'];
                $Code201 = $row['C201'];
                $Code202 = $row['C202'];
                $Code203 = $row['C203'];
                $Code204 = $row['C204'];
                $Code205 = $row['C205'];
                $Code206 = $row['C206'];
                $Code207 = $row['C207'];
                $Code208 = $row['C208'];
                $Code209 = $row['C209'];
                $Code210 = $row['C210'];
                $Code211 = $row['C211'];
                $Code212 = $row['C212'];
                $Code213 = $row['C213'];
                $Code214 = $row['C214'];
                $Code215 = $row['C215'];
                $Code216 = $row['C216'];
                $Code301 = $row['C301'];
                $Code302 = $row['C302'];
                $Code303 = $row['C303'];
                $Code304 = $row['C304'];
                $Code305 = $row['C305'];
                $Code306 = $row['C306'];
                $Code307 = $row['C307'];
                $Code308 = $row['C308'];
                $Code309 = $row['C309'];
                $Code310 = $row['C310'];
                $Code311 = $row['C311'];
                $Code312 = $row['C312'];
                $Code313 = $row['C313'];
                $RegDL = $row['DL'];
                $RegIDL = $row['IDL'];
                $OT_DL = $row['ODL'];
                $OT_IDL = $row['OIDL'];
                $Total = $row['Total'];


                $RegDLformat = number_format($RegDL / 60, 1);
                $RegIDLformat = number_format($RegIDL / 60, 1);
                $OT_DLformat = number_format($OT_DL / 60, 1);
                $OT_IDLformat = number_format($OT_IDL / 60, 1);
                $Totalformat = number_format($Total, 1);



                echo "<tr>";
                echo "<td>$Name</td>";
                echo "<td>$Code200</td>";
                echo "<td>$Code201</td>";
                echo "<td>$Code202</td>";
                echo "<td>$Code203</td>";
                echo "<td>$Code204</td>";
                echo "<td>$Code205</td>";
                echo "<td>$Code206</td>";
                echo "<td>$Code207</td>";
                echo "<td>$Code208</td>";
                echo "<td>$Code209</td>";
                echo "<td>$Code210</td>";
                echo "<td>$Code211</td>";
                echo "<td>$Code212</td>";
                echo "<td>$Code213</td>";
                echo "<td>$Code214</td>";
                echo "<td>$Code215</td>";
                echo "<td>$Code216</td>";
                echo "<td>$Code301</td>";
                echo "<td>$Code302</td>";
                echo "<td>$Code303</td>";
                echo "<td>$Code304</td>";
                echo "<td>$Code305</td>";
                echo "<td>$Code306</td>";
                echo "<td>$Code307</td>";
                echo "<td>$Code308</td>";
                echo "<td>$Code309</td>";
                echo "<td>$Code310</td>";
                echo "<td>$Code311</td>";
                echo "<td>$Code312</td>";
                echo "<td>$Code313</td>";


                echo "<td>$RegDLformat</td>";
                echo "<td>$RegIDLformat</td>";
                echo "<td>$OT_DLformat</td>";
                echo "<td>$OT_IDLformat</td>";
                echo "<td>$Totalformat</td>";
              }

              echo "</tr>";
              echo "</thead>";
              echo "</tbody>";
              echo "<tfoot class='text-primary'>
<tr>
    <td style='text-align:right'>TOTAL MINUTES:</td>";
              while ($row = mysqli_fetch_array($Totalmins)) {
                $m200 = $row['m200'];
                $m201 = $row['m201'];
                $m202 = $row['m202'];
                $m203 = $row['m203'];
                $m204 = $row['m204'];
                $m205 = $row['m205'];
                $m206 = $row['m206'];
                $m207 = $row['m207'];
                $m208 = $row['m208'];
                $m209 = $row['m209'];
                $m210 = $row['m210'];
                $m211 = $row['m211'];
                $m212 = $row['m212'];
                $m213 = $row['m213'];
                $m214 = $row['m214'];
                $m215 = $row['m215'];
                $m216 = $row['m216'];
                $m301 = $row['m301'];
                $m302 = $row['m302'];
                $m303 = $row['m303'];
                $m304 = $row['m304'];
                $m305 = $row['m305'];
                $m306 = $row['m306'];
                $m307 = $row['m307'];
                $m308 = $row['m308'];
                $m309 = $row['m309'];
                $m310 = $row['m310'];
                $m311 = $row['m311'];
                $m312 = $row['m312'];
                $m313 = $row['m313'];

                $m_RDL = $row['m_RDL'];
                $m_RIDL = $row['m_RIDL'];
                $m_ODL = $row['m_ODL'];
                $m_OIDL = $row['m_OIDL'];
                $m_ODL = $row['m_ODL'];
                $m_Total = $row['m_Total'];


                $m200format = number_format($m200);
                $m201format = number_format($m201);
                $m202format = number_format($m202);
                $m203format = number_format($m203);
                $m204format = number_format($m204);
                $m205format = number_format($m205);
                $m206format = number_format($m206);
                $m207format = number_format($m207);
                $m208format = number_format($m208);
                $m209format = number_format($m209);
                $m210format = number_format($m210);
                $m211format = number_format($m211);
                $m212format = number_format($m212);
                $m213format = number_format($m213);
                $m214format = number_format($m214);
                $m215format = number_format($m215);
                $m216format = number_format($m216);
                $m301format = number_format($m301);
                $m302format = number_format($m302);
                $m303format = number_format($m303);
                $m304format = number_format($m304);
                $m305format = number_format($m305);
                $m306format = number_format($m306);
                $m307format = number_format($m307);
                $m308format = number_format($m308);
                $m309format = number_format($m309);
                $m310format = number_format($m310);
                $m311format = number_format($m311);
                $m312format = number_format($m312);
                $m313format = number_format($m313);


                $m_RDLformat = number_format($m_RDL);
                $m_RIDLformat = number_format($m_RIDL);
                $m_ODLformat = number_format($m_ODL);
                $m_OIDLformat = number_format($m_OIDL);
                $m_Totalformat = number_format($m_Total);

                echo "<td>$m200format</td>";
                echo "<td>$m201format</td>";
                echo "<td>$m202format</td>";
                echo "<td>$m203format</td>";
                echo "<td>$m204format</td>";
                echo "<td>$m205format</td>";
                echo "<td>$m206format</td>";
                echo "<td>$m207format</td>";
                echo "<td>$m208format</td>";
                echo "<td>$m209format</td>";
                echo "<td>$m210format</td>";
                echo "<td>$m211format</td>";
                echo "<td>$m212format</td>";
                echo "<td>$m213format</td>";
                echo "<td>$m214format</td>";
                echo "<td>$m215format</td>";
                echo "<td>$m216format</td>";
                echo "<td>$m301format</td>";
                echo "<td>$m302format</td>";
                echo "<td>$m303format</td>";
                echo "<td>$m304format</td>";
                echo "<td>$m305format</td>";
                echo "<td>$m306format</td>";
                echo "<td>$m307format</td>";
                echo "<td>$m308format</td>";
                echo "<td>$m309format</td>";
                echo "<td>$m310format</td>";
                echo "<td>$m311format</td>";
                echo "<td>$m312format</td>";
                echo "<td>$m313format</td>";

                echo "<td>$m_RDLformat</td>";
                echo "<td>$m_RIDLformat</td>";
                echo "<td>$m_ODLformat</td>";
                echo "<td>$m_OIDLformat</td>";
                echo "<td>$m_Totalformat</td>";
              }
              echo "</tr>";

              echo " <tr>";
              echo "  <td style='text-align:right'>TOTAL HOURS:</td>";
              while ($row = mysqli_fetch_array($Totalquery)) {
                $T200 = $row['T200'];
                $T201 = $row['T201'];
                $T202 = $row['T202'];
                $T203 = $row['T203'];
                $T204 = $row['T204'];
                $T205 = $row['T205'];
                $T206 = $row['T206'];
                $T207 = $row['T207'];
                $T208 = $row['T208'];
                $T209 = $row['T209'];
                $T210 = $row['T210'];
                $T211 = $row['T211'];
                $T212 = $row['T212'];
                $T213 = $row['T213'];
                $T214 = $row['T214'];
                $T215 = $row['T215'];
                $T216 = $row['T216'];
                $T301 = $row['T301'];
                $T302 = $row['T302'];
                $T303 = $row['T303'];
                $T304 = $row['T304'];
                $T305 = $row['T305'];
                $T306 = $row['T306'];
                $T307 = $row['T307'];
                $T308 = $row['T308'];
                $T309 = $row['T309'];
                $T310 = $row['T310'];
                $T311 = $row['T311'];
                $T312 = $row['T312'];
                $T313 = $row['T313'];


                $T_RDL = $row['T_RDL'];
                $T_RIDL = $row['T_RIDL'];
                $T_ODL = $row['T_ODL'];
                $T_OIDL = $row['T_OIDL'];
                $T_ODL = $row['T_ODL'];
                $G_Total = $row['G_Total'];


                $T200format = number_format($T200, 1);
                $T201format = number_format($T201, 1);
                $T202format = number_format($T202, 1);
                $T203format = number_format($T203, 1);
                $T204format = number_format($T204, 1);
                $T205format = number_format($T205, 1);
                $T206format = number_format($T206, 1);
                $T207format = number_format($T207, 1);
                $T208format = number_format($T208, 1);
                $T209format = number_format($T209, 1);
                $T210format = number_format($T210, 1);
                $T211format = number_format($T211, 1);
                $T212format = number_format($T212, 1);
                $T213format = number_format($T213, 1);
                $T214format = number_format($T214, 1);
                $T215format = number_format($T215, 1);
                $T216format = number_format($T216, 1);
                $T301format = number_format($T301, 1);
                $T302format = number_format($T302, 1);
                $T303format = number_format($T303, 1);
                $T304format = number_format($T304, 1);
                $T305format = number_format($T305, 1);
                $T306format = number_format($T306, 1);
                $T307format = number_format($T307, 1);
                $T308format = number_format($T308, 1);
                $T309format = number_format($T309, 1);
                $T310format = number_format($T310, 1);
                $T311format = number_format($T311, 1);
                $T312format = number_format($T312, 1);
                $T313format = number_format($T313, 1);


                $T_RDLformat = number_format($T_RDL, 1);
                $T_RIDLformat = number_format($T_RIDL, 1);
                $T_ODLformat = number_format($T_ODL, 1);
                $T_OIDLformat = number_format($T_OIDL, 1);
                $G_Totalformat = number_format($G_Total, 1);


                echo "<td>$T200format</td>";
                echo "<td>$T201format</td>";
                echo "<td>$T202format</td>";
                echo "<td>$T203format</td>";
                echo "<td>$T204format</td>";
                echo "<td>$T205format</td>";
                echo "<td>$T206format</td>";
                echo "<td>$T207format</td>";
                echo "<td>$T208format</td>";
                echo "<td>$T209format</td>";
                echo "<td>$T210format</td>";
                echo "<td>$T211format</td>";
                echo "<td>$T212format</td>";
                echo "<td>$T213format</td>";
                echo "<td>$T214format</td>";
                echo "<td>$T215format</td>";
                echo "<td>$T216format</td>";
                echo "<td>$T301format</td>";
                echo "<td>$T302format</td>";
                echo "<td>$T303format</td>";
                echo "<td>$T304format</td>";
                echo "<td>$T305format</td>";
                echo "<td>$T306format</td>";
                echo "<td>$T307format</td>";
                echo "<td>$T308format</td>";
                echo "<td>$T309format</td>";
                echo "<td>$T310format</td>";
                echo "<td>$T311format</td>";
                echo "<td>$T312format</td>";
                echo "<td>$T313format</td>";


                echo "<td>$T_RDLformat</td>";
                echo "<td>$T_RIDLformat</td>";
                echo "<td>$T_ODLformat</td>";
                echo "<td>$T_OIDLformat</td>";
                echo "<td>$G_Totalformat</td>";
              }
              echo "</tr>";
              echo "</tfoot>";
              echo " </table> </div>";

              echo "<div class='d-flex row mx-0'>
<div class='column' style='background-color:#ddd;'>
  <p>
  200-MANUAL CUTTING OF WIRES<BR>
  201-AUTO WIRE CUTTING<br>
  202-STRIPPING<br>
  203-MANUAL CRIMPING
  204-AUTO CRIMPING<br>
  
  </p>
</div>
<div class='column' style='background-color:#ddd;'>
  <p>
  
  205-ASSEMBLY<br>
  206-LABELLING<br>
  207-FVI/TESTING<br>
  208-SOLDERING
  
</p>
</div>
<div class='column' style='background-color:#ddd;'>
  <p>
  301 - REWORK / RETEST<br>
  302 - PARTS RECEIVED CHECKING<br>
  303 - BREAKTIME<br>
  304 - WAIT PART <br>
  305 - DOCUMENT GENERATION (QIF)<br>
  306 - DRAWING / BOM / MPI / MTI VERIFICATION
  </p>
</div>
<div class='column' style='background-color:#ddd;'>
  <p>
  307 - TRAINING/MEETING/SEMINAR <br>
  308 - FACILITY DOWNTIME (INCLUDES IT)<br>
  309 - TRIP TO CLINIC / HR / FINANCE<br>
  310 - 5S HOUSEKEEPING<br>
  311 - SUPPORT TO OTHER GROUPS/SPECIAL PROJECT<br>
  312 - INVENTORY TAKING
  313 - MACHINE SET UP
  
  </p>
</div>
</div>";
            }
          }
            ?>
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

<script>
  var myApp = new function() {
    this.printTable = function() {
      var tab = document.getElementById('dtrsummary');
      var win = window.open('', '', 'height=700,width=700');
      win.document.write(tab.outerHTML);
      win.document.close();
      win.print();
    }
  }
</script>

</html>