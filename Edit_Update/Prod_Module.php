<?php include '../ATS_Prod_Header.php'; ?>
<?php include '../PROD_navbar.php'; ?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Production Main Module Editor</title>
    <link href="/ATS/ATSPROD_PORTAL/assets/DataTables/datatables.min.css" rel="stylesheet" />
    <link href="/ATS/ATSPROD_PORTAL/assets/DataTables/Buttons-2.3.6/css/buttons.dataTables.min.css" rel="stylesheet" />
    <script src="/ATS/ATSPROD_PORTAL/assets/js/bootstrap.bundle.min.js"></script>
    <script src="/ATS/ATSPROD_PORTAL/assets/DataTables/datatables.min.js"></script>
    <script src="/ATS/ATSPROD_PORTAL/assets/DataTables/Buttons-2.3.6/js/dataTables.buttons.min.js"></script>
    <script src="/ATS/ATSPROD_PORTAL/assets/DataTables/Buttons-2.3.6/js/buttons.html5.min.js"></script>
    <script src="/ATS/ATSPROD_PORTAL/assets/DataTables/Buttons-2.3.6/js/buttons.print.min.js"></script>
    <script src="/ATS/ATSPROD_PORTAL/assets/DataTables/Buttons-2.3.6/js/buttons.colVis.min.js"></script>
    <script src="/ATS/ATSPROD_PORTAL/assets/DataTables/JSZip-2.5.0/jszip.min.js"></script>
    <!-- Add this line before your DataTable initialization code -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css" rel="stylesheet">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js"></script>
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
    <div class="table-responsive py-2">
        <button id="delete-selected-btn" class="btn btn-danger mx-1"><i class="fa-solid fa-circle-minus"></i> Delete Selected</button>
        <table id="prod_module" style="width:100%" class="table table-hover table-sm display compact">
            <thead class="table-dark fw-bold">
                <tr>
                    <th><input type="checkbox" id="select-all"></th>
                    <th>ID</th>
                    <th>Date</th>
                    <th>Part No</th>
                    <th>Description</th>
                    <th>Product</th>
                    <th>PO</th>
                    <th>Batch</th>
                    <th>Name</th>
                    <th>Emp ID</th>
                    <th>Stations</th>
                    <th>Code</th>
                    <th>Started</th>
                    <th>Ended</th>
                    <th>Status</th>
                    <th>Build%</th>
                    <th>Remarks</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
            </tbody>
        </table>
    </div>
    <script>
        $(document).ready(function() {
            var table = $('#prod_module').DataTable({
                ajax: {
                    url: 'Prod_Module_command.php',
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
                columns: [{
                        data: null,
                        orderable: false,
                        render: function(data, type, row) {
                            return '<input type="checkbox" class="delete-checkbox" data-id="' + row.ID + '">';
                        }
                    }, {
                        data: 'ID'
                    },
                    {
                        data: 'date_updated'
                    },
                    {
                        data: 'Part_No'
                    },
                    {
                        data: 'description'
                    },
                    {
                        data: 'product'
                    },
                    {
                        data: 'Prod_Order_No'
                    },
                    {
                        data: 'batch_no',
                        render: function(data, type, row) {
                            return '<span style="font-weight: bold;">' + data + '</span>';
                        }
                    },
                    {
                        data: 'Name'
                    },
                    {
                        data: 'Emp_ID'
                    },
                    {
                        data: 'Stations'
                    },
                    {
                        data: 'Act_Code'
                    },
                    {
                        data: 'act_started'
                    },
                    {
                        data: 'act_ended'
                    },
                    {
                        data: 'wo_status',
                        render: function(data, type, row) {
                            if (data === 'IN-PROCESS') {
                                return '<span class="text-success fw-bold">' + data + '</span>';
                            } else if (data === 'IDLE') {
                                return '<span class="text-warning fw-bold">' + data + '</span>';
                            } else {
                                return data;
                            }
                        }
                    },
                    {
                        data: 'build_percent'
                    },
                    {
                        data: 'remarks'
                    },
                    {
                        data: null,
                        orderable: false,
                        render: function(data, type, row) {
                            return '<a href="javascript:void();" class="text-success edit-btn fs-5" data-id="' + row.ID + '" data-bs-toggle="modal" data-bs-target="#editModal"><i class="fa-solid fa-pen-to-square"></i></a>' + ' <a href="javascript:void();" class ="text-danger delete-btn fs-5" data-id= "' + row.ID + '" ><i class = "fa-solid fa-trash" ></i></a>';
                        }
                    }
                ]
            });
            //Handle edit button click
            $('#prod_module tbody').on('click', '.edit-btn', function() {
                selectedId = $(this).data('id'); // Store the id value in the variable
                var rowData = table.row($(this).closest('tr')).data(); // Get the data of the clicked row
                console.log(selectedId);
                $('#editModal').modal('show');

                // Set the values inside the form fields
                $('#dateField').val(rowData.date_updated);
                $('#part_noField').val(rowData.Part_No);
                $('#descField').val(rowData.description);
                $('#productField').val(rowData.product);
                $('#prod_noField').val(rowData.Prod_Order_No);
                $('#batch_noField').val(rowData.batch_no);
                $('#nameField').val(rowData.Name);
                $('#emp_idField').val(rowData.Emp_ID);
                $('#stationsField').val(rowData.Stations);
                $('#Act_CodeField').val(rowData.Act_Code);
                $('#Act_StartField').val(rowData.act_started);
                $('#Act_EndedField').val(rowData.act_ended);
                $('#wo_statusField').val(rowData.wo_status);
                $('#build_percentField').val(rowData.build_percent);
                $('#remarksField').val(rowData.remarks);

            });
            // Handle submit button click inside the modal
            $('#submitBtn').on('click', function(event) {
                event.preventDefault(); // Prevent default form submission

                var id = selectedId;
                var updateDate = $('#dateField').val();
                var updatePartNo = $('#part_noField').val();
                var updateDesc = $('#descField').val();
                var updateProd = $('#productField').val();
                var updatePO = $('#prod_noField').val();
                var updateBatch = $('#batch_noField').val();
                var updateName = $('#nameField').val();
                var updateEMP = $('#emp_idField').val();
                var updateStations = $('#stationsField').val();
                var updateCode = $('#Act_CodeField').val();
                var updateStart = $('#Act_StartField').val();
                var updateEnd = $('#Act_EndedField').val();
                var updateWo = $('#wo_statusField').val();
                var updateBuild = $('#build_percentField').val();
                var updateRemarks = $('#remarksField').val();

                // Make an AJAX request to update the values in the database
                $.ajax({
                    url: 'Prod_Module_command.php',
                    method: 'POST',
                    data: {
                        id: id,
                        date: updateDate,
                        part_no: updatePartNo,
                        description: updateDesc,
                        product: updateProd,
                        prod_no: updatePO,
                        batch_no: updateBatch,
                        name: updateName,
                        emp_id: updateEMP,
                        stations: updateStations,
                        act_code: updateCode,
                        act_start: updateStart,
                        act_end: updateEnd,
                        wo_status: updateWo,
                        build_percent: updateBuild,
                        remarks: updateRemarks,
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
            // // Handle delete button click
            $('#prod_module tbody').on('click', '.delete-btn', function() {
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
                            url: 'Prod_Module_command.php',
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
            // Handle select all checkbox
            $('#select-all').click(function() {
                $('.delete-checkbox').prop('checked', $(this).prop('checked'));
            });

            // Handle individual checkbox click
            $('#prod_module tbody').on('click', '.delete-checkbox', function() {
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
                            url: 'Prod_Module_command.php',
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

            // Handle click event of "Submit" button in the modal
            $('#addBtn').on('click', function(event) {
                event.preventDefault(); // Prevent default form submission
                // Retrieve the values from the modal inputs
                var product = $('#floatProduct').val();
                var partNo = $('#floatPartNo').val();
                var station = $('#floatStation').val();
                var activity = $('#floatActivity').val();
                var std = $('#floatStd').val();
                var name = "<?php echo $_SESSION['Name']; ?>";
                // Check if any required field is empty
                if (product === '' || partNo === '' || station === '' || std === '') {
                    // Display error message for empty fields
                    $('.alert').removeClass('d-none'); // Show the alert message
                    return; // Prevent further execution of the code
                }

                // Hide the alert message if all fields are filled
                $('.alert').addClass('d-none');
                // Perform an AJAX request to insert the data into the database
                $.ajax({
                    url: 'Prod_Module_command.php',
                    method: 'POST',
                    data: {
                        insert: true,
                        product: product,
                        partNo: partNo,
                        station: station,
                        activity: activity,
                        std: std,
                        name: name
                    },
                    success: function(response) {
                        // Handle the success response
                        console.log(response); // Check the value of the response
                        // Show success message using SweetAlert2
                        Swal.fire({
                            icon: 'success',
                            title: 'Added Successful',
                            text: 'The data has been added successfully!',
                            showConfirmButton: false,
                            timer: 1500
                        }).then(function() {
                            // Close the modal
                            $('#addCycletime').modal('hide');
                            // Remove the modal backdrop
                            $('body').removeClass('modal-open');
                            $('.modal-backdrop').remove();
                            // Reload the DataTable
                            table.ajax.reload();
                        });
                    },
                    error: function() {
                        // Handle the error case
                        console.log('Error occurred during data insertion');
                    }
                });
            });

        });
    </script>
    <!-- Modal -->
    <div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title" id="editModalLabel">Update Prod Module</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="id" id="id" value="">
                    <input type="hidden" name="trid" id="trid" value="">
                    <div class="row g-3 m-2">
                        <div class="col-sm form-floating">
                            <input type="date" class="form-control" id="dateField" name="date">
                            <label for="dateField">Date</label>
                        </div>
                        <div class="col-sm form-floating">
                            <input type="text" class="form-control" id="part_noField" name="part_no">
                            <label for="part_noField">Part Number</label>
                        </div>
                        <div class="col-sm form-floating">
                            <input type="number" class="form-control" id="batch_noField" name="batch_no">
                            <label for="batch_noField">Batch/Serial Number</label>
                        </div>
                        <div class="col-sm form-floating">
                            <input type="number" class="form-control" id="prod_noField" name="prod_no">
                            <label for="prod_noField">PO Number</label>
                        </div>
                    </div>
                    <div class="row g-3 m-2">
                        <div class="col-sm-6 form-floating">
                            <input type="text" class="form-control" id="descField" name="desc">
                            <label for="descField">Description</label>
                        </div>
                        <div class="col-sm form-floating">
                            <input type="text" class="form-control" id="stationsField" name="stations">
                            <label for="stationsField">Stations</label>
                        </div>
                        <div class="col-sm form-floating">
                            <input type="number" class="form-control" id="Act_CodeField" name="act_code">
                            <label for="Act_CodeField">Code</label>
                        </div>
                    </div>
                    <div class="row g-3 m-2">
                        <div class="col-sm form-floating">
                            <input type="text" class="form-control" id="nameField" name="Name">
                            <label for="nameField">Name</label>
                        </div>
                        <div class="col-sm form-floating">
                            <input type="number" class="form-control" id="emp_idField" name="emp_id">
                            <label for="emp_idField">Emp_ID</label>
                        </div>
                    </div>
                    <div class="row g-3 m-2">
                        <div class="col-sm form-floating">
                            <input type="datetime-local" class="form-control" id="Act_StartField" name="Act_Start" step="1">
                            <label for="Act_StartField">Act_Started</label>
                        </div>
                        <div class="col-sm form-floating">
                            <input type="datetime-local" class="form-control" id="Act_EndedField" name="Act_Ended" step="1">
                            <label for="Act_EndedField">Act_Ended</label>
                        </div>
                        <div class="col-sm form-floating">
                            <select class="form-select" id="wo_statusField" name="wo_status">
                                <option value="IDLE">IDLE</option>
                                <option value="IN-PROCESS">IN-PROCESS</option>
                                <option value="INDIRECT">INDIRECT</option>
                            </select>
                            <label for="wo_statusField">Status</label>
                        </div>
                    </div>
                    <div class="row g-3 m-2">
                        <div class="col-sm-3 form-floating">
                            <select class="form-select" id="productField" name="product">
                                <option value="JLP">JLP</option>
                                <option value="PNP">PNP</option>
                                <option value="PNP IO">PNP IO</option>
                                <option value="PNP TRANSFER">PNP TRANSFER</option>
                                <option value="OLB">OLB</option>
                                <option value="JTP">JTP</option>
                            </select>
                            <label for="productField">Product</label>
                        </div>
                        <div class="col-sm-3 form-floating">
                            <input type="number" class="form-select" id="build_percentField" name="build_percent">
                            <label for="build_percentField">Build%</label>
                        </div>
                        <div class="col-sm form-floating">
                            <input type="text" class="form-control" id="remarksField" name="remarks">
                            <label for="remarksField">Remarks</label>
                        </div>
                    </div>
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