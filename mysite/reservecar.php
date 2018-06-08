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
		<script type="text/javascript" src="jquery-1.2.6.min.js"></script>
	</head>
	
	<style>
		th {
		text-align: center;
		}
		.box {
		width:700px;
		height:100px;
		position:fixed;
		margin-left:-350px; /* half of width */
		margin-top:-275px;  /* half of height */
		top:50%;
		left:50%;
		}
		.resbox {
		width:1000px;
		height:auto;
		position:fixed;
		margin-left:-500px; /* half of width */
		margin-top:-5px;  /* half of height */
		top:50%;
		left:50%;
		text-align:center;
		}
		.blankRow {
		height: 20px
		}
		.object_ok
		{
		border: 1px solid green; 
		color: #333333; 
		}
		.object_error
		{
		border: 1px solid #AC3962; 
		color: #333333; 
		}
		input
		{
		margin: 5 5 5 0;
		padding: 2px; 
		border: 1px solid #999999; 
		border-top-color: #CCCCCC; 
		border-left-color: #CCCCCC; 
		color: #333333; 
		font-size: 13px;
		-moz-border-radius: 3px;
		}
	</style>
	
	<body>
		
		<?php
			
			function generateRandomString($loctxt = '', $length = 6) {
				$characters = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
				$charactersLength = strlen($characters);
				$randomString = $loctxt;
				for ($i = 0; $i < $length; $i++) {
					$randomString .= $characters[rand(0, $charactersLength - 1)];
				}
				return $randomString;
			}
			
			//Create a user session or resume an existing one
			session_start();
			
			if(!isset($_SESSION['MemNo'])){
				//User is not logged in. Redirect the browser to the login index.php page and kill this page.
				header("Location: index.php?page=reservecar.php");
				die();
			}
			
			// include database connection
			include_once 'config/connection.php'; 
			
			$dispData = false;
			$fail = false;
			if(isset($_POST['selectBtn']) && isset($_POST['selLoc']) && isset($_POST['selCar']) && isset($_POST['month_start']) && isset($_POST['day_start']) && isset($_POST['year_start']) && $_COOKIE['length_ok'] == 1) {
				//echo $_POST['selLoc'];
				$txtLoc = strpos($_POST['selLoc'], " ") + 1;
				$prefix = strtoupper(substr($_POST['selLoc'], $txtLoc, 2));
				$rentID = generateRandomString($prefix);
				$date = $_POST['year_start']."-".$_POST['month_start']."-".$_POST['day_start'];
				$day = date("Y-m-d");
				
				if($date > $day) {
					$dispData = true;
					$query = "INSERT into reservation VALUES (?, ?, ?, ?, ?, 'Y')";
					$stmt = $con->prepare($query);	
					$stmt->bind_param('sssss', $rentID, $_SESSION['MemNo'], $_POST['selCar'], $date, $_POST['res_length']);
					$stmt->execute();
					
					// Get one access code for car rental
					$query = "SELECT AccessCode FROM availablecodes WHERE RentalID IS NULL ORDER BY RAND() LIMIT 1";
					$stmt = $con->prepare($query);	
					$stmt->execute();
					$result = $stmt->get_result();
					$row = $result->fetch_assoc();
					$accessCode = $row['AccessCode'];
					
					$query = "UPDATE availablecodes SET RentalID=? WHERE AccessCode=?";
					$stmt = $con->prepare($query);	
					$stmt->bind_param('ss', $rentID, $accessCode);
					$stmt->execute();
				} else {
					$fail = true;
				}
			} else {
				$fail = true;
			}
			
		?>
		
		<div id="siteHeader"></div>
		<script>
			$(function() {
				$("#siteHeader").load("header.php");
			});
		</script>
		
		<script type="text/javascript">
			function ajaxfunction(parent) {
				$.ajax({
					url: 'populatecar.php?parent=' + parent,
					success: function(data) {
						$("#sub").html(data);
					}
				});
			}
			pic1 = new Image(16, 16); 
			pic1.src="img/loader.gif";
			$(document).ready(function(){
				document.cookie = "length_ok=0";
				$("#res_length").change(function() { 
					var len = $("#res_length").val();
					var mth = $("#month_start").val();
					var da = $("#day_start").val();
					var yr = $("#year_start").val();
					var cvin = $("#sub").val()
					if(len.length >= 1)
					{
						$("#status").html('<img src="img/loader.gif" align="absmiddle">&nbsp;Checking availability...');
						$.ajax({  
							type: "POST",  
							url: "reslengthcheck.php",  
							data: {length: len, month: mth, day: da, year: yr, VIN: cvin},  
							success: function(msg){  
								$("#status").ajaxComplete(function(event, request, settings){ 
									if(msg == 'OK')
									{ 
										document.cookie = "length_ok=1";
										$("#res_length").removeClass('object_error'); // if necessary
										$("#res_length").addClass("object_ok");
										$(this).html('&nbsp;<img src="img/tick.gif" align="absmiddle">');
									}  
									else  
									{  
										$("#res_length").removeClass('object_ok'); // if necessary
										$("#res_length").addClass("object_error");
										$(this).html(msg);
									}  
								});
							} 
						}); 
					}
					else
					{
						$("#status").html('<font color="red">' +
						'The res_length should have at least <strong>4</strong> characters.</font>');
						$("#res_length").removeClass('object_ok'); // if necessary
						$("#res_length").addClass("object_error");
					}
				});
			});
		</script>
		
		<h2><center>Reserve a Car</center></h2>
		<form name='carSelect' id='carSelect' action='reservecar.php' method='post'>
			<table border='0' class="box">
				<tr>
					<td>
						<?php
							// SELECT all current cars
							$query = "SELECT Location FROM parking";
							$stmt = $con->prepare($query);
							$stmt->execute();
							$result = $stmt->get_result();
							
							// Create a select box with each car as an option
							echo '<center><select onchange="ajaxfunction(this.value)" name="selLoc" style="width: 250px"></center>';
							echo '<option selected disabled hidden>--Select Location--</option>';
							while($row = $result->fetch_assoc()) {
								echo '<option value="'.$row['Location'].'">'.$row['Location'].'</option>';
							}
							echo '</select>';
						?>
					</td>
					<td>
						<center><select id="sub" name="selCar" style="width: 250px"></center>
							<option value="none" selected disabled hidden>--Select Car--</option>
						</select>
					</td>
				</tr>
				<tr class="blankRow"></tr>
				<tr>
					<td colspan=2>
						<center><select id="month_start" name="month_start" />
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
					
				<select id="year_start" name="year_start" /></center>
				<option selected disabled hidden>Year</option>
				<option value='2014'>2014</option>       
				<option value='2015'>2015</option>       
				<option value='2016'>2016</option>       
				<option value='2017'>2017</option>       
				<option value='2018'>2018</option>       
			</select>
		</td>
		<tr>			
			<tr class="blankRow"></tr>
			<tr>
				<td><div align="right">Reservation Length:&nbsp;<input id="res_length" size="20" type="number" min="0" onkeypress="return event.charCode >= 48" name="res_length" style="width: 50px"></div></td>
				<td align="left"><div id="status"></div></td>
			</tr>
			<tr class="blankRow"></tr>
			<tr>
				<td colspan=2><center><input type='submit' name='selectBtn' id='selectBtn' value='Reserve'/></center></td>
			</tr>
		</table>
		<table border='1' class="resbox">
			<?php if($dispData) : ?>
			<tr><td><?php echo 'Reservation successful'; ?><br>
			<?php echo 'Your access code is: '.$accessCode; ?></td></tr>
			<?php endif; ?>
			<?php if($fail) : ?>
			<tr><td>Submission Failed<br>
			Please Submit Vaild Information</td></tr>
			<?php endif; ?>
			
		</table>
	</form>
	
</body>
</html>