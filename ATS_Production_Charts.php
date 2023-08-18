<?php include 'ATS_Prod_Header.php';

function getAllAttendanceData($conn, $interval)
{
    $data = array();

    if ($interval === 'daily') {

        $totalMP_sql = mysqli_query($conn, "SELECT user_ID, emp_name, department FROM user WHERE (department = 'Cable Assy' AND role = 'operator' AND username NOT IN ('13394', '13351', '5555')) OR (Department IN ('Prod Main', 'Production Main') AND username NOT IN ('4444', '13472', '947', '2023', '11742')) GROUP BY emp_name, department ORDER BY emp_name;");
        $total_MP = mysqli_num_rows($totalMP_sql);

        $totalPresent_sql = "SELECT DATE, SUM(CASE WHEN department = 'CABLE ASSY' THEN count ELSE 0 END) AS cable_count, SUM(CASE WHEN department = 'Production Main' THEN count ELSE 0 END) AS production_count, SUM(count) AS total_count FROM ( SELECT DATE, 'CABLE ASSY' AS department, COUNT(*) AS count FROM prod_attendance WHERE Department = 'CABLE ASSY' AND WEEK(DATE) = WEEK(NOW()) - 1 AND DAYOFWEEK(DATE) BETWEEN 2 AND 6 AND Emp_ID NOT IN ('5555', '13640', '13394', '13351') GROUP BY DATE UNION SELECT DATE, 'Production Main' AS department, COUNT(*) AS count FROM prod_attendance WHERE Department IN ('Production Main', 'Prod Main') AND WEEK(DATE) = WEEK(NOW()) - 1 AND DAYOFWEEK(DATE) BETWEEN 2 AND 6 AND Emp_ID NOT IN ('4444', '13472', '947', '2023', '11742') GROUP BY DATE ) AS subquery GROUP BY DATE;";

        $dailyData = array();

        $result = mysqli_query($conn, $totalPresent_sql);
        while ($row = mysqli_fetch_assoc($result)) {
            $dailyData[$row['DATE']] = round($row['total_count'] / $total_MP * 100);
        }
        return $dailyData;
    } elseif ($interval === 'weekly') {
        $weekNumber = date('W');

        $totalMP_sql = mysqli_query($conn, "SELECT user_ID, emp_name, department FROM user WHERE (department = 'Cable Assy' AND role = 'operator' AND username NOT IN ('13394', '13351', '5555')) OR (Department IN ('Prod Main', 'Production Main') AND username NOT IN ('4444', '13472', '947', '2023', '11742')) GROUP BY emp_name, department ORDER BY emp_name;");
        $total_MP = mysqli_num_rows($totalMP_sql);

        $start_date = date('Y-m-d', strtotime("2023-W$weekNumber-1")); // Get the start date (Monday) of the week
        $end_date = date('Y-m-d', strtotime("2023-W$weekNumber-5"));   // Get the end date (Friday) of the week

        $total_working_days = 0;
        $current_date = $start_date;

        while ($current_date <= $end_date) {
            $day_of_week = date('N', strtotime($current_date));
            // Consider weekdays (Monday to Friday) as working days
            if ($day_of_week >= 1 && $day_of_week <= 5) {
                $total_working_days++;
            }
            $current_date = date('Y-m-d', strtotime($current_date . ' +1 day'));
        }

        $totalPresent_sql = mysqli_query($conn, "SELECT DATE, SUM(CASE WHEN department = 'CABLE ASSY' THEN count ELSE 0 END) AS cable_count, SUM(CASE WHEN department = 'Production Main' THEN count ELSE 0 END) AS production_count, SUM(count) AS total_count FROM ( SELECT DATE, 'CABLE ASSY' AS department, COUNT(*) AS count FROM prod_attendance WHERE Department = 'CABLE ASSY'AND (WEEK(DATE) = WEEK(NOW()) OR WEEK(DATE) = WEEK(NOW()) - 1) AND DAYOFWEEK(DATE) BETWEEN 2 AND 6 AND Emp_ID NOT IN ('5555', '13640', '13394', '13351') GROUP BY DATE UNION SELECT DATE, 'Production Main' AS department, COUNT(*) AS count FROM prod_attendance WHERE Department IN ('Production Main', 'Prod Main') AND (WEEK(DATE) = WEEK(NOW()) OR WEEK(DATE) = WEEK(NOW()) - 1) AND DAYOFWEEK(DATE) BETWEEN 2 AND 6 AND Emp_ID NOT IN ('4444', '13472', '947', '2023', '11742') GROUP BY DATE ) AS subquery GROUP BY WEEK(DATE);");

        $weeklyData = array();

        while ($row = mysqli_fetch_assoc($totalPresent_sql)) {
            $date = $row['DATE'];
            $weekNumber = date('W', strtotime($date));
            $attendance_count = $row['total_count'];

            if ($total_MP !== 0 && $total_working_days > 0) {
                $attendance_rate = round(($attendance_count / ($total_MP * $total_working_days)) * 100);
            } else {
                $attendance_rate = 0; // To avoid division by zero error
            }

            $weeklyData[$weekNumber] = $attendance_rate;
        }

        return $weeklyData;
    } elseif ($interval === 'monthly') {

        $totalMP_sql = mysqli_query($conn, "SELECT user_ID, emp_name, department FROM user WHERE (department = 'Cable Assy' AND role = 'operator' AND username NOT IN ('13394', '13351', '5555')) OR (Department IN ('Prod Main', 'Production Main') AND username NOT IN ('4444', '13472', '947', '2023', '11742')) GROUP BY emp_name, department ORDER BY emp_name;");
        $total_MP = mysqli_num_rows($totalMP_sql);

        $today = date('Y-m-d');
        $year = date('Y', strtotime($today));
        $lastDayOfYear = date('Y-12-t', strtotime("$year-01-01"));

        $currentDate = date('Y-m-01'); // Start from the current month
        $endOfYear = false;

        $monthlyAttendanceRates = array();

        while (!$endOfYear) {
            $totalPresent_sql = mysqli_query($conn, "SELECT COUNT(DISTINCT CASE WHEN (WEEK(DATE) = WEEK(NOW()) OR WEEK(DATE) = WEEK(NOW()) - 1) THEN DATE END) AS present_days,SUM(CASE WHEN department = 'CABLE ASSY' THEN count ELSE 0 END) AS cable_count, SUM(CASE WHEN department = 'Production Main' THEN count ELSE 0 END) AS production_count, SUM(count) AS total_count FROM ( SELECT DATE, 'CABLE ASSY' AS department, COUNT(*) AS count FROM prod_attendance WHERE Department = 'CABLE ASSY'AND (MONTH(DATE) = MONTH('$currentDate')) AND (WEEK(DATE) = WEEK(NOW()) OR WEEK(DATE) = WEEK(NOW()) - 1) AND DAYOFWEEK(DATE) BETWEEN 2 AND 6 AND Emp_ID NOT IN ('5555', '13640', '13394', '13351') GROUP BY DATE UNION SELECT DATE, 'Production Main' AS department, COUNT(*) AS count FROM prod_attendance WHERE Department IN ('Production Main', 'Prod Main')AND (MONTH(DATE) = MONTH('$currentDate')) AND(WEEK(DATE) = WEEK(NOW()) OR WEEK(DATE) = WEEK(NOW()) - 1) AND DAYOFWEEK(DATE) BETWEEN 2 AND 6 AND Emp_ID NOT IN ('4444', '13472', '947', '2023', '11742') GROUP BY DATE ) AS subquery");

            $attendance_count = 0;
            $row = mysqli_fetch_assoc($totalPresent_sql);
            $attendance_count += $row['total_count'];
            $number_of_days = $row['present_days'];

            $attendance_count;
            // Calculate the attendance rate for the current month
            if ($total_MP !== 0 && $number_of_days > 0) {
                $attendance_rate = round(($attendance_count / ($total_MP * $number_of_days)) * 100);
            } else {
                $attendance_rate = 0; // To avoid division by zero error
            }
            // Store the monthly attendance rate in the array
            $monthName = date('F', strtotime($currentDate)); // Get the full month name
            $monthlyAttendanceRates[$monthName] = array(
                'attendance_rate' => $attendance_rate,
                'number_of_days' => $number_of_days
            );

            // Move to the next month
            $currentDate = date('Y-m-d', strtotime('+1 month', strtotime($currentDate)));

            if ($currentDate > $lastDayOfYear) {
                $endOfYear = true;
            }
        }

        return $monthlyAttendanceRates;
    }
    return $data;
}
function getAttendanceData($conn, $type, $interval)
{
    $data = array();

    // Modify the SQL queries to fetch the data based on the interval
    if ($interval === 'daily') {
        // Retrieve daily attendance data for the current week
        $startOfWeek = date('Y-m-d', strtotime('this week'));
        $endOfWeek = date('Y-m-d', strtotime('this week +6 days'));

        if ($type === 'cable') {
            $operator_sql = mysqli_query($conn, "SELECT user_ID, emp_name FROM user WHERE department = 'Cable Assy' AND role = 'operator' AND username NOT IN ('13394', '13351', '5555') GROUP BY emp_name ORDER BY emp_name");
            $operators = mysqli_num_rows($operator_sql);

            $sql = "SELECT DATE, COUNT(*) as count FROM prod_attendance WHERE Department = 'CABLE ASSY' AND WEEK(DATE)=WEEK(NOW()) AND DAYOFWEEK(DATE) BETWEEN 2 AND 6 AND Emp_ID NOT IN ('5555', '13640', '13394', '13351') GROUP BY DATE ORDER BY DATE";
        } elseif ($type === 'main') {
            $technician_sql = mysqli_query($conn, "SELECT user_ID, emp_name, username FROM user WHERE Department IN ('Prod Main', 'Production Main') AND username NOT IN ('4444', '13472', '947', '2023', '11742') GROUP BY emp_name ORDER BY emp_name");
            $operators = mysqli_num_rows($technician_sql);

            $sql = "SELECT DATE, COUNT(*) as count FROM prod_attendance WHERE Department IN ('Production Main', 'Prod Main') AND WEEK(DATE)=WEEK(NOW()) AND DAYOFWEEK(DATE) BETWEEN 2 AND 6 AND Emp_ID NOT IN ('4444', '13472', '947', '2023', '11742') GROUP BY DATE ORDER BY DATE";
        }
        $dailyData = array();
        $result = mysqli_query($conn, $sql);
        while ($row = mysqli_fetch_assoc($result)) {
            $dailyData[$row['DATE']] = round($row['count'] / $operators * 100);
        }
        return $dailyData;
    } elseif ($interval === 'weekly') {
        // Retrieve weekly attendance data for the current month
        $startOfMonth = date('Y-m-01');
        $endOfMonth = date('Y-m-t');

        if ($type === 'cable') {
            $operator_sql = mysqli_query($conn, "SELECT user_ID, emp_name FROM user WHERE department = 'Cable Assy' AND role = 'operator' AND username NOT IN ('13394', '13351', '5555') GROUP BY emp_name ORDER BY emp_name");
            $operators = mysqli_num_rows($operator_sql);

            $sql = "SELECT DATE, COUNT(*) as count FROM prod_attendance WHERE Department = 'CABLE ASSY' AND WEEK(DATE)=31 AND DAYOFWEEK(DATE) BETWEEN 2 AND 6 AND Emp_ID NOT IN ('5555', '13640', '13394', '13351') GROUP BY DATE ORDER BY DATE";
        } elseif ($type === 'main') {
            $technician_sql = mysqli_query($conn, "SELECT user_ID, emp_name, username FROM user WHERE Department IN ('Prod Main', 'Production Main') AND username NOT IN ('4444', '13472', '947', '2023', '11742') GROUP BY emp_name ORDER BY emp_name");
            $operators = mysqli_num_rows($technician_sql);

            $sql = "SELECT DATE, COUNT(*) as count FROM prod_attendance WHERE Department IN ('Production Main', 'Prod Main') AND WEEK(DATE)=31 AND DAYOFWEEK(DATE) BETWEEN 2 AND 6 AND Emp_ID NOT IN ('4444', '13472', '947', '2023', '11742') GROUP BY DATE ORDER BY DATE";
        }

        $weeklyData = array();
        $workingDaysPerWeek = array(); // To store the total working days per week

        $result = mysqli_query($conn, $sql);
        while ($row = mysqli_fetch_assoc($result)) {
            $date = $row['DATE'];
            $weekNumber = date('W', strtotime($date));

            // Increment the count of working days for the current week
            if (!isset($workingDaysPerWeek[$weekNumber])) {
                $workingDaysPerWeek[$weekNumber] = 1;
            } else {
                $workingDaysPerWeek[$weekNumber]++;
            }

            // Increment the count of attendance for the current week
            if ($operators !== 0) {
                $weeklyData[$weekNumber] = isset($weeklyData[$weekNumber]) ? $weeklyData[$weekNumber] + ($row['count'] / $operators) : ($row['count'] / $operators);
            } else {
                $weeklyData[$weekNumber] = 0; // To avoid division by zero error
            }
        }

        // Calculate the average attendance rate for each week
        foreach ($weeklyData as $weekNumber => $totalAttendance) {
            $weeklyData[$weekNumber] = round(($totalAttendance / $workingDaysPerWeek[$weekNumber]) * 100);
        }

        return $weeklyData;
    }
    return $data;
}
// function getYearlyAttendanceData($conn, $type)
// {
//     $data = array();

//     // Modify the SQL query to fetch the yearly data
//     $startOfYear = date('Y-01-01');
//     $endOfYear = date('Y-12-31');

//     if ($type === 'cable') {
//         $operator_sql = mysqli_query($conn, "SELECT user_ID, emp_name FROM user WHERE department = 'Cable Assy' AND role = 'operator' AND username NOT IN ('13394', '13351', '5555') GROUP BY emp_name ORDER BY emp_name");
//         $operators = mysqli_num_rows($operator_sql);

//         $sql = "SELECT MONTH(DATE) AS month_number, COUNT(*) as count FROM prod_attendance WHERE Department = 'CABLE ASSY' AND DATE BETWEEN '$startOfYear' AND '$endOfYear' AND Emp_ID NOT IN ('5555', '13640', '13394', '13351') GROUP BY MONTH(DATE) ORDER BY month_number";
//     } elseif ($type === 'main') {
//         $technician_sql = mysqli_query($conn, "SELECT user_ID, emp_name, username FROM user WHERE Department IN ('Prod Main', 'Production Main') AND username NOT IN ('4444', '13472', '947', '2023', '11742') GROUP BY emp_name ORDER BY emp_name");
//         $operators = mysqli_num_rows($technician_sql);

//         $sql = "SELECT MONTH(DATE) AS month_number, COUNT(*) as count FROM prod_attendance WHERE Department IN ('Production Main', 'Prod Main') AND DATE BETWEEN '$startOfYear' AND '$endOfYear' AND Emp_ID NOT IN ('4444', '13472', '947', '2023', '11742') GROUP BY MONTH(DATE) ORDER BY month_number";
//     }

//     $result = mysqli_query($conn, $sql);
//     while ($row = mysqli_fetch_assoc($result)) {
//         $monthNumber = $row['month_number'];

//         if ($operators !== 0) {
//             $data[$monthNumber] = number_format($row['count'] / $operators * 100, 2);
//         } else {
//             $data[$monthNumber] = 0; // To avoid division by zero error
//         }
//     }

//     return $data;
// }
function getMonthlyAttendanceData($conn, $type, $year)
{
    $data = array();
    $monthNames = array("January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December");

    // Modify the SQL query to fetch the data for all months in the year
    if ($type === 'cable') {
        $operator_sql = mysqli_query($conn, "SELECT user_ID, emp_name FROM user WHERE department = 'Cable Assy' AND role = 'operator' AND username NOT IN ('13394', '13351', '5555') GROUP BY emp_name ORDER BY emp_name");
        $operators = mysqli_num_rows($operator_sql);

        for ($month = 1; $month <= 12; $month++) {
            // Determine the total number of days in the month
            $totalDaysInMonth = cal_days_in_month(CAL_GREGORIAN, $month, $year);

            // Determine the start and end dates for the month
            $startOfMonth = date('Y-m-01', strtotime("$year-$month-01"));
            $endOfMonth = date('Y-m-t', strtotime("$year-$month-01"));

            $sql = "SELECT DATE, COUNT(*) as count FROM prod_attendance WHERE Department = 'CABLE ASSY' AND DATE BETWEEN '$startOfMonth' AND '$endOfMonth' AND Emp_ID NOT IN ('5555', '13640', '13394', '13351') GROUP BY DATE ORDER BY DATE";

            $result = mysqli_query($conn, $sql);
            if (mysqli_num_rows($result) > 0) {
                while ($row = mysqli_fetch_assoc($result)) {
                    $date = $row['DATE'];
                    $dayNumber = date('d', strtotime($date));

                    // Increment the count of attendance for the current day
                    if ($operators !== 0) {
                        $data[$month][$dayNumber] = isset($data[$month][$dayNumber]) ? $data[$month][$dayNumber] + ($row['count'] / $operators) : ($row['count'] / $operators);
                    } else {
                        $data[$month][$dayNumber] = 0; // To avoid division by zero error
                    }
                }

                // Calculate the monthly attendance rate for each day of the month
                foreach ($data[$month] as $dayNumber => $attendanceCount) {
                    $data[$month][$dayNumber] = number_format(($attendanceCount / $totalDaysInMonth) * 100, 2);
                }
            } else {
                // If there is no data for the month, set all days' attendance rate to 0
                for ($day = 1; $day <= $totalDaysInMonth; $day++) {
                    $data[$month][$day] = 0;
                }
            }
        }
    } elseif ($type === 'main') {
        $technician_sql = mysqli_query($conn, "SELECT user_ID, emp_name, username FROM user WHERE Department IN ('Prod Main', 'Production Main') AND username NOT IN ('4444', '13472', '947', '2023', '11742') GROUP BY emp_name ORDER BY emp_name");
        $operators = mysqli_num_rows($technician_sql);

        for ($month = 1; $month <= 12; $month++) {
            // Determine the total number of days in the month
            $totalDaysInMonth = cal_days_in_month(CAL_GREGORIAN, $month, $year);

            // Determine the start and end dates for the month
            $startOfMonth = date('Y-m-01', strtotime("$year-$month-01"));
            $endOfMonth = date('Y-m-t', strtotime("$year-$month-01"));

            $sql = "SELECT DATE, COUNT(*) as count FROM prod_attendance WHERE Department IN ('Production Main', 'Prod Main') AND DATE BETWEEN '$startOfMonth' AND '$endOfMonth' AND Emp_ID NOT IN ('4444', '13472', '947', '2023', '11742') GROUP BY DATE ORDER BY DATE";

            $result = mysqli_query($conn, $sql);
            if (mysqli_num_rows($result) > 0) {
                while ($row = mysqli_fetch_assoc($result)) {
                    $date = $row['DATE'];
                    $dayNumber = date('d', strtotime($date));

                    // Increment the count of attendance for the current day
                    if ($operators !== 0) {
                        $data[$month][$dayNumber] = isset($data[$month][$dayNumber]) ? $data[$month][$dayNumber] + ($row['count'] / $operators) : ($row['count'] / $operators);
                    } else {
                        $data[$month][$dayNumber] = 0; // To avoid division by zero error
                    }
                }

                // Calculate the monthly attendance rate for each day of the month
                foreach ($data[$month] as $dayNumber => $attendanceCount) {
                    $data[$month][$dayNumber] = number_format(($attendanceCount / $totalDaysInMonth) * 100, 2);
                }
            } else {
                // If there is no data for the month, set all days' attendance rate to 0
                for ($day = 1; $day <= $totalDaysInMonth; $day++) {
                    $data[$month][$day] = 0;
                }
            }
        }
    }

    return $data;
}



function getYearlyAttendanceData($conn, $type)
{
    $data = array();

    // Modify the SQL query to fetch the yearly data
    $startOfYear = date('Y-01-01');
    $endOfYear = date('Y-12-31');

    if ($type === 'cable') {
        $operator_sql = mysqli_query($conn, "SELECT user_ID, emp_name FROM user WHERE department = 'Cable Assy' AND role = 'operator' AND username NOT IN ('13394', '13351', '5555') GROUP BY emp_name ORDER BY emp_name");
        $operators = mysqli_num_rows($operator_sql);

        $sql = "SELECT COUNT(*) as count FROM prod_attendance WHERE Department = 'CABLE ASSY' AND DATE BETWEEN '$startOfYear' AND '$endOfYear' AND Emp_ID NOT IN ('5555', '13640', '13394', '13351')";
    } elseif ($type === 'main') {
        $technician_sql = mysqli_query($conn, "SELECT user_ID, emp_name, username FROM user WHERE Department IN ('Prod Main', 'Production Main') AND username NOT IN ('4444', '13472', '947', '2023', '11742') GROUP BY emp_name ORDER BY emp_name");
        $operators = mysqli_num_rows($technician_sql);

        $sql = "SELECT COUNT(*) as count FROM prod_attendance WHERE Department IN ('Production Main', 'Prod Main') AND DATE BETWEEN '$startOfYear' AND '$endOfYear' AND Emp_ID NOT IN ('4444', '13472', '947', '2023', '11742')";
    }

    $result = mysqli_query($conn, $sql);
    $row = mysqli_fetch_assoc($result);

    if ($operators !== 0) {
        $attendanceCount = $row['count'];
        $data['yearly'] = number_format(($attendanceCount / ($operators * 365)) * 100, 2);
    } else {
        $data['yearly'] = 0; // To avoid division by zero error
    }

    return $data;
}


// Call the getAttendanceData function to retrieve the data for cable department
$cableDailyData = getAttendanceData($conn, 'cable', 'daily');
$cableWeeklyData = getAttendanceData($conn, 'cable', 'weekly');
$cableMonthlyData = getMonthlyAttendanceData($conn, 'cable', date('Y'));
$cableYearlyData = getYearlyAttendanceData($conn, 'cable');

// Call the getAttendanceData function to retrieve the data for main department
$mainDailyData = getAttendanceData($conn, 'main', 'daily');
$mainWeeklyData = getAttendanceData($conn, 'main', 'weekly');
$mainMonthlyData = getMonthlyAttendanceData($conn, 'main', date('Y'));
$mainYearlyData = getYearlyAttendanceData($conn, 'main');


// ALL ATTENDANCE
$OverAllDailyData = getAllAttendanceData($conn, 'daily');
$OverAllWeeklyData = getAllAttendanceData($conn, 'weekly');
$OverAllMonthlyData = getAllAttendanceData($conn, 'monthly');

$monthlyAttendanceRatesWithDays = array();
$monthlyAttendanceDays = array();
foreach ($OverAllMonthlyData as $monthName => $data) {
    $attendanceRate = $data['attendance_rate'];
    $numberOfDays = $data['number_of_days'];

    $monthlyAttendanceRatesWithDays[$monthName] = $attendanceRate;
    $monthlyAttendanceDays[$monthName] = $numberOfDays;
}

// echo json_encode(array(
//     'attendance_rates' => $monthlyAttendanceRatesWithDays,
//     'number_of_days' => $monthlyAttendanceDays
// ));
?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Production Charts</title>
    <!-- include the Chart.js library -->
    <script src="assets/js/Chart.js"></script>
    <script src="assets/js/Chartjsannotation.js"></script>
    <script src="assets/js/chartjs-plugin-datalabels.min.js"></script>
    <!-- <script src="https://cdnjs.cloudflare.com/ajax/libs/chartjs-plugin-datalabels/2.2.0/chartjs-plugin-datalabels.min.js" integrity="sha512-JPcRR8yFa8mmCsfrw4TNte1ZvF1e3+1SdGMslZvmrzDYxS69J7J49vkFL8u6u8PlPJK+H3voElBtUCzaXj+6ig==" crossorigin="anonymous" referrerpolicy="no-referrer"></script> -->

</head>

<body>
    <div class="row mx-0">
        <div class="col-sm m-2">
            <div class="card">
                <div class="card-header text-bg-primary">
                    <h5 class="fw-bold " type="button" data-bs-toggle="modal" data-bs-target="#exampleModal"> Attendance Rate <i class="fas fa-info-circle"></i></h5>
                </div>
                <div class="card-body">
                    <div class="chart-container">
                        <input type="checkbox" id="toggleAllWeekly" onchange="toggleAllInterval()" />
                        <label for="toggleAllWeekly">Weekly</label>
                        <input type="checkbox" id="toggleAllMonthly" onchange="toggleAllInterval()" />
                        <label for="toggleAllMonthly">Monthly</label>
                        <canvas id="all_att_chart"></canvas>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm">
            <div class="card">
                <div class="card-header text-bg-primary">
                    <h5 class="fw-bold " type="button" data-bs-toggle="modal" data-bs-target="#exampleModal"> Efficiency Rate <i class="fas fa-info-circle"></i></h5>
                </div>
                <div class="card-body">
                    <div class="chart-container">
                        <!-- <input type="checkbox" id="toggleAllWeekly" onchange="toggleAllInterval()" />
                        <label for="toggleAllWeekly">Weekly</label>
                        <input type="checkbox" id="toggleAllMonthly" onchange="toggleAllInterval()" />
                        <label for="toggleAllMonthly">Monthly</label>
                        <canvas id="all_att_chart"></canvas> -->
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Modal -->
    <div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl">
            <div class=" modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5" id="exampleModalLabel">Attendance Rate Cable and Main</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="card">
                        <div class="card-body">
                            <div class="">
                                <input type="checkbox" id="toggleWeekly" />
                                <label for=" toggleWeekly">Weekly</label>
                                <canvas id="att_chart"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="/ATS/ATSPROD_PORTAL/assets/js/bootstrap.bundle.min.js"></script>
    <script>
        $('#exampleModal').on('shown.bs.modal', function() {
            // Initialize default interval as 'daily'
            let currentInterval = 'daily';

            // Function to toggle the interval between 'daily' and 'weekly'
            function toggleInterval() {
                const toggleWeekly = document.getElementById('toggleWeekly').checked;

                if (toggleWeekly) {
                    currentInterval = 'weekly';
                } else {
                    currentInterval = 'daily';
                }

                // Update the chart data based on the new interval and department
                let newData;
                if (currentInterval === 'daily') {
                    newData = <?php echo json_encode($cableDailyData); ?>;
                } else if (currentInterval === 'weekly') {
                    newData = <?php echo json_encode($cableWeeklyData); ?>;
                }

                Attchart.data.labels = Object.keys(newData);
                Attchart.data.datasets[0].data = Object.values(newData);

                // Update the label and data for the main department
                if (currentInterval === 'daily') {
                    newData = <?php echo json_encode($mainDailyData); ?>;
                } else if (currentInterval === 'weekly') {
                    newData = <?php echo json_encode($mainWeeklyData); ?>;
                }

                Attchart.data.datasets[1].data = Object.values(newData);
                Attchart.update();
            }

            // Chart data
            const cableData = <?php echo json_encode($cableDailyData); ?>;
            const attendanceCableValues = Object.values(cableData);
            const mainData = <?php echo json_encode($mainDailyData); ?>;
            const attendanceMainValues = Object.values(mainData);
            const cableWeeklyData = <?php echo json_encode($cableWeeklyData); ?>;
            const mainWeeklyData = <?php echo json_encode($mainWeeklyData); ?>;

            // Create the chart
            const ctx = document.getElementById('att_chart').getContext('2d');
            const Attchart = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: Object.keys(cableData),
                    datasets: [{
                        label: 'Cable',
                        data: attendanceCableValues,
                        backgroundColor: 'rgba(255, 177, 193)',
                        borderColor: 'rgba(255,99,132,255)',
                        borderWidth: 2
                    }, {
                        label: 'Main',
                        data: attendanceMainValues,
                        backgroundColor: 'rgba(154,208,245,255)',
                        borderColor: 'rgba(65,167,236,255)',
                        borderWidth: 2
                    }]
                },
                options: {
                    plugins: {
                        legend: {
                            display: true,
                            labels: {
                                color: 'black',
                                font: {
                                    weight: 'bold',
                                },
                            },
                        },
                        tooltip: {
                            enabled: true,
                            callbacks: {
                                label: function(context) {
                                    return context.dataset.label + ': ' + context.parsed.y + '%';
                                },
                            },
                        },
                        annotation: {
                            annotations: {
                                targetLine: {
                                    type: 'line',
                                    yMin: 95, // Target attendance rate (96%)
                                    yMax: 95, // Target attendance rate (96%)
                                    borderColor: 'rgba(255, 174, 66)',
                                    borderWidth: 2,
                                    label: {
                                        enabled: true,
                                        content: 'Target: 95%', // The label content
                                        position: 'end', // Position of the label relative to the target line (start, center, end)
                                    },
                                },
                            },
                        },
                        datalabels: {
                            anchor: 'top',
                            align: 'top',
                            formatter: function(value, context) {
                                if (value !== null && value !== undefined) {
                                    return value + '%';
                                } else {
                                    console.log('Null or undefined value detected:', value);
                                    return 'N/A'; // Return 'N/A' if value is null or undefined
                                }
                            },
                            color: 'black',
                            font: {
                                weight: 'bold',
                                size: 14
                            }
                        },
                    },
                    // maintainAspectRatio: false,
                    responsive: true,
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                callback: function(value) {
                                    return value + '%';
                                },
                            },
                        },
                    },
                },
                plugins: [ChartDataLabels]
            });

            // Add event listener to handle click on the chart
            document.getElementById('att_chart').addEventListener('click', function(event) {
                const activePoints = Attchart.getElementsAtEventForMode(event, 'nearest', {
                    intersect: true
                }, true);
                if (activePoints.length > 0) {
                    const clickedDatasetIndex = activePoints[0].datasetIndex;
                    const clickedIndex = activePoints[0].index;

                    // Perform action based on the clicked dataset and index
                    if (clickedDatasetIndex === 0 && clickedIndex === 0) {
                        // Bar for dataset 'Cable' and index 0 was clicked
                        window.location.href = 'Generate Reports/cable_attendance_summary.php';
                    } else if (clickedDatasetIndex === 1 && clickedIndex === 0) {
                        // Bar for dataset 'Main' and index 0 was clicked
                        window.location.href = 'Generate Reports/module_attendance_summary.php';
                    }
                }
            });

            document.getElementById('toggleWeekly').addEventListener('change', toggleInterval);
        });
    </script>
    <script>
        // Initialize default interval as 'daily'
        let currentAllInterval = 'daily';
        // Function to toggle the interval between 'daily', 'weekly', and 'yearly'
        function toggleAllInterval() {
            const toggleAllWeekly = document.getElementById('toggleAllWeekly').checked;
            const toggleAllMonthly = document.getElementById('toggleAllMonthly').checked;

            if (toggleAllWeekly) {
                currentAllInterval = 'weekly';
            } else if (toggleAllMonthly) {
                currentAllInterval = 'monthly';
            } else {
                currentAllInterval = 'daily';
            }

            // Update the chart data based on the new interval
            let newData;
            if (currentAllInterval === 'daily') {
                newData = <?php echo json_encode($OverAllDailyData); ?>;
            } else if (currentAllInterval === 'weekly') {
                newData = Object.fromEntries(Object.entries(<?php echo json_encode($OverAllWeeklyData); ?>).map(([key, value]) => [`Week ${key}`, value]));
            } else if (currentAllInterval === 'monthly') {
                newData = <?php echo json_encode($monthlyAttendanceRatesWithDays); ?>;
            }
            // } else if (currentAllInterval === 'yearly') {
            //     newData = <?php ?>;
            // }

            // Update chart labels and data
            Att.data.labels = Object.keys(newData);
            Att.data.datasets[0].data = Object.values(newData);
            Att.update();
        }

        // Chart data
        const OvrAllDailyAttendance = <?php echo json_encode($OverAllDailyData); ?>;
        const attendanceValues = Object.values(OvrAllDailyAttendance);

        // const OvrAllWeeklyAttendance = <?php echo json_encode($OverAllWeeklyData); ?>;
        // Create the chart
        const All_Att = document.getElementById('all_att_chart').getContext('2d');
        const Att = new Chart(All_Att, {
            type: 'bar',
            data: {
                labels: Object.keys(OvrAllDailyAttendance),
                datasets: [{
                    label: 'Production',
                    data: attendanceValues,
                    backgroundColor: 'rgba(227, 246, 245, 1)',
                    borderColor: 'rgba(44, 105, 141, 255)',
                    borderWidth: 2
                }]
            },
            options: {
                plugins: {
                    legend: {
                        display: true,
                        labels: {
                            color: 'black',
                            font: {
                                weight: 'bold',
                            },
                        },
                    },
                    tooltip: {
                        enabled: true,
                        callbacks: {
                            label: function(context) {
                                return context.dataset.label + ': ' + context.parsed.y + '%';
                            },
                        },
                    },
                    datalabels: {
                        anchor: 'top',
                        align: 'top',
                        // formatter: function(value, context) {
                        //     return value + '%';
                        // },
                        formatter: function(value, context) {
                            if (value !== null && value !== undefined) {
                                return value + '%';
                            } else {
                                console.log('Null or undefined value detected:', value);
                                return 'N/A'; // Return 'N/A' if value is null or undefined
                            }
                        },
                        font: {
                            weight: 'bold',
                            size: 14
                        }
                    },
                    annotation: {
                        annotations: {
                            targetLine: {
                                type: 'line',
                                yMin: 95, // Target attendance rate (96%)
                                yMax: 95, // Target attendance rate (96%)
                                borderColor: 'rgba(255, 174, 66)',
                                borderWidth: 2,
                                label: {
                                    enabled: true,
                                    content: 'Target: 95%', // The label content
                                    position: 'end', // Position of the label relative to the target line (start, center, end)
                                },
                            },
                        },
                    },
                },
                responsive: true,
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function(value) {
                                return value + '%';
                            },
                        },
                    },
                },
            },
            plugins: [ChartDataLabels]
        });
    </script>
</body>

</html>