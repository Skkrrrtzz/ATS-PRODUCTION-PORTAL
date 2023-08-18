<?php require_once '../db_connection.php';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $datefrom = $_POST['datefrom'];
    $dateto = $_POST['dateto'];
    $Dept = "Cable Assy";

    $total_wd_sql = mysqli_query($conn, "SELECT ID FROM dtr WHERE OT_day = 'No' AND Department = '$Dept' AND DATE BETWEEN '$datefrom' and '$dateto' GROUP BY DATE");
    $total_work_day = mysqli_num_rows($total_wd_sql);
    // Run the query to get the list of names
    $name_result = mysqli_query($conn, "SELECT user_ID,emp_name FROM user WHERE department = 'Cable Assy' AND role = 'operator' AND username NOT IN ('13394','13351','5555') group by emp_name order by emp_name");
    $total_headcount = mysqli_num_rows($name_result);
    // Initialize a variable to hold the total actual time for all names
    $total_actual_time_all_names = 0;
    $total_std_time_all_names = 0;
    $total_qty_all_names = 0;

    // Initialize an array to hold the total actual time and detailed actual time for each name
    $name_totals = array();

    // Loop through the list of names and lookup the corresponding total actual time
    while ($name_row = mysqli_fetch_assoc($name_result)) {
        $name = $name_row['emp_name'];

        // CHECKS THE PRESENTS
        $total_present_sql = mysqli_query($conn, "SELECT ID FROM dtr WHERE OT_day='No' AND Department='$Dept' AND Name='$name'  AND DATE BETWEEN '$datefrom' and '$dateto' GROUP BY DATE");
        $total_present_day = mysqli_num_rows($total_present_sql);

        // ATTENDANCE IDENTIFICATION
        if ($total_present_day != 0) {
            $total_present_day = "P";
        } else {
            $total_present_day = "A";
        }

        $all_total_present_day += mysqli_num_rows($total_present_sql);

        // ATTENDANCE EFFICIENCY
        if ($total_present_day || $total_work_day != 0) {
            $all_attendance = ($all_total_present_day / $total_headcount) * 100;
            $all_attendance_format = round($all_attendance);
        } else {
            $all_attendance_format = "0";
        }
        // GET THE DATA FOR STANDARD TIME 
        $sql_result = mysqli_query($conn, "SELECT dtr.Name,dtr.DATE,dtr.Duration AS total_actual_time,dtr.Qty_Make,dtr.Duration/dtr.Qty_Make AS detailed_total_act,dtr.wo_id,dtr.Part_No,dtr.Prod_Order_No,cable_cycletime.cycle_time,(CASE WHEN dtr.Duration/dtr.Qty_Make < cable_cycletime.cycle_time THEN dtr.Duration/dtr.Qty_Make ELSE cable_cycletime.cycle_time END) AS Std_time FROM (SELECT Name,DATE,SUM(Duration) AS Duration,Qty_Make,wo_id,Part_No,Stations,Code,Station_No,Labor_Type,Act_Start,Prod_Order_No FROM dtr WHERE DATE BETWEEN '$datefrom' and '$dateto' AND Name = '$name' AND Emp_ID NOT IN ('13394','13351') AND Qty_Make > 0 AND wo_status = 'IN-PROCESS' AND Stations!='FG TRANSACTION' AND Labor_Type='Reg_DL' AND Department = 'Cable Assy' GROUP BY Name, Prod_Order_No,Stations) AS dtr LEFT JOIN cable_cycletime ON dtr.Part_No = cable_cycletime.Part_No AND cable_cycletime.station=dtr.Stations GROUP BY dtr.Name, dtr.Prod_Order_No,dtr.Stations ORDER BY dtr.Name, dtr.Act_Start ASC");

        /* // Run the query and get the result set
                                $result = mysqli_query($conn, "SELECT Name,DATE,total_actual_time,Qty_Make,total_actual_time/Qty_Make AS detailed_total_act,wo_id,Part_No,Prod_Order_No FROM(SELECT Name, DATE, SUM(actual_time) AS total_actual_time,Qty_Make, wo_id, Part_No,Prod_Order_No FROM (SELECT Name, DATE, SUM(Duration) AS actual_time,Qty_Make, wo_id, Part_No, remarks, Code, Station_No, Labor_Type, Act_Start, Prod_Order_No, Duration FROM dtr WHERE DATE BETWEEN '2023-03-06' AND '2023-03-06' AND Name= '$name' AND Qty_Make > 0 AND wo_status ='IN-PROCESS' AND Department='Cable Assy' GROUP BY Name, Prod_Order_No) AS t GROUP BY Name, Prod_Order_No ORDER BY Name, Act_Start ASC) AS subquery"); */

        // Initialize a variable to hold the running total and detailed actual time for each Name
        $total_actual_time_by_name = 0;
        $detailed_actual_time = '';
        $total_std_time_by_name = 0;
        $total_actual_time = 0;
        $QTY_ = 0;


        // Loop through the result set and calculate the total actual time and detailed actual time for each Name
        while ($row = mysqli_fetch_assoc($sql_result)) {
            $name = $row['Name'];
            $std_time = $row['Std_time'];
            $qty_make = $row['Qty_Make'];
            $detailed_actual_time = $row['detailed_total_act'];
            $actual_time = $row['total_actual_time'] / 60;
            $total_actual_time += $actual_time;
            $total_actual_time_by_name += $actual_time;
            if ($std_time != 0) {
                $total_time = $std_time * $qty_make / 60;
            } else {
                $total_time = number_format($detailed_actual_time * $qty_make / 60, 2);
            }
            $total_std_time_by_name += $total_time;
            $QTY_ += $qty_make;
            //$QTY_ += number_format($std_time * $qty_make / 60, 2);
        }
        //echo number_format($total_std_time_by_name, 2);
        // Total actual_time and detailed_actual_time for each names
        $name_totals[$name] = array(
            'total_actual_time' => number_format($total_actual_time_by_name, 2),
            'total_std_time' => $total_std_time_by_name,
            'detailed_actual_time' => rtrim($detailed_actual_time, '; '),
            'total_present' => ($total_present_day),
            'QTY_MAKE' => $QTY_
        );

        // Add the actual_time value to the running total for all names
        $total_actual_time_all_names += $total_actual_time_by_name;
        $total_std_time_all_names += $total_std_time_by_name;
    }
    $total_percentage = 0;
    // Print out the total actual time and detailed actual time for each Name
    foreach ($name_totals as $name => $totals) {
        $total_std = number_format($totals['total_std_time'], 2);

        $name;
        $total_std;
        $totals['total_actual_time'];
        if ($totals['total_actual_time'] &&  $total_std != 0) {
            if ($totals['total_actual_time'] <  $total_std) {
                $percentage = 100;
            } else {
                $percentage = round(($total_std / $totals['total_actual_time']) * 100, 2);
            }
        } elseif ($totals['total_actual_time'] > 0) {
            $total_std = $totals['total_actual_time'];
            $percentage = 100;
        } else {
            $percentage = 0;
        }

        $percentage;
        $totals['total_present'];
        $Efficiency = round($total_percentage += $percentage, 2);
    }
    if ($total_std_time_all_names && $total_actual_time_all_names != 0) {
        $total_eff = round(($total_std_time_all_names / $total_actual_time_all_names) * 100, 2);
    } else {
        $total_eff = 0;
    }
    // Create an array to store all the data
    $data = array(
        "total_work_day" => $total_work_day,
        "total_headcount" => $total_headcount,
        "total_percentage" => $percentage,
        "total_eff" => $total_eff,
        "names" => array()
    );
    // Loop through the name_totals array and add each name's data to the "names" array
    foreach ($name_totals as $name => $totals) {
        $data["names"][] = array(
            "name" => $name,
            "total_std_time" => $totals['total_std_time'],
            "total_actual_time" => $totals['total_actual_time'],
            "total_present" => $totals['total_present'],
            "efficiency_percentage" => $totals['efficiency_percentage'],
            "QTY_MAKE" => $totals['QTY_MAKE']
        );
    }



    $wosql_data = mysqli_query($conn, "SELECT Name,wo_id,Qty_Make,Stations,Part_No,Activity,Duration,remarks,SUM(Duration) AS TOTALD,Name,Prod_Order_No,Station_No,Code,Act_Start,Act_End FROM dtr WHERE DATE BETWEEN '$datefrom' and '$dateto' AND Qty_Make > 0 AND wo_status = 'IN-PROCESS' AND Stations!='FG TRANSACTION' AND Labor_Type='Reg_DL' AND Emp_ID NOT IN ('13394','13351') AND Department='$Dept' AND Duration!='' GROUP BY Name,Prod_Order_No,Stations,Activity ORDER BY Name,Act_Start asc");

    while ($row = mysqli_fetch_array($wosql_data)) {
        $wo_id    = $row['wo_id'];
        $wo_qty   = $row['Qty_Make'];
        $stations = $row['Stations'];
        $part_no = $row['Part_No'];
        $detailed_duration = $row['TOTALD'];
        $detailed_actual = $detailed_duration / $wo_qty;


        $module_sql = mysqli_query($conn, "SELECT module FROM wo WHERE wo_id ='$wo_id'");
        //  while($_module_row = mysqli_fetch_array( $module_sql)){
        $_module_row = mysqli_fetch_array($module_sql);
        $_module_code = $_module_row['module'];
        if ($_module_code == '18204CH') {
            $_module = 'PNP';
        } elseif ($_module_code == '18203CH') {
            $_module = 'JLP';
        } elseif ($_module_code == '18207CH') {
            $_module = 'OLB';
        } elseif ($_module_code == '0720TN') {
            $_module = 'TERADYNE';
        } elseif ($_module_code == '1820CH') {
            $_module = 'SPARES';
        } else {
            $_module = 'JTP';
        }

        $standard_time_sql = mysqli_query($conn, "SELECT cycle_time FROM cable_cycletime WHERE station = '$stations' AND product = '$_module' AND part_no='$part_no'");
        $std_time = mysqli_num_rows($standard_time_sql);
        $std_row = mysqli_fetch_array($standard_time_sql);
        $detailed_std = $std_row['cycle_time'];

        if ($detailed_actual != 0) {
            if ($detailed_actual < $detailed_std) {
                $detailed_std = $detailed_actual;
                $detailed_eff = "100.00";
            } elseif ($detailed_std == 0) {
                $detailed_std = $detailed_actual;
                $detailed_eff = "100.00";
            } else {
                $detailed_eff = round(($detailed_std / $detailed_actual) * 100, 2);
            }
        } else {
            $detailed_eff = 0;
        }
        $row['Name'];
        $row['Stations'];
        $row['Station_No'];
        $row['Prod_Order_No'];
        $_module;
        $row['Part_No'];
        $row['Code'] . "-" . $row['Activity'];
        $row['Act_Start'];
        $row['Act_End'];
        number_format($detailed_duration, 2);
        number_format($detailed_std * $row['Qty_Make'], 2);
        $row['Qty_Make'];
        number_format($detailed_actual, 2);
        number_format($detailed_std, 2);
        $detailed_eff;
    }

    // Convert the data array to JSON
    $json_data = json_encode($data);
    echo $json_data;
}
