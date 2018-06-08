<!DOCTYPE HTML>
<html>
	<head>
		<title>Bootstrap Case</title>
		<meta charset="utf-8">
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
		<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.1/jquery.min.js"></script>
		<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
	</head>
	<link rel="stylesheet" href="style.css" />
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
		#formSel {
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
		.lrow {
		outline: thin solid black;
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
				header("Location: index.php?page=form.php");
				die();
			}
			
			// include database connection
			include_once 'config/connection.php'; 
			
			$dispData = false;
			$insert = false;
			$update = false;
			$payUp = false;
			if(isset($_POST['selectBtn']) && isset($_POST['formSel']) && isset($_POST['selRes']) && isset($_POST['statusSel']) && !empty($_POST['gas_val']) && !empty($_POST['odo_val'])) {
				$dispData = true;
				$day = date("Y-m-d");
				$invDate = date("Y-m").'-01';
				$time = date("H:i:s");
				$txtLoc = strpos($_POST['selRes'], "@");
				$rentalID_val = substr($_POST['selRes'], 0, $txtLoc);
				$vin_val = substr($_POST['selRes'], $txtLoc + 1, strlen($_POST['selRes']) - 1);
				if(!$_POST['formSel']) {  // Car pickup form
					// Add a new form entry for pickup
					$query = "INSERT into rental VALUES (?, ?, ?, ?, ?, ?, ?, ?, NULL, NULL, NULL, NULL, NULL, NULL, NULL)";
					$stmt = $con->prepare($query);	
					$stmt->bind_param('ssssiiss', $rentalID_val, $_SESSION['MemNo'], $vin_val, $_POST['statusSel'], $_POST['odo_val'], $_POST['gas_val'], $day, $time);
					if($stmt->execute()) {
						$insert = true;
					}
					
					// Add a comments entry for this rental
					$query = "INSERT into rentalcomments VALUES (?, ?, ?, NULL, NULL, NULL)";
					$stmt = $con->prepare($query);	
					$stmt->bind_param('sss', $_SESSION['MemNo'], $vin_val, $rentalID_val);
					$stmt->execute();
					
					// Update the car to be not available
					$query = "UPDATE car SET Available='N' WHERE VIN=?";
					$stmt = $con->prepare($query);	
					$stmt->bind_param('s', $vin_val);
					$stmt->execute();
				} else {  // Car return form
					// Find if an entry already exists
					$query = "SELECT * FROM rental WHERE RentalID=?";
					$stmt = $con->prepare($query);
					$stmt->bind_param('s', $rentalID_val);
					$stmt->execute();
					$result = $stmt->get_result();
					$row = $result->fetch_assoc();
					if($result->num_rows != 0) {
						// Select the initial data for comparison
						$query = "SELECT PickUpStatus, PickUpGas FROM rental WHERE RentalID=?";
						$stmt = $con->prepare($query);
						$stmt->bind_param('s', $rentalID_val);
						$stmt->execute();
						$result = $stmt->get_result();
						$row = $result->fetch_assoc();
						
						$damages = 0;
						$gas_diff = $_POST['gas_val'] - $row['PickUpGas'];
						if($gas_diff < -5) {
							$damages += abs($gas_diff * 2);
						}
						if(($row['PickUpStatus'] == 'N' || $row['PickUpStatus'] == 'D') && $_POST['statusSel'] == 'NR') {
							$damages += 650;  // $650 deductible
						}
						if($row['PickUpStatus'] == 'N' && $_POST['statusSel'] == 'D') {
							$damages += 150;
						}
						// Update dropoff
						$query = "UPDATE rental SET DropOffStatus=?, DropOffOdo=?, DropOffGas=?, DropOffDate=?, DropOffTime=?, FeesOut=? WHERE RentalID=?";
						$stmt = $con->prepare($query);	
						$stmt->bind_param('siissis', $_POST['statusSel'], $_POST['odo_val'], $_POST['gas_val'], $day, $time, $damages, $rentalID_val);
						if($stmt->execute()) {
							$query = "UPDATE reservation SET Active='N' WHERE RentalID=?";
							$stmt = $con->prepare($query);
							$stmt->bind_param('s', $rentalID_val);
							$stmt->execute();
							$update = true;
							if($damages > 0) {
								$payUp = true;
							}
						}
						
						// Find the cost/day and length of reservation for the car
						$query = "SELECT Fee, ResLength, MonthlyFee FROM car NATURAL JOIN reservation NATURAL JOIN member WHERE MemNo=? AND VIN=?";
						$stmt = $con->prepare($query);
						$stmt->bind_param('ss', $_SESSION['MemNo'], $vin_val);
						$stmt->execute();
						$result = $stmt->get_result();
						$row = $result->fetch_assoc();
						$rentalCost = $row['Fee'] * $row['ResLength'];
						$monthFee = $row['MonthlyFee'];
						
						// Check if a current invoice for this month exists
						$query = "SELECT InvoiceNo FROM invoice WHERE MemNo=? AND Date=?";
						$stmt = $con->prepare($query);
						$stmt->bind_param('ss', $_SESSION['MemNo'], $invDate);
						$stmt->execute();
						$result = $stmt->get_result();
						if($result->num_rows == 0) {
							// Create new invoice entry for this month
							$invID = generateRandomString("IN");
							$query = "INSERT into invoice VALUES (?, ?, ?, NULL)";
							$stmt = $con->prepare($query);
							$stmt->bind_param('sss', $invID, $invDate, $_SESSION['MemNo']);
							$stmt->execute();
							
							// Add user's monthly fee to invoice
							$chargeTxt = "Monthly membership fee";
							$query = "INSERT into invoicecharges VALUES (?, ?, ?)";
							$stmt = $con->prepare($query);
							$stmt->bind_param('ssi', $invID, $chargeTxt, $monthFee);
							$stmt->execute();
						} else {
							// Entry exists so add to that invoice
							$row = $result->fetch_assoc();
							$invID = $row['InvoiceNo'];
						}
						// Add charge for the car rental
						$chargeTxt = "Car rental ".$day;
						$query = "INSERT into invoicecharges VALUES (?, ?, ?)";
						$stmt = $con->prepare($query);
						$stmt->bind_param('ssi', $invID, $chargeTxt, $rentalCost);
						$stmt->execute();
						// Add charges for damages if they exist
						if($damages > 0) {
							$chargeTxt = "Car damages on rental ".$rentalID_val;
							$query = "INSERT into invoicecharges VALUES (?, ?, ?)";
							$stmt = $con->prepare($query);
							$stmt->bind_param('ssi', $invID, $chargeTxt, $damages);
							$stmt->execute();
						}
					}
				}	
			}
			if($insert || $update) {
				$message = "Form successfully submitted";
			} else {
				$message = "Form submission unsuccessful";
			}
		?>
		
		<div id="siteHeader"></div>
		<script>
			$(function() {
				$("#siteHeader").load("header.php");
			});
		</script>
		
		
		<h2><center>Form</center></h2>
		<form name='carSelect' id='carSelect' action='form.php' method='post'>
			<table border='0' class="box">
				<tr>
					<td width="50%">
						<center><font color='red'>*</font>Pickup <input type="radio" id="formSel" name="formSel" value="0"></center>
					</td>
					<td width="50%">
						<center><font color='red'>*</font>Dropoff <input type="radio" id="formSel" name="formSel" value="1"></center>
					</td>
				</tr>
				<tr class="blankRow"></tr>
				<tr>
					<td colspan=2>
						<?php
							// SELECT all current reservations
							$query = "SELECT RentalID, Date, VIN FROM reservation WHERE Active='Y' AND MemNo=?";
							$stmt = $con->prepare($query);
							$stmt->bind_param('s', $_SESSION['MemNo']);
							$stmt->execute();
							$result = $stmt->get_result();
							
							// Create a select box with each car as an option
							echo '<center><font color="red">*</font><select name="selRes" style="width: 500px"></center>';
							if($result->num_rows == 0) {
								echo '<option selected disabled hidden>No Active Reservations</option>';
							} else {
								echo '<option selected disabled hidden>--Select Reservation--</option>';
							}
							while($row = $result->fetch_assoc()) {
								$val = $row['RentalID'].'@'.$row['VIN'];
								echo '<option value="'.$val.'">'.$row['RentalID'].', '.$row['Date'].'</option>';
							}
							echo '</select>';
						?>
					</td>
				</tr>
				<tr class="blankRow"></tr>
				<tr>
					<td colspan=2>
						<center><font color='red'>*</font><select id="statusSel" name="statusSel" />
							<option selected disabled hidden>Select Status</option>
							<option value='N'>Normal</option>       
							<option value='D'>Damaged</option>       
							<option value='NR'>Not Running</option>       
						</select>
					</td>
				</tr>
				<tr class="blankRow"></tr>			
				<tr>
					<td align="center" colspan=2><font color='red'>*</font>Gas in Tank:<input id="gas_val" size="20" type="number" min="0" max="100" onkeypress="return event.charCode >= 48" name="gas_val" style="width: 50px"><b>%</b></td>
				</tr>
				<tr class="blankRow"></tr>
				<tr>
					<td align="center" colspan=2><font color='red'>*</font>Odometer Reading:<input id="odo_val" size="20" type="number" min="0" onkeypress="return event.charCode >= 48" name="odo_val" style="width: 100px"></td>
				</tr>
				<tr class="blankRow"></tr>
				<tr>
					<td colspan=2><center><input type='submit' name='selectBtn' id='selectBtn' value='Submit'/></center></td>
				</tr>
				<tr class="blankRow"></tr>
				<tr class="lrow">
				<td colspan=2><font size="1"><b>Warning:</b> Falsifying information on this form is a violation of the ToS and can result in member account suspension or additional fees. Discrepencies in gas tank amount greater than 5% will result in a charge to the user account of $2/%. Members are responsible for damages to the car during use and may be charged a deductible if damaged.</font></td>
				</tr>
				<?php if($dispData) : ?>
				<tr class="blankRow"></tr>
				<tr><td colspan=2><center><?php echo $message; ?></center></td></tr>
				<?php if($payUp) : ?>
				<tr class="blankRow"></tr>
				<tr><td colspan=2 class="lrow"><center><?php echo 'You have been charged fees of $'.$damages.' for damages or not enough gas. You case will be manually reviewed and some of the fee may be refunded if the repair does not use the full amount'; ?></center></td></tr>
				<?php endif; ?>
				<?php endif; ?>
			</table>
		</form>
	</body>
</html>						