<?php
include 'ATS_Prod_Header.php';
include 'PROD_navbar.php';

if ($role !== 'cable_supervisor') {
    echo '
    <div class="container-fluid">
    <!-- 404 Error Text -->
    <div class="text-center">
      <div class="error mx-auto" data-text="404">404</div>
      <p class="lead text-gray-800 mb-5">Page Not Found</p>
        <a href="/ATS/ATSPROD_PORTAL/ATS_Prod_Home.php">&larr; Back to Dashboard</a>
    </div>
  </div>';
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ADMINISTRATOR</title>
    <link href="/ATS/ATSPROD_PORTAL/assets/DataTables/datatables.min.css" rel="stylesheet" />
    <link href="/ATS/ATSPROD_PORTAL/assets/DataTables/Buttons-2.3.6/css/buttons.dataTables.min.css" rel="stylesheet" />
    <script src="/ATS/ATSPROD_PORTAL/assets/js/bootstrap.bundle.min.js"></script>
    <script src="/ATS/ATSPROD_PORTAL/assets/DataTables/datatables.min.js"></script>
    <script src="/ATS/ATSPROD_PORTAL/assets/DataTables/Buttons-2.3.6/js/dataTables.buttons.min.js"></script>
    <script src="/ATS/ATSPROD_PORTAL/assets/DataTables/Buttons-2.3.6/js/buttons.html5.min.js"></script>
    <script src="/ATS/ATSPROD_PORTAL/assets/DataTables/Buttons-2.3.6/js/buttons.print.min.js"></script>
    <script src="/ATS/ATSPROD_PORTAL/assets/DataTables/Buttons-2.3.6/js/buttons.colVis.min.js"></script>
    <script src="/ATS/ATSPROD_PORTAL/assets/DataTables/pdfmake-0.2.7/pdfmake.min.js"></script>
    <script src="/ATS/ATSPROD_PORTAL/assets/DataTables/pdfmake-0.2.7/vfs_fonts.js"></script>
    <script src="/ATS/ATSPROD_PORTAL/assets/DataTables/JSZip-2.5.0/jszip.min.js"></script>
    <!-- Add this line before your DataTable initialization code -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css" rel="stylesheet">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js"></script>
    <script src="/ATS/ATSPROD_PORTAL/assets/js/bootstrap.bundle.min.js"></script>


    <style>
        .dataTables_wrapper .dataTables_filter input[type="search"] {
            background-image: url('data:image/svg+xml;utf8,<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-search" viewBox="0 0 16 16"><path d="M11.742 10.344a6.5 6.5 0 1 0-1.397 1.398h-.001c.03.04.062.078.098.115l3.85 3.85a1 1 0 0 0 1.415-1.414l-3.85-3.85a1.007 1.007 0 0 0-.115-.1zM12 6.5a5.5 5.5 0 1 1-11 0 5.5 5.5 0 0 1 11 0z" /> </svg>');
            background-repeat: no-repeat;
            background-position: left 0.5rem center;
            background-size: 1rem;
        }

        .dataTables_wrapper .dataTables_filter input[type="search"] {
            padding-left: 1.75rem;
        }

        .dataTables_wrapper .dt-buttons {
            padding: 2px;

        }
    </style>
</head>

<body>
    <div class="table-responsive py-2">
        <div class="btn-group mx-1" role="group" aria-label="Mul-Del and Add-User">
            <button id="delete-selected-btn" class="btn btn-danger"><i class="fa-solid fa-circle-minus"></i> Delete Selected</button>
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addUserModal"><i class="fa-solid fa-circle-plus"></i> Add User</button>
        </div>

        <table id="admin_users" class="table table-striped table-sm" style="width: 100%">
            <thead class="table-dark fw-bold">
                <tr>
                    <th><input type="checkbox" id="select-all"></th>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Emp_ID</th>
                    <th>Password</th>
                    <th>Hashed Password</th>
                    <th>Department</th>
                    <th>Role</th>
                    <th>Options</th>
                </tr>
            </thead>
            <tbody>
            </tbody>
        </table>
    </div>
    <script>
        $(document).ready(function() {
            var table = $('#admin_users').DataTable({
                ajax: {
                    url: 'Users_Admin_command.php',
                    method: 'GET',
                    dataSrc: ''
                },
                dom: 'B<"row"<"col-sm-6"l><"col-sm-6"f>>t<"row"<"col-sm-6"i><"col-sm-6"p>>',
                buttons: [{
                    extend: 'copyHtml5',
                    text: '<i class="fa-solid fa-copy"></i> Copy',
                    className: 'btn btn-dark-subtle btn-sm',
                    init: function(api, node, config) {
                        $(node).removeClass('dt-button');
                    }
                }, {
                    extend: 'excelHtml5',
                    text: '<i class="fa-solid fa-file-excel"></i> Excel',
                    className: 'btn btn-success btn-sm',
                    init: function(api, node, config) {
                        $(node).removeClass('dt-button');
                    }
                }, {
                    extend: 'csvHtml5',
                    text: '<i class="fa-solid fa-file-csv"></i> CSV',
                    className: 'btn btn-dark btn-sm',
                    init: function(api, node, config) {
                        $(node).removeClass('dt-button');
                    }
                }, {
                    extend: 'pdfHtml5',
                    text: '<i class="fa-solid fa-file-pdf"></i> PDF',
                    className: 'btn btn-danger btn-sm',
                    orientation: 'landscape',
                    init: function(api, node, config) {
                        $(node).removeClass('dt-button');
                    }
                }, {
                    extend: 'print',
                    text: '<i class="fa-solid fa-print"></i> Print',
                    className: 'btn btn-dark-subtle btn-sm',
                    init: function(api, node, config) {
                        $(node).removeClass('dt-button');
                    }
                }],
                deferRender: true,
                language: {
                    searchPlaceholder: 'Search here..',
                    search: ""
                },
                fixedColumns: true,
                order: [
                    [1, 'asc']
                ],
                columns: [{
                        data: null,
                        orderable: false,
                        render: function(data, type, row) {
                            return '<input type="checkbox" class="delete-checkbox" data-id="' + row.user_ID + '">';
                        }
                    }, {
                        data: 'user_ID'
                    }, {
                        data: 'emp_name'
                    },
                    {
                        data: 'username'
                    },
                    {
                        data: 'password',
                        render: function(data, type, row) {
                            if (data) {
                                return '<div class="input-group password-toggle-container">' +
                                    '<input type="password" class="form-control password-input" value="' + data + '" disabled>' +
                                    '<button class="btn btn-outline-secondary password-toggle" type="button"><i class="fa-solid fa-eye-slash"></i></button>' +
                                    '</div>';
                            } else {
                                return '';
                            }
                        }
                    },
                    {
                        data: 'hashed_password'
                    },
                    {
                        data: 'department'
                    },
                    {
                        data: 'role'
                    },
                    {
                        data: null,
                        orderable: false,
                        render: function(data, type, row) {
                            return '<a href="javascript:void();" class="text-primary edit-btn fs-4" data-id="' + row.user_ID + '" data-bs-toggle="modal" data-bs-target="#editModal"><i class="fa-solid fa-pen-to-square"></i></a>' + ' <a href="javascript:void();" class="text-danger delete-btn fs-4" data-id= "' + row.user_ID + '" ><i class = "fa-solid fa-trash" ></i></a>';
                        }
                    }
                ]
            });

            // Handle password toggle
            $('#admin_users tbody').on('click', '.password-toggle', function() {
                var input = $(this).siblings('.password-input');
                var icon = $(this).find('i');

                if (input.attr('type') === 'password') {
                    input.attr('type', 'text');
                    icon.removeClass('fa-eye-slash').addClass('fa-eye');
                } else {
                    input.attr('type', 'password');
                    icon.removeClass('fa-eye').addClass('fa-eye-slash');
                }
            });
            // Handle select all checkbox
            $('#select-all').click(function() {
                $('.delete-checkbox').prop('checked', $(this).prop('checked'));
            });

            // Handle individual checkbox click
            $('#admin_users tbody').on('click', '.delete-checkbox', function() {
                if (!$(this).prop('checked')) {
                    $('#select-all').prop('checked', false);
                }
            });

            // Handle multiple delete button click
            $('#delete-selected-btn').click(function() {
                var selectedRows = $('.delete-checkbox:checked').closest('tr');
                var ids = [];
                selectedRows.each(function() {
                    var id = $(this).find('.delete-checkbox').data('id');
                    ids.push(id);
                });

                // Check if any checkboxes are selected
                if (selectedRows.length === 0) {
                    // Show a message indicating that no rows are selected
                    Swal.fire({
                        icon: 'info',
                        title: 'No Rows Selected',
                        text: 'Please select at least one row to delete.',
                    });
                    return; // Prevent further execution of the code
                }

                console.log(ids);

                // Display confirmation dialog using SweetAlert2
                Swal.fire({
                    icon: 'warning',
                    title: 'Are you sure?',
                    text: 'You are about to delete the selected rows: ' + ids,
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'Delete',
                    cancelButtonText: 'Cancel'
                }).then(function(result) {
                    if (result.isConfirmed) {
                        // Make an AJAX request to delete the rows
                        $.ajax({
                            url: 'Users_Admin_command.php',
                            method: 'POST',
                            data: {
                                ids: ids,
                                multipleDel: true
                            },
                            success: function(response) {
                                // Show success message using SweetAlert2
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Delete Successful',
                                    text: 'The data has been deleted successfully!',
                                    showConfirmButton: false,
                                    timer: 1000
                                }).then(function() {
                                    table.ajax.reload(); // Reload the DataTable
                                });

                            },
                            error: function() {
                                // Handle the case when the AJAX request encounters an error
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Delete Error!',
                                    text: 'An error occurred during deletion. Please try again.',
                                });
                            }
                        });
                    }
                });

                // Clear the selected checkboxes
                $('.delete-checkbox:checked').prop('checked', false);
            });
            //Handle edit button click
            $('#admin_users tbody').on('click', '.edit-btn', function() {
                selectedId = $(this).data('id'); // Store the id value in the variable
                var rowData = table.row($(this).closest('tr')).data(); // Get the data of the clicked row
                console.log(selectedId);
                $('#editModal').modal('show');

                // Set the values inside the form fields
                $('#nameField').val(rowData.emp_name);
                $('#empField').val(rowData.username);
                $('#passField').val(rowData.password);
                $('#deptField').val(rowData.department);
                $('#roleField').val(rowData.role);

            });
            // Handle submit button click inside the modal
            $('#submitBtn').on('click', function(event) {
                event.preventDefault(); // Prevent default form submission

                var id = selectedId;
                var updateName = $('#nameField').val();
                var updateEMP = $('#empField').val();
                var updatePass = $('#passField').val();
                var updateDept = $('#deptField').val();
                var updateRole = $('#roleField').val();

                // Make an AJAX request to update the values in the database
                $.ajax({
                    url: 'Users_Admin_command.php',
                    method: 'POST',
                    data: {
                        id: id,
                        name: updateName,
                        username: updateEMP,
                        pass: updatePass,
                        dept: updateDept,
                        role: updateRole,
                        update: true
                    },
                    success: function(response) {
                        console.log(response); // Check the value of the response

                        // If the update was successful, update the values in the DataTable
                        if (response === 'success') {
                            // Show success message using SweetAlert2
                            Swal.fire({
                                icon: 'success',
                                title: 'Update Successful',
                                text: 'The data has been updated successfully!',
                                showConfirmButton: false,
                                timer: 1000
                            }).then(function() {
                                // Close the modal
                                $('#editModal').modal('hide');
                                $('.modal-backdrop').remove();
                                table.ajax.reload(); // Reload the DataTable
                            });

                        } else {
                            // Handle the case when the update failed
                            Swal.fire({
                                icon: 'error',
                                title: 'Update Failed',
                                text: 'Failed to update the data. Please try again.',
                                showConfirmButton: true
                            });
                        }
                    },
                    error: function() {
                        // Handle the case when the AJAX request encounters an error
                        Swal.fire({
                            icon: 'error',
                            title: 'Update Error!',
                            text: 'An error occurred during the update. Please try again.',
                        });
                    }
                });

            });
            // Handle delete button click
            $('#admin_users tbody').on('click', '.delete-btn', function() {
                var row = $(this).closest('tr');
                var id = row.find('.edit-btn').data('id');

                // Display confirmation dialog using SweetAlert2
                Swal.fire({
                    icon: 'warning',
                    title: 'Are you sure?',
                    text: 'You are about to delete this row: ' + id,
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'Delete',
                    cancelButtonText: 'Cancel'
                }).then(function(result) {
                    if (result.isConfirmed) {
                        // Make an AJAX request to delete the row
                        $.ajax({
                            url: 'Users_Admin_command.php',
                            method: 'POST',
                            data: {
                                id: id,
                                delete: true
                            },
                            success: function(response) {
                                // Handle success and error responses
                                console.log(response); // Check the value of the response
                                // Show success message using SweetAlert2
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Delete Successful',
                                    text: 'The data has been deleted successfully!',
                                    showConfirmButton: false,
                                    timer: 1000
                                }).then(function() {
                                    table.ajax.reload(); // Reload the DataTable
                                });
                            },
                            error: function() {
                                // Handle the case when the AJAX request encounters an error
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Delete Error!',
                                    text: 'An error occurred during deletion. Please try again.',
                                });
                            }
                        });
                    }
                });
            });
            // Handle click event of "Submit" button in the modal
            $('#addBtn').on('click', function(event) {
                event.preventDefault(); // Prevent default form submission
                // Retrieve the values from the modal inputs
                var user = $('#addUserField').val();
                var emp_id = $('#addEmpField').val();
                var pass = $('#addpassField').val();
                var dept = $('#addDeptField').val();
                var role = $('#addRoleField').val();
                // Check if any required field is empty
                if (user === '' || emp_id === '' || pass === '' || dept === '' || role === '') {
                    // Display error message for empty fields
                    $('.alert').removeClass('d-none'); // Show the alert message
                    return; // Prevent further execution of the code
                } // Hide the alert message if all fields are filled
                $('.alert').addClass('d-none');
                // Perform an AJAX request to insert the data into the database
                $.ajax({
                    url: 'Users_Admin_command.php',
                    method: 'POST',
                    data: {
                        insert: true,
                        user: user,
                        emp_id: emp_id,
                        pass: pass,
                        dept: dept,
                        role: role
                    },
                    success: function(response) {
                        // Handle the success response
                        console.log(response); // Check the value of the response

                        // If the update was successful, update the values in the DataTable
                        if (response === 'success') {
                            // Show success message using SweetAlert2
                            Swal.fire({
                                icon: 'success',
                                title: 'Added Successful',
                                text: 'The data has been added successfully!',
                                showConfirmButton: false,
                                timer: 1000
                            }).then(function() {
                                // Reset the modal inputs
                                $('#addUserField').val('');
                                $('#addEmpField').val('');
                                $('#addpassField').val('');
                                $('#addDeptField').val('');
                                $('#addRoleField').val('');
                                // Close the modal
                                $('#addUserModal').modal('hide');
                                $('.modal-backdrop').remove();
                                table.ajax.reload(); // Reload the DataTable
                            });

                        } else {
                            // Handle the case when the update failed
                            Swal.fire({
                                icon: 'error',
                                title: 'Failed',
                                text: 'Failed to add the data, ' + response,
                                showConfirmButton: true
                            });
                        }
                    },
                    error: function(response) {
                        // Handle the error case
                        console.log('Error occurred during data insertion');
                    }
                });
            });
        });
    </script>
    <!-- Add user Modal -->
    <div class="modal fade" id="addUserModal" tabindex="-1" aria-labelledby="addUserModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-primary">
                    <h5 class="modal-title text-white" id="addUserModalLabel">Add User</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="alert alert-warning d-flex align-items-center mx-2 my-1 d-none" role="alert">
                    <i class="bi bi-exclamation-triangle-fill me-2"></i>
                    <div>
                        Please fill in all required fields.
                    </div>
                </div>
                <form action=""></form>
                <div class="modal-body">
                    <div class="mb-3 row">
                        <label for="addUserField" class="col-md-3 form-label">Name</label>
                        <div class="col-md-9">
                            <input type="text" class="form-control" id="addUserField" name="name" required>
                        </div>
                    </div>
                    <div class="mb-3 row">
                        <label for="addEmpField" class="col-md-3 form-label">Emp ID</label>
                        <div class="col-md-9">
                            <input type="number" class="form-control" id="addEmpField" name="Emp_ID" required>
                        </div>
                    </div>
                    <div class="mb-3 row">
                        <label for="addpassField" class="col-md-3 form-label">Password</label>
                        <div class="col-md-9">
                            <input type="password" class="form-control" id="addpassField" name="password" required>
                        </div>
                    </div>
                    <div class="mb-3 row">
                        <label for="addDeptField" class="col-md-3 form-label">Department</label>
                        <div class="col-md-9">
                            <input type="text" class="form-control" id="addDeptField" name="Department" required>
                        </div>
                    </div>
                    <div class="mb-3 row">
                        <label for="addRoleField" class="col-md-3 form-label">Role</label>
                        <div class="col-md-9">
                            <select class="form-select" id="addRoleField" name="role" required>
                                <option value="operator">Operator</option>
                                <option value="technician">Technician</option>
                                <option value="inspector">Inspector</option>
                                <option value="planner">Planner</option>
                                <option value="cable_supervisor">Supervisor</option>
                                <option value="viewer">Viewer</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="button" id="addBtn" class="btn btn-primary">Submit</button>
                </div>
            </div>
        </div>
    </div>
    <!-- Update user Modal -->
    <div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editModalLabel">Update User</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3 row">
                        <label for="nameField" class="col-md-3 form-label">Name</label>
                        <div class="col-md-9">
                            <input type="text" class="form-control" id="nameField" name="name">
                        </div>
                    </div>
                    <div class="mb-3 row">
                        <label for="empField" class="col-md-3 form-label">Emp_ID</label>
                        <div class="col-md-9">
                            <input type="number" class="form-control" id="empField" name="email">
                        </div>
                    </div>
                    <div class="mb-3 row">
                        <label for="passField" class="col-md-3 form-label">Password</label>
                        <div class="col-md-9">
                            <input type="password" class="form-control" id="passField" name="password">
                        </div>
                    </div>
                    <div class="mb-3 row">
                        <label for="deptField" class="col-md-3 form-label">Department</label>
                        <div class="col-md-9">
                            <input type="text" class="form-control" id="deptField" name="mobile">
                        </div>
                    </div>
                    <div class="mb-3 row">
                        <label for="roleField" class="col-md-3 form-label">Role</label>
                        <div class="col-md-9">
                            <select class="form-select" id="roleField" name="role">
                                <option value="operator">Operator</option>
                                <option value="technician">Technician</option>
                                <option value="inspector">Inspector</option>
                                <option value="planner">Planner</option>
                                <option value="cable_supervisor">Supervisor</option>
                                <option value="viewer">Viewer</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" id="submitBtn" class="btn btn-primary">Submit</button>
                </div>
            </div>
        </div>
    </div>
</body>


</html>