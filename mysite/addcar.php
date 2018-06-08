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
		.instr {
		width:300px;
		position:fixed;
		text-align: center;
		margin-left:-600px; /* half of width */
		margin-top:-165px;  /* half of height */
		top:50%;
		left:50%;
		}
		#location {
		margin-top:8px;
		width:12px;
		height:12px;
		-moz-border-radius: 5px;
		border-radius: 5px;
		border:1px solid #ccc;
		color:#999;
		margin-left:0px;
		padding:10px;
		}
		.box {
		width:460px;
		height:460px;
		position:fixed;
		margin-left:-200px; /* half of width */
		margin-top:-175px;  /* half of height */
		top:50%;
		left:50%;
		}
		.boximg {
		width:450px;
		height:125px;
		position:fixed;
		margin-left:-225px; /* half of width */
		margin-top: -300px;  /* half of height */
		top:50%;
		left:50%;
		}
	</style>
	
	<body>
		
		<?php
			//Create a user session or resume an existing one
			session_start();
			
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
			
			// include database connection
			include_once 'config/connection.php'; 
			
			// Find which loactions have available spots
			$query = "SELECT Location, NumSpaces-NumCars AS FreeSpaces FROM parking WHERE NumSpaces > NumCars";
			$stmt = $con->prepare($query);	
			$stmt->execute();
			$result = $stmt->get_result();

			if(isset($_POST['addBtn'])) {
				if($_POST['vin'] != "" && isset($_POST['location']) && !empty($_POST['fee'])) { 
					//echo $_POST['fee'];
					$query = "INSERT into car VALUES (?, ?, ?, ?, ?, ?, 'Y')";
					$stmt = $con->prepare($query);	
					$stmt->bind_param('sssisi', $_POST['vin'], $_POST['make'], $_POST['model'], $_POST['year'], $_POST['location'], $_POST['fee']);
					// Execute the query
					if($stmt->execute()) {
						$query = "UPDATE parking SET NumCars=NumCars+1 WHERE Location=?";
						$stmt = $con->prepare($query);	
						$stmt->bind_param('s', $_POST['location']);
						$stmt->execute();
						header("Location: addcar.php");
						die();
					} else {
						echo 'Something Went Wrong. <br/>';
						//echo $stmt->error;
					}
				}
			}
		?>
		
		<div id="siteHeader"></div>
		<script>
			$(function() {
				$("#siteHeader").load("header.php")
			});
		</script>
		
		<!-- dynamic content will be here -->
		
		<img src="img/kar.png" class="boximg">
		
		<form name='carLoc' id='carLoc' action='addcar.php' method='post'>
			<table border='1' class="instr">
				<caption>Available Car Locations</caption>
				<tr>
					<td><b>Location</b></td>
					<td><b>Free Slots</b></td>
					<td><font color='red'>*</font><b>Select</b></td>
				</tr>
				<?php
					while($row = $result->fetch_assoc()) {
						//echo '<option>'.$row['Date'].'</option>';
						echo "<tr>";
						echo "<td>".$row['Location']."</td>";
						echo "<td>".$row['FreeSpaces']."</td>";
						echo "<td><input type='radio' id='location' name='location' value='".$row['Location']."'></td>";
						echo "</tr>";
					}
				?>
			</table>
				
			<table border='0' class="box">
				<tr>
					<td><font color='red'>*</font>VIN</td>
					<td><input type='text' name='vin' id='vin' /></td>
				</tr>
				<tr>
					<td>Make</td>
					<td><input type='text' name='make' id='make' /></td>
				</tr>
				<tr>
					<td>Model</td>
					<td><input type='text' name='model' id='model' /></td>
				</tr>
				<tr>
					<td>Year</td>
					<td><input type='number' name='year' min="0" onkeypress="return event.charCode >= 48" id='year' /></td>
				</tr>
				<tr>
					<td><font color='red'>*</font>Fee  $</td>
					<td><input type='number' name='fee' min="0" onkeypress="return event.charCode >= 48" id='fee' /></td>
				</tr>
				<tr>
					<td></td>
					<td>
						<input type='submit' name='addBtn' id='addBtn' value='Add Car' /> 
					</td>
				</tr>
				<tr>
					<td></td>
					<td><font color='red'>*Please fill in all required fields</font></td>
				</tr>
			</table>
		</form>
	</body>
</html>