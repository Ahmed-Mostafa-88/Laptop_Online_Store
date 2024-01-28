<?php
session_start();

// Destroy the session
session_destroy();

// Redirect to login page
header("Location: login.php");

// Optionally, show a confirmation message
exit('<p>You have been successfully logged out. Please <a href="login.php">login</a> to continue.</p>');
