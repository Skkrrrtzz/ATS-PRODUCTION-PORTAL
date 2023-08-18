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
  <title>Cable Actual Cycle Time</title>
</head>

<body>
  <center>
    <div class="fw-bold pt-1">
      <form method="POST">
        SEARCH: <input align='Left' type='text' id='myInputname' name='part_no' placeholder='Search Part Number' title='Type in the parts'>
        <button class="btn btn-secondary btn-sm" type="submit" id="filter" name="filter">View</button>
        <div class="table-responsive">
          <table class="table table-bordered table-striped table-sm w-75" id="parts">
            <thead class="table-dark">
              <tr>
                <th>PART NO.</th>
                <th>STATION</th>
                <th>DURATION</th>
                <th>QTY</th>
                <th>CYCLE TIME</th>
                <th>AVERAGE(DURATION/QTY)</th>
              </tr>
            </thead>

            <?php
            if (isset($_POST['filter'])) {
              $part_no = $_POST['part_no'];
              $wosql = mysqli_query($conn, "SELECT sum(Duration) as Duration, sum(Qty_Make) as Qty_Make, AVG(Duration/Qty_Make) as Average,Stations,Part_No,remarks,Code,Station_No,Labor_Type FROM dtr WHERE Part_No='$part_no'  AND Qty_Make >0 AND wo_status ='IN-PROCESS' GROUP BY Stations  ORDER BY Stations desc");
              while ($row = mysqli_fetch_array($wosql)) {

            ?>
                <tbody>
                  <tr class="bg-light">
                    <td><?php echo $row['Part_No']; ?></td>
                    <td><?php echo $row['Stations']; ?></td>
                    <td><?php echo $row['Duration']; ?></td>
                    <td><?php echo $row['Qty_Make']; ?></td>
                    <td><?php echo number_format($row['Duration'] / $row['Qty_Make'], 2); ?></td>
                    <td><?php echo number_format($row['Average'], 2); ?></td>
                  </tr>
                </tbody>

              <?php } ?>
          </table>
          <table class="table table-bordered table-striped table-sm" id="parts_data">
            <thead class="table-dark text-center">
              <tr>
                <th colspan="14">ACTUAL CYCLE TIME DATA</th>
              </tr>
            </thead>
            <thead class="table-secondary text-center">
              <tr>
                <th>WO ID</th>
                <th>OPERATOR</th>
                <th>STATION</th>
                <th>NO.</th>
                <th>PROD NO.</th>
                <th>PART NO.</th>
                <th>ACTIVITY</th>
                <th>STARTED</th>
                <th>ENDED</th>
                <th>MINS</th>
                <th>QTY</th>
                <th>CYCLE TIME</th>
                <th>REMARKS</th>
              </tr>
            </thead>

            <?php
              $wosql_data = mysqli_query($conn, "SELECT * FROM dtr WHERE Part_No='$part_no'  AND Qty_Make >0 AND wo_status ='IN-PROCESS'   ORDER BY Stations desc");
              while ($row = mysqli_fetch_array($wosql_data)) {

            ?>
              <tr class="text-center fw-bold">
                <td><?php echo $row['wo_id']; ?></td>
                <td><?php echo $row['Name']; ?></td>
                <td><?php echo $row['Stations']; ?></td>
                <td><?php echo $row['Station_No']; ?></td>
                <td><?php echo $row['Prod_Order_No']; ?></td>
                <td><?php echo $row['Part_No']; ?></td>
                <td><?php echo $row['Code']; ?></td>
                <td><?php echo $row['Act_Start']; ?></td>
                <td><?php echo $row['Act_End']; ?></td>
                <td><?php echo $row['Duration']; ?></td>
                <td><?php echo $row['Qty_Make']; ?></td>
                <td><?php echo number_format($row['Duration'] / $row['Qty_Make'], 2); ?></td>
                <td><?php echo $row['remarks']; ?></td>
              </tr>
          <?php }
            } ?>
          </table>
        </div>
      </form>
    </div>
  </center>

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