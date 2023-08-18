<?php include '../ATS_Prod_Header.php'; ?>
<?php include '../PROD_navbar.php'; ?>
<!DOCTYPE html>
<html lang="en">

<head>
    <title>Update Cable CycleTime</title>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
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
    <div class="table-responsive my-2 px-2">
        <div class="py-2 text-end">
            <button type="button" class="btn btn-success" id="btnAdd" data-bs-toggle="modal" data-bs-target="#addCycletime">Add Cycle Time</button>
        </div>
        <table class="table table-hover" id="stdTable" style="width:100%">
            <thead class="table-dark fw-bold">
                <tr>
                    <th></th>
                    <th>ID</th>
                    <th>PRODUCT</th>
                    <th>PART NO.</th>
                    <th>STATION</th>
                    <th>ACTIVITY</th>
                    <th>STANDARD TIME</th>
                    <th>UPDATED BY</th>
                    <th>DATE UPDATED</th>
                    <th>ACTION</th>
                </tr>
            </thead>
            <tbody>
            </tbody>
        </table>
    </div>

    <script>
        $(document).ready(function() {
            var table = $('#stdTable').DataTable({
                ajax: {
                    url: 'AU_cycletime_command.php',
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
                    [7, 'desc']
                ],
                columns: [{
                        data: null,
                        targets: 0,
                        orderable: false,
                        render: function(data, type, row) {
                            return '<input type="checkbox" class="select-checkbox" data-id="' + row.ID + '">';
                        }
                    },
                    {
                        data: 'ID'
                    },
                    {
                        data: 'product'
                    },
                    {
                        data: 'part_no'
                    },
                    {
                        data: 'station'
                    },
                    {
                        data: 'Activity'
                    },
                    {
                        data: 'cycle_time'
                    },
                    {
                        data: 'updated_by',
                        render: function(data, type, row) {
                            return '<span style="font-weight: bold;">' + data + '</span>';
                        }
                    },
                    {
                        data: 'date_updated'
                    },
                    {
                        data: null,
                        orderable: false,
                        render: function(data, type, row) {
                            return '<button type="button" class="btn btn-primary mx-1 edit-btn" data-id="' + row.ID + '" data-bs-toggle="modal" data-bs-target="#staticBackdrop"><i class="fa-solid fa-pen-to-square"></i></button>' +
                                '<button class="btn btn-danger delete-btn" data-id="' + row.ID + '"><i class="fa-solid fa-trash"></i></button>';
                        }
                    }
                ]
            });
            //Handle edit button click
            $('#stdTable tbody').on('click', '.edit-btn', function() {
                selectedId = $(this).data('id'); // Store the id value in the variable
                var rowData = table.row($(this).closest('tr')).data(); // Get the data of the clicked row
                console.log(selectedId);
                $('#staticBackdrop').modal('show');

                // Set the values inside the form fields
                $('#floatingProduct').val(rowData.product);
                $('#floatingPartNo').val(rowData.part_no);
                $('#floatingStation').val(rowData.station);
                $('#floatingActivity').val(rowData.Activity);
                $('#floatingStd').val(rowData.cycle_time);
                $('#floatingUpdatedBy').val(rowData.updated_by);
                $('#floatingDateUpdated').val(rowData.date_updated);
            });
            // Handle submit button click inside the modal
            $('#submitBtn').on('click', function(event) {
                event.preventDefault(); // Prevent default form submission

                var id = selectedId;
                var updatedProduct = $('#floatingProduct').val();
                var updatedPartNo = $('#floatingPartNo').val();
                var updatedStation = $('#floatingStation').val();
                var updatedActivity = $('#floatingActivity').val();
                var updatedStd = $('#floatingStd').val();
                var name = "<?php echo $_SESSION['Name']; ?>";
                // Make an AJAX request to update the values in the database
                $.ajax({
                    url: 'AU_cycletime_command.php',
                    method: 'POST',
                    data: {
                        id: id,
                        product: updatedProduct,
                        partNo: updatedPartNo,
                        station: updatedStation,
                        activity: updatedActivity,
                        std: updatedStd,
                        update: true,
                        name: name
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
            $('#stdTable tbody').on('click', '.delete-btn', function() {
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
                            url: 'AU_cycletime_command.php',
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
                    url: 'AU_cycletime_command.php',
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
                            // Manually trigger the dismissal of the modal
                            document.getElementById('dismissModalBtn').click();
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


    <!-- Add Modal -->
    <div class="modal fade" id="addCycletime" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="addCycletimeLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-success text-white">
                    <h1 class="modal-title fs-5" id="addCycletimeLabel">ADD STANDARD TIME</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="alert alert-warning d-flex align-items-center mx-2 my-1 d-none" role="alert">
                    <i class="bi bi-exclamation-triangle-fill me-2"></i>
                    <div>
                        Please fill in all required fields.
                    </div>
                </div>
                <div class="modal-body">
                    <div class="form-floating mb-2">
                        <select class="form-select" id="floatProduct" required>
                            <option selected value="">-- Select Product --</option>
                            <option value="JLP">JLP</option>
                            <option value="PNP">PNP</option>
                            <option value="OLB">OLB</option>
                            <option value="JTP">JTP</option>
                            <option value="SPARES">SPARES</option>
                            <option value="TERADYNE">TERADYNE</option>
                        </select>
                        <label for="floatProduct">Product</label>
                    </div>

                    <div class="form-floating mb-2">
                        <input type="number" class="form-control" id="floatPartNo" placeholder="Part No" required>
                        <label for="floatPartNo">Part No</label>
                    </div>
                    <div class="form-floating mb-2">
                        <select class="form-select" id="floatStation" required>
                            <option selected value="">-- Select Station --</option>
                            <option value="WIRE/TUBE CUTTING">WIRE/TUBE CUTTING</option>
                            <option value="WIRE STRIPPING">WIRE STRIPPING</option>
                            <option value="TERMINAL CRIMPING">TERMINAL CRIMPING</option>
                            <option value="IPQC">IPQC</option>
                            <option value="PRE-BLOCKING">PRE-BLOCKING</option>
                            <option value="SOLDERING">SOLDERING</option>
                            <option value="MOLDING">MOLDING</option>
                            <option value="WIRE HARNESSING">WIRE HARNESSING</option>
                            <option value="TAPING">TAPING</option>
                            <option value="FINAL ASSEMBLY">FINAL ASSEMBLY</option>
                            <option value="HEAT SHRINKING">HEAT SHRINKING</option>
                            <option value="LABELLING">LABELLING</option>
                            <option value="TESTING">TESTING</option>
                            <option value="VISUAL INSPECTION">VISUAL INSPECTION</option>
                            <option value="OQA">OQA</option>
                            <option value="FG TRANSACTION">FG TRANSACTION</option>
                            <option value="PACKAGING">PACKAGING</option>
                        </select>
                        <label for="floatStation">Station</label>
                    </div>
                    <div class="form-floating mb-2">
                        <input type="text" class="form-control" id="floatActivity" placeholder="Activity" required>
                        <label for="floatActivity">Activity</label>
                    </div>
                    <div class="form-floating mb-2">
                        <input type="number" class="form-control" id="floatStd" placeholder="Standard Time" required>
                        <label for="floatStd">Standard Time(Mins)</label>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" id="dismissModalBtn">Close</button>
                    <button type="button" id="addBtn" class="btn btn-success">Submit</button>
                </div>
            </div>
        </div>
    </div>
    <!-- Edit Modal -->
    <div class="modal fade" id="staticBackdrop" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h1 class="modal-title fs-5" id="staticBackdropLabel">EDIT STANDARD TIME</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="form-floating mb-2">
                        <input type="text" class="form-control" id="floatingProduct" placeholder="Product">
                        <label for="floatingProduct">Product</label>
                    </div>
                    <div class="form-floating mb-2">
                        <input type="number" class="form-control" id="floatingPartNo" placeholder="Part No">
                        <label for="floatingPartNo">Part No</label>
                    </div>
                    <div class="form-floating mb-2">
                        <select class="form-select" id="floatingStation">
                            <option value="WIRE/TUBE CUTTING">WIRE/TUBE CUTTING</option>
                            <option value="WIRE STRIPPING">WIRE STRIPPING</option>
                            <option value="TERMINAL CRIMPING">TERMINAL CRIMPING</option>
                            <option value="IPQC">IPQC</option>
                            <option value="PRE-BLOCKING">PRE-BLOCKING</option>
                            <option value="SOLDERING">SOLDERING</option>
                            <option value="MOLDING">MOLDING</option>
                            <option value="WIRE HARNESSING">WIRE HARNESSING</option>
                            <option value="TAPING">TAPING</option>
                            <option value="FINAL ASSEMBLY">FINAL ASSEMBLY</option>
                            <option value="HEAT SHRINKING">HEAT SHRINKING</option>
                            <option value="LABELLING">LABELLING</option>
                            <option value="TESTING">TESTING</option>
                            <option value="VISUAL INSPECTION">VISUAL INSPECTION</option>
                            <option value="OQA">OQA</option>
                            <option value="FG TRANSACTION">FG TRANSACTION</option>
                            <option value="PACKAGING">PACKAGING</option>
                        </select>
                        <label for="floatingStation">Select Station</label>
                    </div>
                    <div class="form-floating mb-2">
                        <input type="text" class="form-control" id="floatingActivity" placeholder="Activity">
                        <label for="floatingActivity">Activity</label>
                    </div>
                    <div class="form-floating mb-2">
                        <input type="number" class="form-control" id="floatingStd" placeholder="Standard Time">
                        <label for="floatingStd">Standard Time(Mins)</label>
                    </div>
                    <div class="form-floating mb-2">
                        <input type="text" class="form-control" id="floatingUpdatedBy" placeholder="Updated By">
                        <label for="floatingUpdatedBy">Updated By</label>
                    </div>
                    <div class="form-floating mb-2">
                        <input type="date" class="form-control" id="floatingDateUpdated" placeholder="Date Updated">
                        <label for="floatingDateUpdated">Date Updated</label>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="button" id="submitBtn" class="btn btn-primary">Submit</button>
                </div>
            </div>
        </div>
    </div>

</body>

</html>