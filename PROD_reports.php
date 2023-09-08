<?php include 'ATS_Prod_Header.php'; ?>
<?php include 'PROD_navbar.php'; ?>
<?php
//session_start();
$name = $_SESSION['Name'];
$dept = $_SESSION['Department'];
$emp_id = $_SESSION['Emp_ID'];
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Production Reports</title>
  <style>
    .custom-accordion .accordion-item {
      background-color: #e8e9ea;
      /* Set the desired background color */
    }

    .no-underline {
      text-decoration: none;
    }
  </style>

</head>

<body>
  <div class="container text-center">
    <div class="row">
      <div class="col-md-4">
        <h2>CABLE ASSY</h2>
        <div class="d-flex flex-column">
          <a href="Generate Reports/cable_wip.php?linkTitle=CABLE" class="btn btn-secondary m-2" name="update">CABLE WIP</a>
          <a href="Generate Reports/sup_cable_wo.php?linkTitle=CABLE PO STATUS" class="btn btn-secondary m-2" name="update">CABLE PO STATUS</a>
          <a href="Generate Reports/cable_actual_cycle_time.php?linkTitle=CABLE CYCLE TIME" class="btn btn-secondary m-2" name="update">CABLE CYCLE TIME</a>
          <a href="Generate Reports/sup_cable_parts_history.php?linkTitle=CABLE PARTS HISTORY" class="btn btn-secondary m-2" name="update">CABLE PARTS HISTORY</a>
          <a href="Generate Reports/cable_dtr_summary.php?linkTitle=CABLE ASSY DTR SUMMARY" class="btn btn-secondary m-2" name="update">CABLE ASSY DTR SUMMARY</a>
          <a href="Generate Reports/cable_attendance_summary.php?linkTitle=CABLE ASSY ATTENDANCE SUMMARY" class="btn btn-secondary m-2" name="update">CABLE ASSY ATTENDANCE SUMMARY</a>
          <a href="Generate Reports/cable_efficiency_summary.php?linkTitle=CABLE ASSY EFFICIENCY SUMMARY" class="btn btn-secondary m-2" name="update">CABLE ASSY EFFICIENCY SUMMARY</a>
        </div>
      </div>
      <!-- <div class="col-md-4">
        <h2>PRODUCTION SUMMARY</h2>
        <div class="d-flex flex-column">
          <a href="Generate Reports/module_attendance_summary.php?linkTitle=PRODUCTION ATTENDANCE" class="btn btn-primary m-2" name="update">PRODUCTION ATTENDANCE</a>
          <a href="Generate Reports/PROD_trainings.php?linkTitle=PRODUCTION TRAINING INFORMATIONS" class="btn btn-primary m-2" name="update">PRODUCTION TRAINING INFORMATIONS</a>
          <a href="Generate Reports/PROD_skills_matrix.php?linkTitle=PRODUCTION SKILLS MATRIX" class="btn btn-primary m-2" name="update">PRODUCTION SKILLS MATRIX</a>
          <a href="Generate Reports/PROD_Efficiency.php?linkTitle=PRODUCTION EFFICIENCY" class="btn btn-primary m-2" name="update">PRODUCTION EFFICIENCY</a>
        </div>
      </div> -->
      <div class="col-md-4">
        <h2>PRODUCTION SUMMARY</h2>
        <div class="d-flex flex-column">
          <a href="Generate Reports/module_attendance_summary.php?linkTitle=PRODUCTION ATTENDANCE" class="btn btn-primary m-2" name="update">PRODUCTION ATTENDANCE</a>
          <?php if ($role === 'cable_supervisor') { ?>
            <a href="Generate Reports/PROD_Eff_data.php?linkTitle=PRODUCTION EFFICIENCY" class="btn btn-primary m-2" name="update">PRODUCTION EFFICIENCY</a>
          <?php } ?>

          <a href="Generate Reports/PROD_trainings.php?linkTitle=PRODUCTION TRAINING INFORMATIONS" class="btn btn-primary m-2" name="update">PRODUCTION TRAINING INFORMATIONS</a>
        </div>
        <div class="accordion custom-accordion accordion-flush primary-button m-2" id="accordionFlushExample">
          <div class="accordion-item">
            <h2 class="accordion-header">
              <button class="accordion-button collapsed text-bg-primary rounded-1" type="button" data-bs-toggle="collapse" data-bs-target="#flush-collapseOne" aria-expanded="false" aria-controls="flush-collapseOne">
                PRODUCTION SKILLS MATRIX
              </button>
            </h2>
            <div id="flush-collapseOne" class="accordion-collapse collapse" data-bs-parent="#accordionFlushExample">
              <div class="accordion-body"> <a class="no-underline fw-bold" href="Generate Reports/PROD_skills_matrix.php?linkTitle=PRODUCTION MAIN SKILLS MATRIX" name="update">Production Main Skills Matrix</a></div>
              <div class="accordion-body"> <a class="no-underline fw-bold" href="Generate Reports/PROD_skills_matrix_cable.php?linkTitle=PRODUCTION CABLE SKILLS MATRIX" name="update">Production Cable Skills Matrix</a></div>
            </div>
          </div>
        </div>
      </div>

      <div class="col-md-4">
        <h2>MAIN ASSY</h2>
        <div class="d-flex flex-column">
          <a href="Generate Reports/module_wip.php?linkTitle=MAIN" class="btn btn-dark m-2" name="update">MAIN WIP</a>
          <a href="Generate Reports/sup_prod_po_status.php?linkTitle=MAIN PO STATUS" class="btn btn-dark m-2" name="update">MAIN PO STATUS</a>
          <a href="Generate Reports/prod_actual_cycle_time.php?linkTitle=MAIN CYCLE TIME" class="btn btn-dark m-2" name="update">MAIN CYCLE TIME</a>
          <a href="Generate Reports/module_build_status.php?linkTitle=MAIN BUILD STATUS" class="btn btn-dark m-2" name="update">MAIN BUILD STATUS</a>
          <a href="Generate Reports/Main_Efficiency_Summary.php?linkTitle=MAIN EFFICIENCY SUMMARY" class="btn btn-dark m-2" name="update">MAIN EFFICIENCY SUMMARY</a>
        </div>
      </div>

    </div>
  </div>
  <!-- <script src="/ATS/ATSPROD_PORTAL/assets/js/bootstrap.bundle.min.js"></script> -->
</body>

</html> <!-- <a href="Generate Reports/cable_performance.php?linkTitle=CABLE ASSY PERFORMANCE"><button class="btn btn-secondary" type="submit" name="update">CABLE ASSY PERFORMANCE</button></a> 
        --> <!-- <a href="Generate Reports/cable_PO_status_summary.php?linkTitle=CABLE PO STATUS SUMMARY"><button class="btn btn-secondary" type="submit" name="update">CABLE PO STATUS SUMMARY</button></a>
         -->