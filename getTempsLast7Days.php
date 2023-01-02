<?php

error_reporting(E_ERROR);

$result = new stdClass();

require("connection_details.php");


if (isset($_POST["date"]))
{
	$date = $_POST["date"];
}
else
{
	$date = "";
}
$con = getConnection();


$today = new DateTime($date);
$lastWeek = new DateTime($date);

$lastWeek = $lastWeek->sub(new DateInterval('P7D'));

$lastWeek = $lastWeek->format('Y-m-d');
$today = $today->format('Y-m-d');


$sql = "SELECT 
	distinct (cast(Temperatures.LastUpdate as Date)) as date,
    max(Temperatures.snowReportWeatherUpperMtnLow) minUpper,
    max(Temperatures.snowReportWeatherUpperMtnHigh) maxUpper,
    max(Temperatures.snowReportWeatherLowerMtnLow) minLower,
    max(Temperatures.snowReportWeatherLowerMtnHigh) maxLower
    
    from Temperatures
    WHERE (cast(LastUpdate as Date)) BETWEEN '$lastWeek' AND '$today'
	
    group by date;
	";
	
$result = mysqli_query($con,$sql);
$result->message = mysqli_error($con);
$result->temps = array();

while ($tempRow = mysqli_fetch_assoc($result))
{
	$tempResult = new stdClass();
	
	$tempResult->date = $tempRow["date"];
	$tempResult->minUpper = $tempRow["minUpper"];
	$tempResult->maxUpper = $tempRow["maxUpper"];
	$tempResult->minLower = $tempRow["minLower"];
	$tempResult->maxLower = $tempRow["maxLower"];
	
	array_push($result->temps, $tempResult);
}


$result->sql = $sql;

$result->status = "success";

echo json_encode($result);

?>