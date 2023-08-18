<?php

// initialize variables

$id = 0;
$update = false;

if (isset($_POST['save'])) {
	if (empty($_POST['wo_quantity']) || empty($_POST['part_no']) || empty($_POST['prod_no']) || empty($_POST['description']) || empty($_POST['remarks']) || empty($_POST['module']) || empty($_POST['for_station']) || empty($_POST['tcd']) || empty($_POST['prioritization'])) {

		$_SESSION['message'] = "Cannot be empty!";
		$_SESSION['message_code'] = "warning";
		header('location: add_wo.php');
	} else {
		$status = "New";
		$wo_quantity = $_POST['wo_quantity'];
		$part_no = $_POST['part_no'];
		$prod_no = $_POST['prod_no'];
		$description = $_POST['description'];
		$remarks = $_POST['remarks'];
		$module = $_POST['module'];
		$for_station = $_POST['for_station'];
		$planner = $_POST['planner'];
		$dept = $_POST['dept'];
		$tcd = $_POST['tcd'];
		$terminal = $_POST['terminal_no'];
		$prioritization = $_POST['prioritization'];
		$cycle_time = "";
		mysqli_query($dbconnect, "insert into wo (wo_id,wo_date,wo_quantity,part_no,prod_no,description,remarks,module,terminal,prioritization,for_station,TCD,cycle_time,planner,status,dept) 
        Values
        ('',now(),'$wo_quantity','$part_no','$prod_no','$description','$remarks','$module','$terminal','$prioritization','$for_station','$tcd','$cycle_time','$planner','$status','$dept')");
		$_SESSION['message'] = "Work Order Added";
		$_SESSION['message_code'] = "success";
		header('location: add_wo.php');
	}
}

if (isset($_GET['delete'])) {
	$id = $_GET['delete'];
	mysqli_query($dbconnect, "DELETE FROM wo WHERE wo_id=$id");
	$_SESSION['message'] = "Production Order Deleted!";
	$_SESSION['message_code'] = "success";
	header('location: add_wo.php');
}
if (isset($_POST['selected'])) {
	$selected = $_POST['selected'];
	// Loop through the selected IDs and delete the corresponding rows from the database
	foreach ($selected as $id) {
		$sql = "DELETE FROM wo WHERE wo_id=$id";
		mysqli_query($dbconnect, $sql);
	}
	echo count($selected) . " rows deleted successfully";
}




if (isset($_POST['update'])) {
	$id = $_POST['ID'];
	$wo_quantity = $_POST['wo_quantity'];
	$part_no = $_POST['part_no'];
	$prod_no = $_POST['prod_no'];
	$description = $_POST['description'];
	$remarks = $_POST['remarks'];
	$planner = $_POST['planner'];
	$for_station = $_POST['for_station'];
	$dept = $_POST['dept'];
	$module = $_POST['module'];
	$terminal = $_POST['terminal'];
	$tcd = $_POST['tcd'];

	mysqli_query($dbconnect, "UPDATE wo SET wo_quantity='$wo_quantity', part_no='$part_no', prod_no='$prod_no',TCD='$tcd',description='$description',remarks='$remarks',module='$module',for_station='$for_station',planner='$planner',dept='$dept'  WHERE wo_id=$id");
	$_SESSION['message'] = "Record Updated!";
	$_SESSION['message_code'] = "success";
	header('location: add_wo.php');
}



?>
 <?php
	if (isset($_POST["Import"])) {
		$planner = $_POST['planner'];
		$dept = $_POST['dept'];
		$status = "New";


		//$tcd = strtotime("+7 day");
		//echo date('M d, Y', $tcd);   

		echo $filename = $_FILES["file"]["tmp_name"];


		if ($_FILES["file"]["size"] > 0) {

			$file = fopen($filename, "r");
			while (($data = fgetcsv($file, 10000, ",")) !== FALSE) {

				//It will insert a row to our subject table from our csv file`
				$sql = "INSERT into wo (wo_id,wo_date,wo_quantity,part_no,prod_no,description,remarks,module,prioritization,terminal,for_station,TCD,cycle_time,planner,status,dept) 
			  values('',now(),'$data[0]','$data[1]','$data[2]','$data[3]','$data[4]','$data[5]','$data[6]','$data[7]','$data[8]','$data[9]','$data[10]','$planner','$status','$dept')";
				//we are using mysql_query function. it returns a resource on true else False on error
				$result = mysqli_query($dbconnect, $sql);
				if (!$result) {
					echo "<script type=\"text/javascript\">
							alert(\"Invalid File:Please Upload CSV File.\");
							window.location = \"add_wo.php\"
						</script>";
				}
			}
			fclose($file);
			//throws a message if data successfully imported to mysql database from excel file
			echo "<script type=\"text/javascript\">
						alert(\"CSV File has been successfully Imported.\");
						window.location = \"add_wo.php\"
					</script>";



			//close of connection
			mysqli_close($conn);
		}
	}
	?>