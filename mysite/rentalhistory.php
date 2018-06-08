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
			margin-top:-175px;  /* half of height */
			top:50%;
			left:50%;
			text-align:center;
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
			if(isset($_POST['selectBtn']) && isset($_POST['selCar'])) {
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
				
		<h2><center>Car Rental History</center></h2>
		<form name='carSelect' id='carSelect' action='rentalhistory.php' method='post'>
			<table border='0' class="box">
				<tr>
					<td><?php
					// SELECT all current cars
					$query = "SELECT VIN, Make, Model FROM car";
					$stmt = $con->prepare($query);
					$stmt->execute();
					$result = $stmt->get_result();
					
					// Create a select box with each car as an option
					echo '<select name="selCar" select style="width: 345px">';
					echo '<option selected disabled hidden>--Select Car--</option>';
					while($row = $result->fetch_assoc()) {
						echo '<option value="' . $row['VIN'] . '">' . $row['Make'] . ', ' . $row['Model'] . ', VIN: ' . $row['VIN'] . '</option>';
					}
					echo '</select>';
					?></td>
				</tr>
				<tr>
					<td><input type='submit' name='selectBtn' id='selectBtn' value='Select'/></td>
				</tr>
			</table>
			<table border='1' class="resbox"
				<?php
					// Add results table only if there are results
					if($dispData) {
						// SELECT all rental history elements for selected car
						$query = "SELECT * FROM rental WHERE VIN=?";
						$stmt = $con->prepare($query);
						$stmt->bind_param('s', $_POST['selCar']);
						$stmt->execute();
						$result = $stmt->get_result();
						//$num = $result->num_rows;
						if($result->num_rows > 0) {
						// Display results in a table
							echo '<tr><th rowspan=2>RentalID</th><th rowspan=2>MemNo</th><th rowspan=2>VIN</th><th colspan=2>Status</th><th colspan=2>Odometer</th><th colspan=2>Gas</th><th colspan=2>Date</th><th colspan=2>Time</th><th rowspan=2>Fees</th><th rowspan=2>Description</th></tr>';
							echo '<tr><td>PickUp</td><td>DropOff</td><td>PickUp</td><td>DropOff</td><td>PickUp</td><td>DropOff</td><td>PickUp</td><td>DropOff</td><td>PickUp</td><td>DropOff</td></tr>';
							while($row = $result->fetch_assoc()) {
								echo '<tr><td>'.$row['RentalID'].'</td><td>'.$row['MemNo'].'</td><td>'.$row['VIN'].'</td><td>'.$row['PickUpStatus'].'</td><td>'.$row['DropOffStatus'].'</td><td>'.$row['PickUpOdo'].'</td><td>'.$row['DropOffOdo'].'</td><td>'.$row['PickUpGas'].'</td><td>'.$row['DropOffGas'].'</td><td>'.$row['PickUpDate'].'</td><td>'.$row['DropOffDate'].'</td><td>'.$row['PickUpTime'].'</td><td>'.$row['DropOffTime'].'</td><td>'.$row['FeesOut'].'</td><td>'.$row['FeesDesc'].'</td></tr>';
							}
						} else {
							// Car has no history yet
							echo '<tr><th>Car has no rental history</th></tr>';
						}
					}
					
				?>
			</table>
		</form>
		
	</body>
</html>