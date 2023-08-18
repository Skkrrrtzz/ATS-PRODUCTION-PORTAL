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

$wwk = date('W', strtotime('sunday this week')) + 1;
$nextwwk = date('W', strtotime('sunday next week')) + 1;
//echo date("YW", strtotime("2011-01-01")); // gives 201152 too

$ws1_inprocess_sql = mysqli_query($dbconnect, "SELECT ID FROM prod_module WHERE Stations='SUB-ASSY' AND wo_status='IN-PROCESS' AND description!='INDIRECT ACTIVITY' GROUP BY batch_no, description ");
$ws1_idle_sql = mysqli_query($dbconnect, "SELECT ID FROM prod_module WHERE Stations='SUB-ASSY' AND wo_status='IDLE' AND description!='INDIRECT ACTIVITY'");

$ws2_inprocess_sql = mysqli_query($dbconnect, "SELECT ID FROM prod_module WHERE Stations='FVI MODULE' AND wo_status='IN-PROCESS' GROUP BY batch_no, work_station");
$ws2_idle_sql = mysqli_query($dbconnect, "SELECT ID FROM prod_module WHERE Stations='FVI MODULE' AND wo_status='IDLE' AND description!='INDIRECT ACTIVITY'");

$ws3_inprocess_sql = mysqli_query($dbconnect, "SELECT ID FROM prod_module WHERE Stations='OQA FAC' AND wo_status='IN-PROCESS' GROUP BY batch_no, work_station");
$ws3_idle_sql = mysqli_query($dbconnect, "SELECT ID FROM prod_module WHERE Stations='OQA FAC' AND wo_status='IDLE' AND description!='INDIRECT ACTIVITY'");

$ws4_inprocess_sql = mysqli_query($dbconnect, "SELECT ID FROM prod_module WHERE Stations='SUB TEST' AND wo_status='IN-PROCESS' GROUP BY batch_no, work_station");
$ws4_idle_sql = mysqli_query($dbconnect, "SELECT ID FROM prod_module WHERE Stations='SUB TEST' AND wo_status='IDLE' AND description!='INDIRECT ACTIVITY'");

$ws5_inprocess_sql = mysqli_query($dbconnect, "SELECT ID FROM prod_module WHERE Stations IN('MAIN ASSY','PNP INT') AND wo_status='IN-PROCESS' GROUP BY batch_no, work_station");
$ws5_idle_sql = mysqli_query($dbconnect, "SELECT ID FROM prod_module WHERE Stations IN('MAIN ASSY','PNP INT') AND wo_status='IDLE' AND description!='INDIRECT ACTIVITY'");

$ws6_inprocess_sql = mysqli_query($dbconnect, "SELECT ID FROM prod_module WHERE Stations='FINAL TESTS' AND wo_status='IN-PROCESS' GROUP BY batch_no");
$ws6_idle_sql = mysqli_query($dbconnect, "SELECT ID FROM prod_module WHERE Stations='FINAL TESTS' AND wo_status='IDLE' AND description!='INDIRECT ACTIVITY'");

$ws7_inprocess_sql = mysqli_query($dbconnect, "SELECT ID FROM prod_module WHERE Stations='FVI MACS' AND wo_status='IN-PROCESS' GROUP BY batch_no");
$ws7_idle_sql = mysqli_query($dbconnect, "SELECT ID FROM prod_module WHERE Stations='FVI MACS' AND wo_status='IDLE' AND description!='INDIRECT ACTIVITY'");

$ws8_inprocess_sql = mysqli_query($dbconnect, "SELECT ID FROM prod_module WHERE Stations='FINAL OQA' AND wo_status='IN-PROCESS' GROUP BY batch_no");
$ws8_idle_sql = mysqli_query($dbconnect, "SELECT ID FROM prod_module WHERE Stations='FINAL OQA' AND wo_status='IDLE' AND description!='INDIRECT ACTIVITY'");

$ws9_inprocess_sql = mysqli_query($dbconnect, "SELECT ID FROM prod_module WHERE Stations='SI RUN' AND wo_status='IN-PROCESS' AND product='JLP' GROUP BY batch_no");
$ws9_idle_sql = mysqli_query($dbconnect, "SELECT ID FROM prod_module WHERE Stations='SI RUN' AND wo_status='IDLE' AND description!='INDIRECT ACTIVITY' AND product='JLP'");

$ws10_inprocess_sql = mysqli_query($dbconnect, "SELECT ID FROM prod_module WHERE Stations='PTS/BELT TENSION' AND wo_status='IN-PROCESS' GROUP BY batch_no");
$ws10_idle_sql = mysqli_query($dbconnect, "SELECT ID FROM prod_module WHERE Stations='PTS/BELT TENSION' AND wo_status='IDLE' AND description!='INDIRECT ACTIVITY'");

$ws11_inprocess_sql = mysqli_query($dbconnect, "SELECT ID FROM prod_module WHERE Stations='SKIN N COVERS' AND wo_status='IN-PROCESS' GROUP BY batch_no");
$ws11_idle_sql = mysqli_query($dbconnect, "SELECT ID FROM prod_module WHERE Stations='SKIN N COVERS' AND wo_status='IDLE' AND description!='INDIRECT ACTIVITY'");

$ws12_inprocess_sql = mysqli_query($dbconnect, "SELECT ID FROM prod_module WHERE Stations='CRATING' AND wo_status='IN-PROCESS' GROUP BY batch_no");
$ws12_idle_sql = mysqli_query($dbconnect, "SELECT ID FROM prod_module WHERE Stations='CRATING' AND wo_status='IDLE' AND description!='INDIRECT ACTIVITY'");

$ws13_inprocess_sql = mysqli_query($dbconnect, "SELECT ID FROM prod_module WHERE Stations='SHIPPED' GROUP BY batch_no");
$ws13_idle_sql = mysqli_query($dbconnect, "SELECT ID FROM prod_module WHERE Stations='SHIPPED' AND wo_status='IDLE' AND description!='INDIRECT ACTIVITY'");

$ws14_inprocess_sql = mysqli_query($dbconnect, "SELECT sum(Qty) as ws14_inprocess FROM out_po WHERE part_status='MOVED-OUT'");

$ws1_inprocess = mysqli_num_rows($ws1_inprocess_sql);
$ws1_idle = mysqli_num_rows($ws1_idle_sql);

$ws2_inprocess = mysqli_num_rows($ws2_inprocess_sql);
$ws2_idle = mysqli_num_rows($ws2_idle_sql);

$ws3_inprocess = mysqli_num_rows($ws3_inprocess_sql);
$ws3_idle = mysqli_num_rows($ws3_idle_sql);

$ws4_inprocess = mysqli_num_rows($ws4_inprocess_sql);
$ws4_idle = mysqli_num_rows($ws4_idle_sql);

$ws5_inprocess = mysqli_num_rows($ws5_inprocess_sql);
$ws5_idle = mysqli_num_rows($ws5_idle_sql);

$ws6_inprocess = mysqli_num_rows($ws6_inprocess_sql);
$ws6_idle = mysqli_num_rows($ws6_idle_sql);

$ws7_inprocess = mysqli_num_rows($ws7_inprocess_sql);
$ws7_idle = mysqli_num_rows($ws7_idle_sql);

$ws8_inprocess = mysqli_num_rows($ws8_inprocess_sql);
$ws8_idle = mysqli_num_rows($ws8_idle_sql);

$ws9_inprocess = mysqli_num_rows($ws9_inprocess_sql);
$ws9_idle = mysqli_num_rows($ws9_idle_sql);

$ws10_inprocess = mysqli_num_rows($ws10_inprocess_sql);
$ws10_idle = mysqli_num_rows($ws10_idle_sql);

$ws11_inprocess = mysqli_num_rows($ws11_inprocess_sql);
$ws11_idle = mysqli_num_rows($ws11_idle_sql);

$ws12_inprocess = mysqli_num_rows($ws12_inprocess_sql);
$ws12_idle = mysqli_num_rows($ws12_idle_sql);

$ws13_inprocess = mysqli_num_rows($ws13_inprocess_sql);
$ws13_idle = mysqli_num_rows($ws13_idle_sql);

$ws14_inprocess = mysqli_num_rows($ws14_inprocess_sql);




?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta http-equiv="refresh" content="30">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Supervisor Prod Main</title>

  <style>
    .button {
      background-color: #4CAF50;
      /* Green */
      border: none;
      color: white;
      padding: 8px 16px;
      text-align: center;
      text-decoration: none;
      display: inline-block;
      font-size: 20px;
      margin: 1px 1px;
      transition-duration: 0.4s;
      cursor: pointer;
      width: 100%;
    }

    .button1 {
      background-color: white;
      color: black;
      border: 2px solid #4CAF50;
      width: 90%;
      height: 80px;
    }

    .button1:hover {
      background-color: #4CAF50;
      color: white;
    }


    table {
      border-collapse: collapse;
      width: 100%;
      float: center;
    }

    td {
      text-align: center;
      padding: 2px;
      height: 100px;
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

    textarea {
      font-size: 18px;
    }

    .bg-myorange {
      --my-orange: #FF8C00;
      background-color: var(--my-orange);
      opacity: .9;
    }
  </style>


</head>

<body>
  <main class="text-center">
    <table id=working_stations>
      <tr>
        <div class="bg-secondary text-white">
          <h4>LEGEND:
            <span class="badge rounded-pill bg-success opacity-100">IN-PROCESS</span>
            <span class="badge rounded-pill bg-myorange">IDLE</span>
          </h4>
        </div>
      </tr>
      <tr>
        <td colspan="">
          <a class="btn btn-outline-success border-2 btn-lg position-relative text-black pt-20" href="prod_parts_receiving_sub.php" style="width: 70%; height:80px;" role="button">PARTS RECEIVING
            <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-myorange">
              <?php while ($row = mysqli_fetch_array($ws14_inprocess_sql)) {
                echo $row['ws14_inprocess'];
              } ?>
            </span></a>
        </td>
        <td>
          <a class="btn btn-outline-success border-2 btn-lg position-relative text-black pt-2" href="Main_Stations/SUBASSY.php" style="width: 70%; height:80px;" role="button">SUB-ASSEMBLY
            <span class="position-absolute top-0 start-0 translate-middle badge rounded-pill bg-success">
              <?php echo $ws1_inprocess; ?>
            </span>

            <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-myorange">
              <?php
              if ($ws1_idle != 0) {
                echo $ws1_idle;
              } else {
                echo 0;
              } ?>
            </span>
          </a>
        </td>
        <td>
          <a class="btn btn-outline-success border-2 btn-lg position-relative text-black pt-2" href="prod_fvi_module_sub.php" style="width: 70%; height:80px;" role="button">
            FVI-MODULE
            <span class="position-absolute top-0 start-0 translate-middle badge rounded-pill bg-success">
              <?php echo $ws2_inprocess; ?>
            </span>
            <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-myorange">
              <?php
              if ($ws2_idle != 0) {
                echo $ws2_idle;
              } else {
                echo 0;
              } ?>
            </span>
          </a>
        </td>
        <td><a class="btn btn-outline-success border-2 btn-lg position-relative text-black pt-2" href="" style="width: 70%; height:80px;" role="button">
            OQA-FACILITY
            <span class="position-absolute top-0 start-0 translate-middle badge rounded-pill bg-success">
              <?php echo $ws3_inprocess; ?>
            </span>
            <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-myorange">
              <?php
              if ($ws3_idle != 0) {
                echo $ws3_idle;
              } else {
                echo 0;
              } ?>
            </span>
          </a>
        </td>
      </tr>

      <tr>

        <td>
          <a class="btn btn-outline-success border-2 btn-lg position-relative text-black pt-2" href="prod_sub_test_sub.php" style="width: 70%; height:80px;" role="button">SUB-TEST
            <span class="position-absolute top-0 start-0 translate-middle badge rounded-pill bg-success">
              <?php echo $ws4_inprocess; ?>
            </span>
            <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-myorange">
              <?php
              if ($ws4_idle != 0) {
                echo $ws4_idle;
              } else {
                echo 0;
              } ?>
            </span>
          </a>
        </td>
        <td>
          <a class="btn btn-outline-success border-2 btn-lg position-relative text-black pt-2" href="prod_final_integration_sub.php" style="width: 70%; height:80px;" role="button">FINAL INTEGRATION
            <span class="position-absolute top-0 start-0 translate-middle badge rounded-pill bg-success">
              <?php echo $ws5_inprocess; ?>
            </span>
            <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-myorange">
              <?php
              if ($ws5_idle != 0) {
                echo $ws5_idle;
              } else {
                echo 0;
              } ?>
            </span>
          </a>
        </td>
        <td>
          <a class="btn btn-outline-success border-2 btn-lg position-relative text-black pt-2" href="prod_final_test_sub.php" style="width: 70%; height:80px;" role="button">FINAL TEST
            <span class="position-absolute top-0 start-0 translate-middle badge rounded-pill bg-success">
              <?php echo $ws6_inprocess; ?>
            </span>
            <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-myorange">
              <?php
              if ($ws6_idle != 0) {
                echo $ws6_idle;
              } else {
                echo 0;
              } ?>
            </span>
          </a>
        </td>
        <td>
          <a class="btn btn-outline-success border-2 btn-lg position-relative text-black pt-2" href="prod_fvi_machine_sub.php" style="width: 70%; height:80px;" role="button">FVI-MACHINE
            <span class="position-absolute top-0 start-0 translate-middle badge rounded-pill bg-success">
              <?php echo $ws7_inprocess; ?>
            </span>
            <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-myorange">
              <?php
              if ($ws7_idle != 0) {
                echo $ws7_idle;
              } else {
                echo 0;
              } ?>
            </span>
          </a>
        </td>
      </tr>
      <tr>
        <td>
          <a class="btn btn-outline-success border-2 btn-lg position-relative text-black pt-2" href="" style="width: 70%; height:80px;" role="button">
            FINAL OQA
            <span class="position-absolute top-0 start-0 translate-middle badge rounded-pill bg-success">
              <?php echo $ws8_inprocess; ?>
            </span>
            <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-myorange">
              <?php
              if ($ws8_idle != 0) {
                echo $ws8_idle;
              } else {
                echo 0;
              } ?>
            </span>
          </a>
        </td>
        <td>
          <a class="btn btn-outline-success border-2 btn-lg position-relative text-black pt-2" href="prod_sirun_sub.php" style="width: 70%; height:80px;" role="button">SI RUN
            <span class="position-absolute top-0 start-0 translate-middle badge rounded-pill bg-success">
              <?php echo $ws9_inprocess; ?>
            </span>
            <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-myorange">
              <?php
              if ($ws9_idle != 0) {
                echo $ws9_idle;
              } else {
                echo 0;
              } ?>
            </span>
          </a>
        </td>
        <td>
          <a class="btn btn-outline-success border-2 btn-lg position-relative text-black pt-2" href="prod_pts&bt_sub.php" style="width: 70%; height:80px;" role="button">PTS/BELT TENSION
            <span class="position-absolute top-0 start-0 translate-middle badge rounded-pill bg-success">
              <?php echo $ws10_inprocess; ?>
            </span>
            <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-myorange">
              <?php
              if ($ws10_idle != 0) {
                echo $ws10_idle;
              } else {
                echo 0;
              } ?>
            </span>
          </a>
        </td>
        <td>
          <a class="btn btn-outline-success border-2 btn-lg position-relative text-black pt-2" href="prod_snc_sub.php" style="width: 70%; height:80px;" role="button">SKIN N COVERS
            <span class="position-absolute top-0 start-0 translate-middle badge rounded-pill bg-success">
              <?php echo $ws11_inprocess; ?>
            </span>
            <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-myorange">
              <?php
              if ($ws11_idle != 0) {
                echo $ws11_idle;
              } else {
                echo 0;
              } ?>
            </span>
          </a>
        </td>
      </tr>
      <tr>
        <td></td>
        <td>
          <a class="btn btn-outline-success border-2 btn-lg position-relative text-black pt-2" href="prod_crating_sub.php" style="width: 70%; height:80px;" role="button"> CRATING
            <span class="position-absolute top-0 start-0 translate-middle badge rounded-pill bg-success">
              <?php echo $ws12_inprocess; ?>
            </span>
            <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-myorange">
              <?php
              if ($ws12_idle != 0) {
                echo $ws12_idle;
              } else {
                echo 0;
              } ?>
            </span>
          </a>
        </td>
        <td>
          <a class="btn btn-outline-success border-2 btn-lg position-relative text-black pt-2" href="prod_shipped_sub.php" style="width: 70%; height:80px;" role="button">SHIPPED OUT
            <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-myorange">
              <?php echo $ws13_inprocess; ?>
            </span>
            <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-myorange">
              <?php
              if ($ws13_idle != 0) {
                echo $ws13_idle;
              } else {
                echo 0;
              } ?>
            </span>
          </a>
        </td>
      </tr>
    </table>
  </main>
</body>

</html>