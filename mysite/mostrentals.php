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
			if(isset($_POST['selectBtn']) && isset($_POST['selHL'])) {
				$dispData = true;
			}
			
		?>
		
		<div id="siteHeader"></div>
		<script>
			$(function() {
				$("#siteHeader").load("header.php")
			});
		</script>
				
		<h2><center>Best / Worst Rental Tracker</center></h2>
		<form name='carSelect' id='carSelect' action='mostrentals.php' method='post'>
			<table border='0' class="box">
				<tr>
					<td>
						<select name="selHL" select style="width: 345px">
						<option selected disabled hidden>--Select--</option>
						<option value='1'>High</option>
						<option value='0'>Low</option>
						</select>
					</td>
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
						if($_POST['selHL']) {
							$query = "SELECT VIN, Rentals, Make, Model FROM (SELECT VIN, COUNT(VIN) as Rentals FROM rental GROUP BY VIN) as SUBQUERY NATURAL JOIN car ORDER BY Rentals DESC LIMIT 1";
						} else {
							$query = "SELECT VIN, Rentals, Make, Model FROM (SELECT VIN, COUNT(VIN) as Rentals FROM rental GROUP BY VIN) as SUBQUERY NATURAL JOIN car ORDER BY Rentals ASC LIMIT 1";
						}
						$stmt = $con->prepare($query);
						$stmt->execute();
						$result = $stmt->get_result();
						//$num = $result->num_rows;
						if($result->num_rows > 0) {
						// Display results in a table
							echo '<tr><th>Car</th><th>VIN</th><th>Number of Rentals</th></tr>';
							while($row = $result->fetch_assoc()) {
								$name = $row['Make'].', '.$row['Model'];
								echo '<tr><td>'.$name.'</td><td>'.$row['VIN'].'</td><td>'.$row['Rentals'].'</td></tr>';
							}
						} else {
							// No car has any rentals
							echo '<tr><th>There are no cars with rental history</th></tr>';
						}
					}
					
				?>
			</table>
		</form>
		
	</body>
</html>