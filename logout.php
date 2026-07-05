<?php
require __DIR__ . '/includes/config.php';
// Only clear the login flag — $_SESSION['data'] is standing in for a real
// database until MySQL is wired up, so it must survive logout/login cycles.
unset($_SESSION['logged_in']);
header('Location: login.php');
exit;
