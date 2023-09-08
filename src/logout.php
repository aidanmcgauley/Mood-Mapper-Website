<?php

// Start a session
session_start();
unset($_SESSION['logged_in']);

// Unset all session variables
$_SESSION = array();

// destroy the session cookie
if (isset($_COOKIE[session_name()])) {
    setcookie(session_name(), '', time()-42000, '/');
}

// destroy the session
session_destroy();

// redirect to home page
header("Location: index.php");
exit;

?>