<?php include '../ATS_Prod_Header.php'; ?>
<?php include '../PROD_navbar.php'; ?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="/ATS/ATSPROD_PORTAL/assets/DataTables/datatables.min.css" rel="stylesheet" />
    <link href="/ATS/ATSPROD_PORTAL/assets/DataTables/Buttons-2.3.6/css/buttons.dataTables.min.css" rel="stylesheet" />
    <script src="/ATS/ATSPROD_PORTAL/assets/js/bootstrap.bundle.min.js"></script>
    <script src="/ATS/ATSPROD_PORTAL/assets/DataTables/datatables.min.js"></script>
    <script src="/ATS/ATSPROD_PORTAL/assets/DataTables/Buttons-2.3.6/js/dataTables.buttons.min.js"></script>
    <script src="/ATS/ATSPROD_PORTAL/assets/DataTables/Buttons-2.3.6/js/buttons.html5.min.js"></script>
    <script src="/ATS/ATSPROD_PORTAL/assets/DataTables/Buttons-2.3.6/js/buttons.print.min.js"></script>
    <script src="/ATS/ATSPROD_PORTAL/assets/DataTables/JSZip-2.5.0/jszip.min.js"></script>
    <script src="/ATS/ATSPROD_PORTAL/assets/js/bootstrap.bundle.min.js"></script>
    <style>
        .dataTables_wrapper .dataTables_filter input[type="search"] {
            background-image: url('data:image/svg+xml;utf8,<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-search" viewBox="0 0 16 16"><path d="M14.83 13.543l-3.328-3.33A5.53 5.53 0 0 0 13.5 6.5c0-3.09-2.41-5.5-5.5-5.5S2.5 3.41 2.5 6.5 4.91 12 8 12c1.182 0 2.28-.357 3.205-.973l3.328 3.33a.498.498 0 0 0 .707 0l.56-.56a.5.5 0 0 0 0-.707zM8 11a4 4 0 1 1 0-8 4 4 0 0 1 0 8z"/></svg>');
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
    <div class="d-flex justify-content-center py-1">
        <div class="table-responsive py-2 w-75 border border-3 border-dark-subtle rounded">
            <table id="prod_eff" style="width:100%" class="table table-hover table-sm display compact">
                <thead class="table-dark fw-bold">
                    <tr>
                        <th>ID</th>
                        <th>Date</th>
                        <th>Cable Efficiency</th>
                        <th>Main Efficiency</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                </tbody>
            </table>
        </div>
    </div>
    <script>
        $(document).ready(function() {
            var table = $('#prod_eff').DataTable({
                ajax: {
                    url: 'PROD_Eff_data_command.php',
                    method: 'GET',
                    dataSrc: ''
                },
                dom: 'B<"row"<"col-sm-6"l><"col-sm-6"f>>t<"row"<"col-sm-6"i><"col-sm-6"p>>',
                buttons: [{
                    text: '<i class="fa-solid fa-circle-plus"></i> Add', // Custom button text
                    className: 'btn btn-dark btn-sm', // Custom button classes
                    action: function(e, dt, node, config) {
                        $('#addEff').modal('show');
                    }
                }, {
                    extend: 'excelHtml5',
                    text: '<i class="bi bi-filetype-xlsx"></i> Excel',
                    className: 'btn btn-success btn-sm',
                    init: function(api, node, config) {
                        $(node).removeClass('dt-button');
                    }
                }, {
                    extend: 'print',
                    text: '<i class="fa-solid fa-print"></i> Print',
                    className: 'btn btn-info btn-sm',
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
                    [1, 'desc']
                ],
                columnDefs: [{
                    targets: [''],
                    className: "fontsize"
                }],
                columns: [{
                        data: 'id'
                    },
                    {
                        data: 'record_date'
                    },
                    {
                        data: 'operator_efficiency',
                        render: function(data, type, row) {
                            if (data !== null && data !== '') {
                                return '<span style="font-weight: bold;">' + data + '%</span>';
                            } else {
                                return '<span style="font-weight: bold;">0%</span>';
                            }
                        }
                    },
                    {
                        data: 'technician_efficiency',
                        render: function(data, type, row) {
                            if (data !== null && data !== '') {
                                return '<span style="font-weight: bold;">' + data + '%</span>';
                            } else {
                                return '<span style="font-weight: bold;">0%</span>';
                            }
                        }
                    },
                    {
                        data: null,
                        orderable: false,
                        render: function(data, type, row) {
                            return '<a href="javascript:void();" class="text-primary edit-btn fs-4" data-id="' + row.id + '" data-bs-toggle="modal" data-bs-target="#editModal"><i class="fa-solid fa-pen-to-square"></i></a>' + ' <a href="javascript:void();" class ="text-danger delete-btn fs-4" data-id= "' + row.id + '" ><i class = "fa-solid fa-trash" ></i></a>';
                        }
                    }
                ]
            });
            //Handle edit button click
            $('#prod_eff tbody').on('click', '.edit-btn', function() {
                selectedId = $(this).data('id'); // Store the id value in the variable
                var rowData = table.row($(this).closest('tr')).data(); // Get the data of the clicked row
                console.log(selectedId);
                $('#editModal').modal('show');

                // Set the values inside the form fields
                $('#dateField').val(rowData.record_date);
                $('#operatorEffField').val(rowData.operator_efficiency);
                $('#techEffField').val(rowData.technician_efficiency);

            });
            // Handle submit button click inside the modal
            $('#submitBtn').on('click', function(event) {
                event.preventDefault(); // Prevent default form submission

                var id = selectedId;
                var updateDate = $('#dateField').val();
                var updateCableEff = $('#operatorEffField').val();
                var updateTechEff = $('#techEffField').val();

                // Make an AJAX request to update the values in the database
                $.ajax({
                    url: 'PROD_Eff_data_command.php',
                    method: 'POST',
                    data: {
                        id: id,
                        date: updateDate,
                        cableEff: updateCableEff,
                        techEff: updateTechEff,
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
                                // Manually trigger the dismissal of the modal
                                document.getElementById('dismissModalBtn').click();
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
            $('#prod_eff tbody').on('click', '.delete-btn', function() {
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
                            url: 'PROD_Eff_data_command.php',
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

                // Prevent default form submission
                event.preventDefault();

                // Retrieve the values from the modal inputs
                var InsertDate = $('#dateAddField').val();
                var InsertCableEff = $('#cableEffAddField').val();
                var InsertTechEff = $('#techEffAddField').val();

                // Check if any required field is empty
                if (InsertDate === '' || InsertCableEff === '' || InsertTechEff === '') {
                    // Display error message for empty fields
                    $('.alert').removeClass('d-none'); // Show the alert message
                    return; // Prevent further execution of the code
                }

                // Hide the alert message if all fields are filled
                $('.alert').addClass('d-none');

                // Perform an AJAX request to insert the data into the database
                $.ajax({
                    url: 'PROD_Eff_data_command.php',
                    method: 'POST',
                    data: {
                        insert: true,
                        date: InsertDate,
                        cableEff: InsertCableEff,
                        techEff: InsertTechEff
                    },
                    success: function(response) {
                        // Handle the success response

                        console.log(response); // Check the value of the response

                        // Show success message using SweetAlert2
                        Swal.fire({
                            icon: 'success',
                            title: 'Added Successful',
                            text: 'The data has been added!',
                            showConfirmButton: false,
                            timer: 1500
                        }).then(function() {
                            // Close the modal
                            document.getElementById('dismissModalBtn').click();
                            table.ajax.reload();
                        });
                    },
                    error: function() {
                        // Handle the error case
                        Swal.fire({
                            icon: 'error',
                            title: 'Insert Data Error!',
                            text: 'An error occurred during inserting data. Please try again.',
                        });
                        console.log('Error occurred during data insertion');
                    }
                });
            });
        });
    </script>
    <!-- Add Modal -->
    <div class="modal fade" id="addEff" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="addEffLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-success text-white">
                    <h1 class="modal-title fs-5" id="addEffLabel">ADD</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="alert alert-warning d-flex align-items-center mx-2 my-1 d-none" role="alert">
                        <i class="bi bi-exclamation-triangle-fill me-2"></i>
                        <div>
                            Please fill in all required fields.
                        </div>
                    </div>
                    <div class="row g-3 m-2">
                        <div class="col-sm form-floating">
                            <input type="date" class="form-control" id="dateAddField" name="date">
                            <label for="dateAddField">Date</label>
                        </div>
                        <div class="col-sm form-floating">
                            <input type="number" class="form-control" id="cableEffAddField" name="addcable_eff">
                            <label for="cableEffAddField">Cable Efficiency</label>
                        </div>
                        <div class="col-sm form-floating">
                            <input type="number" class="form-control" id="techEffAddField" name="addtech_eff">
                            <label for="techEffAddField">Technician Efficiency</label>
                        </div>
                    </div>
                    <!-- <div class="row g-3 m-2">
                        <div class="col-sm form-floating">
                            <input type="text" class="form-control" id="remarksAddField" name="remarks">
                            <label for="remarksAddField">Remarks</label>
                        </div>
                    </div> -->
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" id="dismissModalBtn">Close</button>
                    <button type="button" id="addBtn" class="btn btn-success">Submit</button>
                </div>
            </div>
        </div>
    </div>
    <!--Update Modal -->
    <div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title" id="editModalLabel">Update</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row g-3 m-2">
                        <div class="col-sm form-floating">
                            <input type="date" class="form-control" id="dateField" name="date">
                            <label for="dateField">Date</label>
                        </div>
                        <div class="col-sm form-floating">
                            <input type="number" class="form-control" id="operatorEffField" name="cable_eff">
                            <label for="operatorEffField">Cable Efficiency</label>
                        </div>
                        <div class="col-sm form-floating">
                            <input type="number" class="form-control" id="techEffField" name="tech_eff">
                            <label for="techEffField">Technician Efficiency</label>
                        </div>
                    </div>
                    <!-- <div class="row g-3 m-2">
                        <div class="col-sm form-floating">
                            <input type="text" class="form-control" id="remarksField" name="remarks">
                            <label for="remarksField">Remarks</label>
                        </div>
                    </div> -->
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" id="dismissModalBtn">Close</button>
                    <button type="submit" id="submitBtn" class="btn btn-primary">Submit</button>
                </div>
            </div>
        </div>
    </div>
</body>

</html>