<?php include 'ATS_Prod_Header.php' ?>
<?php include 'PROD_navbar.php' ?>
<?php require_once 'PROD_dashboard.php' ?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <script src="./assets/js/Chart.js"></script>
</head>

<body>
    <div class="container-fluid">
        <div class="card">
            <div class="card-header bg-primary-subtle">
                <a class="no-underline text-dark" href="Generate Reports/module_build_status.php">
                    <h5 class="fw-bold text-center fs-4 ">Build Status <i class="fas fa-info-circle"></i></h5>
                </a>
            </div>
            <div class="card-body">
                <div class="chart-container">
                    <canvas id="buildStatusChart" style="width: 100%; height:300px"></canvas>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Extract batch numbers, SUB_ASSY, and MAIN_ASSY from the data
        var batchNumbers = <?php echo json_encode(array_column($build_status_data, 'batch_no')); ?>;
        var subAssyPercentages = <?php echo json_encode(array_column($build_status_data, 'SUB_ASSY')); ?>;
        var mainAssyPercentages = <?php echo json_encode(array_column($build_status_data, 'MAIN_ASSY')); ?>;
        var testingPercentages = <?php echo json_encode(array_column($build_status_data, 'TESTING')); ?>;
        var totalPercentages = <?php echo json_encode(array_column($build_status_data, 'total')); ?>;
        var noDays = <?php echo json_encode(array_column($build_status_data, 'No_Days')); ?>;
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
                                var aging = noDays[dataIndex];
                                // Format the tooltip footer with new lines for each percentage
                                return [
                                    'SUB-ASSY: ' + subAssyPercentage + '%',
                                    'MAIN-ASSY: ' + mainAssyPercentage + '%',
                                    'TESTING: ' + testingPercentage + '%',
                                    'AGING: ' + aging + 'day/s'
                                ];
                            }
                        }
                    }
                },
            }
        });
    </script>
</body>

</html>