<?php include('header_login.php'); ?>

<?php // include('tech_log_activity_command.php'); ?>
<?php include('cable_supervisor_nav.php'); ?>
<?php
//session_start();

if(!isset($_SESSION['emp_id'])){
  header('Location:home.php');
 exit();
  }

$name=$_SESSION['name'];
$dept=$_SESSION['department'];
$emp_id=$_SESSION['emp_id'];
$quantity="";
$part_no="";
$prod_no="";
$description="";
$for_station="";
$station1="";
$station2="FG STORE";

//$dbconnect=mysqli_connect('localhost','root','','ewip');


?>

<!DOCTYPE html>
<html>

  <head>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Cable Performance</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
  <script src='https://kit.fontawesome.com/a076d05399.js' crossorigin='anonymous'></script>

  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
  <style>
  
table {
  border-collapse: collapse;
  width: 50%;
  float: none;
}

}

td {
  text-align: center;
  padding: 8px;

}

th{
  text-align: center;
  padding: 8px;
  background-color: Gray;
  color:white;
  width:;
}

tr:nth-child(even) {background-color: #f2f2f2;}

.btn {
  background-color: lightgreen;
  border: none;
  color: white;
  padding: 12px 30px;
  font-size: 16px;
  cursor: pointer;
}

/* Darker background on mouse-over */
.btn:hover {
  background-color: green;
}


/* Clear floats after the columns */
.row:after {
  content: "";
  display: table;
  clear: both;
}

</style>





  </head>


  <body>
  
  <center><h1>CABLE ASSY PRODUCTIVITY PERFORMANCE</h1>


<form method="POST">
<!--<input  type="hidden" name="fg_qty" value="<?php// echo $wo_quantity;?>">
    <input  type="hidden" name="wo_ID" value="<?php //echo $wo_id;?>"> -->
<label for="datefrom"><b>BETWEEN:</b></label>
<input  type="date" name="datefrom" value="">
<label for="dateto"><b>AND:</b></label>
<input  type="date" name="dateto" value="">
<!--<label for="ot_days"><b>No. OT Days:</b></label>
<input  type="number" name="ot_days" value="" required>
<label for="OT_HC"><b>OT HeadCounts:</b></label>
<input  type="number" name="OT_HC" value="" required><br><br> -->
<input  type="submit" id="filter" name="filter" value="View">
<input type='button' value='Print/Save' onclick='myApp.printTable()' />
<button id="btnExport" onclick="javascript:xport.toCSV('dtrsummary');">Export to Excel</button> 

<?PHP
$month="";
$year="";
$Dept="";
//$ot_days="";
//$OT_HC="";


$dbconnect=mysqli_connect('localhost','root','','ewip');

if(isset($_POST['filter'])){
  //$fg_qty=$_POST['fg_qty'];
  //$wo_ID=$_POST['wo_ID'];
$datefrom=$_POST['datefrom'];
$dateto=$_POST['dateto'];
$Dept="Cable Assy"; 
//$ot_days=$_POST['ot_days'];
//$OT_HC=$_POST['OT_HC'];






$sql_total_operator=mysqli_query($dbconnect,"SELECT count(username) as total_operator FROM user WHERE Department='$Dept' AND role='operator' AND emp_name!='operator'");
$row = mysqli_fetch_array($sql_total_operator);
  $total_operator=$row['total_operator'];


$sqlday01=mysqli_query($dbconnect,"SELECT * FROM dtr WHERE OT_day='No' AND Department='$Dept' AND Name !='' AND Day(DATE)='01' AND DATE BETWEEN '$datefrom' and '$dateto' GROUP BY Name ");
$sqlday02=mysqli_query($dbconnect,"SELECT * FROM dtr WHERE OT_day='No' AND Department='$Dept' AND Name !='' AND Day(DATE)='02' AND DATE BETWEEN '$datefrom' and '$dateto' GROUP BY Name ");
$sqlday03=mysqli_query($dbconnect,"SELECT * FROM dtr WHERE OT_day='No' AND Department='$Dept' AND Name !='' AND Day(DATE)='03' AND DATE BETWEEN '$datefrom' and '$dateto' GROUP BY Name ");
$sqlday04=mysqli_query($dbconnect,"SELECT * FROM dtr WHERE OT_day='No' AND Department='$Dept' AND Name !='' AND Day(DATE)='04' AND DATE BETWEEN '$datefrom' and '$dateto' GROUP BY Name");
$sqlday05=mysqli_query($dbconnect,"SELECT * FROM dtr WHERE OT_day='No' AND Department='$Dept' AND Name !='' AND Day(DATE)='05' AND DATE BETWEEN '$datefrom' and '$dateto' GROUP BY Name");
$sqlday06=mysqli_query($dbconnect,"SELECT * FROM dtr WHERE OT_day='No' AND Department='$Dept' AND Name !='' AND Day(DATE)='06' AND DATE BETWEEN '$datefrom' and '$dateto' GROUP BY Name");
$sqlday07=mysqli_query($dbconnect,"SELECT * FROM dtr WHERE OT_day='No' AND Department='$Dept' AND Name !='' AND Day(DATE)='07' AND DATE BETWEEN '$datefrom' and '$dateto' GROUP BY Name");
$sqlday08=mysqli_query($dbconnect,"SELECT * FROM dtr WHERE OT_day='No' AND Department='$Dept' AND Name !='' AND Day(DATE)='08' AND DATE BETWEEN '$datefrom' and '$dateto' GROUP BY Name");
$sqlday09=mysqli_query($dbconnect,"SELECT * FROM dtr WHERE OT_day='No' AND Department='$Dept' AND Name !='' AND Day(DATE)='09' AND DATE BETWEEN '$datefrom' and '$dateto' GROUP BY Name");
$sqlday10=mysqli_query($dbconnect,"SELECT * FROM dtr WHERE OT_day='No' AND Department='$Dept' AND Name !='' AND Day(DATE)='10' AND DATE BETWEEN '$datefrom' and '$dateto' GROUP BY Name");
$sqlday11=mysqli_query($dbconnect,"SELECT * FROM dtr WHERE OT_day='No' AND Department='$Dept' AND Name !='' AND Day(DATE)='11' AND DATE BETWEEN '$datefrom' and '$dateto' GROUP BY Name");
$sqlday12=mysqli_query($dbconnect,"SELECT * FROM dtr WHERE OT_day='No' AND Department='$Dept' AND Name !='' AND Day(DATE)='12' AND DATE BETWEEN '$datefrom' and '$dateto' GROUP BY Name");
$sqlday13=mysqli_query($dbconnect,"SELECT * FROM dtr WHERE OT_day='No' AND Department='$Dept' AND Name !='' AND Day(DATE)='13' AND DATE BETWEEN '$datefrom' and '$dateto' GROUP BY Name");
$sqlday14=mysqli_query($dbconnect,"SELECT * FROM dtr WHERE OT_day='No' AND Department='$Dept' AND Name !='' AND Day(DATE)='14' AND DATE BETWEEN '$datefrom' and '$dateto' GROUP BY Name");
$sqlday15=mysqli_query($dbconnect,"SELECT * FROM dtr WHERE OT_day='No' AND Department='$Dept' AND Name !='' AND Day(DATE)='15' AND DATE BETWEEN '$datefrom' and '$dateto' GROUP BY Name");
$sqlday16=mysqli_query($dbconnect,"SELECT * FROM dtr WHERE OT_day='No' AND Department='$Dept' AND Name !='' AND Day(DATE)='16' AND DATE BETWEEN '$datefrom' and '$dateto' GROUP BY Name");
$sqlday17=mysqli_query($dbconnect,"SELECT * FROM dtr WHERE OT_day='No' AND Department='$Dept' AND Name !='' AND Day(DATE)='17' AND DATE BETWEEN '$datefrom' and '$dateto' GROUP BY Name");
$sqlday18=mysqli_query($dbconnect,"SELECT * FROM dtr WHERE OT_day='No' AND Department='$Dept' AND Name !='' AND Day(DATE)='18' AND DATE BETWEEN '$datefrom' and '$dateto' GROUP BY Name");
$sqlday19=mysqli_query($dbconnect,"SELECT * FROM dtr WHERE OT_day='No' AND Department='$Dept' AND Name !='' AND Day(DATE)='19' AND DATE BETWEEN '$datefrom' and '$dateto' GROUP BY Name");
$sqlday20=mysqli_query($dbconnect,"SELECT * FROM dtr WHERE OT_day='No' AND Department='$Dept' AND Name !='' AND Day(DATE)='20' AND DATE BETWEEN '$datefrom' and '$dateto' GROUP BY Name");
$sqlday21=mysqli_query($dbconnect,"SELECT * FROM dtr WHERE OT_day='No' AND Department='$Dept' AND Name !='' AND Day(DATE)='21' AND DATE BETWEEN '$datefrom' and '$dateto' GROUP BY Name");
$sqlday22=mysqli_query($dbconnect,"SELECT * FROM dtr WHERE OT_day='No' AND Department='$Dept' AND Name !='' AND Day(DATE)='22' AND DATE BETWEEN '$datefrom' and '$dateto' GROUP BY Name");
$sqlday23=mysqli_query($dbconnect,"SELECT * FROM dtr WHERE OT_day='No' AND Department='$Dept' AND Name !='' AND Day(DATE)='23' AND DATE BETWEEN '$datefrom' and '$dateto' GROUP BY Name");
$sqlday24=mysqli_query($dbconnect,"SELECT * FROM dtr WHERE OT_day='No' AND Department='$Dept' AND Name !='' AND Day(DATE)='24' AND DATE BETWEEN '$datefrom' and '$dateto' GROUP BY Name");
$sqlday25=mysqli_query($dbconnect,"SELECT * FROM dtr WHERE OT_day='No' AND Department='$Dept' AND Name !='' AND Day(DATE)='25' AND DATE BETWEEN '$datefrom' and '$dateto' GROUP BY Name");
$sqlday26=mysqli_query($dbconnect,"SELECT * FROM dtr WHERE OT_day='No' AND Department='$Dept' AND Name !='' AND Day(DATE)='26' AND DATE BETWEEN '$datefrom' and '$dateto' GROUP BY Name");
$sqlday27=mysqli_query($dbconnect,"SELECT * FROM dtr WHERE OT_day='No' AND Department='$Dept' AND Name !='' AND Day(DATE)='27' AND DATE BETWEEN '$datefrom' and '$dateto' GROUP BY Name");
$sqlday28=mysqli_query($dbconnect,"SELECT * FROM dtr WHERE OT_day='No' AND Department='$Dept' AND Name !='' AND Day(DATE)='28' AND DATE BETWEEN '$datefrom' and '$dateto' GROUP BY Name");
$sqlday29=mysqli_query($dbconnect,"SELECT * FROM dtr WHERE OT_day='No' AND Department='$Dept' AND Name !='' AND Day(DATE)='29' AND DATE BETWEEN '$datefrom' and '$dateto' GROUP BY Name");
$sqlday30=mysqli_query($dbconnect,"SELECT * FROM dtr WHERE OT_day='No' AND Department='$Dept' AND Name !='' AND Day(DATE)='30' AND DATE BETWEEN '$datefrom' and '$dateto' GROUP BY Name");
$sqlday31=mysqli_query($dbconnect,"SELECT * FROM dtr WHERE OT_day='No' AND Department='$Dept' AND Name !='' AND Day(DATE)='31' AND DATE BETWEEN '$datefrom' and '$dateto' GROUP BY Name");
$HC01=mysqli_num_rows($sqlday01);
$HC02=mysqli_num_rows($sqlday02);
$HC03=mysqli_num_rows($sqlday03);
$HC04=mysqli_num_rows($sqlday04);
$HC05=mysqli_num_rows($sqlday05);
$HC06=mysqli_num_rows($sqlday06);
$HC07=mysqli_num_rows($sqlday07);
$HC08=mysqli_num_rows($sqlday08);
$HC09=mysqli_num_rows($sqlday09);
$HC10=mysqli_num_rows($sqlday10);
$HC11=mysqli_num_rows($sqlday11);
$HC12=mysqli_num_rows($sqlday12);
$HC13=mysqli_num_rows($sqlday13);
$HC14=mysqli_num_rows($sqlday14);
$HC15=mysqli_num_rows($sqlday15);
$HC16=mysqli_num_rows($sqlday16);
$HC17=mysqli_num_rows($sqlday17);
$HC18=mysqli_num_rows($sqlday18);
$HC19=mysqli_num_rows($sqlday19);
$HC20=mysqli_num_rows($sqlday20);
$HC21=mysqli_num_rows($sqlday21);
$HC22=mysqli_num_rows($sqlday22);
$HC23=mysqli_num_rows($sqlday23);
$HC24=mysqli_num_rows($sqlday24);
$HC25=mysqli_num_rows($sqlday25);
$HC26=mysqli_num_rows($sqlday26);
$HC27=mysqli_num_rows($sqlday27);
$HC28=mysqli_num_rows($sqlday28);
$HC29=mysqli_num_rows($sqlday29);
$HC30=mysqli_num_rows($sqlday30);
$HC31=mysqli_num_rows($sqlday31);
$total_HC=$HC01+$HC02+$HC03+$HC04+$HC05+$HC06+$HC07+$HC08+$HC09+$HC10+$HC11+$HC12+$HC13+$HC14+$HC15+$HC16+$HC17+$HC18+$HC19+$HC20+$HC21+$HC22+$HC23+$HC24+$HC25+$HC26+$HC27+$HC28+$HC29+$HC30+$HC31;

$total_wd_sql=mysqli_query($dbconnect,"SELECT * FROM dtr  WHERE OT_day='No' AND Department='$Dept' AND DATE BETWEEN '$datefrom' and '$dateto' GROUP BY DATE");
$total_work_day=mysqli_num_rows($total_wd_sql);//$ot_days
$standard_hours=$total_HC*8;

//PRODUCTION ORDER STATUS
$total_fg_po_sql=mysqli_query($dbconnect,"SELECT * FROM wo  WHERE  TCD BETWEEN '$datefrom' and '$dateto' AND FG='Yes'");
$total_po_fg=mysqli_num_rows($total_fg_po_sql);
$total_target_po_sql=mysqli_query($dbconnect,"SELECT * FROM wo  WHERE  TCD BETWEEN '$datefrom' and '$dateto'  ");
$total_po_target=mysqli_num_rows($total_target_po_sql);
$prod_order=($total_po_fg/$total_po_target)*100;
$prod_order_closure=number_format($prod_order,2);

//BACKLOG PO
$total_acd_fg_sql=mysqli_query($dbconnect,"SELECT * FROM wo  WHERE  WEEK(ACD) <= WEEK(TCD) AND TCD BETWEEN '$datefrom' and '$dateto'");
$total_acd_fg=mysqli_num_rows($total_acd_fg_sql);
$acd_fg=($total_acd_fg/$total_po_target)*100;
$acd_closure=number_format($acd_fg,2);

// MFG EFFICIENCY
$total_fg_qty_sql=mysqli_query($dbconnect,"SELECT sum(wo_quantity) as fg_qty FROM wo  WHERE WEEK(ACD) <= WEEK(TCD) AND TCD BETWEEN '$datefrom' and '$dateto' ");
while($row = mysqli_fetch_array($total_fg_qty_sql)){
  $total_fg_qty=$row['fg_qty'];

$total_target_qty_sql=mysqli_query($dbconnect,"SELECT sum(wo_quantity) as tartget_qty FROM wo  WHERE   TCD BETWEEN '$datefrom' and '$dateto'");
while($row = mysqli_fetch_array($total_target_qty_sql)){
  $total_target_qty=$row['tartget_qty'];

  
  $mfg_eff=($total_fg_qty/$total_target_qty)*100;
  $mfg_efficiency=number_format($mfg_eff,2);

$total_mp_sql=mysqli_query($dbconnect,"SELECT * FROM user  WHERE Department='$Dept' AND role='operator'");
$total_mp=mysqli_num_rows($total_mp_sql);
$total_absent=($total_operator*$total_work_day)-$total_HC;

$absenteeism=($total_absent/($total_operator*$total_work_day))*100;
$HC_WD=$total_operator*$total_work_day;
$absenteeismformat=number_format($absenteeism,2);

$dbconnect=mysqli_connect('localhost','root','','ewip');

//GET THE ACTUAL DIRECT LABOR HOURS BASED ON ACTUAL PROCESSED PART
$actual_time_query=mysqli_query($dbconnect,"SELECT DATE,sum(Duration)/60 as actual_time, sum(Qty_Make) as Qty_Make,Stations,Part_No,remarks,Code,Station_No,Labor_Type,WEEK(DATE)+1 as Week_No FROM dtr 
WHERE  Qty_Make >0 AND wo_status ='IN-PROCESS' AND Department='$Dept' AND DATE BETWEEN '$datefrom' and '$dateto' ");
 
while($actual_time_row = mysqli_fetch_array($actual_time_query)){
$part_nos=$actual_time_row['Part_No'];
$qty_make=$actual_time_row['Qty_Make'];
$stations=$actual_time_row['Stations'];
$date=$actual_time_row['DATE'];
$actual_time=$actual_time_row['actual_time'];
$actual_time_format=number_format($actual_time,2);

//GET STANDARD TIME BASED ON PROCESSED PART //*dtr.Qty_Make
$standard_time_query=mysqli_query($dbconnect,"SELECT
SUM(CASE
WHEN cable_cycletime.part_no = dtr.Part_No AND cable_cycletime.station = dtr.Stations THEN cable_cycletime.cycle_time*dtr.Qty_Make END)/60 AS standard_time
FROM cable_cycletime
INNER JOIN dtr 
WHERE dtr.Qty_Make >0 AND dtr.wo_status ='IN-PROCESS' AND dtr.Department='$Dept' AND dtr.DATE BETWEEN '$datefrom' and '$dateto'");
$row = mysqli_fetch_array($standard_time_query);
$standard_time=$row['standard_time']; 
$standard_time_format=number_format($standard_time,2);

$eff=($row['standard_time']/$actual_time_row['actual_time'])*100;
$efficiency=number_format($eff,2);

//echo "<br>Qty Make:$qty_make<br>Standard Time:$standard_time<br>Actual Time:$actual_time<br>$efficiency";

}


//GET THE ACTUAL OUTPUT PER HOUR BASED ON ACTUAL PROCESSED PART
$actual_output_query=mysqli_query($dbconnect,"SELECT DATE,sum(Duration/Qty_Make)/60 as actual_time, Qty_Make,Stations,Part_No,remarks,Code,Station_No,Labor_Type,WEEK(DATE)+1 as Week_No FROM dtr 
WHERE  Qty_Make >0 AND wo_status ='IN-PROCESS' AND Department='$Dept' AND DATE BETWEEN '$datefrom' and '$dateto' ");
 
while($actual_output_row = mysqli_fetch_array($actual_output_query)){
$part_nos=$actual_output_row['Part_No'];
$qty_make=$actual_output_row['Qty_Make'];
$stations=$actual_output_row['Stations'];
$date=$actual_output_row['DATE'];
$actual_output=60/($actual_output_row['actual_time']);
$actual_output_format=number_format($actual_output,2);

//GET STANDARD OUTPUT PER HOUR BASED ON PROCESSED PART
$standard_output_query=mysqli_query($dbconnect,"SELECT
SUM(CASE
WHEN cable_cycletime.part_no = dtr.Part_No AND cable_cycletime.station = dtr.Stations THEN cable_cycletime.cycle_time END)/60 AS standard_time
FROM cable_cycletime
INNER JOIN dtr 
WHERE dtr.Qty_Make >0 AND dtr.wo_status ='IN-PROCESS' AND dtr.Department='$Dept' AND dtr.DATE BETWEEN '$datefrom' and '$dateto'");
$row = mysqli_fetch_array($standard_output_query);
$standard_output=60/($row['standard_time']); 
$standard_output_format=number_format($standard_output,2);

$output_eff=($actual_output_format/$standard_output_format)*100;
$output_efficiency=number_format($output_eff,2);

//echo $actual_output_format, $standard_output, '&nbsp;',$output_efficiency ;

}


//REGULAR DIRECT LABOR
$reg_direct_labor_sql=mysqli_query($dbconnect,"SELECT DATE,sum(Duration)/60 as Duration, sum(Qty_Make) as Qty_Make, AVG(Duration/Qty_Make) as Average,Stations,Part_No,remarks,Code,Station_No,Labor_Type,WEEK(DATE)+1 as Week_No FROM dtr  
WHERE Labor_Type='Reg_DL' AND Qty_Make >0 AND wo_status ='IN-PROCESS' AND Department='$Dept' AND DATE BETWEEN '$datefrom' and '$dateto'");
$reg_direct_labor_row = mysqli_fetch_array($reg_direct_labor_sql);
$reg_direct_labor=$reg_direct_labor_row['Duration'];
$reg_direct_labor_format=number_format($reg_direct_labor,2);

//REGULAR INDIRECT LABOR
$reg_indirect_labor_sql=mysqli_query($dbconnect,"SELECT DATE,sum(Duration)/60 as Duration, sum(Qty_Make) as Qty_Make, AVG(Duration/Qty_Make) as Average,Stations,Part_No,remarks,Code,Station_No,Labor_Type,WEEK(DATE)+1 as Week_No FROM dtr  
WHERE Labor_Type='Reg_IDL' AND Qty_Make >0 AND wo_status ='INDIRECT' AND Department='$Dept' AND DATE BETWEEN '$datefrom' and '$dateto'");
$reg_indirect_labor_row = mysqli_fetch_array($reg_indirect_labor_sql);
$reg_indirect_labor=$reg_indirect_labor_row['Duration'];
$reg_indirect_labor_format=number_format($reg_indirect_labor,2);
//REGULAR DIRECT PLUS REGULAR INDIRECT
$reg_direct_indirect=$reg_direct_labor_row['Duration']+$reg_indirect_labor_row['Duration'];

//OPERATOR'S UTILIZATION   (actual process hrs) / (total labor hrs)
$op_uti=($reg_direct_labor_row['Duration']/($reg_direct_labor_row['Duration']+$reg_indirect_labor_row['Duration']))*100;
$operator_utilization=number_format($op_uti,2);

//ACTUAL OUTPUTS
$total_output_qty_sql=mysqli_query($dbconnect,"SELECT sum(wo_quantity) as output_qty FROM wo  WHERE  ACD BETWEEN '$datefrom' and '$dateto' ");
while($row = mysqli_fetch_array($total_output_qty_sql)){
  $total_output_qty=$row['output_qty'];

//TOTAL MAN HOURS
$total_man_hrs_sql=mysqli_query($dbconnect,"SELECT DATE,sum(Duration)/60 as Duration FROM dtr  
WHERE Stations !='PARTS KITTING' AND Qty_Make >0 AND Department='$Dept' AND DATE BETWEEN '$datefrom' and '$dateto'");

$total_man_hrs_row = mysqli_fetch_array($total_man_hrs_sql);
$total_man_hrs=$total_man_hrs_row['Duration'];
$total_man_hrs_format=number_format($total_man_hrs,2);

//LABOR PRODUCTIVITY
$productivityformula=$row['output_qty']/$total_man_hrs_row['Duration'];
$productivity=number_format($productivityformula,2);

}

$Totalquery=mysqli_query($dbconnect,"SELECT Duration, Department,DATE,sum(Code303)/60 as T303,
                sum(Code310)/60 as T310,sum(Code311)/60 as T311,sum(Reg_DL)/60 as T_RDL,sum(Reg_IDL)/60 as T_RIDL,sum(OT_DL)/60 as T_ODL,sum(OT_IDL)/60 as T_OIDL,sum(Total_DL)/60 as Total_DL ,sum(Total_IDL)/60 as Total_IDL,
                sum(Duration)/60 as G_Total FROM dtr WHERE Department='$Dept' AND DATE BETWEEN '$datefrom' and '$dateto' ");


$count= mysqli_num_rows($Totalquery);

if($count=="0"){
    Echo "No data found from $datefrom to $dateto!";
}
else
{

  while($row = mysqli_fetch_array($Totalquery)){
    
    $T303=$row['T303'];
    $T310=$row['T310'];
    $T311=$row['T311'];
    
    $T_RDL=$row['T_RDL'];
    $T_RIDL=$row['T_RIDL'];
    $T_Reg_Hrs=$row['T_RDL']+$row['T_RIDL'];
    $T_ODL=$row['T_ODL'];
    $T_OIDL=$row['T_OIDL'];
    $T_ODL=$row['T_ODL'];
    $T_DL=$row['T_RDL'] + $row['T_ODL'];
    $T_IDL=$row['Total_IDL'];
    $G_Total=$row['G_Total'];
    $OTrate=($row['T_ODL']/$row['T_RDL'])*100;

    
  
    //$standard_time_format=number_format($standard_time,1);
    $T503format=number_format($T303,1);
    $T510format=number_format($T310,1);
    $T511format=number_format($T311,1);
    
    $T_RDLformat=number_format($T_RDL,1);
    $T_RIDLformat=number_format($T_RIDL,1);
    $T_ODLformat=number_format($T_ODL,1);
    $T_OIDLformat=number_format($T_OIDL,1);
    
    $T_DLformat=number_format($T_DL,1);
    $T_IDLformat=number_format($T_IDL,1);

    $G_Totalformat=number_format($G_Total,1);

    $total_OT=$T_ODLformat+$T_OIDLformat;
    $Allowed_OT=$total_operator*50;
    $Ave_OT=($total_OT/$Allowed_OT)*100;
    $Ave_OTformat=number_format($Ave_OT,2);
    $OTrateformat=number_format($OTrate,2);
    
    
    
  
    ?>


<TABLE  id= "dtrsummary"style="width:70%" border="1">
<thead>
<tr><td style='text-align:center' colspan='32' width='100%' ><h4 style="background-color: #F8C471 " ><bold>PRODUCTIVITY PERFORMANCE</bold></h4>
<?php Echo"FROM: $datefrom TO: $dateto"; ?>  
</td></tr>

<tr style="background-color:#D1D0CE;color:white">
<th>FIELD</th><th>ITEM</th><th colspan="">FORMULA</th><th colspan="">ACTUAL VALUE</th><th colspan="">PERCENT VALUE</th><th colspan="">TARGET</th>

</TR>
<TR>
<td rowspan="4" Style="background-color:#C0C0C0;text-align:center">ATTENDANCE</td>
<td rowspan= "2" style="background-color:#FFF8DC;text-align:right">Allowable OT Rate Consumption =</td><TD style="text-align:center;background-color:#FFF8DC">Total OT hrs.</td><td style="text-align:center;background-color:#FFF8DC"><?php echo "$total_OT"; ?></td><td rowspan="2" style="text-align:center;background-color:#FFF8DC"><?php echo "$Ave_OTformat%" ;?></td><td rowspan="2" Style="background-color:#C0C0C0;text-align:center"> < 100%</td>
<tr><td style="text-align:center;background-color:#FFF8DC">Total Headcount * 50</TD><td style="text-align:center;background-color:#FFF8DC"><?php echo $Allowed_OT;?></td>
</TR>

<TR>
<td rowspan= "2" style="background-color:#FFF8DC;text-align:right">Absenteeism(%) =</td><TD style="text-align:center;background-color:#FFF8DC">Total Absences</td><td style="text-align:center;background-color:#FFF8DC"><?php echo "$total_absent";?></td><td rowspan="2" style="text-align:center;background-color:#FFF8DC"><?php echo "$absenteeismformat%"; ?></td><td rowspan="2" Style="background-color:#C0C0C0;text-align:center">0%</td></tr>
<tr><td style="text-align:center;background-color:#FFF8DC">(Headcount * Total Workday)</TD><td style="text-align:center;background-color:#FFF8DC"><?php echo $HC_WD; ?></td>
</TR>

<TR>
<td rowspan="2" Style="background-color:#C0C0C0;text-align:center">EFFICIENCY</td>
<td rowspan= "2" style="background-color:;text-align:right"> Operator's Efficiency(%) =</td><TD style="text-align:center">Standard Cycletime(Output)</td><td style="text-align:center"><?php echo $standard_time_format; ?> hrs.</td><td rowspan="2"style="text-align:center"> <?php echo $efficiency ; ?>%</td><td rowspan="2" Style="background-color:#C0C0C0;text-align:center"> 70%</td></tr>
<tr><td style="text-align:center">Actual Cycletime(Output)</TD><td style="text-align:center"><?php echo $actual_time_format; ?> hrs.</td></TR>
<tr>
<td rowspan="2" Style="background-color:#C0C0C0;text-align:center">UTILIZATION</td>
<td rowspan= "2" style="background-color:#FFF8DC;text-align:right">Operators Utilization(%) =</td><TD style="text-align:center;background-color:#FFF8DC">Reg. Direct Labor</td><td style="text-align:center;background-color:#FFF8DC"><?php echo $reg_direct_labor_format;?></td><td rowspan="2" style="text-align:center;background-color:#FFF8DC"><?php echo $operator_utilization;?>%</td><td rowspan="2" Style="background-color:#C0C0C0;text-align:center">70% </td></tr>
<tr><td style="text-align:center;background-color:#FFF8DC">Reg. Direct Labor + Reg. Indirect Labor</TD><td style="text-align:center;background-color:#FFF8DC"><?php echo $reg_direct_indirect;?></td></TR>

<TR>
<td rowspan="2" Style="background-color:#C0C0C0;text-align:center">PRODUCTIVITY</td>
<td rowspan= "2" style="background-color:;text-align:right"> Labor Productivity =</td><TD style="text-align:center">Actual output(units)</td><td style="text-align:center"><?php  echo $total_output_qty; ?></td><td rowspan="2" style="text-align:center"><?php echo $productivity;?> &nbsp;unit/ManHours</td><td rowspan="2" Style="background-color:#C0C0C0;text-align:center"> </td></tr>
<tr><td style="text-align:center">Total ManHours</TD><td style="text-align:center"><?php echo $total_man_hrs_format;?></td></TR>

<TR>
<td rowspan="6" Style="background-color:#C0C0C0;text-align:center">WEEKLY PRODUCTION ORDER STATUS</td>
<td rowspan= "2" style="background-color:#FFF8DC;text-align:right"> Updated PO Closure(%) =</td><TD style="text-align:center;background-color:#FFF8DC">New Actual FG</td><td style="text-align:center;background-color:#FFF8DC"><?php echo $total_po_fg; ?></td><td rowspan="2" style="text-align:center;background-color:#FFF8DC"><?php echo $prod_order_closure; ?>%</td><td rowspan="2" Style="background-color:#C0C0C0;text-align:center"> 100%</td></tr>
<tr><td style="text-align:center;background-color:#FFF8DC">Total Items Loaded in a batch</TD><td style="text-align:center;background-color:#FFF8DC"><?php echo $total_po_target; ?></td></TR>

<TR>

<td rowspan= "2" style="background-color:#FFF8DC;text-align:right"> Based on ACD PO Closure(%) =</td><TD style="text-align:center;background-color:#FFF8DC">Actual FG</td><td style="text-align:center;background-color:#FFF8DC"><?php echo $total_acd_fg; ?></td><td rowspan="2" style="text-align:center;background-color:#FFF8DC"><?php echo $acd_closure; ?>%</td><td rowspan="2" Style="background-color:#C0C0C0;text-align:center"> 100%</td></tr>
<tr><td style="text-align:center;background-color:#FFF8DC">Total Items Loaded in a batch</TD><td style="text-align:center;background-color:#FFF8DC"><?php echo $total_po_target; ?></td></TR>
<TR>

<td rowspan= "2" style="background-color:#FFF8DC;text-align:right"> Based on ACD QTY Closure(%) =</td><TD style="text-align:center;background-color:#FFF8DC">Actual FG QTY</td><td style="text-align:center;background-color:#FFF8DC"><?php echo $total_fg_qty; ?></td><td rowspan="2" style="text-align:center;background-color:#FFF8DC"><?php echo  $mfg_efficiency; ?>%</td><td rowspan="2" Style="background-color:#C0C0C0;text-align:center"> 70%</td></tr>
<tr><td style="text-align:center;background-color:#FFF8DC">Total quantity Loaded in a batch</TD><td style="text-align:center;background-color:#FFF8DC"><?php echo $total_target_qty; ?></td></TR>

</table>

<?php

}}}}
  
  $weekly_standard_time_query=mysqli_query($dbconnect,"SELECT sum(cycle_time*wo_quantity)/60 as weekly_standard_time, WEEK(TCD)+1 as Week_No, wo_id FROM wo WHERE dept='$Dept' GROUP BY  WEEK(TCD) ");
  while($weekly_standard_time_row = mysqli_fetch_array($weekly_standard_time_query)){
  $weekly_standard_time=$weekly_standard_time_row['weekly_standard_time'];
  $wo_id=$weekly_standard_time_row['wo_id'];
  $Week_No=$weekly_standard_time_row['Week_No'];

$Weekly_actual_query=mysqli_query($dbconnect,"SELECT sum(Reg_DL)/60 as T_RDL,sum(OT_DL)/60 as T_ODL,  wo_id FROM dtr WHERE wo_id='$wo_id' ");
$weekly_actual_time_row = mysqli_fetch_array($Weekly_actual_query);
$weekly_reg_time=$weekly_actual_time_row['T_RDL'];
$weekly_over_time=$weekly_actual_time_row['T_ODL'];
$weekly_direct=$weekly_actual_time_row['T_RDL']+$weekly_actual_time_row['T_ODL'];

};
  echo"<a href='cable_performance_charts.php' >VIEW GRAPHICAL REPORT</a>";
}


?>


  </body>

  <script>
var xport = {
  _fallbacktoCSV: true,  
  toXLS: function(tableId, filename) {   
    this._filename = (typeof filename == 'undefined') ? tableId : filename;
    
    //var ieVersion = this._getMsieVersion();
    //Fallback to CSV for IE & Edge
    if ((this._getMsieVersion() || this._isFirefox()) && this._fallbacktoCSV) {
      return this.toCSV(tableId);
    } else if (this._getMsieVersion() || this._isFirefox()) {
      alert("Not supported browser");
    }

    //Other Browser can download xls
    var htmltable = document.getElementById(tableId);
    var html = htmltable.outerHTML;

    this._downloadAnchor("data:application/vnd.ms-excel" + encodeURIComponent(html), 'xls'); 
  },
  toCSV: function(tableId, filename) {
    this._filename = (typeof filename === 'undefined') ? tableId : filename;
    // Generate our CSV string from out HTML Table
    var csv = this._tableToCSV(document.getElementById(tableId));
    // Create a CSV Blob
    var blob = new Blob([csv], { type: "text/csv" });

    // Determine which approach to take for the download
    if (navigator.msSaveOrOpenBlob) {
      // Works for Internet Explorer and Microsoft Edge
      navigator.msSaveOrOpenBlob(blob, this._filename + ".csv");
    } else {      
      this._downloadAnchor(URL.createObjectURL(blob), 'csv');      
    }
  },
  _getMsieVersion: function() {
    var ua = window.navigator.userAgent;

    var msie = ua.indexOf("MSIE ");
    if (msie > 0) {
      // IE 10 or older => return version number
      return parseInt(ua.substring(msie + 5, ua.indexOf(".", msie)), 10);
    }

    var trident = ua.indexOf("Trident/");
    if (trident > 0) {
      // IE 11 => return version number
      var rv = ua.indexOf("rv:");
      return parseInt(ua.substring(rv + 3, ua.indexOf(".", rv)), 10);
    }

    var edge = ua.indexOf("Edge/");
    if (edge > 0) {
      // Edge (IE 12+) => return version number
      return parseInt(ua.substring(edge + 5, ua.indexOf(".", edge)), 10);
    }

    // other browser
    return false;
  },
  _isFirefox: function(){
    if (navigator.userAgent.indexOf("Firefox") > 0) {
      return 1;
    }
    
    return 0;
  },
  _downloadAnchor: function(content, ext) {
      var anchor = document.createElement("a");
      anchor.style = "display:none !important";
      anchor.id = "downloadanchor";
      document.body.appendChild(anchor);

      // If the [download] attribute is supported, try to use it
      
      if ("download" in anchor) {
        anchor.download = this._filename + "." + ext;
      }
      anchor.href = content;
      anchor.click();
      anchor.remove();
  },
  _tableToCSV: function(table) {
    // We'll be co-opting `slice` to create arrays
    var slice = Array.prototype.slice;

    return slice
      .call(table.rows)
      .map(function(row) {
        return slice
          .call(row.cells)
          .map(function(cell) {
            return '"t"'.replace("t", cell.textContent);
          })
          .join(",");
      })
      .join("\r\n");
  }
};
</script>

<script>
    var myApp = new function () {
        this.printTable = function () {
            var tab = document.getElementById('dtrsummary');
            var win = window.open('', '', 'height=700,width=700');
            win.document.write(tab.outerHTML);
            win.document.close();
            win.print();
        }
    }
</script>

    </html>