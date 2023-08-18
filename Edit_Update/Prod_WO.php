<?php include '../ATS_Prod_Header.php'; ?>
<?php include '../PROD_navbar.php'; ?>
<!DOCTYPE html>
<html lang="en">

<head>
    <title>Update Work Orders</title>
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
    <div class="table-responsive my-2 py-2">
        <!-- <div class="py-2 text-end">
            <button type="button" class="btn btn-success" id="btnAdd" data-bs-toggle="modal" data-bs-target="#addCycletime">Add Cycle Time</button>
        </div> -->
        <table class="table table-hover table-sm" id="woTable" style="width:100%">
            <thead class="table-dark fw-bold">
                <tr>
                    <th>ID</th>
                    <th>Date</th>
                    <th>Part No.</th>
                    <th>PO</th>
                    <th>Description</th>
                    <th>WO Qty.</th>
                    <th>Remarks</th>
                    <th>Module</th>
                    <th>Station</th>
                    <th>Code</th>
                    <th>TCD</th>
                    <th>ACD</th>
                    <th>Status</th>
                    <th>Updated By</th>
                    <th>Date Updated</th>
                    <th>FG</th>
                    <th>ACTION</th>
                </tr>
            </thead>
            <tbody>
            </tbody>
        </table>
    </div>

    <script>
        $(document).ready(function() {
            var table = $('#woTable').DataTable({
                ajax: {
                    url: 'WO_command.php',
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
                    [0, 'desc']
                ],
                columns: [{
                        data: 'wo_id'
                    },
                    {
                        data: 'wo_date'
                    },
                    {
                        data: 'part_no'
                    },
                    {
                        data: 'prod_no'
                    },
                    {
                        data: 'description'
                    },
                    {
                        data: 'wo_quantity'
                    },
                    {
                        data: 'remarks'
                    },
                    {
                        data: 'module'
                    },
                    {
                        data: 'for_station'
                    },
                    {
                        data: 'Act_Code'
                    },
                    {
                        data: 'TCD'
                    },
                    {
                        data: 'ACD'
                    },
                    {
                        data: 'status'
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
                        data: 'FG'
                    },
                    {
                        data: null,
                        orderable: false,
                        render: function(data, type, row) {
                            return '<a href="javascript:void();" class="text-primary edit-btn fs-5" data-id="' + row.wo_id + '" data-bs-toggle="modal" data-bs-target="#editModal"><i class="fa-solid fa-pen-to-square"></i></a>' + ' <a href="javascript:void();" class ="text-danger delete-btn fs-5" data-id= "' + row.wo_id + '" ><i class = "fa-solid fa-trash" ></i></a>';
                        }
                    }
                ]
            });
            //Handle edit button click
            $('#woTable tbody').on('click', '.edit-btn', function() {
                selectedId = $(this).data('id'); // Store the id value in the variable
                var rowData = table.row($(this).closest('tr')).data(); // Get the data of the clicked row
                console.log(selectedId);
                $('#editModal').modal('show');

                // Set the values inside the form fields
                $('#floatWoID').val(rowData.wo_id);
                $('#floatWoDate').val(rowData.wo_date);
                $('#floatPartNo').val(rowData.part_no);
                $('#floatProdNo').val(rowData.prod_no);
                $('#floatDesc').val(rowData.description);
                $('#floatModule').val(rowData.module);
                $('#floatQty').val(rowData.wo_quantity);
                $('#floatStation').val(rowData.for_station);
                $('#floatCode').val(rowData.Act_Code);
                $('#floatStatus').val(rowData.status);
                $('#floatTCD').val(rowData.TCD);
                $('#floatACD').val(rowData.ACD);
                $('#floatFG').val(rowData.FG);
                $('#floatDept').val(rowData.dept);
                $('#floatPlanner').val(rowData.planner);
                $('#floatUpdatedBy').val(rowData.updated_by);
                $('#floatDateUpdated').val(rowData.date_updated);
                $('#floatRemarks').val(rowData.remarks);

            });
            // Handle submit button click inside the modal
            $('#submitBtn').on('click', function(event) {
                event.preventDefault(); // Prevent default form submission

                var id = selectedId;
                var updatedWoDate = $('#floatWoDate').val();
                var updatedPartNo = $('#floatPartNo').val();
                var updatedProdNo = $('#floatProdNo').val();
                var updatedDesc = $('#floatDesc').val();
                var updatedModule = $('#floatModule').val();
                var updatedQty = $('#floatQty').val();
                var updatedStation = $('#floatStation').val();
                var updatedCode = $('#floatCode').val();
                var updatedStatus = $('#floatStatus').val();
                var updatedTCD = $('#floatTCD').val();
                var updatedACD = $('#floatACD').val();
                var updatedFG = $('#floatFG').val();
                var updatedDept = $('#floatDept').val();
                var updatedPlanner = $('#floatPlanner').val();
                var updatedUpdatedBy = $('#floatUpdatedBy').val();
                var updatedDateUpdated = $('#floatDateUpdated').val();
                var updatedRemarks = $('#floatRemarks').val();
                // Make an AJAX request to update the values in the database
                $.ajax({
                    url: 'WO_command.php',
                    method: 'POST',
                    data: {
                        Wo_ID: id,
                        Wo_Date: updatedWoDate,
                        PartNo: updatedPartNo,
                        ProdNo: updatedProdNo,
                        Description: updatedDesc,
                        Module: updatedModule,
                        Wo_quantity: updatedQty,
                        Station: updatedStation,
                        Code: updatedCode,
                        Status: updatedStatus,
                        TCD: updatedTCD,
                        ACD: updatedACD,
                        FG: updatedFG,
                        Dept: updatedDept,
                        Planner: updatedPlanner,
                        Updated_By: updatedUpdatedBy,
                        Date_Updated: updatedDateUpdated,
                        Remarks: updatedRemarks,
                        update: true,
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
            $('#woTable tbody').on('click', '.delete-btn', function() {
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
                            url: 'WO_command.php',
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
                    url: 'WO_command.php',
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


    <!-- Update Modal -->
    <div class="modal fade" id="editModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-success text-white">
                    <h1 class="modal-title fs-5" id="editModalLabel">EDIT WO DATA</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="alert alert-warning d-flex align-items-center mx-2 my-1 d-none" role="alert">
                    <i class="bi bi-exclamation-triangle-fill me-2"></i>
                    <div>
                        Please fill in all required fields.
                    </div>
                </div>
                <div class="modal-body">
                    <div class="row g-3 m-2">
                        <div class="col-sm form-floating">
                            <input type="number" class="form-control" id="floatWoID" placeholder="WO ID" value="" readonly>
                            <label for="floatWoID">WO ID</label>
                        </div>
                        <div class="col-sm form-floating">
                            <input type="datetime-local" class="form-control" id="floatWoDate" placeholder="Date" value="" readonly>
                            <label for="floatWoDate">Date</label>
                        </div>
                        <div class="col-sm form-floating">
                            <input type="number" class="form-control" id="floatPartNo" placeholder="Part No" value="" required>
                            <label for="floatPartNo">Part No</label>
                        </div>
                        <div class="col-sm form-floating">
                            <input type="text" class="form-control" id="floatProdNo" placeholder="PO No" value="" required>
                            <label for="floatProdNo">PO No</label>
                        </div>
                    </div>
                    <div class="row g-3 m-2">
                        <div class="col-sm-7 form-floating">
                            <input type="text" class="form-control" id="floatDesc" placeholder="Description" value="">
                            <label for="floatDesc">Description</label>
                        </div>
                        <div class="col-sm-3 form-floating">
                            <select class="form-select" id="floatModule" required>
                                <option selected value="">Select Product</option>
                                <option value="18203CH">JLP</option>
                                <option value="18204CH">PNP</option>
                                <option value="18207CH">OLB</option>
                                <option value="18201CH">JTP</option>
                                <option value="1820CH">SPARES</option>
                                <option value="0720TN">TERADYNE</option>
                                <option value="1810CH">SWAP</option>
                            </select>
                            <label for="floatModule">Module</label>
                        </div>
                        <div class="col-sm form-floating">
                            <input type="number" class="form-control" id="floatQty" placeholder="Qty" value="" required>
                            <label for="floatQty">Qty.</label>
                        </div>
                    </div>
                    <div class="row g-3 m-2">
                        <div class="col-sm form-floating">
                            <select class="form-select" id="floatStation" required>
                                <option selected value="">Select Station</option>
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
                        <div class="col-sm form-floating">
                            <select class="form-select" id="floatCode" required>
                                <option selected value="">Select Code</option>
                                <option value="200">200</option>
                                <option value="201">201</option>
                                <option value="202">202</option>
                                <option value="203">203</option>
                                <option value="204">204</option>
                                <option value="205">205</option>
                                <option value="206">206</option>
                                <option value="207">207</option>
                                <option value="208">208</option>
                                <option value="209">209</option>
                                <option value="210">210</option>
                                <option value="211">211</option>
                                <option value="301">301</option>
                                <option value="302">302</option>
                                <option value="303">303</option>
                                <option value="304">304</option>
                                <option value="305">305</option>
                                <option value="306">306</option>
                                <option value="307">307</option>
                                <option value="309">309</option>
                                <option value="310">310</option>
                                <option value="311">311</option>
                                <option value="312">312</option>
                                <option value="313">313</option>
                            </select>
                            <label for="floatCode">Code</label>
                        </div>
                        <div class="col-sm form-floating">
                            <select class="form-select" id="floatStatus" required>
                                <option selected value="">Select Status</option>
                                <option value="IDLE">IDLE</option>
                                <option value="IN-PROCESS">IN-PROCESS</option>
                            </select>
                            <label for="floatStatus">Status</label>
                        </div>
                    </div>
                    <div class="row g-3 m-2">
                        <div class="col-sm form-floating">
                            <input type="date" class="form-control" id="floatTCD" placeholder="TCD" value="" required>
                            <label for="floatTCD">TCD</label>
                        </div>
                        <div class="col-sm form-floating">
                            <input type="date" class="form-control" id="floatACD" placeholder="ACD" value="" required>
                            <label for="floatACD">ACD</label>
                        </div>
                        <div class="col-sm form-floating">
                            <select class="form-select" id="floatFG" required>
                                <option value="Yes">Yes</option>
                                <option value="'No'">'No'</option>
                            </select>
                            <label for="floatFG">FG</label>
                        </div>
                        <div class="col-sm form-floating">
                            <input type="text" class="form-control" id="floatDept" placeholder="Dept" value="" required>
                            <label for="floatDept">Dept</label>
                        </div>
                    </div>
                    <div class="row g-3 m-2">
                        <div class="col-sm form-floating">
                            <input type="text" class="form-control" id="floatPlanner" placeholder="Planner" required>
                            <label for="floatPlanner">Planner</label>
                        </div>
                        <div class="col-sm form-floating">
                            <input type="text" class="form-control" id="floatUpdatedBy" placeholder="Updated By" required>
                            <label for="floatUpdatedBy">Updated By</label>
                        </div>
                        <div class="col-sm-4 form-floating">
                            <input type="datetime-local" class="form-control" id="floatDateUpdated" placeholder="Date Updated" required>
                            <label for="floatDateUpdated">Date Updated</label>
                        </div>
                        <div class="form-floating mb-2">
                            <input type="text" class="form-control" id="floatRemarks" placeholder="Remarks" required>
                            <label for="floatRemarks">Remarks</label>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" id="dismissModalBtn">Close</button>
                        <button type="button" id="submitBtn" class="btn btn-success">Submit</button>
                    </div>
                </div>
            </div>
        </div>
        <!-- Edit Modal
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
        </div> -->

</body>

</html>