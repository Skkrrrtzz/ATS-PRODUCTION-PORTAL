<?php
session_start();
session_unset(); // Clear all session variables
session_destroy(); // Destroy the session
// Prevent caching
header("Cache-Control: no-cache, no-store, must-revalidate");
header("Pragma: no-cache");
header("Expires: 0");
// Redirect other users to a default login form
header("Location: ATS_Prod_Home.php");

exit();
