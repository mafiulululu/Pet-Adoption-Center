<?php
session_start();

// Handle AJAX Login Request
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    header('Content-Type: application/json');
    
    // Database connection
    require_once '../db/db_conn.php'; 

    $response = [
        'success' => false,
        'emailError' => '',
        'passwordError' => '',
        'nameError' => '',
        'confirmError' => '',
        'message' => ''
    ];

    $action = $_POST['action'] ?? 'login';

    if ($action === 'login') {
    $email = isset($_POST['email']) ? trim($_POST['email']) : '';
    $password = isset($_POST['password']) ? $_POST['password'] : '';
    $remember = isset($_POST['remember']) ? true : false;

    $hasError = false;

    if (empty($email)) {
        $response['emailError'] = 'Email is required';
        $hasError = true;
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $response['emailError'] = 'Invalid email format';
        $hasError = true;
    }

    if (empty($password)) {
        $response['passwordError'] = 'Password is required';
        $hasError = true;
    }

    if ($hasError) {
        echo json_encode($response);
        exit;
    }

    // Prepare statement
    $stmt = $conn->prepare("SELECT user_id, full_name, email, password_hash, role, status FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 0) {
        $response['emailError'] = 'No account found with this email';
        echo json_encode($response);
        exit;
    }

    $user = $result->fetch_assoc();

    if ($user['status'] === 'blocked') {
        $response['message'] = 'Your account has been blocked.';
        echo json_encode($response);
        exit;
    }

    if (password_verify($password, $user['password_hash'])) {
        $response['success'] = true;
        $response['message'] = 'Login successful';
        
        $_SESSION['user_id'] = $user['user_id'];
        $_SESSION['user_name'] = $user['full_name'];
        $_SESSION['user_email'] = $user['email'];
        $_SESSION['user_role'] = $user['role'];

        // Log login
        $ip_address = $_SERVER['REMOTE_ADDR'];
        $logStmt = $conn->prepare("INSERT INTO login_logs (user_id, ip_address) VALUES (?, ?)");
        $logStmt->bind_param("is", $user['user_id'], $ip_address);
        $logStmt->execute();
        $logStmt->close();

        if ($remember) {
            setcookie('remember_token', bin2hex(random_bytes(32)), time() + (86400 * 30), "/");
        }
    } else {
        $response['passwordError'] = 'Incorrect password';
    }

    $stmt->close();
    $conn->close();
    
    echo json_encode($response);
    exit;
    } elseif ($action === 'register') {
        // Registration Logic
        $name = isset($_POST['name']) ? trim($_POST['name']) : '';
        $email = isset($_POST['email']) ? trim($_POST['email']) : '';
        $password = isset($_POST['password']) ? $_POST['password'] : '';
        $confirm_password = isset($_POST['confirm_password']) ? $_POST['confirm_password'] : '';

        $hasError = false;

        if (empty($name)) {
            $response['nameError'] = 'Name is required';
            $hasError = true;
        } elseif (strlen($name) < 2) {
            $response['nameError'] = 'Name must be at least 2 characters';
            $hasError = true;
        } elseif (!preg_match("/^[a-zA-Z\s]+$/", $name)) {
            $response['nameError'] = 'Name can only contain letters and spaces';
            $hasError = true;
        }

        if (empty($email)) {
            $response['emailError'] = 'Email is required';
            $hasError = true;
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $response['emailError'] = 'Invalid email format';
            $hasError = true;
        }

        if (empty($password)) {
            $response['passwordError'] = 'Password is required';
            $hasError = true;
        } elseif (strlen($password) < 6) {
            $response['passwordError'] = 'Password must be at least 6 characters';
            $hasError = true;
        }

        if (empty($confirm_password)) {
            $response['confirmError'] = 'Please confirm your password';
            $hasError = true;
        } elseif ($password !== $confirm_password) {
            $response['confirmError'] = 'Passwords do not match';
            $hasError = true;
        }

        if ($hasError) {
            echo json_encode($response);
            exit;
        }

        // Check if email exists
        $stmt = $conn->prepare('SELECT user_id FROM users WHERE email = ? LIMIT 1');
        $stmt->bind_param('s', $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $response['emailError'] = 'An account with this email already exists';
            echo json_encode($response);
            exit;
        }
        $stmt->close();

        // Create User
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        $role = 'client';
        $status = 'active';

        $stmt = $conn->prepare('INSERT INTO users (full_name, email, password_hash, role, status) VALUES (?, ?, ?, ?, ?)');
        $stmt->bind_param('sssss', $name, $email, $hashedPassword, $role, $status);

        if ($stmt->execute()) {
            $response['success'] = true;
            $response['message'] = 'Account created successfully';
            $_SESSION['user_id'] = $conn->insert_id;
            $_SESSION['user_email'] = $email;
            $_SESSION['user_name'] = $name;
            $_SESSION['user_role'] = $role;
        } else {
            $response['message'] = 'Failed to create account. Please try again.';
        }
        $stmt->close();
        $conn->close();
        echo json_encode($response);
        exit;
    }
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pet Adoption Center - Welcome</title>
    <meta name="description" content="Join Pet Adoption Center - Find your perfect furry companion">
    <link rel="stylesheet" href="../css/style.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
</head>

<body>
    <div class="main-container">
        <!-- Left Side: Branding & Illustration -->
        <div class="left-panel">
            <div class="brand-content">
                <div class="logo-container">
                    <svg class="brand-logo" viewBox="0 0 120 120" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <ellipse cx="60" cy="70" rx="18" ry="22" fill="url(#logo-gradient)" />
                        <ellipse cx="45" cy="45" rx="12" ry="15" fill="url(#logo-gradient)" />
                        <ellipse cx="75" cy="45" rx="12" ry="15" fill="url(#logo-gradient)" />
                        <ellipse cx="35" cy="55" rx="10" ry="13" fill="url(#logo-gradient)" />
                        <ellipse cx="85" cy="55" rx="10" ry="13" fill="url(#logo-gradient)" />
                        <defs>
                            <linearGradient id="logo-gradient" x1="0" y1="0" x2="120" y2="120">
                                <stop offset="0%" stop-color="#FF1B6B" />
                                <stop offset="100%" stop-color="#45CAFF" />
                            </linearGradient>
                        </defs>
                    </svg>
                </div>

                <h1 class="brand-title">Pet Adoption Center</h1>
                <p class="brand-tagline">Connecting loving families with furry companions</p>

                <div class="features-list">
                    <div class="feature-item">
                        <span class="feature-icon">🐾</span>
                        <span class="feature-text">Thousands of pets waiting for homes</span>
                    </div>
                    <div class="feature-item">
                        <span class="feature-icon">❤️</span>
                        <span class="feature-text">Safe and verified adoptions</span>
                    </div>
                    <div class="feature-item">
                        <span class="feature-icon">🏠</span>
                        <span class="feature-text">Find your perfect companion</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Right Side: Forms -->
        <div class="right-panel">
            <div class="form-container">
                <!-- Tab Toggle -->
                <div class="auth-tabs">
                    <button class="auth-tab active" data-tab="login">Login</button>
                    <button class="auth-tab" data-tab="register">Sign Up</button>
                </div>

                <!-- Login Form -->
                <div class="form-content active" id="login-form">
                    <div class="form-header">
                        <h2 class="form-title">Welcome Back</h2>
                        <p class="form-subtitle">Enter your credentials to access your account</p>
                    </div>

                    <form id="loginForm" action="login.php" method="POST">
                        <input type="hidden" name="action" value="login">
                        <div class="input-group">
                            <label for="login-email" class="input-label">Email Address</label>
                            <div class="input-wrapper">
                                <input type="email" id="login-email" name="email" class="input-field"
                                    placeholder="you@example.com" required>
                            </div>
                            <span class="error-msg" id="loginEmailError"></span>
                        </div>

                        <div class="input-group">
                            <label for="login-password" class="input-label">Password</label>
                            <div class="password-wrapper">
                                <input type="password" id="login-password" name="password" class="input-field"
                                    placeholder="Enter your password" required>
                                <button type="button" class="password-toggle" data-target="login-password">
                                    <svg class="eye-icon" width="20" height="20" viewBox="0 0 24 24" fill="none"
                                        stroke="currentColor" stroke-width="2">
                                        <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z" />
                                        <circle cx="12" cy="12" r="3" />
                                    </svg>
                                </button>
                            </div>
                            <span class="error-msg" id="loginPasswordError"></span>
                        </div>

                        <div class="form-options">
                            <label class="remember-check">
                                <input type="checkbox" name="remember" id="remember">
                                <span class="checkmark"></span>
                                <span class="check-label">Remember me</span>
                            </label>
                            <a href="#" class="forgot-password">Forgot password?</a>
                        </div>

                        <button type="submit" class="btn-primary">
                            Sign In
                        </button>
                    </form>
                </div>

                <!-- Register Form -->
                <div class="form-content" id="register-form">
                    <div class="form-header">
                        <h2 class="form-title">Create Account</h2>
                        <p class="form-subtitle">Join us to start your pet adoption journey</p>
                    </div>

                    <form id="signupForm" action="login.php" method="POST">
                        <input type="hidden" name="action" value="register">
                        <div class="input-group">
                            <label for="signup-name" class="input-label">Full Name</label>
                            <div class="input-wrapper">
                                <input type="text" id="signup-name" name="name" class="input-field"
                                    placeholder="John Doe" required>
                            </div>
                            <span class="error-msg" id="signupNameError"></span>
                        </div>

                        <div class="input-group">
                            <label for="signup-email" class="input-label">Email Address</label>
                            <div class="input-wrapper">
                                <input type="email" id="signup-email" name="email" class="input-field"
                                    placeholder="you@example.com" required>
                            </div>
                            <span class="error-msg" id="signupEmailError"></span>
                        </div>

                        <div class="input-group">
                            <label for="signup-password" class="input-label">Password</label>
                            <div class="password-wrapper">
                                <input type="password" id="signup-password" name="password" class="input-field"
                                    placeholder="At least 6 characters" required>
                                <button type="button" class="password-toggle" data-target="signup-password">
                                    <svg class="eye-icon" width="20" height="20" viewBox="0 0 24 24" fill="none"
                                        stroke="currentColor" stroke-width="2">
                                        <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z" />
                                        <circle cx="12" cy="12" r="3" />
                                    </svg>
                                </button>
                            </div>
                            <span class="error-msg" id="signupPasswordError"></span>
                        </div>

                        <div class="input-group">
                            <label for="signup-confirm" class="input-label">Confirm Password</label>
                            <div class="password-wrapper">
                                <input type="password" id="signup-confirm" name="confirm_password" class="input-field"
                                    placeholder="Re-enter your password" required>
                                <button type="button" class="password-toggle" data-target="signup-confirm">
                                    <svg class="eye-icon" width="20" height="20" viewBox="0 0 24 24" fill="none"
                                        stroke="currentColor" stroke-width="2">
                                        <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z" />
                                        <circle cx="12" cy="12" r="3" />
                                    </svg>
                                </button>
                            </div>
                            <span class="error-msg" id="signupConfirmError"></span>
                        </div>

                        <label class="remember-check terms-check">
                            <input type="checkbox" name="terms" id="terms" required>
                            <span class="checkmark"></span>
                            <span class="check-label">I agree to the <a href="#" class="terms-link">Terms &
                                    Conditions</a></span>
                        </label>

                        <button type="submit" class="btn-primary">
                            Create Account
                        </button>

                        <div class="form-footer">
                            <p class="footer-text">Already have an account? <a href="?tab=login" class="switch-form"
                                    data-target="login">Back to Login</a></p>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <footer class="main-footer">
        <p class="copyright">&copy; 2025 Pet Adoption Center. All rights reserved.</p>
    </footer>

    <script src="script.js?v=1.1"></script>
</body>

</html>