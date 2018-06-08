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
		.replybox{
			margin-top:5px;
			margin-bottom:5px;
			width:990px;
			height:32px;
			-moz-border-radius: 5px;
			border-radius: 5px;
			border:1px solid #ccc;
			margin-left:5px;
			margin-right:5px;
			padding:5px;
		}
		.replybtn{
			margin-top:5px;
			margin-bottom:5px;
			width:125px;
			height:32px;
			-moz-border-radius: 5px;
			border-radius: 5px;
			border:1px solid #ccc;
			margin-left:5px;
			margin-right:5px;
			padding:5px;
		}
		#replySel {
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
			
			
			if(isset($_POST['subBtn']) && isset($_POST['replySel']) && !empty($_POST['replyText'])) {
				$query = "UPDATE rentalcomments SET Reply=? WHERE RentalID=?";
				$stmt = $con->prepare($query);	
				$stmt->bind_param('ss', $_POST['replyText'], $_POST['replySel']);
				// Execute the query
				if($stmt->execute()) {
					
				} else {
					echo 'Something Went Wrong. <br/>';
					//echo $stmt->error;
				}
			}
			
			// SELECT all cars in the selected parking area that are available and get their reservation info
			$query = "SELECT * FROM rentalcomments WHERE Reply IS NULL AND Rating IS NOT NULL";
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
				
		<h2><center>Comments Without Replies</center></h2>
		<form name='commentSelect' id='commentSelect' action='commentreply.php' method='post'>
			<table border='1' class="resbox"
				<?php
					if($result->num_rows > 0) {
						// Display results in a table
						echo '<tr><th>RentalID</th><th>Member Number</th><th>VIN</th><th>Rating</th><th>Comment</th><th>Reply</th></tr>';
						while($row = $result->fetch_assoc()) {
							$stars = "";
							$numStars = $row['Rating'];
							for($i = 0; $i < 4; $i++) {
								if($numStars > $i) {
									$stars = $stars . '★';
								} else {
									$stars = $stars . '☆';
								}
							}
							echo '<tr><td>'.$row['RentalID'].'</td><td>'.$row['MemNo'].'</td><td>'.$row['VIN'].'</td><td>'.$stars.'</td><td>'.$row['Text'].'</td><td><input type="radio" id="replySel" name="replySel" value='.$row['RentalID'].'></td></tr>';
						}
						echo '<tr><td colspan=5><input type="text" name="replyText" id="replyText" class="replybox"/></td><td><input type="submit" name="subBtn" id="subBtn" value="Reply" class="replybtn"/></td></tr>';
					} else {
						echo '<tr><th>No comments currently need replies</th></tr>';
					}
				?>
			</table>
		</form>
	</body>
</html>