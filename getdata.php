<?php

error_reporting(E_ERROR);

$result = new stdClass();

require("connection_details.php");


$date = $_POST["date"];

$con = getConnection();



$nextDate = new DateTime($date);
$date = new DateTime($date);

$nextDate->add(new DateInterval('P1D'));
$nextDate = $nextDate->format('Y-m-d');
$date = $date->format('Y-m-d');


$bowlSql = "SELECT * from bowls WHERE LastUpdate BETWEEN '$date' AND '$nextDate'";
$bowlResult = mysqli_query($con,$bowlSql);
$result->message = mysqli_error($con);

$tempSql = "SELECT * from Temperatures WHERE LastUpdate BETWEEN '$date' AND '$nextDate'";
$tempResult = mysqli_query($con,$tempSql);
while ($tempRow = mysqli_fetch_assoc($tempResult))
{
	$result->tempUpperMtnHigh = $tempRow["snowReportWeatherUpperMtnHigh"];
	$result->tempUpperMtnLow = $tempRow["snowReportWeatherUpperMtnLow"];
	$result->tempLowerMtnHigh = $tempRow["snowReportWeatherLowerMtnHigh"];
	$result->tempLowerMtnLow = $tempRow["snowReportWeatherLowerMtnLow"];
}

$snowSql = "SELECT * from NewSnow WHERE LastUpdate BETWEEN '$date' AND '$nextDate'";
$snowResult = mysqli_query($con,$snowSql);
while ($snowRow = mysqli_fetch_assoc($snowResult))
{
	$result->snowLastNight = $snowRow["snowReportNewSnowFallOvernight"];
	$result->snow24 = $snowRow["snowReportNewSnowFall24"];
	$result->snow48 = $snowRow["snowReportNewSnowFall48"];
	$result->snow7days = $snowRow["snowReportNewSnowFall7days"];
}

$result->data = array();
$i=0;

$fieldArray = array();
$bowls = array();
$updates = array();

while ($columnName = mysqli_fetch_field($bowlResult))
{
	array_push($fieldArray, $columnName->name);
}


while ($bowlRow = mysqli_fetch_assoc($bowlResult))
{
	
	$update = new stdClass();
	$update->bowls = array();
	$update->delta = array();
	
	foreach ($fieldArray as $field)
	{
		
		$currentBowl = new stdClass();
		if ($field == "LastUpdate")
		{
			$update->time = $bowlRow[$field];
		}
		else 
		{
			$currentBowl->bowl = $field;
			$currentBowl->status = $bowlRow[$field];
		}
		
		array_push($update->bowls,$currentBowl);
		
	}
	array_push($updates,$update);
	$result->i = $i;
	
	if ($i > 0)
	{
		for ($bowlNumber=0; $bowlNumber < count($updates[$i]->bowls); $bowlNumber++)
		{
			if ($updates[$i]->bowls[$bowlNumber]->status != $updates[$i-1]->bowls[$bowlNumber]->status && $updates[$i]->bowls[$bowlNumber]->bowl != "Id")
			{
				array_push($updates[$i]->delta, $updates[$i]->bowls[$bowlNumber]->bowl . " changed its status to " . $updates[$i]->bowls[$bowlNumber]->status);
			}
		}
	}
	$i++;
}

array_push($result->data,$updates);
array_push($result->fields,$fieldArray);

$result->sql = $bowlSql;
$result->status = "success";

echo json_encode($result);

?>