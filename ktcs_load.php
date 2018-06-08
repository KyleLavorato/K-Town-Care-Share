<html>
<head><title>Load KTCS Database</title></head>
<body>

<?php
/* Program: ktcs_load.php
 * Desc:    Creates and loads the KTCS database tables with 
 *          sample data and adds constraints.
 */
 
 $host = "localhost";
 $user = "cisc332";
 $password = "cisc332password";
 $database = "ktcs";
 $cxn = mysqli_connect($host,$user,$password, $database);
 // Check connection
 if (mysqli_connect_errno())
  {
  echo "Failed to connect to MySQL: " . mysqli_connect_error();
  die();
  }
	
	/////////////////////
	// Drop Old Tables //
	/////////////////////
	
	mysqli_query($cxn,"drop table maintenanceHistory;");
	mysqli_query($cxn,"drop table logins;");
	mysqli_query($cxn,"drop table rentalComments;");
	mysqli_query($cxn,"drop table rental;");
	mysqli_query($cxn,"drop table invoice;");
	mysqli_query($cxn,"drop table availablecodes;");
	mysqli_query($cxn,"drop table invoicecharges;");
	mysqli_query($cxn,"drop table invoice;");
	mysqli_query($cxn,"drop table reservation;");
	mysqli_query($cxn,"drop table car;");
	mysqli_query($cxn,"drop table parking;");
	mysqli_query($cxn,"drop table member;");
	echo "Dropped All<br /><br />";
  
  
  
	///////////////////
	// Create Tables //
	///////////////////
	
	echo "<hr /><u>Create Tables</u><br /><br />";
	mysqli_query($cxn, "create table parking
	(Location	varchar(40) NOT NULL, 
	 NumSpaces	int, 
	 NumCars	int,
	 primary key (Location)
	);");
	echo "Parking created.<br />";
	echo "Error: ", mysqli_error($cxn), "<br /><br />";
	
	mysqli_query($cxn, "create table member
	(MemNo		char(8) NOT NULL, 
	 FName		varchar(20), 
	 LName		varchar(20),
	 Address	varchar(40),
	 PhoneNo	char(13),
	 Email		varchar(40) NOT NULL,
	 DriverNo	char(17) NOT NULL,
	 MonthlyFee	int NOT NULL,
	 primary key (MemNo)
	);");
	echo "Member created.<br />";
	echo "Error: ", mysqli_error($cxn), "<br /><br />";
	
	mysqli_query($cxn, "create table logins
	(MemNo		char(8) NOT NULL, 
	 Password	varchar(40) NOT NULL, 
	 foreign key (MemNo) references member(MemNo)
	);");
	echo "Logins created.<br />";
	echo "Error: ", mysqli_error($cxn), "<br /><br />";
	
	mysqli_query($cxn, "create table car
	(VIN		char(17) NOT NULL, 
	 Make		varchar(15), 
	 Model		varchar(20),
	 Year		int,
	 Location	varchar(40) NOT NULL,
	 Fee		int NOT NULL,
	 Available	char(1) NOT NULL,
	 primary key (VIN),
	 foreign key (Location) references parking(Location)
	);");
	echo "Car created.<br />";
	echo "Error: ", mysqli_error($cxn), "<br /><br />";
	
	mysqli_query($cxn, "create table maintenanceHistory
	(VIN			char(17) NOT NULL, 
	 Date			date, 
	 Cost			int,
	 OdoReading		int NOT NULL,
	 Type			char(2),
	 Description	varchar(250),
	 foreign key (VIN) references car(VIN)
	);");
	echo "MaintenanceHistory created.<br />";
	echo "Error: ", mysqli_error($cxn), "<br /><br />";
	
	mysqli_query($cxn, "create table reservation
	(RentalID		char(8) NOT NULL, 
	 MemNo			char(8) NOT NULL, 
	 VIN			char(17) NOT NULL,
	 Date			date,
	 ResLength		int,
	 Active			char(1),
	 primary key (RentalID),
	 foreign key (MemNo) references member(MemNo),
	 foreign key (VIN) references car(VIN)
	);");
	echo "Reservation created.<br />";
	echo "Error: ", mysqli_error($cxn), "<br /><br />";
	
	mysqli_query($cxn, "create table availablecodes
	(AccessCode		char(8) NOT NULL,
	 RentalID		char(8) DEFAULT NULL,
	 primary key (AccessCode),
	 foreign key (RentalID) references reservation(RentalID)
	);");
	echo "Available Codes created.<br />";
	echo "Error: ", mysqli_error($cxn), "<br /><br />";
	
	mysqli_query($cxn, "create table invoice
	(InvoiceNo		char(8) NOT NULL,
	 Date			date NOT NULL,
	 MemNo			char(8) NOT NULL,
	 Total			varchar(8),
	 primary key (InvoiceNo),
	 foreign key (MemNo) references member(MemNo)
	);");
	echo "Invoice created.<br />";
	echo "Error: ", mysqli_error($cxn), "<br /><br />";
	
	mysqli_query($cxn, "create table invoicecharges
	(InvoiceNo		char(8) NOT NULL,
	 Description	varchar(250),
	 Cost			int NOT NULL,
	 foreign key (InvoiceNo) references invoice(InvoiceNo)
	);");
	echo "Reservation created.<br />";
	echo "Error: ", mysqli_error($cxn), "<br /><br />";
	
	mysqli_query($cxn, "create table rental
	(RentalID		char(8) UNIQUE,
	 MemNo			char(8) NOT NULL, 
	 VIN			char(17) NOT NULL,
	 PickUpStatus	char(2) NOT NULL,
	 PickUpOdo		int NOT NULL,
	 PickUpGas		int NOT NULL,
	 PickUpDate		date NOT NULL,
	 PickUpTime		time NOT NULL,
	 DropOffStatus	char(2),
	 DropOffOdo		int,
	 DropOffGas		int,
	 DropOffDate	date,
	 DropOffTime	time,
	 FeesOut		int,
	 FeesDesc		varchar(250),
	 foreign key (RentalID) references reservation(RentalID),
	 foreign key (MemNo) references member(MemNo),
	 foreign key (VIN) references car(VIN)
	);");
	echo "Rental created.<br />";
	echo "Error: ", mysqli_error($cxn), "<br /><br />";
	
	mysqli_query($cxn, "create table rentalComments
	(MemNo		char(8) NOT NULL, 
	 VIN		char(17) NOT NULL, 
	 RentalID	char(8) NOT NULL,
	 Rating		char(1), 
	 Text		varchar(250), 
	 Reply		varchar(250), 
	 foreign key (MemNo) references member(MemNo),
	 foreign key (VIN) references car(VIN),
	 foreign key (RentalID) references rental(RentalID)
	);");
	echo "RentalComments created.<br />";
	echo "Error: ", mysqli_error($cxn), "<br /><br />";
	
	
	
	////////////////////
	// Create Trigers //
	////////////////////
	
	echo "<hr /><u>Create Triggers</u><br /><br />";
	
	// Rating must be between 1-4
	mysqli_query($cxn, "
	CREATE TRIGGER chk_ins_rating BEFORE INSERT ON rentalComments
		FOR EACH ROW BEGIN
		IF (new.Rating!='1' AND new.Rating!='2' AND new.Rating!='3' AND new.Rating!='4')
		THEN
			SIGNAL sqlstate '45000'
			set message_text = 'Rating must be between 0-4!';
		END IF;
		END;
	");
	echo "chk_ins_rating trigger created.<br />";
	echo "Error: ", mysqli_error($cxn), "<br /><br />";
	mysqli_query($cxn, "
	CREATE TRIGGER chk_upd_rating BEFORE UPDATE ON rentalComments
		FOR EACH ROW BEGIN
		IF (new.Rating!='1' AND new.Rating!='2' AND new.Rating!='3' AND new.Rating!='4')
		THEN
			SIGNAL sqlstate '45000'
			set message_text = 'Rating must be between 0-4!';
		END IF;
		END;
	");
	echo "chk_upd_rating trigger created.<br />";
	echo "Error: ", mysqli_error($cxn), "<br /><br />";
	
	// Reservations can be for a max of 14 days
	mysqli_query($cxn, "
	CREATE TRIGGER chk_ins_reslength BEFORE INSERT ON reservation
		FOR EACH ROW BEGIN
		IF (new.ResLength>14)
		THEN
			SIGNAL sqlstate '45000'
			set message_text = 'Reservations can be for a maximum of 14 days!';
		END IF;
		END;
	");
	echo "chk_ins_reslength trigger created.<br />";
	echo "Error: ", mysqli_error($cxn), "<br /><br />";
	mysqli_query($cxn, "
	CREATE TRIGGER chk_upd_reslength BEFORE UPDATE ON reservation
		FOR EACH ROW BEGIN
		IF (new.ResLength>14)
		THEN
			SIGNAL sqlstate '45000'
			set message_text = 'Reservations can be for a maximum of 14 days!';
		END IF;
		END;
	");
	echo "chk_upd_reslength trigger created.<br />";
	echo "Error: ", mysqli_error($cxn), "<br /><br />";
	
	// Car status must be one of Damaged (D), Not Running (NR) or Normal (N)
	mysqli_query($cxn, "
	CREATE TRIGGER chk_ins_pickup_status BEFORE INSERT ON rental
		FOR EACH ROW BEGIN
		IF (new.PickUpStatus!='D' AND new.PickUpStatus!='NR' AND new.PickUpStatus!='N' AND 
			new.DropOffStatus!='D' AND new.DropOffStatus!='NR' AND new.DropOffStatus!='N')
		THEN
			SIGNAL sqlstate '45000'
			set message_text = 'Car status must be one of Damaged (D), Not Running (NR) or Normal (N)';
		END IF;
		END;
	");
	echo "chk_ins_pickup_status trigger created.<br />";
	echo "Error: ", mysqli_error($cxn), "<br /><br />";
	mysqli_query($cxn, "
	CREATE TRIGGER chk_upd_pickup_status BEFORE UPDATE ON rental
		FOR EACH ROW BEGIN
		IF (new.PickUpStatus!='D' AND new.PickUpStatus!='NR' AND new.PickUpStatus!='N' AND 
			new.DropOffStatus!='D' AND new.DropOffStatus!='NR' AND new.DropOffStatus!='N')
		THEN
			SIGNAL sqlstate '45000'
			set message_text = 'Car status must be one of Damaged (D), Not Running (NR) or Normal (N)';
		END IF;
		END;
	");
	echo "chk_upd_pickup_status trigger created.<br />";
	echo "Error: ", mysqli_error($cxn), "<br /><br />";
	
	// Maintenance type must be one of Scheduled (S), Repair (R) or Body Work (BW)
	mysqli_query($cxn, "
	CREATE TRIGGER chk_ins_maintenancetype BEFORE INSERT ON maintenanceHistory
		FOR EACH ROW BEGIN
		IF (new.Type!='S' AND new.Type!='R' AND new.Type!='BW')
		THEN
			SIGNAL sqlstate '45000'
			set message_text = 'Maintenance type must be one of Scheduled (S), Repair (R) or Body Work (BW)';
		END IF;
		END;
	");
	echo "chk_ins_maintenancetype trigger created.<br />";
	echo "Error: ", mysqli_error($cxn), "<br /><br />";
	mysqli_query($cxn, "
	CREATE TRIGGER chk_upd_maintenancetype BEFORE UPDATE ON maintenanceHistory
		FOR EACH ROW BEGIN
		IF (new.Type!='S' AND new.Type!='R' AND new.Type!='BW')
		THEN
			SIGNAL sqlstate '45000'
			set message_text = 'Maintenance type must be one of Scheduled (S), Repair (R) or Body Work (BW)';
		END IF;
		END;
	");
	echo "chk_upd_car_status trigger created.<br />";
	echo "Error: ", mysqli_error($cxn), "<br /><br />";
	
	// Car availability must be either (Y)es or N(o)
	mysqli_query($cxn, "
	CREATE TRIGGER chk_ins_available BEFORE INSERT ON car
		FOR EACH ROW BEGIN
		IF (new.Available!='Y' AND new.Available!='N')
		THEN
			SIGNAL sqlstate '45000'
			set message_text = 'Car availability must be either (Y)es or N(o)';
		END IF;
		END;
	");
	echo "chk_ins_available trigger created.<br />";
	echo "Error: ", mysqli_error($cxn), "<br /><br />";
	mysqli_query($cxn, "
	CREATE TRIGGER chk_upd_available BEFORE UPDATE ON car
		FOR EACH ROW BEGIN
		IF (new.Available!='Y' AND new.Available!='N')
		THEN
			SIGNAL sqlstate '45000'
			set message_text = 'Car availability must be either (Y)es or N(o)';
		END IF;
		END;
	");
	echo "chk_upd_available trigger created.<br />";
	echo "Error: ", mysqli_error($cxn), "<br /><br />";

	
	
	/////////////////////
	// Populate Tables //
	/////////////////////
	
	echo "<hr /><u>Load Tables</u><br /><br />";
	mysqli_query($cxn, "insert into parking values
		('25 Princess', '4', '3'),
		('346 Johnson', '1', '0'),
		('69 Union', '5', '1'),
		('67 Queen', '1', '1'),
		('42 Victoria', '3', '1')
		;");
	echo "Parking Loaded.<br />";
	echo "Error: ", mysqli_error($cxn), "<br /><br />";
	
	mysqli_query($cxn, "insert into member values
		('12345678', 'Aiden', 'Boatright', '25 Aberdeen', '613-524-0045', 'boatleft@gmail.com', 'B9030-38475-45218', '65'),
		('87654321', 'Joe', 'Fresh', '50 Colborne', '613-908-3124', 'sofresh@hotmail.com', 'B2650-46492-25309', '65'),
		('12121212', 'Bob', 'Billy', '62 Nelson', '613-444-5555', 'bobbilly@gmail.com', 'B1460-56564-23405', '65'),
		('23232323', 'Sarah', 'Silva', '101 York', '613-234-1234', 'silvas@hotmail.com', 'B2340-23903-40954', '65'),
		('45678123', 'John', 'Smith', '12 Victoria', '613-533-1721', 'jsmith@gmail.com', 'B3720-72592-27780', '65')
		;");
	echo "Member Loaded.<br />";
	echo "Error: ", mysqli_error($cxn), "<br /><br />";
	
	mysqli_query($cxn, "insert into logins values
		('12345678', '92eb9a07d0f15df580f8eb355046a591'),
		('87654321', '404a6e35ea5384667d3527e6bd89f3a8'),
		('12121212', '00bfc8c729f5d4d529a412b12c58ddd2'),
		('23232323', '2ab96390c7dbe3439de74d0c9b0b1767'),
		('45678123', '1315486de8a38cc5a7f121aecdbf8c94')
		;");
		// ginger7
		// applesauce
		// pokemon
		// hunter2
		// zeldaoot
	echo "Logins Loaded.<br />";
	echo "Error: ", mysqli_error($cxn), "<br /><br />";
	
	mysqli_query($cxn, "insert into car values
		('1HGBH41JXMN109186', 'Honda', 'Civic', '2015', '25 Princess', '35', 'Y'),
		('1IYFW81OPEX701544', 'Toyota', 'Corolla', '2015', '25 Princess', '35', 'Y'),
		('1KSDJ41DEGK984509', 'Toyota', 'Prius', '2014', '67 Queen', '30', 'Y'),
		('1DGRS51FAWB398432', 'Ford', 'Focus', '2016', '42 Victoria', '40','Y'),
		('6IYGU95VEYD597164', 'Toyota', 'Yaris', '2016', '25 Princess', '40','Y'),
		('1YZFP21KLOM890325', 'Chevrolet', 'Cruze', '2014', '69 Union', '30', 'Y')
		;");
	echo "Car Loaded.<br />";
	echo "Error: ", mysqli_error($cxn), "<br /><br />";
	
	mysqli_query($cxn, "insert into maintenanceHistory values
		('1HGBH41JXMN109186', '2016-01-09', '350', '45000', 'R', 'Replace front brake pads'),
		('1IYFW81OPEX701544', '2016-02-12', '70', '30500', 'S', 'Oil change'),
		('1HGBH41JXMN109186', '2015-08-15', '70', '20000', 'S', 'Oil Change'),
		('1KSDJ41DEGK984509', '2015-09-10', '85', '44000', 'R', 'Fix paint scratches'),
		('1DGRS51FAWB398432', '2016-12-04', '170', '9200', 'S', 'Winter tires'),
		('1YZFP21KLOM890325', '2015-10-06', '160', '41000', 'R', 'Tire change')
		;");
	echo "Maintenance History Loaded.<br />";
	echo "Error: ", mysqli_error($cxn), "<br /><br />";

	mysqli_query($cxn, "insert into reservation values
		('PR902581', '12345678', '1HGBH41JXMN109186', '2016-02-07', '4', 'N'),
		('PR906482', '87654321', '1IYFW81OPEX701544', '2016-05-04', '8', 'N'),
		('PR904526', '45678123', '1YZFP21KLOM890325', '2016-05-06', '1', 'N'),
		('PR904794', '23232323', '1HGBH41JXMN109186', '2016-06-06', '14', 'N'),
		('PR904389', '12121212', '1KSDJ41DEGK984509', '2016-07-22', '1', 'N'),
		('PR903498', '23232323', '1DGRS51FAWB398432', '2016-11-07', '1', 'Y')
		;");
	echo "Reservation Loaded.<br />";
	echo "Error: ", mysqli_error($cxn), "<br /><br />";
	
	mysqli_query($cxn, "insert into rental values
		('PR902581', '12345678', '1HGBH41JXMN109186', 'N', '47000', '70', '2016-01-22', '08:30:00', 'N', '47050', '71', '2016-01-22', '22:00:00', '0', NULL),
		('PR904794', '23232323', '1HGBH41JXMN109186', 'N', '48500', '70', '2016-02-22', '08:30:00', 'N', '50000', '71', '2016-02-22', '22:00:00', '50', 'Flat Tire'),
		('PR906482', '87654321', '1IYFW81OPEX701544', 'N', '37800', '65', '2016-04-21', '09:00:00', 'N', '37900', '66', '2016-04-21', '18:30:00', '0', NULL),
		('PR904526', '45678123', '1YZFP21KLOM890325', 'N', '43000', '59', '2016-04-26', '08:30:00', 'N', '43200', '65', '2016-04-26', '19:30:00', '0', NULL),
		('PR904389', '12121212', '1KSDJ41DEGK984509', 'N', '48000', '73', '2016-07-15', '09:00:00', 'D', '48100', '73', '2016-07-15', '21:30:00', '200', 'Cracked Windshield')
		;");
	echo "Car Rental History Loaded.<br />";
	echo "Error: ", mysqli_error($cxn), "<br /><br />";
	
	mysqli_query($cxn, "insert into rentalComments values
		('12345678', '1HGBH41JXMN109186', 'PR902581', NULL, NULL, NULL),
		('87654321', '1IYFW81OPEX701544', 'PR906482', '3', 'Great car. A bit dirty', NULL),
		('45678123', '1YZFP21KLOM890325', 'PR904526', '4', 'Very impressed.', NULL),
		('12121212', '1KSDJ41DEGK984509', 'PR904389', '2', 'Car had only 5% gas when picked up', NULL),
		('23232323', '1HGBH41JXMN109186', 'PR904794', '1', 'Car had a cracked winsheild', 'Sorry about that, enjoy 50% off your next rental on us!')
		;");
	echo "Reservation Loaded.<br />";
	echo "Error: ", mysqli_error($cxn), "<br /><br />";
	
	mysqli_query($cxn, "insert into invoice values
		('15769423', '2016-01-01', '12345678', NULL),
		('12852135', '2016-04-01', '87654321', NULL),
		('13789524', '2016-04-01', '45678123', NULL),
		('16984932', '2016-07-01', '12121212', NULL),
		('17324890', '2016-12-01', '23232323', NULL)
		;");
	echo "Reservation Loaded.<br />";
	echo "Error: ", mysqli_error($cxn), "<br /><br />";
	
	mysqli_query($cxn, "insert into invoicecharges values
		('15769423', 'Monthly membership fee', '65'),
		('15769423', 'Car rental 2016-01-22', '35'),
		('12852135', 'Monthly membership fee', '65'),
		('12852135', 'Car rental 2016-04-21', '30'),
		('13789524', 'Monthly membership fee', '65'),
		('13789524', 'Car rental 2016-04-26', '30'),
		('16984932', 'Monthly membership fee', '65'),
		('16984932', 'Car rental 2016-07-15', '30'),
		('17324890', 'Monthly membership fee', '65'),
		('17324890', 'Car rental 2016-12-21', '40'),
		('17324890', 'Car rental 2016-02-22', '60')
		;");
	echo "Reservation Loaded.<br />";
	echo "Error: ", mysqli_error($cxn), "<br /><br />";
	
	// Generate a random string
	function generateRandomString($length = 8) {
		$characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
		$charactersLength = strlen($characters);
		$randomString = '';
		for ($i = 0; $i < $length; $i++) {
			$randomString .= $characters[rand(0, $charactersLength - 1)];
		}
    return $randomString;
	}
	
	// Create multiple random access codes and insert them into the table
	for( $i = 0; $i < 100; $i++) {
		$randomString = generateRandomString();
		$query = "insert into availablecodes values ('" . $randomString . "', NULL);";
		mysqli_query($cxn, $query);
	}
	echo "AvailableCodes Loaded.<br />";
	echo "Error: ", mysqli_error($cxn), "<br /><br />";
	
?>
</body></html>