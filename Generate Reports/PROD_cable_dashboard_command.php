<?php
$conn = mysqli_connect("localhost", "root", "") or die("Could not connect");
mysqli_select_db($conn, "ewip") or die("could not connect database");

// Define the required variables (Assuming you have already defined them in your original file)
$monday = date('Y-m-d', strtotime('monday this week'));
$saturday = date('Y-m-d', strtotime('saturday this week'));
$wwk = date('W', strtotime('monday this week')) + 0;

$nextmonday = date('Y-m-d', strtotime('monday next week'));
$nextsaturday = date('Y-m-d', strtotime('saturday next week'));
$nextwwk = date('W', strtotime('monday next week')) + 0;

function getUpdatedData($conn, $monday, $saturday, $wwk, $nextmonday, $nextsaturday)
{
    // Initialize variables here
    $weekly_target = 0;
    $total_fg_qty = 0;
    $backlog_target = 0;
    $backlog_remaining = 0;
    $backlog_fg_qty = 0;
    $nextweekly_target = 0;
    $nexttotal_fg_qty = 0;
    $stationIdleQuantities = array();
    $stationInProcessQuantities = array();
    $stationFG = 0;
    $stationPK = 0;
    $shipped = 0;

    $wk_targetsql = mysqli_query($conn, "SELECT sum(wo_quantity) as weekly_target, TCD FROM wo WHERE TCD between '$monday' AND '$saturday' AND FG!='Yes' ");
    while ($row = mysqli_fetch_array($wk_targetsql)) {
        $weekly_target = $row['weekly_target'];
    }

    $total_fg_qty_sql = mysqli_query($conn, "SELECT sum(wo_quantity) as fg_qty FROM wo  WHERE  TCD BETWEEN '$monday' AND '$saturday' AND FG='Yes'  ");
    while ($row = mysqli_fetch_array($total_fg_qty_sql)) {
        $total_fg_qty = $row['fg_qty'];
    }

    $backlog_targetsql = mysqli_query($conn, "SELECT backlog_target + backlog_remaining as total_backlog FROM (SELECT SUM(wo_quantity) as backlog_target FROM `wo` WHERE ((YEAR(TCD) = YEAR(NOW()) AND WEEK(TCD) < WEEK(Now())) OR (YEAR(TCD) = YEAR(NOW() - INTERVAL 1 YEAR))) AND YEAR(ACD) = YEAR(NOW()) AND WEEK(ACD) = WEEK(Now())) as subquery1,(SELECT sum(wo_quantity) as backlog_remaining FROM wo WHERE WEEK(TCD) < '$wwk' AND FG !='Yes') as subquery2");

    while ($row = mysqli_fetch_array($backlog_targetsql)) {
        $backlog_target = $row['total_backlog'];
    }
    // BACKLOG REMAINING
    $backlog_remainingsql = mysqli_query($conn, "SELECT sum(wo_quantity) as backlog_remaining FROM wo WHERE WEEK(TCD) < '$wwk' AND FG !='Yes' ");

    while ($row = mysqli_fetch_array($backlog_remainingsql)) {
        $backlog_remaining = $row['backlog_remaining'];
    }
    // BACKLOG FG
    $backlog_fg_qty_sql = mysqli_query($conn, "SELECT SUM(wo_quantity) as backlog_fg_qty FROM `wo`WHERE YEAR(TCD) = YEAR(NOW()) AND YEAR(ACD) = YEAR(NOW()) AND WEEK(TCD) <= WEEK(NOW() - INTERVAL 1 WEEK) AND WEEK(ACD) = $wwk");

    while ($row = mysqli_fetch_array($backlog_fg_qty_sql)) {
        $backlog_fg_qty = $row['backlog_fg_qty'];
    }

    $nextwk_targetsql = mysqli_query($conn, "SELECT sum(wo_quantity) as nextweekly_target, TCD FROM wo WHERE TCD between '$nextmonday' AND '$nextsaturday' ");

    while ($row = mysqli_fetch_array($nextwk_targetsql)) {
        $nextweekly_target = $row['nextweekly_target'];
    }

    $nexttotal_fg_qty_sql = mysqli_query($conn, "SELECT sum(wo_quantity) as nextfg_qty FROM wo WHERE  TCD BETWEEN '$nextmonday' AND '$nextsaturday' AND FG='Yes' ");

    while ($row = mysqli_fetch_array($nexttotal_fg_qty_sql)) {
        $nexttotal_fg_qty = $row['nextfg_qty'];
    }

    $stationQueries = array(
        'WIRE/TUBE CUTTING',
        'WIRE STRIPPING',
        'TERMINAL CRIMPING',
        'IPQC',
        'PRE-BLOCKING',
        'SOLDERING',
        'MOLDING',
        'WIRE HARNESSING',
        'TAPING',
        'FINAL ASSEMBLY',
        'HEAT SHRINKING',
        'LABELLING',
        'TESTING',
        'VISUAL INSPECTION',
        'OQA',
        'FG TRANSACTION',
        'PACKAGING',
        'FG STORE',
        'PARTS KITTING'
    );



    $sqlQuery = "SELECT all_stations.for_station,
        COALESCE(SUM(CASE WHEN wo.status != 'IN-PROCESS' THEN wo.wo_quantity ELSE 0 END), 0) AS idle_quantity,
        COALESCE(SUM(CASE WHEN wo.status = 'IN-PROCESS' THEN wo.wo_quantity ELSE 0 END), 0) AS inprocess_quantity,
        COALESCE(SUM(CASE WHEN all_stations.for_station = 'FG STORE' THEN wo.new_wo_qty ELSE 0 END), 0) AS fg_wo,
        COALESCE(SUM(CASE WHEN all_stations.for_station = 'PARTS KITTING' THEN wo.wo_quantity ELSE 0 END), 0) AS pk_wo
        FROM (SELECT '" . implode("' AS for_station UNION ALL SELECT '", $stationQueries) . "' AS for_station) all_stations
        LEFT JOIN wo ON all_stations.for_station = wo.for_station
        GROUP BY all_stations.for_station";

    $result = mysqli_query($conn, $sqlQuery);

    // Iterate through the results and store the quantities in arrays
    while ($row = mysqli_fetch_assoc($result)) {
        $stationIdleQuantities[$row['for_station']] = $row['idle_quantity'] ?? 0;
        $stationInProcessQuantities[$row['for_station']] = $row['inprocess_quantity'] ?? 0;
        $stationFG = ($row['for_station'] === 'FG STORE') ? $row['fg_wo'] : $stationFG;
        $stationPK = ($row['for_station'] === 'PARTS KITTING') ? $row['pk_wo'] : $stationPK;
    }

    $ship_query = "SELECT SUM(Qty) as shipped FROM out_po";

    $result_ship = mysqli_query($conn, $ship_query);

    while ($row = mysqli_fetch_assoc($result_ship)) {
        $shipped = $row['shipped'];
    }
    // Add debugging to log data
    $data = array(
        'stationQueries' => $stationQueries,
        'weekly_target' => $weekly_target,
        'total_fg_qty' => $total_fg_qty != 0 ? $total_fg_qty : 0,
        'backlog_fg_qty' => $backlog_fg_qty != 0 ? $backlog_fg_qty : 0,
        'backlog_target' => $backlog_target,
        'backlog_remaining' => $backlog_remaining,
        'nextweekly_target' => $nextweekly_target,
        'nexttotal_fg_qty' => $nexttotal_fg_qty != 0 ? $nexttotal_fg_qty : 0,
        'stationIdleQuantities' => $stationIdleQuantities,
        'stationInProcessQuantities' => $stationInProcessQuantities,
        'stationPK' => $stationPK,
        'stationFG' => $stationFG,
        'shipped' => $shipped
    );
    return $data;
}
// Call the function to get the updated data
$data = getUpdatedData($conn, $monday, $saturday, $wwk, $nextmonday, $nextsaturday);
// Assign the values to respective variables
$weekly_target = $data['weekly_target'];
$backlog_remaining = $data['backlog_remaining'];
$backlog_fg_qty = $data['backlog_fg_qty'];
$total_fg_qty = $data['total_fg_qty'];
$nextweekly_target = $data['nextweekly_target'];
$nexttotal_fg_qty = $data['nexttotal_fg_qty'];
$stationIdleQuantities = $data['stationIdleQuantities'];
$stationInProcessQuantities = $data['stationInProcessQuantities'];
$stationQueries = $data['stationQueries'];
$stationPK = $data['stationPK'];
$stationFG = $data['stationFG'];
$shipped = $data['shipped'];
// Check if the variable is defined
if (isset($data) && basename($_SERVER['SCRIPT_FILENAME']) === basename(__FILE__)) {
    // Variable is defined and the script is being accessed directly, use it
    echo json_encode($data);
} else {
    // Variable is not defined yet or the script is included, return an empty response or handle the situation accordingly

}
