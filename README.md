# K-Town Car Share
#### A Simple HTML/CSS Front End with MySQL Back End
---
K-Town Care Share is a complete working mock up for a company to implement a local car rental service for agile and on demand service. The system was designed to the client's specification to include a full web front end, simply and ascetically designed in HTML/CSS and have a full database system as a back end, implemented in MySQL. The client also specified several constraints on the data due to the business side of the service, which were integrated into the system. SQL queries are built into the web front end with PHP as the interface between the front and back ends.

## Notable Features
* User reservations in advanced
* Unique unlock codes to gain access to rented vehicles
* User rental history with monthly and on demand invoice generation
* Simple user registration
* Comment and rating system for all rentals
* Advanced admin panel to allow owners deeper access into the database for specific controlled functionality
* Intuitive interface with no SQL knowledge required from the user or admin
* Hashing of passwords and sensitive information for user security

### System Tour
A full user's guide and tour of the system is provided in `Documentation/UserGuide.pdf` and a technical report consisting of the database design and schema choices as well as the SQL that is running under the hood is provided in `Documentation/TechnicalReport.pdf`

## Technical Specs
---
### HTML/CSS Front End
The system implements a simple web page front end, using HTML elements inside PHP files. The PHP is used to interface with the database back end and dynamically change the pages depending on the data the SQL queries written in PHP return. Small amounts of JavaScript is also used to add advanced functionality that HTML/PHP are unable to provide on their own. The main styling of the front end is determined through the use of CSS for simplicity.

### MySQL Back End
The back end of the system is implemented in the form of a MySQL database. The schema of the database was carefully selected to both enhance query response time, while also not sacrificing data integrity. The schema and database design can be viewed in `/Documentation`. Care was also taken to ensure that the database is not able to be targeted by basic SQL injection attacks, with the PHP interface sanitizing inputs before sending them to the MySQL engine.

### Implementation
The system was locally developed using the [XAMPP](https://www.apachefriends.org/index.html) tool package to implement *PHPMyAdmin*, *Apache localhost* and MySQL as the database. The `/mysite` directory contains all necessary assets and pages, with `home.php` as the main page.

For testing purposes a PHP script `ktcs_load.php` is provided to initialize a MySQL database with some entries to test the features of the system.

---
##### Disclaimer
This project is presented in a mock up state, with most of the development time spent on the back end of the software. No development time was spent on the aesthetics of the front end, excluding basic styling.