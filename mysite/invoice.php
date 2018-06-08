<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
	<link rel="stylesheet" href="style.css" />
	<link rel="stylesheet" href="style.css" />
	<head>
		<title>User Control Panel</title>
		<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
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
			height:150px;
			position:fixed;
			margin-left:-175px; /* half of width */
			margin-top:-300px;  /* half of height */
			top:50%;
			left:50%;
		}
		.blankRow {
			height: 20px
		}
	</style>
	
	<body>
		<div id="siteHeader"></div>
		<script>
			$(function() {
				$("#siteHeader").load("header.php")
			});
		</script>
		
		<?php
			//Create a user session or resume an existing one
			session_start();
			
			if(!isset($_SESSION['MemNo'])){
				//User is not logged in. Redirect the browser to the login index.php page and kill this page.
				header("Location: index.php?page=invoice.php");
				die();
			}
		
			// include database connection
			include_once 'config/connection.php'; 
			
			$query = "SELECT Date FROM invoice WHERE MemNo=?";
			$stmt = $con->prepare($query);
			$stmt->bind_Param("s", $_SESSION['MemNo']);
			$stmt->execute();
			$result = $stmt->get_result();
			echo '<form action="invoice.php" method="post">';
			echo '<table border="0" class="box">';
			echo '<tr><td><select name="invdate" select style="width: 400px">';
			echo '<option selected disabled hidden>--Choose Date--</option>';
			while($row = $result->fetch_assoc()) {
				echo '<option>'.$row['Date'].'</option>';
			}
			echo '</select></td></tr>';
			echo '<tr><td><center><input name="submit" type="submit" value="SUBMIT DATE"/></center></td></tr>';
			echo '<tr class="blankRow"></tr>';
			
			if (isset($_POST['invdate'])) {
				// SELECT query to find member's personal info
				$query = "SELECT Date, InvoiceNo, Total, Email, PhoneNo, FName, LName FROM invoice NATURAL JOIN member WHERE MemNo=? AND Date=?";
				$stmt = $con->prepare($query);
				$stmt->bind_Param("ss", $_SESSION['MemNo'], $_POST['invdate']);
				$stmt->execute();
				$result = $stmt->get_result();
				$row = $result->fetch_assoc();
				
				// Save the requested fields
				$number = $row['InvoiceNo'];
				$uname = $row['FName']." ".$row['LName'];
				$uemail = $row['Email'];
				$uphone = $row['PhoneNo'];
				
				
				$query = "SELECT Description, Cost FROM invoicecharges WHERE InvoiceNo=?";
				$stmt = $con->prepare($query);
				$stmt->bind_Param("s", $number);
				$stmt->execute();
				$result = $stmt->get_result();
			}
			
			if(isset($_POST["submit"]) && isset($_POST['invdate'])) {
				$dispInv = true;
				$company = "K-Town Car Share";
				$address = "25 Princess";
				$email = "admin@ktcs.ca";
				$telephone = "(613) 579-5516";
				
				
				$com = "KTCS 2017";
				$pay = "Invoice To:";
				require('u/fpdf.php');
				
				class PDF extends FPDF
				{
					function Header()
					{
						$this->Image("logo/Homepage_banner2.jpg",10,10,80);
						$this->SetFont('Arial','B',12);
						$this->Ln(1);
					}
					function Footer()
					{
						$this->SetY(-15);
						$this->SetFont('Arial','I',8);
						$this->Cell(0,10,'Page '.$this->PageNo().'/{nb}',0,0,'C');
					}
					function ChapterTitle($num, $label)
					{
						$this->SetFont('Arial','',12);
						$this->SetFillColor(200,220,255);
						$this->Cell(0,6,"$num $label",0,1,'L',true);
						$this->Ln(0);
					}
					function ChapterTitle2($num, $label)
					{
						$this->SetFont('Arial','',12);
						$this->SetFillColor(249,249,249);
						$this->Cell(0,6,"$num $label",0,1,'L',true);
						$this->Ln(0);
					}
				}
				
				$pdf = new PDF();
				$pdf->AliasNbPages();
				$pdf->AddPage();
				$pdf->SetFont('Times','',12);
				$pdf->SetTextColor(32);
				$pdf->Cell(0,5,$company,0,1,'R');
				$pdf->Cell(0,5,$address,0,1,'R');
				$pdf->Cell(0,5,$email,0,1,'R');
				$pdf->Cell(0,5,'Tel: '.$telephone,0,1,'R');
				$pdf->Cell(0,30,'',0,1,'R');
				$pdf->SetFillColor(200,220,255);
				$pdf->ChapterTitle('Invoice Number ',$number);
				$pdf->ChapterTitle('Invoice Date ',date('d-m-Y'));
				$pdf->Cell(0,20,'',0,1,'R');
				$pdf->SetFillColor(224,235,255);
				$pdf->SetDrawColor(192,192,192);
				$pdf->Cell(170,7,'Item',1,0,'L');
				$pdf->Cell(20,7,'Price',1,1,'C');
				$total = 0;
				while($row = $result->fetch_assoc()) {
					$item = $row['Description'];
					$price = $row['Cost'];
					$total = $total + $price;
					$pdf->Cell(170,7,$item,1,0,'L',0);
					$pdf->Cell(20,7,'$'.$price,1,1,'C',0);
				}
				$vat = number_format($total * 0.13, 2);
				$total = number_format($total * 1.13, 2);
				
				// Update total
				$query = "UPDATE invoice SET Total=? WHERE MemNo=?";
				$stmt = $con->prepare($query);
				$stmt->bind_Param("ss", $total, $_SESSION['MemNo']);
				$stmt->execute();
				
				$pdf->Cell(0,0,'',0,1,'R');
				$pdf->Cell(170,7,'GST',1,0,'R',0);
				$pdf->Cell(20,7,'$'.$vat,1,1,'C',0);
				$pdf->Cell(170,7,'Total',1,0,'R',0);
				$pdf->Cell(20,7,'$'.$total,1,0,'C',0);
				$pdf->Cell(0,20,'',0,1,'R');
				$pdf->Cell(0,5,$pay,0,1,'L');
				$pdf->Cell(0,5,$uname,0,1,'L');
				$pdf->Cell(0,5,$uemail,0,1,'L');
				$pdf->Cell(0,20,'',0,1,'R');
				$pdf->Cell(190,40,$com,0,0,'C');
				$filename="invoice.pdf";
				$pdf->Output($filename,'F');
				echo '<tr><td><center><a href="invoice.pdf">Download your Invoice</a></center></td></tr>';
				echo '</table></form>';
			}
		?>		
	</body>
</html>
