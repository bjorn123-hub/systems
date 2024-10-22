<?php
require_once '../config/Database.php'; // If you need database connection, adjust as needed
require_once '../classes/SessionManager.php'; // Include the SessionManager class

$sessionManager = new SessionManager(); // Create an instance of the SessionManager
$sessionManager->startSession(); // Start the session
$sessionManager->endSession(); // End the session
$sessionManager->redirectTo('login.php'); // Redirect to the login page
?>
