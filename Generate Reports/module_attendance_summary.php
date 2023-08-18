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
<html>

<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Production Attendance Summary</title>
  <script src="../assets/js/table2excel.js"></script>
  <script src="../assets/js/html2canvas.min.js"></script>

  <style>
    table {
      border-collapse: collapse;
      width: 85%;
      float: center;
    }


    td {
      text-align: center;
      padding: 8px;

    }

    th {
      text-align: center;
      padding: 8px;
      background-color: Gray;
      width: 15%;
    }

    tr:nth-child(even) {
      background-color: #f2f2f2;
    }
  </style>
</head>

<body>
  <center>
    <button class="btn btn-secondary btn-sm mt-2 mb-2" onclick="setUpDownloadPageAsImage()">Export as Image</button>
    <button class="btn btn-secondary btn-sm mt-2 mb-2" onclick="printDivContent()">Print</button>
    <div class="fw-bold">
      <form method="POST">
        <label for="month"><b>MONTH:</b></label>
        <select name="month">
          <?php
          $months = [
            'Jan' => '01',
            'Feb' => '02',
            'March' => '03',
            'April' => '04',
            'May' => '05',
            'June' => '06',
            'July' => '07',
            'Aug' => '08',
            'Sep' => '09',
            'Oct' => '10',
            'Nov' => '11',
            'Dec' => '12',
          ];

          foreach ($months as $label => $value) {
            $selected = '';
            if (isset($_POST['month']) && $_POST['month'] == $value) {
              $selected = 'selected="selected"';
            }
            printf('<option value="%s" %s>%s</option>', $value, $selected, $label);
          }
          ?>
        </select>

        <label for="year">Select Year:</label>
        <select name="year" id="year">
          <?php
          $year = "";
          $current_year = date('Y');
          for ($i = $current_year - 3; $i <= $current_year; $i++) {
            echo "<option value=\"$i\"";
            if ($year == $i) {
              //echo "selected";
            }
            echo ">$i</option>";
          }
          ?>
        </select>
        <input type="submit" class="btn btn-secondary btn-sm mb-2" id="filter" name="filter" value="View">

        <!-- Button trigger modal -->
        <button type="button" class="btn btn-primary btn-sm mb-2" data-bs-toggle="modal" data-bs-target="#exampleModal">
          Login Time
        </button>

        <!-- Modal -->
        <div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
          <div class="modal-dialog">
            <div class="modal-content">
              <div class="modal-header">
                <h1 class="modal-title fs-5" id="exampleModalLabel">Attendance Login Time</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
              </div>
              <div class="modal-body">
                <table class="table">
                  <thead class="text-white bg-black">
                    <tr>
                      <th>Name</th>
                      <th>Time In</th>
                    </tr>
                  </thead>


                  <?php
                  $date = date("Y-m-d");
                  $sql = "SELECT Name,Time_In FROM prod_attendance WHERE Department IN ('Prod Main','Production Main') AND Emp_ID NOT IN('4444','11742','2023') AND DATE='$date' GROUP BY Name";
                  $result = mysqli_query($dbconnect, $sql);

                  while ($sql_result = mysqli_fetch_assoc($result)) {
                    $Time_In = $sql_result['Time_In'];
                    $Name = $sql_result['Name']; ?>

                    <tr>
                      <td><?php echo $Name; ?></td>
                      <td><?php echo $Time_In; ?></td>
                    </tr>

                  <?php } ?>
                </table>
              </div>
              <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
              </div>
            </div>
          </div>
        </div>
        <?PHP
        $month = "";
        $year = "";
        $Dept = "";

        if (isset($_POST['filter'])) {
          $month = $_POST['month'];
          $year = $_POST['year'];
          $Dept = "Prod Main"; ?>
        <?php
        }

        // GET DATA FROM DATABASE
        $sqlname = mysqli_query($dbconnect, "SELECT user_ID,emp_name,username FROM `user` WHERE department IN ('Prod Main','Production Main') AND role IN ('technician','cable_supervisor') AND emp_name!='TECHNICIAN' AND username NOT IN ('13472','11742','947','2023')");


        // FOR ABSENT NAMES
        for ($day = 1; $day <= 31; $day++) {
          $day_padded = str_pad($day, 2, "0", STR_PAD_LEFT);
          $sqldays_abs = mysqli_query($dbconnect, "SELECT DISTINCT u.emp_name, u.username FROM user u LEFT JOIN (SELECT DISTINCT Name, Emp_ID FROM prod_attendance WHERE Department IN ('Prod Main', 'Production Main') AND Name != 'TECHNICIAN' AND Name != ''AND DAY(DATE) = '$day_padded' AND MONTH(DATE) = '$month' AND YEAR(DATE) = '$year') p ON u.emp_name = p.Name AND u.username = p.Emp_ID WHERE Department IN ('Prod Main', 'Production Main') AND emp_name!= 'TECHNICIAN' AND  username NOT IN ('13472','947','11742','2023') AND p.Name IS NULL ORDER BY u.emp_name;");
          $sql_abs[$day] = $sqldays_abs;
        }

        // FOR PRESENT NAMES
        for ($day = 1; $day <= 31; $day++) {
          $day_padded = str_pad($day, 2, "0", STR_PAD_LEFT);
          $sqldays = mysqli_query($dbconnect, "SELECT Name FROM prod_attendance WHERE Department IN ('$Dept','Production Main') AND Name!='TECHNICIAN' AND Name !='' AND Emp_ID NOT IN ('13472','947','11742','2023') AND Day(DATE)='$day_padded' AND Month(DATE)='$month' AND YEAR(DATE)='$year' GROUP BY Name order by Name");
          $sqlday[$day] = $sqldays;
        }

        // CONVERT TO NUMBER
        $ALL_PRODUCTION = mysqli_num_rows($sqlname);
        /*while ($NAMES = mysqli_fetch_array($sqlname)) {
        $user = $NAMES['emp_name'];
      }*/

        // initialize an array to store the row counts for each day
        $row_counts = array();

        // iterate over the 31 days of the month
        for ($day = 1; $day <= 31; $day++) {
          // create a variable name for the row count for this day
          $var_name = "HC" . str_pad($day, 2, "0", STR_PAD_LEFT);

          // count the number of rows for this day
          ${$var_name} = mysqli_num_rows($sqlday[$day]);

          // store the row count in the array
          $row_counts[$day] = ${$var_name};
        }

        $sqlOTdays = mysqli_query($dbconnect, "SELECT DATE FROM prod_dtr WHERE OT_Day ='Yes' AND Department='$Dept' AND Name !='' AND Month(DATE)='$month' AND YEAR(DATE)='$year' GROUP BY DATE order by DATE");
        $Total_OTdays = mysqli_num_rows($sqlOTdays);

        $sqlRegdays = mysqli_query($dbconnect, "SELECT DATE FROM prod_dtr WHERE OT_Day !='Yes' AND Department='$Dept' AND Name !='' AND Month(DATE)='$month' AND YEAR(DATE)='$year' GROUP BY DATE order by DATE");
        $Total_Regdays = mysqli_num_rows($sqlRegdays);

        ?>
        <div class="table-responsive" id="attendance">
          <table class=" table-sm table-bordered">
            <thead>
              <tr>
                <td style='text-align:center' colspan='32' width='100%'>
                  <h4 style="background-color: #ADD8E6 ">
                    <bold>ATTENDANCE SUMMARY</bold>
                  </h4>
                  <?php echo "MONTH: $month YEAR: $year"; ?>

                </td>
              </tr>

              <tr style="background-color:#D1D0CE">
                <th style='text-align:center;color:black' colspan='5'>DAYS</th>
              </TR>
              <TR class="text-primary">
                <th style="background-color:#C0C0C0">01</th>
                <th style="background-color:#C0C0C0">02</th>
                <th style="background-color:#C0C0C0">03</th>
                <th style="background-color:#C0C0C0">04</th>
                <th style="background-color:#C0C0C0">05</th>
              </tr>
              <tr>
                <td><?php while ($row = mysqli_fetch_array($sqlday[1])) {
                      $Name01 = $row['Name'];
                      echo "$Name01<br>";
                    }  ?></td>
                <td><?php while ($row = mysqli_fetch_array($sqlday[2])) {
                      $Name02 = $row['Name'];
                      echo "$Name02 <br>";
                    }  ?></td>

                <td><?php while ($row = mysqli_fetch_array($sqlday[3])) {
                      $Name03 = $row['Name'];
                      echo "$Name03 <br>";
                    }  ?></td>
                <td><?php while ($row = mysqli_fetch_array($sqlday[4])) {
                      $Name04 = $row['Name'];
                      echo "$Name04 <br>";
                    }  ?></td>
                <td><?php while ($row = mysqli_fetch_array($sqlday[5])) {
                      $Name05 = $row['Name'];
                      echo "$Name05 <br>";
                    }  ?></td>
              </tr>
              <TR>
                <TD><?php if (!$sqldays_abs) {
                      // The query failed, handle the error here
                      echo "Error: " . mysqli_error($dbconnect);
                    } else {
                      // The query was successful, fetch the results and output the names
                      $absent_employees = ""; // initialize variable to store absent employees
                      while ($rows = mysqli_fetch_assoc($sql_abs[1])) {
                        $absent_employees .= $rows['emp_name'] . "<br>"; // concatenate each absent employee name with a line break
                      }
                    }

                    if ($HC01 != 0) {
                      $att = round(($HC01 / $ALL_PRODUCTION) * 100) . "%";
                      $abs = $ALL_PRODUCTION - $HC01;
                      echo "Present: $HC01" . "&nbsp;&nbsp;&nbsp;&nbsp;";
                      echo '<span class="d-inline-block" tabindex="0" data-bs-toggle="popover" title="Names" data-bs-html="true" data-bs-trigger="hover focus" data-bs-content="' . $absent_employees . ' " ><button type="button"  class="btn btn-primary btn-sm" disabled>Absent: ' . $abs . '</button></span>';
                      echo "<br>Attendance Rate: $att";
                    } else {
                      echo "No Work";
                    } ?>
                </TD>

                <TD> <?php if (!$sqldays_abs) {
                        // The query failed, handle the error here
                        echo "Error: " . mysqli_error($dbconnect);
                      } else {
                        // The query was successful, fetch the results and output the names
                        $absent_employees = ""; // initialize variable to store absent employees
                        while ($rows = mysqli_fetch_assoc($sql_abs[2])) {
                          $absent_employees .= $rows['emp_name'] . "<br>"; // concatenate each absent employee name with a line break
                        }
                      }

                      if ($HC02 != 0) {
                        $att = round(($HC02 / $ALL_PRODUCTION) * 100) . "%";
                        $abs = $ALL_PRODUCTION - $HC02;
                        echo "Present: $HC02" . "&nbsp;&nbsp;&nbsp;&nbsp;";
                        echo '<span class="d-inline-block" tabindex="0" data-bs-toggle="popover" title="Names" data-bs-html="true" data-bs-trigger="hover focus" data-bs-content="' . $absent_employees . ' " ><button type="button"  class="btn btn-primary btn-sm" disabled>Absent: ' . $abs . '</button></span>';
                        echo "<br>Attendance Rate: $att";
                      } else {
                        echo "No Work";
                      } ?></TD>
                <TD><?php if (!$sqldays_abs) {
                      // The query failed, handle the error here
                      echo "Error: " . mysqli_error($dbconnect);
                    } else {
                      // The query was successful, fetch the results and output the names
                      $absent_employees = ""; // initialize variable to store absent employees
                      while ($rows = mysqli_fetch_assoc($sql_abs[3])) {
                        $absent_employees .= $rows['emp_name'] . "<br>"; // concatenate each absent employee name with a line break
                      }
                    }

                    if ($HC03 != 0) {
                      $att = round(($HC03 / $ALL_PRODUCTION) * 100) . "%";
                      $abs = $ALL_PRODUCTION - $HC03;
                      echo "Present: $HC03" . "&nbsp;&nbsp;&nbsp;&nbsp;";
                      echo '<span class="d-inline-block" tabindex="0" data-bs-toggle="popover" title="Names" data-bs-html="true" data-bs-trigger="hover focus" data-bs-content="' . $absent_employees . ' " ><button type="button"  class="btn btn-primary btn-sm" disabled>Absent: ' . $abs . '</button></span>';
                      echo "<br>Attendance Rate: $att";
                    } else {
                      echo "No Work";
                    } ?></TD>
                <TD><?php if (!$sqldays_abs) {
                      // The query failed, handle the error here
                      echo "Error: " . mysqli_error($dbconnect);
                    } else {
                      // The query was successful, fetch the results and output the names
                      $absent_employees = ""; // initialize variable to store absent employees
                      while ($rows = mysqli_fetch_assoc($sql_abs[4])) {
                        $absent_employees .= $rows['emp_name'] . "<br>"; // concatenate each absent employee name with a line break
                      }
                    }

                    if ($HC04 != 0) {
                      $att = round(($HC04 / $ALL_PRODUCTION) * 100) . "%";
                      $abs = $ALL_PRODUCTION - $HC04;
                      echo "Present: $HC04" . "&nbsp;&nbsp;&nbsp;&nbsp;";
                      echo '<span class="d-inline-block" tabindex="0" data-bs-toggle="popover" title="Names" data-bs-html="true" data-bs-trigger="hover focus" data-bs-content="' . $absent_employees . ' " ><button type="button"  class="btn btn-primary btn-sm" disabled>Absent: ' . $abs . '</button></span>';
                      echo "<br>Attendance Rate: $att";
                    } else {
                      echo "No Work";
                    } ?></TD>
                <TD><?php if (!$sqldays_abs) {
                      // The query failed, handle the error here
                      echo "Error: " . mysqli_error($dbconnect);
                    } else {
                      // The query was successful, fetch the results and output the names
                      $absent_employees = ""; // initialize variable to store absent employees
                      while ($rows = mysqli_fetch_assoc($sql_abs[5])) {
                        $absent_employees .= $rows['emp_name'] . "<br>"; // concatenate each absent employee name with a line break
                      }
                    }

                    if ($HC05 != 0) {
                      $att = round(($HC05 / $ALL_PRODUCTION) * 100) . "%";
                      $abs = $ALL_PRODUCTION - $HC05;
                      echo "Present: $HC05" . "&nbsp;&nbsp;&nbsp;&nbsp;";
                      echo '<span class="d-inline-block" tabindex="0" data-bs-toggle="popover" title="Names" data-bs-html="true" data-bs-trigger="hover focus" data-bs-content="' . $absent_employees . ' " ><button type="button"  class="btn btn-primary btn-sm" disabled>Absent: ' . $abs . '</button></span>';
                      echo "<br>Attendance Rate: $att";
                    } else {
                      echo "No Work";
                    } ?></TD>
              </TR>



              <tr class="text-primary">
                <th style="background-color:#C0C0C0">06</th>
                <th style="background-color:#C0C0C0">07</th>
                <th style="background-color:#C0C0C0">08</th>
                <th style="background-color:#C0C0C0">09</th>
                <th style="background-color:#C0C0C0">10</th>
              </tr>
              <tr>
                <td><?php while ($row = mysqli_fetch_array($sqlday[6])) {
                      $Name06 = $row['Name'];
                      echo "$Name06 <br>";
                    }  ?></td>
                <td><?php while ($row = mysqli_fetch_array($sqlday[7])) {
                      $Name07 = $row['Name'];
                      echo "$Name07 <br>";
                    }  ?></td>
                <td><?php while ($row = mysqli_fetch_array($sqlday[8])) {
                      $Name08 = $row['Name'];
                      echo "$Name08 <br>";
                    }  ?></td>
                <td><?php while ($row = mysqli_fetch_array($sqlday[9])) {
                      $Name09 = $row['Name'];
                      echo "$Name09 <br>";
                    }  ?></td>
                <td><?php while ($row = mysqli_fetch_array($sqlday[10])) {
                      $Name10 = $row['Name'];
                      echo "$Name10 <br>";
                    }  ?></td>
              </tr>
              <TR>
                <TD><?php if (!$sqldays_abs) {
                      // The query failed, handle the error here
                      echo "Error: " . mysqli_error($dbconnect);
                    } else {
                      // The query was successful, fetch the results and output the names
                      $absent_employees = ""; // initialize variable to store absent employees
                      while ($rows = mysqli_fetch_assoc($sql_abs[6])) {
                        $absent_employees .= $rows['emp_name'] . "<br>"; // concatenate each absent employee name with a line break
                      }
                    }

                    if ($HC06 != 0) {
                      $att = round(($HC06 / $ALL_PRODUCTION) * 100) . "%";
                      $abs = $ALL_PRODUCTION - $HC06;
                      echo "Present: $HC06" . "&nbsp;&nbsp;&nbsp;&nbsp;";
                      echo '<span class="d-inline-block" tabindex="0" data-bs-toggle="popover" title="Names" data-bs-html="true" data-bs-trigger="hover focus" data-bs-content="' . $absent_employees . ' " ><button type="button"  class="btn btn-primary btn-sm" disabled>Absent: ' . $abs . '</button></span>';
                      echo "<br>Attendance Rate: $att";
                    } else {
                      echo "No Work";
                    } ?></TD>
                <TD><?php if (!$sqldays_abs) {
                      // The query failed, handle the error here
                      echo "Error: " . mysqli_error($dbconnect);
                    } else {
                      // The query was successful, fetch the results and output the names
                      $absent_employees = ""; // initialize variable to store absent employees
                      while ($rows = mysqli_fetch_assoc($sql_abs[7])) {
                        $absent_employees .= $rows['emp_name'] . "<br>"; // concatenate each absent employee name with a line break
                      }
                    }

                    if ($HC07 != 0) {
                      $att = round(($HC07 / $ALL_PRODUCTION) * 100) . "%";
                      $abs = $ALL_PRODUCTION - $HC07;
                      echo "Present: $HC07" . "&nbsp;&nbsp;&nbsp;&nbsp;";
                      echo '<span class="d-inline-block" tabindex="0" data-bs-toggle="popover" title="Names" data-bs-html="true" data-bs-trigger="hover focus" data-bs-content="' . $absent_employees . ' " ><button type="button"  class="btn btn-primary btn-sm" disabled>Absent: ' . $abs . '</button></span>';
                      echo "<br>Attendance Rate: $att";
                    } else {
                      echo "No Work";
                    } ?></TD>
                <TD><?php if (!$sqldays_abs) {
                      // The query failed, handle the error here
                      echo "Error: " . mysqli_error($dbconnect);
                    } else {
                      // The query was successful, fetch the results and output the names
                      $absent_employees = ""; // initialize variable to store absent employees
                      while ($rows = mysqli_fetch_assoc($sql_abs[8])) {
                        $absent_employees .= $rows['emp_name'] . "<br>"; // concatenate each absent employee name with a line break
                      }
                    }

                    if ($HC08 != 0) {
                      $att = round(($HC08 / $ALL_PRODUCTION) * 100) . "%";
                      $abs = $ALL_PRODUCTION - $HC08;
                      echo "Present: $HC08" . "&nbsp;&nbsp;&nbsp;&nbsp;";
                      echo '<span class="d-inline-block" tabindex="0" data-bs-toggle="popover" title="Names" data-bs-html="true" data-bs-trigger="hover focus" data-bs-content="' . $absent_employees . ' " ><button type="button"  class="btn btn-primary btn-sm" disabled>Absent: ' . $abs . '</button></span>';
                      echo "<br>Attendance Rate: $att";
                    } else {
                      echo "No Work";
                    } ?></TD>
                <TD><?php if (!$sqldays_abs) {
                      // The query failed, handle the error here
                      echo "Error: " . mysqli_error($dbconnect);
                    } else {
                      // The query was successful, fetch the results and output the names
                      $absent_employees = ""; // initialize variable to store absent employees
                      while ($rows = mysqli_fetch_assoc($sql_abs[9])) {
                        $absent_employees .= $rows['emp_name'] . "<br>"; // concatenate each absent employee name with a line break
                      }
                    }

                    if ($HC09 != 0) {
                      $att = round(($HC09 / $ALL_PRODUCTION) * 100) . "%";
                      $abs = $ALL_PRODUCTION - $HC09;
                      echo "Present: $HC09" . "&nbsp;&nbsp;&nbsp;&nbsp;";
                      echo '<span class="d-inline-block" tabindex="0" data-bs-toggle="popover" title="Names" data-bs-html="true" data-bs-trigger="hover focus" data-bs-content="' . $absent_employees . ' " ><button type="button"  class="btn btn-primary btn-sm" disabled>Absent: ' . $abs . '</button></span>';
                      echo "<br>Attendance Rate: $att";
                    } else {
                      echo "No Work";
                    } ?></TD>
                <TD><?php if (!$sqldays_abs) {
                      // The query failed, handle the error here
                      echo "Error: " . mysqli_error($dbconnect);
                    } else {
                      // The query was successful, fetch the results and output the names
                      $absent_employees = ""; // initialize variable to store absent employees
                      while ($rows = mysqli_fetch_assoc($sql_abs[10])) {
                        $absent_employees .= $rows['emp_name'] . "<br>"; // concatenate each absent employee name with a line break
                      }
                    }

                    if ($HC10 != 0) {
                      $att = round(($HC10 / $ALL_PRODUCTION) * 100) . "%";
                      $abs = $ALL_PRODUCTION - $HC10;
                      echo "Present: $HC10" . "&nbsp;&nbsp;&nbsp;&nbsp;";
                      echo '<span class="d-inline-block" tabindex="0" data-bs-toggle="popover" title="Names" data-bs-html="true" data-bs-trigger="hover focus" data-bs-content="' . $absent_employees . ' " ><button type="button"  class="btn btn-primary btn-sm" disabled>Absent: ' . $abs . '</button></span>';
                      echo "<br>Attendance Rate: $att";
                    } else {
                      echo "No Work";
                    } ?></TD>
              </TR>

              <tr class="text-primary">
                <th style="background-color:#C0C0C0">11</th>
                <th style="background-color:#C0C0C0">12</th>
                <th style="background-color:#C0C0C0">13</th>
                <th style="background-color:#C0C0C0">14</th>
                <th style="background-color:#C0C0C0">15</th>
              </tr>
              <tr>
                <td><?php while ($row = mysqli_fetch_array($sqlday[11])) {
                      $Name11 = $row['Name'];
                      echo "$Name11 <br>";
                    }  ?></td>
                <td><?php while ($row = mysqli_fetch_array($sqlday[12])) {
                      $Name12 = $row['Name'];
                      echo "$Name12 <br>";
                    }  ?></td>
                <td><?php while ($row = mysqli_fetch_array($sqlday[13])) {
                      $Name13 = $row['Name'];
                      echo "$Name13 <br>";
                    }  ?></td>
                <td><?php while ($row = mysqli_fetch_array($sqlday[14])) {
                      $Name14 = $row['Name'];
                      echo "$Name14 <br>";
                    }  ?></td>
                <td><?php while ($row = mysqli_fetch_array($sqlday[15])) {
                      $Name15 = $row['Name'];
                      echo "$Name15 <br>";
                    }  ?></td>
              </tr>
              <TR>
                <TD><?php if (!$sqldays_abs) {
                      // The query failed, handle the error here
                      echo "Error: " . mysqli_error($dbconnect);
                    } else {
                      // The query was successful, fetch the results and output the names
                      $absent_employees = ""; // initialize variable to store absent employees
                      while ($rows = mysqli_fetch_assoc($sql_abs[11])) {
                        $absent_employees .= $rows['emp_name'] . "<br>"; // concatenate each absent employee name with a line break
                      }
                    }

                    if ($HC11 != 0) {
                      $att = round(($HC11 / $ALL_PRODUCTION) * 100) . "%";
                      $abs = $ALL_PRODUCTION - $HC11;
                      echo "Present: $HC11" . "&nbsp;&nbsp;&nbsp;&nbsp;";
                      echo '<span class="d-inline-block" tabindex="0" data-bs-toggle="popover" title="Names" data-bs-html="true" data-bs-trigger="hover focus" data-bs-content="' . $absent_employees . ' " ><button type="button"  class="btn btn-primary btn-sm" disabled>Absent: ' . $abs . '</button></span>';
                      echo "<br>Attendance Rate: $att";
                    } else {
                      echo "No Work";
                    } ?></TD>
                <TD><?php if (!$sqldays_abs) {
                      // The query failed, handle the error here
                      echo "Error: " . mysqli_error($dbconnect);
                    } else {
                      // The query was successful, fetch the results and output the names
                      $absent_employees = ""; // initialize variable to store absent employees
                      while ($rows = mysqli_fetch_assoc($sql_abs[12])) {
                        $absent_employees .= $rows['emp_name'] . "<br>"; // concatenate each absent employee name with a line break
                      }
                    }

                    if ($HC12 != 0) {
                      $att = round(($HC12 / $ALL_PRODUCTION) * 100) . "%";
                      $abs = $ALL_PRODUCTION - $HC12;
                      echo "Present: $HC12" . "&nbsp;&nbsp;&nbsp;&nbsp;";
                      echo '<span class="d-inline-block" tabindex="0" data-bs-toggle="popover" title="Names" data-bs-html="true" data-bs-trigger="hover focus" data-bs-content="' . $absent_employees . ' " ><button type="button"  class="btn btn-primary btn-sm" disabled>Absent: ' . $abs . '</button></span>';
                      echo "<br>Attendance Rate: $att";
                    } else {
                      echo "No Work";
                    } ?></TD>
                <TD><?php if (!$sqldays_abs) {
                      // The query failed, handle the error here
                      echo "Error: " . mysqli_error($dbconnect);
                    } else {
                      // The query was successful, fetch the results and output the names
                      $absent_employees = ""; // initialize variable to store absent employees
                      while ($rows = mysqli_fetch_assoc($sql_abs[13])) {
                        $absent_employees .= $rows['emp_name'] . "<br>"; // concatenate each absent employee name with a line break
                      }
                    }

                    if ($HC13 != 0) {
                      $att = round(($HC13 / $ALL_PRODUCTION) * 100) . "%";
                      $abs = $ALL_PRODUCTION - $HC13;
                      echo "Present: $HC13" . "&nbsp;&nbsp;&nbsp;&nbsp;";
                      echo '<span class="d-inline-block" tabindex="0" data-bs-toggle="popover" title="Names" data-bs-html="true" data-bs-trigger="hover focus" data-bs-content="' . $absent_employees . ' " ><button type="button"  class="btn btn-primary btn-sm" disabled>Absent: ' . $abs . '</button></span>';
                      echo "<br>Attendance Rate: $att";
                    } else {
                      echo "No Work";
                    } ?></TD>
                <TD><?php if (!$sqldays_abs) {
                      // The query failed, handle the error here
                      echo "Error: " . mysqli_error($dbconnect);
                    } else {
                      // The query was successful, fetch the results and output the names
                      $absent_employees = ""; // initialize variable to store absent employees
                      while ($rows = mysqli_fetch_assoc($sql_abs[14])) {
                        $absent_employees .= $rows['emp_name'] . "<br>"; // concatenate each absent employee name with a line break
                      }
                    }

                    if ($HC14 != 0) {
                      $att = round(($HC14 / $ALL_PRODUCTION) * 100) . "%";
                      $abs = $ALL_PRODUCTION - $HC14;
                      echo "Present: $HC14" . "&nbsp;&nbsp;&nbsp;&nbsp;";
                      echo '<span class="d-inline-block" tabindex="0" data-bs-toggle="popover" title="Names" data-bs-html="true" data-bs-trigger="hover focus" data-bs-content="' . $absent_employees . ' " ><button type="button"  class="btn btn-primary btn-sm" disabled>Absent: ' . $abs . '</button></span>';
                      echo "<br>Attendance Rate: $att";
                    } else {
                      echo "No Work";
                    } ?></TD>
                <TD><?php if (!$sqldays_abs) {
                      // The query failed, handle the error here
                      echo "Error: " . mysqli_error($dbconnect);
                    } else {
                      // The query was successful, fetch the results and output the names
                      $absent_employees = ""; // initialize variable to store absent employees
                      while ($rows = mysqli_fetch_assoc($sql_abs[15])) {
                        $absent_employees .= $rows['emp_name'] . "<br>"; // concatenate each absent employee name with a line break
                      }
                    }

                    if ($HC15 != 0) {
                      $att = round(($HC15 / $ALL_PRODUCTION) * 100) . "%";
                      $abs = $ALL_PRODUCTION - $HC15;
                      echo "Present: $HC15" . "&nbsp;&nbsp;&nbsp;&nbsp;";
                      echo '<span class="d-inline-block" tabindex="0" data-bs-toggle="popover" title="Names" data-bs-html="true" data-bs-trigger="hover focus" data-bs-content="' . $absent_employees . ' " ><button type="button"  class="btn btn-primary btn-sm" disabled>Absent: ' . $abs . '</button></span>';
                      echo "<br>Attendance Rate: $att";
                    } else {
                      echo "No Work";
                    } ?></TD>
              </TR>


              <tr class="text-primary">
                <th style="background-color:#C0C0C0">16</th>
                <th style="background-color:#C0C0C0">17</th>
                <th style="background-color:#C0C0C0">18</th>
                <th style="background-color:#C0C0C0">19</th>
                <th style="background-color:#C0C0C0">20</th>
              </tr>
              <tr>
                <td><?php while ($row = mysqli_fetch_array($sqlday[16])) {
                      $Name16 = $row['Name'];
                      echo "$Name16 <br>";
                    }  ?></td>
                <td><?php while ($row = mysqli_fetch_array($sqlday[17])) {
                      $Name17 = $row['Name'];
                      echo "$Name17 <br>";
                    }  ?></td>
                <td><?php while ($row = mysqli_fetch_array($sqlday[18])) {
                      $Name18 = $row['Name'];
                      echo "$Name18 <br>";
                    }  ?></td>
                <td><?php while ($row = mysqli_fetch_array($sqlday[19])) {
                      $Name19 = $row['Name'];
                      echo "$Name19 <br>";
                    }  ?></td>
                <td><?php while ($row = mysqli_fetch_array($sqlday[20])) {
                      $Name20 = $row['Name'];
                      echo "$Name20 <br>";
                    }  ?></td>
              </tr>
              <TR>
                <TD><?php if (!$sqldays_abs) {
                      // The query failed, handle the error here
                      echo "Error: " . mysqli_error($dbconnect);
                    } else {
                      // The query was successful, fetch the results and output the names
                      $absent_employees = ""; // initialize variable to store absent employees
                      while ($rows = mysqli_fetch_assoc($sql_abs[16])) {
                        $absent_employees .= $rows['emp_name'] . "<br>"; // concatenate each absent employee name with a line break
                      }
                    }

                    if ($HC16 != 0) {
                      $att = round(($HC16 / $ALL_PRODUCTION) * 100) . "%";
                      $abs = $ALL_PRODUCTION - $HC16;
                      echo "Present: $HC16" . "&nbsp;&nbsp;&nbsp;&nbsp;";
                      echo '<span class="d-inline-block" tabindex="0" data-bs-toggle="popover" title="Names" data-bs-html="true" data-bs-trigger="hover focus" data-bs-content="' . $absent_employees . ' " ><button type="button"  class="btn btn-primary btn-sm" disabled>Absent: ' . $abs . '</button></span>';
                      echo "<br>Attendance: $att";
                    } else {
                      echo "No Work";
                    } ?></TD>
                <TD><?php if (!$sqldays_abs) {
                      // The query failed, handle the error here
                      echo "Error: " . mysqli_error($dbconnect);
                    } else {
                      // The query was successful, fetch the results and output the names
                      $absent_employees = ""; // initialize variable to store absent employees
                      while ($rows = mysqli_fetch_assoc($sql_abs[17])) {
                        $absent_employees .= $rows['emp_name'] . "<br>"; // concatenate each absent employee name with a line break
                      }
                    }

                    if ($HC17 != 0) {
                      $att = round(($HC17 / $ALL_PRODUCTION) * 100) . "%";
                      $abs = $ALL_PRODUCTION - $HC17;
                      echo "Present: $HC17" . "&nbsp;&nbsp;&nbsp;&nbsp;";
                      echo '<span class="d-inline-block" tabindex="0" data-bs-toggle="popover" title="Names" data-bs-html="true" data-bs-trigger="hover focus" data-bs-content="' . $absent_employees . ' " ><button type="button"  class="btn btn-primary btn-sm" disabled>Absent: ' . $abs . '</button></span>';
                      echo "<br>Attendance Rate: $att";
                    } else {
                      echo "No Work";
                    } ?></TD>
                <TD><?php if (!$sqldays_abs) {
                      // The query failed, handle the error here
                      echo "Error: " . mysqli_error($dbconnect);
                    } else {
                      // The query was successful, fetch the results and output the names
                      $absent_employees = ""; // initialize variable to store absent employees
                      while ($rows = mysqli_fetch_assoc($sql_abs[18])) {
                        $absent_employees .= $rows['emp_name'] . "<br>"; // concatenate each absent employee name with a line break
                      }
                    }

                    if ($HC18 != 0) {
                      $att = round(($HC18 / $ALL_PRODUCTION) * 100) . "%";
                      $abs = $ALL_PRODUCTION - $HC18;
                      echo "Present: $HC18" . "&nbsp;&nbsp;&nbsp;&nbsp;";
                      echo '<span class="d-inline-block" tabindex="0" data-bs-toggle="popover" title="Names" data-bs-html="true" data-bs-trigger="hover focus" data-bs-content="' . $absent_employees . ' " ><button type="button"  class="btn btn-primary btn-sm" disabled>Absent: ' . $abs . '</button></span>';
                      echo "<br>Attendance Rate: $att";
                    } else {
                      echo "No Work";
                    } ?></TD>
                <TD><?php if (!$sqldays_abs) {
                      // The query failed, handle the error here
                      echo "Error: " . mysqli_error($dbconnect);
                    } else {
                      // The query was successful, fetch the results and output the names
                      $absent_employees = ""; // initialize variable to store absent employees
                      while ($rows = mysqli_fetch_assoc($sql_abs[19])) {
                        $absent_employees .= $rows['emp_name'] . "<br>"; // concatenate each absent employee name with a line break
                      }
                    }

                    if ($HC19 != 0) {
                      $att = round(($HC19 / $ALL_PRODUCTION) * 100) . "%";
                      $abs = $ALL_PRODUCTION - $HC19;
                      echo "Present: $HC19" . "&nbsp;&nbsp;&nbsp;&nbsp;";
                      echo '<span class="d-inline-block" tabindex="0" data-bs-toggle="popover" title="Names" data-bs-html="true" data-bs-trigger="hover focus" data-bs-content="' . $absent_employees . ' " ><button type="button"  class="btn btn-primary btn-sm" disabled>Absent: ' . $abs . '</button></span>';
                      echo "<br>Attendance Rate: $att";
                    } else {
                      echo "No Work";
                    } ?></TD>
                <TD><?php if (!$sqldays_abs) {
                      // The query failed, handle the error here
                      echo "Error: " . mysqli_error($dbconnect);
                    } else {
                      // The query was successful, fetch the results and output the names
                      $absent_employees = ""; // initialize variable to store absent employees
                      while ($rows = mysqli_fetch_assoc($sql_abs[20])) {
                        $absent_employees .= $rows['emp_name'] . "<br>"; // concatenate each absent employee name with a line break
                      }
                    }

                    if ($HC20 != 0) {
                      $att = round(($HC20 / $ALL_PRODUCTION) * 100) . "%";
                      $abs = $ALL_PRODUCTION - $HC20;
                      echo "Present: $HC20" . "&nbsp;&nbsp;&nbsp;&nbsp;";
                      echo '<span class="d-inline-block" tabindex="0" data-bs-toggle="popover" title="Names" data-bs-html="true" data-bs-trigger="hover focus" data-bs-content="' . $absent_employees . ' " ><button type="button"  class="btn btn-primary btn-sm" disabled>Absent: ' . $abs . '</button></span>';
                      echo "<br>Attendance Rate: $att";
                    } else {
                      echo "No Work";
                    } ?></TD>
              </TR>

              <tr class="text-primary">
                <th style="background-color:#C0C0C0">21</th>
                <th style="background-color:#C0C0C0">22</th>
                <th style="background-color:#C0C0C0">23</th>
                <th style="background-color:#C0C0C0">24</th>
                <th style="background-color:#C0C0C0">25</th>
              </tr>
              <tr>
                <td><?php while ($row = mysqli_fetch_array($sqlday[21])) {
                      $Name21 = $row['Name'];
                      echo "$Name21 <br>";
                    }  ?></td>
                <td><?php while ($row = mysqli_fetch_array($sqlday[22])) {
                      $Name22 = $row['Name'];
                      echo "$Name22 <br>";
                    }  ?></td>
                <td><?php while ($row = mysqli_fetch_array($sqlday[23])) {
                      $Name23 = $row['Name'];
                      echo "$Name23 <br>";
                    }  ?></td>
                <td><?php while ($row = mysqli_fetch_array($sqlday[24])) {
                      $Name24 = $row['Name'];
                      echo "$Name24 <br>";
                    }  ?></td>
                <td><?php while ($row = mysqli_fetch_array($sqlday[25])) {
                      $Name25 = $row['Name'];
                      echo "$Name25 <br>";
                    }  ?></td>
              </tr>
              <TR>
                <TD><?php if (!$sqldays_abs) {
                      // The query failed, handle the error here
                      echo "Error: " . mysqli_error($dbconnect);
                    } else {
                      // The query was successful, fetch the results and output the names
                      $absent_employees = ""; // initialize variable to store absent employees
                      while ($rows = mysqli_fetch_assoc($sql_abs[21])) {
                        $absent_employees .= $rows['emp_name'] . "<br>"; // concatenate each absent employee name with a line break
                      }
                    }

                    if ($HC21 != 0) {
                      $att = round(($HC21 / $ALL_PRODUCTION) * 100) . "%";
                      $abs = $ALL_PRODUCTION - $HC21;
                      echo "Present: $HC21" . "&nbsp;&nbsp;&nbsp;&nbsp;";
                      echo '<span class="d-inline-block" tabindex="0" data-bs-toggle="popover" title="Names" data-bs-html="true" data-bs-trigger="hover focus" data-bs-content="' . $absent_employees . ' " ><button type="button"  class="btn btn-primary btn-sm" disabled>Absent: ' . $abs . '</button></span>';
                      echo "<br>Attendance Rate: $att";
                    } else {
                      echo "No Work";
                    } ?></TD>
                <TD><?php if (!$sqldays_abs) {
                      // The query failed, handle the error here
                      echo "Error: " . mysqli_error($dbconnect);
                    } else {
                      // The query was successful, fetch the results and output the names
                      $absent_employees = ""; // initialize variable to store absent employees
                      while ($rows = mysqli_fetch_assoc($sql_abs[22])) {
                        $absent_employees .= $rows['emp_name'] . "<br>"; // concatenate each absent employee name with a line break
                      }
                    }

                    if ($HC22 != 0) {
                      $att = round(($HC22 / $ALL_PRODUCTION) * 100) . "%";
                      $abs = $ALL_PRODUCTION - $HC22;
                      echo "Present: $HC22" . "&nbsp;&nbsp;&nbsp;&nbsp;";
                      echo '<span class="d-inline-block" tabindex="0" data-bs-toggle="popover" title="Names" data-bs-html="true" data-bs-trigger="hover focus" data-bs-content="' . $absent_employees . ' " ><button type="button"  class="btn btn-primary btn-sm" disabled>Absent: ' . $abs . '</button></span>';
                      echo "<br>Attendance: $att";
                    } else {
                      echo "No Work";
                    } ?></TD>
                <TD><?php if (!$sqldays_abs) {
                      // The query failed, handle the error here
                      echo "Error: " . mysqli_error($dbconnect);
                    } else {
                      // The query was successful, fetch the results and output the names
                      $absent_employees = ""; // initialize variable to store absent employees
                      while ($rows = mysqli_fetch_assoc($sql_abs[23])) {
                        $absent_employees .= $rows['emp_name'] . "<br>"; // concatenate each absent employee name with a line break
                      }
                    }

                    if ($HC23 != 0) {
                      $att = round(($HC23 / $ALL_PRODUCTION) * 100) . "%";
                      $abs = $ALL_PRODUCTION - $HC23;
                      echo "Present: $HC23" . "&nbsp;&nbsp;&nbsp;&nbsp;";
                      echo '<span class="d-inline-block" tabindex="0" data-bs-toggle="popover" title="Names" data-bs-html="true" data-bs-trigger="hover focus" data-bs-content="' . $absent_employees . ' " ><button type="button"  class="btn btn-primary btn-sm" disabled>Absent: ' . $abs . '</button></span>';
                      echo "<br>Attendance Rate: $att";
                    } else {
                      echo "No Work";
                    } ?></TD>
                <TD><?php if (!$sqldays_abs) {
                      // The query failed, handle the error here
                      echo "Error: " . mysqli_error($dbconnect);
                    } else {
                      // The query was successful, fetch the results and output the names
                      $absent_employees = ""; // initialize variable to store absent employees
                      while ($rows = mysqli_fetch_assoc($sql_abs[24])) {
                        $absent_employees .= $rows['emp_name'] . "<br>"; // concatenate each absent employee name with a line break
                      }
                    }

                    if ($HC24 != 0) {
                      $att = round(($HC24 / $ALL_PRODUCTION) * 100) . "%";
                      $abs = $ALL_PRODUCTION - $HC24;
                      echo "Present: $HC24" . "&nbsp;&nbsp;&nbsp;&nbsp;";
                      echo '<span class="d-inline-block" tabindex="0" data-bs-toggle="popover" title="Names" data-bs-html="true" data-bs-trigger="hover focus" data-bs-content="' . $absent_employees . ' " ><button type="button"  class="btn btn-primary btn-sm" disabled>Absent: ' . $abs . '</button></span>';
                      echo "<br>Attendance Rate: $att";
                    } else {
                      echo "No Work";
                    } ?></TD>
                <TD><?php if (!$sqldays_abs) {
                      // The query failed, handle the error here
                      echo "Error: " . mysqli_error($dbconnect);
                    } else {
                      // The query was successful, fetch the results and output the names
                      $absent_employees = ""; // initialize variable to store absent employees
                      while ($rows = mysqli_fetch_assoc($sql_abs[25])) {
                        $absent_employees .= $rows['emp_name'] . "<br>"; // concatenate each absent employee name with a line break
                      }
                    }

                    if ($HC25 != 0) {
                      $att = round(($HC25 / $ALL_PRODUCTION) * 100) . "%";
                      $abs = $ALL_PRODUCTION - $HC25;
                      echo "Present: $HC25" . "&nbsp;&nbsp;&nbsp;&nbsp;";
                      echo '<span class="d-inline-block" tabindex="0" data-bs-toggle="popover" title="Names" data-bs-html="true" data-bs-trigger="hover focus" data-bs-content="' . $absent_employees . ' " ><button type="button"  class="btn btn-primary btn-sm" disabled>Absent: ' . $abs . '</button></span>';
                      echo "<br>Attendance Rate: $att";
                    } else {
                      echo "No Work";
                    } ?></TD>
              </TR>


              <tr class="text-primary">
                <th style="background-color:#C0C0C0">26</th>
                <th style="background-color:#C0C0C0">27</th>
                <th style="background-color:#C0C0C0">28</th>
                <th style="background-color:#C0C0C0">29</th>
                <th style="background-color:#C0C0C0">30</th>
              </tr>
              <tr>
                <td><?php while ($row = mysqli_fetch_array($sqlday[26])) {
                      $Name26 = $row['Name'];
                      echo "$Name26 <br>";
                    }  ?></td>
                <td><?php while ($row = mysqli_fetch_array($sqlday[27])) {
                      $Name27 = $row['Name'];
                      echo "$Name27 <br>";
                    }  ?></td>
                <td><?php while ($row = mysqli_fetch_array($sqlday[28])) {
                      $Name28 = $row['Name'];
                      echo "$Name28 <br>";
                    }  ?></td>
                <td><?php while ($row = mysqli_fetch_array($sqlday[29])) {
                      $Name29 = $row['Name'];
                      echo "$Name29 <br>";
                    }  ?></td>
                <td><?php while ($row = mysqli_fetch_array($sqlday[30])) {
                      $Name30 = $row['Name'];
                      echo "$Name30 <br>";
                    }  ?></td>
              </tr>
              <TR>
                <TD><?php if (!$sqldays_abs) {
                      // The query failed, handle the error here
                      echo "Error: " . mysqli_error($dbconnect);
                    } else {
                      // The query was successful, fetch the results and output the names
                      $absent_employees = ""; // initialize variable to store absent employees
                      while ($rows = mysqli_fetch_assoc($sql_abs[26])) {
                        $absent_employees .= $rows['emp_name'] . "<br>"; // concatenate each absent employee name with a line break
                      }
                    }

                    if ($HC26 != 0) {
                      $att = round(($HC26 / $ALL_PRODUCTION) * 100) . "%";
                      $abs = $ALL_PRODUCTION - $HC26;
                      echo "Present: $HC26" . "&nbsp;&nbsp;&nbsp;&nbsp;";
                      echo '<span class="d-inline-block" tabindex="0" data-bs-toggle="popover" title="Names" data-bs-html="true" data-bs-trigger="hover focus" data-bs-content="' . $absent_employees . ' " ><button type="button"  class="btn btn-primary btn-sm" disabled>Absent: ' . $abs . '</button></span>';
                      echo "<br>Attendance Rate: $att";
                    } else {
                      echo "No Work";
                    } ?></TD>
                <TD><?php if (!$sqldays_abs) {
                      // The query failed, handle the error here
                      echo "Error: " . mysqli_error($dbconnect);
                    } else {
                      // The query was successful, fetch the results and output the names
                      $absent_employees = ""; // initialize variable to store absent employees
                      while ($rows = mysqli_fetch_assoc($sql_abs[27])) {
                        $absent_employees .= $rows['emp_name'] . "<br>"; // concatenate each absent employee name with a line break
                      }
                    }

                    if ($HC27 != 0) {
                      $att = round(($HC27 / $ALL_PRODUCTION) * 100) . "%";
                      $abs = $ALL_PRODUCTION - $HC27;
                      echo "Present: $HC27" . "&nbsp;&nbsp;&nbsp;&nbsp;";
                      echo '<span class="d-inline-block" tabindex="0" data-bs-toggle="popover" title="Names" data-bs-html="true" data-bs-trigger="hover focus" data-bs-content="' . $absent_employees . ' " ><button type="button"  class="btn btn-primary btn-sm" disabled>Absent: ' . $abs . '</button></span>';
                      echo "<br>Attendance Rate: $att";
                    } else {
                      echo "No Work";
                    } ?></TD>
                <TD><?php if (!$sqldays_abs) {
                      // The query failed, handle the error here
                      echo "Error: " . mysqli_error($dbconnect);
                    } else {
                      // The query was successful, fetch the results and output the names
                      $absent_employees = ""; // initialize variable to store absent employees
                      while ($rows = mysqli_fetch_assoc($sql_abs[28])) {
                        $absent_employees .= $rows['emp_name'] . "<br>"; // concatenate each absent employee name with a line break
                      }
                    }

                    if ($HC28 != 0) {
                      $att = round(($HC28 / $ALL_PRODUCTION) * 100) . "%";
                      $abs = $ALL_PRODUCTION - $HC28;
                      echo "Present: $HC28" . "&nbsp;&nbsp;&nbsp;&nbsp;";
                      echo '<span class="d-inline-block" tabindex="0" data-bs-toggle="popover" title="Names" data-bs-html="true" data-bs-trigger="hover focus" data-bs-content="' . $absent_employees . ' " ><button type="button"  class="btn btn-primary btn-sm" disabled>Absent: ' . $abs . '</button></span>';
                      echo "<br>Attendance Rate: $att";
                    } else {
                      echo "No Work";
                    } ?></TD>
                <TD><?php if (!$sqldays_abs) {
                      // The query failed, handle the error here
                      echo "Error: " . mysqli_error($dbconnect);
                    } else {
                      // The query was successful, fetch the results and output the names
                      $absent_employees = ""; // initialize variable to store absent employees
                      while ($rows = mysqli_fetch_assoc($sql_abs[29])) {
                        $absent_employees .= $rows['emp_name'] . "<br>"; // concatenate each absent employee name with a line break
                      }
                    }

                    if ($HC29 != 0) {
                      $att = round(($HC29 / $ALL_PRODUCTION) * 100) . "%";
                      $abs = $ALL_PRODUCTION - $HC29;
                      echo "Present: $HC29" . "&nbsp;&nbsp;&nbsp;&nbsp;";
                      echo '<span class="d-inline-block" tabindex="0" data-bs-toggle="popover" title="Names" data-bs-html="true" data-bs-trigger="hover focus" data-bs-content="' . $absent_employees . ' " ><button type="button"  class="btn btn-primary btn-sm" disabled>Absent: ' . $abs . '</button></span>';
                      echo "<br>Attendance Rate: $att";
                    } else {
                      echo "No Work";
                    } ?></TD>
                <TD><?php if (!$sqldays_abs) {
                      // The query failed, handle the error here
                      echo "Error: " . mysqli_error($dbconnect);
                    } else {
                      // The query was successful, fetch the results and output the names
                      $absent_employees = ""; // initialize variable to store absent employees
                      while ($rows = mysqli_fetch_assoc($sql_abs[30])) {
                        $absent_employees .= $rows['emp_name'] . "<br>"; // concatenate each absent employee name with a line break
                      }
                    }

                    if ($HC30 != 0) {
                      $att = round(($HC30 / $ALL_PRODUCTION) * 100) . "%";
                      $abs = $ALL_PRODUCTION - $HC30;
                      echo "Present: $HC30" . "&nbsp;&nbsp;&nbsp;&nbsp;";
                      echo '<span class="d-inline-block" tabindex="0" data-bs-toggle="popover" title="Names" data-bs-html="true" data-bs-trigger="hover focus" data-bs-content="' . $absent_employees . ' " ><button type="button"  class="btn btn-primary btn-sm" disabled>Absent: ' . $abs . '</button></span>';
                      echo "<br>Attendance Rate: $att";
                    } else {
                      echo "No Work";
                    } ?></TD>
              </TR>

              <tr class="text-primary">
                <th style="background-color:#C0C0C0">31</th>
                <th style="background-color:#C0C0C0">REGULAR DAYS</th>
                <th style="background-color:#C0C0C0">OVERTIME DAYS</th>
                <th style="background-color:#C0C0C0">TOTAL WORKDAYS</th>
                <th style="background-color:#C0C0C0">LOG OT DAYS</th>

              </tr>
              <tr>
                <td><?php while ($row = mysqli_fetch_array($sqlday[31])) {
                      $Name31 = $row['Name'];
                      echo "$Name31 <br>";
                    }  ?></td>
                <td><?php while ($row = mysqli_fetch_array($sqlRegdays)) {
                      $Reg_days = $row['DATE'];
                      echo "$Reg_days <br>";
                    }  ?></td>
                <td><?php while ($row = mysqli_fetch_array($sqlOTdays)) {
                      $OT_days = $row['DATE'];
                      echo "$OT_days <br>";
                    }  ?></td>
                <td></td>
                <td>
                  <form id="OT_form">
                    <input type="date" name="OT_days">
                    <input type="submit" id="log_OT" name="log_OT" value="Add">&nbsp;&nbsp;
                    <input type="submit" id="del_OT" name="del_OT" value="Del">
                  </form>
                </td>
              </tr>
              <TR>
                <TD><?php if (!$sqldays_abs) {
                      // The query failed, handle the error here
                      echo "Error: " . mysqli_error($dbconnect);
                    } else {
                      // The query was successful, fetch the results and output the names
                      $absent_employees = ""; // initialize variable to store absent employees
                      while ($rows = mysqli_fetch_assoc($sql_abs[31])) {
                        $absent_employees .= $rows['emp_name'] . "<br>"; // concatenate each absent employee name with a line break
                      }
                    }

                    if ($HC31 != 0) {
                      $att = round(($HC31 / $ALL_PRODUCTION) * 100) . "%";
                      $abs = $ALL_PRODUCTION - $HC31;
                      echo "Present: $HC31" . "&nbsp;&nbsp;&nbsp;&nbsp;";
                      echo '<span class="d-inline-block" tabindex="0" data-bs-toggle="popover" title="Names" data-bs-html="true" data-bs-trigger="hover focus" data-bs-content="' . $absent_employees . ' " ><button type="button"  class="btn btn-primary btn-sm" disabled>Absent: ' . $abs . '</button></span>';
                      echo "<br>Attendance Rate: $att";
                    } else {
                      echo "No Work";
                    } ?></TD>
                <TD><?PHP echo " Total: $Total_Regdays"; ?> </TD>
                <TD><?PHP echo " Total: $Total_OTdays"; ?> </TD>

              </TR>
          </table>
        </div>
      </FORM>

      <script>
        $(document).ready(function() {
          $('#log_OT').click(function() {
            var formData = $('#OT_form').serialize();
            formData += '&log_OT=1';
            $.ajax({
              type: 'POST',
              url: '<?php echo $_SERVER['PHP_SELF']; ?>',
              data: formData,
              success: function(response) {
                // Handle the response from the server if needed
              }
            });
          });

          $('#del_OT').click(function() {
            var formData = $('#OT_form').serialize();
            formData += '&del_OT=1'; // Add a parameter to indicate which button was clicked
            $.ajax({
              type: 'POST',
              url: '<?php echo $_SERVER['PHP_SELF']; ?>',
              data: formData,
              success: function(response) {
                // Handle the response from the server if needed
              }
            });
          });
        });
      </script>

      <?php

      if (isset($_POST['log_OT'])) {
        $month = $_POST['month'];
        $year = $_POST['year'];
        $Dept = "Prod Main";
        $OT_days = $_POST['OT_days'];

        $dbconnect = mysqli_connect('localhost', 'root', '', 'ewip');

        $query = "UPDATE prod_dtr SET OT_Day='Yes' WHERE DATE='$OT_days' AND Department='$Dept' AND description!='INDIRECT ACTIVITY'";
        if (mysqli_query($dbconnect, $query)) {
          echo 'Update successful';
        } else {
          echo 'Error updating database: ' . mysqli_error($dbconnect);
        }
      }

      if (isset($_POST['del_OT'])) {
        $month = $_POST['month'];
        $year = $_POST['year'];
        $Dept = "Prod Main";
        $OT_days = $_POST['OT_days'];

        $dbconnect = mysqli_connect('localhost', 'root', '', 'ewip');

        $query = "UPDATE prod_dtr SET OT_Day='No' WHERE DATE='$OT_days' AND Department='$Dept'";
        if (mysqli_query($dbconnect, $query)) {
          echo 'Update successful';
        } else {
          echo 'Error updating database: ' . mysqli_error($dbconnect);
        }
      }
      ?>
      <script>
        // Initialize popovers
        var popoverTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="popover"]'))
        var popoverList = popoverTriggerList.map(function(popoverTriggerEl) {
          return new bootstrap.Popover(popoverTriggerEl)
        })
      </script>
    </div>
</body>
<script>
  function setUpDownloadPageAsImage() {
    var divId = "attendance";
    var targetDiv = document.getElementById(divId);

    html2canvas(targetDiv).then(function(canvas) {
      console.log(canvas);
      simulateDownloadImageClick(canvas.toDataURL(), "attendance_summary.png");
    });
  }

  function simulateDownloadImageClick(uri, filename) {
    var link = document.createElement("a");
    if (typeof link.download !== "string") {
      window.open(uri);
    } else {
      link.href = uri;
      link.download = filename;
      accountForFirefox(clickLink, link);
    }
  }

  function clickLink(link) {
    link.click();
  }

  function accountForFirefox(click) {
    // wrapper function
    let link = arguments[1];
    document.body.appendChild(link);
    click(link);
    document.body.removeChild(link);
  }

  function printDivContent() {
    const divContent = document.getElementById('attendance').innerHTML;
    const win = window.open('', '', 'height=700,width=700');
    win.document.write('<html><body>' + divContent + '</body></html>');
    win.document.close();
    win.print();
  }
</script>

</html>