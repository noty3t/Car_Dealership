<?php
require_once 'config.php';
require_once 'functions.php';
// Check if user is logged in, if not redirect to login page
if (!isLoggedIn() && basename($_SERVER['PHP_SELF']) != 'login.php' && basename($_SERVER['PHP_SELF']) != 'register.php') {
    redirect('/car-dealership/login.php', 'Please login to access this page.');
}

// Check if admin is accessing admin pages
if (strpos($_SERVER['REQUEST_URI'], '/admin/') !== false && !isAdmin()) {
    redirect('/car-dealership/', 'You do not have permission to access the admin area.');
}

// Check if client is trying to access admin pages
if (isLoggedIn() && !isAdmin() && strpos($_SERVER['REQUEST_URI'], '/admin/') !== false) {
    redirect('/car-dealership/client/dashboard.php', 'You do not have permission to access the admin area.');
}

// Check if admin is trying to access client pages
if (isAdmin() && strpos($_SERVER['REQUEST_URI'], '/client/') !== false) {
    redirect('/car-dealership/admin/dashboard.php', 'Please use the admin dashboard.');
}

if(isLoggedIn() && isAdmin() && basename($_SERVER['REQUEST_URI']) == 'login.php')
{
     redirect('/car-dealership/admin/dashboard.php', 'Please use the admin dashboard.');
}

if(isLoggedIn() && basename($_SERVER['REQUEST_URI']) == 'login.php' &&  !isAdmin()){
     redirect('/car-dealership/client/dashboard.php', 'Please use the admin dashboard.');
}

if(isLoggedIn() && isAdmin() && basename($_SERVER['REQUEST_URI']) == 'register.php')
{
     redirect('/car-dealership/admin/dashboard.php', 'Please use the admin dashboard.');
}

if(isLoggedIn() && basename($_SERVER['REQUEST_URI']) == 'register.php' &&  !isAdmin()){
     redirect('/car-dealership/client/dashboard.php', 'Please use the admin dashboard.');
}

if(isLoggedIn() && isAdmin() && basename($_SERVER['REQUEST_URI']) == 'contact.php')
{
     redirect('/car-dealership/admin/dashboard.php', 'You are viewing as admin');
}
?>