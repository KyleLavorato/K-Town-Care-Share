<html lang="en">
	
	<body>
		
		<?php
			//Create a user session or resume an existing one
			session_start();
		?>
		
			
		<?php
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
		?>
		
		<nav class="navbar navbar-inverse">
			<div class="container-fluid">
				<div class="navbar-header">
					<button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#myNavbar">
						<span class="icon-bar"></span>
						<span class="icon-bar"></span>
						<span class="icon-bar"></span>                        
					</button>
					<a class="navbar-brand" href="home.php">KTCS</a>
					<img src="img/ktown_header.jpg" style="width:50px;height:50px;border:0;" hspace="5">
				</div>
				<div class="collapse navbar-collapse" id="myNavbar">
					<ul class="nav navbar-nav">
						<li class="active"><a href="home.php"> Home</a></li>
						<li class="dropdown">
							<a class="dropdown-toggle" data-toggle="dropdown" href="#">Member Functions<span class="caret"></span></a>
							<ul class="dropdown-menu">
								<li><a href="reservecar.php">Reserve</a></li>
								<li><a href="form.php">Pickup/Dropoff Form</a></li>
								<li><a href="memrentalhist.php">Rental History</a></li>
								<li><a href="invoice.php">Invoices</a></li>
							</ul>
						</li>
						<li><a href="alllocations.php">KTCS Locations</a></li>
						<li><a href="availablecheck.php">Availability Checker</a></li>
					</ul>
					<ul class="nav navbar-nav navbar-right">
						<?php if($admin) : ?>
						<li><a href="admin.php"><span class="glyphicon glyphicon-lock"></span> Admin Control Panel</a></li>
						<?php endif; ?>
						<?php if(isset($_SESSION['MemNo'])) : ?>
						<li><a href="index.php"><span class="glyphicon glyphicon-user"></span><?php echo ' '.$_SESSION['MemNo'];?></a></li>
						<li><a href="index.php?logout=1"><span class="glyphicon glyphicon-log-out"></span> Log Out</a></li>
						<?php else : ?>
						<li><a href="register.php"><span class="glyphicon glyphicon-user"></span> Register</a></li>
						<li><a href="index.php"><span class="glyphicon glyphicon-log-in"></span> Login</a></li>
						<?php endif; ?>
						
					</ul>
				</div>
			</div>
		</nav>
	</body>
</html>			