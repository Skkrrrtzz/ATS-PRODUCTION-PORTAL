<?php include 'ATS_Prod_Header.php'; ?>
<?php include 'PROD_navbar.php'; ?>
<?php include 'PROD_dashboard.php' ?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Shipment Status</title>
    <link rel="stylesheet" href="assets/DataTables/datatables.min.css">
    <script src="assets/DataTables/datatables.min.js"></script>
</head>

<body>
    <div class="table-responsive py-2">
        <table class="table table-bordered table-striped display compact" id="Shipment_status" style="width:100%;">
            <thead class="table-dark">
                <tr>
                    <th>ID</th>
                    <th>MACHINE</th>
                    <th>Batch No.</th>
                    <th>Shipment Date</th>
                    <th>Build Status</th>
                    <th>Remarks</th>
                </tr>
            </thead>
            <tbody>
            </tbody>
        </table>
    </div>
    <script>
        $(document).ready(function() {
            var tables = $('#Shipment_status').DataTable({
                ajax: {
                    url: 'Shipment_data.php',
                    method: 'GET',
                    dataSrc: ''
                },
                order: [
                    [2, 'desc']
                ],
                columnDefs: [{
                    targets: [1, 2],
                    className: 'fw-bold'
                }],
                columns: [{
                        data: 'ID'
                    },
                    {
                        data: 'product'
                    },
                    {
                        data: 'batch_no'
                    },
                    {
                        data: 'date_shipped'
                    },
                    {
                        data: 'Stations'
                    },
                    {
                        data: 'remarks'
                    }
                ]
            });
        });
    </script>
</body>

</html>