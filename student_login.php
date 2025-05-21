<?php
session_start();
require_once 'models/Student.php';

// Redirect if already logged in
if(isset($_SESSION['student_id'])) {
    header("Location: index.php");
    exit();
}

$error = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $password = $_POST['password'];
    
    $student = new Student();
    $result = $student->authenticate($email, $password);
    
    if ($result) {
        $_SESSION['student_id'] = $result['id'];
        $_SESSION['student_name'] = $result['name'];
        $_SESSION['student_email'] = $result['email'];
        $_SESSION['student_id_number'] = $result['student_id'];
        $_SESSION['student_department'] = $result['department'];
        
        header("Location: index.php");
        exit();
    } else {
        $error = "Invalid email or password";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Login - Zarqa University</title>
    <link rel="stylesheet" href="styles.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        .login-container {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(rgba(0,0,0,0.5), rgba(0,0,0,0.5)), url('https://via.placeholder.com/1920x1080');
            background-size: cover;
            background-position: center;
        }
        
        .login-form {
            background: white;
            padding: 2rem;
            border-radius: 10px;
            box-shadow: 0 0 20px rgba(0,0,0,0.1);
            width: 100%;
            max-width: 400px;
        }
        
        .login-form h2 {
            text-align: center;
            color: #1a237e;
            margin-bottom: 2rem;
        }
        
        .form-group {
            margin-bottom: 1.5rem;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 0.5rem;
            color: #333;
        }
        
        .form-group input {
            width: 100%;
            padding: 0.8rem;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 1rem;
        }
        
        .login-button {
            width: 100%;
            padding: 1rem;
            background-color: #1a237e;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 1rem;
            transition: background-color 0.3s ease;
        }
        
        .login-button:hover {
            background-color: #0d1642;
        }
        
        .error-message {
            color: red;
            text-align: center;
            margin-bottom: 1rem;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <form class="login-form" method="POST" action="">
            <h2><i class="fas fa-user-graduate"></i> Student Login</h2>
            
            <?php if($error): ?>
                <div class="error-message">
                    <i class="fas fa-exclamation-circle"></i>
                    <?php echo htmlspecialchars($error); ?>
                </div>
            <?php endif; ?>
            
            <div class="form-group">
                <label for="email"><i class="fas fa-envelope"></i> Email Address</label>
                <input type="email" id="email" name="email" required>
            </div>
            
            <div class="form-group">
                <label for="password"><i class="fas fa-lock"></i> Password</label>
                <div style="position: relative;">
                    <input type="password" id="password" name="password" required>
                    <i class="fas fa-eye toggle-password" style="position: absolute; right: 10px; top: 50%; transform: translateY(-50%); cursor: pointer; color: #666;"></i>
                </div>
            </div>
            
            <div style="text-align: right; margin-bottom: 1rem;">
                <a href="student_forgot_password.php" style="color: var(--secondary-color); text-decoration: none; font-size: 0.9rem;">
                    <i class="fas fa-key"></i> Forgot Password?
                </a>
            </div>
            
            <button type="submit" class="login-button">
                <i class="fas fa-sign-in-alt"></i> Login
            </button>
            
            <div style="text-align: center; margin-top: 1rem;">
                <p>Don't have an account? <a href="register.php" style="color: var(--secondary-color); text-decoration: none;">
                    <i class="fas fa-user-plus"></i> Register here
                </a></p>
                <a href="index.php" style="color: var(--secondary-color); text-decoration: none; display: inline-block; margin-top: 0.5rem;">
                    <i class="fas fa-arrow-left"></i> Back to Home
                </a>
            </div>
        </form>
    </div>

    <script>
        // Add loading state to form submission
        document.querySelector('.login-form').addEventListener('submit', function() {
            const button = this.querySelector('.login-button');
            button.innerHTML = '<span class="loading"></span> Logging in...';
            button.disabled = true;
        });

        // Toggle password visibility
        document.querySelector('.toggle-password').addEventListener('click', function() {
            const passwordInput = document.getElementById('password');
            const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
            passwordInput.setAttribute('type', type);
            this.classList.toggle('fa-eye');
            this.classList.toggle('fa-eye-slash');
        });
    </script>
</body>
</html> 