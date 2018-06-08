<!DOCTYPE HTML>
<html>
    <link rel="stylesheet" href="style.css" />
	<head>
		<title>User Control Panel</title>
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
		.lbox {
			width:320px;
			height:400px;
			position:fixed;
			margin-left:-400px; /* half of width */
			margin-top:-300px;  /* half of height */
			top:50%;
			left:50%;
		}
		.rbox {
			width:340px;
			height:230px;
			position:fixed;
			margin-left:150px; /* half of width */
			margin-top:-185px;  /* half of height */
			top:50%;
			left:50%;
		}
		.blankRow {
		height: 20px
		}
		.lrow {outline: thin solid black;}
		input {
		width:200px
		}
	</style>
	
	<body>
		<?php
			//Create a user session or resume an existing one
			session_start();
			
			// include database connection
			include_once 'config/connection.php';
		?>
		
		<div id="siteHeader"></div>
		<script>
			$(function() {
				$("#siteHeader").load("header.php")
			});
		</script>
		
		<?php
			
			$dispPro = false;
			$proFail = false;
			if(isset($_POST['updateBtn']) && isset($_SESSION['MemNo'])){ 
				
				$query = "UPDATE member SET Address=?, PhoneNo=?, Email=? WHERE MemNo=?";
				
				$stmt = $con->prepare($query);	$stmt->bind_param('ssss', $_POST['address'], $_POST['phoneno'], $_POST['email'], $_SESSION['MemNo']);
				// Execute the query
				if($stmt->execute()){
					$dispPro = true;
				} else {
					$proFail = true;
				}
			}
			
			$fail = false;
			$dispPass = false;
			if(isset($_POST['passBtn']) && !empty($_POST['cpass']) && !empty($_POST['npass']) && !empty($_POST['npass2'])) {
				$query = "SELECT Password FROM logins WHERE MemNo=?";
				$stmt = $con->prepare($query);
				$stmt->bind_Param("s", $_SESSION['MemNo']);
				$stmt->execute();
				$result = $stmt->get_result();
				$row = $result->fetch_assoc();
				if($_POST['npass'] == $_POST['npass2'] && md5($_POST['cpass']) == $row['Password']) {
					$query = "UPDATE logins SET Password=? WHERE MemNo=?";
					$stmt = $con->prepare($query);
					$stmt->bind_Param("ss", md5($_POST['npass']), $_SESSION['MemNo']);
					$stmt->execute();
					$dispPass = true;
				} else {
					$fail = true;
				}
			}
			
			if(isset($_SESSION['MemNo'])){
				
				// SELECT query
				$query = "SELECT MemNo, FName, LName, Address, PhoneNo, Email, DriverNo, MonthlyFee FROM member WHERE MemNo=?";
				
				// prepare query for execution
				$stmt = $con->prepare($query);
				
				// bind the parameters. This is the best way to prevent SQL injection hacks.
				$stmt->bind_Param("s", $_SESSION['MemNo']);
				
				// Execute the query
				$stmt->execute();
				
				// results 
				$result = $stmt->get_result();
				
				// Row data
				$myrow = $result->fetch_assoc();
				} else {
				//User is not logged in. Redirect the browser to the login index.php page and kill this page.
				header("Location: index.php");
				die();
			}
		?>
		<!-- dynamic content will be here -->
		<form name='editProfile' id='editProfile' action='profile.php' method='post'>
			<table border='0' class='lbox'>
				<tr>
					<td>Username</td>
					<td><input type='text' name='username' id='username' value=<?php echo $myrow['MemNo']; ?> disabled  /></td>
				</tr>
				<tr>
					<td>First Name</td>
					<td><input type='text' name='fname' id='fname' value=<?php echo $myrow['FName']; ?> disabled /></td>
				</tr>
				<tr>
					<td>Last Name</td>
					<td><input type='text' name='lname' id='lname' value=<?php echo $myrow['LName']; ?> disabled /></td>
				</tr>
				<tr>
					<td>Address</td>
					<?php echo "<td><input type='text' name='address' id='address' value='".$myrow['Address']."'/></td>"; ?>
				</tr>
				<tr>
					<td>Phone Number</td>
					<td><input type='text' name='phoneno' id='phoneno' value=<?php echo $myrow['PhoneNo']; ?> /></td>
				</tr>
				<tr>
					<td>Email</td>
					<td><input type='text' name='email' id='email' value=<?php echo $myrow['Email']; ?> /></td>
				</tr>
				<tr>
					<td>Driver's License</td>
					<td><input type='text' name='dno' id='dno' value=<?php echo $myrow['DriverNo']; ?> disabled /></td>
				</tr>
				<tr>
					<td></td>
					<td>
						<input type='submit' name='updateBtn' id='updateBtn' value='Update Profile' /> 
					</td>
				</tr>
				<?php
					if($proFail) {
						echo '<tr><td colspan=2>Profile update failed</td></tr>';
					}
					if($dispPro) {
						echo '<tr><td colspan=2>Profile update successful</td></tr>';
					}
				?>
				<tr class="blankRow"></tr>
				<tr>
					<td class="lrow" colspan=2><center>Your monthly fee is: $<?php echo $myrow['MonthlyFee']; ?></center></td>
				</tr>
			</table>
			
			<table border='0' class='rbox'>
				<tr>
					<td>Current Password</td>
					<td><input type='password' name='cpass' id='cpass'/></td>
				</tr>
				<tr>
					<td>New Password</td>
					<td><input type='password' name='npass' id='npass'/></td>
				</tr>
				<tr>
					<td>Confirm Password</td>
					<td><input type='password' name='npass2' id='npass2'/></td>
				</tr>
				<tr>
					<td></td>
					<td>
						<input type='submit' name='passBtn' id='passBtn' value='Update Password' /> 
					</td>
				</tr>
				<?php
				echo $fail;
					if($fail) {
						echo '<tr><td colspan=2>Current password wrong or new passwords do not match</td></tr>';
					}
					if($dispPass) {
						echo '<tr><td colspan=2>Password update successful</td></tr>';
					}
				?>
			</table>
		</form>
		
	</body>
</html>			