<?php
require_once 'includes/config.php';
require_once 'includes/functions.php';

session_start();
session_unset();
session_destroy();

redirect('/car-dealership/login.php', 'You have been logged out successfully.');
?>