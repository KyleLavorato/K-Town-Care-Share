<!DOCTYPE HTML>
<html>
	<link rel="stylesheet" href="style.css" />
	<head>
		<title>Bootstrap Case</title>
		<meta charset="utf-8">
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
		<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.1/jquery.min.js"></script>
		<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
	</head>
	
	<style>
		th {
			text-align: center;
		}
		.box {
			width:345px;
			height:100px;
			position:fixed;
			margin-left:-175px; /* half of width */
			margin-top:-275px;  /* half of height */
			top:50%;
			left:50%;
		}
		.resbox {
			width:1000px;
			height:auto;
			position:fixed;
			margin-left:-500px; /* half of width */
			margin-top:-150px;  /* half of height */
			top:50%;
			left:50%;
			text-align:center;
		}
		td {
			border-top: 1px solid black;
			border-left: 1px solid black;
			border-right: 1px solid black;
		}
		th {
			border-top: 1px solid black;
			border-left: 1px solid black;
			border-right: 1px solid black;
			border-bottom: 1px solid black;
		}
		.noBorder {
			border-top: none !important;
		}
		.bottomRow {
			border-top: 1px solid black;
			border-left: none !important;
			border-right: none !important;
			border-bottom: none !important;
		}
		.removeBorder {
			border-top: none !important;
			border-left: none !important;
			border-right: none !important;
			border-bottom: none !important;
		}
	</style>
	
	<body>
	
		<?php
			//Create a user session or resume an existing one
			session_start();
			
			// include database connection
			include_once 'config/connection.php'; 
			
			// Determine if current user is an ADMIN
			$admin = false;
			if(isset($_SESSION['MemNo'])) {
				$adminArray = explode("\n", file_get_contents("admin.txt"));
				foreach($adminArray as $string) {
					if(trim($string) == $_SESSION['MemNo']) {
						$admin = true;
					}
				}
			}
			if($admin == false) {
				// User is not allowed on this page
				header("Location: home.php");
				die();
			}
			
			$dispData = false;
			if(isset($_POST['selectBtn']) && isset($_POST['selLoc'])) {
				//echo $_POST['selCar'];
				$dispData = true;
			}
			
		?>
		
		<div id="siteHeader"></div>
		<script>
			$(function() {
				$("#siteHeader").load("header.php")
			});
		</script>
				
		<h2><center>Location Checker</center></h2>
		<form name='locationSelect' id='locationSelect' action='locationcheck.php' method='post'>
			<table border='0' class="box">
				<tr>
					<td class="removeBorder"><?php
					// Find which loactions have available spots
					$query = "SELECT Location FROM parking";
					$stmt = $con->prepare($query);	
					$stmt->execute();
					$result = $stmt->get_result();
					
					// Create a select box with each car as an option
					echo '<select name="selLoc" select style="width: 345px">';
					echo '<option selected disabled hidden>--Select Location--</option>';
					while($row = $result->fetch_assoc()) {
						echo '<option value="'.$row['Location'].'">'.$row['Location'].'</option>';
					}
					echo '</select>';
					?></td>
				</tr>
				<tr>
					<td class="removeBorder"><input type='submit' name='selectBtn' id='selectBtn' value='Select'/></td>
				</tr>
			</table>
			<table border='0' class="resbox"
				<?php
					// Add results table only if there are results
					if($dispData) {
						$day = date("Y-m-d");
						// SELECT all cars in the selected parking area that are available and get their reservation info
						$query = "SELECT VIN, Make, Model, RentalID, MemNo, Date, ResLength FROM car LEFT OUTER JOIN (SELECT VIN, RentalID, MemNo, Date, ResLength FROM reservation WHERE Date > Date(?)) AS subquery USING (VIN) WHERE Location=? ORDER BY Make";
						//$query = "SELECT VIN, Make, Model, RentalID, MemNo, Date, ResLength FROM car NATURAL JOIN parking LEFT OUTER JOIN reservation USING (VIN) WHERE Available='Y' AND Location=? AND Date > Date(?)";
						$stmt = $con->prepare($query);
						$stmt->bind_param('ss', $day, $_POST['selLoc']);
						$stmt->execute();
						$result = $stmt->get_result();
						if($result->num_rows > 0) {
							// Display results in a table
							echo '<tr><th rowspan=2>Car</th><th rowspan=2>VIN</th><th colspan=4>Reservation</th></tr>';
							echo '<tr><td>RentalID</td><td>MemNo</td><td>Date</td><td>Length (Days)</td>';
							$row = $result->fetch_assoc();
							$name = $row['Make'].', '.$row['Model'];
							echo '<tr><td>'.$name.'</td><td>'.$row['VIN'].'</td><td>'.$row['RentalID'].'</td><td>'.$row['MemNo'].'</td><td>'.$row['Date'].'</td><td>'.$row['ResLength'].'</td></tr>';
							$old_vin = $row['VIN'];
							while($row = $result->fetch_assoc()) {
								$name = $row['Make'].', '.$row['Model'];
								if($old_vin == $row['VIN']) {
									echo '<tr><td class="noBorder"></td><td class="noBorder"></td><td>'.$row['RentalID'].'</td><td>'.$row['MemNo'].'</td><td>'.$row['Date'].'</td><td>'.$row['ResLength'].'</td></tr>';
								} else {
									echo '<tr><td>'.$name.'</td><td>'.$row['VIN'].'</td><td>'.$row['RentalID'].'</td><td>'.$row['MemNo'].'</td><td>'.$row['Date'].'</td><td>'.$row['ResLength'].'</td></tr>';
								}
								$old_vin = $row['VIN'];
							}
							echo '<tr class="bottomRow"><td></td><td></td><td></td><td></td><td></td><td></td></tr>';
						} else {
							// Location has no cars available
							echo '<tr><th>Location has no cars available</th></tr>';
						}
					}
					
				?>
			</table>
		</form>
		
	</body>
</html>