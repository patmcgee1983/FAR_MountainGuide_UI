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
	distinct (cast(LastUpdate as Date)) as date,
    max(snowReportNewSnowFallOvernight) snowReportNewSnowFallOvernight,
    max(snowReportNewSnowFall24) snowReportNewSnowFall24,
    max(snowReportNewSnowFall48) snowReportNewSnowFall48,
    max(snowReportNewSnowFall7days) snowReportNewSnowFall7days
	from NewSnow WHERE (cast(LastUpdate as Date)) BETWEEN '$lastWeek' AND '$today'
    group by date;";

$result = mysqli_query($con,$sql);
$result->message = mysqli_error($con);
$result->newSnow = array();

while ($tempRow = mysqli_fetch_assoc($result))
{
	$tempResult = new stdClass();
	
	$tempResult->date = $tempRow["date"];
	$tempResult->snowReportNewSnowfallOvernight = $tempRow["snowReportNewSnowFallOvernight"];
	$tempResult->snowReportNewSnowfall24 = $tempRow["snowReportNewSnowFall24"];
	$tempResult->snowReportNewSnowfall48 = $tempRow["snowReportNewSnowFall48"];
	$tempResult->snowReportNewSnowfall7Days = $tempRow["snowReportNewSnowFall7Days"];
	
	array_push($result->newSnow, $tempResult);
}


$result->sql = $sql;

$result->status = "success";

echo json_encode($result);

?>