<?php
session_start();

$host = "localhost";
$user = "root";
$password = "";
$dbname = "ewip";

$conn = new mysqli($host, $user, $password, $dbname);
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
            window.location.href = 'ATS_Header.php';
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