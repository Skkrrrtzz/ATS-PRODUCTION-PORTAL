<?php include '../ATS_Prod_Header.php'; ?>
<?php include '../PROD_navbar.php'; ?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Production Main DTR Editor</title>
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

        /* .fontsize {
            font-size: 13px;
        } */
    </style>
</head>

<body>
    <div class="table-responsive py-2">

        <button id="delete-selected-btn" class="btn btn-danger mx-1"><i class="fa-solid fa-circle-minus"></i> Delete Selected</button>
        <button type="button" class="btn btn-success" id="btnAdd" data-bs-toggle="modal" data-bs-target="#addDTR"><i class="fa-solid fa-circle-plus"></i> Add</button>
        <table id="prod_dtr" style="width:100%" class="table table-hover table-sm display compact">
            <thead class="table-dark fw-bold">
                <tr>
                    <th><input type="checkbox" id="select-all"></th>
                    <th>ID</th>
                    <th>Date</th>
                    <th>Part NO</th>
                    <th>Description</th>
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
                    <th>Std time</th>
                    <th>Remarks</th>
                    <th>Activity</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
            </tbody>
        </table>
    </div>
    <script>
        $(document).ready(function() {
            var table = $('#prod_dtr').DataTable({
                ajax: {
                    url: 'Prod_Dtr_command.php',
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
                columnDefs: [{
                    targets: [''],
                    className: "fontsize"
                }],
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
                        data: 'DATE'
                    },
                    {
                        data: 'Part_No'
                    },
                    {
                        data: 'description'
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
                        data: 'Code'
                    },
                    {
                        data: 'Act_Start'
                    },
                    {
                        data: 'Act_End'
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
                        data: 'cycle_time'
                    },
                    {
                        data: 'remarks'
                    },
                    {
                        data: 'Activity'
                    },
                    {
                        data: null,
                        orderable: false,
                        render: function(data, type, row) {
                            return '<a href="javascript:void();" class="text-primary edit-btn fs-4" data-id="' + row.ID + '" data-bs-toggle="modal" data-bs-target="#editModal"><i class="fa-solid fa-pen-to-square"></i></a>' + ' <a href="javascript:void();" class ="text-danger delete-btn fs-4" data-id= "' + row.ID + '" ><i class = "fa-solid fa-trash" ></i></a>';
                        }
                    }
                ]
            });
            //Handle edit button click
            $('#prod_dtr tbody').on('click', '.edit-btn', function() {
                selectedId = $(this).data('id'); // Store the id value in the variable
                var rowData = table.row($(this).closest('tr')).data(); // Get the data of the clicked row
                console.log(selectedId);
                $('#editModal').modal('show');

                // Set the values inside the form fields
                $('#dateField').val(rowData.DATE);
                $('#part_noField').val(rowData.Part_No);
                $('#descField').val(rowData.description);
                $('#prod_noField').val(rowData.Prod_Order_No);
                $('#batch_noField').val(rowData.batch_no);
                $('#nameField').val(rowData.Name);
                $('#emp_idField').val(rowData.Emp_ID);
                $('#stationsField').val(rowData.Stations);
                $('#Act_CodeField').val(rowData.Code);
                $('#Act_StartField').val(rowData.Act_Start);
                $('#Act_EndedField').val(rowData.Act_End);
                $('#wo_statusField').val(rowData.wo_status);
                $('#build_percentField').val(rowData.build_percent);
                $('#StdField').val(rowData.cycle_time);
                $('#remarksField').val(rowData.remarks);
                $('#ActivityField').val(rowData.Activity);

            });
            // Handle submit button click inside the modal
            $('#submitBtn').on('click', function(event) {
                event.preventDefault(); // Prevent default form submission

                var id = selectedId;
                var updateDate = $('#dateField').val();
                var updatePartNo = $('#part_noField').val();
                var updateDesc = $('#descField').val();
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
                var updateStd = $('#StdField').val();
                var updateRemarks = $('#remarksField').val();
                var updateAct = $('#ActivityField').val();
                // Make an AJAX request to update the values in the database
                $.ajax({
                    url: 'Prod_Dtr_command.php',
                    method: 'POST',
                    data: {
                        id: id,
                        date: updateDate,
                        part_no: updatePartNo,
                        description: updateDesc,
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
                        std: updateStd,
                        remarks: updateRemarks,
                        activity: updateAct,
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
            $('#prod_dtr tbody').on('click', '.delete-btn', function() {
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
                            url: 'Prod_Dtr_command.php',
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
            $('#prod_dtr tbody').on('click', '.delete-checkbox', function() {
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
                            url: 'Prod_Dtr_command.php',
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

                // Prevent default form submission
                event.preventDefault();

                // Retrieve the values from the modal inputs
                var InsertDate = $('#dateAddField').val();
                var InsertPartNo = $('#part_noAddField').val();
                var InsertDesc = $('#descAddField').val();
                var InsertPO = $('#prod_noAddField').val();
                var InsertBatch = $('#batch_noAddField').val();
                var InsertName = $('#nameAddField').val();
                var InsertEMP = $('#emp_idAddField').val();
                var InsertStations = $('#stationsAddField').val();
                var InsertCode = $('#Act_CodeAddField').val();
                var InsertStart = $('#Act_StartAddField').val();
                var InsertEnd = $('#Act_EndedAddField').val();
                var InsertWo = $('#wo_statusAddField').val();
                var InsertBuild = $('#build_percentAddField').val();
                var InsertStd = $('#StdAddField').val();
                var InsertRemarks = $('#remarksAddField').val();
                var InsertAct = $('#ActivityAddField').val();

                // Check if any required field is empty
                if (InsertDate === '' || InsertPartNo === '' || InsertDesc === '' || InsertPO === '' || InsertBatch === '' || InsertName === '' || InsertEMP === '' || InsertStations === '' || InsertCode === '' || InsertWo === '' || InsertBuild === '' || InsertStd === '' || InsertAct === '') {
                    // Display error message for empty fields
                    $('.alert').removeClass('d-none'); // Show the alert message
                    return; // Prevent further execution of the code
                }

                // Hide the alert message if all fields are filled
                $('.alert').addClass('d-none');

                // Perform an AJAX request to insert the data into the database
                $.ajax({
                    url: 'Prod_Dtr_command.php',
                    method: 'POST',
                    data: {
                        insert: true,
                        date: InsertDate,
                        part_no: InsertPartNo,
                        description: InsertDesc,
                        prod_no: InsertPO,
                        batch_no: InsertBatch,
                        name: InsertName,
                        emp_id: InsertEMP,
                        stations: InsertStations,
                        act_code: InsertCode,
                        act_start: InsertStart,
                        act_end: InsertEnd,
                        wo_status: InsertWo,
                        build_percent: InsertBuild,
                        std: InsertStd,
                        remarks: InsertRemarks,
                        activity: InsertAct
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
    <div class="modal fade" id="addDTR" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="addDTRLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-success text-white">
                    <h1 class="modal-title fs-5" id="addDTRLabel">ADD DTR</h1>
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
                            <input type="number" class="form-control" id="part_noAddField" name="part_no">
                            <label for="part_noAddField">Part Number</label>
                        </div>
                        <div class="col-sm form-floating">
                            <input type="number" class="form-control" id="prod_noAddField" name="prod_no">
                            <label for="prod_noAddField">PO Number</label>
                        </div>
                        <div class="col-sm form-floating">
                            <input type="number" class="form-control" id="batch_noAddField" name="batch_no">
                            <label for="batch_noAddField">Batch/Serial Number</label>
                        </div>
                    </div>
                    <div class="row g-3 m-2">
                        <div class="col-sm-6 form-floating">
                            <select class="form-select" id="descAddField" name="desc">
                                <option value="">Select Description</option>
                            </select>
                            <label for="descAddField">Description</label>
                        </div>
                        <div class="col-sm form-floating">
                            <input type="text" class="form-control" id="stationsAddField" name="stations">
                            <label for="stationsAddField">Stations</label>
                        </div>
                        <div class="col-sm form-floating">
                            <input type="number" class="form-control" id="Act_CodeAddField" name="act_code">
                            <label for="Act_CodeAddField">Code</label>
                        </div>
                    </div>
                    <div class="row g-3 m-2">
                        <div class="col-sm form-floating">
                            <select class="form-select" id="nameAddField" name="Name">
                                <option value="">Select Name</option>
                            </select>
                            <label for="nameAddField">Name</label>
                        </div>
                        <div class="col-sm form-floating">
                            <input type="number" class="form-control" id="emp_idAddField" name="emp_id">
                            <label for="emp_idAddField">EMP ID</label>
                        </div>
                    </div>
                    <div class="row g-3 m-2">
                        <div class="col-sm form-floating">
                            <input type="datetime-local" class="form-control" id="Act_StartAddField" name="Act_Start" step="1">
                            <label for="Act_StartAddField">Act Started</label>
                        </div>
                        <div class="col-sm form-floating">
                            <input type="datetime-local" class="form-control" id="Act_EndedAddField" name="Act_Ended" step="1">
                            <label for="Act_EndedAddField">Act Ended</label>
                        </div>
                        <div class="col-sm form-floating">
                            <select class="form-select" id="wo_statusAddField" name="wo_status">
                                <option value="IDLE">IDLE</option>
                                <option value="IN-PROCESS">IN-PROCESS</option>
                                <option value="INDIRECT">INDIRECT</option>
                            </select>
                            <label for="wo_statusAddField">Status</label>
                        </div>
                    </div>
                    <div class="row g-3 m-2">
                        <div class="col-sm form-floating">
                            <select class="form-select" id="ActivityAddField" name="activity">
                                <option value="SUB-ASSY">SUB-ASSY</option>
                                <option value="SUB-ASSY">SUB TEST</option>
                                <option value="FVI MODULE">FVI MODULE</option>
                                <option value="OQA FAC">OQA FAC</option>
                                <option value="FINAL INT">FINAL INT</option>
                                <option value="FINAL TEST">FINAL TEST</option>
                                <option value="Rework/Retest">Rework/Retest</option>
                                <option value="PARTS RECEIVED CHECKING">PARTS RECEIVED CHECKING</option>
                                <option value="BREAKTIME">BREAKTIME</option>
                                <option value="WAIT PART">WAIT PART</option>
                                <option value="DOCUMENT GENERATION (QIF)">DOCUMENT GENERATION (QIF)</option>
                                <option value="DRAWING/BOM/MPI/MTI VERIFICATION">DRAWING/BOM/MPI/MTI VERIFICATION</option>
                                <option value="TRAINING/MEETING/SEMINAR">TRAINING/MEETING/SEMINAR</option>
                                <option value="FACILITY DOWNTIME (INCLUDES IT)">FACILITY DOWNTIME (INCLUDES IT)</option>
                                <option value="PERSONAL NEEDS/TRIP TO CLINIC/HR/FINANCE">PERSONAL NEEDS/TRIP TO CLINIC/HR/FINANCE</option>
                                <option value="5S HOUSEKEEPING">5S HOUSEKEEPING</option>
                                <option value="SUPPORT TO OTHER GROUPS/SPECIAL PROJECTS">SUPPORT TO OTHER GROUPS/SPECIAL PROJECTS</option>
                                <option value="INVENTORY TAKING">INVENTORY TAKING</option>
                            </select>
                            <label for="ActivityAddField">Activity</label>
                        </div>
                        <div class="col-sm form-floating">
                            <select class="form-select" id="build_percentAddField" name="build_percent">
                                <option value="5">5</option>
                                <option value="10">10</option>
                                <option value="20">20</option>
                                <option value="30">30</option>
                                <option value="40">40</option>
                                <option value="50">50</option>
                                <option value="60">60</option>
                                <option value="70">70</option>
                                <option value="80">80</option>
                                <option value="90">90</option>
                                <option value="100">100</option>
                            </select>
                            <label for="build_percentAddField">Build%</label>
                        </div>
                        <div class="col-sm form-floating">
                            <input type="number" class="form-control" id="StdAddField" name="std">
                            <label for="StdAddField">Std Cycle Time</label>
                        </div>
                    </div>
                    <div class="row g-3 m-2">
                        <div class="col-sm form-floating">
                            <input type="text" class="form-control" id="remarksAddField" name="remarks">
                            <label for="remarksAddField">Remarks</label>
                        </div>
                    </div>
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
                    <h5 class="modal-title" id="editModalLabel">Update Prod Main DTR</h5>
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
                            <input type="number" class="form-control" id="part_noField" name="part_no">
                            <label for="part_noField">Part Number</label>
                        </div>
                        <div class="col-sm form-floating">
                            <input type="number" class="form-control" id="prod_noField" name="prod_no">
                            <label for="prod_noField">PO Number</label>
                        </div>
                        <div class="col-sm form-floating">
                            <input type="number" class="form-control" id="batch_noField" name="batch_no">
                            <label for="batch_noField">Batch/Serial Number</label>
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
                            <label for="emp_idField">EMP ID</label>
                        </div>
                    </div>
                    <div class="row g-3 m-2">
                        <div class="col-sm form-floating">
                            <input type="datetime-local" class="form-control" id="Act_StartField" name="Act_Start" step="1">
                            <label for="Act_StartField">Act Started</label>
                        </div>
                        <div class="col-sm form-floating">
                            <input type="datetime-local" class="form-control" id="Act_EndedField" name="Act_Ended" step="1">
                            <label for="Act_EndedField">Act Ended</label>
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
                        <div class="col-sm form-floating">
                            <select class="form-select" id="ActivityField" name="activity">
                                <option value="SUB-ASSY">SUB-ASSY</option>
                                <option value="SUB-ASSY">SUB TEST</option>
                                <option value="FVI MODULE">FVI MODULE</option>
                                <option value="OQA FAC">OQA FAC</option>
                                <option value="FINAL INT">FINAL INT</option>
                                <option value="FINAL TEST">FINAL TEST</option>
                                <option value="Rework/Retest">Rework/Retest</option>
                                <option value="PARTS RECEIVED CHECKING">PARTS RECEIVED CHECKING</option>
                                <option value="BREAKTIME">BREAKTIME</option>
                                <option value="WAIT PART">WAIT PART</option>
                                <option value="DOCUMENT GENERATION (QIF)">DOCUMENT GENERATION (QIF)</option>
                                <option value="DRAWING/BOM/MPI/MTI VERIFICATION">DRAWING/BOM/MPI/MTI VERIFICATION</option>
                                <option value="TRAINING/MEETING/SEMINAR">TRAINING/MEETING/SEMINAR</option>
                                <option value="FACILITY DOWNTIME (INCLUDES IT)">FACILITY DOWNTIME (INCLUDES IT)</option>
                                <option value="PERSONAL NEEDS/TRIP TO CLINIC/HR/FINANCE">PERSONAL NEEDS/TRIP TO CLINIC/HR/FINANCE</option>
                                <option value="5S HOUSEKEEPING">5S HOUSEKEEPING</option>
                                <option value="SUPPORT TO OTHER GROUPS/SPECIAL PROJECTS">SUPPORT TO OTHER GROUPS/SPECIAL PROJECTS</option>
                                <option value="INVENTORY TAKING">INVENTORY TAKING</option>
                            </select>
                            <label for="ActivityField">Activity</label>
                        </div>
                        <div class="col-sm form-floating">
                            <select class="form-select" id="build_percentField" name="build_percent">
                                <option value="5">5</option>
                                <option value="10">10</option>
                                <option value="20">20</option>
                                <option value="30">30</option>
                                <option value="40">40</option>
                                <option value="50">50</option>
                                <option value="60">60</option>
                                <option value="70">70</option>
                                <option value="80">80</option>
                                <option value="90">90</option>
                                <option value="100">100</option>
                            </select>
                            <label for="build_percentField">Build%</label>
                        </div>
                        <div class="col-sm form-floating">
                            <input type="number" class="form-control" id="StdField" name="std">
                            <label for="StdField">Std Cycle Time</label>
                        </div>
                    </div>
                    <div class="row g-3 m-2">
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
    <script>
        $(document).ready(function() {
            // AJAX request to fetch users data
            $.ajax({
                url: 'Prod_Dtr_command.php',
                type: 'POST',
                data: {
                    fetchUsers: true
                }, // Sending this parameter to indicate fetching users
                dataType: 'json',
                success: function(data) {
                    // Populate the select dropdown with users data
                    var selectDropdown = $('#nameAddField');
                    $.each(data, function(index, user) {
                        var option = $('<option></option>').attr('value', user.username).text(user.emp_name);
                        selectDropdown.append(option);
                    });
                },
                error: function(xhr, status, error) {
                    console.log('Error: ' + error);
                }
            });

            // Update the EMP ID field when a user is selected
            $('#nameAddField').on('change', function() {
                var selectedEmpID = $(this).val();
                $('#emp_idAddField').val(selectedEmpID);
            });
        });
        $(document).ready(function() {
            // AJAX request to fetch data from the database
            $.ajax({
                url: 'Prod_Dtr_command.php', // Replace 'fetch_data.php' with the PHP file where you'll handle the database query
                type: 'POST',
                data: {
                    dataType: 'description'
                }, // Sending this parameter to indicate fetching descriptions
                dataType: 'json',
                success: function(data) {
                    // Populate the select dropdown with descriptions and their part numbers
                    var selectDropdown = $('#descAddField');
                    $.each(data, function(index, item) {
                        var option = $('<option></option>').attr('value', item.part_no).text(item.description);
                        selectDropdown.append(option);
                    });
                },
                error: function(xhr, status, error) {
                    console.log('Error: ' + error);
                }
            });

            // Update the part number field when a user selects a description
            $('#descAddField').on('change', function() {
                var selectedPartno = $(this).val();
                $('#part_noAddField').val(selectedPartno);
            });
        });
    </script>
</body>

</html>