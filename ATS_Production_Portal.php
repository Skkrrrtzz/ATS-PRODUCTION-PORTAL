<?php
include 'ATS_Prod_Header.php';
include 'PROD_navbar.php';
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="assets/css/loading.css">
    <style>
        .no-underline {
            text-decoration: none;
        }
    </style>

</head>

<body>
    <div class="container text-center mt-5">
        <div class="row mx-0">
            <div class="col-sm">
                <div class="m-4">
                    <a class="no-underline text-dark" href="/ATS/ATSPROD_PORTAL/PROD_PORTAL.php?linkTitle=Dashboard" onclick="showLoader()">
                        <img src="assets/images/prod.png" type="button" class="img-fluid img-thumbnail rounded" alt="" width="200px">
                        <h4>PPMS</h4>
                    </a>
                </div>
            </div>
            <div class="col-sm">
                <div class="m-4">
                    <a class="no-underline text-dark" href="/ATS/ATSPROD_PORTAL/PROD_Torque_Monitoring.php?linkTitle=Torque Monitoring" onclick="showLoader()">
                        <img src="assets/images/torque-wrench.png" type="button" class="img-fluid img-thumbnail rounded" alt="" width="200px">
                        <h4>TORQUE SUMMARY</h4>
                    </a>
                </div>
            </div>
            <!-- <div class="col">
                <div class="m-4">
                    <a class="no-underline text-dark" href="/ATS/ATSPROD_PORTAL/PROD_PRCO.php?linkTitle=PRCO">
                        <img src="assets/images/priority.png" type="button" class="img-fluid img-thumbnail rounded " alt="" width="200px">
                        <h4>PRCO</h4>
                    </a>
                </div>
            </div> -->
            <!-- <div class="col">
                <div class="m-4">
                    <a class="no-underline text-dark" href="#">
                        <img src="assets/images/manage.png" type="button" class="img-fluid img-thumbnail rounded " alt="" width="200px">
                        <h4>DOCUMENT MASTER LIST</h4>
                    </a>
                </div>
            </div> -->
            <div class="col-sm">
                <div class="m-4">
                    <a class="no-underline text-dark" href="#">
                        <img src="assets/images/EPM.png" type="button" class="img-fluid img-thumbnail rounded " alt="" width="200px">
                        <h4>EPM</h4>
                    </a>
                </div>
            </div>
        </div>
    </div>
    <div class="loader-container" id="loader">
        <div class="dot-spinner">
            <div class="dot-spinner__dot"></div>
            <div class="dot-spinner__dot"></div>
            <div class="dot-spinner__dot"></div>
            <div class="dot-spinner__dot"></div>
            <div class="dot-spinner__dot"></div>
            <div class="dot-spinner__dot"></div>
            <div class="dot-spinner__dot"></div>
            <div class="dot-spinner__dot"></div>
        </div>
    </div>
    <script src="assets/js/loading.js"></script>
</body>

</html>