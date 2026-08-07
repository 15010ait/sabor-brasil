# Sabor Brasil

A web application for discovering, reviewing, and saving favourite Brazilian meals. Built for INT1059 Advanced Web, Assessment 3.

## Team

- Andre — Student ID: 15182
- Francine Yoneyama — Student ID: 15010

## Technologies Used

- PHP (MySQLi, prepared statements)
- MySQL / MariaDB
- MySQLi (Prepared Statements)
- Bootstrap 5
- Bootstrap Icons
- HTML5
- CSS3
- JavaScript
- Git & GitHub
- XAMPP
- phpMyAdmin
- Google Fonts (Poppins, Nunito)

## Features

- Dynamic homepage with a Bootstrap carousel displaying randomly selected meals
- Category filtering
- Search by title, category, or description
- Meal detail pages with image galleries
- Reviews and star ratings (with average rating calculation)
- Favourites (add, remove, manage)
- User registration, login, and account management

## Local Setup Instructions

1. Clone this repository into your `htdocs` folder (or equivalent for your local server):
git clone https://github.com/15010ait/sabor-brasil.git
or download the ZIP file.

2. Move the project

Copy the folder to:
xampp/htdocs/

3. Start XAMPP

Start:
Apache
MySQL

4. Import the database

Open:
http://localhost/phpmyadmin

Create a database called:
sabor_brasil

Import:
database/sabor_brasil.sql

5. Open the website

Go to:
http://localhost/sabor-brasil

Default Test Account

If your SQL export includes one:

Email:
Password:

If not, simply write:

Register a new account to begin using the application.

Project Structure
assets/
    css/
    images/

config/
    db.php

database/
    sabor_brasil.sql

includes/
    header.php
    footer.php

index.php
meal.php
profile.php
search.php
login.php
register.php
Security Features
Prepared Statements
Password Hashing (password_hash())
Password Verification (password_verify())
Session-based Authentication
Server-side Validation
Email Validation
Strong Password Validation
Version Control

The project was developed collaboratively using Git and GitHub.

Development followed a feature-based workflow with regular commits and testing after each completed feature.

Authors

Andre Perez

Francine Yoneyama

AIT NSW

INT1059 Advanced Web