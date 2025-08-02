<?php
$user_id = get_current_user_id();
$task_id = $_GET['id'] ?? '';
$error = '';
$success = '';

if (empty($task_id)) {
    redirect('?page=tasks');
}

$task = get_task_by_id($task_id, $user_id);

if (!$task) {
    redirect('?page=tasks');
}

if ($_POST) {
    $title = sanitize_input($_POST['title'] ?? '');
    $description = sanitize_input($_POST['description'] ?? '');
    $priority = sanitize_input($_POST['priority'] ?? 'medium');
    $status = sanitize_input($_POST['status'] ?? 'pending');
    $category = sanitize_input($_POST['category'] ?? '');
    $due_date = sanitize_input($_POST['due_date'] ?? '');
    
    if (empty($title)) {
        $error = 'Task title is required.';
    } else {
        if (update_task($task_id, $user_id, $title, $description, $priority, $status, $category, $due_date)) {
            $success = 'Task updated successfully!';
            // Refresh task data
            $task = get_task_by_id($task_id, $user_id);
        } else {
            $error = 'Failed to update task. Please try again.';
        }
    }
}
?>

<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white">
                <div class="d-flex align-items-center justify-content-between">
                    <div class="d-flex align-items-center">
                        <i class="fas fa-edit text-primary me-2"></i>
                        <h4 class="mb-0 fw-bold">Edit Task</h4>
                    </div>
                    <a href="?page=tasks" class="btn btn-outline-secondary btn-sm">
                        <i class="fas fa-arrow-left me-2"></i>Back to Tasks
                    </a>
                </div>
            </div>
            <div class="card-body p-4">
                <?php if ($error): ?>
                    <div class="alert alert-danger" role="alert">
                        <i class="fas fa-exclamation-circle me-2"></i><?= $error ?>
                    </div>
                <?php endif; ?>
                
                <?php if ($success): ?>
                    <div class="alert alert-success" role="alert">
                        <i class="fas fa-check-circle me-2"></i><?= $success ?>
                    </div>
                <?php endif; ?>
                
                <form method="POST" action="">
                    <div class="row">
                        <div class="col-md-8 mb-3">
                            <label for="title" class="form-label fw-bold">Task Title <span class="text-danger">*</span></label>
                            <input type="text" class="form-control form-control-lg" id="title" name="title" 
                                   value="<?= htmlspecialchars($_POST['title'] ?? $task['title']) ?>" 
                                   placeholder="Enter task title..." required>
                        </div>
                        
                        <div class="col-md-4 mb-3">
                            <label for="status" class="form-label fw-bold">Status</label>
                            <select class="form-select form-select-lg" id="status" name="status">
                                <option value="pending" <?= (($_POST['status'] ?? $task['status']) === 'pending') ? 'selected' : '' ?>>
                                    Pending
                                </option>
                                <option value="in_progress" <?= (($_POST['status'] ?? $task['status']) === 'in_progress') ? 'selected' : '' ?>>
                                    In Progress
                                </option>
                                <option value="completed" <?= (($_POST['status'] ?? $task['status']) === 'completed') ? 'selected' : '' ?>>
                                    Completed
                                </option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="description" class="form-label fw-bold">Description</label>
                        <textarea class="form-control" id="description" name="description" rows="4" 
                                  placeholder="Describe your task in detail..."><?= htmlspecialchars($_POST['description'] ?? $task['description']) ?></textarea>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label for="priority" class="form-label fw-bold">Priority</label>
                            <select class="form-select" id="priority" name="priority">
                                <option value="low" <?= (($_POST['priority'] ?? $task['priority']) === 'low') ? 'selected' : '' ?>>
                                    Low Priority
                                </option>
                                <option value="medium" <?= (($_POST['priority'] ?? $task['priority']) === 'medium') ? 'selected' : '' ?>>
                                    Medium Priority
                                </option>
                                <option value="high" <?= (($_POST['priority'] ?? $task['priority']) === 'high') ? 'selected' : '' ?>>
                                    High Priority
                                </option>
                            </select>
                        </div>
                        
                        <div class="col-md-4 mb-3">
                            <label for="category" class="form-label fw-bold">Category</label>
                            <input type="text" class="form-control" id="category" name="category" 
                                   value="<?= htmlspecialchars($_POST['category'] ?? $task['category']) ?>" 
                                   placeholder="e.g., Work, Personal, Study...">
                        </div>
                        
                        <div class="col-md-4 mb-3">
                            <label for="due_date" class="form-label fw-bold">Due Date</label>
                            <input type="date" class="form-control" id="due_date" name="due_date" 
                                   value="<?= htmlspecialchars($_POST['due_date'] ?? $task['due_date']) ?>">
                        </div>
                    </div>
                    
                    <div class="d-flex gap-2 pt-3 border-top">
                        <button type="submit" class="btn btn-primary btn-lg">
                            <i class="fas fa-save me-2"></i>Update Task
                        </button>
                        <a href="?page=tasks" class="btn btn-outline-secondary btn-lg">
                            <i class="fas fa-times me-2"></i>Cancel
                        </a>
                    </div>
                </form>
            </div>
        </div>
        
        <!-- Task Information -->
        <div class="card border-0 shadow-sm mt-4">
            <div class="card-body">
                <h6 class="fw-bold mb-3">
                    <i class="fas fa-info-circle text-info me-2"></i>Task Information
                </h6>
                <div class="row text-muted small">
                    <div class="col-md-6">
                        <p class="mb-2">
                            <strong>Created:</strong> <?= format_date($task['created_at']) ?>
                            <span class="ms-2">(<?= time_ago($task['created_at']) ?>)</span>
                        </p>
                        <p class="mb-2">
                            <strong>Last Updated:</strong> <?= format_date($task['updated_at']) ?>
                            <span class="ms-2">(<?= time_ago($task['updated_at']) ?>)</span>
                        </p>
                    </div>
                    <div class="col-md-6">
                        <p class="mb-2">
                            <strong>Current Status:</strong> <?= get_status_badge($task['status']) ?>
                        </p>
                        <p class="mb-2">
                            <strong>Current Priority:</strong> <?= get_priority_badge($task['priority']) ?>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>