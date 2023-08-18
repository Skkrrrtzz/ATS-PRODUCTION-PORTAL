<?php include 'ATS_Prod_Header.php';

date_default_timezone_set("Asia/Manila"); // set default timezone
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

function displayError($message)
{
?>
    <script>
        Swal.fire({
            icon: 'error',
            title: 'Oops...',
            text: '<?php echo $message; ?>',
            showConfirmButton: false,
            timer: 1500
        }).then(function() {
            window.location.href = 'ATS_Prod_Home.php';
        });
    </script>
<?php
    exit();
}
// Validate and sanitize the username and password
if (isset($_POST['login_prod'])) {
    $username = $_POST['prod_uname'];
    $password = $_POST['prod_pw'];

    // Perform any necessary validation and sanitization of the username and password here
    $sql = "SELECT * FROM user WHERE username = ? AND department = ?";
    $stmt = $conn->prepare($sql);
    $department = 'Production Main'; // Hardcoded department
    // $role = "cable_supervisor";
    $stmt->bind_param("ss", $username, $department);
    $stmt->execute();
    $result = $stmt->get_result();

    if (empty($username) || empty($password)) {
        displayError("Input Fields Can`t Be Empty");
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
            if ($_SESSION['role'] === "cable_supervisor") {
                // You can redirect the user to another page or perform any other desired actions
                header('location: ATS_Production_Portal.php');
                exit();
            } else {
                // You can redirect the user to another page or perform any other desired actions
                header('location: PROD_Viewer.php');
                exit();
            }
        } else {
            // User authentication failed
            displayError("Authentication Failed");
        }
    } else {
        // User not found
        displayError("Invalid username or password.");
    }

    $stmt->close();
    if (isset($stmts)) {
        $stmts->close();
    }
}

$conn->close();

?>
<?php
// FUNCTIONS

function wp()
{ ?>
    <script>
        Swal.fire({
            icon: 'warning',
            title: 'Invalid',
            text: 'Invalid Password!',
            showConfirmButton: false,
            timer: 1500
        })
    </script>
<?php
}
function login()
{ ?>
    <script>
        Swal.fire({
            icon: 'success',
            title: 'Login Successfully!',
            showConfirmButton: false,
            timer: 1500
        }).then(function() {
            window.location.href = "sup_cable_main.php";
        });
    </script>
<?php
} ?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <style>
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

        /*
 * Keyframe Animations 
 */

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

<body>
    <div class="text-center">
        <marquee behavior="scroll" direction="left" width="50%">
            <h1 class="h3 mt-3 mb-3">Welcome to ATS PRODUCTION PORTAL!</h1>
        </marquee>
    </div>
    <div class="container mx-auto">
        <form action="" method="POST" class="flex-column justify-content-center">

            <div class="gears-container">
                <div class="gear-rotate"></div>
                <div class="gear-rotate-left"></div>
            </div>
            <div class="login-container pt-3">
                <h2 class="mb-3 fw-bold">Login</h2>
            </div>
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


    <script>
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
    </script>
</body>

</html>