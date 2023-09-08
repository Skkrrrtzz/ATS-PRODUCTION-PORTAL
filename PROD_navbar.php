<?php
if (!isset($_SESSION['Emp_ID'])) {
    header('location:/ATS/ATSPROD_PORTAL/ATS_Prod_Home.php');
    exit();
}

$name = $_SESSION['Name'];
$dept = $_SESSION['Department'];
$emp_id = $_SESSION['Emp_ID'];
$role = $_SESSION['role'];
$position = $_SESSION['position'];
$pw = $_SESSION['password'];

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <title>Production NavBar</title>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="/ATS/ATSPROD_PORTAL/assets/css/loading.css">
    <style>
        .navbar {
            padding-top: 0 !important;
            padding-bottom: 0 !important;
        }

        .hoverable-dropdown:hover .dropdown-menu {
            display: block;
        }
    </style>

</head>

<body>
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
    <nav class="navbar navbar-dark bg-dark shadow-sm">
        <div class="container-fluid">
            <div class=" dropdown">
                <button class="navbar-toggler" type="button" id="navbarDropdownToggle" data-bs-toggle="dropdown" aria-expanded="false">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <ul class="dropdown-menu dropdown-menu-dark" aria-labelledby="navbarDropdownToggle">
                    <?php $selectedLinkTitle = isset($_GET['linkTitle']) ? $_GET['linkTitle'] : 'Home';
                    if ($role === "cable_supervisor") { ?>
                        <li><a class="dropdown-item active" href="/ATS/ATSPROD_PORTAL/ATS_Production_Portal.php?linkTitle=Home"><i class="fa fa-home"></i> Home</a></li>
                    <?php } ?>
                    <?php if ($selectedLinkTitle !== 'Home' && $selectedLinkTitle !== 'Torque Monitoring' && $selectedLinkTitle !== 'PRCO') { ?>
                        <li><a class="dropdown-item" href="/ATS/ATSPROD_PORTAL/PROD_PORTAL.php?linkTitle=Dashboard" onclick="showLoader()"><i class="bi bi-columns-gap"></i> Dashboard</a></li>
                        <li><a class="dropdown-item" href="/ATS/ATSPROD_PORTAL/Generate Reports/PROD_cable_dashboard.php?linkTitle=Cable Dashboard"><i class="bi bi-grid-1x2-fill"></i> Cable Dashboard</a></li>
                        <li><a class="dropdown-item" href="/ATS/ATSPROD_PORTAL/Generate Reports/PROD_main_dashboard.php?linkTitle=Module Dashboard"><i class="bi bi-grid-1x2-fill"></i> Module Dashboard</a></li>
                        <li><a class="dropdown-item" href="/ATS/ATSPROD_PORTAL/PROD_reports.php?linkTitle=Generate Reports"><i class="fa fa-list"></i> Generate Reports</a></li>
                        <li><a class="dropdown-item" href="/ATS/ATSPROD_PORTAL/PROD_update.php?linkTitle=Edit/Update Data"><i class="fa-solid fa-pen-to-square"></i> Edit/Update Data</a></li>
                        <li><a class="dropdown-item" href="/ATS/ATSPROD_PORTAL/Carousel.php?linkTitle=Updates" onclick="showLoader()"><i class="bi bi-info-square-fill"></i> Updates</a></li>
                        <li>
                            <hr class=" dropdown-divider">
                        </li>
                        <li class="hoverable-dropdown dropend">
                            <a class="dropdown-item dropdown-toggle" href="#">
                                <i class="fa-solid fa-magnifying-glass"></i> Monitoring
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end">
                                <!-- <li>
                                    <a class="dropdown-item" href="/ATS/ATSPROD_PORTAL/PROD_Torque_Monitoring.php?linkTitle=Torque Monitoring">
                                        <i class="fa-solid fa-wrench"></i> Torque Summary
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item" href="/ATS/ATSPROD_PORTAL/PROD_PRCO.php?linkTitle=PRCO"><i class="fa-brands fa-product-hunt"></i> PRCO
                                    </a>
                                </li> -->
                                <li>
                                    <a class="dropdown-item" href="/ATS/ATSPROD_PORTAL/PROD_Shipment_Status.php?linkTitle=Shipment Status"><i class="fa-solid fa-cart-flatbed"></i> Production Shipment Status
                                    </a>
                                </li>
                            </ul>
                        </li>
                    <?php } ?>
                </ul>
            </div>
            <h3 class="text-white">
                <?php echo $selectedLinkTitle; ?>
            </h3>

            <div class="dropdown dropstart py-1">
                <?php
                // Fetch the image data from the database based on the user or file ID
                $sql = "SELECT content FROM files_data WHERE emp_id = ?";
                $stmt = mysqli_prepare($conn, $sql);
                mysqli_stmt_bind_param($stmt, 's', $emp_id);
                mysqli_stmt_execute($stmt);
                $result = mysqli_stmt_get_result($stmt);
                // Check if a row was returned from the query
                if (mysqli_num_rows($result) > 0) {
                    $row = mysqli_fetch_assoc($result);

                    // Check if the row has the 'content' key
                    if (isset($row['content'])) {
                        $imageData = $row['content'];
                        // Generate the image URL for the <img> tag
                        $imgUrl = 'data:image/jpeg;base64,' . base64_encode($imageData);
                    } else {
                        // Use a default image URL when the 'content' key is not present
                        $imgUrl = '/ATS/ATSPROD_PORTAL/assets/images/user.png';
                    }
                } else {
                    // Use a default image URL when no rows are returned
                    $imgUrl = '/ATS/ATSPROD_PORTAL/assets/images/user.png';
                }

                mysqli_stmt_close($stmt);
                ?>
                <img src="<?php echo $imgUrl; ?>" class="rounded-circle" id="imageDropdownToggle" type="button" data-bs-toggle="dropdown" aria-expanded="false" alt="Profile Image" width="42" height="42">
                <ul class="dropdown-menu dropdown-menu-dark" aria-labelledby="imageDropdownToggle">
                    <li class="dropdown-item active"><?php echo $name; ?></li>
                    <li><a class="dropdown-item" href="/ATS/ATSPROD_PORTAL/PROD_profile.php?linkTitle=Profile"> <i class="fa fa-user"></i> Profile</a></li>
                    <?php if ($position === "Admin" && $role === "cable_supervisor") { ?>
                        <li><a class="dropdown-item" href="/ATS/ATSPROD_PORTAL/Users_Admin.php?linkTitle=Authorized Users"><i class="fa-solid fa-user-secret"></i> Users Administrator</a></li>
                    <?php } else { ?>
                        <div class="container-fluid">
                            <!-- 404 Error Text -->
                            <div class="text-center">
                                <div class="error mx-auto" data-text="404">404</div>
                                <p class="lead text-gray-800 mb-5">Page Not Found</p>
                                <a href="/ATS/ATSPROD_PORTAL/ATS_Prod_Home.php">&larr; Back to Login Page</a>
                            </div>
                        </div>
                    <?php  } ?>
                    <li>
                        <hr class="dropdown-divider">
                    </li>
                    <li>
                        <a class="dropdown-item logout" onclick="logoutAlert()">
                            <i class="fa-solid fa-right-from-bracket"></i> Logout
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>
    <script src="/ATS/ATSPROD_PORTAL/assets/js/loading.js"></script>
    <script src="/ATS/ATSPROD_PORTAL/assets/js/bootstrap.bundle.min.js"></script>
</body>
<script type="text/javascript">
    function logoutAlert() {
        Swal.fire({
            title: "Are you sure?",
            text: "You will be logged out!",
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            confirmButtonText: "Yes, log me out!",
            cancelButtonText: "No, keep me signed in",
        }).then((result) => {
            if (result.value) {
                window.location.href = "/ATS/ATSPROD_PORTAL/PROD_logout.php";
            }
        });
    }
</script>

</html>