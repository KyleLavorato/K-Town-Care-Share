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
		.box {
			width:460px;
			height:460px;
			position:fixed;
			margin-left:-175px; /* half of width */
			margin-top:-200px;  /* half of height */
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
		img {
			width:64px;
			height:64px;
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
		
			if(isset($_POST['addCarBtn'])) {
				header("Location: addcar.php");
				die();
			}
			if(isset($_POST['rentalHistBtn'])) {
				header("Location: rentalhistory.php");
				die();
			}
			if(isset($_POST['locationChkBtn'])) {
				header("Location: locationcheck.php");
				die();
			}
			if(isset($_POST['5000Btn'])) {
				header("Location: 5000miles.php");
				die();
			}
			if(isset($_POST['hlRentalsBtn'])) {
				header("Location: mostrentals.php");
				die();
			}
			if(isset($_POST['damagedBtn'])) {
				header("Location: damagedcars.php");
				die();
			}
			if(isset($_POST['resChkBtn'])) {
				header("Location: reservations.php");
				die();
			}
			if(isset($_POST['commentReplyBtn'])) {
				header("Location: commentreply.php");
				die();
			}
			
		?>
		
		<div id="siteHeader"></div>
		<script>
			$(function() {
				$("#siteHeader").load("header.php")
			});
		</script>
		
		<center><h1><img src="img/admin.png">Admin Control Panel</h1></center>
		
		<form name='buttonSelect' id='buttonSelect' action='admin.php' method='post'>
			<table border='0' class="box">
				<tr>
					<td><input type='submit' name='addCarBtn' id='addCarBtn' value='Add Car'/></td>
				</tr>
				<tr>
					<td><input type='submit' name='rentalHistBtn' id='rentalHistBtn' value='Rental History'/></td>
				</tr>
				<tr>
					<td><input type='submit' name='locationChkBtn' id='locationChkBtn' value='Location Check'/></td>
				</tr>
				<tr>
					<td><input type='submit' name='5000Btn' id='5000Btn' value='5000 Km'/></td>
				</tr>
				<tr>
					<td><input type='submit' name='hlRentalsBtn' id='hlRentalsBtn' value='High/Low Rentals'/></td>
				</tr>
				<tr>
					<td><input type='submit' name='damagedBtn' id='damagedBtn' value='Damaged Cars'/></td>
				</tr>
				<tr>
					<td><input type='submit' name='resChkBtn' id='resChkBtn' value='Reservations'/></td>
				</tr>
				<tr>
					<td><input type='submit' name='commentReplyBtn' id='commentReplyBtn' value='Comment Reply'/></td>
				</tr>
			</table>
		</form>
		
	</body>
</html>