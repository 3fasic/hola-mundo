<?php
header('Content-Type: application/json');

// Start session and include dependencies
session_start();
require_once '../config/database.php';
require_once '../includes/functions.php';

// Check if user is logged in
if (!is_logged_in()) {
    http_response_code(401);
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}

$user_id = get_current_user_id();
$method = $_SERVER['REQUEST_METHOD'];

try {
    switch ($method) {
        case 'GET':
            handleGetRequests();
            break;
        case 'POST':
            handlePostRequests();
            break;
        case 'PUT':
            handlePutRequests();
            break;
        case 'DELETE':
            handleDeleteRequests();
            break;
        default:
            http_response_code(405);
            echo json_encode(['error' => 'Method not allowed']);
    }
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Internal server error', 'message' => $e->getMessage()]);
}

function handleGetRequests() {
    global $user_id;
    
    $action = $_GET['action'] ?? '';
    
    switch ($action) {
        case 'list':
            $status = $_GET['status'] ?? null;
            $search = $_GET['search'] ?? '';
            $category = $_GET['category'] ?? '';
            
            $tasks = get_user_tasks($user_id, $status);
            
            // Apply filters
            if ($search || $category) {
                $filtered_tasks = [];
                foreach ($tasks as $task) {
                    $include = true;
                    
                    if ($search) {
                        $include = stripos($task['title'], $search) !== false || 
                                  stripos($task['description'], $search) !== false;
                    }
                    
                    if ($include && $category) {
                        $include = stripos($task['category'], $category) !== false;
                    }
                    
                    if ($include) {
                        $filtered_tasks[] = $task;
                    }
                }
                $tasks = $filtered_tasks;
            }
            
            echo json_encode(['tasks' => $tasks]);
            break;
            
        case 'stats':
            $stats = get_task_stats($user_id);
            echo json_encode(['stats' => $stats]);
            break;
            
        case 'categories':
            $all_tasks = get_user_tasks($user_id);
            $categories = array_unique(array_filter(array_column($all_tasks, 'category')));
            sort($categories);
            echo json_encode(['categories' => $categories]);
            break;
            
        default:
            http_response_code(400);
            echo json_encode(['error' => 'Invalid action']);
    }
}

function handlePostRequests() {
    global $user_id;
    
    $input = json_decode(file_get_contents('php://input'), true);
    $action = $input['action'] ?? $_POST['action'] ?? '';
    
    switch ($action) {
        case 'create':
            $title = sanitize_input($input['title'] ?? '');
            $description = sanitize_input($input['description'] ?? '');
            $priority = sanitize_input($input['priority'] ?? 'medium');
            $category = sanitize_input($input['category'] ?? '');
            $due_date = sanitize_input($input['due_date'] ?? '');
            
            if (empty($title)) {
                http_response_code(400);
                echo json_encode(['error' => 'Title is required']);
                return;
            }
            
            if (create_task($user_id, $title, $description, $priority, $category, $due_date)) {
                echo json_encode(['success' => true, 'message' => 'Task created successfully']);
            } else {
                http_response_code(500);
                echo json_encode(['error' => 'Failed to create task']);
            }
            break;
            
        case 'update_status':
            $task_id = $input['task_id'] ?? $_POST['task_id'] ?? '';
            $status = $input['status'] ?? $_POST['status'] ?? '';
            
            if (empty($task_id) || empty($status)) {
                http_response_code(400);
                echo json_encode(['error' => 'Task ID and status are required']);
                return;
            }
            
            $task = get_task_by_id($task_id, $user_id);
            if (!$task) {
                http_response_code(404);
                echo json_encode(['error' => 'Task not found']);
                return;
            }
            
            if (update_task($task_id, $user_id, $task['title'], $task['description'], 
                           $task['priority'], $status, $task['category'], $task['due_date'])) {
                echo json_encode(['success' => true, 'message' => 'Task status updated']);
            } else {
                http_response_code(500);
                echo json_encode(['error' => 'Failed to update task status']);
            }
            break;
            
        default:
            http_response_code(400);
            echo json_encode(['error' => 'Invalid action']);
    }
}

function handlePutRequests() {
    global $user_id;
    
    $input = json_decode(file_get_contents('php://input'), true);
    $task_id = $input['id'] ?? '';
    
    if (empty($task_id)) {
        http_response_code(400);
        echo json_encode(['error' => 'Task ID is required']);
        return;
    }
    
    $task = get_task_by_id($task_id, $user_id);
    if (!$task) {
        http_response_code(404);
        echo json_encode(['error' => 'Task not found']);
        return;
    }
    
    $title = sanitize_input($input['title'] ?? $task['title']);
    $description = sanitize_input($input['description'] ?? $task['description']);
    $priority = sanitize_input($input['priority'] ?? $task['priority']);
    $status = sanitize_input($input['status'] ?? $task['status']);
    $category = sanitize_input($input['category'] ?? $task['category']);
    $due_date = sanitize_input($input['due_date'] ?? $task['due_date']);
    
    if (update_task($task_id, $user_id, $title, $description, $priority, $status, $category, $due_date)) {
        echo json_encode(['success' => true, 'message' => 'Task updated successfully']);
    } else {
        http_response_code(500);
        echo json_encode(['error' => 'Failed to update task']);
    }
}

function handleDeleteRequests() {
    global $user_id;
    
    $input = json_decode(file_get_contents('php://input'), true);
    $task_id = $input['id'] ?? $_GET['id'] ?? '';
    
    if (empty($task_id)) {
        http_response_code(400);
        echo json_encode(['error' => 'Task ID is required']);
        return;
    }
    
    $task = get_task_by_id($task_id, $user_id);
    if (!$task) {
        http_response_code(404);
        echo json_encode(['error' => 'Task not found']);
        return;
    }
    
    if (delete_task($task_id, $user_id)) {
        echo json_encode(['success' => true, 'message' => 'Task deleted successfully']);
    } else {
        http_response_code(500);
        echo json_encode(['error' => 'Failed to delete task']);
    }
}
?>