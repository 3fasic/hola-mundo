<?php
$error = '';
$success = '';

if ($_POST) {
    $username = sanitize_input($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    
    if (empty($username) || empty($password)) {
        $error = 'Please fill in all fields.';
    } else {
        // Check user credentials
        $stmt = $pdo->prepare("SELECT id, username, password FROM users WHERE username = :username OR email = :username");
        $stmt->execute([':username' => $username]);
        $user = $stmt->fetch();
        
        if ($user && verify_password($password, $user['password'])) {
            login_user($user['id'], $user['username']);
            redirect('?page=dashboard');
        } else {
            $error = 'Invalid username or password.';
        }
    }
}
?>

<div class="row justify-content-center">
    <div class="col-md-6 col-lg-4">
        <div class="card shadow">
            <div class="card-body p-5">
                <div class="text-center mb-4">
                    <i class="fas fa-tasks fa-3x text-primary mb-3"></i>
                    <h2 class="fw-bold">Welcome Back</h2>
                    <p class="text-muted">Sign in to your TaskManager account</p>
                </div>
                
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
                    <div class="mb-3">
                        <label for="username" class="form-label">Username or Email</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fas fa-user"></i></span>
                            <input type="text" class="form-control" id="username" name="username" 
                                   value="<?= htmlspecialchars($_POST['username'] ?? '') ?>" required>
                        </div>
                    </div>
                    
                    <div class="mb-4">
                        <label for="password" class="form-label">Password</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fas fa-lock"></i></span>
                            <input type="password" class="form-control" id="password" name="password" required>
                        </div>
                    </div>
                    
                    <button type="submit" class="btn btn-primary w-100 py-2 mb-3">
                        <i class="fas fa-sign-in-alt me-2"></i>Sign In
                    </button>
                    
                    <div class="text-center">
                        <p class="mb-0">Don't have an account? 
                            <a href="?page=register" class="text-primary text-decoration-none fw-bold">Sign up</a>
                        </p>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>