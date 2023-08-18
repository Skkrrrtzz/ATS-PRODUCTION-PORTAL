<?php
include '../ATS_Prod_Header.php';
include '../PROD_navbar.php';
require_once 'PROD_cable_dashboard_command.php';

if (!isset($_SESSION['Emp_ID'])) {
    header('location:ATS_Prod_Home.php');
    exit();
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SUPERVISOR CABLE DASHBOARD</title>
    <style>
        textarea {
            font-size: 18px;
        }

        .bg-myblue {
            --my-blue: #2706F0;
            background-color: var(--my-blue);
            opacity: .6;
        }

        .bg-myorange {
            --my-orange: #FF8C00;
            background-color: var(--my-orange);
            opacity: .9;
        }
    </style>


</head>

<body class="text-center fw-bold">
    <div class="d-flex justify-content-between">
        <div class="col-6 pt-2 pb-2 text-white bg-primary">
            <i class="fas fa-calendar-alt"></i> Week <?php echo $wwk; ?>
        </div>
        <div class="col-6 pt-2 pb-2 text-white bg-myblue">
            <i class="fas fa-calendar-alt"></i> Week <?php echo $nextwwk; ?>
        </div>
    </div>
    <div class="d-flex justify-content-between my-2 mx-3">
        <div class="row col-6">
            <div class="col">
                <div class="card border-0 shadow">
                    <h5 class="card-header bg-primary text-white fs-6">TARGET QTY.</h5>
                    <div class="fs-3" id="weeklyTargetDiv">
                        <?php echo $weekly_target != 0 ? $weekly_target : 0; ?>
                    </div>
                </div>
            </div>
            <div class="col">
                <div class="card border-0 shadow">
                    <h5 class="card-header bg-primary text-white fs-6">BACKLOG</h5>
                    <div class="fs-3" id="backlogDiv">
                        <?php echo $backlog_remaining != 0 ? $backlog_remaining : 0; ?>
                    </div>
                </div>
            </div>
            <div class="col">
                <div class="card border-0 shadow">
                    <h5 class="card-header bg-primary text-white fs-6">BACKLOG FG</h5>
                    <div class="fs-3" id="backlogFGDiv">
                        <?php echo $backlog_fg_qty != 0 ? $backlog_fg_qty : 0; ?>
                    </div>
                </div>
            </div>
            <div class="col">
                <div class="card border-0 shadow">
                    <h5 class="card-header bg-primary text-white fs-6">FG OUTPUT</h5>
                    <div class="fs-3" id="fgOutputDiv">
                        <?php echo $total_fg_qty != 0 ? $total_fg_qty : 0; ?>

                    </div>
                </div>
            </div>
        </div>
        <div class="row col-6">
            <div class="col">
                <div class="card border-0 shadow">
                    <h5 class="card-header bg-myblue text-white fs-6">TARGET QTY.</h5>
                    <div class="fs-3" id="nextWeeklyTargetDiv">
                        <?php echo $nextweekly_target != 0 ? $nextweekly_target : 0; ?>
                    </div>
                </div>
            </div>
            <div class="col">
                <div class="card border-0 shadow">
                    <h5 class="card-header bg-myblue text-white fs-6">ACTUAL FG OUTPUT</h5>
                    <div class="fs-3" id="actualFGOutputDiv">
                        <?php echo $nexttotal_fg_qty != 0 ? $nexttotal_fg_qty : 0; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="bg-secondary border-2 border-top rounded-top text-white">
        <h1 class="fw-bold">CABLE ASSY WORKING STATIONS</h1>
        <h4 class="bg-dark-subtle bg-gradient text-black m-1">LEGEND:
            <span class="badge rounded-pill bg-success opacity-100 m-1">IN-PROCESS</span>
            <span class="badge rounded-pill bg-myorange m-1">IDLE</span>
        </h4>
    </div>
    <div class="my-2 mx-3">
        <div class="row">
            <div class="col-sm m-2">
                <a href="cable_parts_kitting2.php">
                    <button type="button" class="btn btn-outline-success border-2 fw-bold shadow-sm bg-gradient btn-lg position-relative fs-5" style="width: 70%; height:80px;">
                        PARTS KITTING
                        <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-myorange" id="PK">
                            <?php echo $stationPK; ?>
                        </span>
                    </button>
                </a>
            </div>
            <div class="col-sm m-2">
                <a href="cable_wire_cutting_sub.php">
                    <button type="button" class="btn btn-outline-success border-2 fw-bold shadow-sm bg-gradient btn-lg position-relative fs-5" style="width: 70%; height:80px;">
                        WIRE/TUBE CUTTING
                        <span class="position-absolute top-0 start-0 translate-middle badge rounded-pill bg-success" id="inProcessQuantity_WIRE_TUBE_CUTTING">
                            <?php echo $stationInProcessQuantities['WIRE/TUBE CUTTING']; ?>
                        </span>
                        <span class=" position-absolute top-0 start-100 translate-middle badge rounded-pill bg-myorange" id="idleQuantity_WIRE_TUBE_CUTTING">
                            <?php echo $stationIdleQuantities['WIRE/TUBE CUTTING']; ?>
                        </span>

                    </button>
                </a>
            </div>
            <div class="col-sm m-2">
                <a href="cable_wire_stripping_sub.php">
                    <button type="button" class="btn btn-outline-success border-2 fw-bold shadow-sm bg-gradient btn-lg position-relative fs-5" style="width: 70%; height:80px;">
                        WIRE STRIPPING <span class="position-absolute top-0 start-0 translate-middle badge rounded-pill bg-success" id="inProcessQuantity_WIRE_STRIPPING">
                            <?php echo $stationInProcessQuantities['WIRE STRIPPING']; ?>
                        </span>
                        <span class=" position-absolute top-0 start-100 translate-middle badge rounded-pill bg-myorange" id="idleQuantity_WIRE_STRIPPING">
                            <?php echo $stationIdleQuantities['WIRE STRIPPING']; ?>
                        </span>
                    </button>
                </a>
            </div>
            <div class="col-sm m-2">
                <a href="cable_terminal_crimping_sub.php">
                    <button type="button" class="btn btn-outline-success border-2 fw-bold shadow-sm bg-gradient btn-lg position-relative fs-5" style="width: 70%; height:80px;">
                        TERMINAL CRIMPING <span class="position-absolute top-0 start-0 translate-middle badge rounded-pill bg-success" id="inProcessQuantity_TERMINAL_CRIMPING">
                            <?php echo $stationInProcessQuantities['TERMINAL CRIMPING']; ?>
                        </span>
                        <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-myorange" id="idleQuantity_TERMINAL_CRIMPING">
                            <?php echo $stationIdleQuantities['TERMINAL CRIMPING'];  ?>
                        </span>
                    </button>
                </a>
            </div>
        </div>
        <div class="row">
            <div class="col-sm m-2">
                <a href="cable_IPQC_operator_page.php">
                    <button type="button" class="btn btn-outline-success border-2 fw-bold shadow-sm bg-gradient btn-lg position-relative fs-5" style="width: 70%; height:80px;">
                        IPQC <span class="position-absolute top-0 start-0 translate-middle badge rounded-pill bg-success" id="inProcessQuantity_IPQC">
                            <?php echo $stationInProcessQuantities['IPQC']; ?>
                        </span>
                        <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-myorange" id="idleQuantity_IPQC">
                            <?php echo $stationIdleQuantities['IPQC']; ?>
                        </span>
                    </button>
                </a>
            </div>

            <!-- <div class="col-sm m-2">
        <a href="cable_pre-blocking_sub.php">
          <button type="button" class="btn btn-outline-success border-2 fw-bold shadow-sm bg-gradient btn-lg position-relative fs-5" style="width: 70%; height:80px;">
            PRE-BLOCKING <span class="position-absolute top-0 start-0 translate-middle badge rounded-pill bg-success">
              <?php  ?>
            </span>
            <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-myorange">
              <?php  ?>
            </span>
          </button>
        </a>
      </div> -->
            <div class="col-sm m-2">
                <a href="cable_soldering_sub.php">
                    <button type="button" class="btn btn-outline-success border-2 fw-bold shadow-sm bg-gradient btn-lg position-relative fs-5" style="width: 70%; height:80px;">
                        SOLDERING <span class="position-absolute top-0 start-0 translate-middle badge rounded-pill bg-success" id="inProcessQuantity_SOLDERING">
                            <?php echo $stationInProcessQuantities['SOLDERING']; ?>
                        </span>
                        <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-myorange" id="idleQuantity_SOLDERING">
                            <?php echo $stationIdleQuantities['SOLDERING']; ?>
                        </span>
                    </button>
                </a>
            </div>
            <div class="col-sm m-2">
                <a href="cable_molding_sub.php">
                    <button type="button" class="btn btn-outline-success border-2 fw-bold shadow-sm bg-gradient btn-lg position-relative fs-5" style="width: 70%; height:80px;">
                        MOLDING <span class="position-absolute top-0 start-0 translate-middle badge rounded-pill bg-success" id="inProcessQuantity_MOLDING">
                            <?php echo $stationInProcessQuantities['MOLDING']; ?>
                        </span>
                        <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-myorange" id="idleQuantity_MOLDING">
                            <?php echo $stationIdleQuantities['MOLDING']; ?>
                        </span>
                    </button>
                </a>
            </div>
            <div class="col-sm m-2">
                <a href="cable_wire_harnessing_sub.php">
                    <button type="button" class="btn btn-outline-success border-2 fw-bold shadow-sm bg-gradient btn-lg position-relative fs-5" style="width: 70%; height:80px;">
                        WIRE HARNESSING <span class="position-absolute top-0 start-0 translate-middle badge rounded-pill bg-success" id="inProcessQuantity_WIRE_HARNESSING">
                            <?php echo $stationInProcessQuantities['WIRE HARNESSING']; ?>
                        </span>
                        <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-myorange" id="idleQuantity_WIRE_HARNESSING">
                            <?php echo $stationIdleQuantities['WIRE HARNESSING']; ?>
                        </span>
                    </button>
                </a>
            </div>
        </div>
        <div class="row">

            <!-- <div class="col-sm m-2">
        <a href="cable_taping_sub.php">
          <button type="button" class="btn btn-outline-success border-2 fw-bold shadow-sm bg-gradient btn-lg position-relative fs-5" style="width: 70%; height:80px;">
            TAPING <span class="position-absolute top-0 start-0 translate-middle badge rounded-pill bg-success">
              <?php  ?>
            </span>
            <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-myorange">
              <?php ?>
            </span>
          </button>
        </a>
      </div> -->
            <div class="col-sm m-2">
                <a href="cable_final_assy_sub.php">
                    <button type="button" class="btn btn-outline-success border-2 fw-bold shadow-sm bg-gradient btn-lg position-relative fs-5" style="width: 70%; height:80px;">
                        FINAL ASSEMBLY <span class="position-absolute top-0 start-0 translate-middle badge rounded-pill bg-success" id="inProcessQuantity_FINAL_ASSEMBLY">
                            <?php echo $stationInProcessQuantities['FINAL ASSEMBLY']; ?>
                        </span>
                        <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-myorange" id="idleQuantity_FINAL_ASSEMBLY">
                            <?php echo $stationIdleQuantities['FINAL ASSEMBLY']; ?>
                        </span>
                    </button>
                </a>
            </div>
            <div class="col-sm m-2">
                <a href="cable_labelling_sub.php">
                    <button type="button" class="btn btn-outline-success border-2 fw-bold shadow-sm bg-gradient btn-lg position-relative fs-5" style="width: 70%; height:80px;">
                        LABELLING <span class="position-absolute top-0 start-0 translate-middle badge rounded-pill bg-success" id="inProcessQuantity_LABELLING">
                            <?php echo $stationInProcessQuantities['LABELLING']; ?>
                        </span>
                        <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-myorange" id="idleQuantity_LABELLING">
                            <?php echo $stationIdleQuantities['LABELLING']; ?>
                        </span>
                    </button>
                </a>
            </div>
            <div class="col-sm m-2">
                <a href="cable_visual_inspection_sub.php">
                    <button type="button" class="btn btn-outline-success border-2 fw-bold shadow-sm bg-gradient btn-lg position-relative fs-5" style="width: 70%; height:80px;">
                        VISUAL INSPECTION <span class="position-absolute top-0 start-0 translate-middle badge rounded-pill bg-success" id="inProcessQuantity_VISUAL_INSPECTION">
                            <?php echo $stationInProcessQuantities['VISUAL INSPECTION']; ?>
                        </span>
                        <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-myorange" id="idleQuantity_VISUAL_INSPECTION">
                            <?php echo $stationIdleQuantities['VISUAL INSPECTION']; ?>
                        </span>
                    </button>
                </a>
            </div>
            <div class="col-sm m-2">
                <a href="cable_oqa_operator_page.php">
                    <button type="button" class="btn btn-outline-success border-2 fw-bold shadow-sm bg-gradient btn-lg position-relative fs-5" style="width: 70%; height:80px; font-size:x-small">
                        OQA <span class="position-absolute top-0 start-0 translate-middle badge rounded-pill bg-success" id="inProcessQuantity_OQA">
                            <?php echo $stationInProcessQuantities['OQA']; ?>
                        </span>
                        <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-myorange" id="idleQuantity_OQA">
                            <?php echo $stationIdleQuantities['OQA']; ?>
                        </span>
                    </button>
                </a>
            </div>
            <!-- <div class="col-sm m-2">
        <a href="cable_heat_shrinking_sub.php">
          <button type="button" class="btn btn-outline-success border-2 fw-bold shadow-sm bg-gradient btn-lg position-relative fs-5" style="width: 70%; height:80px;">
            HEAT SHRINKING <span class="position-absolute top-0 start-0 translate-middle badge rounded-pill bg-success">
              <?php  ?>
            </span>
            <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-myorange">
              <?php  ?>
            </span>
          </button>
        </a>
      </div> -->
        </div>


        <!-- <div class="row row-col-sm m-2s-4">


       <div class="col-sm m-2">
        <a href="cable_testing_sub.php">
          <button type="button" class="btn btn-outline-success border-2 fw-bold shadow-sm bg-gradient btn-lg position-relative fs-5" style="width: 70%; height:80px;">
            TESTING <span class="position-absolute top-0 start-0 translate-middle badge rounded-pill bg-success">
              <?php ?>
            </span>
            <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-myorange">
              <?php  ?>
            </span>
          </button>
        </a>
      </div> -->

        <div class="row">
            <div class="col-sm m-2">
                <a href="cable_fg_transaction_sub.php">
                    <button type="button" class="btn btn-outline-success border-2 fw-bold shadow-sm bg-gradient btn-lg position-relative fs-5" style="width: 70%; height:80px;">
                        FG TRANSACTION <span class="position-absolute top-0 start-0 translate-middle badge rounded-pill bg-success" id="inProcessQuantity_FG_TRANSACTION">
                            <?php echo $stationInProcessQuantities['FG TRANSACTION']; ?>
                        </span>
                        <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-myorange" id="idleQuantity_FG_TRANSACTION">
                            <?php echo $stationIdleQuantities['FG TRANSACTION']; ?>
                        </span>
                    </button>
                </a>
            </div>
            <div class="col-sm m-2">
                <a href="cable_packaging_sub.php">
                    <button type="button" class="btn btn-outline-success border-2 fw-bold shadow-sm bg-gradient btn-lg position-relative fs-5" style="width: 70%; height:80px;">
                        PACKAGING <span class="position-absolute top-0 start-0 translate-middle badge rounded-pill bg-success" id="inProcessQuantity_PACKAGING">
                            <?php echo $stationInProcessQuantities['PACKAGING']; ?>
                        </span>
                        <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-myorange" id="idleQuantity_PACKAGING">
                            <?php echo $stationIdleQuantities['PACKAGING']; ?>
                        </span>
                    </button>
                </a>
            </div>
            <div class="col-sm m-2">
                <a href="cable_fg_store_sub.php">
                    <button type="button" class="btn btn-outline-success border-2 fw-bold shadow-sm bg-gradient btn-lg position-relative fs-5" style="width: 70%; height:80px;">
                        FINISHED GOOD STORE
                        <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-myorange" id="FG">
                            <?php echo $stationFG; ?>
                        </span>
                    </button>
                </a>
            </div>
            <div class="col-sm m-2">
                <a href="cable_out_po_operator_page.php">
                    <button type="button" class="btn btn-outline-success border-2 fw-bold shadow-sm bg-gradient btn-lg position-relative fs-5" style="width: 70%; height:80px;">
                        SHIPPED PARTS
                        <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-myorange" id="shipped">
                            <?php echo $shipped; ?>
                        </span>
                    </button>
                </a>
            </div>
        </div>
    </div>
    <script>
        function updateData() {
            // Make an AJAX request to the PHP file to get the updated data
            $.ajax({
                url: 'PROD_cable_dashboard_command.php',
                dataType: 'json',
                success: function(data) {
                    // Update the HTML elements with the new data
                    $('#weeklyTargetDiv').text(data.weekly_target != 0 ? data.weekly_target : 0);
                    $("#backlogDiv").text(data.backlog_remaining != 0 ? data.backlog_remaining : 0);
                    $("#backlogFGDiv").text(data.backlog_fg_qty != 0 ? data.backlog_fg_qty : 0);
                    $("#fgOutputDiv").text(data.total_fg_qty != 0 ? data.total_fg_qty : 0);
                    $("#nextWeeklyTargetDiv").text(data.nextweekly_target != 0 ? data.nextweekly_target : 0);
                    $("#actualFGOutputDiv").text(data.nexttotal_fg_qty != 0 ? data.nexttotal_fg_qty : 0);
                    $("#shipped").text(data.shipped != 0 ? data.shipped : 0);
                    $("#PK").text(data.stationPK != 0 ? data.stationPK : 0);
                    $("#FG").text(data.stationFG != 0 ? data.stationFG : 0);
                    let stationName;
                    <?php foreach ($stationQueries as $station) { ?>
                        stationName = '<?php echo str_replace(' ', '_', $station); ?>';
                        $('#inProcessQuantity_' + stationName.replace(/\//g, '_')).text(data.stationInProcessQuantities['<?php echo $station; ?>']);
                        $('#idleQuantity_' + stationName.replace(/\//g, '_')).text(data.stationIdleQuantities['<?php echo $station; ?>']);
                    <?php } ?>
                },
                error: function(xhr, status, error) {
                    console.error('AJAX error: ' + status + ', ' + error);
                }
            });
        }

        // Function to automatically update data every 5 seconds
        function autoUpdateData() {
            updateData(); // Initial call to update data
            setInterval(updateData, 10000); // Call updateData() every 10 seconds
        }

        // Call the autoUpdateData() function to start automatic updates when the page loads
        autoUpdateData();
    </script>
</body>

</html>