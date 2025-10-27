<?php
session_start();

require_once __DIR__ . '/../vendor/autoload.php';

use Twig\Environment;
use Twig\Loader\FilesystemLoader;

// Initialize Twig
$loader = new FilesystemLoader(__DIR__ . '/../templates');
$twig = new Environment($loader, [
    'cache' => false, // Set to a path in production
    'debug' => true,
]);

// Helper function to check authentication
function isAuthenticated() {
    return isset($_SESSION['ticketapp_session']) && !empty($_SESSION['ticketapp_session']);
}

// Helper function to get tickets from session
function getTickets() {
    return isset($_SESSION['tickets']) ? $_SESSION['tickets'] : [];
}

// Helper function to save tickets to session
function saveTickets($tickets) {
    $_SESSION['tickets'] = $tickets;
}

// Route handling
$page = isset($_GET['page']) ? $_GET['page'] : 'landing';
$action = isset($_POST['action']) ? $_POST['action'] : null;

// Handle POST actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    // Login action
    if ($action === 'login') {
        $email = $_POST['email'] ?? '';
        $password = $_POST['password'] ?? '';
        
        if (!empty($email) && !empty($password)) {
            $_SESSION['ticketapp_session'] = 'token_' . time();
            $_SESSION['user_email'] = $email;
            header('Location: index.php?page=dashboard');
            exit;
        } else {
            $_SESSION['error_message'] = 'Invalid credentials';
            header('Location: index.php?page=login');
            exit;
        }
    }
    
    // Signup action
    if ($action === 'signup') {
        $name = $_POST['name'] ?? '';
        $email = $_POST['email'] ?? '';
        $password = $_POST['password'] ?? '';
        $confirmPassword = $_POST['confirm_password'] ?? '';
        
        $errors = [];
        if (empty($name)) $errors[] = 'Name is required';
        if (empty($email)) $errors[] = 'Email is required';
        if (empty($password)) $errors[] = 'Password is required';
        if ($password !== $confirmPassword) $errors[] = 'Passwords do not match';
        
        if (empty($errors)) {
            $_SESSION['success_message'] = 'Account created successfully!';
            header('Location: index.php?page=login');
            exit;
        } else {
            $_SESSION['error_message'] = implode(', ', $errors);
            header('Location: index.php?page=signup');
            exit;
        }
    }
    
    // Logout action
    if ($action === 'logout') {
        session_destroy();
        header('Location: index.php?page=landing');
        exit;
    }
    
    // Create ticket action
    if ($action === 'create_ticket') {
        if (!isAuthenticated()) {
            header('Location: index.php?page=login');
            exit;
        }
        
        $title = trim($_POST['title'] ?? '');
        $description = trim($_POST['description'] ?? '');
        $status = $_POST['status'] ?? 'open';
        $priority = $_POST['priority'] ?? 'medium';
        
        $errors = [];
        if (empty($title)) $errors[] = 'Title is required';
        if (!in_array($status, ['open', 'in_progress', 'closed'])) {
            $errors[] = 'Invalid status value';
        }
        
        if (empty($errors)) {
            $tickets = getTickets();
            $newTicket = [
                'id' => time() . rand(1000, 9999),
                'title' => $title,
                'description' => $description,
                'status' => $status,
                'priority' => $priority,
            ];
            $tickets[] = $newTicket;
            saveTickets($tickets);
            $_SESSION['success_message'] = 'Ticket created successfully!';
        } else {
            $_SESSION['error_message'] = implode(', ', $errors);
        }
        
        header('Location: index.php?page=tickets');
        exit;
    }
    
    // Update ticket action
    if ($action === 'update_ticket') {
        if (!isAuthenticated()) {
            header('Location: index.php?page=login');
            exit;
        }
        
        $id = $_POST['id'] ?? '';
        $title = trim($_POST['title'] ?? '');
        $description = trim($_POST['description'] ?? '');
        $status = $_POST['status'] ?? 'open';
        $priority = $_POST['priority'] ?? 'medium';
        
        $errors = [];
        if (empty($title)) $errors[] = 'Title is required';
        if (!in_array($status, ['open', 'in_progress', 'closed'])) {
            $errors[] = 'Invalid status value';
        }
        
        if (empty($errors)) {
            $tickets = getTickets();
            foreach ($tickets as &$ticket) {
                if ($ticket['id'] == $id) {
                    $ticket['title'] = $title;
                    $ticket['description'] = $description;
                    $ticket['status'] = $status;
                    $ticket['priority'] = $priority;
                    break;
                }
            }
            saveTickets($tickets);
            $_SESSION['success_message'] = 'Ticket updated successfully!';
        } else {
            $_SESSION['error_message'] = implode(', ', $errors);
        }
        
        header('Location: index.php?page=tickets');
        exit;
    }
    
    // Delete ticket action
    if ($action === 'delete_ticket') {
        if (!isAuthenticated()) {
            header('Location: index.php?page=login');
            exit;
        }
        
        $id = $_POST['id'] ?? '';
        $tickets = getTickets();
        $tickets = array_filter($tickets, function($ticket) use ($id) {
            return $ticket['id'] != $id;
        });
        saveTickets(array_values($tickets));
        $_SESSION['success_message'] = 'Ticket deleted successfully!';
        
        header('Location: index.php?page=tickets');
        exit;
    }
}

// Protect dashboard and tickets pages
if (($page === 'dashboard' || $page === 'tickets') && !isAuthenticated()) {
    header('Location: index.php?page=login');
    exit;
}

// Get messages and clear them
$successMessage = $_SESSION['success_message'] ?? null;
$errorMessage = $_SESSION['error_message'] ?? null;
unset($_SESSION['success_message'], $_SESSION['error_message']);

// Get editing ticket if specified
$editingTicket = null;
if ($page === 'tickets' && isset($_GET['edit'])) {
    $editId = $_GET['edit'];
    $tickets = getTickets();
    foreach ($tickets as $ticket) {
        if ($ticket['id'] == $editId) {
            $editingTicket = $ticket;
            break;
        }
    }
}

// Prepare data for templates
$data = [
    'page' => $page,
    'isAuthenticated' => isAuthenticated(),
    'successMessage' => $successMessage,
    'errorMessage' => $errorMessage,
    'tickets' => getTickets(),
    'editingTicket' => $editingTicket,
];

// Calculate statistics for dashboard
if ($page === 'dashboard') {
    $tickets = getTickets();
    $data['totalTickets'] = count($tickets);
    $data['openTickets'] = count(array_filter($tickets, fn($t) => $t['status'] === 'open'));
    $data['inProgressTickets'] = count(array_filter($tickets, fn($t) => $t['status'] === 'in_progress'));
    $data['closedTickets'] = count(array_filter($tickets, fn($t) => $t['status'] === 'closed'));
}

// Render the template
echo $twig->render('index.twig', $data);