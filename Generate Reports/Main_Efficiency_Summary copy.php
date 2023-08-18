<?php include '../ATS_Prod_Header.php'; ?>
<?php include '../PROD_navbar.php'; ?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Efficiency Summary Main Production</title>
    <script src="../assets/js/exceljs.min.js"></script>
</head>

<body>
    <center>
        <div class="table-responsive text-center mt-2 mb-2">
            <form action="" method="POST">
                <label for="datefrom"><b>FROM:</b></label>
                <input align="center" type="date" name="datefrom" value="">
                <label for="dateto"><b>TO:</b></label>
                <input type="date" name="dateto" value="">
                <div class="m-1">
                    <button class="btn btn-secondary btn-sm mb-1" id="filter" name="filter">View</button>
                    <button class="btn btn-secondary btn-sm mb-1" onclick='myApp.printTable()'>Print/Save</button>
                    <button class="btn btn-success btn-sm mb-1" onclick="exportToExcel()">Export to Excel</button>
                </div>

                <div id="spinner">
                    <span id="loading-text">Loading...</span>
                    <div class="spinner-border" role="status" id="spinner" aria-hidden="true">
                    </div>
                </div>
                <?php
                function OVRPRODUCTEFF($conn, $datefrom, $dateto)
                {
                    $result = [];
                    // QUERY FOR HEADCOUNT
                    $jlp_hc_sql = mysqli_query($conn, "SELECT COUNT(DISTINCT Name) AS total_headcount FROM prod_dtr WHERE Department = 'Prod Main' AND Act_End != '' AND Name != 'TECHNICIAN' AND product='JLP' AND description != 'INDIRECT ACTIVITY' AND Emp_ID NOT IN ('4444','11451') AND DATE BETWEEN '$datefrom' AND '$dateto'");
                    $jlp_hc_row = mysqli_fetch_assoc($jlp_hc_sql);
                    $total_hc_jlp = $jlp_hc_row['total_headcount'];

                    // QUERY FOR EMPLOYEES
                    $jlp_sql_data = mysqli_query($conn, "SELECT Name,Qty_Make,cycle_time,SUM(build_percent/100 *Qty_Make) AS build_percent,Stations,description,Part_No,Duration,Prod_Order_No,batch_no,Code,Act_Start,Act_End,remarks,product FROM prod_dtr WHERE DATE BETWEEN '$datefrom' AND '$dateto' AND Qty_Make > 0 AND product = 'JLP' AND Emp_ID NOT IN ('4444','11451') AND Labor_Type != 'Prod_Reg_ID' GROUP BY Name,description ORDER BY Name ASC");

                    $array1 = []; // Array to store cycle time values
                    $array2 = []; // Array to store build percent values
                    $result_jlp = [];
                    $descArray = array();
                    $all_desc = array();
                    $jlp_bp_new = 0;
                    // Define the specific values for each description
                    $values = array(
                        "TRAY TRANSPORT ASSY,JLP G3" => 0.57, "ASSY CART DOCKING MECH-JLP G3" => 1, "ASSY, FACILITY CABINET, JLP-G3" => 0.67, "CONVEYOR DRIVE ASSY,JLP G3" => 1, "ASSY, CONVEYOR JLP-G3" => 1.14, "LIFT ASSY,TRAY STACK-JLP G3" => 1, "FRAME ASSY, JLP G3" => 1.14, "KIT, JLP, AIR KNIFE OPTION" => 1.60, "TRAY FLIP MECHANISM, JLP G2" => 0.67, "KIT, MANUAL TRAY PLATF, JLP G2" => 1
                    );

                    while ($jlp_row = mysqli_fetch_array($jlp_sql_data)) {
                        $jlp_desc = $jlp_row['description'];
                        $jlp_std = $jlp_row['cycle_time'];
                        $jlp_stations = $jlp_row['Stations'];
                        $jlp = $jlp_row['product'];
                        $jlp_bp = number_format($jlp_row['build_percent'], 2);
                        $descArray[] = $jlp_desc;

                        if (isset($values[$jlp_desc])) {
                            $all_desc[$jlp_desc] = $values[$jlp_desc] . "<br>";
                            if ($jlp_bp == 1) {
                                $jlp_bp_new = $all_desc[$jlp_desc];
                            } else {
                                $jlp_bp_new = $jlp_bp;
                            }
                        }
                        if ($jlp_stations == 'FVI MODULE' || $jlp_stations == 'FVI MAC') {
                            // Assign a specific value for $jlp_bp_new when $jlp_stations matches
                            if ($jlp_bp == 1 or 3) {
                                $jlp_bp_new = 2.67;
                            } else {
                                $jlp_bp_new = $jlp_bp;
                            }
                        }

                        // Output or usage of $jlp_bp_new within the loop
                        // echo "Description: " . $jlp_desc . ", BP: " . $jlp_bp_new . "<br>";

                        $output_jlp = $jlp_bp_new;
                        $output_jlp;

                        if ($jlp_row['Stations'] == 'FVI MODULE') {
                            $std_cycle_time = "3.00";
                        } elseif ($jlp_row['Stations'] == 'SUB TEST') {
                            $std_cycle_time = "2.00";
                        } else {
                            $std_cycle_time = $jlp_std;
                        }

                        $array1[] = $std_cycle_time;
                        $array2[] = $output_jlp;

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
                            'product_jlp' => $jlp
                        ];
                    }
                    $result[] = $result_jlp;
                    // QUERY FOR HEADCOUNT
                    $pnp_hc_sql = mysqli_query($conn, "SELECT COUNT(DISTINCT Name) AS total_headcount FROM prod_dtr WHERE Department = 'Prod Main' AND Act_End != '' AND Name != 'TECHNICIAN' AND product='PNP' AND description != 'INDIRECT ACTIVITY' AND Emp_ID != '11451' AND DATE BETWEEN '$datefrom' AND '$dateto'");
                    $pnp_hc_row = mysqli_fetch_assoc($pnp_hc_sql);
                    $total_hc_pnp = $pnp_hc_row['total_headcount'];

                    // QUERY FOR EMPLOYEES
                    $pnp_sql_data = mysqli_query($conn, "SELECT Name,Qty_Make,cycle_time,SUM(build_percent/100 *Qty_Make) AS build_percent,Stations,description,Part_No,Duration,Prod_Order_No,batch_no,Code,Act_Start,Act_End,remarks,product FROM prod_dtr WHERE DATE BETWEEN '$datefrom' AND '$dateto' AND Qty_Make > 0 AND product = 'PNP' AND Emp_ID NOT IN ('4444','11451') AND Labor_Type != 'Prod_Reg_ID' GROUP BY Name,description ORDER BY Name ASC");

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
                function calculateDetailedEfficiency($conn, $datefrom, $dateto)
                {
                    $results = [];
                    $sum_detailed_eff = [];
                    $sum_detailed_output = [];
                    // QUERY FOR HEADCOUNT
                    $hc_sql = mysqli_query($conn, "SELECT COUNT(DISTINCT Name) AS total_headcount FROM prod_dtr WHERE Department = 'Prod Main' AND Act_End != '' AND Name != 'TECHNICIAN' AND description != 'INDIRECT ACTIVITY' AND Emp_ID != '11451' AND DATE BETWEEN '$datefrom' AND '$dateto'");
                    $hc_row = mysqli_fetch_assoc($hc_sql);
                    $total_headcount = $hc_row['total_headcount'];

                    // QUERY FOR EMPLOYEES
                    $wosql_data = mysqli_query($conn, "SELECT dtr.Name, dtr.Qty_Make,(8/dtr.cycle_time) AS cycle_time,SUM(dtr.build_percent/100 *dtr.Qty_Make) as build_percent,dtr.Stations,dtr.description,dtr.Part_No,(dtr.Duration/60) AS Duration,dtr.Prod_Order_No,dtr.batch_no,dtr.Code,dtr.Act_Start,dtr.Act_End,dtr.remarks,dtr.product,dtr.rem_cycletime,module.build_percent AS module_build_percent FROM prod_dtr AS dtr LEFT JOIN prod_module AS module ON dtr.description = module.description AND dtr.batch_no = module.batch_no WHERE dtr.DATE BETWEEN '$datefrom' AND '$dateto' AND dtr.Qty_Make > 0 AND dtr.product IN ('JLP') AND dtr.Department IN ('Prod Main') AND dtr.Emp_ID NOT IN ('4444','11451') AND dtr.Labor_Type != 'Prod_Reg_ID' GROUP BY dtr.Name, dtr.description, dtr.batch_no ORDER BY dtr.Name ASC");

                    $stmt = mysqli_prepare($conn, "SELECT description FROM bom WHERE prod_type=? AND description!='INDIRECT ACTIVITY' AND level='1'");
                    mysqli_stmt_bind_param($stmt, "s", $prodType);

                    $prodType = 'JLP';

                    mysqli_stmt_execute($stmt);
                    $result = mysqli_stmt_get_result($stmt);

                    $descArray = array();
                    $all_desc = array();

                    // // Define the specific values for each description
                    // $values = array(
                    //     "TRAY TRANSPORT ASSY,JLP G3" => 0.57, "ASSY CART DOCKING MECH-JLP G3" => 1, "ASSY, FACILITY CABINET, JLP-G3" => 0.67, "CONVEYOR DRIVE ASSY,JLP G3" => 1, "ASSY, CONVEYOR JLP-G3" => 1.14, "LIFT ASSY,TRAY STACK-JLP G3" => 1, "FRAME ASSY, JLP G3" => 1.14, "KIT, JLP, AIR KNIFE OPTION" => 1.60, "TRAY FLIP MECHANISM, JLP G2" => 0.67, "KIT, MANUAL TRAY PLATF, JLP G2" => 1
                    // );

                    while ($row = mysqli_fetch_assoc($result)) {
                        $desc = $row['description'];
                        $descArray[] = $desc;
                    }
                    mysqli_stmt_close($stmt);

                    $array1 = []; // Array to store cycle time values
                    $array2 = []; // Array to store build percent values
                    $newbp = 0;
                    while ($row = mysqli_fetch_array($wosql_data)) {
                        $name = $row['Name'];
                        $description = $row['description'];
                        $prod_no = $row['Prod_Order_No'];
                        $product = $row['product'];
                        $batch_no = $row['batch_no'];
                        $code = $row['Code'];
                        $start = $row['Act_Start'];
                        $end = $row['Act_End'];
                        $remarks = $row['remarks'];
                        $wo_qty = $row['Qty_Make'];
                        $stations = $row['Stations'];
                        $part_no = $row['Part_No'];
                        $detailed_duration = $row['Duration'];
                        $std = $row['cycle_time'];
                        $bp = $row['build_percent'];
                        $module_bp = $row['module_build_percent'];

                        $checking_previous_batch = mysqli_query($conn, "SELECT COUNT(batch_no) as check_batch_no,SUM(build_percent/100) AS build_percent FROM prod_dtr WHERE DATE < '$datefrom' AND batch_no='$batch_no' AND Name='$name' AND description='$description'");
                        $checking_previous_batch_row = mysqli_fetch_assoc($checking_previous_batch);
                        $checking = $checking_previous_batch_row['check_batch_no'];
                        $checking_bp = number_format($checking_previous_batch_row['build_percent'], 2);
                        // $matchFound = false; // Flag to track if a match is found

                        // foreach ($descArray as $desc) {
                        //     // Check if $desc matches $description and assign specific values accordingly
                        //     if ($desc == $description) {
                        //         $desc . " & " . $description . " (Match found)";

                        //         // Assign the specific value for the description
                        //         if (isset($values[$desc])) {
                        //             $all_desc[$desc] = $values[$desc];
                        //             if ($bp == 1) {
                        //                 $newbp = $all_desc[$desc];
                        //             } else {
                        //                 $newbp = $bp;
                        //             }
                        //         } else {
                        //             $all_desc[$desc] = 0;
                        //         }
                        //         break; // Exit the loop if a match is found
                        //     } elseif ($stations == 'FVI MODULE' || $stations == 'FVI MAC') {
                        //         if ($bp == 1) {
                        //             $newbp = 2.67;
                        //             // echo $newbp;
                        //             break;
                        //         } else {
                        //             $newbp = $bp;
                        //         }
                        //     } elseif ($stations == 'FINAL INT') {
                        //         if ($bp == 1) {
                        //             $newbp = 0.57;
                        //             // echo $newbp;
                        //             break;
                        //         } else {
                        //             $newbp = $bp;
                        //         }
                        //     }
                        // }
                        // $matchFound = isset($all_desc[$description]); // Check if a match was found

                        // if (!$matchFound) {
                        //     //echo "No match found";
                        // }
                        $output = round($bp, 2);

                        if ($stations == 'FVI MODULE' || $stations == 'FVI MAC') {
                            $std_cycle_time = "3.00";
                        } elseif ($stations == 'SUB TEST') {
                            $std_cycle_time = "2.00";
                        } else {
                            $std_cycle_time = $std;
                        }
                        // echo $std_cycle_time;
                        $array1[] = $std_cycle_time * $wo_qty;
                        $array2[] = $output;

                        $detailed_std = $std_cycle_time != 0 ? number_format($std_cycle_time * $wo_qty, 2) : 0;
                        // $std_status = number_format(($module_bp / 100) * $detailed_std, 2);
                        $detailed_actual = $detailed_duration != 0 ? number_format(8 / $detailed_duration, 2) : 0;
                        // $bp_minus_module_bp = ($module_bp - ($output * 100)) / 100;
                        $output_continue = $detailed_std * (1 - $checking_bp);

                        if ($checking == 0) {
                            $eff = $detailed_std;
                            $detailed_eff = $output != 0 ? number_format(($detailed_std / $detailed_actual) * 100) : 0;
                        } else {
                            $eff = $output_continue;
                            $detailed_eff = $output != 0 ? number_format(($output_continue / $detailed_actual) * 100) : 0;
                        }

                        // Sum $detailed_eff for each unique name
                        if (!isset($sum_detailed_eff[$name])) {
                            $sum_detailed_eff[$name] = $detailed_eff;
                            $sum_detailed_output[$name] = $output * 100;
                        } else {
                            $sum_detailed_eff[$name] += $detailed_eff;
                            $sum_detailed_output[$name] += $output * 100;
                        }

                        $STDxOUTPUT = round(array_sum(array_map(function ($a, $b) {
                            return $a * $b;
                        }, $array1, $array2)));
                        $WHxMP = 8 * $total_headcount;
                        $total_efficiency = $WHxMP != 0 ? number_format(($STDxOUTPUT / $WHxMP) * 100, 2) : 0;
                        $results[] = [
                            'name' => $name,
                            'description' => $description,
                            'prod_no' => $prod_no,
                            'product' => $product,
                            'batch_no' => $batch_no,
                            'code' => $code,
                            'start' => $start,
                            'end' => $end,
                            'remarks' => $remarks,
                            'wo_qty' => $wo_qty,
                            'stations' => $stations,
                            'part_no' => $part_no,
                            'detailed_duration' => $detailed_duration,
                            'build_percent' => $output,
                            'detailed_std' => $detailed_std,
                            'detailed_actual' => $detailed_actual,
                            'detailed_eff' => $detailed_eff,
                            'WHxMP' => $WHxMP,
                            'STDxOUTPUT' => $STDxOUTPUT,
                            'OVERALLEFF' => $total_efficiency,
                            'desc' => $all_desc,
                            'total_bp' => $module_bp,
                            'output_continue' => $output_continue,
                            'eff' => $eff, 'check_bp' => $checking_bp,
                            'check' => $checking_previous_batch_row
                        ];
                    }


                    // Create the final result array with unique names, summed detailed efficiency, and output
                    foreach ($sum_detailed_eff as $name => $sum_eff) {
                        $results[] = [
                            'name' => $name,
                            'sum_detailed_eff' => $sum_eff,
                            'output' => isset($sum_detailed_output[$name]) ? $sum_detailed_output[$name] : null
                        ];
                    }

                    return $results;
                }

                if (isset($_POST['filter'])) {
                    $datefrom = $_POST['datefrom'];
                    $dateto = $_POST['dateto'];

                ?>
                    <div class="container">
                        <div class="row mx-0">
                            <div class="col">
                                <table class="table table-sm table-hover table-bordered display compact " id="table1">
                                    <thead class="table-secondary">
                                        <tr>
                                            <td colspan="32" class="text-dark bg-light">
                                                <h4 style="background-color: #ADD8E6" class="fw-bold">MAIN OVERALL EFFFICIENCY</h4>
                                                <h6 class="fw-bold"> FROM: <?php echo $datefrom . " TO: " . $dateto; ?></h6>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th></th>
                                            <th>Total</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        $results = OVRPRODUCTEFF($conn, $datefrom, $dateto);
                                        $result_jlp = $results[0]; // Get JLP results
                                        $result_pnp = $results[1]; // Get PNP results
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
                                        // foreach ($result_pnp as $results) {
                                        //     if (isset($results['PNP_STDxOUTPUT']) && isset($results['PNP_WHxMP']) && isset($results['PNP_TOTALHC']) && isset($results['PNP_OVERALLEFF'])) {
                                        //         $STDxOUTPUT_pnp = $results['PNP_STDxOUTPUT'];
                                        //         $WHxMP_pnp = $results['PNP_WHxMP'];
                                        //         $TOTALHC_pnp = $results['PNP_TOTALHC'];
                                        //         $OVERALLEFF_pnp = $results['PNP_OVERALLEFF'];
                                        //     }
                                        // }
                                        $OVR_STDxOUTPUT = $STDxOUTPUT_jlp + $STDxOUTPUT_pnp + $STDxOUTPUT_olb + $STDxOUTPUT_jtp;
                                        $OVER_WHxMP = round($WHxMP_jlp + $WHxMP_pnp + $WHxMP_olb + $WHxMP_jtp);

                                        if ($OVR_STDxOUTPUT != 0 && $OVER_WHxMP != 0) {
                                            $OVR_EFF = round($OVR_STDxOUTPUT / $OVER_WHxMP * 100);
                                        } else {
                                            $OVR_EFF = 0;
                                        }
                                        ?>
                                        <tr>
                                            <td>STD X OUTPUT</td>
                                            <td class="fw-bold text-primary"><?php echo $OVR_STDxOUTPUT; ?></td>
                                        </tr>
                                        <tr>
                                            <td>WH X MP</td>
                                            <td class="fw-bold text-primary"><?php echo $OVER_WHxMP; ?></td>
                                        </tr>
                                        <tr>
                                            <td class="fw-bold">Efficiency</td>
                                            <td class="fw-bold text-primary"><?php echo $OVR_EFF . "%"; ?></td>
                                        </tr>
                                    </tbody>
                                </table>
                                <table class="table table-sm table-hover table-striped table-bordered display compact " id="table2">
                                    <thead class="table-secondary">
                                        <tr>
                                            <td colspan="32" class="text-dark bg-light">
                                                <h4 style="background-color: #ADD8E6" class="fw-bold">EFFFICIENCY PER EMPLOYEE</h4>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th>Name</th>
                                            <th>Output</th>
                                            <!-- <th>Std</th> -->
                                            <th>Efficiency</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        $results = calculateDetailedEfficiency($conn, $datefrom, $dateto);
                                        foreach ($results as $result) {
                                            if (isset($result['sum_detailed_eff'])) {
                                        ?>
                                                <tr class="text-center">
                                                    <td><?php echo $result['name']; ?></td>
                                                    <td><?php echo $result['output'] . "%"; ?></td>
                                                    <!-- <td><?php echo number_format($result['std_status'], 2); ?></td> -->
                                                    <td class="text-primary fw-bolder"><?php echo round($result['sum_detailed_eff']); ?>%</td>
                                                </tr>
                                        <?php
                                            }
                                        }
                                        ?>
                                    </tbody>
                                </table>
                            </div>
                            <div class="col">
                                <table class="table table-sm table-hover table-bordered display compact text-center " id="table3">
                                    <thead class="table-secondary">
                                        <tr>
                                            <td colspan="32" class="text-dark bg-light">
                                                <h4 style="background-color: #ADD8E6" class="fw-bold">JLP OVERALL EFFICIENCY</h4>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th></th>
                                            <th>H/C</th>
                                            <th>STD X OUTPUT</th>
                                            <th>WH X MP</th>
                                            <th>Efficiency</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr class="text-center">
                                            <td class="fw-bold">OVERALL:</td>
                                            <td><?php echo $TOTALHC_jlp; ?></td>
                                            <td><?php echo round($STDxOUTPUT_jlp); ?></td>
                                            <td><?php echo $WHxMP_jlp; ?></td>
                                            <td class="text-primary fw-bolder"><?php echo round($OVERALLEFF_jlp) . "%"; ?></td>
                                        </tr>
                                    </tbody>
                                </table>
                                <table class="table table-sm table-hover table-bordered display compact text-center " id="table4">
                                    <thead class="table-secondary">
                                        <tr>
                                            <td colspan="32" class="text-dark bg-light">
                                                <h4 style="background-color: #ADD8E6" class="fw-bold">PNP OVERALL EFFICIENCY</h4>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th></th>
                                            <th>H/C</th>
                                            <th>STD X OUTPUT</th>
                                            <th>WH X MP</th>
                                            <th>Efficiency</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr class="text-center">
                                            <td class="fw-bold">OVERALL:</td>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                            <td class="text-primary fw-bolder"></td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    <div class="accordion accordion-flush m-1" id="accordionFlushExample">
                        <div class="accordion-item">
                            <h2 class="accordion-header">
                                <button class="accordion-button collapsed" style="background-color: #ADD8E6" type="button" data-bs-toggle="collapse" data-bs-target="#flush-collapseOne" aria-expanded="false" aria-controls="flush-collapseOne">
                                    VIEW DETAILS
                                </button>
                            </h2>
                            <div id="flush-collapseOne" class="accordion-collapse collapse" data-bs-parent="#accordionFlushExample">
                                <div class="accordion-body">
                                    <table class="table table-sm table-hover table-bordered display compact mt-3 fs-6" id="table5">
                                        <thead class="table-secondary">
                                            <tr>
                                                <td colspan="32" class="text-dark bg-light">
                                                    <h4 style="background-color: #ADD8E6" class="fw-bold">DETAILED EFFFICIENCY</h4>
                                                </td>
                                            </tr>
                                            <tr class="">
                                                <th>TECHNICIAN</th>
                                                <th>MODULE</th>
                                                <th>STATION</th>
                                                <th>PROD NO.</th>
                                                <th>PRODUCT</th>
                                                <th>PART NO.</th>
                                                <th>SERIAL NO.</th>
                                                <th>ACTIVITY</th>
                                                <th>STARTED</th>
                                                <th>ENDED</th>
                                                <th>REMARKS</th>
                                                <th>HRS</th>
                                                <th>QTY</th>
                                                <th>OUTPUT TODAY</th>
                                                <th>STATUS</th>
                                                <th>STD CYCLE TIME</th>
                                                <th>EFF</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                            $results = calculateDetailedEfficiency($conn, $datefrom, $dateto);
                                            foreach ($results as $result) {
                                                if (!isset($result['sum_detailed_eff'])) {
                                            ?>
                                                    <tr class="text-center">
                                                        <td><?php echo $result['name']; ?></td>
                                                        <td><?php echo $result['description']; ?></td>
                                                        <td><?php echo $result['stations']; ?></td>
                                                        <td><?php echo $result['prod_no']; ?></td>
                                                        <td><?php echo $result['product']; ?></td>
                                                        <td><?php echo $result['part_no']; ?></td>
                                                        <td><?php echo $result['batch_no']; ?></td>
                                                        <td><?php echo $result['code']; ?></td>
                                                        <td><?php echo $result['start']; ?></td>
                                                        <td><?php echo $result['end']; ?></td>
                                                        <td><?php echo $result['remarks']; ?></td>
                                                        <td><?php echo $result['detailed_actual']; ?></td>
                                                        <td><?php echo $result['wo_qty']; ?></td>
                                                        <td><?php echo ($result['build_percent'] * 100) . "%"; ?></td>
                                                        <td><?php echo $result['total_bp'] . "%"; ?></td>
                                                        <td><?php echo number_format($result['eff'], 2); ?></td>
                                                        <td class="text-primary fw-bold"><?php echo round($result['detailed_eff']) . "%"; ?></td>
                                                    </tr>
                                            <?php
                                                }
                                            }
                                            ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php } ?>
            </form>
        </div>

        <script>
            // Hide the spinner by default
            document.getElementById("spinner").style.display = "none";
            const viewBtn = document.getElementById("filter");
            const spinner = document.getElementById("spinner");
            const table = document.getElementById("efficiencysummary");

            viewBtn.addEventListener("click", async () => {
                spinner.style.display = "block";
                table.style.display = "none";

                const datefrom = document.getElementById("datefrom").value;
                const dateto = document.getElementById("dateto").value;
                const url = `cable_efficiency_summary.php?datefrom=${datefrom}&dateto=${dateto}`;

                try {
                    const response = await fetch(url);
                    const data = await response.text();
                    table.innerHTML += data;
                } catch (error) {
                    console.error(error);
                    alert("Failed to fetch data. Please try again.");
                } finally {
                    spinner.style.display = "none";
                    table.style.display = "table";
                }
            });
        </script>
        <script>
            // function exportTablesToExcel() {
            //     var tables = ['table1', 'table2', 'table3']; // Array of table IDs

            //     var workbook = new ExcelJS.Workbook();

            //     tables.forEach(function(tableID) {
            //         var worksheet = workbook.addWorksheet(tableID);

            //         var table = document.getElementById(tableID);
            //         var rows = table.getElementsByTagName('tr');

            //         for (var i = 0; i < rows.length; i++) {
            //             var row = rows[i];
            //             var cells = row.getElementsByTagName('td');

            //             for (var j = 0; j < cells.length; j++) {
            //                 var cell = cells[j];
            //                 var value = cell.innerText || cell.textContent;

            //                 worksheet.getCell(String.fromCharCode(65 + j) + (i + 1)).value = value;
            //             }
            //         }
            //     });

            //     workbook.xlsx.writeBuffer().then(function(buffer) {
            //         var blob = new Blob([buffer], {
            //             type: 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'
            //         });
            //         var url = URL.createObjectURL(blob);
            //         var link = document.createElement('a');
            //         link.href = url;
            //         link.download = 'tables.xlsx';
            //         link.click();
            //         URL.revokeObjectURL(url);
            //     });
            // }
            function exportToExcel() {
                // Create a new Workbook
                var workbook = new ExcelJS.Workbook();

                // Add a new Worksheet
                var worksheet = workbook.addWorksheet('Sheet 1');

                // Get the table elements by their IDs
                var table1 = document.getElementById('table1');
                var table2 = document.getElementById('table2');
                var table3 = document.getElementById('table3');
                var table4 = document.getElementById('table4');
                var table5 = document.getElementById('table5');

                // Set the starting row index in the Worksheet
                var rowIndex = 1;

                // Export Table 1
                rowIndex = exportTableToWorksheet(table1, worksheet, rowIndex);

                // Export Table 2
                rowIndex = exportTableToWorksheet(table2, worksheet, rowIndex);

                // Export Table 3
                rowIndex = exportTableToWorksheet(table3, worksheet, rowIndex);

                // Export Table 4
                rowIndex = exportTableToWorksheet(table4, worksheet, rowIndex);

                // Export Table 5
                rowIndex = exportTableToWorksheet(table5, worksheet, rowIndex);


                // Save the Workbook as an Excel file
                workbook.xlsx.writeBuffer().then(function(buffer) {
                    var blob = new Blob([buffer], {
                        type: 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'
                    });
                    var url = URL.createObjectURL(blob);
                    var a = document.createElement('a');
                    a.href = url;
                    a.download = 'tables.xlsx';
                    a.click();
                });
            }

            function exportTableToWorksheet(table, worksheet, rowIndex) {
                // Get the table header row
                var headerRow = table.rows[0];

                // Create a new Row in the Worksheet for the header
                var excelHeaderRow = worksheet.getRow(rowIndex);

                // Iterate over each cell in the header row
                for (var j = 0; j < headerRow.cells.length; j++) {
                    var cell = headerRow.cells[j];

                    // Add the cell value to the Excel header row and set the font style to bold
                    var excelCell = excelHeaderRow.getCell(j + 1);
                    excelCell.value = cell.innerText;
                    excelCell.font = {
                        bold: true
                    };
                }

                // Increment the row index for data rows
                rowIndex++;

                // Iterate over each data row in the table
                for (var i = 1; i < table.rows.length; i++) {
                    var row = table.rows[i];

                    // Create a new Row in the Worksheet for data
                    var excelRow = worksheet.getRow(rowIndex);

                    // Iterate over each cell in the data row
                    for (var j = 0; j < row.cells.length; j++) {
                        var cell = row.cells[j];

                        // Add the cell value to the Excel Row
                        excelRow.getCell(j + 1).value = cell.innerText;
                    }

                    // Increment the row index
                    rowIndex++;
                }

                // Add an empty row as a separator between tables
                rowIndex++;
                return rowIndex;
            }
        </script>
        <script>
            var myApp = new function() {
                this.printTable = function() {
                    var tab = document.getElementById('efficiencysummary');
                    var win = window.open('', '', 'height=700,width=700');
                    win.document.write(tab.outerHTML);
                    win.document.close();
                    win.print();
                }
            }
        </script>
</body>
</center>

</html>