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
		form {
			display: inline-block;
			text-align: center;
		}
		.box {
			width:460px;
			height:460px;
			position:fixed;
			margin-left:-225px; /* half of width */
			margin-top:-170px;  /* half of height */
			top:50%;
			left:50%;
		}
		.instr {
			width:300px;
			position:fixed;
			text-align: center;
			margin-left:-600px; /* half of width */
			margin-top:-165px;  /* half of height */
			top:50%;
			left:50%;
		}
		.boximg {
			width:450px;
			height:125px;
			position:fixed;
			margin-left:-220px; /* half of width */
			margin-top: -300px;  /* half of height */
			top:50%;
			left:50%;
		}
	</style>
	
	<body>
		<?php
			//Create a user session or resume an existing one
			session_start();
			
			$monthly_rate = 60;
			
			$errorval = 0;
			if(isset($_POST['regBtn'])){
				if(!empty($_POST['password']) && !empty($_POST['dno']) && !empty($_POST['username']) && !empty($_POST['email'])&& !empty($_POST['fname'])&& !empty($_POST['lname'])) {
					//echo $_POST['username'].$_POST['fname'].$_POST['lname'].$_POST['address'].$_POST['phoneno'].$_POST['email'].$_POST['dno'];
					// include database connection
					include_once 'config/connection.php'; 
					$query = "INSERT into member VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
					$stmt = $con->prepare($query);	
					$stmt->bind_param('sssssssi', $_POST['username'], $_POST['fname'], $_POST['lname'], $_POST['address'], $_POST['phoneno'], $_POST['email'], $_POST['dno'], $monthly_rate);
					// Execute the query
					if($stmt->execute()){
						$query = "INSERT into logins VALUES (?, ?)";
						$stmt = $con->prepare($query);	
						$stmt->bind_param('ss', $_POST['username'], md5($_POST['password']));
						// Execute the query
						if($stmt->execute()){
							$_SESSION['MemNo'] = $_POST['username'];
							//Redirect the browser to the profile editing page and kill this page.
							header("Location: profile.php");
						die();
						}
					} else {
						if($stmt->errno == 1062) {
							$errorval = 1;
						}
						//echo 'Shit. <br/>';
						//echo $stmt->error;
					}
				} else {
					$errorval = 2; // User needs to fill in NOT NULLs
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
		
		<img src="img/new-user-register-banner.jpg" class="boximg">
		
		<table border='1' class="instr">
			<tr>
				<th>Monthly Membership Rate</th>
			</tr>
			<tr>
				<td>Special Promo 8% off<br><font color='red'>Was <strike><b>$65</b></strike> now <b>$<?php echo $monthly_rate; ?></b>!</font></td>
			</tr>
			<tr>
				<?php if($errorval == 1) : ?>
				<td>Error: Member Number is already taken</td>
				<?php elseif($errorval == 2) : ?>
				<td>Error: Please fill in required fields</td>
				<?php endif; ?>
			</tr>
		</table>
		
		<form name='registerUser' id='registerUser' action='register.php' method='post'>
			<table border='0' class="box">
				<tr>
					<td><font color='red'>*</font>Member Number</td>
					<td><input type='number' name='username' id='username' /></td>
				</tr>
				<tr>
					<td><font color='red'>*</font>Password</td>
					<td><input type='password' name='password' id='password' /></td>
				</tr>
				<tr>
					<td><font color='red'>*</font>First Name</td>
					<td><input type='text' name='fname' id='fname' /></td>
				</tr>
				<tr>
					<td><font color='red'>*</font>Last Name</td>
					<td><input type='text' name='lname' id='lname' /></td>
				</tr>
				<tr>
					<td>Address</td>
					<td><input type='text' name='address' id='address' /></td>
				</tr>
				<tr>
					<td>Phone Number</td>
					<td><input type='text' name='phoneno' id='phoneno' /></td>
				</tr>
				<tr>
					<td><font color='red'>*</font>Email</td>
					<td><input type='text' name='email' id='email' /></td>
				</tr>
				<tr>
					<td><font color='red'>*</font>Driver's License</td>
					<td><input type='text' name='dno' id='dno' /></td>
				</tr>
				<tr>
					<td></td>
					<td>
						<input type='submit' name='regBtn' id='regBtn' value='Register' /> 
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