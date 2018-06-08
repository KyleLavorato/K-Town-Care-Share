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
			
			// SELECT all cars in the selected parking area that are available and get their reservation info
			$query = "SELECT Location, Make, Model FROM parking NATURAL JOIN car WHERE Available='Y' ORDER BY Location";
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
				
		<h2><center>All KTCS Locations and Their Currently Available Cars</center></h2>
		<table border='1' class="resbox"
			<?php
				if($result->num_rows > 0) {
					// Display results in a table
					echo '<tr><th>Location</th><th>Car</th></tr>';
					$row = $result->fetch_assoc();
					$stack = array();
					$count = 1;
					$loc = $row['Location'];
					$name = $row['Make'].', '.$row['Model'];
					array_push($stack, $name);
					while($row = $result->fetch_assoc()) {
						$name = $row['Make'].', '.$row['Model'];
						if($loc != $row['Location']) {
							echo '<tr><td rowspan='.$count.'>'.$loc.'</td><td>'.array_pop($stack).'</td></tr>';
							for($i = 1; $i < $count; $i++) {
								echo '<tr><td>'.array_pop($stack).'</td></tr>';
							}
							$loc = $row['Location'];
							$count = 1;
							array_push($stack, $name);
						} else {
							array_push($stack, $name);
							$count++;
						}
					}
					echo '<tr><td rowspan='.$count.'>'.$loc.'</td><td>'.$name.'</td></tr>';
					for($i = 1; $i < $count; $i++) {
						echo '<tr><td>'.array_pop($stack).'</td></tr>';
					}
				} else {
					echo '<tr><th>No locations exist</th></tr>';
				}
			?>
		</table>
		
	</body>
</html>