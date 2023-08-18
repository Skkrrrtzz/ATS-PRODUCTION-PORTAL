<?php include '../ATS_Prod_Header.php'; ?>
<?php include '../PROD_navbar.php'; ?>
<?php
//session_start();

if (!isset($_SESSION['emp_id'])) {
  header('Location:ATS_Prod_Home.php');
  exit();
}

$updated_by = $_SESSION['name'];
$dept = $_SESSION['department'];
$emp_id = $_SESSION['emp_id'];
$quantity = "";
$part_no = "";
$prod_no = "";
$description = "";
$for_station = "";
$Name = "";
$Labor_Type = "";
$Act_Start = "";
$Act_End = "";
$id = "";
$station = "";
$Code = "";
$Duration = "";

?>
<?php
if (isset($_GET['edit'])) {
  $id = $_GET['edit'];
  $update = true;
  $sqlquery = mysqli_query($conn, "SELECT * FROM dtr WHERE ID= $id");

  $count = mysqli_num_rows($sqlquery);
  if ($count == "0") {
    echo "No data found!";
  } else {

    $row = mysqli_fetch_array($sqlquery);
    $Name = $row['Name'];
    $Date = $row['DATE'];
    $Emp_ID = $row['Emp_ID'];
    $Department = $row['Department'];
    $prod_no = $row['Prod_Order_No'];
    //$for_station=$row['Module'];
    $station = $row['Stations'];
    $part_no = $row['Part_No'];
    $wo_quantity = $row['Qty_Make'];
    $sample_qty = $row['sample_qty'];
    $Code = $row['Code'];
    $Act_Start = $row['Act_Start'];
    $Act_End = $row['Act_End'];
    $Labor_Type = $row['Labor_Type'];
    $wo_id = $row['wo_id'];
    $id = $row['ID'];
    $remarks = $row['remarks'];
    $Duration = $row['Duration'];
  }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Parts History</title>

</head>


<body>
  <center>
    <form method="POST">
      SEARCH: <input align='Left' type='text' id='myInputname' name='part_no' placeholder='Search Part Number' title='Type in the parts'>
      <button class="btn btn-secondary btn-sm mb-1" id="filter" name="filter">View</button>
      <div class="table-responsive">
        <table class="table table-striped table-bordered" id="parts">
          <thead class="table-dark">
            <tr>
              <th>WO ID</th>
              <th>OPERATOR</th>
              <th>STATION</th>
              <th>NO.</th>
              <th>PROD NO.</th>
              <th>PART NO.</th>
              <th>QTY</th>
              <th>ACTIVITY</th>
              <th>STARTED</th>
              <th>ENDED</th>
              <th>LABOR</th>
              <th>MINS</th>
              <th>REMARKS</th>
            </tr>
          </thead>
          <?php

          if (isset($_POST['filter'])) {
            $part_no = $_POST['part_no'];
            $wosql = mysqli_query($conn, "SELECT * FROM dtr WHERE Part_No='$part_no' AND Act_Start !='' ORDER BY wo_id desc, Act_Start Asc ");
            while ($row = mysqli_fetch_array($wosql)) {

          ?><tbody>
                <tr style="text-align:center">
                  <td><?php echo $row['wo_id']; ?></td>
                  <td><?php echo $row['Name']; ?></td>
                  <td><?php echo $row['Stations']; ?></td>
                  <td><?php echo $row['Station_No']; ?></td>
                  <td><?php echo $row['Prod_Order_No']; ?></td>
                  <td><?php echo $row['Part_No']; ?></td>
                  <td><?php echo $row['Qty_Make']; ?></td>
                  <td><?php echo $row['Code']; ?></td>
                  <td><?php echo $row['Act_Start']; ?></td>
                  <td><?php echo $row['Act_End']; ?></td>
                  <td><?php echo $row['Labor_Type']; ?></td>
                  <td><?php echo $row['Duration']; ?></td>
                  <td><?php echo $row['remarks']; ?></td>
                </tr>
              </tbody>

          <?php }
          } ?>

        </table>


        <table class="table table-striped table-bordered table-sm text-center">
          <thead class="table-dark">
            <tr>
              <th colspan="9">ACTIVITY CODE:</th>
            </tr>
          </thead>
          <tr>
            <td style="text-align:left;font-size:10px">101 - MH Parts Kitting</td>
            <td style="text-align:left;font-size:10px">102 - MH Breaktime</td>
            <td style="text-align:left;font-size:10px">103 - M Training/Meeting/Seminar</td>
            <td style="text-align:left;font-size:10px">104 - MH Personal Needs/Trip to Clinic/HR/Finance </td>
            <td style="text-align:left;font-size:10px">105 - MH 5's Housekeeping</td>
            <td style="text-align:left;font-size:10px" colspan="">106 - MH Support to other group/Special Project</td>
            <td style="text-align:left;font-size:10px" colspan="">107 - MH Inventory Taking</td>
            <td style="text-align:left;font-size:10px" colspan="">108 - NG Transaction</td>
            <td style="text-align:left;font-size:10px" colspan="">109 - Problem Log</td>
          </tr>
          <tr>
            <td style="text-align:left;font-size:10px">200 - Manual Wire Cutting</td>
            <td style="text-align:left;font-size:10px">201 - Auto Wire Cutting</td>
            <td style="text-align:left;font-size:10px">202 - Wire Stripping/Tube Cutting</td>
            <td style="text-align:left;font-size:10px">203 - Manual Terminal Crimping</td>
            <td style="text-align:left;font-size:10px">204 - Auto Terminal Crimping</td>
            <td style="text-align:left;font-size:10px">205 - Soldering</td>
            <td style="text-align:left;font-size:10px">206 - Molding</td>
            <td style="text-align:left;font-size:10px">207 - Wire Harnessing/Final Assembly</td>
          </tr>
          <tr>
            <td style="text-align:left;font-size:10px">209 - Labelling</td>
            <td style="text-align:left;font-size:10px">210 - Testing</td>
            <td style="text-align:left;font-size:10px">211 - Visual Inspection</td>
            <td style="text-align:left;font-size:10px">1004 - OQA</td>
            <td style="text-align:left;font-size:10px">213 - FG Transaction</td>
            <td style="text-align:left;font-size:10px"> 0000 - OBQ</td>
            <td style="text-align:left;font-size:10px" colspan="2">215 - Packaging</td>
          </tr>
          <tr>
            <td></td>
            <td style="text-align:left;font-size:10px">301 -Rework / Retest</td>
            <td style="text-align:left;font-size:10px">302 - Parts received checking</td>
            <td style="text-align:left;font-size:10px">303 - Breaktime</td>
            <td style="text-align:left;font-size:10px">304 - Wait part </td>
            <td style="text-align:left;font-size:10px">305 - Doc. Gen'tion(QIF)</td>
            <td style="text-align:left;font-size:10px" colspan="2">306 - Drawing/BOM/MPI/MTI Verification</td>
          </tr>
          <tr>
            <td></td>
            <td style="text-align:left;font-size:10px">307 - Training/Meeting/Seminar</td>
            <td style="text-align:left;font-size:10px">308 - Facility Downtime</td>
            <td style="text-align:left;font-size:10px">309 - Personal Needs/Trip to clinic/HR/Finance</td>
            <td style="text-align:left;font-size:10px">310 - 5's/Housekeeping </td>
            <td style="text-align:left;font-size:10px">311 - Support to other group</td>
            <td style="text-align:left;font-size:10px" colspan="2">312 - Inventory Taking</td>
          </tr>
        </table>
      </div>

      </tr>

      <script>
        function myFunction() {
          var input, filter, table, tr, td, i, txtValue;
          input = document.getElementById("myInputname");

          filter = input.value.toUpperCase();
          table = document.getElementById("parts");
          tr = table.getElementsByTagName("tr");

          for (i = 0; i < tr.length; i++) {
            td = tr[i].getElementsByTagName("td")[5];
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

</html>