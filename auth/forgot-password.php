<?php
// auth/forgot-password.php
require_once '../config/init.php';

// If already logged in, redirect to home
if (isLoggedIn()) {
    redirect('/index.php');
}

$page_title = "Forgot Password";
$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = sanitize($_POST['username'] ?? '');
    $new_password = $_POST['new_password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';
    
    if (empty($username) || empty($new_password) || empty($confirm_password)) {
        $error = "Please fill in all fields";
    } elseif ($new_password !== $confirm_password) {
        $error = "Passwords do not match";
    } elseif (strlen($new_password) < 6) {
        $error = "Password must be at least 6 characters long";
    } else {
        $conn = getDatabaseConnection();
        
        // Check if user exists
        $stmt = $conn->prepare("SELECT id, username, email FROM users WHERE username = ? OR email = ?");
        $stmt->bind_param("ss", $username, $username);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($user = $result->fetch_assoc()) {
            // Hash the new password
            $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
            
            // Update password in database
            $update_stmt = $conn->prepare("UPDATE users SET password = ? WHERE id = ?");
            $update_stmt->bind_param("si", $hashed_password, $user['id']);
            
            if ($update_stmt->execute()) {
                $success = "Password reset successfully! You can now login with your new password.";
                
                // Store success message in session
                $_SESSION['reset_success'] = $success;
                
                // Clear form
                $username = '';
                $new_password = '';
                $confirm_password = '';
                
                $update_stmt->close();
            } else {
                $error = "Failed to reset password. Please try again.";
            }
        } else {
            $error = "User not found. Please check your username or email.";
        }
        $stmt->close();
    }
}

// Display success message from session if redirected
if (isset($_SESSION['reset_success'])) {
    $success = $_SESSION['reset_success'];
    unset($_SESSION['reset_success']);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forgot Password - PhoneStore</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        
        .reset-container {
            width: 100%;
            max-width: 450px;
        }
        
        .reset-card {
            background: white;
            border-radius: 15px;
            padding: 40px;
            box-shadow: 0 20px 40px rgba(0,0,0,0.1);
            animation: slideIn 0.5s ease;
        }
        
        @keyframes slideIn {
            from {
                opacity: 0;
                transform: translateY(-20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        .logo {
            text-align: center;
            margin-bottom: 30px;
        }
        
        .logo i {
            font-size: 40px;
            color: #667eea;
            margin-bottom: 10px;
        }
        
        .logo h1 {
            color: #333;
            font-size: 28px;
        }
        
        .logo .highlight {
            color: #667eea;
        }
        
        .logo p {
            color: #666;
            margin-top: 10px;
            font-size: 14px;
        }
        
        .form-group {
            margin-bottom: 20px;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 8px;
            color: #555;
            font-weight: 500;
        }
        
        .form-control {
            width: 100%;
            padding: 12px 15px;
            border: 2px solid #e1e5eb;
            border-radius: 8px;
            font-size: 16px;
            transition: border-color 0.3s;
        }
        
        .form-control:focus {
            outline: none;
            border-color: #667eea;
        }
        
        .password-strength {
            margin-top: 5px;
            font-size: 12px;
            color: #666;
        }
        
        .password-strength.weak {
            color: #f56565;
        }
        
        .password-strength.medium {
            color: #ed8936;
        }
        
        .password-strength.strong {
            color: #48bb78;
        }
        
        .btn-reset {
            width: 100%;
            padding: 14px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: transform 0.3s;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
        }
        
        .btn-reset:hover {
            transform: translateY(-2px);
        }
        
        .btn-reset:disabled {
            opacity: 0.6;
            cursor: not-allowed;
        }
        
        .error-message {
            background: #f56565;
            color: white;
            padding: 12px;
            border-radius: 8px;
            margin-bottom: 20px;
            text-align: center;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            animation: fadeIn 0.3s ease;
        }
        
        .success-message {
            background: #48bb78;
            color: white;
            padding: 12px;
            border-radius: 8px;
            margin-bottom: 20px;
            text-align: center;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            animation: fadeIn 0.3s ease;
        }
        
        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        .login-link {
            text-align: center;
            margin-top: 20px;
            color: #666;
        }
        
        .login-link a {
            color: #667eea;
            text-decoration: none;
            font-weight: 500;
            display: inline-flex;
            align-items: center;
            gap: 5px;
        }
        
        .login-link a:hover {
            text-decoration: underline;
        }
        
        .password-requirements {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 8px;
            margin-top: 20px;
            font-size: 13px;
            color: #666;
        }
        
        .password-requirements h4 {
            color: #333;
            margin-bottom: 10px;
            font-size: 14px;
        }
        
        .password-requirements ul {
            list-style: none;
            padding-left: 0;
        }
        
        .password-requirements li {
            margin-bottom: 5px;
            padding-left: 20px;
            position: relative;
        }
        
        .password-requirements li:before {
            content: "•";
            color: #667eea;
            position: absolute;
            left: 0;
        }
        
        .form-footer {
            text-align: center;
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #e1e5eb;
        }
        
        .form-footer p {
            color: #666;
            font-size: 14px;
        }
    </style>
</head>
<body>
    <div class="reset-container">
        <div class="reset-card">
            <div class="logo">
                <i class="fas fa-key"></i>
                <h1>Reset <span class="highlight">Password</span></h1>
                <p>Enter your username/email and new password</p>
            </div>
            
            <?php if($error): ?>
                <div class="error-message">
                    <i class="fas fa-exclamation-circle"></i> <?php echo $error; ?>
                </div>
            <?php endif; ?>
            
            <?php if($success): ?>
                <div class="success-message">
                    <i class="fas fa-check-circle"></i> <?php echo $success; ?>
                    <script>
                        // Redirect to login after 3 seconds if success
                        setTimeout(function() {
                            window.location.href = '/auth/login.php';
                        }, 3000);
                    </script>
                </div>
            <?php endif; ?>
            
            <form method="POST" action="" id="resetForm">
                <div class="form-group">
                    <label for="username">
                        <i class="fas fa-user"></i> Username or Email
                    </label>
                    <input type="text" id="username" name="username" class="form-control" 
                           value="<?php echo htmlspecialchars($username ?? ''); ?>" 
                           placeholder="Enter your username or email" required>
                </div>
                
                <div class="form-group">
                    <label for="new_password">
                        <i class="fas fa-lock"></i> New Password
                    </label>
                    <input type="password" id="new_password" name="new_password" class="form-control" 
                           placeholder="Enter new password (min. 6 characters)" required>
                    <div id="passwordStrength" class="password-strength"></div>
                </div>
                
                <div class="form-group">
                    <label for="confirm_password">
                        <i class="fas fa-lock"></i> Confirm Password
                    </label>
                    <input type="password" id="confirm_password" name="confirm_password" class="form-control" 
                           placeholder="Confirm your new password" required>
                    <div id="passwordMatch" class="password-strength"></div>
                </div>
                
                <button type="submit" class="btn-reset" id="resetBtn">
                    <i class="fas fa-sync-alt"></i> Reset Password
                </button>
            </form>
            
            <div class="password-requirements">
                <h4>Password Requirements:</h4>
                <ul>
                    <li>Minimum 6 characters</li>
                    <li>Use a mix of letters and numbers</li>
                    <li>Avoid common passwords</li>
                    <li>Do not use personal information</li>
                </ul>
            </div>
            
            <div class="login-link">
                <a href="/auth/login.php">
                    <i class="fas fa-arrow-left"></i> Back to Login
                </a>
            </div>
            
            <div class="form-footer">
                <p>Need help? <a href="mailto:support@phonestore.com">Contact Support</a></p>
            </div>
        </div>
    </div>
    
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const newPassword = document.getElementById('new_password');
            const confirmPassword = document.getElementById('confirm_password');
            const passwordStrength = document.getElementById('passwordStrength');
            const passwordMatch = document.getElementById('passwordMatch');
            const resetBtn = document.getElementById('resetBtn');
            
            // Password strength checker
            newPassword.addEventListener('input', function() {
                const password = this.value;
                let strength = 'weak';
                let message = 'Weak password';
                
                if (password.length >= 8) {
                    strength = 'medium';
                    message = 'Medium password';
                    
                    if (password.length >= 12 || /[A-Z]/.test(password) && /[0-9]/.test(password)) {
                        strength = 'strong';
                        message = 'Strong password';
                    }
                } else if (password.length >= 6) {
                    strength = 'medium';
                    message = 'Medium password';
                }
                
                passwordStrength.textContent = message;
                passwordStrength.className = 'password-strength ' + strength;
            });
            
            // Password match checker
            function checkPasswordMatch() {
                const password = newPassword.value;
                const confirm = confirmPassword.value;
                
                if (confirm === '') {
                    passwordMatch.textContent = '';
                    passwordMatch.className = 'password-strength';
                    return;
                }
                
                if (password === confirm) {
                    passwordMatch.textContent = 'Passwords match ✓';
                    passwordMatch.className = 'password-strength strong';
                    resetBtn.disabled = false;
                } else {
                    passwordMatch.textContent = 'Passwords do not match ✗';
                    passwordMatch.className = 'password-strength weak';
                    resetBtn.disabled = true;
                }
            }
            
            newPassword.addEventListener('input', checkPasswordMatch);
            confirmPassword.addEventListener('input', checkPasswordMatch);
            
            // Form validation before submit
            document.getElementById('resetForm').addEventListener('submit', function(e) {
                const username = document.getElementById('username').value.trim();
                const password = newPassword.value;
                const confirm = confirmPassword.value;
                
                if (!username) {
                    e.preventDefault();
                    alert('Please enter your username or email');
                    return;
                }
                
                if (password.length < 6) {
                    e.preventDefault();
                    alert('Password must be at least 6 characters long');
                    return;
                }
                
                if (password !== confirm) {
                    e.preventDefault();
                    alert('Passwords do not match');
                    return;
                }
                
                // Show loading state
                resetBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Resetting Password...';
                resetBtn.disabled = true;
            });
            
            // Auto-focus on username field
            document.getElementById('username').focus();
            
            // Test credentials for development
            const urlParams = new URLSearchParams(window.location.search);
            if (urlParams.get('test') === 'admin') {
                document.getElementById('username').value = 'admin';
                document.getElementById('new_password').value = 'newpassword123';
                document.getElementById('confirm_password').value = 'newpassword123';
                checkPasswordMatch();
            }
        });
    </script>
</body>
</html>
