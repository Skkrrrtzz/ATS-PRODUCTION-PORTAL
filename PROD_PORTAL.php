<?php include 'ATS_Prod_Header.php' ?>
<?php include 'PROD_navbar.php' ?>
<?php require_once 'PROD_dashboard.php' ?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PRODUCTION PORTAL</title>

    <!-- include the Chart.js library -->
    <script src="./assets/js/Chart.js"></script>
    <script src="./assets/js/Chartjsannotation.js"></script>
    <script src="assets/js/chartjs-plugin-datalabels.min.js"></script>
    <script src="./assets/js/exceljs.min.js"></script>
    <style>
        .card {
            background-color: #f8f9fa;
        }

        .card-title {
            font-size: 1.5rem;
        }

        .card-text.text-md-start.text-success .variable {
            display: inline-block;
            margin-left: 10px;
        }

        .custom-bg-cable {
            background-color: rgba(107, 114, 142, 1);
        }

        .custom-bg-main {
            background-color: rgba(64, 66, 88, 1);
        }

        .no-underline {
            text-decoration: none;
        }

        .input-group-text {
            padding-right: 10px;
        }

        .rounded-table {
            border-collapse: separate;
            border-spacing: 0;
            border-radius: 10px;
            overflow: hidden;
        }

        .rounded-table th,
        .rounded-table td {
            padding: 6px;
        }

        /* .chart-container {
            height: auto;
            flex: 0 0 auto;
            width: 100%;
        } */

        /* @media (max-width: 767px) {
            .col-6 {
                width: 100%;
            }

            #att_chart {
                height: 300px;
                width: 100%;
            }

            #eff_chart {
                height: 300px;
                width: 100%;
            }

            #yield_chart {
                height: 300px;
                width: 100%;
            }

            #Leadtime {
                height: 300px;
                width: 100%;
            }

            #skill_matrix_main {
                height: 300px;
                width: 100%;
            }

            #skill_matrix_cable {
                height: 300px;
                width: 100%;
            }
        } */
    </style>
</head>

<body class="bg-dark-subtle">
    <div class="row g-0 text-center" id="dashboard">
        <div class="col-sm-6 col-md-8 bg-dark-subtle">
            <div class="row m-1">
                <div class="container d-flex justify-content-between">
                    <div class="d-inline-flex">
                        <div class="input-group">
                            <span class="input-group-text fw-bolder"><i class="fa-solid fa-calendar-day"></i></span>
                            <input type="date" id="date" name="date" class="form-control" value="<?php echo getDefaultDate(); ?>
                            ">
                            <input type="submit" id="filter" name="filter" value="View" class="btn btn-primary m-1">
                        </div>
                    </div>
                    <div class="d-flex flex-row-reverse">
                        <button type="button" onclick="printCharts('dashboard')" class="btn btn-secondary m-1">Print</button>
                        <button type="button" onclick="saveCharts('dashboard')" class="btn btn-secondary m-1">Save</button>
                    </div>
                </div>
                <div class="col-sm-6 p-2">
                    <div class="card">
                        <div class="card-header bg-primary-subtle">
                            <h5 class="fw-bold " type="button" data-bs-toggle="modal" data-bs-target="#AllAttendance"> Attendance Rate <i class="fas fa-info-circle"></i></h5>
                        </div>
                        <div class="card-body">
                            <h4><i class="fa fa-users"></i>Today: <?php echo $ATT_OVERALL . "%", " Absent ", $all_abs ?></h4>
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
                <div class="col-sm-6 p-2">
                    <div class="card">
                        <div class="card-header bg-primary-subtle">
                            <h5 class="fw-bold" type="button" data-bs-toggle="modal" data-bs-target="#"> Efficiency Rate <i class="fas fa-info-circle"></i></h5>
                        </div>
                        <div class="card-body">
                            <h4><i class="fa fa-line-chart"></i>Today: <?php echo $ALL_EFF . "%"; ?></h4>
                            <div class="chart-container">
                                <input type="checkbox" id="toggleWeekly" />
                                <label for="toggleWeekly">Weekly</label>
                                <canvas id="eff_chart"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- <div class="col-sm col-sm-4 p-2">
                    <div class="card">
                        <div class="card-header bg-primary-subtle">
                            <h5 class="fw-bold" type="button" data-bs-toggle="modal" data-bs-target="#staticBackdrop">Production Yield <i class="fas fa-info-circle"></i></h5>
                        </div>
                        <div class="card-body">
                            <h4><i class="fa-solid fa-chart-column"></i></i><?php echo $yield, "%" ?></h4>
                            <div class="chart-container">
                                <canvas id="yield_chart"></canvas>
                            </div>
                        </div>
                    </div>
                </div> -->
                <div class="col-sm-6 p-2 h-100">
                    <div class="card">
                        <div class="card-header bg-primary-subtle">
                            <!-- Button trigger modal -->
                            <h5 class="fw-bold text-center" type="button" data-bs-toggle="modal" data-bs-target="#staticBackdrop">Lead Time <i class="fas fa-info-circle"></i></h5>
                        </div>
                        <div class="card-body">
                            <!-- <h4><i class="fa-solid fa-clock"></i></h4> -->
                            <div class="chart-container">
                                <canvas id="Leadtime" height="180px"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-sm-6 p-2">
                    <div class="card">
                        <div class="card-header bg-primary-subtle">
                            <a class="no-underline text-dark" href="Generate Reports/module_build_status.php">
                                <h5 class="fw-bold text-center">Build Status <i class="fas fa-info-circle"></i></h5>
                            </a>
                        </div>
                        <div class="card-body">
                            <!-- <h4><i class="fa-solid fa-bars-progress"></i></h4> -->
                            <div class="chart-container">
                                <canvas id="buildStatusChart" height="180px"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm col-md-4 bg-dark-subtle">
            <div class="container-fluid m-2 mx-0" id="WIP">
                <div class="row">
                    <div class="col-sm">
                        <div class="card">
                            <div class="card-header bg-primary-subtle">
                                <!-- <a class="no-underline text-dark" href="Generate Reports/cable_wip.php?linkTitle=CABLE">
                                    <h5 class="fw-bold text-start fs-4 "><i class="fa-solid fa-bars-progress"></i> CABLE</h5>
                                </a> -->
                                <h4 class="fw-bold" id="modalLink" type="button" data-bs-toggle="modal" data-bs-target="#CableWIPModal">
                                    CABLE WIP <i class="fas fa-info-circle"></i>
                                </h4>
                            </div>

                            <div class="card-body">
                                <h3 class="text-start fs-4"> <span class="badge text-bg-success"><i class="fa-solid fa-play"></i> IN-PROCESS</span> <span class="float-end" id="total_cable_inprocess"><?php echo $updatedValues['total_cable_inprocess']; ?></span></h3>
                                <h3 class="text-start fs-4"> <span class="badge text-bg-secondary"><i class="fas fa-hourglass-end"></i> INDIRECT</span> <span class="float-end" id="total_cable_indirect"><?php echo $updatedValues['total_cable_indirect']; ?></span></h3>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm">
                        <div class="card">
                            <div class="card-header bg-primary-subtle">
                                <!-- <a class="no-underline text-dark" href="Generate Reports/module_wip.php?linkTitle=MAIN">
                                    <h5 class=" fw-bold text-start fs-4 "><i class=" fa-solid fa-bars-progress"></i> MAIN</h5>
                                </a> -->
                                <h4 class="fw-bold" id="modalLink" type="button" data-bs-toggle="modal" data-bs-target="#MainWIPModal">
                                    MAIN WIP <i class="fas fa-info-circle"></i>
                                </h4>
                            </div>
                            <div class="card-body">
                                <h3 class="text-start fs-4"> <span class="badge text-bg-success"><i class="fa-solid fa-play"></i> IN-PROCESS</span> <span class="float-end" id="total_main_inprocess"><?php echo $updatedValues['total_main_inprocess']; ?></span></h3>
                                <h3 class="text-start fs-4"> <span class="badge text-bg-secondary"><i class="fas fa-hourglass-end"></i> INDIRECT</span> <span class="float-end" id="total_main_idl"><?php echo $updatedValues['total_main_idl']; ?></span></h3>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="container-fluid m-2 mx-0">
                <div class="col-sm col-md p-1">
                    <div class="card bg-light">
                        <div class="card-header bg-primary-subtle">
                            <!-- Button trigger modal -->
                            <h4 class="fw-bold" type="button" data-bs-toggle="modal" data-bs-target="#MainMatrixModal">Main Production Skill <i class="fas fa-info-circle"></i></h4>
                        </div>
                        <div class="card-body">
                            <div class="chart-container">
                                <canvas id="skill_matrix_main" height="180px"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-sm col-md p-1">
                    <div class="card bg-light">
                        <div class="card-header bg-primary-subtle">
                            <!-- Button trigger modal -->
                            <h4 class="fw-bold" type="button" data-bs-toggle="modal" data-bs-target="#CableMatrixModal">Cable Production Skill <i class="fas fa-info-circle"></i></h4>
                        </div>
                        <div class="card-body">
                            <div class="chart-container">
                                <canvas id="skill_matrix_cable" height="180px"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
    <!--Attendance Cable and Main Modal -->
    <div class="modal fade" id="AllAttendance" tabindex="-1" aria-labelledby="AllAttendanceLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl">
            <div class=" modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5" id="AllAttendanceLabel">Attendance Rate Cable and Main</h1>
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

    <!--Main Matrix Modal -->
    <div class="modal fade" id="MainMatrixModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="MatrixModal" aria-hidden="true">
        <div class="modal-dialog modal-fullscreen modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header bg-primary">
                    <h1 class="modal-title fs-5 text-white" id="MatrixModal">Main Production Skill</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body py-0 my-0">
                    <div class="position-relative">
                        <table class="table table-bordered fs-6 table-sm border-dark table-hover display compact" id="MainMatrix">
                            <thead class="sticky-top">
                                <tr class="text-center">
                                    <th class="text-bg-dark" colspan="4">TECHNICIANS</th>
                                    <th class="bg-primary-subtle" colspan="11">JLP</th>
                                    <th class="bg-info-subtle" colspan="2">MATRIX</th>
                                    <th class="bg-secondary" colspan="2">TEST</th>
                                </tr>
                                <tr class="text-center">
                                    <th class="text-bg-dark">No.</th>
                                    <th class="text-bg-dark">NAME</th>
                                    <th class="text-bg-dark">EMP ID</th>
                                    <th class="text-bg-dark">TECH LEVEL</th>
                                    <th class="text-black bg-primary-subtle">CDA</th>
                                    <th class="text-black bg-primary-subtle">CDM</th>
                                    <th class="text-black bg-primary-subtle">TSL</th>
                                    <th class="text-black bg-primary-subtle">FA</th>
                                    <th class="text-black bg-primary-subtle">TXP</th>
                                    <th class="text-black bg-primary-subtle">AC</th>
                                    <th class="text-black bg-primary-subtle">FC</th>
                                    <th class="text-black bg-primary-subtle">MTP</th>
                                    <th class="text-black bg-primary-subtle">ION</th>
                                    <th class="text-black bg-primary-subtle">FLIP</th>
                                    <th class="text-black bg-primary-subtle">INT</th>
                                    <th class="text-black bg-info-subtle">SUB ASSY</th>
                                    <th class="text-black bg-info-subtle">INT</th>
                                    <th class="text-black bg-secondary">SUB TEST</th>
                                    <th class="text-black bg-secondary">F-TEST</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                // Query the database to retrieve data
                                $matrix_query = "SELECT * FROM prod_skills_matrix";
                                $matrix_result = mysqli_query($conn, $matrix_query);
                                $lvl_query = "SELECT TECH_LVL, COUNT(TECH_LVL) AS LVL FROM `prod_skills_matrix` GROUP BY TECH_LVL ASC;";
                                $lvl_result = mysqli_query($conn, $lvl_query);
                                // Check if the query was successful
                                if ($matrix_result && $lvl_query) {

                                    // Generate the table rows dynamically
                                    while ($row = mysqli_fetch_assoc($matrix_result)) {
                                        // Increment the count based on the technician level
                                        $technicianLevel = $row['TECH_LVL'];
                                        $number = filter_var($technicianLevel, FILTER_SANITIZE_NUMBER_INT);
                                        $trimmedNumber = ($number !== false) ? intval($number) : '';

                                        echo "<tr class='text-center'>";
                                        echo "<td>" . $row['ID'] . "</td>";
                                        echo "<td>" . $row['Name'] . "</td>";
                                        echo "<td>" . $row['Emp_ID'] . "</td>";

                                        echo "<td class='" . (($trimmedNumber == 1) ? 'text-bg-warning bg-gradient' : (($trimmedNumber == 2) ? 'text-bg-primary bg-gradient' : (($trimmedNumber == 3) ? 'text-bg-success bg-gradient' : 'text-bg-secondary bg-gradient'))) . "'>" . $row['TECH_LVL'] . "</td>";
                                        echo "<td class='bg-primary-subtle'>" . $row['CDA'] . "</td>";
                                        echo "<td class='bg-primary-subtle'>" . $row['CDM'] . "</td>";
                                        echo "<td class='bg-primary-subtle'>" . $row['TSL'] . "</td>";
                                        echo "<td class='bg-primary-subtle'>" . $row['FA'] . "</td>";
                                        echo "<td class='bg-primary-subtle'>" . $row['TXP'] . "</td>";
                                        echo "<td class='bg-primary-subtle'>" . $row['AC'] . "</td>";
                                        echo "<td class='bg-primary-subtle'>" . $row['FC'] . "</td>";
                                        echo "<td class='bg-primary-subtle'>" . $row['MTP'] . "</td>";
                                        echo "<td class='bg-primary-subtle'>" . $row['ION'] . "</td>";
                                        echo "<td class='bg-primary-subtle'>" . $row['FLIP'] . "</td>";
                                        echo "<td class='bg-primary-subtle'>" . $row['INTEGRATION'] . "</td>";
                                        echo "<td class='text-black bg-info-subtle'>" . $row['PNP_SUB_ASSY'] . "</td>";
                                        echo "<td class='text-black bg-info-subtle'>" . $row['PNP_INT'] . "</td>";
                                        echo "<td class='text-black bg-secondary'>" . $row['SUB_TEST'] . "</td>";
                                        echo "<td class='text-black bg-secondary'>" . $row['FINAL_TEST'] . "</td>";
                                        echo "</tr>";
                                    }
                                    mysqli_free_result($matrix_result);
                                } else {
                                    // Handle the case when the query fails
                                    echo "Error: " . mysqli_error($conn);
                                }
                                ?>
                            </tbody>
                        </table>
                        <?php
                        // Initialize an array to store the level counts
                        $levelCounts = array();

                        // Fetch the results and store the counts in the array
                        while ($row = mysqli_fetch_assoc($lvl_result)) {
                            $levelCounts[$row['TECH_LVL']] = $row['LVL'];
                        } ?>
                        <!-- Display the level counts -->
                        <div class='row text-start mx-0'>
                            <div class="col">
                                <span class='badge text-bg-warning fs-4'>LEVEL 1: <?php echo $levelCounts['LVL 1']; ?></span>
                                <span class='badge text-bg-primary fs-4'>LEVEL 2: <?php echo $levelCounts['LVL 2']; ?></span>
                                <span class='badge text-bg-success fs-4'>LEVEL 3: <?php echo $levelCounts['LVL 3']; ?></span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-success" id="exportMainMatrix">Export to Excel</button>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <!--Cable Matrix Modal -->
    <div class="modal fade" id="CableMatrixModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
        <div class="modal-dialog modal-fullscreen">
            <div class="modal-content">
                <div class="modal-header bg-primary">
                    <h1 class="modal-title fs-5 text-white" id="staticBackdropLabel">Cable Production Skill</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body py-0 my-0">
                    <div class="position-relative">
                        <table class="table table-bordered fs-6 table-sm border-dark table-hover display compact" id="CableMatrix">
                            <thead class="sticky-top">
                                <tr class="text-center">
                                    <th class="text-bg-dark">No.</th>
                                    <th class="text-bg-dark">NAME</th>
                                    <th class="text-bg-dark">EMP ID</th>
                                    <th class="text-bg-dark">SKILL LEVEL</th>
                                    <th class="text-black bg-warning">Manual Cutting</th>
                                    <th class="text-black bg-warning">Manual Stripping</th>
                                    <th class="text-black bg-warning">Manual Crimping</th>
                                    <th class="text-black bg-success-subtle">Semi-Auto Wire Crimp</th>
                                    <th class="text-black bg-success-subtle">Machine set Up</th>
                                    <th class="text-black bg-warning-subtle">Soldering</th>
                                    <th class="text-black bg-warning-subtle">Molding</th>
                                    <th class="text-black bg-primary-subtle">Wire Harnessing</th>
                                    <th class="text-black bg-primary-subtle">Final Assembly</th>
                                    <th class="text-black bg-info">Machine Change-over</th>
                                    <th class="text-black bg-info-subtle">Labelling</th>
                                    <th class="text-black bg-info-subtle">Electrical Testing </th>
                                    <th class="text-black bg-info-subtle">Visual Inspection</th>
                                    <th class="text-black bg-info-subtle">Pre Blocking</th>
                                    <th class="text-black bg-info-subtle">Taping</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                // Query the database to retrieve data
                                $matrix_query = "SELECT * FROM prod_skills_matrix_cable";
                                $matrix_result = mysqli_query($conn, $matrix_query);
                                $lvl_query = "SELECT SKILL_LVL, COUNT(SKILL_LVL) AS LVL FROM `prod_skills_matrix_cable` GROUP BY SKILL_LVL ASC;";
                                $lvl_result = mysqli_query($conn, $lvl_query);
                                // Check if the query was successful
                                if ($matrix_result && $lvl_query) {

                                    // Generate the table rows dynamically
                                    while ($row = mysqli_fetch_assoc($matrix_result)) {
                                        // Increment the count based on the technician level
                                        $technicianLevel = $row['SKILL_LVL'];
                                        $number = filter_var($technicianLevel, FILTER_SANITIZE_NUMBER_INT);
                                        $trimmedNumber = ($number !== false) ? intval($number) : '';

                                        echo "<tr class='text-center'>";
                                        echo "<td>" . $row['ID'] . "</td>";
                                        echo "<td>" . $row['Name'] . "</td>";
                                        echo "<td>" . $row['Emp_ID'] . "</td>";

                                        echo "<td class='" . (($trimmedNumber == 1) ? 'text-bg-warning bg-gradient' : (($trimmedNumber == 2) ? 'text-bg-primary bg-gradient' : (($trimmedNumber == 3) ? 'text-bg-success bg-gradient' : 'text-bg-secondary bg-gradient'))) . "'>" . $row['SKILL_LVL'] . "</td>";
                                        echo "<td class='bg-warning' >" . $row['MCUTTING'] . "</td>";
                                        echo "<td class='bg-warning' >" . $row['MSTRIPPING'] . "</td>";
                                        echo "<td class='bg-warning' >" . $row['MCRIMPING'] . "</td>";
                                        echo "<td class='bg-success-subtle' >" . $row['SAWC'] . "</td>";
                                        echo "<td class='bg-success-subtle' >" . $row['MsU'] . "</td>";
                                        echo "<td class='bg-warning-subtle' >" . $row['SOLDERING'] . "</td>";
                                        echo "<td class='bg-warning-subtle' >" . $row['MOLDING'] . "</td>";
                                        echo "<td class='bg-primary-subtle' >" . $row['WHARNESS'] . "</td>";
                                        echo "<td class='bg-primary-subtle' >" . $row['FINALASSY'] . "</td>";
                                        echo "<td class='bg-info' >" . $row['MCO'] . "</td>";
                                        echo "<td class='bg-info-subtle' >" . $row['LABELLING'] . "</td>";
                                        echo "<td class='bg-info-subtle' >" . $row['ETESTING'] . "</td>";
                                        echo "<td class='text-black bg-info-subtle' >" . $row['VI'] . "</td>";
                                        echo "<td class='text-black bg-info-subtle' >" . $row['PB'] . "</td>";
                                        echo "<td class='text-black bg-info-subtle'>" . $row['TAPING'] . "</td>";
                                        echo "</tr>";
                                    }
                                    mysqli_free_result($matrix_result);
                                } else {
                                    // Handle the case when the query fails
                                    echo "Error: " . mysqli_error($conn);
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-success" id="exportCableMatrix">Export to Excel</button>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal CABLE -->
    <div class="modal fade" id="CableWIPModal" tabindex="-1" aria-labelledby="CableModal" aria-hidden="true">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header bg-primary-subtle">
                    <h5 class="modal-title text-center" id="CableModal">CABLE WIP</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="table-responsive">
                        <table class="table table-sm table-bordered table-striped table-hover display compact rounded-table" id="eclipse1">
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
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal MAIN -->
    <div class="modal fade" id="MainWIPModal" tabindex="-1" aria-labelledby="MainModal" aria-hidden="true">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header bg-primary-subtle">
                    <h5 class="modal-title text-center" id="MainModal">MAIN WIP</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="table-responsive ">
                        <table class="table table-bordered table-striped table-hover display compact rounded-table" id="eclipse2">
                            <thead>
                                <tr class="table-primary text-center">
                                    <th>TECHNICIAN</th>
                                    <th>DESCRIPTION</th>
                                    <th>PRODUCT</th>
                                    <th>PROD NO</th>
                                    <th>BATCH</th>
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
                                    'DIRECT LABOR' => [
                                        'status' => 'IN-PROCESS',
                                        'color' => 'text-success'
                                    ],
                                    'INDIRECT LABOR' => [
                                        'status' => 'INDIRECT',
                                        'color' => 'text-warning'
                                    ]
                                ];

                                foreach ($categories as $category => $data) {
                                    $status = $data['status'];
                                    $color = $data['color'];

                                    $query = "SELECT Name, Stations, batch_no, description, Prod_Order_No, product, Act_Start, Qty_Make, Activity, remarks, NOW() as timer
                          FROM prod_dtr
                          WHERE Duration = '' AND Act_Start != '' AND wo_status = '$status'
                          ORDER BY Stations";

                                    $result = mysqli_query($conn, $query);

                                    echo "<tr class='bg-success-subtle'>
                        <th colspan='10' class='text-center $color bg-secondary-subtle'>$category</th>
                      </tr>";

                                    while ($row = mysqli_fetch_array($result)) {
                                        $now = $row['timer'];
                                        $Start = strtotime($row['Act_Start']);
                                        $End = strtotime($now);
                                        $Duration = ($End - $Start) / 60;
                                        $total = number_format($Duration);

                                        echo "<tr class='fw-bold text-center'>";
                                        echo "<td>" . $row['Name'] . "</td>";
                                        echo "<td>" . $row['description'] . "</td>";
                                        echo "<td>" . $row['product'] . "</td>";
                                        echo "<td>" . $row['Prod_Order_No'] . "</td>";
                                        echo "<td>" . $row['batch_no'] . "</td>";
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
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <!--ATTENDANCE TODAY MODAL -->
    <div class="modal fade" id="AllAttModal" tabindex="-1" aria-labelledby="AllAttModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h1 class="modal-title fs-5" id="AllAttModalLabel">Attendance Login Time</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="table-responsive">
                        <div class="container-fluid">
                            <div class="row">
                                <div class="col-6">
                                    <input type="hidden" id="clickedDateInput" name="clickedDate">
                                    <h4>CABLE <span id="cablePresentBadge" class="badge rounded-pill text-bg-success">Present: </span>
                                        <span id="cableAbsentBadge" class="badge rounded-pill text-bg-warning">Absent: </span>
                                    </h4>
                                    <table class="table table-hover fw-bold rounded-table" id="cableTable">
                                        <thead class="text-bg-primary">
                                            <tr>
                                                <th>Name</th>
                                                <th>Time In</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                        </tbody>
                                    </table>
                                </div>
                                <div class="col-6">
                                    <h4>MAIN <span id="prodPresentBadge" class="badge rounded-pill text-bg-success">Present: </span>
                                        <span id="prodAbsentBadge" class="badge rounded-pill text-bg-warning">Absent: </span>
                                    </h4>
                                    <table class="table table-hover fw-bold rounded-table" id="mainTable">
                                        <thead class="text-white bg-primary">
                                            <tr>
                                                <th>Name</th>
                                                <th>Time In</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <!-- <script>
        let showWeekly = false;
        let showYearly = false;

        // Function to update the chart based on the checkbox toggle
        function updateChart() {
            showWeekly = document.getElementById('toggleWeekly').checked;
            // showYearly = document.getElementById('toggleYearly').checked;

            // Update the chart data and options based on the showWeekly and showMonthly variables
            const newData = getChartData(showWeekly);
            Attchart.data.labels = newData.labels;
            Attchart.data.datasets[0].data = newData.cable;
            Attchart.data.datasets[1].data = newData.main;
            Attchart.update();

        }

        function getChartData(showWeekly) {
            let chartData = {
                labels: ['<?php echo $date; ?>'],
                cable: [<?php echo json_encode($cableData); ?>],
                main: [<?php echo json_encode($mainData); ?>]
            };

            if (showWeekly) {
                chartData.labels = <?php echo json_encode(getWeeklyDates($date)); ?>;
                chartData.cable = <?php echo json_encode(getWeeklyAttendanceData($conn, 'cable')); ?>;
                chartData.main = <?php echo json_encode(getWeeklyAttendanceData($conn, 'main')); ?>;
            }
            // else if (showYearly) {
            //     chartData.labels = <?php echo json_encode($yearly); ?>;
            //     chartData.cable = <?php echo json_encode(getYearlyAttendanceData($conn, 'cable')); ?>;
            //     chartData.main = <?php echo json_encode(getYearlyAttendanceData($conn, 'main')); ?>;
            // } 
            else {
                // Reset the chart data to the default data
                chartData.labels = ['<?php echo $date; ?>'];
                chartData.cable = [<?php echo json_encode($cable); ?>];
                chartData.main = [<?php echo json_encode($main); ?>];
            }

            return chartData;
        }
        // Create the initial chart with default data
        const ctx = document.getElementById('att_chart').getContext('2d');
        const Attchart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: ['<?php echo $date; ?>'],
                datasets: [{
                        label: 'Cable',
                        data: [<?php echo $cable; ?>],
                        backgroundColor: 'rgba(255, 177, 193)',
                        borderColor: 'rgba(255,99,132,255)',
                        borderWidth: 2
                    },
                    {
                        label: 'Main',
                        data: [<?php echo $main; ?>],
                        backgroundColor: 'rgba(154,208,245,255)',
                        borderColor: 'rgba(65,167,236,255)',
                        borderWidth: 2
                    }
                ]
            },
            options: {
                plugins: {
                    legend: {
                        display: true,
                        labels: {
                            color: 'black',
                            font: {
                                weight: 'bold'
                            }
                        }
                    },
                    tooltip: {
                        enabled: true,
                        callbacks: {
                            label: function(context) {
                                return context.dataset.label + ': ' + context.parsed.y + '%';
                            }
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
                    }
                },
                responsive: true,
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function(value) {
                                return value + '%';
                            }
                        }
                    }
                }
            }
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
    </script> -->
    <!-- <script src="/ATS/ATSPROD_PORTAL/assets/js/bootstrap.bundle.min.js"></script> -->

    <?php include 'Dashboard_Charts.php'; ?>
    <!-- <script>
        // Initialize the chart when the modal is shown
        $('#AllAttendance').on('shown.bs.modal', function() {
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
            const mainData = <?php echo json_encode($mainDailyData); ?>;
            const cableWeeklyData = <?php echo json_encode($cableWeeklyData); ?>;
            const mainWeeklyData = <?php echo json_encode($mainWeeklyData); ?>;

            // Create the chart
            const ctx = document.getElementById('att_chart').getContext('2d');
            const Attchart = new Chart(ctx, {
                type: 'bar',
                data: {
                    datasets: [{
                        label: 'Cable',
                        data: cableData,
                        backgroundColor: 'rgba(255, 177, 193)',
                        borderColor: 'rgba(255,99,132,255)',
                        borderWidth: 2
                    }, {
                        label: 'Main',
                        data: mainData,
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

        // Function to toggle the interval between 'daily', 'weekly'
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

            // Update chart labels and data
            Att.data.labels = Object.keys(newData);
            Att.data.datasets[0].data = Object.values(newData);
            Att.update();
        }

        // Chart data
        const OvrAllDailyAttendance = <?php echo json_encode($OverAllDailyData); ?>;
        const attendanceValues = Object.values(OvrAllDailyAttendance);

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
                    annotation: {
                        annotations: {
                            targetLine: {
                                type: 'line',
                                yMin: 95, // Target attendance rate 
                                yMax: 95,
                                borderColor: 'rgba(255, 174, 66)',
                                borderWidth: 2,
                                label: {
                                    enabled: true,
                                    content: 'Target: 95%',
                                    position: 'end',
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
                                return 'N/A';
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

        // Get the bar chart canvas element by ID
        const chartCanvas = document.getElementById('all_att_chart');

        chartCanvas.addEventListener('click', function(event) {
            const activePoints = Att.getElementsAtEventForMode(event, 'nearest', {
                intersect: true
            }, false);

            if (activePoints.length > 0) {
                const firstPoint = activePoints[0];
                const clickedDate = Att.data.labels[firstPoint.index];

                if (currentAllInterval === 'daily') {
                    // Use jQuery AJAX to fetch attendance data from PROD_dashboard.php
                    $.ajax({
                        url: 'PROD_dashboard.php',
                        method: 'GET',
                        data: {
                            clickedDate: encodeURIComponent(clickedDate)
                        },
                        dataType: 'json',
                        success: function(response) {
                            // console.log(response);
                            const cableAttendance = response.cable;
                            const prodAttendance = response.prod;
                            const cablePresent = response.cable_present;
                            const cableAbsent = response.cable_abs;
                            const prodPresent = response.prod_present;
                            const prodAbsent = response.prod_abs;

                            // Update the HTML elements with the cable attendance data
                            $('#cablePresentBadge').text(`Present: ${cablePresent}`);
                            $('#cableAbsentBadge').text(`Absent: ${cableAbsent}`);
                            $('#prodPresentBadge').text(`Present: ${prodPresent}`);
                            $('#prodAbsentBadge').text(`Absent: ${prodAbsent}`);

                            // Update the modal content with the fetched data
                            updateModalContent(response);

                            // Show the modal
                            const modal = new bootstrap.Modal(document.getElementById('AllAttModal'));
                            modal.show();
                        },
                        error: function(xhr, status, error) {
                            console.error('Request failed:', error);
                        }
                    });
                } else if (currentAllInterval === 'monthly') {
                    window.location.href = 'Generate Reports/cable_attendance_summary.php';
                }
            }
        });

        function updateModalContent(response) {
            const cableTableBody = $('#cableTable tbody');
            const mainTableBody = $('#mainTable tbody');

            // Clear the existing table rows
            cableTableBody.empty();
            mainTableBody.empty();

            // Populate the tables with attendance data
            $.each(response.cable, function(index, entry) {
                const row = `
            <tr>
                <td>${entry.Name}</td>
                <td>${entry.Time_In}</td>
            </tr>`;
                cableTableBody.append(row);
            });

            $.each(response.prod, function(index, entry) {
                const row = `
            <tr>
                <td>${entry.Name}</td>
                <td>${entry.Time_In}</td>
            </tr>`;
                mainTableBody.append(row);
            });
        }
    </script> -->

    <!-- <script>
        const ctx1 = document.getElementById("eff_chart").getContext("2d");
        const chart1 = new Chart(ctx1, {
            type: 'bar',
            data: {
                labels: ['<?php echo $date; ?>'],
                datasets: [{
                        label: 'Cable',
                        data: [<?php echo $operator_eff; ?>],
                        backgroundColor: 'rgba(255, 177, 193)',
                        borderColor: 'rgba(255,99,132,255)',
                        borderWidth: 2
                    },
                    {
                        label: 'Main',
                        data: [<?php echo $OVR_EFF; ?>],
                        backgroundColor: 'rgba(154,208,245,255)',
                        borderColor: 'rgba(65,167,236,255)',
                        borderWidth: 2
                    }
                ]
            },
            options: {
                maintainAspectRatio: true,
                plugins: {
                    legend: {
                        display: true,
                        labels: {
                            color: 'black',
                            font: {
                                weight: 'bold'
                            }
                        }
                    },
                    tooltip: {
                        enabled: true,
                        callbacks: {
                            label: function(context) {
                                return context.dataset.label + ': ' + context.parsed.y + '%';
                            }
                        }
                    },
                    annotation: {
                        annotations: {
                            targetLine: {
                                type: 'line',
                                yMin: 96, // Target attendance rate (96%)
                                yMax: 96, // Target attendance rate (96%)
                                borderColor: 'rgba(255, 174, 66)',
                                borderWidth: 2,
                                label: {
                                    enabled: true,
                                    content: 'Target: 96%', // The label content
                                    position: 'end', // Position of the label relative to the target line (start, center, end)
                                },
                            },
                        },
                    }
                },
                responsive: true,
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function(value) {
                                return value + '%';
                            }
                        }
                    }
                }
            }
        });
        // Add event listener to handle click on the chart
        document.getElementById('eff_chart').addEventListener('click', function(event) {
            const activePoints1 = chart1.getElementsAtEventForMode(event, 'nearest', {
                intersect: true
            }, true);
            if (activePoints1.length > 0) {
                const clickedDatasetIndex1 = activePoints1[0].datasetIndex;
                const clickedIndex1 = activePoints1[0].index;

                // Perform action based on the clicked dataset and index
                if (clickedDatasetIndex1 === 0 && clickedIndex1 === 0) {
                    // Bar for dataset 'Cable' and index 0 was clicked
                    window.location.href = 'Generate Reports/cable_efficiency_summary.php?linkTitle=CABLE EFFICIENCY SUMMARY';
                } else if (clickedDatasetIndex1 === 1 && clickedIndex1 === 0) {
                    // Bar for dataset 'Main' and index 0 was clicked
                    window.location.href = 'Generate Reports/Main_Efficiency_Summary.php?linkTitle=MAIN EFFICIENCY SUMMARY';
                }
            }
        });
    </script> -->

    <script>
        const ctx2 = document.getElementById("yield_chart").getContext("2d");

        const chart2 = new Chart(ctx2, {
            type: 'bar',
            data: {
                labels: <?php echo json_encode(array_column($station_values[$date_updated], 'station')); ?>,
                datasets: [{
                    label: '<?php echo $date; ?>',
                    data: <?php echo json_encode(array_column($station_values[$date_updated], 'yield')); ?>,
                    backgroundColor: 'rgba(107,114,142)',
                    borderColor: 'rgba(107,114,142, 1)',
                    borderWidth: 1
                }]
            },
            options: {
                plugins: {
                    legend: {
                        display: true,
                        labels: {
                            color: 'black',
                            font: {
                                weight: 'bold'
                            }
                        }
                    },
                    tooltip: {
                        enabled: true,
                        callbacks: {
                            label: function(context) {
                                var index = context.dataIndex;
                                var inspectedCounts = <?php echo json_encode(array_column($station_values[$date_updated], 'count')); ?>;
                                var count = inspectedCounts[index];
                                return "Total Inspected: " + count;
                            }
                        }
                    },
                    annotation: {
                        annotations: {
                            line1: {
                                type: 'line',
                                yMin: 94,
                                yMax: 94,
                                borderColor: 'rgb(255, 174, 66)',
                                borderWidth: 2
                            }
                        }
                    }
                },
                responsive: true,
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function(value) {
                                return value + '%'; // Display y-axis ticks with '%' symbol
                            }
                        }
                    }
                }
            }
        });
    </script>

    <!-- <script>
        var batchNumbers = <?php echo json_encode($batchNumbers); ?>;
        var noDays = <?php echo json_encode($noDays); ?>;

        // Create the chart using Chart.js
        var leadTime = document.getElementById('Leadtime').getContext('2d');
        var batchChart = new Chart(leadTime, {
            type: 'bar',
            axis: 'y',
            data: {
                labels: batchNumbers,
                datasets: [{
                    label: 'Number of Days',
                    data: noDays,
                    backgroundColor: [
                        'rgb(115, 147, 179)',
                        'rgb(54, 69, 79)',
                        'rgb(169, 169, 169)',
                        'rgb(96, 130, 182)',
                        'rgb(128, 128, 128)',
                        'rgb(129, 133, 137)'
                    ],
                    borderColor: [
                        'rgb(115, 147, 179)',
                        'rgb(54, 69, 79)',
                        'rgb(169, 169, 169)',
                        'rgb(96, 130, 182)',
                        'rgb(128, 128, 128)',
                        'rgb(129, 133, 137)'
                    ],
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                indexAxis: 'y',
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });
    </script> -->

    <!-- <script>
        // Extract batch numbers, SUB_ASSY, and MAIN_ASSY from the data
        var batchNumbers = <?php echo json_encode(array_column($build_status_data, 'batch_no')); ?>;
        var subAssyPercentages = <?php echo json_encode(array_column($build_status_data, 'SUB_ASSY')); ?>;
        var mainAssyPercentages = <?php echo json_encode(array_column($build_status_data, 'MAIN_ASSY')); ?>;
        var testingPercentages = <?php echo json_encode(array_column($build_status_data, 'TESTING')); ?>;
        var totalPercentages = <?php echo json_encode(array_column($build_status_data, 'total')); ?>;
        // List of colors in RGBA format
        var colorsRgba = [
            '#126ba3',
            '#106093',
            '#0e5682',
            '#0d4b72',
            '#0b4062'
        ];

        // Create the Chart.js chart
        var ctx5 = document.getElementById("buildStatusChart").getContext("2d");
        var myChart = new Chart(ctx5, {
            type: 'bar',
            data: {
                labels: batchNumbers,
                datasets: [{
                    label: 'Build Status',
                    data: totalPercentages,
                    backgroundColor: colorsRgba,
                    borderWidth: 1,
                }, ]
            },
            options: {
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function(value) {
                                return value + '%';
                            }
                        }
                    }
                },
                plugins: {
                    tooltip: {
                        enabled: true,
                        mode: 'index', // Set tooltip mode to index for stacking
                        intersect: false,
                        callbacks: {
                            label: function(context) {
                                return context.dataset.label + ': ' + context.parsed.y + '%';
                            },
                            title: function(context) {
                                return 'Batch Number: ' + context[0].label;
                            },
                            footer: function(tooltipItems) {
                                var dataIndex = tooltipItems[0].dataIndex;
                                var subAssyPercentage = subAssyPercentages[dataIndex];
                                var mainAssyPercentage = mainAssyPercentages[dataIndex];
                                var testingPercentage = testingPercentages[dataIndex];

                                // Format the tooltip footer with new lines for each percentage
                                return [
                                    'ASSEMBLY: ' + subAssyPercentage + '%',
                                    'INTEGRATION: ' + mainAssyPercentage + '%',
                                    'TESTING: ' + testingPercentage + '%'
                                ];
                            }
                        }
                    }
                },
            }
        });
    </script> -->

    <!-- <script>
        const smatrix_main = document.getElementById("skill_matrix_main").getContext("2d");
        const labels = <?php echo json_encode($shortcutNames); ?>;
        const datasets_main = [{
            label: 'Level 3',
            data: <?php echo json_encode(array_values($mainlevel3Values)); ?>,
            backgroundColor: 'rgba(84,130,53,255)',
            order: 1
        }, {
            label: 'Level 2',
            data: <?php echo json_encode(array_values($mainlevel2Values)); ?>,
            backgroundColor: 'rgba(255,255,0,255)',
            order: 1
        }, {
            label: 'Level 1',
            data: <?php echo json_encode(array_values($mainlevel1Values)); ?>,
            borderColor: 'rgba(240, 255, 0)',
            backgroundColor: 'rgba(46,117,182,255)',
            order: 1
        }, {
            label: 'Target',
            data: <?php echo json_encode($maintargetValues); ?>,
            borderColor: 'rgba(255, 0, 0)',
            backgroundColor: 'rgba(255, 0, 0)',
            type: 'line',
            stacked: 'combined',
            order: 0
        }];


        new Chart(smatrix_main, {
            type: 'bar',
            data: {
                labels: labels,
                datasets: datasets_main
            },
            options: {
                plugins: {
                    title: {
                        display: true,
                        text: 'Technician`s Skill Level Per Module - <?php echo $month; ?>',
                        color: 'black'
                    }
                },
                responsive: true,
                scales: {
                    x: {
                        stacked: true,
                    },
                    y: {
                        stacked: true
                    }
                }
            }
        });
    </script> -->

    <!-- <script>
        const smatrix_cable = document.getElementById("skill_matrix_cable").getContext("2d");
        const labels_cable = ['Manual Cutting', 'Manual Stripping', 'Manual Crimping', 'Semi-Auto Wire Crimp', 'Machine set Up', 'Soldering', 'Molding', 'Wire Harnessing', 'Final Assy', 'Machine Change-over', 'Labelling', 'Electrical Testing', 'VI', 'Pre Blocking', 'Taping'];
        const datasets_cable = [{
            label: 'Level 3',
            data: <?php echo json_encode(array_values($cablelevel3Values)); ?>,
            backgroundColor: 'rgba(84,130,53,255)',
            order: 1
        }, {
            label: 'Level 2',
            data: <?php echo json_encode(array_values($cablelevel2Values)); ?>,
            backgroundColor: 'rgba(255,255,0,255)',
            order: 1
        }, {
            label: 'Level 1',
            data: <?php echo json_encode(array_values($cablelevel1Values)); ?>,
            borderColor: 'rgba(240, 255, 0)',
            backgroundColor: 'rgba(46,117,182,255)',
            order: 1
        }];


        new Chart(smatrix_cable, {
            type: 'bar',
            data: {
                labels: labels_cable,
                datasets: datasets_cable
            },
            options: {
                plugins: {
                    title: {
                        display: true,
                        text: 'Operator`s Skill Level Per Station - <?php echo $month; ?>',
                        color: 'black'
                    }
                },
                responsive: true,
                scales: {
                    x: {
                        stacked: true,
                    },
                    y: {
                        stacked: true
                    }
                }
            }
        });
    </script> -->

    <script>
        function printCharts(divId) {
            var div = document.getElementById("dashboard");
            html2canvas(div).then(function(canvas) {
                var dataURL = canvas.toDataURL("image/png");
                var img = new Image();
                img.src = dataURL;
                img.onload = function() {
                    var printWindow = window.open();
                    printWindow.document.write('<html><head><title>Print Chart</title></head><body>');
                    printWindow.document.write('<img src="' + dataURL + '" width="' + img.width + '" height="' + img.height + '">');
                    printWindow.document.write('</body></html>');
                    printWindow.document.close();
                    printWindow.focus();
                    printWindow.print();
                    printWindow.close();
                };
            });
        }

        function saveCharts(divId) {
            var div = document.getElementById(divId);
            html2canvas(div).then(function(canvas) {
                var dataURL = canvas.toDataURL("image/png");
                var link = document.createElement("a");
                link.download = divId + ".png";
                link.href = dataURL;
                link.click();
            });
        }
    </script>

    <script>
        $(document).ready(function() {
            $('#filter').click(function(e) {
                e.preventDefault(); // Prevent form submission

                var selectedDate = $('#date').val();

                // Redirect to PHP page with the selected date as a parameter
                window.location.href = 'PROD_PORTAL.php?date=' + selectedDate;
            });
        });

        // Function to update the values of $total_cable_inprocess and $total_main_inprocess using AJAX
        function updateWIP() {
            $.ajax({
                url: "PROD_dashboard.php",
                dataType: "json",
                success: function(data) {
                    var totalCableInProcess = data.total_cable_inprocess;
                    var totalMainInProcess = data.total_main_inprocess;
                    var totalCableInDirect = data.total_cable_indirect;

                    // Update the content of the respective elements
                    $("#total_cable_inprocess").text(totalCableInProcess);
                    $("#total_main_inprocess").text(totalMainInProcess);
                    $("#total_cable_indirect").text(totalCableInDirect);
                }
            });
        }
        // Function to refresh the tables using AJAX
        function refreshTables() {
            $.ajax({
                url: "",
                dataType: "html",
                success: function(data) {
                    // Replace the existing tables with the updated ones
                    var updatedTable1 = $(data).find("#eclipse1");
                    var updatedTable2 = $(data).find("#eclipse2");
                    $("#eclipse1").html(updatedTable1);
                    $("#eclipse2").html(updatedTable2);
                },
                error: function(xhr, status, error) {
                    console.error('AJAX error: ' + status + ', ' + error);
                }
            });
        }
        setInterval(function() {
            updateWIP();
            refreshTables();
        }, 5000);

        // Function to export Table 1 to Excel
        function exportTable1ToExcel() {
            const workbook = new ExcelJS.Workbook();
            const worksheet = workbook.addWorksheet('Table 1');

            const table = document.getElementById('MainMatrix');
            const headerRows = table.querySelectorAll('thead tr');
            const dataRows = table.querySelectorAll('tbody tr');

            // Add header rows to worksheet
            headerRows.forEach(row => {
                const rowData = Array.from(row.querySelectorAll('th')).map(th => th.textContent);
                const headerRow = worksheet.addRow(rowData);
                headerRow.font = {
                    bold: true
                };
            });

            // Add table data to worksheet
            dataRows.forEach(row => {
                const rowData = Array.from(row.querySelectorAll('td')).map(td => td.textContent);
                worksheet.addRow(rowData);
            });

            // Save the workbook as Excel file
            workbook.xlsx.writeBuffer().then(buffer => {
                const blob = new Blob([buffer], {
                    type: 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'
                });
                const url = URL.createObjectURL(blob);
                const a = document.createElement('a');
                a.href = url;
                a.download = 'MainMatrix.xlsx';
                a.click();
            });
        }



        // Function to export Table 2 to Excel
        function exportTable2ToExcel() {
            const workbook = new ExcelJS.Workbook();
            const worksheet = workbook.addWorksheet('Table 2');

            const table = document.getElementById('CableMatrix');
            const rows = table.querySelectorAll('tr');

            // Add table headers to worksheet
            const headerRow = worksheet.addRow(Array.from(rows[0].querySelectorAll('th')).map(th => th.textContent));
            headerRow.font = {
                bold: true
            };

            // Add table data to worksheet
            for (let i = 1; i < rows.length; i++) {
                const row = rows[i];
                const rowData = Array.from(row.querySelectorAll('td')).map(td => td.textContent);
                worksheet.addRow(rowData);
            }

            // Save the workbook as Excel file
            workbook.xlsx.writeBuffer().then(buffer => {
                const blob = new Blob([buffer], {
                    type: 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'
                });
                const url = URL.createObjectURL(blob);
                const a = document.createElement('a');
                a.href = url;
                a.download = 'CableMatrix.xlsx';
                a.click();
            });
        }

        // Attach click event listeners to export buttons
        document.getElementById('exportMainMatrix').addEventListener('click', exportTable1ToExcel);
        document.getElementById('exportCableMatrix').addEventListener('click', exportTable2ToExcel);
    </script>
</body>
<footer class="bg-dark">
    <div class="container-fluid pt-3 pb-3">
        <div class="fw-bold text-white">
            ATS © 2023 <a href="https://pimes.com.ph">P. IMES Corp.</a>
        </div>
    </div>
</footer>

</html>