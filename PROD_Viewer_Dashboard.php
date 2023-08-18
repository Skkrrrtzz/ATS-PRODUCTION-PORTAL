<?php
$conn = mysqli_connect("localhost", "root", "") or die("Could not connect");
mysqli_select_db($conn, "ewip") or die("could not connect database");

$operator_eff = 0;
$technicians_eff = 0;
$ATT_OVERALL = 0;
$ALL_EFF = 0;
$all_abs = 0;
$main = 0;
$cable = 0;
$month = date("F");
$date = date("Y-m-d");


function getUpdatedInProcessValues($conn)
{
    // Retrieve the updated value for $total_main_inprocess
    $sql_main_inprocess = mysqli_query($conn, "SELECT ID FROM `prod_dtr` WHERE wo_status='IN-PROCESS' AND description!='INDIRECT ACTIVITY' AND product IN('JLP','PNP','PNP IO','OLB','JTP')");
    $total_main_inprocess = mysqli_num_rows($sql_main_inprocess);

    // Retrieve the updated value for $total_cable_inprocess
    $sql_cable_inprocess = mysqli_query($conn, "SELECT ID FROM dtr  WHERE Duration = '' AND Act_Start !='' AND wo_status='IN-PROCESS' ORDER BY Stations");
    $total_cable_inprocess = mysqli_num_rows($sql_cable_inprocess);

    // Retrieve the updated value for $total_cable_indirect
    $sql_cable_indirect = mysqli_query($conn, "SELECT ID FROM dtr  WHERE Duration = '' AND Act_Start !='' AND wo_status='INDIRECT' ORDER BY Stations");
    $total_cable_indirect = mysqli_num_rows($sql_cable_indirect);

    // Return the updated values as an associative array
    $updatedValues = [
        'total_cable_inprocess' => $total_cable_inprocess,
        'total_main_inprocess' => $total_main_inprocess,
        'total_cable_indirect' => $total_cable_indirect
    ];

    return $updatedValues;
}
// Call the function and retrieve the updated values
$updatedValues = getUpdatedInProcessValues($conn);

// Check if the variable is defined
if (isset($updatedValues) && basename($_SERVER['SCRIPT_FILENAME']) === basename(__FILE__)) {
    // Variable is defined and the script is being accessed directly, use it
    echo json_encode($updatedValues);
} else {
    // Variable is not defined yet or the script is included, return an empty response or handle the situation accordingly

}

$sql_pnp_idle = mysqli_query($conn, "SELECT ID FROM `prod_module` WHERE wo_status='IDLE' AND description!='INDIRECT ACTIVITY'AND module='PNP' AND product='PNP IO' AND date_updated='$date'");
$total_pnp_idle = mysqli_num_rows($sql_pnp_idle);

// OLB INPROCESS & IDLE
$sql_olb_inprocess = mysqli_query($conn, "SELECT ID FROM `prod_module` WHERE wo_status='IN-PROCESS' AND description!='INDIRECT ACTIVITY'AND module='OLB' AND product='OLB'");
$total_olb_inprocess = mysqli_num_rows($sql_olb_inprocess);

$sql_olb_idle = mysqli_query($conn, "SELECT ID FROM `prod_module` WHERE wo_status='IDLE' AND description!='INDIRECT ACTIVITY'AND module='OLB' AND product='OLB'");
$total_olb_idle = mysqli_num_rows($sql_olb_idle);

// JTP INPROCESS & IDLE
$sql_jtp_inprocess = mysqli_query($conn, "SELECT ID FROM `prod_module` WHERE wo_status='IN-PROCESS' AND description!='INDIRECT ACTIVITY'AND module='JTP' AND product='JTP'");
$total_jtp_inprocess = mysqli_num_rows($sql_jtp_inprocess);

$sql_jtp_idle = mysqli_query($conn, "SELECT ID FROM `prod_module` WHERE wo_status='IDLE' AND description!='INDIRECT ACTIVITY'AND module='JTP' AND product='JTP'");
$total_jtp_idle = mysqli_num_rows($sql_jtp_idle);

function getDefaultDate()
{
    return isset($_GET['date']) ? $_GET['date'] : date('Y-m-d');
}

$defaultDate = getDefaultDate();
$date = $defaultDate; // Assign the default date initially

// Check if a different date is selected
if (isset($_GET['date']) && $_GET['date'] != $defaultDate) {
    $date = $_GET['date']; // Assign the selected date
}

function getWeeklyDates($startDate)
{
    $weeklyDates = [];
    $currentDate = $startDate;

    // Get the date of Monday for the given week
    $monday = date('Y-m-d', strtotime('monday this week', strtotime($currentDate)));

    // Get the date of Saturday for the given week
    $saturday = date('Y-m-d', strtotime('friday this week', strtotime($currentDate)));

    // Loop through the days from Monday to Saturday
    while ($monday <= $saturday) {
        $weeklyDates[] = $monday;
        $monday = date('Y-m-d', strtotime('+1 day', strtotime($monday)));
    }

    return $weeklyDates;
}

$weeklyDates = getWeeklyDates($date);
$yearly = date("Y");

$cableData = getWeeklyAttendanceData($conn, 'cable');
$mainData = getWeeklyAttendanceData($conn, 'main');

$cableYearlyData = getYearlyAttendanceData($conn, 'cable');
$mainYearlyData = getYearlyAttendanceData($conn, 'main');
// ATTENDANCE
// SELECT DATA FROM user TABLE
$technician_sql = mysqli_query($conn, "SELECT user_ID, emp_name, username FROM user WHERE Department IN ('Prod Main', 'Production Main') AND username NOT IN ('4444', '13472', '947','11742','2023') GROUP BY emp_name ORDER BY emp_name");
$technicians = mysqli_num_rows($technician_sql);

$operator_sql = mysqli_query($conn, "SELECT user_ID, emp_name FROM user WHERE department = 'Cable Assy' AND role = 'operator' AND username NOT IN ('12379', '13394', '13351', '5555') GROUP BY emp_name ORDER BY emp_name");
$operators = mysqli_num_rows($operator_sql);

// SELECT PRESENT DATA FROM prod_attendance TABLE
$present_opr_sql = mysqli_query($conn, "SELECT * FROM `prod_attendance` WHERE Department='CABLE ASSY' AND DATE ='$date' AND Emp_ID NOT IN ('5555', '13640', '12379', '13394', '13351') ORDER BY `DATE`");
$present_operators = mysqli_num_rows($present_opr_sql);

$present_tech_sql = mysqli_query($conn, "SELECT * FROM `prod_attendance` WHERE Department IN ('Production Main', 'Prod Main') AND DATE ='$date' AND Emp_ID NOT IN ('4444', '13472', '947','2023','11742') ORDER BY `DATE`");
$present_technicians = mysqli_num_rows($present_tech_sql);

function getWeeklyAttendanceData($conn, $type)
{
    // Retrieve weekly attendance data based on the selected date or default date
    $weeklyData = array();

    // Modify the SQL queries to fetch the weekly data for the respective type (cable or main)
    if ($type === 'cable') {
        $operator_sql = mysqli_query($conn, "SELECT user_ID,emp_name FROM user WHERE department = 'Cable Assy' AND role = 'operator' AND username NOT IN ('12379','13394','13351','5555') GROUP BY emp_name ORDER BY emp_name");
        $operators = mysqli_num_rows($operator_sql);

        $weekly_sql = mysqli_query($conn, "SELECT DATE, COUNT(*) as count FROM prod_attendance WHERE Department = 'CABLE ASSY' AND WEEK(DATE) = WEEK(NOW()) AND Emp_ID NOT IN ('5555', '13640', '12379', '13394', '13351') GROUP BY DATE ORDER BY DATE");
    } elseif ($type === 'main') {
        $technician_sql = mysqli_query($conn, "SELECT user_ID,emp_name,username FROM user WHERE Department IN ('Prod Main','Production Main') AND username NOT IN ('4444','13472','947','2023','11742') GROUP BY emp_name ORDER BY emp_name");
        $technicians = mysqli_num_rows($technician_sql);

        $weekly_sql = mysqli_query($conn, "SELECT DATE, COUNT(*) as count FROM prod_attendance WHERE Department IN ('Production Main', 'Prod Main') AND WEEK(DATE) = WEEK(NOW()) AND Emp_ID NOT IN ('4444', '13472', '947','2023','11742') GROUP BY DATE ORDER BY DATE");
    }

    // Fetch and store the weekly attendance data in an array
    while ($row = mysqli_fetch_assoc($weekly_sql)) {
        if ($type === 'cable') {
            $weeklyData[] = number_format($row['count'] / $operators * 100, 2);
        } elseif ($type === 'main') {
            $weeklyData[] = number_format($row['count'] / $technicians * 100, 2);
        }
    }

    // Return the array
    return $weeklyData;
}

function getYearlyAttendanceData($conn, $type)
{
    // Retrieve yearly attendance data based on the selected date or default date
    $yearlyData = array();

    // Modify the SQL queries to fetch the yearly data for the respective type (cable or main)
    if ($type === 'cable') {
        $operator_sql = mysqli_query($conn, "SELECT user_ID,emp_name FROM user WHERE department = 'Cable Assy' AND role = 'operator' AND username NOT IN ('12379','13394','13351','5555') GROUP BY emp_name ORDER BY emp_name");
        $operators = mysqli_num_rows($operator_sql);

        $yearly_sql = mysqli_query($conn, "SELECT DATE, COUNT(*) as count FROM prod_attendance WHERE Department = 'CABLE ASSY' AND YEAR(DATE) = YEAR(NOW()) AND Emp_ID NOT IN ('5555', '13640', '12379', '13394', '13351') GROUP BY MONTH(DATE) ORDER BY MONTH(DATE)");
    } elseif ($type === 'main') {
        $technician_sql = mysqli_query($conn, "SELECT user_ID,emp_name,username FROM user WHERE Department IN ('Prod Main','Production Main') AND username NOT IN ('4444','13472','947','11742') GROUP BY emp_name ORDER BY emp_name");
        $technicians = mysqli_num_rows($technician_sql);

        $yearly_sql = mysqli_query($conn, "SELECT DATE, COUNT(*) as count FROM prod_attendance WHERE Department IN ('Production Main', 'Prod Main') AND YEAR(DATE) = YEAR(NOW()) AND Emp_ID NOT IN ('4444', '13472', '947') GROUP BY MONTH(DATE) ORDER BY MONTH(DATE)");
    }

    // Fetch and store the yearly attendance data in an array
    while ($row = mysqli_fetch_assoc($yearly_sql)) {
        if ($type === 'cable') {
            $yearlyData[] = number_format($row['count'] / $operators * 100, 2);
        } elseif ($type === 'main') {
            $yearlyData[] = number_format($row['count'] / $technicians * 100, 2);
        }
    }

    // Return the array
    return $yearlyData;
}
$main = number_format(($present_technicians / $technicians) * 100, 2); // FORMULA FOR MAIN ATTENDANCE
$cable = number_format(($present_operators / $operators) * 100, 2); // FORMULA FOR CABLE ATTENDANCE

$all_present = $present_technicians + $present_operators; // TOTAL PRESENT USERS
$all = $operators + $technicians; // TOTAL USERS
$all_abs = $all - $all_present; // TOTAL ABSENT

// OVERALL ATTENDANCE PERCENTAGE
if ($main != 0 && $cable != 0) {
    $OVERALL_ATT = ($all_present / $all) * 100;
} else {
    $OVERALL_ATT = 0;
}
$ATT_OVERALL = number_format($OVERALL_ATT, 2);

// SELECT CABLE ASSY USERS FROM USER TABLE
$sql_total_operator = mysqli_query($conn, "SELECT user_ID,emp_name FROM user WHERE department = 'Cable Assy' AND role = 'operator' AND username NOT IN ('12379','13394','2008','13351','5555') GROUP BY emp_name ORDER BY emp_name");
$total_operator = mysqli_num_rows($sql_total_operator);

?>

<?php
// Initialize a variable to hold the total actual time for all names
$total_actual_time_all_names = 0;
$total_std_time_all_names = 0;
// Initialize an array to hold the total actual time and detailed actual time for each name
$name_totals = array();
// CABLE EFFICIENCY SUMMARY
while ($row = mysqli_fetch_array($sql_total_operator)) {
    $name = $row['emp_name'];

    //GET THE ACTUAL DIRECT LABOR HOURS BASED ON ACTUAL PROCESSED PART per EMPLOYEE
    $sql_result = mysqli_query($conn, "SELECT dtr.Name,dtr.DATE,dtr.Duration AS total_actual_time,dtr.Qty_Make,dtr.Duration/dtr.Qty_Make AS detailed_total_act,dtr.wo_id,dtr.Part_No,dtr.Prod_Order_No,cable_cycletime.cycle_time,(CASE WHEN dtr.Duration/dtr.Qty_Make < cable_cycletime.cycle_time THEN dtr.Duration/dtr.Qty_Make ELSE cable_cycletime.cycle_time END) AS Std_time FROM (SELECT Name,DATE,SUM(Duration) AS Duration,Qty_Make,wo_id,Part_No,Stations,Code,Station_No,Labor_Type,Act_Start,Prod_Order_No FROM dtr WHERE DATE BETWEEN '$date' and '$date' AND Name = '$name' AND Emp_ID NOT IN ('12379','11679','13394','2008','13351') AND Qty_Make > 0 AND wo_status = 'IN-PROCESS' AND Stations!='FG TRANSACTION' AND Labor_Type='Reg_DL' AND Department = 'Cable Assy' GROUP BY Name, Prod_Order_No,Stations) AS dtr LEFT JOIN cable_cycletime ON dtr.Part_No = cable_cycletime.Part_No AND cable_cycletime.station=dtr.Stations GROUP BY dtr.Name, dtr.Prod_Order_No,dtr.Stations ORDER BY dtr.Name, dtr.Act_Start ASC");

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
        //$QTY_ += number_format($std_time * $qty_make / 60, 2);
    }
    $name_totals[$name] = array(
        'total_actual_time' => number_format($total_actual_time_by_name, 2),
        'total_std_time' => $total_std_time_by_name,
        'detailed_actual_time' => rtrim($detailed_actual_time, '; ')
    );

    // Add the actual_time value to the running total for all names
    $total_actual_time_all_names += $total_actual_time_by_name;
    $total_std_time_all_names += $total_std_time_by_name;
}
if ($total_std_time_all_names && $total_actual_time_all_names != 0) {
    $operator_eff = round(($total_std_time_all_names / $total_actual_time_all_names) * 100, 2);
} else {
    $operator_eff = 0;
}


function OVRPRODUCTEFF($conn, $date)
{
    $result = [];
    // QUERY FOR HEADCOUNT
    $jlp_hc_sql = mysqli_query($conn, "SELECT COUNT(DISTINCT Name) AS total_headcount FROM prod_dtr WHERE Department = 'Prod Main' AND Act_End != '' AND Name != 'TECHNICIAN' AND product='JLP' AND description != 'INDIRECT ACTIVITY' AND Emp_ID NOT IN ('4444','11451') AND DATE BETWEEN '$date' AND '$date'");
    $jlp_hc_row = mysqli_fetch_assoc($jlp_hc_sql);
    $total_hc_jlp = $jlp_hc_row['total_headcount'];

    // QUERY FOR EMPLOYEES
    $jlp_sql_data = mysqli_query(
        $conn,
        "SELECT Name,Qty_Make,cycle_time,SUM(build_percent/100 *Qty_Make) AS build_percent,Stations,description,Part_No,Duration,Prod_Order_No,batch_no,Code,Act_Start,Act_End,remarks,product FROM prod_dtr WHERE DATE BETWEEN '$date' AND '$date' AND Qty_Make > 0 AND product = 'JLP' AND Emp_ID NOT IN ('11451','4444') AND Act_End != '' AND Labor_Type != 'Prod_Reg_ID' GROUP BY Name,description ORDER BY Name ASC"
    );

    $array1 = []; // Array to store cycle time values
    $array2 = []; // Array to store build percent values
    $result_jlp = [];

    while ($jlp_row = mysqli_fetch_array($jlp_sql_data)) {
        $jlp_std = $jlp_row['cycle_time'];
        $jlp_stations = $jlp_row['Stations'];
        $jlp = $jlp_row['product'];
        $jlp_bp = number_format($jlp_row['build_percent'], 2);
        $jlp_output = $jlp_bp;

        if ($jlp_stations == 'FVI MODULE') {
            $std_cycle_time = "3.00";
            if ($jlp_output == 1) {
                $jlp_output = '0.89';
            }
        } elseif ($jlp_stations == 'SUB TEST') {
            $std_cycle_time = "2.00";
        } else {
            $std_cycle_time = $jlp_std;
        }

        $jlp_detailed_std = $std_cycle_time != 0 ? number_format(8 / $std_cycle_time, 2) : 0;
        $jlp_detailed_output = $jlp_output != 0 ? $jlp_detailed_std * $jlp_output : 0;

        $array1[] = $std_cycle_time;
        $array2[] = $jlp_output;

        $JLP_STDxOUTPUT = array_sum(array_map(function ($a, $b) {
            return $a * $b;
        }, $array1, $array2));

        $JLP_WHxMP = 8 * $total_hc_jlp;
        $JLP_TOTALEFF = $JLP_WHxMP != 0 ? number_format(($JLP_STDxOUTPUT / $JLP_WHxMP) * 100, 2) : 0;

        $result_jlp[] = [
            'JLP_WHxMP' => $JLP_WHxMP,
            'JLP_STDxOUTPUT' => $JLP_STDxOUTPUT,
            'JLP_OVERALLEFF' => $JLP_TOTALEFF,
            'JLP_TOTALHC' => $total_hc_jlp,
            'product_jlp' => $jlp, 'jlp_detailed_output' => $jlp_detailed_output //thiss error
        ];
    }
    $result[] = $result_jlp;
    // QUERY FOR HEADCOUNT
    $pnp_hc_sql = mysqli_query($conn, "SELECT COUNT(DISTINCT Name) AS total_headcount FROM prod_dtr WHERE Department = 'Prod Main' AND Act_End != '' AND Name != 'TECHNICIAN' AND product='PNP' AND description != 'INDIRECT ACTIVITY' AND Emp_ID != '11451' AND DATE BETWEEN '$date' AND '$date'");
    $pnp_hc_row = mysqli_fetch_assoc($pnp_hc_sql);
    $total_hc_pnp = $pnp_hc_row['total_headcount'];

    // QUERY FOR EMPLOYEES
    $pnp_sql_data = mysqli_query($conn, "SELECT Name,Qty_Make,cycle_time,SUM(build_percent/100 *Qty_Make) AS build_percent,Stations,description,Part_No,Duration,Prod_Order_No,batch_no,Code,Act_Start,Act_End,remarks,product FROM prod_dtr WHERE DATE BETWEEN '$date' AND '$date' AND Qty_Make > 0 AND product = 'PNP' AND Emp_ID NOT IN ('4444','11451') AND Labor_Type != 'Prod_Reg_ID' GROUP BY Name,description ORDER BY Name ASC");

    $array1 = []; // Array to store cycle time values
    $array2 = []; // Array to store build percent values
    $result_pnp = [];

    while ($pnp_row = mysqli_fetch_array($pnp_sql_data)) {

        $pnp_std = $pnp_row['cycle_time'];
        $pnp_bp = $pnp_row['build_percent'];
        $pnp = $pnp_row['product'];

        $array1[] = $pnp_std;
        $array2[] = $pnp_bp;

        $PNP_STDxOUTPUT = array_sum(array_map(function ($a, $b) {
            return $a * $b;
        }, $array1, $array2));

        $PNP_STDxOUTPUT;
        $PNP_WHxMP = 8 * $total_hc_pnp;
        $PNP_TOTALEFF = $PNP_WHxMP != 0 ? number_format(($PNP_STDxOUTPUT / $PNP_WHxMP) * 100, 2) : 0;

        $result_pnp[] = [
            'PNP_WHxMP' => $PNP_WHxMP,
            'PNP_STDxOUTPUT' => $PNP_STDxOUTPUT,
            'PNP_OVERALLEFF' => $PNP_TOTALEFF,
            'PNP_TOTALHC' => $total_hc_pnp,
            'product_pnp' => $pnp
        ];
    }
    $result[] = $result_pnp;
    return $result;
}
$results = OVRPRODUCTEFF($conn, $date, $date);
$result_jlp = $results[0]; // Get JLP results 
$TOTALHC_jlp = null;
$WHxMP_jlp = 0;
$OVERALLEFF_jlp = null;
$STDxOUTPUT_jlp = null;
$TOTALHC_pnp = null;
$WHxMP_pnp = 0;
$OVERALLEFF_pnp = null;
$STDxOUTPUT_pnp = null;
// for future purpose (OLB AND JTP)
$STDxOUTPUT_olb = null;
$STDxOUTPUT_jtp = null;
$WHxMP_olb = null;
$WHxMP_jtp = null;

foreach ($result_jlp as $results) {
    if (isset($results['JLP_STDxOUTPUT']) && isset($results['JLP_WHxMP']) && isset($results['JLP_TOTALHC']) && isset($results['JLP_OVERALLEFF'])) {
        $STDxOUTPUT_jlp = $results['JLP_STDxOUTPUT'];
        $WHxMP_jlp = $results['JLP_WHxMP'];
        $TOTALHC_jlp = $results['JLP_TOTALHC'];
        $OVERALLEFF_jlp = $results['JLP_OVERALLEFF'];
    }
}
$OVR_STDxOUTPUT = $STDxOUTPUT_jlp + $STDxOUTPUT_pnp + $STDxOUTPUT_olb + $STDxOUTPUT_jtp;
$OVER_WHxMP = round($WHxMP_jlp + $WHxMP_pnp + $WHxMP_olb + $WHxMP_jtp);

if ($OVR_STDxOUTPUT != 0 && $OVER_WHxMP != 0) {
    $OVR_EFF = round($OVR_STDxOUTPUT / $OVER_WHxMP * 100);
} else {
    $OVR_EFF = 0;
}

$WHxMP_Cable = $total_actual_time_all_names; // CABLE PRESENT & WH
$WHxMP_Main = $OVER_WHxMP; // MAIN PRESENT & WH
$STDxOUTPUT_OVERALL = number_format($OVR_STDxOUTPUT + $total_std_time_all_names, 2); // STD X OUTPUT OVR
$WHxMP_OVERALL = $WHxMP_Main + $WHxMP_Cable; // WORKING HR X MP

// FORMULA EFFICIENCY OVERALL
if ($STDxOUTPUT_OVERALL != 0) {
    $ALL_EFF = number_format(($STDxOUTPUT_OVERALL / $WHxMP_OVERALL) * 100, 2);
} else {
    $ALL_EFF = 0;
}
$mainmatrix_query = "SELECT
  SUM(CASE WHEN CDA = 1 THEN 1 ELSE 0 END) AS CDA_Count_1,
  SUM(CASE WHEN CDA = 2 THEN 1 ELSE 0 END) AS CDA_Count_2,
  SUM(CASE WHEN CDA = 3 THEN 1 ELSE 0 END) AS CDA_Count_3,
  COUNT(CASE WHEN CDA IN (1, 2, 3) THEN Name END) AS CDA_Total,
  SUM(CASE WHEN CDM = 1 THEN 1 ELSE 0 END) AS CDM_Count_1,
  SUM(CASE WHEN CDM = 2 THEN 1 ELSE 0 END) AS CDM_Count_2,
  SUM(CASE WHEN CDM = 3 THEN 1 ELSE 0 END) AS CDM_Count_3,
  COUNT(CASE WHEN CDM IN (1, 2, 3) THEN Name END) AS CDM_Total,
  SUM(CASE WHEN TSL = 1 THEN 1 ELSE 0 END) AS TSL_Count_1,
  SUM(CASE WHEN TSL = 2 THEN 1 ELSE 0 END) AS TSL_Count_2,
  SUM(CASE WHEN TSL = 3 THEN 1 ELSE 0 END) AS TSL_Count_3,
  COUNT(CASE WHEN TSL IN (1, 2, 3) THEN Name END) AS TSL_Total,
  SUM(CASE WHEN FA = 1 THEN 1 ELSE 0 END) AS FA_Count_1,
  SUM(CASE WHEN FA = 2 THEN 1 ELSE 0 END) AS FA_Count_2,
  SUM(CASE WHEN FA = 3 THEN 1 ELSE 0 END) AS FA_Count_3,
  COUNT(CASE WHEN FA IN (1, 2, 3) THEN Name END) AS FA_Total,
  SUM(CASE WHEN TXP = 1 THEN 1 ELSE 0 END) AS TXP_Count_1,
  SUM(CASE WHEN TXP = 2 THEN 1 ELSE 0 END) AS TXP_Count_2,
  SUM(CASE WHEN TXP = 3 THEN 1 ELSE 0 END) AS TXP_Count_3,
  COUNT(CASE WHEN TXP IN (1, 2, 3) THEN Name END) AS TXP_Total,
  SUM(CASE WHEN AC = 1 THEN 1 ELSE 0 END) AS AC_Count_1,
  SUM(CASE WHEN AC = 2 THEN 1 ELSE 0 END) AS AC_Count_2,
  SUM(CASE WHEN AC = 3 THEN 1 ELSE 0 END) AS AC_Count_3,
  COUNT(CASE WHEN AC IN (1, 2, 3) THEN Name END) AS AC_Total,
  SUM(CASE WHEN FC = 1 THEN 1 ELSE 0 END) AS FC_Count_1,
  SUM(CASE WHEN FC = 2 THEN 1 ELSE 0 END) AS FC_Count_2,
  SUM(CASE WHEN FC = 3 THEN 1 ELSE 0 END) AS FC_Count_3,
  COUNT(CASE WHEN FC IN (1, 2, 3) THEN Name END) AS FC_Total,
  SUM(CASE WHEN MTP = 1 THEN 1 ELSE 0 END) AS MTP_Count_1,
  SUM(CASE WHEN MTP = 2 THEN 1 ELSE 0 END) AS MTP_Count_2,
  SUM(CASE WHEN MTP = 3 THEN 1 ELSE 0 END) AS MTP_Count_3,
  COUNT(CASE WHEN MTP IN (1, 2, 3) THEN Name END) AS MTP_Total,
  SUM(CASE WHEN ION = 1 THEN 1 ELSE 0 END) AS ION_Count_1,
  SUM(CASE WHEN ION = 2 THEN 1 ELSE 0 END) AS ION_Count_2,
  SUM(CASE WHEN ION = 3 THEN 1 ELSE 0 END) AS ION_Count_3,
  COUNT(CASE WHEN ION IN (1, 2, 3) THEN Name END) AS ION_Total,
  SUM(CASE WHEN FLIP = 1 THEN 1 ELSE 0 END) AS FLIP_Count_1,
  SUM(CASE WHEN FLIP = 2 THEN 1 ELSE 0 END) AS FLIP_Count_2,
  SUM(CASE WHEN FLIP = 3 THEN 1 ELSE 0 END) AS FLIP_Count_3,
  COUNT(CASE WHEN FLIP IN (1, 2, 3) THEN Name END) AS FLIP_Total,
  SUM(CASE WHEN INTEGRATION = 1 THEN 1 ELSE 0 END) AS INTEGRATION_Count_1,
  SUM(CASE WHEN INTEGRATION = 2 THEN 1 ELSE 0 END) AS INTEGRATION_Count_2,
  SUM(CASE WHEN INTEGRATION = 3 THEN 1 ELSE 0 END) AS INTEGRATION_Count_3,
  COUNT(CASE WHEN INTEGRATION IN (1, 2, 3) THEN Name END) AS INTEGRATION_Total,
  SUM(CASE WHEN PNP_SUB_ASSY = 1 THEN 1 ELSE 0 END) AS PNP_SUB_ASSY_Count_1,
  SUM(CASE WHEN PNP_SUB_ASSY = 2 THEN 1 ELSE 0 END) AS PNP_SUB_ASSY_Count_2,
  SUM(CASE WHEN PNP_SUB_ASSY = 3 THEN 1 ELSE 0 END) AS PNP_SUB_ASSY_Count_3,
  COUNT(CASE WHEN PNP_SUB_ASSY IN (1, 2, 3) THEN Name END) AS PNP_SUB_ASSY_Total,
  SUM(CASE WHEN PNP_INT = 1 THEN 1 ELSE 0 END) AS PNP_INT_Count_1,
  SUM(CASE WHEN PNP_INT = 2 THEN 1 ELSE 0 END) AS PNP_INT_Count_2,
  SUM(CASE WHEN PNP_INT = 3 THEN 1 ELSE 0 END) AS PNP_INT_Count_3,
  COUNT(CASE WHEN PNP_INT IN (1, 2, 3) THEN Name END) AS PNP_INT_Total,
  SUM(CASE WHEN OLB_MAIN = 1 THEN 1 ELSE 0 END) AS OLB_MAIN_Count_1,
  SUM(CASE WHEN OLB_MAIN = 2 THEN 1 ELSE 0 END) AS OLB_MAIN_Count_2,
  SUM(CASE WHEN OLB_MAIN = 3 THEN 1 ELSE 0 END) AS OLB_MAIN_Count_3,
  COUNT(CASE WHEN OLB_MAIN IN (1, 2, 3) THEN Name END) AS OLB_MAIN_Total,
  SUM(CASE WHEN ABLP = 1 THEN 1 ELSE 0 END) AS ABLP_Count_1,
  SUM(CASE WHEN ABLP = 2 THEN 1 ELSE 0 END) AS ABLP_Count_2,
  SUM(CASE WHEN ABLP = 3 THEN 1 ELSE 0 END) AS ABLP_Count_3,
  COUNT(CASE WHEN ABLP IN (1, 2, 3) THEN Name END) AS ABLP_Total,
  SUM(CASE WHEN OLB_F_INT = 1 THEN 1 ELSE 0 END) AS OLB_F_INT_Count_1,
  SUM(CASE WHEN OLB_F_INT = 2 THEN 1 ELSE 0 END) AS OLB_F_INT_Count_2,
  SUM(CASE WHEN OLB_F_INT = 3 THEN 1 ELSE 0 END) AS OLB_F_INT_Count_3,
  COUNT(CASE WHEN OLB_F_INT IN (1, 2, 3) THEN Name END) AS OLB_F_INT_Total,
  SUM(CASE WHEN SUB_TEST = 1 THEN 1 ELSE 0 END) AS SUB_TEST_Count_1,
  SUM(CASE WHEN SUB_TEST = 2 THEN 1 ELSE 0 END) AS SUB_TEST_Count_2,
  SUM(CASE WHEN SUB_TEST = 3 THEN 1 ELSE 0 END) AS SUB_TEST_Count_3,
  COUNT(CASE WHEN SUB_TEST IN (1, 2, 3) THEN Name END) AS SUB_TEST_Total,
  SUM(CASE WHEN FINAL_TEST = 1 THEN 1 ELSE 0 END) AS FINAL_TEST_Count_1,
  SUM(CASE WHEN FINAL_TEST = 2 THEN 1 ELSE 0 END) AS FINAL_TEST_Count_2,
  SUM(CASE WHEN FINAL_TEST = 3 THEN 1 ELSE 0 END) AS FINAL_TEST_Count_3,
  COUNT(CASE WHEN FINAL_TEST IN (1, 2, 3) THEN Name END) AS FINAL_TEST_Total
FROM prod_skills_matrix";

$cablematrix_query = "SELECT
  SUM(CASE WHEN MCUTTING = 1 THEN 1 ELSE 0 END) AS MCUTTING_Count_1,
  SUM(CASE WHEN MCUTTING = 2 THEN 1 ELSE 0 END) AS MCUTTING_Count_2,
  SUM(CASE WHEN MCUTTING = 3 THEN 1 ELSE 0 END) AS MCUTTING_Count_3,
  COUNT(CASE WHEN MCUTTING IN (1, 2, 3) THEN Name END) AS MCUTTING_Total,
  SUM(CASE WHEN MSTRIPPING = 1 THEN 1 ELSE 0 END) AS MSTRIPPING_Count_1,
  SUM(CASE WHEN MSTRIPPING = 2 THEN 1 ELSE 0 END) AS MSTRIPPING_Count_2,
  SUM(CASE WHEN MSTRIPPING = 3 THEN 1 ELSE 0 END) AS MSTRIPPING_Count_3,
  COUNT(CASE WHEN MSTRIPPING IN (1, 2, 3) THEN Name END) AS MSTRIPPING_Total,
  SUM(CASE WHEN MCRIMPING = 1 THEN 1 ELSE 0 END) AS MCRIMPING_Count_1,
  SUM(CASE WHEN MCRIMPING = 2 THEN 1 ELSE 0 END) AS MCRIMPING_Count_2,
  SUM(CASE WHEN MCRIMPING = 3 THEN 1 ELSE 0 END) AS MCRIMPING_Count_3,
  COUNT(CASE WHEN MCRIMPING IN (1, 2, 3) THEN Name END) AS MCRIMPING_Total,
  SUM(CASE WHEN SAWC = 1 THEN 1 ELSE 0 END) AS SAWC_Count_1,
  SUM(CASE WHEN SAWC = 2 THEN 1 ELSE 0 END) AS SAWC_Count_2,
  SUM(CASE WHEN SAWC = 3 THEN 1 ELSE 0 END) AS SAWC_Count_3,
  COUNT(CASE WHEN SAWC IN (1, 2, 3) THEN Name END) AS SAWC_Total,
  SUM(CASE WHEN MsU = 1 THEN 1 ELSE 0 END) AS MsU_Count_1,
  SUM(CASE WHEN MsU = 2 THEN 1 ELSE 0 END) AS MsU_Count_2,
  SUM(CASE WHEN MsU = 3 THEN 1 ELSE 0 END) AS MsU_Count_3,
  COUNT(CASE WHEN MsU IN (1, 2, 3) THEN Name END) AS MsU_Total,
  SUM(CASE WHEN SOLDERING = 1 THEN 1 ELSE 0 END) AS SOLDERING_Count_1,
  SUM(CASE WHEN SOLDERING = 2 THEN 1 ELSE 0 END) AS SOLDERING_Count_2,
  SUM(CASE WHEN SOLDERING = 3 THEN 1 ELSE 0 END) AS SOLDERING_Count_3,
  COUNT(CASE WHEN SOLDERING IN (1, 2, 3) THEN Name END) AS SOLDERING_Total,
  SUM(CASE WHEN MOLDING = 1 THEN 1 ELSE 0 END) AS MOLDING_Count_1,
  SUM(CASE WHEN MOLDING = 2 THEN 1 ELSE 0 END) AS MOLDING_Count_2,
  SUM(CASE WHEN MOLDING = 3 THEN 1 ELSE 0 END) AS MOLDING_Count_3,
  COUNT(CASE WHEN MOLDING IN (1, 2, 3) THEN Name END) AS MOLDING_Total,
  SUM(CASE WHEN WHARNESS = 1 THEN 1 ELSE 0 END) AS WHARNESS_Count_1,
  SUM(CASE WHEN WHARNESS = 2 THEN 1 ELSE 0 END) AS WHARNESS_Count_2,
  SUM(CASE WHEN WHARNESS = 3 THEN 1 ELSE 0 END) AS WHARNESS_Count_3,
  COUNT(CASE WHEN WHARNESS IN (1, 2, 3) THEN Name END) AS WHARNESS_Total,
  SUM(CASE WHEN FINALASSY = 1 THEN 1 ELSE 0 END) AS FINALASSY_Count_1,
  SUM(CASE WHEN FINALASSY = 2 THEN 1 ELSE 0 END) AS FINALASSY_Count_2,
  SUM(CASE WHEN FINALASSY = 3 THEN 1 ELSE 0 END) AS FINALASSY_Count_3,
  COUNT(CASE WHEN FINALASSY IN (1, 2, 3) THEN Name END) AS FINALASSY_Total,
  SUM(CASE WHEN MCO = 1 THEN 1 ELSE 0 END) AS MCO_Count_1,
  SUM(CASE WHEN MCO = 2 THEN 1 ELSE 0 END) AS MCO_Count_2,
  SUM(CASE WHEN MCO = 3 THEN 1 ELSE 0 END) AS MCO_Count_3,
  COUNT(CASE WHEN MCO IN (1, 2, 3) THEN Name END) AS MCO_Total,
  SUM(CASE WHEN LABELLING = 1 THEN 1 ELSE 0 END) AS LABELLING_Count_1,
  SUM(CASE WHEN LABELLING = 2 THEN 1 ELSE 0 END) AS LABELLING_Count_2,
  SUM(CASE WHEN LABELLING = 3 THEN 1 ELSE 0 END) AS LABELLING_Count_3,
  COUNT(CASE WHEN LABELLING IN (1, 2, 3) THEN Name END) AS LABELLING_Total,
  SUM(CASE WHEN ETESTING = 1 THEN 1 ELSE 0 END) AS ETESTING_Count_1,
  SUM(CASE WHEN ETESTING = 2 THEN 1 ELSE 0 END) AS ETESTING_Count_2,
  SUM(CASE WHEN ETESTING = 3 THEN 1 ELSE 0 END) AS ETESTING_Count_3,
  COUNT(CASE WHEN ETESTING IN (1, 2, 3) THEN Name END) AS ETESTING_Total,
  SUM(CASE WHEN VI = 1 THEN 1 ELSE 0 END) AS VI_Count_1,
  SUM(CASE WHEN VI = 2 THEN 1 ELSE 0 END) AS VI_Count_2,
  SUM(CASE WHEN VI = 3 THEN 1 ELSE 0 END) AS VI_Count_3,
  COUNT(CASE WHEN VI IN (1, 2, 3) THEN Name END) AS VI_Total,
  SUM(CASE WHEN PB = 1 THEN 1 ELSE 0 END) AS PB_Count_1,
  SUM(CASE WHEN PB = 2 THEN 1 ELSE 0 END) AS PB_Count_2,
  SUM(CASE WHEN PB = 3 THEN 1 ELSE 0 END) AS PB_Count_3,
  COUNT(CASE WHEN PB IN (1, 2, 3) THEN Name END) AS PB_Total,
  SUM(CASE WHEN TAPING = 1 THEN 1 ELSE 0 END) AS TAPING_Count_1,
  SUM(CASE WHEN TAPING = 2 THEN 1 ELSE 0 END) AS TAPING_Count_2,
  SUM(CASE WHEN TAPING = 3 THEN 1 ELSE 0 END) AS TAPING_Count_3,
  COUNT(CASE WHEN TAPING IN (1, 2, 3) THEN Name END) AS TAPING_Total
FROM prod_skills_matrix_cable";

// Execute the query
$main_lvl_statement = mysqli_prepare($conn, $mainmatrix_query);
mysqli_stmt_execute($main_lvl_statement);
$main_lvl_result = mysqli_stmt_get_result($main_lvl_statement);

$cable_lvl_statement = mysqli_prepare($conn, $cablematrix_query);
mysqli_stmt_execute($cable_lvl_statement);
$cable_lvl_result = mysqli_stmt_get_result($cable_lvl_statement);
// Fetch the row from the query result
$main_row = mysqli_fetch_assoc($main_lvl_result);
$cable_row = mysqli_fetch_assoc($cable_lvl_result);

// Define the Level arrays
$mainlevel1Values = array();
$mainlevel2Values = array();
$mainlevel3Values = array();
$maintargetValues = array('4', '4', '4', '4', '4', '4', '4', '6', '5', '4', '6', '8');

$cablelevel1Values = array();
$cablelevel2Values = array();
$cablelevel3Values = array();

// Iterate over the column names and retrieve the Level values
$main_columns = array('FC', 'CDM', 'TSL', 'AC', 'CDA', 'TXP', 'FA', 'INTEGRATION', 'PNP_SUB_ASSY', 'PNP_INT', 'SUB_TEST', 'FINAL_TEST');

$cable_columns = array('MCUTTING', 'MSTRIPPING', 'MCRIMPING', 'SAWC', 'MsU', 'SOLDERING', 'MOLDING', 'WHARNESS', 'FINALASSY', 'MCO', 'LABELLING', 'ETESTING', 'VI', 'PB', 'TAPING');

foreach ($main_columns as $column) {
    $mainlevel1Values[$column] = $main_row[$column . '_Count_1'];
    $mainlevel2Values[$column] = $main_row[$column . '_Count_2'];
    $mainlevel3Values[$column] = $main_row[$column . '_Count_3'];
}
foreach ($cable_columns as $column) {
    $cablelevel1Values[$column] = $cable_row[$column . '_Count_1'];
    $cablelevel2Values[$column] = $cable_row[$column . '_Count_2'];
    $cablelevel3Values[$column] = $cable_row[$column . '_Count_3'];
}

$maindescriptionMap = [
    'ASSY, FACILITY CABINET, JLP-G3' => 'Facility Cabinet',
    'ASSY CART DOCKING MECH-JLP G3' => 'CDM',
    'LIFT ASSY,TRAY STACK-JLP G3' => 'LIFT',
    'ASSY, CONVEYOR JLP-G3' => 'Assy Conveyor',
    'CONVEYOR DRIVE ASSY,JLP G3' => 'CDA',
    'TRAY TRANSPORT ASSY,JLP G3' => 'Tray Transport',
    'FRAME ASSY, JLP G3' => 'Frame Assy',
    'JLP GEN 3, MAIN ASSEMBLY' => 'Final Integration',
    'Matrix Sub-Assy',
    'Matrix Integration',
    'Sub Test',
    'Final Test'
];

$renamedColumns = array_map(function ($column) use ($maindescriptionMap) {
    return isset($maindescriptionMap[$column]) ? $maindescriptionMap[$column] : $column;
}, $main_columns);

$shortcutNames = [];

foreach ($maindescriptionMap as $shortcut) {
    $shortcutNames[] = $shortcut;
}
// // Output the Level arrays for testing
// echo "Level 1:\n";
// print_r($level1Values);

// echo "Level 2:\n";
// print_r($level2Values);

// echo "Level 3:\n";
// print_r($level3Values);

// echo "Target: \n";
// print_r($targetValues);

?>
