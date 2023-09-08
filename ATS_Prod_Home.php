<?php include 'ATS_Prod_Headers.php';

date_default_timezone_set("Asia/Manila"); // set default timezone
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
function displayError($message)
{
    echo '<div class="alert alert-danger" role="alert"><i class="bi bi-exclamation-triangle-fill"></i> ' . $message . '</div>';
}

// Validate and sanitize the username and password
if (isset($_POST['login_prod'])) {
    $username = $_POST['prod_uname'];
    $password = $_POST['prod_pw'];

    // Perform any necessary validation and sanitization of the username and password here
    $sql = "SELECT * FROM user WHERE username = ? AND department IN (?, ?);";
    $stmt = $conn->prepare($sql);
    $prod = 'Production Main'; // Hardcoded department
    $ppic = 'DPIC';
    // $role = "cable_supervisor";
    $stmt->bind_param("sss", $username, $prod, $ppic);
    $stmt->execute();
    $result = $stmt->get_result();

    if (empty($username) || empty($password)) {
        $errorMessage = "Input Fields Can`t Be Empty";
    } elseif ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $storedPassword = $row["hashed_password"];

        // Check if the stored password needs to be rehashed
        if (password_needs_rehash($storedPassword, PASSWORD_DEFAULT)) {
            // Rehash the password
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

            // Update the user's hashed password in the database
            $updateSql = "UPDATE user SET hashed_password = ? WHERE username = ?";
            $updateStmt = $conn->prepare($updateSql);
            $updateStmt->bind_param("ss", $hashedPassword, $username);
            $updateStmt->execute();
            $updateStmt->close();
        } else {
            // Use the stored password for verification
            $hashedPassword = $storedPassword;
        }

        if (password_verify($password, $hashedPassword)) {
            // User authentication successful
            $_SESSION['Name'] = $row["emp_name"];
            $_SESSION['Department'] = $row["department"];
            $_SESSION['Emp_ID'] = $row["username"];
            $_SESSION['role'] = $row["role"];
            $_SESSION['password'] = $row["password"];
            $_SESSION['position'] = $row["position"];
            $_SESSION['time_in'] = date('Y-m-d H:i:s');
            $_SESSION['date'] = date('Y-m-d');
            $_SESSION['token'] = bin2hex(random_bytes(32)); // Generate a CSRF token

            // Check if user already has a record in prod_attendance table
            $emp_id = $_SESSION['Emp_ID'];
            $sqls = "SELECT COUNT(*) AS count FROM prod_attendance WHERE Emp_ID=? AND DATE = CURDATE()";
            $stmts = $conn->prepare($sqls);
            $stmts->bind_param("s", $emp_id);
            $stmts->execute();
            $result_count = $stmts->get_result();
            $rows = $result_count->fetch_assoc();

            if ($rows['count'] > 0) {
                // User already has a record, update the existing record with new login time
            } else {
                // User does not have a record, insert a new record
                $time_in = $_SESSION['time_in'];
                $date = $_SESSION['date'];
                $department = $_SESSION['Department'];
                $emp_name = $_SESSION['Name'];
                $sql_insert = "INSERT INTO prod_attendance (Emp_ID, Name, Department, DATE, Time_In) VALUES (?, ?, ?, ?, ?)";
                $stmt_insert = $conn->prepare($sql_insert);
                $stmt_insert->bind_param("sssss", $emp_id, $emp_name, $department, $date, $time_in);
                $stmt_insert->execute();
                $stmt_insert->close();
            }

            // Regenerate session ID to prevent session fixation
            session_regenerate_id();

            // Set session cookie to be secure and httponly
            $params = session_get_cookie_params();
            setcookie(session_name(), session_id(), time() + 3600, $params['path'], $params['domain'], true, true);
            // Set the session lifetime to 1 hour (3600 seconds)
            ini_set('session.gc_maxlifetime', 7200);
            if ($_SESSION['role'] === "cable_supervisor") {
                // You can redirect the user to another page or perform any other desired actions
                header('location: ATS_Production_Portal.php');
                exit();
            } else if ($_SESSION['role'] === "planner") {
                header('location: /ATS/ATSPPIC_PORTAL/sb-admin/ppic_dashboard.php');
                exit();
            } else {
                // You can redirect the user to another page or perform any other desired actions
                header('location: PROD_Viewer.php');
                exit();
            }
        } else {
            // User authentication failed
            $errorMessage = "Authentication Failed";
        }
    } else {
        // User not found
        $errorMessage = "Invalid username or password.";
    }

    $stmt->close();
    if (isset($stmts)) {
        $stmts->close();
    }
}

$conn->close();

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- <title>Production Portal</title>
    <link rel="icon" href="/ATS/ATSPROD_PORTAL/assets/images/pimes.ico" type="image/ico">
    <!-- CSS -->
    <!-- <link rel="stylesheet" href="/ATS/ATSPROD_PORTAL/assets/css/bootstrap.min.css">
    <link rel="stylesheet" href="/ATS/ATSPROD_PORTAL/assets/css/fontawesome-6.3.0/css/all.min.css">
    <link rel="stylesheet" href="/ATS/ATSPROD_PORTAL/assets/css/fontawesome-6.3.0/css/regular.min.css">
    <link rel="stylesheet" href="/ATS/ATSPROD_PORTAL/assets/icons/font/bootstrap-icons.min.css">
    <!-- JS -->
    <!-- <script src="/ATS/ATSPROD_PORTAL/assets/js/jquery-3.6.0.min.js"></script>
    <script src="/ATS/ATSPROD_PORTAL/assets/js/sweetalert2.all.min.js"></script>
    <script src="/ATS/ATSPROD_PORTAL/assets/js/html2canvas.min.js"></script>
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script> -->

    <!-- <style>
        body {
            background-color: #f2f2f2;
        }

        .container {
            max-width: 400px;
            margin: 0 auto;
            padding: 20px 20px;
            background-color: #fff;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        .container-big {
            max-width: 100%;
            margin: 0 auto;
            padding: 20px 20px;
            background-color: #fff;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        .form-control:focus {
            border-color: #6c757d;
            box-shadow: none;
        }

        .btn-primary {
            background-color: #007bff;
            border-color: #007bff;
            transition: all 0.2s ease-in-out;
        }

        .btn-primary:hover {
            background-color: #0069d9;
            border-color: #0062cc;
        }

        .btn-primary:focus {
            box-shadow: none;
        }

        .login-container {
            display: inline-block;
            vertical-align: top;
        }

        .no-underline {
            text-decoration: none;
        }

        .gears-container {
            width: 130px;
            height: 95px;
            font-size: 24px;
            padding-top: 10px;
            position: relative;
            display: inline-block;
        }

        .gear-rotate {
            width: 2em;
            height: 2em;
            top: 50%;
            left: 60%;
            margin-top: -1em;
            margin-left: -1em;
            background: #2706F0;
            position: absolute;
            border-radius: 1em;
            -webkit-animation: 1s gear-rotate linear infinite;
            -moz-animation: 1s gear-rotate linear infinite;
            animation: 1s gear-rotate linear infinite;
        }

        .gear-rotate-left {
            margin-top: -2.2em;
            top: 50%;
            width: 2em;
            height: 2em;
            background: #2706F0;
            position: absolute;
            border-radius: 1em;
            -webkit-animation: 1s gear-rotate-left linear infinite;
            -moz-animation: 1s gear-rotate-left linear infinite;
            animation: 1s gear-rotate-left linear infinite;
        }

        .gear-rotate::before,
        .gear-rotate-left::before {
            width: 2.8em;
            height: 2.8em;
            background:
                -webkit-linear-gradient(0deg, transparent 39%, #2706F0 39%, #2706F0 61%, transparent 61%),
                -webkit-linear-gradient(60deg, transparent 42%, #2706F0 42%, #2706F0 58%, transparent 58%),
                -webkit-linear-gradient(120deg, transparent 42%, #2706F0 42%, #2706F0 58%, transparent 58%);
            background:
                -moz-linear-gradient(0deg, transparent 39%, #2706F0 39%, #47EC19 61%, transparent 61%),
                -moz-linear-gradient(60deg, transparent 42%, #2706F0 42%, #2706F0 58%, transparent 58%),
                -moz-linear-gradient(120deg, transparent 42%, #2706F0 42%, #2706F0 58%, transparent 58%);
            background:
                -o-linear-gradient(0deg, transparent 39%, #2706F0 39%, #2706F0 61%, transparent 61%),
                -o-linear-gradient(60deg, transparent 42%, #2706F0 42%, #2706F0 58%, transparent 58%),
                -o-linear-gradient(120deg, transparent 42%, #47EC19 42%, #2706F0 58%, transparent 58%);
            background: -ms-linear-gradient(0deg, transparent 39%, #2706F0 39%, #2706F0 61%, transparent 61%), -ms-linear-gradient(60deg, transparent 42%, #2706F0 42%, #2706F0 58%, transparent 58%), -ms-linear-gradient(120deg, transparent 42%, #2706F0 42%, #2706F0 58%, transparent 58%);
            background:
                linear-gradient(0deg, transparent 39%, #2706F0 39%, #2706F0 61%, transparent 61%),
                linear-gradient(60deg, transparent 42%, #2706F0 42%, #2706F0 58%, transparent 58%),
                linear-gradient(120deg, transparent 42%, #2706F0 42%, #2706F0 58%, transparent 58%);
            position: absolute;
            content: "";
            top: -.4em;
            left: -.4em;
            border-radius: 1.4em;
        }

        .gear-rotate::after,
        .gear-rotate-left::after {
            width: 1em;
            height: 1em;
            background: #FFF01F;
            position: absolute;
            content: "";
            top: .5em;
            left: .5em;
            border-radius: .5em;
        }

        /* CSS for the entrance animation from left to right */
        .entrance-animation-left {
            opacity: 0;
            transform: translateX(-50px);
            animation: slideInFromLeft 1.5s ease forwards;
        }

        /* CSS for the entrance animation from right to left */
        .entrance-animation-right {
            opacity: 0;
            transform: translateX(50px);
            animation: slideInFromRight 1.5s ease forwards;
        }

        /* Media Query for responsiveness */
        @media (max-width: 768px) {
            .col-12 {
                padding-top: 20px;
                /* Adjust as needed */
            }
        }

        /* Keyframes for the animation from left to right */
        @keyframes slideInFromLeft {
            0% {
                opacity: 0;
                transform: translateX(-50px);
            }

            100% {
                opacity: 1;
                transform: translateX(0);
            }
        }

        /* Keyframes for the animation from right to left */
        @keyframes slideInFromRight {
            0% {
                opacity: 0;
                transform: translateX(50px);
            }

            100% {
                opacity: 1;
                transform: translateX(0);
            }
        }

        /* Hide horizontal overflow to prevent scroll bar */
        .col-12 {
            overflow-x: hidden;
        }

        /* CSS for the entrance animation */
        .h4-animation {
            opacity: 0;
            transform: translateY(20px);
            transition: opacity 1s ease, transform 1s ease;
        }

        .h4-animation.visible {
            opacity: 1;
            transform: translateY(0);
        }

        /* Media Query for responsiveness */
        @media (max-width: 768px) {
            .h4-animation {
                transition-duration: 1s;
                transition-timing-function: ease;
            }
        }

        @-webkit-keyframes gear-rotate {
            0% {
                -webkit-transform: rotate(0deg);
            }

            100% {
                -webkit-transform: rotate(-180deg);
            }
        }

        @-moz-keyframes gear-rotate {
            0% {
                transform: rotate(0deg);
            }

            100% {
                transform: rotate(-180deg);
            }
        }

        @keyframes gear-rotate {
            0% {
                transform: rotate(0deg);
            }

            100% {
                transform: rotate(-180deg);
            }
        }

        @-webkit-keyframes gear-rotate-left {
            0% {
                -webkit-transform: rotate(30deg);
            }

            100% {
                -webkit-transform: rotate(210deg);
            }
        }

        @-moz-keyframes gear-rotate-left {
            0% {
                -webkit-transform: rotate(30deg);
            }

            100% {
                -webkit-transform: rotate(210deg);
            }
        }

        @keyframes gear-rotate-left {
            0% {
                -webkit-transform: rotate(30deg);
            }

            100% {
                -webkit-transform: rotate(210deg);
            }
        }
    </style> -->
    <style>
        body {
            /* background-color: #f2f2f2; */
            min-height: 100vh;
            background-image: url("./assets/images/pimesbg6.jpg");
            background-attachment: fixed;
            background-size: 100% 100%;
            background-repeat: no-repeat;
        }

        footer {
            position: absolute;
            bottom: 0;
            left: 0;
            width: 100%;
        }

        .no-underline {
            text-decoration: none;
        }

        .container {
            max-width: 400px;
            margin: 0 auto;
            padding: 20px 20px;
            background-color: #fff;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        .container-big {
            max-width: 100%;
            margin: 0 auto;
            padding: 10px 20px;
            background-color: #fff;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        .form-control:focus {
            border-color: #6c757d;
            box-shadow: none;
        }

        .btn-primary {
            background-color: #007bff;
            border-color: #007bff;
            transition: all 0.2s ease-in-out;
        }

        .btn-primary:hover {
            background-color: #0069d9;
            border-color: #0062cc;
        }

        .btn-primary:focus {
            box-shadow: none;
        }

        .login-container {
            display: inline-block;
            vertical-align: top;
        }

        .no-underline {
            text-decoration: none;
        }

        .gears-container {
            width: 130px;
            height: 95px;
            font-size: 24px;
            padding-top: 10px;
            position: relative;
            display: inline-block;
        }

        .gear-rotate {
            width: 2em;
            height: 2em;
            top: 50%;
            left: 60%;
            margin-top: -1em;
            margin-left: -1em;
            background: #2706F0;
            position: absolute;
            border-radius: 1em;
            -webkit-animation: 1s gear-rotate linear infinite;
            -moz-animation: 1s gear-rotate linear infinite;
            animation: 1s gear-rotate linear infinite;
        }

        .gear-rotate-left {
            margin-top: -2.2em;
            top: 50%;
            width: 2em;
            height: 2em;
            background: #2706F0;
            position: absolute;
            border-radius: 1em;
            -webkit-animation: 1s gear-rotate-left linear infinite;
            -moz-animation: 1s gear-rotate-left linear infinite;
            animation: 1s gear-rotate-left linear infinite;
        }

        .gear-rotate::before,
        .gear-rotate-left::before {
            width: 2.8em;
            height: 2.8em;
            background:
                -webkit-linear-gradient(0deg, transparent 39%, #2706F0 39%, #2706F0 61%, transparent 61%),
                -webkit-linear-gradient(60deg, transparent 42%, #2706F0 42%, #2706F0 58%, transparent 58%),
                -webkit-linear-gradient(120deg, transparent 42%, #2706F0 42%, #2706F0 58%, transparent 58%);
            background:
                -moz-linear-gradient(0deg, transparent 39%, #2706F0 39%, #47EC19 61%, transparent 61%),
                -moz-linear-gradient(60deg, transparent 42%, #2706F0 42%, #2706F0 58%, transparent 58%),
                -moz-linear-gradient(120deg, transparent 42%, #2706F0 42%, #2706F0 58%, transparent 58%);
            background:
                -o-linear-gradient(0deg, transparent 39%, #2706F0 39%, #2706F0 61%, transparent 61%),
                -o-linear-gradient(60deg, transparent 42%, #2706F0 42%, #2706F0 58%, transparent 58%),
                -o-linear-gradient(120deg, transparent 42%, #47EC19 42%, #2706F0 58%, transparent 58%);
            background: -ms-linear-gradient(0deg, transparent 39%, #2706F0 39%, #2706F0 61%, transparent 61%), -ms-linear-gradient(60deg, transparent 42%, #2706F0 42%, #2706F0 58%, transparent 58%), -ms-linear-gradient(120deg, transparent 42%, #2706F0 42%, #2706F0 58%, transparent 58%);
            background:
                linear-gradient(0deg, transparent 39%, #2706F0 39%, #2706F0 61%, transparent 61%),
                linear-gradient(60deg, transparent 42%, #2706F0 42%, #2706F0 58%, transparent 58%),
                linear-gradient(120deg, transparent 42%, #2706F0 42%, #2706F0 58%, transparent 58%);
            position: absolute;
            content: "";
            top: -.4em;
            left: -.4em;
            border-radius: 1.4em;
        }

        .gear-rotate::after,
        .gear-rotate-left::after {
            width: 1em;
            height: 1em;
            background: #FFF01F;
            position: absolute;
            content: "";
            top: .5em;
            left: .5em;
            border-radius: .5em;
        }

        p.indent {
            text-indent: 40px;
        }

        /* Keyframe Animations */

        @-webkit-keyframes gear-rotate {
            0% {
                -webkit-transform: rotate(0deg);
            }

            100% {
                -webkit-transform: rotate(-180deg);
            }
        }

        @-moz-keyframes gear-rotate {
            0% {
                transform: rotate(0deg);
            }

            100% {
                transform: rotate(-180deg);
            }
        }

        @keyframes gear-rotate {
            0% {
                transform: rotate(0deg);
            }

            100% {
                transform: rotate(-180deg);
            }
        }

        @-webkit-keyframes gear-rotate-left {
            0% {
                -webkit-transform: rotate(30deg);
            }

            100% {
                -webkit-transform: rotate(210deg);
            }
        }

        @-moz-keyframes gear-rotate-left {
            0% {
                -webkit-transform: rotate(30deg);
            }

            100% {
                -webkit-transform: rotate(210deg);
            }
        }

        @keyframes gear-rotate-left {
            0% {
                -webkit-transform: rotate(30deg);
            }

            100% {
                -webkit-transform: rotate(210deg);
            }
        }
    </style>
</head>

<body class="d-flex flex-column min-vh-100">
    <div class="container-fluid flex-grow-1">
        <div class="row">
            <div class="col-12 col-md-4">
                <div class="container mx-auto m-3">
                    <form action="" method="POST" class="flex-column justify-content-center">
                        <div class="gears-container">
                            <div class="gear-rotate"></div>
                            <div class="gear-rotate-left"></div>
                        </div>
                        <div class="login-container pt-3">
                            <h2 class="mb-3 fw-bold">Login</h2>
                        </div>
                        <?php
                        // Call the displayError function when there's an error message to display
                        if (isset($errorMessage)) {
                            displayError($errorMessage);
                        }
                        ?>
                        <div class="input-group mb-3">
                            <span class="input-group-text"><i class="fa fa-user"></i></span>
                            <div class="form-floating">
                                <input type="text" class="form-control" id="username" name="prod_uname" placeholder="Enter Username" required>
                                <label for="username">Enter Username</label>
                            </div>
                        </div>
                        <div class="input-group mb-3">
                            <span class="input-group-text"><i class="fa fa-lock"></i></span>
                            <div class="form-floating password-input">
                                <input type="password" class="form-control" name="prod_pw" id="password" placeholder="Password" required>
                                <label for="password">Enter Password</label>
                            </div>
                            <span class="input-group-text" id="togglePassword"><i class="fa-regular fa-eye-slash"></i></span>
                        </div>
                        <div class="form-check mt-3">
                            <input type="checkbox" checked="checked" name="remember" class="form-check-input">
                            <label class="form-check-label">Remember me</label>
                        </div>
                        <button class="w-100 btn btn-lg btn-primary" type="submit" name="login_prod">Log in</button>
                    </form>
                    <div class="pt-2">
                        <p>Don't have an account? <a href="ATS_Prod_Create_Acc.php" class="no-underline">Sign Up</a></p>
                    </div>
                </div>

            </div>

            <div class="col-12 col-md-8">
                <div class="container-big mx-auto m-3">
                    <div class="row">
                        <div class="col btn btn-outline-primary active m-1 fw-bold" id="btnFeatures" data-bs-toggle="collapse" data-bs-target="#collapseFeatures" aria-expanded="true" aria-controls="collapseFeatures">Information</div>
                        <div class="col btn btn-outline-primary m-1 fw-bold" id="btnAbout" data-bs-toggle="collapse" data-bs-target="#collapseAbout" aria-expanded="false" aria-controls="collapseAbout">About</div>
                        <div class="col btn btn-outline-primary m-1 fw-bold" id="btnProducts" data-bs-toggle="collapse" data-bs-target="#collapseProducts" aria-expanded="false" aria-controls="collapseProducts">Products</div>
                    </div>
                    <div class="collapse show" id="collapseFeatures">
                        <div class="" data-aos="fade-right" data-aos-offset="300" data-aos-easing="ease-in-sine">
                            <h3 class="mb-2">
                                Production Performance Monitoring System (PPMS)
                            </h3>
                            <p class="indent">
                                Aims to reduce manual gathering of data from production personnels. It optimizes manufacturing processes by collecting real-time data to enable informed decisions, increased efficiency, and improved product quality. It connects production activities to overall business success, reducing downtime and ensuring product consistency.
                            </p>
                        </div>
                        <div class="accordion" id="featureAccordion" data-aos="zoom-in-right">
                            <!-- Feature 1 -->
                            <div class="accordion-item">
                                <h5 class="accordion-header" id="heading1">
                                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapse1" aria-expanded="false" aria-controls="collapse1">
                                        <i class="bi bi-check-circle-fill text-success me-2"></i> Real-time Data Collection
                                    </button>
                                </h5>
                                <div id="collapse1" class="accordion-collapse collapse" aria-labelledby="heading1" data-bs-parent="#featureAccordion">
                                    <div class="accordion-body">
                                        The system gathers data from technicians, operators, and other production personels in real-time, providing an up-to-date view of the entire manufacturing process.
                                    </div>
                                </div>
                            </div>

                            <!-- Feature 2 -->
                            <div class="accordion-item">
                                <h5 class="accordion-header" id="heading2">
                                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapse2" aria-expanded="false" aria-controls="collapse2">
                                        <i class="bi bi-check-circle-fill text-success me-2"></i> Performance Metrics
                                    </button>
                                </h5>
                                <div id="collapse2" class="accordion-collapse collapse" aria-labelledby="heading2" data-bs-parent="#featureAccordion">
                                    <div class="accordion-body">
                                        It offers key performance indicators (KPIs) and performance dashboards that display metrics such as production rate, cycle time, and equipment utilization, helping stakeholders monitor and assess productivity.
                                    </div>
                                </div>
                            </div>
                            <!-- Feature 3 -->
                            <div class="accordion-item">
                                <h5 class="accordion-header" id="heading3">
                                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapse3" aria-expanded="false" aria-controls="collapse3">
                                        <i class="bi bi-check-circle-fill text-success me-2"></i> Anomaly Detection
                                    </button>
                                </h5>
                                <div id="collapse3" class="accordion-collapse collapse" aria-labelledby="heading3" data-bs-parent="#featureAccordion">
                                    <div class="accordion-body">
                                        The system utilizes advanced algorithms to detect abnormalities or deviations from expected production patterns, enabling quick identification and resolution of potential issues.
                                    </div>
                                </div>
                            </div>
                            <!-- Feature 4 -->
                            <div class="accordion-item">
                                <h5 class="accordion-header" id="heading4">
                                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapse4" aria-expanded="false" aria-controls="collapse4">
                                        <i class="bi bi-check-circle-fill text-success me-2"></i> Downtime Tracking
                                    </button>
                                </h5>
                                <div id="collapse4" class="accordion-collapse collapse" aria-labelledby="heading4" data-bs-parent="#featureAccordion">
                                    <div class="accordion-body">
                                        It records and analyzes downtime events, helping identify the root causes and opportunities for reducing production interruptions.
                                    </div>
                                </div>
                            </div>
                            <!-- Feature 5 -->
                            <div class="accordion-item">
                                <h5 class="accordion-header" id="heading5">
                                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapse5" aria-expanded="false" aria-controls="collapse5">
                                        <i class="bi bi-check-circle-fill text-success me-2"></i> Quality Control
                                    </button>
                                </h5>
                                <div id="collapse5" class="accordion-collapse collapse" aria-labelledby="heading5" data-bs-parent="#featureAccordion">
                                    <div class="accordion-body">
                                        The system includes quality monitoring tools to inspect and ensure product quality, reducing defects and maintaining consistent standards.
                                    </div>
                                </div>
                            </div>
                            <!-- Feature 6 -->
                            <div class="accordion-item">
                                <h5 class="accordion-header" id="heading6">
                                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapse6" aria-expanded="false" aria-controls="collapse6">
                                        <i class="bi bi-check-circle-fill text-success me-2"></i> Historical Data and Trend Analysis
                                    </button>
                                </h5>
                                <div id="collapse6" class="accordion-collapse collapse" aria-labelledby="heading6" data-bs-parent="#featureAccordion">
                                    <div class="accordion-body">
                                        It stores historical production data, facilitating trend analysis to identify patterns, make forecasts, and improve production planning.
                                    </div>
                                </div>
                            </div>
                            <!-- Feature 7 -->
                            <div class="accordion-item">
                                <h5 class="accordion-header" id="heading7">
                                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapse7" aria-expanded="false" aria-controls="collapse7">
                                        <i class="bi bi-check-circle-fill text-success me-2"></i> Reporting and Alerts
                                    </button>
                                </h5>
                                <div id="collapse7" class="accordion-collapse collapse" aria-labelledby="heading7" data-bs-parent="#featureAccordion">
                                    <div class="accordion-body">
                                        The system generates customized reports and sends real-time alerts to relevant personnel when critical events or production targets are not being met.
                                    </div>
                                </div>
                            </div>
                            <!-- Feature 8 -->
                            <!-- <div class="accordion-item">
                                <h5 class="accordion-header" id="heading8">
                                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapse8" aria-expanded="false" aria-controls="collapse8">
                                        <i class="bi bi-check-circle-fill text-success me-2"></i> Integration and Connectivity
                                    </button>
                                </h5>
                                <div id="collapse8" class="accordion-collapse collapse" aria-labelledby="heading8" data-bs-parent="#featureAccordion">
                                    <div class="accordion-body">
                                        It can integrate with various manufacturing systems and Enterprise Resource Planning (ERP) software, streamlining data flow and enhancing overall operational efficiency.
                                    </div>
                                </div>
                                </div> -->
                            <!-- Feature 9 -->
                            <div class="accordion-item">
                                <h5 class="accordion-header" id="heading9">
                                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapse9" aria-expanded="false" aria-controls="collapse9">
                                        <i class="bi bi-check-circle-fill text-success me-2"></i> User-friendly Interface
                                    </button>
                                </h5>
                                <div id="collapse9" class="accordion-collapse collapse" aria-labelledby="heading9" data-bs-parent="#featureAccordion">
                                    <div class="accordion-body">
                                        The system offers an intuitive interface with visualizations and easy-to-interpret data, making it accessible to technicians, operators, supervisors and managers with varying technical backgrounds.
                                    </div>
                                </div>
                            </div>
                            <!-- Feature 10 -->
                            <div class="accordion-item">
                                <h5 class="accordion-header" id="heading10">
                                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapse10" aria-expanded="false" aria-controls="collapse10">
                                        <i class="bi bi-check-circle-fill text-success me-2"></i> Scalability
                                    </button>
                                </h5>
                                <div id="collapse10" class="accordion-collapse collapse" aria-labelledby="heading10" data-bs-parent="#featureAccordion">
                                    <div class="accordion-body">
                                        It is designed to accommodate the growing needs of the manufacturing environment, making it adaptable to changes in production volume and complexity.
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="collapse" id="collapseAbout">
                        <!-- <div class="card card-body" data-aos="fade-right" data-aos-offset="300" data-aos-easing="ease-in-sine">
                            <h3 class="mb-4">ATS (Automation Technology & Solutions)</h3>

                            <p>ATS is formerly part of AE (Automation Equipment) with 4 main contributing groups:</p>

                            <ul>
                                <li>COHU (former Delta Design) - Assembly and Test of PnP and Test Handlers</li>
                                <li>WD (Western Digital) - Manufactures and sells data technology products.</li>
                                <li>Nippon Signal - Signaling System.</li>
                                <li>Equipment Development - Design and Assembly of Machine.</li>
                            </ul>

                            <p>
                                As the Business is continuously growing at the same time while encountering constraints in space, Management decided to divide AE into two new Bus such as AE1 (currently SDEI) serving WD and Nippon Signal, and AE2 (currently ATS) serving COHU and focusing on Equipment Development.
                            </p>
                        </div> -->
                        <div class="card card-body" data-aos="fade-right" data-aos-offset="300" data-aos-easing="ease-in-sine">
                            <div class="row">
                                <div class="col">
                                    <h3 class="mb-3">ATS (Automation Technology & Solutions)</h3>
                                    <p> ATS is formerly part of AE (Automation Equipment) with 4 main contributing groups:</p>
                                    <ul>
                                        <li>COHU (former Delta Design) - Assembly and Test of PnP and Test Handlers</li>
                                        <li>WD (Western Digital) - Manufactures and sells data technology products.</li>
                                        <li>Nippon Signal - Signaling System.</li>
                                        <li>Equipment Development - Design and Assembly of Machine.</li>
                                    </ul>
                                    <p>
                                        As the Business is continuously growing at the same time while encountering constraints in space, Management decided to divide AE into two new Bus such as AE1 (currently SDEI) serving WD and Nippon Signal, and AE2 (currently ATS) serving COHU and focusing on Equipment Development.
                                    </p>
                                </div>
                                <div class="col-md-4">
                                    <img class="rounded img-thumbnail m-1" src="/ATS/ATSPROD_PORTAL/assets/images/ATS logoa.png" alt="ATS Logo">
                                    <img class="rounded img-thumbnail m-1" src="/ATS/ATSPROD_PORTAL/assets/images/COHU_BIG.png" alt="COHU Logo">
                                    <!-- <img class="rounded img-thumbnail m-1" src="/ATS/ATSPROD_PORTAL/assets/images/WD.jpg" alt="WD Logo"> -->
                                </div>
                            </div>
                        </div>

                    </div>
                    <div class="collapse" id="collapseProducts">
                        <div class="card-group shadow-lg">
                            <div class="card" data-aos="fade-right" data-aos-offset="300" data-aos-easing="ease-in-sine">
                                <img src="/ATS/ATSPROD_PORTAL/assets/images/JLP.png" class="card-img-top p-1 h-50" alt="JLP Tray Handling System">
                                <div class="card-body">
                                    <h5 class="card-title fw-bold mb-3">JLP</h5>
                                    <p class="card-text">Experience seamless automation with our <b>JLP Tray Handling System</b> transporting JEDEC trays to and from SMEMA-Equipped process tools.</p>
                                </div>
                            </div>
                            <div class="card" data-aos="fade-right" data-aos-offset="300" data-aos-easing="ease-in-sine">
                                <img src="/ATS/ATSPROD_PORTAL/assets/images/PNP.png" class="card-img-top p-1 h-50" alt="PNP Matrix">
                                <div class="card-body">
                                    <h5 class="card-title fw-bold mb-3">PNP Matrix</h5>
                                    <p class="card-text">Discover the cost-effective solution with our <b>PNP Matrix</b> designed for smaller device portfolios compared to Eclipse large devices.</p>
                                </div>
                            </div>
                            <div class="card" data-aos="fade-right" data-aos-offset="300" data-aos-easing="ease-in-sine">
                                <img src="/ATS/ATSPROD_PORTAL/assets/images/OLB.png" class="card-img-top p-1 h-50" alt="OLB Equipment Sorter">
                                <div class="card-body">
                                    <h5 class="card-title fw-bold mb-3">OLB</h5>
                                    <p class="card-text">Efficiently <b>sort devices offline</b> with our <b>OLB Equipment Sorter</b> after testing and binning to more categories than standard handlers.</p>
                                </div>
                            </div>
                            <div class="card" data-aos="fade-right" data-aos-offset="300" data-aos-easing="ease-in-sine">
                                <img src="/ATS/ATSPROD_PORTAL/assets/images/JTP.png" class="card-img-top p-3 h-50" alt="JTP Automated Tool">
                                <div class="card-body">
                                    <h5 class="card-title fw-bold mb-3">JTP</h5>
                                    <p class="card-text">
                                        Introducing the <b>JTP Automated Tool</b> designed to effortlessly <b>load or remove a thin metal plate (top plate) to JEDEC trays</b> on a SMEMA-compliant conveyor.
                                    </p>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
            <div class="container-fluid" style="background-color: #2706F0;">
                <div class="offcanvas offcanvas-end" data-bs-scroll="true" data-bs-backdrop="false" tabindex="-1" id="offcanvasDark" aria-labelledby="offcanvasDarkLabel" style="background-color: #2706F0;">
                    <div class="offcanvas-header text-white">
                        <h5 class="offcanvas-title fw-bold" id="offcanvasDarkLabel">ATS Systems</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="offcanvas" aria-label="Close"></button>
                    </div>
                    <div class="offcanvas-body text-bg-light">
                        <div class="d-flex border border-2 border-dark m-2">
                            <div class="p-2 flex-shrink-1 bg-white border-end border-dark">
                                <img src="http://192.168.6.144:8080/photos/csatlogo.png" alt="Csat_logo" height="35">
                            </div>
                            <div class="p-2 w-100 bg-dark-subtle mx-auto" onclick="window.location.href='http://192.168.6.144:8080/Login/Login';" style="cursor: pointer;">
                                <h4 class="fw-bold">ATS - QA</h4>
                            </div>
                        </div>
                        <div class="d-flex border border-2 border-dark m-2">
                            <div class="p-2 flex-shrink-1 bg-white border-end border-dark">
                                <img src="/ATS/ATSPROD_PORTAL/assets/images/ATS logoa.png" alt="ats_logo" height="35" width="5">
                            </div>
                            <!-- <div class="p-2 w-100 bg-dark-subtle mx-auto" onclick="window.location.href='http://192.168.6.144:8080/Login/Login';" style="cursor: pointer;">
                                <h4 class="fw-bold">ATS - PPIC</h4>
                            </div> -->
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Footer section -->
    <div class="container-fluid py-2" style="background-color: #2706F0;">
        <div class="row justify-content-between"> <!-- Add justify-content-between class here -->
            <div class="col fw-bold text-white">
                <span class="mb-md-0">&copy; P.IMES - ATS Production & Planning 2023</span>
            </div>
            <div class="col d-flex align-items-end justify-content-end">
                <ul class="nav list-unstyled d-flex">
                    <!-- <li class="ms-3"><a class="" href="#"><i class="bi bi-twitter text-white"></i></a></li>
                    <li class="ms-3"><a class="" href="#"><i class="bi bi-instagram text-white"></i></a></li>
                    <li class="ms-3"><a class="" href="#"><i class="bi bi-facebook text-white"></i></a></li> -->
                </ul>
            </div>
        </div>
    </div>

    <!-- ATS © 2023 <a href="https://pimes.com.ph" class="text-white">P. IMES Corp.</a> -->
    <script src="/ATS/ATSPROD_PORTAL/assets/js/bootstrap.bundle.min.js"></script>
    <script>
        AOS.init();

        const togglePassword = document.querySelector('#togglePassword');
        const password = document.querySelector('#password');

        togglePassword.addEventListener('click', function(e) {
            // toggle the type attribute
            const type = password.getAttribute('type') === 'password' ? 'text' : 'password';
            password.setAttribute('type', type);
            // toggle the icon
            this.querySelector('i').classList.toggle('fa-eye');
            this.querySelector('i').classList.toggle('fa-eye-slash');
        });
        $(document).ready(function() {
            // Set the default active button to "btnFeatures"
            $("#btnFeatures").addClass("active");
            $("#collapseFeatures").addClass("show");

            $(".btn").click(function() {
                // If the clicked button is already active, do nothing
                if ($(this).hasClass("active")) return;

                $(".btn").removeClass("active");
                $(this).addClass("active");

                // Hide all content
                $(".collapse").removeClass("show");

                // Show the clicked content
                const target = $(this).attr("data-bs-target");
                $(target).addClass("show");
            });
        });
    </script>
</body>

</html>