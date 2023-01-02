<?php

function makeError($message)
{
	$result->message = $message;

	$result->status = "fail";

	echo json_encode($result);
	die();
}

function getConnection()
{
	// WAMP Creds
  
  $host=getenv("DB_HOST");
  $port=3306;
  $socket="";
  $user=getenv("DB_USER");
  $password=getenv("DB_PASSWORD");
  $dbname=getenv("DB_NAME");
	
	
  $con = new mysqli($host, $user, $password, $dbname, $port, $socket);
  if (!$con)
  {
	  makeError(mysqli_connect_error());
  }

  return $con;
}


?>