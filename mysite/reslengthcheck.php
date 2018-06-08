<?php
	
	//Create a user session or resume an existing one
	session_start();
			
	// include database connection
	include_once 'config/connection.php';
	
	if($_POST['length'] <= 14) {
		if($_POST['length'] > 0) {
			if($_POST['VIN'] != "none") {
				if($_POST['month'] != "Month" && $_POST['day'] != "Day" && $_POST['year'] != "Year") {
					if(isset($_POST['length'])) { 
						$date = $_POST['year']."-".$_POST['month']."-".$_POST['day'];
						// SELECT all current cars
						$query = "SELECT VIN FROM reservation WHERE (Date <= ADDDATE(?, INTERVAL ? DAY) AND ADDDATE(Date, INTERVAL ResLength DAY) >= ?) AND VIN=?";
						$stmt = $con->prepare($query);
						$stmt->bind_param('siss', $date, $_POST['length'], $date, $_POST['VIN']);
						$stmt->execute();
						$result = $stmt->get_result();
						if($result->num_rows != 0) {
							echo '<font color="red">The car is unavilable for '.$_POST['length'].' days at the desired date</font>';
						} else {
							echo 'OK';
						}
					}
				} else {
					echo '<font color="red">Please pick a date first</font>';
				}
			} else {
				echo '<font color="red">Please pick a car first</font>';
			} 
		} else {
			echo '<font color="red">Reservation must be for at least one day</font>';
		}
	} else {
			echo '<font color="red">Reservation can be max 14 days</font>';
	}
?>