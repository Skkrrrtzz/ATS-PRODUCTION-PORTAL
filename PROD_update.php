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
    <meta http-equiv="X-UA-Compatible">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Update Data</title>

</head>


<body>
    <div class="container text-center">
        <div class="row">
            <div class="col">
                <h2>CABLE ASSY</h2>
                <div class="mb-2">
                    <a href="Edit_Update/Cable_DTR.php?linkTitle=CABLE DTR" class="btn btn-secondary btn-sm">CABLE DTR</a>
                </div>
                <div class="mb-2">
                    <a href="Edit_Update/Prod_WO.php?linkTitle=WORK ORDER" class="btn btn-secondary btn-sm">WORK ORDER</a>
                </div>
                <div class="mb-2">
                    <a href="Edit_Update/Add_Update_cycletime.php?linkTitle=CABLE STANDARD CYCLE TIME" class="btn btn-secondary btn-sm">CABLE STANDARD CYCLE TIME</a>
                </div>
            </div>
            <div class="col">
                <h2>MAIN</h2>
                <div class="mb-2">
                    <a href="Edit_Update/Prod_Dtr.php?linkTitle=Production Main DTR" class="btn btn-dark btn-sm">EDIT PROD DTR</a>
                </div>
                <div class="mb-2">
                    <a href="Edit_Update/Prod_Module.php?linkTitle=Production Main Module" class="btn btn-dark btn-sm">EDIT PROD MODULE</a>
                </div>
                <div class="mb-2">
                    <a href="add_module_cycle_time.php" class="btn btn-dark btn-sm">MODULE STANDARD CYCLE TIME</a>
                </div>
                <div class="mb-2">
                    <a href="add_bom.php" class="btn btn-dark btn-sm">BILL OF MATERIALS</a>
                </div>
            </div>

        </div>
    </div>

</body>

</html>