<?php
require_once __DIR__ . '/../config/db.php';
unset($_SESSION['admin_logged_in'], $_SESSION['admin_id'], $_SESSION['admin_username'], $_SESSION['admin_name']);
session_destroy();
header('Location: login.php');
exit;
