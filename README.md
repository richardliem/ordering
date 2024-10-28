Online Food Ordering System via QR Code
Project Details
This project is an online food ordering system that allows customers to order food by scanning a QR Code at the table. The system consists of 3 main parts: Administrator, Staff, and Kitchen.

System Features
Administrator
Menu Management: Add, Edit, Delete Menu
Table Management: Add, Edit, Delete Tables, and Create QR Codes for Each Table
Order Viewing: View All Orders and Order Status
Employee Management: Add, Edit, Delete Employees
Administrator Management: Add, Edit, Delete Administrator
Staff
Order Viewing: View Orders Ordered by Customers from Tables
Order Status Change: Update Order Status When Service is Completed
Kitchen
Order Viewing: View Orders to Be Made in the Kitchen
Order Status Change: Update Order Status When Food is Cooked
Project Structure
css
Copy Code
food-ordering-system/
├── admin/
│   ├── css/
│   │   └── style.css
│   ├── layout/
│   │   └── navbar.php
│   ├── login.php
│   ├── dashboard.php
│   ├── logout.php
│   └── register.php
├── cart.php

├── confirm_order.php
├── css/
│   └── style.css
├── includes/
│   ├── db.php
│   └── functions.php
├── js/
│   └── scripts.js
├── images/
│   └── (images used in the project)
├── kitchen/
│   ├── login.php
│   ├── dashboard.php
│   └── logout.php
├── menu/
│   ├── manage_menus.php
│   ├── add_menu.php
│   ├── edit_menu.php
│   └── delete_menu.php
├── staff/
│   ├── login.php
│   ├── dashboard.php
│   └── logout.php
├── tables/
│   ├── add_table.php
│   ├── manage_tables.php
│   ├── edit_table.php
│   └── delete_table.php
├── upload/
│   └── img/
├── qr_code/
├── database/
│   └── create_tables.sql
├── index.php
├── menu.php
├── node_modules/
├── package.json
├── update_order_status.php
└── README.md
Installation
Clone this project from GitHub:

bash
Copy the code
git clone https://github.com/Dominowite/food-ordering-system.git
Install dependencies using Composer and npm:

bash
Copy the code
composer install
npm install
Create the required database and tables using create_tables.sql:

sql
Copy the code
CREATE DATABASE IF NOT EXISTS food_ordering_db;
USE food_ordering_db;
SOURCE database/create_tables.sql;
Set the database connection in the file includes/db.php:

php
Copy code
<?php
$host = 'localhost';
$db = 'food_ordering_db';
$user = 'root';
$pass = '';

$dsn = "mysql:host=$host;dbname=$db;charset=utf8";
$pdo = new PDO($dsn, $user, $pass, [
PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
]);
?>
Usage
Open a web server (e.g. Apache or Nginx) and go to http://localhost/food-ordering-system in a browser.
Log in to the admin area (admin/login.php).
Manage the menu and tables in the system.
Use the QR Code generated by the system to order food from the table.
Contributor
Developer name
Contact information