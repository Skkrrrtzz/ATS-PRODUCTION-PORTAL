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
    <style>
        /* .chart-container {
            position: relative;
            width: 100%;
            height: 450px;
        } */
    </style>
</head>

<body>
    <div class="m-3">
        <div class="card mx-auto border border-3 border-dark-subtle">
            <div class="row g-0">
                <div class="col-md-4 border border-3 border-bottom-0 border-start-0 border-top-0 border-dark-subtle">
                    <div class="bg-primary">
                        <div class="card-header">
                            <h3 class="text-white"><i class="bi bi-megaphone-fill"></i> UPDATES</h3>
                        </div>
                        <img src="assets/images/Business analytics-rafiki.png" class="img-fluid rounded" alt="Update_Img">
                        <div class="card-footer">
                            <h4 class="text-white">
                                <div id="clock">Loading...</div>
                                <script src="assets/js/clock.js"></script>
                            </h4>
                        </div>
                    </div>
                </div>
                <div class="col-md-8">
                    <div class="card-body">
                        <div id="carouselExampleSlidesOnly" class="carousel slide" data-bs-ride="carousel">
                            <div class="carousel-inner">
                                <div class="carousel-item active">
                                    <div class="card mx-auto d-block border-0">
                                        <div class="card-header bg-primary">
                                            <h4 class="fw-bold text-center text-light" type="button"> Attendance Rate </h4>
                                        </div>
                                        <div class="card-body text-center">
                                            <h4><i class="fa fa-users"></i>Today: <?php echo $ATT_OVERALL . "%", " Absent ", $all_abs ?></h4>
                                            <div class="col-sm col-md col-lg text-center">
                                                <!-- <input type="checkbox" id="toggleAllWeekly" onchange="toggleAllInterval()" />
                                                <label for="toggleAllWeekly">Weekly</label> -->
                                                <!-- <input type="checkbox" id="toggleAllMonthly" onchange="toggleAllInterval()" />
                                                <label for="toggleAllMonthly">Monthly</label> -->
                                                <canvas id="all_att_chart"></canvas>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="carousel-item">
                                    <div class="card mx-auto d-block rounded-top-0 border-0">
                                        <div class="card-header bg-primary">
                                            <h4 class="fw-bold text-center text-light" type="button"> Efficiency Rate </h4>
                                        </div>
                                        <div class="card-body text-center">
                                            <h4><i class="fa fa-line-chart"></i> Today: <?php echo $ALL_EFF . "%"; ?></h4>
                                            <div class="col-sm col-md col-lg">
                                                <!-- <input type="checkbox" id="toggleWeekly" />
                                <label for="toggleWeekly">Weekly</label> -->
                                                <canvas id="eff_chart"></canvas>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="carousel-item">
                                    <div class="card mx-auto d-block rounded-top-0 border-0">
                                        <div class="card-header bg-primary">
                                            <h4 class="fw-bold text-center text-light">Build Status </h4>
                                        </div>
                                        <div class="card-body text-center">
                                            <div class="col-sm col-md col-lg">
                                                <canvas id="buildStatusChart"></canvas>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <?php include_once 'Dashboard_Charts.php'; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>

</html>