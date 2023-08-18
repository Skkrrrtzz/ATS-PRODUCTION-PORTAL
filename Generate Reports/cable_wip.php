<?php include '../ATS_Prod_Header.php'; ?>
<?php include '../PROD_navbar.php'; ?>
<!DOCTYPE html>
<html>

<head>
  <!-- <meta http-equiv="refresh" content="60"> -->
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>WIP CABLE</title>
  <script src="../assets/js/table2excel.js"></script>
</head>

<body>

  <center>
    <h1>WORK IN PROGRESS</h1>
    <div class="text-end p-1">
      <button class="btn btn-success btn-sm" id="Export">Export to Excel</button>
    </div>
    <div class="table-responsive">
      <table class="table table-sm table-bordered table-hover display compact" id="eclipse1">
        <thead>
          <tr class="table-primary text-center">
            <th>STATIONS</th>
            <th>OPERATOR</th>
            <th>PROD NO</th>
            <th>PART NO</th>
            <th>QTY</th>
            <th>ACTIVITY</th>
            <th>REMARKS</th>
            <th>STARTED</th>
            <th>ONGOING(minutes)</th>
          </tr>
        </thead>
        <tbody>
          <?php
          $categories = [
            'IN-PROCESS' => [
              'name' => 'DIRECT LABOR',
              'color' => 'text-success'
            ],
            'INDIRECT' => [
              'name' => 'INDIRECT LABOR',
              'color' => 'text-warning'
            ],
            'MH' => [
              'name' => 'MATERIAL HANDLER',
              'color' => 'text-warning'
            ]
          ];

          foreach ($categories as $category => $data) {
            $categoryName = $data['name'];
            $color = $data['color'];
            $query = "SELECT Name, Stations, Station_No, Part_No, Prod_Order_No, Act_Start, Qty_Make, Activity, remarks, NOW() as timer
                          FROM dtr
                          WHERE Duration = '' AND Act_Start != '' AND wo_status = '$category'
                          ORDER BY Stations";

            $result = mysqli_query($conn, $query);

            echo "<tr>
                        <th colspan='9' class='text-center $color bg-secondary-subtle'>$categoryName</th>
                      </tr>";

            while ($row = mysqli_fetch_array($result)) {
              $now = $row['timer'];
              $Start = strtotime($row['Act_Start']);
              $End = strtotime($now);
              $Duration = ($End - $Start) / 60;
              $total = number_format($Duration);

              echo "<tr class='fw-bold text-center table-bordered'>";
              echo "<td>" . $row['Stations'] . " &nbsp; " . $row['Station_No'] . "</td>";
              echo "<td>" . $row['Name'] . "</td>";
              echo "<td>" . $row['Prod_Order_No'] . "</td>";
              echo "<td>" . $row['Part_No'] . "</td>";
              echo "<td>" . $row['Qty_Make'] . "</td>";
              echo "<td>" . $row['Activity'] . "</td>";
              echo "<td>" . $row['remarks'] . "</td>";
              echo "<td>" . $row['Act_Start'] . "</td>";
              echo "<td>" . $total . "</td>";
              echo "</tr>";
            }
          }
          ?>
        </tbody>
      </table>
    </div>
</body>
<script>
  // Function to refresh the table using AJAX
  function refreshTable() {
    $.ajax({
      url: "",
      dataType: "html",
      success: function(data) {
        // Replace the existing table with the updated one
        var updatedTable = $(data).find("#eclipse");
        $("#eclipse").html(updatedTable);
      }
    });
  }
  // Refresh the table every 5 seconds (adjust the interval as needed)
  setInterval(refreshTable, 5000);

  document.getElementById('Export').addEventListener('click', function() {
    var table2excel = new Table2Excel();
    table2excel.export(document.querySelectorAll("#eclipse"));
  });
</script>

</html>