<?php
session_start();
require_once 'models/Doctor.php';

// Redirect if already logged in
if(isset($_SESSION['doctor_id'])) {
    header("Location: index.php");
    exit();
}

$error = '';
$success = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';
    $officeno = trim($_POST['officeno'] ?? '');
    $specialization = trim($_POST['specialization'] ?? '');
    
    // Validation
    if (empty($name) || empty($email) || empty($password) || empty($confirm_password) || empty($officeno) || empty($specialization)) {
        $error = "All fields are required";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Invalid email format";
    } elseif ($password !== $confirm_password) {
        $error = "Passwords do not match";
    } elseif (strlen($password) < 6) {
        $error = "Password must be at least 6 characters long";
    } else {
        $doctor = new Doctor();
        
        // Check if email already exists
        if ($doctor->getDoctorByEmail($email)) {
            $error = "Email already exists";
        } else {
            // Register the doctor
            if ($doctor->registerDoctor($name, $email, $password, $officeno, $specialization)) {
                $success = "Registration successful! You can now login.";
            } else {
                $error = "Registration failed. Please try again.";
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Doctor Registration - Zarqa University</title>
    <link rel="stylesheet" href="styles.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        .register-container {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(rgba(0,0,0,0.5), rgba(0,0,0,0.5)), url('https://via.placeholder.com/1920x1080');
            background-size: cover;
            background-position: center;
            padding: 2rem;
        }
        
        .register-form {
            background: white;
            padding: 2rem;
            border-radius: 10px;
            box-shadow: 0 0 20px rgba(0,0,0,0.1);
            width: 100%;
            max-width: 500px;
        }
        
        .register-form h2 {
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
        
        .register-button {
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
        
        .register-button:hover {
            background-color: #0d1642;
        }
        
        .error-message {
            background-color: rgba(220, 53, 69, 0.1);
            color: #dc3545;
            padding: 1rem;
            border-radius: 5px;
            margin-bottom: 1.5rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        
        .success-message {
            background-color: rgba(46, 204, 113, 0.1);
            color: #2ecc71;
            padding: 1rem;
            border-radius: 5px;
            margin-bottom: 1.5rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        
        .user-type-selector {
            text-align: center;
            margin-bottom: 2rem;
        }
        
        .user-type-selector a {
            display: inline-block;
            padding: 0.5rem 1rem;
            margin: 0 0.5rem;
            color: #666;
            text-decoration: none;
            border-radius: 5px;
            transition: all 0.3s ease;
        }
        
        .user-type-selector a.active {
            background-color: #1a237e;
            color: white;
        }
        
        .user-type-selector a:hover:not(.active) {
            background-color: #f0f0f0;
        }
    </style>
</head>
<body>
    <div class="register-container">
        <form class="register-form" method="POST" action="">
            <div class="user-type-selector">
                <a href="register.php" class="active"><i class="fas fa-user-md"></i> Doctor Registration</a>
                <a href="student_register.php"><i class="fas fa-user-graduate"></i> Student Registration</a>
            </div>
            
            <h2><i class="fas fa-user-md"></i> Doctor Registration</h2>
            
            <?php if($error): ?>
                <div class="error-message">
                    <i class="fas fa-exclamation-circle"></i>
                    <?php echo htmlspecialchars($error); ?>
                </div>
            <?php endif; ?>
            
            <?php if($success): ?>
                <div class="success-message">
                    <i class="fas fa-check-circle"></i>
                    <?php echo htmlspecialchars($success); ?>
                </div>
            <?php endif; ?>
            
            <div class="form-group">
                <label for="name"><i class="fas fa-user"></i> Full Name</label>
                <input type="text" id="name" name="name" required value="<?php echo isset($_POST['name']) ? htmlspecialchars($_POST['name']) : ''; ?>">
            </div>
            
            <div class="form-group">
                <label for="email"><i class="fas fa-envelope"></i> Email Address</label>
                <input type="email" id="email" name="email" required value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>">
            </div>
            
            <div class="form-group">
                <label for="officeno"><i class="fas fa-door-open"></i> Office Number</label>
                <input type="text" id="officeno" name="officeno" required value="<?php echo isset($_POST['officeno']) ? htmlspecialchars($_POST['officeno']) : ''; ?>">
            </div>
            
            <div class="form-group">
                <label for="specialization"><i class="fas fa-graduation-cap"></i> Specialization</label>
                <input type="text" id="specialization" name="specialization" required value="<?php echo isset($_POST['specialization']) ? htmlspecialchars($_POST['specialization']) : ''; ?>">
            </div>
            
            <div class="form-group">
                <label for="password"><i class="fas fa-lock"></i> Password</label>
                <div style="position: relative;">
                    <input type="password" id="password" name="password" required>
                    <i class="fas fa-eye toggle-password" style="position: absolute; right: 10px; top: 50%; transform: translateY(-50%); cursor: pointer; color: #666;"></i>
                </div>
            </div>
            
            <div class="form-group">
                <label for="confirm_password"><i class="fas fa-lock"></i> Confirm Password</label>
                <div style="position: relative;">
                    <input type="password" id="confirm_password" name="confirm_password" required>
                    <i class="fas fa-eye toggle-password" style="position: absolute; right: 10px; top: 50%; transform: translateY(-50%); cursor: pointer; color: #666;"></i>
                </div>
            </div>
            
            <button type="submit" class="register-button">
                <i class="fas fa-user-plus"></i> Register
            </button>
            
            <div style="text-align: center; margin-top: 1rem;">
                <p>Already have an account? <a href="login.php" style="color: var(--secondary-color); text-decoration: none;">
                    <i class="fas fa-sign-in-alt"></i> Login here
                </a></p>
                <a href="index.php" style="color: var(--secondary-color); text-decoration: none; display: inline-block; margin-top: 0.5rem;">
                    <i class="fas fa-arrow-left"></i> Back to Home
                </a>
            </div>
        </form>
    </div>

    <script>
        // Add loading state to form submission
        document.querySelector('.register-form').addEventListener('submit', function() {
            const button = this.querySelector('.register-button');
            button.innerHTML = '<span class="loading"></span> Registering...';
            button.disabled = true;
        });

        // Toggle password visibility
        document.querySelectorAll('.toggle-password').forEach(function(toggle) {
            toggle.addEventListener('click', function() {
                const input = this.previousElementSibling;
                const type = input.getAttribute('type') === 'password' ? 'text' : 'password';
                input.setAttribute('type', type);
                this.classList.toggle('fa-eye');
                this.classList.toggle('fa-eye-slash');
            });
        });
    </script>
</body>
</html> 