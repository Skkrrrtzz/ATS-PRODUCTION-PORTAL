<?php include_once 'db_connection.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

require __DIR__ . '/../vendor/autoload.php';

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $efficiency_to_insert = $_POST['efficiency'];
    $datefrom = $_POST['datefrom'];
    $dataURL = $_POST['dataURL'];

    try {
        // Check if a record already exists for the current date
        $checkStmt = $conn->prepare("SELECT id FROM efficiency_records WHERE record_date = ?");
        $checkStmt->bind_param('s', $datefrom);
        $checkStmt->execute();
        $result = $checkStmt->get_result();

        if ($result->num_rows == 0) { // No record exists for the current date
            // Prepare the SQL statement for insertion
            $insertStmt = $conn->prepare("INSERT INTO efficiency_records (record_date, operator_efficiency) VALUES (?, ?)");
            $insertStmt->bind_param('sd', $datefrom, $efficiency_to_insert);
            $insertStmt->execute();

            $outputFile = __DIR__ . '/assets/saved_cable_eff/efficiency_capture.png'; // Change the file path as needed

            $data = base64_decode(preg_replace('#^data:image/\w+;base64,#i', '', $dataURL));
            file_put_contents($outputFile, $data);
            // Send email with PHPMailer
            $mail = new PHPMailer(true);

            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com';  // Set your SMTP server
            $mail->SMTPAuth = true;
            $mail->Username = 'noreplyats1@gmail.com';
            $mail->Password = 'mxmppmodmskwwzhv';
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
            $mail->Port = 465;

            $mail->setFrom('noreplyats1@gmail.com', 'ATS Production Portal'); // Set sender details
            $mail->addAddress('kgajete@pimes.com.ph', 'Cable Supervisor'); // Set recipient details jlopez@pimes.com.ph
            $mail->addCC('kgajete@pimes.com.ph', 'Programmer');
            //Attachments
            $mail->addAttachment($outputFile, 'efficiency_capture.png'); // Attach the captured image
            $mail->isHTML(true);
            $mail->Subject = 'Cable Efficiency Report';
            $mail->Body = "The Cable efficiency value is: $efficiency_to_insert";

            $mail->send();
            // Create a response array
            $response = array(
                'status' => 'success',
                'message' => 'Efficiency record inserted successfully & Email sent successfully.'
            );
        } else {
            $response = array(
                'status' => 'error',
                'message' => 'Efficiency record for date ' . $datefrom . ' is already exists & Email will not send.'
            );
        }
        echo json_encode($response);
    } catch (Exception $e) {
        echo "Error: " . $e->getMessage();
    }
} else {
    echo "Invalid request";
}
