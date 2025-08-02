<?php
session_start();

// Include configuration and database connection
require_once 'config/database.php';
require_once 'includes/functions.php';

// Check if user is logged in
$isLoggedIn = isset($_SESSION['user_id']);

// Simple routing
$page = $_GET['page'] ?? 'dashboard';

if (!$isLoggedIn && $page !== 'login' && $page !== 'register') {
    header('Location: ?page=login');
    exit;
}

include 'includes/header.php';

switch ($page) {
    case 'login':
        include 'pages/login.php';
        break;
    case 'register':
        include 'pages/register.php';
        break;
    case 'logout':
        include 'pages/logout.php';
        break;
    case 'dashboard':
        include 'pages/dashboard.php';
        break;
    case 'tasks':
        include 'pages/tasks.php';
        break;
    case 'add-task':
        include 'pages/add-task.php';
        break;
    case 'edit-task':
        include 'pages/edit-task.php';
        break;
    default:
        include 'pages/dashboard.php';
}

include 'includes/footer.php';
?>