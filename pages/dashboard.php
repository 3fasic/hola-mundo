<?php
$user_id = get_current_user_id();
$stats = get_task_stats($user_id);
$recent_tasks = get_user_tasks($user_id, null, 5);
$pending_tasks = get_user_tasks($user_id, 'pending', 3);
?>

<div class="row">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h1 class="h3 fw-bold">Dashboard</h1>
                <p class="text-muted mb-0">Welcome back, <?= htmlspecialchars($_SESSION['username']) ?>!</p>
            </div>
            <a href="?page=add-task" class="btn btn-primary">
                <i class="fas fa-plus me-2"></i>Add New Task
            </a>
        </div>
    </div>
</div>

<!-- Statistics Cards -->
<div class="row mb-4">
    <div class="col-md-3 mb-3">
        <div class="card text-center border-0 shadow-sm">
            <div class="card-body">
                <div class="d-flex align-items-center justify-content-center mb-2">
                    <div class="bg-primary bg-opacity-10 rounded-circle p-3">
                        <i class="fas fa-tasks fa-2x text-primary"></i>
                    </div>
                </div>
                <h3 class="fw-bold mb-1"><?= $stats['total'] ?? 0 ?></h3>
                <p class="text-muted mb-0">Total Tasks</p>
            </div>
        </div>
    </div>
    
    <div class="col-md-3 mb-3">
        <div class="card text-center border-0 shadow-sm">
            <div class="card-body">
                <div class="d-flex align-items-center justify-content-center mb-2">
                    <div class="bg-warning bg-opacity-10 rounded-circle p-3">
                        <i class="fas fa-clock fa-2x text-warning"></i>
                    </div>
                </div>
                <h3 class="fw-bold mb-1"><?= $stats['pending'] ?? 0 ?></h3>
                <p class="text-muted mb-0">Pending</p>
            </div>
        </div>
    </div>
    
    <div class="col-md-3 mb-3">
        <div class="card text-center border-0 shadow-sm">
            <div class="card-body">
                <div class="d-flex align-items-center justify-content-center mb-2">
                    <div class="bg-info bg-opacity-10 rounded-circle p-3">
                        <i class="fas fa-spinner fa-2x text-info"></i>
                    </div>
                </div>
                <h3 class="fw-bold mb-1"><?= $stats['in_progress'] ?? 0 ?></h3>
                <p class="text-muted mb-0">In Progress</p>
            </div>
        </div>
    </div>
    
    <div class="col-md-3 mb-3">
        <div class="card text-center border-0 shadow-sm">
            <div class="card-body">
                <div class="d-flex align-items-center justify-content-center mb-2">
                    <div class="bg-success bg-opacity-10 rounded-circle p-3">
                        <i class="fas fa-check-circle fa-2x text-success"></i>
                    </div>
                </div>
                <h3 class="fw-bold mb-1"><?= $stats['completed'] ?? 0 ?></h3>
                <p class="text-muted mb-0">Completed</p>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <!-- Recent Tasks -->
    <div class="col-lg-8 mb-4">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white">
                <div class="d-flex justify-content-between align-items-center">
                    <h5 class="mb-0 fw-bold">Recent Tasks</h5>
                    <a href="?page=tasks" class="btn btn-sm btn-outline-primary">View All</a>
                </div>
            </div>
            <div class="card-body">
                <?php if (empty($recent_tasks)): ?>
                    <div class="text-center py-4">
                        <i class="fas fa-tasks fa-3x text-muted mb-3"></i>
                        <h6 class="text-muted">No tasks yet</h6>
                        <p class="text-muted mb-3">Create your first task to get started!</p>
                        <a href="?page=add-task" class="btn btn-primary">
                            <i class="fas fa-plus me-2"></i>Add Task
                        </a>
                    </div>
                <?php else: ?>
                    <div class="list-group list-group-flush">
                        <?php foreach ($recent_tasks as $task): ?>
                            <div class="list-group-item px-0 py-3 border-bottom">
                                <div class="d-flex justify-content-between align-items-start">
                                    <div class="flex-grow-1">
                                        <div class="d-flex align-items-center mb-1">
                                            <h6 class="mb-0 me-2"><?= htmlspecialchars($task['title']) ?></h6>
                                            <?= get_priority_badge($task['priority']) ?>
                                        </div>
                                        <?php if ($task['description']): ?>
                                            <p class="text-muted mb-1 small"><?= htmlspecialchars(substr($task['description'], 0, 100)) ?><?= strlen($task['description']) > 100 ? '...' : '' ?></p>
                                        <?php endif; ?>
                                        <div class="d-flex align-items-center text-muted small">
                                            <i class="fas fa-calendar me-1"></i>
                                            <?= time_ago($task['created_at']) ?>
                                            <?php if ($task['due_date']): ?>
                                                <span class="mx-2">•</span>
                                                <i class="fas fa-clock me-1"></i>
                                                Due <?= format_date($task['due_date']) ?>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                    <div class="ms-3">
                                        <?= get_status_badge($task['status']) ?>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
    
    <!-- Quick Actions & Pending Tasks -->
    <div class="col-lg-4">
        <!-- Quick Actions -->
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white">
                <h5 class="mb-0 fw-bold">Quick Actions</h5>
            </div>
            <div class="card-body">
                <div class="d-grid gap-2">
                    <a href="?page=add-task" class="btn btn-primary">
                        <i class="fas fa-plus me-2"></i>New Task
                    </a>
                    <a href="?page=tasks&status=pending" class="btn btn-outline-warning">
                        <i class="fas fa-clock me-2"></i>View Pending
                    </a>
                    <a href="?page=tasks&status=in_progress" class="btn btn-outline-info">
                        <i class="fas fa-spinner me-2"></i>View In Progress
                    </a>
                    <a href="?page=tasks&status=completed" class="btn btn-outline-success">
                        <i class="fas fa-check me-2"></i>View Completed
                    </a>
                </div>
            </div>
        </div>
        
        <!-- Pending Tasks -->
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white">
                <h5 class="mb-0 fw-bold">Priority Tasks</h5>
            </div>
            <div class="card-body">
                <?php if (empty($pending_tasks)): ?>
                    <div class="text-center py-3">
                        <i class="fas fa-check-circle fa-2x text-success mb-2"></i>
                        <p class="text-muted mb-0 small">No pending tasks!</p>
                    </div>
                <?php else: ?>
                    <?php foreach ($pending_tasks as $task): ?>
                        <div class="d-flex align-items-center mb-3 pb-3 border-bottom">
                            <div class="flex-grow-1">
                                <h6 class="mb-1 small"><?= htmlspecialchars($task['title']) ?></h6>
                                <div class="d-flex align-items-center">
                                    <?= get_priority_badge($task['priority']) ?>
                                    <?php if ($task['due_date']): ?>
                                        <small class="text-muted ms-2">
                                            <i class="fas fa-clock me-1"></i>
                                            <?= format_date($task['due_date']) ?>
                                        </small>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <a href="?page=edit-task&id=<?= $task['id'] ?>" class="btn btn-sm btn-outline-primary ms-2">
                                <i class="fas fa-edit"></i>
                            </a>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>