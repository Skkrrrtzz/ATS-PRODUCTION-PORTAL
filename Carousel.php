<?php include 'ATS_Prod_Header.php' ?>
<?php include 'PROD_navbar.php' ?>
<?php require_once 'PROD_dashboard.php' ?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <script src="assets/js/Chart.js"></script>
    <script src="assets/js/Chartjsannotation.js"></script>
    <script src="assets/js/chartjs-plugin-datalabels.min.js"></script>
</head>

<body>
    <div class="m-5">
        <div id="carouselExampleSlidesOnly" class="carousel slide m-3" data-bs-ride="carousel">
            <div class="carousel-inner">
                <div class="carousel-item active">
                    <div class="card mx-auto d-block w-75">
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
                <div class="carousel-item">
                    <div class="card mx-auto d-block w-75">
                        <div class="card-header bg-primary-subtle">
                            <h5 class="fw-bold" type="button" data-bs-toggle="modal" data-bs-target="#"> Efficiency Rate <i class="fas fa-info-circle"></i></h5>
                        </div>
                        <div class="card-body">
                            <h4><i class="fa fa-line-chart"></i><?php echo $ALL_EFF . "%"; ?></h4>
                            <div class="chart-container">
                                <input type="checkbox" id="toggleWeekly" />
                                <label for="toggleWeekly">Weekly</label>
                                <canvas id="eff_chart"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="carousel-item">
                    <div class="card mx-auto d-block w-75">
                        <div class="card-header bg-primary-subtle">
                            <h5 class="fw-bold text-center fs-6 ">Build Status <i class="fas fa-info-circle"></i></h5>
                        </div>
                        <div class="card-body">
                            <div class="chart-container">
                                <canvas id="buildStatusChart"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php include_once 'Dashboard_Charts.php'; ?>
    </div>
</body>

</html>