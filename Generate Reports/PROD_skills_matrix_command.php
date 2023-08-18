<?php
$conn = mysqli_connect("localhost", "root", "") or die("Could not connect");
mysqli_select_db($conn, "ewip") or die("Could not connect to database");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get the edited data from the AJAX request
    if (isset($_POST["data"])) {
        $editedData = $_POST["data"];
        // Iterate through the edited data and update the database
        foreach ($editedData as $row) {
            $id = $row["id"];
            $techLevel = $row["techLevel"];
            $cda = $row["cda"];
            $cdm = $row["cdm"];
            $tsl = $row["tsl"];
            $fa = $row["fa"];
            $txp = $row["txp"];
            $ac = $row["ac"];
            $fc = $row["fc"];
            $mtp = $row["mtp"];
            $ion = $row["ion"];
            $flip = $row["flip"];
            $integration = $row["integration"];
            $pnpSubAssy = $row["pnpSubAssy"];
            $pnpInt = $row["pnpInt"];
            $olbMain = $row["olbMain"];
            $ablp = $row["ablp"];
            $olbFInt = $row["olbFInt"];
            $subTest = $row["subTest"];
            $finalTest = $row["finalTest"];

            // Update the database with the edited data
            $query = "UPDATE prod_skills_matrix SET `TECH LEVEL`='$techLevel', CDA='$cda', CDM='$cdm', TSL='$tsl', FA='$fa', TXP='$txp', AC='$ac', FC='$fc', MTP='$mtp', ION='$ion', FLIP='$flip', INTEGRATION='$integration', `PNP SUB ASSY`='$pnpSubAssy', `PNP INT`='$pnpInt', `OLB MAIN`='$olbMain', ABLP='$ablp', `OLB F-INT`='$olbFInt', `SUB TEST`='$subTest', `FINAL TEST`='$finalTest' WHERE ID=$id";
            $result = mysqli_query($conn, $query);

            // Check if the query was successful
            if (!$result) {
                echo "Error updating record: " . mysqli_error($conn);
                // Handle the error as needed
                exit();
            }
        }

        // Send a success response
        echo "Data updated successfully!";
    } elseif (isset($_POST["data_cable"])) {
        $editedData_cable = $_POST["data_cable"];
        // Iterate through the edited data and update the database
        foreach ($editedData_cable as $row) {
            $id = $row["id"];
            $skillLevel = $row["skillLevel"];
            $mc = $row["mc"];
            $ms = $row["ms"];
            $mcp = $row["mcp"];
            $sawc = $row["sawc"];
            $msu = $row["msu"];
            $sol = $row["sol"];
            $mol = $row["mol"];
            $wh = $row["wh"];
            $fa = $row["fa"];
            $mco = $row["mco"];
            $label = $row["label"];
            $etest = $row["etest"];
            $vi = $row["vi"];
            $pb = $row["pb"];
            $tp = $row["tp"];

            // Update the database with the edited data
            $query = "UPDATE prod_skills_matrix_cable SET `SKILL_LVL`='$skillLevel', MCUTTING='$mc', MSTRIPPING='$ms', MCRIMPING='$mcp', SAWC='$sawc', MsU='$msu', SOLDERING='$sol', MOLDING='$mol', WHARNESS='$wh', FINALASSY='$fa', MCO='$mco', LABELLING='$label', `ETESTING`='$etest', `VI`='$vi', PB='$pb', `TAPING`='$tp' WHERE ID=$id";
            $result = mysqli_query($conn, $query);

            // Check if the query was successful
            if (!$result) {
                echo "Error updating record: " . mysqli_error($conn);
                // Handle the error as needed
                exit();
            }
        }
        // Send a success response
        echo "Data updated successfully!";
    } else {
        // Send an error response for invalid request
        echo "Invalid data!";
    }
} else {
    // Send an error response for invalid request method
    echo "Invalid request method!";
}

// Close the database connection
mysqli_close($conn);
