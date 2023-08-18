<?php include 'ATS_Prod_Header.php'; ?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Barcode Generator</title>
</head>

<body>
    <div class="container">
        <h1>Barcode Generator</h1>
        <form action="" method="post" class="row g-3">
            <div class="col-md-6">
                <label for="employee_id" class="form-label">Employee ID:</label>
                <input type="text" id="employee_id" name="employee_id" class="form-control" required>
            </div>
            <div class="col-md-6">
                <label for="password" class="form-label">Password:</label>
                <input type="password" id="password" name="password" class="form-control" required>
            </div>
            <div class="col-12">
                <button type="submit" class="btn btn-primary">Generate Barcode</button>
            </div>
        </form>
    </div>
</body>

</html>
<?php

use Picqer\Barcode\BarcodeGeneratorPNG;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['employee_id']) && isset($_POST['password'])) {
        $employee_id = $_POST['employee_id'];
        $password = $_POST['password'];
        // Perform validation against your authentication system or database
        $valid_credentials = validateEmployeeCredentials($employee_id, $password);

        if ($valid_credentials) {
            // Concatenate employee ID and password
            $data = $employee_id;
            // Generate barcode
            require_once '../vendor/autoload.php';
            $generator = new BarcodeGeneratorPNG();
            $barcode = $generator->getBarcode($data, $generator::TYPE_CODE_128, 5, 200);
            // Create a unique file name based on timestamp
            $timestamp = time();
            $file_name = 'barcode_' . $timestamp . '.png';

            // Save barcode as PNG file in the barcodes folder
            $file_path = $_SERVER['DOCUMENT_ROOT'] . '/ATS/ATSPROD_PORTAL/assets/images/barcodes/' . $file_name;
            file_put_contents($file_path, $barcode);

            // Create an image resource from the barcode file
            $barcode_image = imagecreatefrompng($file_path);

            // Set the font size and color for the text
            $font_size = 12;
            $text_color = imagecolorallocate($barcode_image, 0, 0, 0); // Black color

            // Calculate the dimensions of the barcode image
            $barcode_width = imagesx($barcode_image);
            $barcode_height = imagesy($barcode_image);

            // Calculate the position to place the text
            $text_width = imagefontwidth(4) * strlen($data);
            $text_x = ($barcode_width - $text_width) / 2; // Center the text horizontally
            $text_y = $barcode_height + 12; // Place the text below the barcode

            // Create a blank white image to hold the barcode and text
            $combined_image = imagecreatetruecolor($barcode_width, $barcode_height + $font_size + 20);

            // Set the background color of the combined image to white
            $white = imagecolorallocate($combined_image, 255, 255, 255);
            imagefill($combined_image, 0, 0, $white);

            // Copy the barcode onto the combined image
            imagecopy($combined_image, $barcode_image, 0, 0, 0, 0, $barcode_width, $barcode_height);

            // Add the text to the combined image
            imagestring($combined_image, 4, $text_x, $text_y, $data, $text_color);

            // Save the modified barcode image with the text
            imagepng($combined_image, $file_path);

            // Display the barcode and data
            echo '<div class="container my-1">';
            echo '<img src="/ATS/ATSPROD_PORTAL/assets/images/barcodes/' . $file_name . '" width="450" height="200">';

            // Provide download link for the barcode
            echo '<a href="/ATS/ATSPROD_PORTAL/assets/images/barcodes/' . $file_name . '" download>Download Barcode</a></div>';

            // Free up memory by destroying the image resources
            imagedestroy($barcode_image);
            imagedestroy($combined_image);
        } else {
            echo 'Invalid credentials. Please try again.';
        }
    }
}



function validateEmployeeCredentials($employee_id, $password)
{

    $host = "localhost";
    $user = "root";
    $pw = "";
    $dbname = "ewip";

    $conn = new mysqli($host, $user, $pw, $dbname);

    if (!$conn) {
        die('Database connection error: ' . mysqli_connect_error());
    }

    // Query the database to validate employee credentials
    $query = "SELECT COUNT(*) AS count FROM user WHERE username = '$employee_id' AND password = '$password'";
    $result = mysqli_query($conn, $query);

    if ($result) {
        $row = mysqli_fetch_assoc($result);
        $count = $row['count'];
        return $count > 0;
    } else {
        mysqli_error($conn);
    }
    return false;
}
