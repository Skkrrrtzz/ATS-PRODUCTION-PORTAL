<?php include 'ATS_Prod_Header.php' ?>
<?php include 'PROD_navbar.php' ?>
<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit'])) {
    if ($_FILES['profileImage']['size'] <= 0) {
        displayError("Hey, Please choose at least one file", "warning");
    } else {
        foreach ($_FILES as $key => $value) {
            if (0 < $value['error']) {
                displayError('Error during file upload ' . $value['error'], "error");
            } else if (!empty($value['name'])) {
                // Function to check if the user exists in the files_data table 
                function checkUserExists($conn, $emp_id)
                {
                    $sql = "SELECT username FROM user WHERE username = ?";
                    $stmt = mysqli_prepare($conn, $sql);
                    mysqli_stmt_bind_param($stmt, 's', $emp_id);
                    mysqli_stmt_execute($stmt);
                    mysqli_stmt_store_result($stmt);
                    $userExists = mysqli_stmt_num_rows($stmt) > 0;
                    mysqli_stmt_close($stmt);

                    $sql = "SELECT emp_id FROM files_data WHERE emp_id = ?";
                    $stmt = mysqli_prepare($conn, $sql);
                    mysqli_stmt_bind_param($stmt, 's', $emp_id);
                    mysqli_stmt_execute($stmt);
                    mysqli_stmt_store_result($stmt);
                    $filesDataExists = mysqli_stmt_num_rows($stmt) > 0;
                    mysqli_stmt_close($stmt);

                    return $userExists && $filesDataExists;
                }

                // Check if the user exists in the files_data table
                $userExists = checkUserExists($conn, $emp_id);

                if ($userExists) {
                    $sql = "UPDATE files_data SET file_name = ?, type = ?, size = ?, content = ?, saved_date = ? WHERE emp_id = ?";
                } else {
                    $sql = "INSERT INTO files_data (emp_id, file_name, type, size, content, saved_date) VALUES (?, ?, ?, ?, ?, ?)";
                }

                $stmt = mysqli_prepare($conn, $sql);
                $file_name = $value['name'];
                $type = $value['type'];
                $size = filesize_formatted($value['size']);
                $content = file_get_contents($value['tmp_name']);
                $saved_date = date('Y-m-d H:i:s');

                if ($userExists) {
                    mysqli_stmt_bind_param($stmt, 'ssssss', $file_name, $type, $size, $content, $saved_date, $emp_id);
                } else {
                    mysqli_stmt_bind_param($stmt, 'ssssss', $emp_id, $file_name, $type, $size, $content, $saved_date);
                }

                $result = mysqli_stmt_execute($stmt);

                if ($result) {
                    displayError("Your profile has been successfully updated", "success");
                } else {
                    displayError("Error occurred while updating your profile", "error");
                }

                mysqli_stmt_close($stmt);
            }
        }
    }
}
function filesize_formatted($size)
{
    $units = array('B', 'KB', 'MB', 'GB', 'TB', 'PB', 'EB', 'ZB', 'YB');
    $power = $size > 0 ? floor(log($size, 1024)) : 0;

    return number_format($size / pow(1024, $power), 2, '.', ',') . ' ' . $units[$power];
}
function displayError($message, $message_code)
{
?>
    <script>
        Swal.fire({
            icon: '<?php echo $message_code; ?>',
            title: '<?php echo $message; ?>',
            showConfirmButton: true
        }).then(function() {
            window.location.href = 'Prod_profile.php';
        });
    </script>
<?php
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Production Profile</title>
</head>

<body>
    <div class="my-2 text-center">
        <h2 class="fw-bold">User Information</h2>
    </div>
    <div class="container col-6 border-2 shadow-lg rounded-1 my-2">
        <form id="editForm">
            <div class="mb-3">
                <div class="text-center">
                    <img class="img-thumbnail text-center mt-2" src="<?php echo $imgUrl; ?>" style="max-width: 200px;" alt="Profile Picture">
                </div>
            </div>

            <div class="mb-3">
                <label for="name" class="form-label fw-bold">Name:</label>
                <input type="text" class="form-control" id="name" value="<?php echo $name; ?>" readonly>
            </div>
            <div class="mb-3">
                <label for="employeeId" class="form-label fw-bold">Employee ID:</label>
                <input type="text" class="form-control" id="employeeId" value="<?php echo $emp_id; ?>" readonly>
            </div>
            <div class="mb-3">
                <label for="password" class="form-label fw-bold">Password:</label>
                <input type="password" class="form-control" id="password" value="<?php echo $pw; ?>" readonly>
            </div>
            <div class="mb-3">
                <label for="email" class="form-label fw-bold">Email:</label>
                <input type="email" class="form-control" id="email" value="john.doe@example.com" readonly>
            </div>
            <div class="mb-3">
                <label for="department" class="form-label fw-bold">Department:</label>
                <input type="text" class="form-control" id="department" value="<?php echo $dept; ?>" readonly>
            </div>
            <div class="mb-3">
                <label for="role" class="form-label fw-bold">Role:</label>
                <input type="text" class="form-control" id="role" value="<?php echo $role; ?>" readonly>
            </div>
            <div class="mb-3">
                <label for="motto" class="form-label fw-bold">Motto:</label>
                <textarea class="form-control" id="motto" rows="3" readonly>Success is not final, failure is not fatal: It is the courage to continue that counts.</textarea>
            </div>
            <div class="mb-3 text-end">
                <button type="button" class="btn btn-primary mb-2" data-bs-toggle="modal" data-bs-target="#editModal">Edit</button>
            </div>
        </form>
    </div>

    <!-- Edit Modal -->
    <div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editModalLabel">Edit User Information</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="POST" enctype="multipart/form-data">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="profileImage" class="form-label fw-bold">Profile Picture:</label>
                            <input type="file" class="form-control" id="profileImage" name="profileImage" onchange="validateImage(event)">
                            <img id="imagePreview" src="#" alt="Preview" class="mt-2" style="max-width: 200px; display: none;">
                        </div>
                        <div class="mb-3">
                            <label for="editName" class="form-label fw-bold">Name:</label>
                            <input type="text" class="form-control" id="editName" value="<?php echo $name; ?>">
                        </div>
                        <div class="mb-3">
                            <label for="editEmployeeId" class="form-label fw-bold">Employee ID:</label>
                            <input type="text" class="form-control" id="editEmployeeId" value="<?php echo $emp_id; ?>">
                        </div>
                        <div class="mb-3">
                            <label for="editPassword" class="form-label fw-bold">Password:</label>
                            <div class="input-group">
                                <input type="password" class="form-control" id="editPassword" value="<?php echo $pw; ?>">
                                <span class="input-group-text" id="togglePassword"><i class="fa-regular fa-eye-slash"></i></span>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="editEmail" class="form-label fw-bold">Email:</label>
                            <input type="email" class="form-control" id="editEmail" value="john.doe@example.com">
                        </div>
                        <div class="mb-3">
                            <label for="editDepartment" class="form-label fw-bold">Department:</label>
                            <input type="text" class="form-control" id="editDepartment" value="<?php echo $dept; ?>">
                        </div>
                        <div class="mb-3">
                            <label for="editRole" class="form-label fw-bold">Role:</label>
                            <input type="text" class="form-control" id="editRole" value="<?php echo $role; ?>">
                        </div>
                        <div class="mb-3">
                            <label for="editMotto" class="form-label fw-bold">Motto:</label>
                            <textarea class="form-control" id="editMotto" rows="3">Success is not final, failure is not fatal: It is the courage to continue that counts.</textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" name="submit" class="btn btn-primary">Save Changes</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        const togglePassword = document.querySelector('#togglePassword');
        const password = document.querySelector('#editPassword');

        togglePassword.addEventListener('click', function(e) {
            // toggle the type attribute
            const type = password.getAttribute('type') === 'password' ? 'text' : 'password';
            password.setAttribute('type', type);
            // toggle the icon
            this.querySelector('i').classList.toggle('fa-eye');
            this.querySelector('i').classList.toggle('fa-eye-slash');
        });

        function validateImage(event) {
            const file = event.target.files[0];
            const imageType = /^image\//;

            if (!imageType.test(file.type)) {
                alert('Please select an image file.');
                // Reset the file input
                event.target.value = '';
                return;
            }

            // Code for previewing the image goes here
            previewImage(event);
        }

        function previewImage(event) {
            const input = event.target;
            const preview = document.getElementById('imagePreview');
            if (input.files && input.files[0]) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    preview.src = e.target.result;
                    preview.style.display = 'block';
                };
                reader.readAsDataURL(input.files[0]);
            } else {
                preview.src = '#';
                preview.style.display = 'none';
            }
        }
    </script>


</body>

</html>