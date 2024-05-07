# Requirements
To run this project you will need a computer with PHP and composer installed.

# Install
To install the project, you just have to run `composer install` to get all the dependencies

# Running the tests
php vendor/bin/phpunit tests/

This project implements a vehicle management application. 
- A User can have any number of Fleets
- A Fleet holds vehicles
- A Vehicle can be parked at a location
- A Vehicle can be shared by more then one Fleet
- No two Vehicle can be parked in the same location

The Project is split into two Steps. Step 1 implements the application using inMemory saving of state. Step 2 implements persistent memory using Doctrine and a MySql database. 


Step 1 (using in memory for storage): 
1. Initiate the php application with the following command: php memory.php, you can quit the application anytime by entering "end"
2. Create User with the following command: create-user
3. Create fleet with the following command: create-fleet <userId>
4. Register Vehicle, register vehicle to a fleet, with the following command: register-vehicle <fleetId> <vehiclePlateNumber>
5. Localize Vehicle, localize a vehicle with the following command: localize-vehicle <fleetId> <vehiclePlateNumber> <lat> <lng> <alt> 

Example: 
input: php memory.php
output: Enter command (type 'end' to quit):
input: create-user
output: User created successfully with ID: user_e9e52
input: create-fleet user_e9e52
output: Fleet created successfully, fleetId: Fleet created successfully, fleetId: fleet_664e0f00e87f0
input: register-vehicle fleet_664e0f00e87f0 ABC123 (where ABC123 is a vehiclePlateNumber supplied by the user)
output: Vehicle registered successfully in the fleet.
input: localize-vehicle fleet_664e0f00e87f0 ABC123 10 5 5
output: Vehicle location updated successfully.
input: end
output: Exciting ...


Step 2 (using persistance for storage, Doctrine plus MySql):
1. Configure Databasee: Ensure you set up the database correctly as listed below in the Set-up Database Server Step:    
2. Create Database: To create the database enter this command: php doctrine.php create 
	- (other commands availabe are as follows: php doctrine.php drop to DROP and php doctrine.php upgrade to UPGRADE)
3. Create User: php persistance.php create-user
4. Create Fleet: php persistance.php create-fleet <userid>
5. Register Vehicle: php persistance.php register-vehicle <fleetId> <vehiclePlateNumber>
6. Localize Vehicle: php persistance.php localize-vehicle <fleetId> <vehiclePlateNumber> <lat> <lng> <alt> 

Example:
input: php persistance.php create-user (php persistance.php create-user)
output: User created successfully with ID: c6f66e39-18f9-11ef-8a9e-c8cb9efe0537 
input: php persistance.php create-fleet c6f66e39-18f9-11ef-8a9e-c8cb9efe0537 (php persistance.php create-fleet <userid>)
output: Fleet created successfully with ID: e7705949-18f9-11ef-8a9e-c8cb9efe0537
input: php persistance.php register-vehicle e7705949-18f9-11ef-8a9e-c8cb9efe0537 ABC123 (php persistance.php register-vehicle <fleetId> <vehiclePlateNumber>)
output: Vehicle registered successfully in the fleet
input: php persistance.php localize-vehicle e7705949-18f9-11ef-8a9e-c8cb9efe0537 ABC123 10 5 4 (php persistance.php localize-vehicle <fleetId> <vehiclePlateNumber> <lat> <lng> <alt>)


Explanation of terms:
<userId> is outputted from create-user command
<fleetId> is outputted from create-fleet command
<vehiclePlateNumber> registration number of vehicle: is supplied externally	
<lat> <lng> <alt> latitude, longitude, altitude: is supplied externally


Set-up Database Server Step:

	- Enter your database credentials in bootstrap.php line 17 $connectionParams: 
	- Enter database driver: for this project mysql was installed on a windows machine, the following line uncommented in the php ini file: (extension=pdo_mysql), change as per your setup. 
	- Host: localhost used for this project, change as per your setup
	- Database Name: create database on your database server and enter name of newly created database here
	- User: as per your setup
	- Password: as per your setup


Step 3: 

For code quality, which tools and why ?

- PHPStan
	Why: PHPStan is a static analysis tool that helps identify potential bugs and code issues before runtime. It ensures the code adheres to best practices and helps you catch type errors and other issues early in the development process.

- PHP_CodeSniffer
	Why: PHP_CodeSniffer helps enforce coding standards by checking your code against a set of defined rules. This ensures consistency in your codebase, making it easier to read and maintain.

- PHPUnit 
	Why: is the standard testing framework for PHP. It allows you to write and run unit tests to ensure your code behaves as expected. By writing tests, you can verify that new changes do not introduce bugs.

- Functional Tests with PHPUnit
	Why: Functional tests focus on the behavior of the application by testing complete scenarios that a user might perform. These tests ensure that the different components of the application work together as expected and meet the requirements.

- SonarCloud
	Why: SonarCloud is a cloud-based code quality and security service. It provides detailed analysis of code, identifying bugs, vulnerabilities, code smells, and duplications. By integrating SonarCloud, one can get a comprehensive overview of the code quality, allowing one to make informed improvements and maintain a high standard.


Can consider to setup a ci/cd process : describe the necessary actions in a few words ?

 - Version Control: 
 	Ensure the project is version-controlled using Git and hosted on a platform like GitHub, GitLab, or Bitbucket.

 - CI/CD Platform: 
 	Use a CI/CD platform like GitHub Actions, GitLab CI, or Jenkins.


 - Pipeline Configuration:
	Code Checkout: Clone the repository.
	Dependency Installation: Install PHP dependencies using Composer.
	Static Analysis: Run PHPStan or Psalm to catch any static analysis errors.
	Code Style Check: Run PHP_CodeSniffer to enforce coding standards.
	Unit Tests: Execute PHPUnit tests to ensure your code works as expected.
	Functional Tests: Run functional tests using PHPUnit to verify that the application meets the specified requirements.
	Code Quality and Security: Integrate SonarCloud to analyze the codebase for quality issues and vulnerabilities.
	Build and Deploy: If all checks pass, build the application and deploy it to the staging/production environment.
	
 - Notifications: 
 	Configure notifications (e.g., email, Slack) to alert the team about the build and test results.

 - Environment Management: 
 	Use environment-specific configurations and secrets management for secure and consistent deployments.