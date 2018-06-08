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
			margin-top:-300px;  /* half of height */
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
			
			// SELECT all cars in the selected parking area that are available and get their reservation info
			$query = "SELECT VIN, DropOffStatus, FeesDesc, Make, Model, MAX(DropOffDate) FROM rental NATURAL JOIN car WHERE DropOffStatus='D' OR DropOffStatus='NR' GROUP BY VIN";
			$stmt = $con->prepare($query);
			$stmt->execute();
			$result = $stmt->get_result();
			
		?>
		
		<div id="siteHeader"></div>
		<script>
			$(function() {
				$("#siteHeader").load("header.php")
			});
		</script>
				
		<h2><center>Damaged or Not Running Cars</center></h2>
		<table border='1' class="resbox"
			<?php
				if($result->num_rows > 0) {
					// Display results in a table
					echo '<tr><th>Car</th><th>VIN</th><th>Status</th><th>Description</th></tr>';
					while($row = $result->fetch_assoc()) {
						$name = $row['Make'].', '.$row['Model'];
						echo '<tr><td>'.$name.'</td><td>'.$row['VIN'].'</td><td>'.$row['DropOffStatus'].'</td><td>'.$row['FeesDesc'].'</td></tr>';
					}
					echo '<tr><td colspan=2>D = Damaged</td><td colspan=2>NR = Not Running</td></tr>';
				} else {
					echo '<tr><th>No cars are currently damaged</th></tr>';
				}
			?>
		</table>
		
	</body>
</html>