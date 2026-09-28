<?php
require_once __DIR__ . '/../config/db.php';

$error = '';

// If already logged in, redirect to dashboard
if (isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true) {
    header('Location: index.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = trim($_POST['password'] ?? '');

    if (empty($username) || empty($password)) {
        $error = 'Please enter both username and password.';
    } else {
        try {
            $stmt = $pdo->prepare("SELECT * FROM admin_users WHERE username = ? OR email = ?");
            $stmt->execute([$username, $username]);
            $admin = $stmt->fetch();

            if ($admin && password_verify($password, $admin['password'])) {
                $_SESSION['admin_logged_in'] = true;
                $_SESSION['admin_id'] = $admin['id'];
                $_SESSION['admin_username'] = $admin['username'];
                $_SESSION['admin_name'] = $admin['name'];

                header('Location: index.php');
                exit;
            } else {
                $error = 'Invalid credentials. Please try again.';
            }
        } catch (Exception $e) {
            $error = 'System error: ' . $e->getMessage();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Admin Login | Vastra Mahal Portal</title>
    <link href="../css/bootstrap.min.css" rel="stylesheet">
    <link href="../css/all.min.css" rel="stylesheet">
    <link href="../css/vastra-mahal-luxury.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #2A0406 0%, #580B0D 50%, #1A0203 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .login-card {
            background: #FFFFFF;
            border-radius: 12px;
            box-shadow: 0 20px 50px rgba(0, 0, 0, 0.4);
            border: 2px solid var(--vm-gold);
            max-width: 440px;
            width: 100%;
        }
    </style>
</head>
<body>
    <div class="container p-3">
        <div class="login-card mx-auto p-4 p-md-5">
            <div class="text-center mb-4">
                <div class="vastra-brand-emblem mx-auto mb-3" style="width:54px;height:54px;font-size:22px;">
                    <span>VM</span>
                </div>
                <h3 class="fw-bold mb-1" style="font-family: var(--vm-font-title); color: var(--vm-maroon);">Vastra Mahal</h3>
                <span class="badge bg-warning text-dark fw-bold text-uppercase px-3 py-1" style="letter-spacing:1px;">Store Management Portal</span>
            </div>

            <?php if (!empty($error)): ?>
                <div class="alert alert-danger py-2 px-3 small mb-3">
                    <i class="fa-solid fa-circle-exclamation me-1"></i> <?= e($error); ?>
                </div>
            <?php endif; ?>

            <form action="login.php" method="POST">
                <div class="mb-3">
                    <label class="form-label small fw-semibold text-dark">Username or Email</label>
                    <div class="input-group">
                        <span class="input-group-text bg-light"><i class="fa-solid fa-user"></i></span>
                        <input type="text" name="username" class="form-control" placeholder="admin" required value="<?= e($_POST['username'] ?? 'admin'); ?>">
                    </div>
                </div>

                <div class="mb-4">
                    <label class="form-label small fw-semibold text-dark">Password</label>
                    <div class="input-group">
                        <span class="input-group-text bg-light"><i class="fa-solid fa-lock"></i></span>
                        <input type="password" name="password" class="form-control" placeholder="••••••••" required value="admin123">
                    </div>
                    <small class="text-muted" style="font-size:11px;">Default login: <code>admin</code> / <code>admin123</code></small>
                </div>

                <button type="submit" class="btn btn-royal w-100 py-2 justify-content-center">
                    <i class="fa-solid fa-arrow-right-to-bracket me-2"></i> Sign In To Admin
                </button>
            </form>

            <div class="text-center mt-4 pt-3 border-top">
                <a href="../index.php" class="small text-muted text-decoration-none">
                    <i class="fa-solid fa-arrow-left me-1"></i> Back to Public Website
                </a>
            </div>
        </div>
    </div>
</body>
</html>
