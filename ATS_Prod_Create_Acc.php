<?php include 'ATS_Prod_Header.php';
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check if the form was submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $emp_name = $_POST["name"];
    $username = $_POST["username"];
    $password = $_POST["password"];
    $role = $_POST["role"];
    $dept = $_POST["dept"];

    // Hash the password
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

    // Check if the user already exists
    $checkQuery = "SELECT * FROM user WHERE username = ?";
    $checkStmt = $conn->prepare($checkQuery);
    $checkStmt->bind_param("s", $username);
    $checkStmt->execute();
    $result = $checkStmt->get_result();

    if ($result->num_rows > 0) {
        displayError("User already exists!", "error");
    } else {
        // Insert the user into the database
        $insertQuery = "INSERT INTO pending_acc (emp_name, username, password, hashed_password, role, department) VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($insertQuery);

        if (!$stmt) {
            die("Error: " . $conn->error);
        }

        $stmt->bind_param("ssssss", $emp_name, $username, $password, $hashedPassword, $role, $dept);

        if ($stmt->execute()) {
            displaySuccess("Information submitted!", "Admin will check your information", "success");
        } else {
            displayError("Failed to create user account", "error");
        }

        $stmt->close();
    }

    $checkStmt->close();
}

$conn->close();

function displaySuccess($message, $msgtext, $message_code)
{
?>
    <script>
        Swal.fire({
            icon: '<?php echo $message_code; ?>',
            title: '<?php echo $message; ?>',
            text: '<?php echo $msgtext ?>',
            showConfirmButton: false,
            timer: 3000
        }).then(function() {
            window.location.href = 'ATS_Prod_Home.php';
        });
    </script>
<?php
}
function displayError($message, $message_code)
{
?>
    <script>
        Swal.fire({
            icon: '<?php echo $message_code; ?>',
            title: '<?php echo $message; ?>',
            showConfirmButton: false,
            timer: 1500
        })
    </script>
<?php
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Account Creation</title>
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

        .password-input {
            position: relative;
        }

        .password-input input {
            padding-right: 36px;
            /* Adjust as needed */
        }

        .password-input .toggle-password {
            position: absolute;
            top: 50%;
            right: 10px;
            /* Adjust as needed */
            transform: translateY(-50%);
            cursor: pointer;
        }
    </style>
</head>

<body>
    <div class="container mt-2">
        <h2>Create User Account</h2>
        <form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>" class="mt-4">
            <div class="input-group mb-3">
                <div class="form-floating">
                    <input type="text" class="form-control" id="name" name="name" placeholder="Name" required>
                    <label for="name">Name</label>
                </div>
            </div>

            <div class="input-group mb-3">
                <div class="form-floating">
                    <input type="text" class="form-control" id="dept" name="dept" placeholder="Department" value="Production" readonly>
                    <label for="dept">Department</label>
                </div>
            </div>

            <div class="input-group mb-3">
                <span class="input-group-text"><i class="fa fa-user"></i></span>
                <div class="form-floating">
                    <input type="text" class="form-control" id="username" name="username" placeholder="Username" required>
                    <label for="username">Username</label>
                </div>
            </div>

            <div class="input-group mb-3">
                <span class="input-group-text"><i class="fa fa-lock"></i></span>
                <div class="form-floating password-input">
                    <input type="password" class="form-control" name="password" id="password" placeholder="Password" required>
                    <label for="password">Password</label>
                    <div class="input-group-append">
                        <span class="input-group-text toggle-password" id="togglePassword">
                            <i class="fa-regular fa-eye-slash"></i>
                        </span>
                    </div>
                </div>
            </div>

            <div class="form-floating mb-3">
                <select class="form-select" id="role" name="role" required>
                    <option selected value="manager">Manager</option>
                    <option value="supervisor">Supervisor</option>
                    <option value="employee">Employee</option>
                </select>
                <label for="role">Select Role</label>
            </div>

            <button type="submit" class="btn btn-primary">Submit</button>
        </form>
    </div>
    <script>
        const togglePassword = document.querySelector('.toggle-password');
        const password = document.querySelector('#password');

        togglePassword.addEventListener('click', function(e) {
            // Toggle the type attribute
            const type = password.getAttribute('type') === 'password' ? 'text' : 'password';
            password.setAttribute('type', type);

            // Toggle the eye icon
            this.querySelector('i').classList.toggle('fa-eye');
            this.querySelector('i').classList.toggle('fa-eye-slash');
        });
    </script>

</body>

</html>