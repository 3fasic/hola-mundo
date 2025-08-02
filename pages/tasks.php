<?php
$user_id = get_current_user_id();
$status_filter = $_GET['status'] ?? '';
$category_filter = $_GET['category'] ?? '';
$search = $_GET['search'] ?? '';

// Handle task actions
if ($_POST) {
    $action = $_POST['action'] ?? '';
    $task_id = $_POST['task_id'] ?? '';
    
    if ($action === 'delete' && $task_id) {
        if (delete_task($task_id, $user_id)) {
            $success = 'Task deleted successfully!';
        } else {
            $error = 'Failed to delete task.';
        }
    } elseif ($action === 'update_status' && $task_id) {
        $new_status = $_POST['status'] ?? '';
        $task = get_task_by_id($task_id, $user_id);
        if ($task) {
            update_task($task_id, $user_id, $task['title'], $task['description'], 
                       $task['priority'], $new_status, $task['category'], $task['due_date']);
            $success = 'Task status updated successfully!';
        }
    }
}

// Get tasks with filters
$tasks = get_user_tasks($user_id, $status_filter);

// Apply additional filters
if ($search || $category_filter) {
    $filtered_tasks = [];
    foreach ($tasks as $task) {
        $include = true;
        
        if ($search) {
            $include = stripos($task['title'], $search) !== false || 
                      stripos($task['description'], $search) !== false;
        }
        
        if ($include && $category_filter) {
            $include = stripos($task['category'], $category_filter) !== false;
        }
        
        if ($include) {
            $filtered_tasks[] = $task;
        }
    }
    $tasks = $filtered_tasks;
}

// Get unique categories for filter
$all_tasks = get_user_tasks($user_id);
$categories = array_unique(array_filter(array_column($all_tasks, 'category')));
sort($categories);
?>

<div class="row">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h1 class="h3 fw-bold">My Tasks</h1>
                <p class="text-muted mb-0"><?= count($tasks) ?> task(s) found</p>
            </div>
            <a href="?page=add-task" class="btn btn-primary">
                <i class="fas fa-plus me-2"></i>Add Task
            </a>
        </div>
    </div>
</div>

<?php if (isset($success)): ?>
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="fas fa-check-circle me-2"></i><?= $success ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>

<?php if (isset($error)): ?>
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <i class="fas fa-exclamation-circle me-2"></i><?= $error ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>

<!-- Filters -->
<div class="card border-0 shadow-sm mb-4">
    <div class="card-body">
        <form method="GET" action="">
            <input type="hidden" name="page" value="tasks">
            <div class="row g-3">
                <div class="col-md-3">
                    <label class="form-label fw-bold">Search Tasks</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="fas fa-search"></i></span>
                        <input type="text" class="form-control" name="search" 
                               value="<?= htmlspecialchars($search) ?>" 
                               placeholder="Search by title or description...">
                    </div>
                </div>
                
                <div class="col-md-3">
                    <label class="form-label fw-bold">Status</label>
                    <select class="form-select" name="status">
                        <option value="">All Status</option>
                        <option value="pending" <?= ($status_filter === 'pending') ? 'selected' : '' ?>>Pending</option>
                        <option value="in_progress" <?= ($status_filter === 'in_progress') ? 'selected' : '' ?>>In Progress</option>
                        <option value="completed" <?= ($status_filter === 'completed') ? 'selected' : '' ?>>Completed</option>
                    </select>
                </div>
                
                <div class="col-md-3">
                    <label class="form-label fw-bold">Category</label>
                    <select class="form-select" name="category">
                        <option value="">All Categories</option>
                        <?php foreach ($categories as $category): ?>
                            <option value="<?= htmlspecialchars($category) ?>" 
                                    <?= ($category_filter === $category) ? 'selected' : '' ?>>
                                <?= htmlspecialchars($category) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div class="col-md-3">
                    <label class="form-label fw-bold">&nbsp;</label>
                    <div class="d-grid">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-filter me-2"></i>Filter
                        </button>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Tasks List -->
<?php if (empty($tasks)): ?>
    <div class="card border-0 shadow-sm">
        <div class="card-body text-center py-5">
            <i class="fas fa-tasks fa-4x text-muted mb-3"></i>
            <h5 class="text-muted">No tasks found</h5>
            <p class="text-muted mb-4">
                <?php if ($status_filter || $search || $category_filter): ?>
                    Try adjusting your filters or create a new task.
                <?php else: ?>
                    You haven't created any tasks yet. Start by adding your first task!
                <?php endif; ?>
            </p>
            <a href="?page=add-task" class="btn btn-primary">
                <i class="fas fa-plus me-2"></i>Create Your First Task
            </a>
        </div>
    </div>
<?php else: ?>
    <div class="row">
        <?php foreach ($tasks as $task): ?>
            <div class="col-lg-6 mb-4">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-start mb-3">
                            <div class="flex-grow-1">
                                <h5 class="card-title fw-bold mb-1"><?= htmlspecialchars($task['title']) ?></h5>
                                <div class="d-flex align-items-center gap-2 mb-2">
                                    <?= get_priority_badge($task['priority']) ?>
                                    <?= get_status_badge($task['status']) ?>
                                    <?php if ($task['category']): ?>
                                        <span class="badge bg-light text-dark">
                                            <i class="fas fa-tag me-1"></i><?= htmlspecialchars($task['category']) ?>
                                        </span>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <div class="dropdown">
                                <button class="btn btn-sm btn-outline-secondary" type="button" 
                                        data-bs-toggle="dropdown" aria-expanded="false">
                                    <i class="fas fa-ellipsis-v"></i>
                                </button>
                                <ul class="dropdown-menu">
                                    <li><a class="dropdown-item" href="?page=edit-task&id=<?= $task['id'] ?>">
                                        <i class="fas fa-edit me-2"></i>Edit
                                    </a></li>
                                    <li><hr class="dropdown-divider"></li>
                                    <li>
                                        <form method="POST" style="display: inline;">
                                            <input type="hidden" name="action" value="delete">
                                            <input type="hidden" name="task_id" value="<?= $task['id'] ?>">
                                            <button type="submit" class="dropdown-item text-danger" 
                                                    onclick="return confirm('Are you sure you want to delete this task?')">
                                                <i class="fas fa-trash me-2"></i>Delete
                                            </button>
                                        </form>
                                    </li>
                                </ul>
                            </div>
                        </div>
                        
                        <?php if ($task['description']): ?>
                            <p class="card-text text-muted mb-3"><?= htmlspecialchars($task['description']) ?></p>
                        <?php endif; ?>
                        
                        <div class="d-flex justify-content-between align-items-center text-muted small mb-3">
                            <span>
                                <i class="fas fa-calendar me-1"></i>
                                Created <?= time_ago($task['created_at']) ?>
                            </span>
                            <?php if ($task['due_date']): ?>
                                <span class="<?= (strtotime($task['due_date']) < time() && $task['status'] !== 'completed') ? 'text-danger' : '' ?>">
                                    <i class="fas fa-clock me-1"></i>
                                    Due <?= format_date($task['due_date']) ?>
                                </span>
                            <?php endif; ?>
                        </div>
                        
                        <!-- Quick Status Update -->
                        <div class="d-flex gap-2">
                            <?php if ($task['status'] !== 'in_progress'): ?>
                                <form method="POST" style="display: inline;">
                                    <input type="hidden" name="action" value="update_status">
                                    <input type="hidden" name="task_id" value="<?= $task['id'] ?>">
                                    <input type="hidden" name="status" value="in_progress">
                                    <button type="submit" class="btn btn-sm btn-outline-info">
                                        <i class="fas fa-play me-1"></i>Start
                                    </button>
                                </form>
                            <?php endif; ?>
                            
                            <?php if ($task['status'] !== 'completed'): ?>
                                <form method="POST" style="display: inline;">
                                    <input type="hidden" name="action" value="update_status">
                                    <input type="hidden" name="task_id" value="<?= $task['id'] ?>">
                                    <input type="hidden" name="status" value="completed">
                                    <button type="submit" class="btn btn-sm btn-outline-success">
                                        <i class="fas fa-check me-1"></i>Complete
                                    </button>
                                </form>
                            <?php endif; ?>
                            
                            <a href="?page=edit-task&id=<?= $task['id'] ?>" class="btn btn-sm btn-outline-primary">
                                <i class="fas fa-edit me-1"></i>Edit
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
<?php endif; ?>