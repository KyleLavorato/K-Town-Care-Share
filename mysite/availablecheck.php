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
			
			$dispData = false;
			if(isset($_POST['selectBtn']) && isset($_POST['month_start']) && isset($_POST['day_start']) && isset($_POST['year_start'])) {
				$dispData = true;
			}
			
		?>
		
		<div id="siteHeader"></div>
		<script>
			$(function() {
				$("#siteHeader").load("header.php")
			});
		</script>
				
		<form name='carSelect' id='carSelect' action='availablecheck.php' method='post'>
			<table border='0' class="box">
				<tr>
					<th colspan=3>Availability Checker</th>
				</tr>
				<tr>
					<td>
						<select id="month_start" name="month_start" /> 
						<option selected disabled hidden>Month</option>
						<option value='01'>January</option>       
						<option value='02'>February</option>       
						<option value='03'>March</option>       
						<option value='04'>April</option>       
						<option value='05'>May</option>       
						<option value='06'>June</option>       
						<option value='07'>July</option>       
						<option value='08'>August</option>       
						<option value='09'>September</option>       
						<option value='10'>October</option>       
						<option value='11'>November</option>       
						<option value='12'>December</option>       
					  </select>
					</td>
					<td>
						<select id="day_start" name="day_start" /> 
						<option selected disabled hidden>Day</option>
						<option value='01'>1</option>       
						<option value='02'>2</option>       
						<option value='03'>3</option>       
						<option value='04'>4</option>       
						<option value='05'>5</option>       
						<option value='06'>6</option>       
						<option value='07'>7</option>       
						<option value='08'>8</option>       
						<option value='09'>9</option>       
						<option value='10'>10</option>       
						<option value='11'>11</option>       
						<option value='12'>12</option>       
						<option value='13'>13</option>       
						<option value='14'>14</option>       
						<option value='15'>15</option>       
						<option value='16'>16</option>       
						<option value='17'>17</option>       
						<option value='18'>18</option>       
						<option value='19'>19</option>       
						<option value='20'>20</option>       
						<option value='21'>21</option>       
						<option value='22'>22</option>       
						<option value='23'>23</option>       
						<option value='24'>24</option>       
						<option value='25'>25</option>       
						<option value='26'>26</option>       
						<option value='27'>27</option>       
						<option value='28'>28</option>       
						<option value='29'>29</option>       
						<option value='30'>30</option>       
						<option value='31'>31</option>       
					   </select>
					</td>
					<td>
						<select id="year_start" name="year_start" /> 
						<option selected disabled hidden>Year</option>
						<option value='2014'>2014</option>       
						<option value='2015'>2015</option>       
						<option value='2016'>2016</option>       
						<option value='2017'>2017</option>       
						<option value='2018'>2018</option>       
						</select> 
					</td>
				</tr>
				<tr>
					<td colspan=3><input type='submit' name='selectBtn' id='selectBtn' value='Select'/></td>
				</tr>
			</table>
			<table border='1' class="resbox"
				<?php
					// Add results table only if there are results
					if($dispData) {
						$dt = $_POST['year_start'].'-'.$_POST['month_start'].'-'.$_POST['day_start'];
						echo $dt;
						// SELECT all cars that have no reseration on specific date
						$query = "SELECT Location, Make, Model FROM car NATURAL JOIN parking WHERE VIN NOT IN (SELECT VIN FROM reservation WHERE ? >= Date AND ? <= Date(Date + ResLength)) ORDER BY Location";
						$stmt = $con->prepare($query);
						$stmt->bind_param('ss', $dt, $dt);
						$stmt->execute();
						$result = $stmt->get_result();
						if($result->num_rows > 0) {
						// Display results in a table
							echo '<tr><th>Car</th><th>Location</th></tr>';
							while($row = $result->fetch_assoc()) {
								$name = $row['Make'].', '.$row['Model'];
								echo '<tr><td>'.$name.'</td><td>'.$row['Location'].'</td></tr>';
							}
							echo '<tr><th colspan=2>Cars available on '.$dt.'</th></tr>';
						} else {
							// Car has no history yet
							echo '<tr><th>There are no cars free on '.$dt.'</th></tr>';
						}
					}
					
				?>
			</table>
		</form>
		
	</body>
</html>