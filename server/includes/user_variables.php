<?php
require_once('/../setup/db_setup_variables.php');

// user registration settings
// NULL to default to table schema
$minUsername = 1;
$maxUsername = NULL;
$minPassword = 6;
$maxPassword = 100;

// registration
// choose whether to allow new registrations
$allowRegister = true;

// choose whether to require an email
$emailOptional = true;

// allowed characters for username (ignoring case)
$usernameChar = 'abcdefghijklmnopqrstuvwxyz1234567890-_.';