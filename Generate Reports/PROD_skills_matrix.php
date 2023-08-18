<?php include '../ATS_Prod_Header.php'; ?>
<?php include '../PROD_navbar.php'; ?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Production Skills Matrix Main</title>
</head>

<body>
    <div class="position-relative m-2">
        <table class="table table-bordered table-sm border-dark table-hover">
            <thead class="sticky-top" id="stickyHeader">
                <tr class="text-center">
                    <th class="text-bg-dark" colspan="4">TECHNICIANS</th>
                    <th class="bg-primary-subtle" colspan="11">JLP</th>
                    <th class="bg-info-subtle" colspan="2">MATRIX</th>
                    <th class="bg-info" colspan="3">OLB</th>
                    <th class="bg-secondary" colspan="2">TEST</th>
                </tr>
                <tr class="text-center">
                    <th class="text-bg-dark">No.</th>
                    <th class="text-bg-dark">NAME</th>
                    <th class="text-bg-dark">EMP ID</th>
                    <th class="text-bg-dark">TECH LEVEL</th>
                    <th class="text-black bg-primary-subtle">CDA</th>
                    <th class="text-black bg-primary-subtle">CDM</th>
                    <th class="text-black bg-primary-subtle">TSL</th>
                    <th class="text-black bg-primary-subtle">FA</th>
                    <th class="text-black bg-primary-subtle">TXP</th>
                    <th class="text-black bg-primary-subtle">AC</th>
                    <th class="text-black bg-primary-subtle">FC</th>
                    <th class="text-black bg-primary-subtle">MTP</th>
                    <th class="text-black bg-primary-subtle">ION</th>
                    <th class="text-black bg-primary-subtle">FLIP</th>
                    <th class="text-black bg-primary-subtle">INT</th>
                    <th class="text-black bg-info-subtle">SUB ASSY</th>
                    <th class="text-black bg-info-subtle">INT</th>
                    <th class="text-black bg-info">OLB MAIN</th>
                    <th class="text-black bg-info">ABLP</th>
                    <th class="text-black bg-info">F-INT</th>
                    <th class="text-black bg-secondary">SUB TEST</th>
                    <th class="text-black bg-secondary">F-TEST</th>
                </tr>
            </thead>
            <tbody>
                <?php
                // Query the database to retrieve data
                $matrix_query = "SELECT * FROM prod_skills_matrix";
                $matrix_result = mysqli_query($conn, $matrix_query);
                $lvl_query = "SELECT TECH_LVL, COUNT(TECH_LVL) AS LVL FROM `prod_skills_matrix` GROUP BY TECH_LVL ASC;";
                $lvl_result = mysqli_query($conn, $lvl_query);
                // Check if the query was successful
                if ($matrix_result && $lvl_query) {

                    // Generate the table rows dynamically
                    while ($row = mysqli_fetch_assoc($matrix_result)) {
                        // Increment the count based on the technician level
                        $technicianLevel = $row['TECH_LVL'];
                        $number = filter_var($technicianLevel, FILTER_SANITIZE_NUMBER_INT);
                        $trimmedNumber = ($number !== false) ? intval($number) : '';

                        echo "<tr class='text-center'>";
                        echo "<td>" . $row['ID'] . "</td>";
                        echo "<td>" . $row['Name'] . "</td>";
                        echo "<td>" . $row['Emp_ID'] . "</td>";

                        echo "<td contenteditable='true' class='" . (($trimmedNumber == 1) ? 'text-bg-warning bg-gradient' : (($trimmedNumber == 2) ? 'text-bg-primary bg-gradient' : (($trimmedNumber == 3) ? 'text-bg-success bg-gradient' : 'text-bg-secondary bg-gradient'))) . "'>" . $row['TECH_LVL'] . "</td>";
                        echo "<td class='bg-primary-subtle' contenteditable='true'>" . $row['CDA'] . "</td>";
                        echo "<td class='bg-primary-subtle' contenteditable='true'>" . $row['CDM'] . "</td>";
                        echo "<td class='bg-primary-subtle' contenteditable='true'>" . $row['TSL'] . "</td>";
                        echo "<td class='bg-primary-subtle' contenteditable='true'>" . $row['FA'] . "</td>";
                        echo "<td class='bg-primary-subtle' contenteditable='true'>" . $row['TXP'] . "</td>";
                        echo "<td class='bg-primary-subtle' contenteditable='true'>" . $row['AC'] . "</td>";
                        echo "<td class='bg-primary-subtle' contenteditable='true'>" . $row['FC'] . "</td>";
                        echo "<td class='bg-primary-subtle' contenteditable='true'>" . $row['MTP'] . "</td>";
                        echo "<td class='bg-primary-subtle' contenteditable='true'>" . $row['ION'] . "</td>";
                        echo "<td class='bg-primary-subtle' contenteditable='true'>" . $row['FLIP'] . "</td>";
                        echo "<td class='bg-primary-subtle' contenteditable='true'>" . $row['INTEGRATION'] . "</td>";
                        echo "<td class='text-black bg-info-subtle' contenteditable='true'>" . $row['PNP_SUB_ASSY'] . "</td>";
                        echo "<td class='text-black bg-info-subtle' contenteditable='true'>" . $row['PNP_INT'] . "</td>";
                        echo "<td class='text-black bg-info'contenteditable='true'>" . $row['OLB_MAIN'] . "</td>";
                        echo "<td class='text-black bg-info'contenteditable='true'>" . $row['ABLP'] . "</td>";
                        echo "<td class='text-black bg-info'contenteditable='true'>" . $row['OLB_F_INT'] . "</td>";
                        echo "<td class='text-black bg-secondary' contenteditable='true'>" . $row['SUB_TEST'] . "</td>";
                        echo "<td class='text-black bg-secondary' contenteditable='true'>" . $row['FINAL_TEST'] . "</td>";
                        echo "</tr>";
                    }
                    mysqli_free_result($matrix_result);
                } else {
                    // Handle the case when the query fails
                    echo "Error: " . mysqli_error($conn);
                }
                ?>
            </tbody>
            <tfoot>
                <tr>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th class="text-center">TARGET</th>
                </tr>
            </tfoot>
        </table>
        <?php
        // Initialize an array to store the level counts
        $levelCounts = array();

        // Fetch the results and store the counts in the array
        while ($row = mysqli_fetch_assoc($lvl_result)) {
            $levelCounts[$row['TECH_LVL']] = $row['LVL'];
        } ?>
        <!-- Display the level counts -->
        <div class='row text-start mx-0'>
            <div class="col">
                <span class='badge text-bg-warning fs-4'>LEVEL 1: <?php echo $levelCounts['LVL 1']; ?></span>
                <span class='badge text-bg-primary fs-4'>LEVEL 2: <?php echo $levelCounts['LVL 2']; ?></span>
                <span class='badge text-bg-success fs-4'>LEVEL 3: <?php echo $levelCounts['LVL 3']; ?></span>
            </div>
            <div class="col">
                <button class="btn btn-success float-end m-2" id="saveChangesBtn">Save Changes</button>
            </div>
        </div>
    </div>

    <script>
        $(document).ready(function() {
            // Disable the save button initially
            $("#saveChangesBtn").prop("disabled", true);
            // Add event listener for saving changes
            $("#saveChangesBtn").click(function() {
                // Create an array to store the edited data
                var editedData = [];

                // Iterate through each table row
                $("tbody tr").each(function() {
                    var rowData = {};
                    var row = $(this);

                    // Get the values from the editable cells
                    rowData.id = row.find("td:eq(0)").text();
                    rowData.techLevel = row.find("td:eq(3)").text();
                    rowData.cda = row.find("td:eq(4)").text();
                    rowData.cdm = row.find("td:eq(5)").text();
                    rowData.tsl = row.find("td:eq(6)").text();
                    rowData.fa = row.find("td:eq(7)").text();
                    rowData.txp = row.find("td:eq(8)").text();
                    rowData.ac = row.find("td:eq(9)").text();
                    rowData.fc = row.find("td:eq(10)").text();
                    rowData.mtp = row.find("td:eq(11)").text();
                    rowData.ion = row.find("td:eq(12)").text();
                    rowData.flip = row.find("td:eq(13)").text();
                    rowData.integration = row.find("td:eq(14)").text();
                    rowData.pnpSubAssy = row.find("td:eq(15)").text();
                    rowData.pnpInt = row.find("td:eq(16)").text();
                    rowData.olbMain = row.find("td:eq(17)").text();
                    rowData.ablp = row.find("td:eq(18)").text();
                    rowData.olbFInt = row.find("td:eq(19)").text();
                    rowData.subTest = row.find("td:eq(20)").text();
                    rowData.finalTest = row.find("td:eq(21)").text();

                    // Add the row data to the array
                    editedData.push(rowData);
                });
                // Check if any data has been edited
                if (editedData.length > 0) {
                    // Send the edited data to the PHP script using AJAX
                    $.ajax({
                        url: "PROD_skills_matrix_command.php",
                        method: "POST",
                        data: {
                            data: editedData
                        },
                        success: function(response) {
                            // Handle the success response
                            console.log(response);
                            alert("Data updated successfully!");
                        },
                        error: function(xhr, status, error) {
                            // Handle the error response
                            console.log(xhr.responseText);
                            alert("Error: " + xhr.responseText);
                        }
                    });
                } else {
                    alert("No changes to save.");
                }
            });

            // Add event listener for detecting changes in the table cells
            $("tbody td").on("input", function() {
                // Enable the save button when any cell is edited
                $("#saveChangesBtn").prop("disabled", false);
            });
        });
        var navbarDropdownElement = document.querySelector('#navbarDropdownToggle');
        var imageDropdownElement = document.querySelector('#imageDropdownToggle');
        var headerElement = document.getElementById('stickyHeader');

        navbarDropdownElement.addEventListener('show.bs.dropdown', function() {
            headerElement.classList.remove('sticky-top');
        });

        navbarDropdownElement.addEventListener('hide.bs.dropdown', function() {
            headerElement.classList.add('sticky-top');
        });

        imageDropdownElement.addEventListener('show.bs.dropdown', function() {
            headerElement.classList.remove('sticky-top');
        });

        imageDropdownElement.addEventListener('hide.bs.dropdown', function() {
            headerElement.classList.add('sticky-top');
        });
    </script>
</body>

</html>