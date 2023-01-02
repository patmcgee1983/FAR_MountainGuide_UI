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
$yesterday = new DateTime($date);

$yesterday = $yesterday->sub(new DateInterval('P1D'));

$yesterday = $yesterday->format('Y-m-d');
$today = $today->format('Y-m-d');


$sql = "select * from Comments where cast(LastUpdate as date) = '$today' order by LastUpdate desc limit 1";

$result->comments = array();

$sql_result = mysqli_query($con,$sql);
$result->message = mysqli_error($con);


while ($tempRow = mysqli_fetch_assoc($sql_result))
{
	$tempResult = new stdClass();
	
	$tempResult->lastUpdate = $tempRow["LastUpdate"];
	$tempResult->comments = $tempRow["Comments"];

	array_push($result->comments, $tempResult);
}


$result->sql = $sql;

$result->status = "success";

echo json_encode($result);

?>