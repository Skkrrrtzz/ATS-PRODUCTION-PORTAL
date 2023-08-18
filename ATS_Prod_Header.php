<?php
session_start();

$host = "localhost";
$user = "root";
$password = "";
$dbname = "ewip";

$conn = new mysqli($host, $user, $password, $dbname);
?>
<!DOCTYPE html>
<html lang="en">

<head>

  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Production Portal</title>
  <link rel="icon" href="/ATS/ATSPROD_PORTAL/assets/images/pimes.ico" type="image/ico">
  <!-- CSS -->
  <link rel="stylesheet" href="/ATS/ATSPROD_PORTAL/assets/css/bootstrap.min.css">
  <link rel="stylesheet" href="/ATS/ATSPROD_PORTAL/assets/css/fontawesome-6.3.0/css/all.min.css">
  <link rel="stylesheet" href="/ATS/ATSPROD_PORTAL/assets/css/fontawesome-6.3.0/css/regular.min.css">
  <link rel="stylesheet" href="/ATS/ATSPROD_PORTAL/assets/icons/font/bootstrap-icons.min.css">
  <!-- JS -->
  <script src="/ATS/ATSPROD_PORTAL/assets/js/jquery-3.6.0.min.js"></script>
  <script src="/ATS/ATSPROD_PORTAL/assets/js/sweetalert2.all.min.js"></script>
  <script src="/ATS/ATSPROD_PORTAL/assets/js/html2canvas.min.js"></script>
  <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
  <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>


  <style>
    .bg-blue {
      --blue: #2706F0;
      background-color: var(--blue);
    }
  </style>
</head>

<body>
  <!-- <div class="bg-blue p-1">
    <div class="mx-auto">
      <img class="rounded float-start" src="/ATS/ATSPROD_PORTAL/assets/images/pimes.png" alt="Logo" width="70" height="45">
      <img class="rounded float-end" src="/ATS/ATSPROD_PORTAL/assets/images/ATS logoa.png" alt="Profile" width="70" height="45">
      <h2 class="text-center text-white fw-bold">ATS PRODUCTION PORTAL</h2>
    </div>
  </div> -->
  <div class="container-fluid" style="background-color: #2706F0;">
    <header class="py-1 mb-0">
      <img class="rounded float-start" src="/ATS/ATSPROD_PORTAL/assets/images/pimes.png" alt="Logo" width="70" height="45">
      <img class="rounded float-end" src="/ATS/ATSPROD_PORTAL/assets/images/ATS logoa.png" alt="Profile" width="70" height="45">
      <h2 class="text-center text-white fw-bold">ATS PRODUCTION PORTAL</h2>
    </header>
  </div>
</body>

</html>