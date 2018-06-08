<?php
	//Create a user session or resume an existing one
	session_start();
			
	// include database connection
	include_once 'config/connection.php'; 
	
	// SELECT all current cars
	//".mysql_real_escape_string($_GET["parent"])
	$query = "SELECT Make, Model, VIN FROM car WHERE Location='".mysql_real_escape_string($_GET["parent"])."'";
	$stmt = $con->prepare($query);
	$stmt->execute();
	$result = $stmt->get_result();
	if($result->num_rows == 0) {
		echo '<option value="none" selected disabled hidden>No Cars Available</option>';
	}
	while($row = $result->fetch_assoc()) {
		$name = $row['Make'].', '.$row['Model'];
		echo '<option value="'.$row['VIN'].'">'.$name.'</option>';
	}
	
?>