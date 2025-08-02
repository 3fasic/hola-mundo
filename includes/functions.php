<?php
// Utility functions for the task management application

function sanitize_input($data) {
    return htmlspecialchars(strip_tags(trim($data)));
}

function validate_email($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL);
}

function hash_password($password) {
    return password_hash($password, PASSWORD_DEFAULT);
}

function verify_password($password, $hash) {
    return password_verify($password, $hash);
}

function login_user($user_id, $username) {
    $_SESSION['user_id'] = $user_id;
    $_SESSION['username'] = $username;
}

function logout_user() {
    session_destroy();
}

function is_logged_in() {
    return isset($_SESSION['user_id']);
}

function get_current_user_id() {
    return $_SESSION['user_id'] ?? null;
}

function redirect($url) {
    header("Location: $url");
    exit;
}

function format_date($date) {
    if (!$date) return '';
    return date('M j, Y', strtotime($date));
}

function time_ago($date) {
    $timestamp = strtotime($date);
    $time_ago = time() - $timestamp;
    
    if ($time_ago < 60) {
        return 'Just now';
    } elseif ($time_ago < 3600) {
        return floor($time_ago / 60) . ' minutes ago';
    } elseif ($time_ago < 86400) {
        return floor($time_ago / 3600) . ' hours ago';
    } elseif ($time_ago < 2592000) {
        return floor($time_ago / 86400) . ' days ago';
    } else {
        return date('M j, Y', $timestamp);
    }
}

function get_priority_badge($priority) {
    $badges = [
        'low' => '<span class="badge badge-success">Low</span>',
        'medium' => '<span class="badge badge-warning">Medium</span>',
        'high' => '<span class="badge badge-danger">High</span>'
    ];
    return $badges[$priority] ?? $badges['medium'];
}

function get_status_badge($status) {
    $badges = [
        'pending' => '<span class="badge badge-secondary">Pending</span>',
        'in_progress' => '<span class="badge badge-primary">In Progress</span>',
        'completed' => '<span class="badge badge-success">Completed</span>'
    ];
    return $badges[$status] ?? $badges['pending'];
}

function get_user_tasks($user_id, $status = null, $limit = null) {
    global $pdo;
    
    $sql = "SELECT * FROM tasks WHERE user_id = :user_id";
    $params = [':user_id' => $user_id];
    
    if ($status) {
        $sql .= " AND status = :status";
        $params[':status'] = $status;
    }
    
    $sql .= " ORDER BY created_at DESC";
    
    if ($limit) {
        $sql .= " LIMIT :limit";
        $params[':limit'] = $limit;
    }
    
    $stmt = $pdo->prepare($sql);
    foreach ($params as $key => $value) {
        if ($key === ':limit') {
            $stmt->bindValue($key, $value, PDO::PARAM_INT);
        } else {
            $stmt->bindValue($key, $value);
        }
    }
    $stmt->execute();
    return $stmt->fetchAll();
}

function get_task_by_id($task_id, $user_id) {
    global $pdo;
    
    $stmt = $pdo->prepare("SELECT * FROM tasks WHERE id = :id AND user_id = :user_id");
    $stmt->execute([':id' => $task_id, ':user_id' => $user_id]);
    return $stmt->fetch();
}

function create_task($user_id, $title, $description, $priority, $category, $due_date) {
    global $pdo;
    
    $stmt = $pdo->prepare("
        INSERT INTO tasks (user_id, title, description, priority, category, due_date) 
        VALUES (:user_id, :title, :description, :priority, :category, :due_date)
    ");
    
    return $stmt->execute([
        ':user_id' => $user_id,
        ':title' => $title,
        ':description' => $description,
        ':priority' => $priority,
        ':category' => $category,
        ':due_date' => $due_date ?: null
    ]);
}

function update_task($task_id, $user_id, $title, $description, $priority, $status, $category, $due_date) {
    global $pdo;
    
    $stmt = $pdo->prepare("
        UPDATE tasks 
        SET title = :title, description = :description, priority = :priority, 
            status = :status, category = :category, due_date = :due_date
        WHERE id = :id AND user_id = :user_id
    ");
    
    return $stmt->execute([
        ':id' => $task_id,
        ':user_id' => $user_id,
        ':title' => $title,
        ':description' => $description,
        ':priority' => $priority,
        ':status' => $status,
        ':category' => $category,
        ':due_date' => $due_date ?: null
    ]);
}

function delete_task($task_id, $user_id) {
    global $pdo;
    
    $stmt = $pdo->prepare("DELETE FROM tasks WHERE id = :id AND user_id = :user_id");
    return $stmt->execute([':id' => $task_id, ':user_id' => $user_id]);
}

function get_task_stats($user_id) {
    global $pdo;
    
    $stmt = $pdo->prepare("
        SELECT 
            COUNT(*) as total,
            SUM(CASE WHEN status = 'pending' THEN 1 ELSE 0 END) as pending,
            SUM(CASE WHEN status = 'in_progress' THEN 1 ELSE 0 END) as in_progress,
            SUM(CASE WHEN status = 'completed' THEN 1 ELSE 0 END) as completed
        FROM tasks WHERE user_id = :user_id
    ");
    $stmt->execute([':user_id' => $user_id]);
    return $stmt->fetch();
}
?>