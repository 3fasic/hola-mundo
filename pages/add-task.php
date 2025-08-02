<?php
$error = '';
$success = '';

if ($_POST) {
    $title = sanitize_input($_POST['title'] ?? '');
    $description = sanitize_input($_POST['description'] ?? '');
    $priority = sanitize_input($_POST['priority'] ?? 'medium');
    $category = sanitize_input($_POST['category'] ?? '');
    $due_date = sanitize_input($_POST['due_date'] ?? '');
    
    if (empty($title)) {
        $error = 'Task title is required.';
    } else {
        if (create_task(get_current_user_id(), $title, $description, $priority, $category, $due_date)) {
            $success = 'Task created successfully!';
            $_POST = []; // Clear form
        } else {
            $error = 'Failed to create task. Please try again.';
        }
    }
}
?>

<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white">
                <div class="d-flex align-items-center">
                    <i class="fas fa-plus text-primary me-2"></i>
                    <h4 class="mb-0 fw-bold">Add New Task</h4>
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
                                   value="<?= htmlspecialchars($_POST['title'] ?? '') ?>" 
                                   placeholder="Enter task title..." required>
                        </div>
                        
                        <div class="col-md-4 mb-3">
                            <label for="priority" class="form-label fw-bold">Priority</label>
                            <select class="form-select form-select-lg" id="priority" name="priority">
                                <option value="low" <?= (($_POST['priority'] ?? '') === 'low') ? 'selected' : '' ?>>
                                    <i class="fas fa-arrow-down"></i> Low Priority
                                </option>
                                <option value="medium" <?= (($_POST['priority'] ?? 'medium') === 'medium') ? 'selected' : '' ?>>
                                    <i class="fas fa-minus"></i> Medium Priority
                                </option>
                                <option value="high" <?= (($_POST['priority'] ?? '') === 'high') ? 'selected' : '' ?>>
                                    <i class="fas fa-arrow-up"></i> High Priority
                                </option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="description" class="form-label fw-bold">Description</label>
                        <textarea class="form-control" id="description" name="description" rows="4" 
                                  placeholder="Describe your task in detail..."><?= htmlspecialchars($_POST['description'] ?? '') ?></textarea>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="category" class="form-label fw-bold">Category</label>
                            <input type="text" class="form-control" id="category" name="category" 
                                   value="<?= htmlspecialchars($_POST['category'] ?? '') ?>" 
                                   placeholder="e.g., Work, Personal, Study...">
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="due_date" class="form-label fw-bold">Due Date</label>
                            <input type="date" class="form-control" id="due_date" name="due_date" 
                                   value="<?= htmlspecialchars($_POST['due_date'] ?? '') ?>" 
                                   min="<?= date('Y-m-d') ?>">
                        </div>
                    </div>
                    
                    <div class="d-flex gap-2 pt-3 border-top">
                        <button type="submit" class="btn btn-primary btn-lg">
                            <i class="fas fa-save me-2"></i>Create Task
                        </button>
                        <a href="?page=dashboard" class="btn btn-outline-secondary btn-lg">
                            <i class="fas fa-times me-2"></i>Cancel
                        </a>
                    </div>
                </form>
            </div>
        </div>
        
        <!-- Task Creation Tips -->
        <div class="card border-0 shadow-sm mt-4">
            <div class="card-body">
                <h6 class="fw-bold mb-3">
                    <i class="fas fa-lightbulb text-warning me-2"></i>Tips for Better Task Management
                </h6>
                <div class="row">
                    <div class="col-md-6">
                        <ul class="list-unstyled mb-0">
                            <li class="mb-2">
                                <i class="fas fa-check text-success me-2"></i>
                                <small>Use clear, actionable titles</small>
                            </li>
                            <li class="mb-2">
                                <i class="fas fa-check text-success me-2"></i>
                                <small>Set realistic due dates</small>
                            </li>
                        </ul>
                    </div>
                    <div class="col-md-6">
                        <ul class="list-unstyled mb-0">
                            <li class="mb-2">
                                <i class="fas fa-check text-success me-2"></i>
                                <small>Organize with categories</small>
                            </li>
                            <li class="mb-2">
                                <i class="fas fa-check text-success me-2"></i>
                                <small>Set appropriate priorities</small>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>