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
		.box {
			width:345px;
			height:120px;
			position:fixed;
			margin-left:-175px; /* half of width */
			margin-top:-275px;  /* half of height */
			top:50%;
			left:50%;
		}
		input {
			width:200px
		}
	</style>
	
	<body>
		<?php
			//Create a user session or resume an existing one
			session_start();
		?>
		
		<div id="siteHeader"></div>
		<script>
			$(function() {
				$("#siteHeader").load("header.php")
			});
		</script>
		
		<?php
			//check if the user clicked the logout link and set the logout GET parameter
			if(isset($_GET['logout'])){
				//Destroy the user's session.
				$_SESSION['MemNo']=null;
				session_destroy();
			}
		?>
		
		
		<?php
			//check if the user is already logged in and has an active session
			if(isset($_SESSION['MemNo'])){
				//Redirect the browser to the profile editing page and kill this page.
				header("Location: profile.php");
				die();
			}
		?>
		
		<?php
			
			
			//check if the login form has been submitted
			if(isset($_POST['loginBtn'])) {
				
				// include database connection
				include_once 'config/connection.php'; 
				
				// SELECT query
				$query = "SELECT MemNo, Password FROM logins WHERE MemNo=? AND Password=?";
				
				// prepare query for execution
				if($stmt = $con->prepare($query)) {
					
					// bind the parameters. This is the best way to prevent SQL injection hacks.
					$stmt->bind_Param("ss", $_POST['username'], md5($_POST['password']));
					
					// Execute the query
					$stmt->execute();
					
					/* resultset */
					$result = $stmt->get_result();
					
					// Get the number of rows returned
					$num = $result->num_rows;;
					
					if($num>0) {
						//If the username/password matches a user in our database
						//Read the user details
						$myrow = $result->fetch_assoc();
						//Create a session variable that holds the user's id
						$_SESSION['MemNo'] = $myrow['MemNo'];
						//Redirect the browser to the profile editing page and kill this page.
						if(isset($_GET['page'])) {
							header("Location: ".$_GET['page']);
						} else {
							header("Location: profile.php");
						}
						die();
					} else {
						//If the username/password doesn't matche a user in our database
						// Display an error message and the login form
						echo "Failed to login";
					}
				}
			}
			
		?>
		
		<!-- dynamic content will be here -->
		<?php 
		if(isset($_GET['page'])) {
			echo "<form name='login' id='login' action='index.php?page=".$_GET['page']."' method='post'>";
		} else {
			echo "<form name='login' id='login' action='index.php' method='post'>";
		}
		?>
		
			<table border='0' class="box">
				<tr>
					<td>Username</td>
					<td><input type='text' name='username' id='username' /></td>
				</tr>
				<tr>
					<td>Password</td>
					<td><input type='password' name='password' id='password' /></td>
				</tr>
				<tr>
					<td></td>
					<td>
						<input type='submit' id='loginBtn' name='loginBtn' value='Log In' /> 
					</td>
				</tr>
			</table>
		</form>
		
	</body>
</html>				