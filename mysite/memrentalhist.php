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
			margin-top:-275px;  /* half of height */
			top:50%;
			left:50%;
		}
		.resbox {
			width:1400px;
			height:auto;
			position:fixed;
			margin-left:-700px; /* half of width */
			margin-top:-175px;  /* half of height */
			top:50%;
			left:50%;
			text-align:center;
		}
		#commentSel {
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
		.replybox{
			margin-top:5px;
			margin-bottom:5px;
			width:1050px;
			height:32px;
			-moz-border-radius: 5px;
			border-radius: 5px;
			border:1px solid #ccc;
			margin-left:5px;
			margin-right:5px;
			padding:5px;
		}
	</style>
	
	<body>
	
		<?php
			//Create a user session or resume an existing one
			session_start();
			
			if(!isset($_SESSION['MemNo'])){
				//User is not logged in. Redirect the browser to the login index.php page and kill this page.
				header("Location: index.php?page=memrentalhist.php");
				die();
			}
			
			// include database connection
			include_once 'config/connection.php'; 
			
			$dispData = true;
			
			if(isset($_POST['subBtn']) && isset($_POST['selRating'])) {
				if(!empty($_POST['replyText'])) {
					$query = "UPDATE rentalcomments SET Rating=?, Text=? WHERE RentalID=?";
					$stmt = $con->prepare($query);	
					$stmt->bind_param('sss', $_POST['selRating'], $_POST['replyText'], $_POST['commentSel']);
				} else {
					$query = "UPDATE rentalcomments SET Rating=? WHERE RentalID=?";
					$stmt = $con->prepare($query);	
					$stmt->bind_param('ss', $_POST['selRating'], $_POST['commentSel']);
				}
				
				// Execute the query
				if($stmt->execute()) {
					
				} else {
					echo 'Something Went Wrong. <br/>';
					//echo $stmt->error;
				}
			}
			
		?>
		
		<div id="siteHeader"></div>
		<script>
			$(function() {
				$("#siteHeader").load("header.php")
			});
		</script>
				
		<h2><center>Member Rental History</center></h2>
		<form name='carSelect' id='carSelect' action='memrentalhist.php' method='post'>
			<table border='1' class="resbox"
				<?php
					// Add results table only if there are results
					if($dispData) {
						// SELECT all rental history elements for selected car
						$query = "SELECT * FROM rental NATURAL JOIN rentalcomments WHERE MemNo=?";
						$stmt = $con->prepare($query);
						$stmt->bind_param('s', $_SESSION['MemNo']);
						$stmt->execute();
						$result = $stmt->get_result();
						if($result->num_rows > 0) {
						// Display results in a table
							echo '<tr><th rowspan=2>RentalID</th><th colspan=2>Status</th><th colspan=2>Odometer</th><th colspan=2>Gas</th><th colspan=2>Date</th><th colspan=2>Time</th><th rowspan=2>Fees</th><th rowspan=2>Description</th><th rowspan=2>Rating</th><th rowspan=2>Comment</th><th rowspan=2>Admin Reply</th></tr>';
							echo '<tr><td>PickUp</td><td>DropOff</td><td>PickUp</td><td>DropOff</td><td>PickUp</td><td>DropOff</td><td>PickUp</td><td>DropOff</td><td>PickUp</td><td>DropOff</td></tr>';
							while($row = $result->fetch_assoc()) {
								if(empty($row['FeesDesc'])) {
									$desc = '-';
								} else {
									$desc = $row['FeesDesc'];
								}
								if(empty($row['Text'])) {
									$text = '<input type="radio" id="commentSel" name="commentSel" value='.$row['RentalID'].'>';
								} else {
									$text = $row['Text'];
								}
								if(empty($row['Reply'])) {
									$reply = '-';
								} else {
									$reply = $row['Reply'];
								}
								if(empty($row['Rating'])) {
									$stars = '-';			
								} else {
									$stars = "";
									$numStars = $row['Rating'];
									for($i = 0; $i < 4; $i++) {
										if($numStars > $i) {
											$stars = $stars . '★';
										} else {
											$stars = $stars . '☆';
										}
									}
								}
								echo '<tr><td>'.$row['RentalID'].'</td><td>'.$row['PickUpStatus'].'</td><td>'.$row['DropOffStatus'].'</td><td>'.$row['PickUpOdo'].'</td><td>'.$row['DropOffOdo'].'</td><td>'.$row['PickUpGas'].'</td><td>'.$row['DropOffGas'].'</td><td>'.$row['PickUpDate'].'</td><td>'.$row['DropOffDate'].'</td><td>'.$row['PickUpTime'].'</td><td>'.$row['DropOffTime'].'</td><td>$'.$row['FeesOut'].'</td><td>'.$desc.'</td><td>'.$stars.'</td><td>'.$text.'</td><td>'.$reply.'</td></tr>';
							}
							echo '<tr><td colspan=13><input type="text" name="replyText" id="replyText" class="replybox"/></td>';
							echo '<td><font color="red">*</font><select name="selRating" select style="width: 100px">';
							echo '<option selected disabled hidden>Rating</option>';
							echo '<option value="1">1</option>';
							echo '<option value="2">2</option>';
							echo '<option value="3">3</option>';
							echo '<option value="4">4</option>';
							echo '</select></td>';
							echo '<td colspan=2><input type="submit" name="subBtn" id="subBtn" value="Reply" class="replybtn"/></td></tr>';
						} else {
							// Car has no history yet
							echo '<tr><th>Member has no rental history</th></tr>';
						}
					}
					
				?>
			</table>
		</form>
		
	</body>
</html>