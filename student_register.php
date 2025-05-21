<?php
session_start();

// Redirect if already logged in
if(isset($_SESSION['student_id'])) {
    header("Location: index.php");
    exit();
}

$error = '';
$success = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST['name'] ?? '';
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';
    $student_id = $_POST['student_id'] ?? '';
    $department = $_POST['department'] ?? '';
    
    // Validate input
    if (empty($name) || empty($email) || empty($password) || empty($confirm_password) || empty($student_id) || empty($department)) {
        $error = "Please fill in all fields";
    } elseif ($password !== $confirm_password) {
        $error = "Passwords do not match";
    } elseif (strlen($password) < 6) {
        $error = "Password must be at least 6 characters long";
    } else {
        // Check if students.csv exists, if not create it
        if (!file_exists("students.csv")) {
            $handle = fopen("students.csv", "w");
            if ($handle !== FALSE) {
                fputcsv($handle, ['id', 'name', 'email', 'password', 'student_id', 'department']);
                fclose($handle);
            } else {
                $error = "System error: Cannot create students.csv file.";
            }
        }

        // Check if email or student_id already exists
        if (empty($error)) {
            if (($handle = fopen("students.csv", "r")) !== FALSE) {
                // Skip header row
                fgetcsv($handle);
                
                while (($data = fgetcsv($handle)) !== FALSE) {
                    if ($data[2] === $email) {
                        $error = "Email already registered";
                        break;
                    }
                    if ($data[4] === $student_id) {
                        $error = "Student ID already registered";
                        break;
                    }
                }
                fclose($handle);
                
                if (empty($error)) {
                    // Generate new student ID
                    $new_id = 1;
                    if (($handle = fopen("students.csv", "r")) !== FALSE) {
                        // Skip header
                        fgetcsv($handle);
                        while (($data = fgetcsv($handle)) !== FALSE) {
                            $new_id = max($new_id, intval($data[0]) + 1);
                        }
                        fclose($handle);
                    }
                    
                    // Hash password
                    $hashed_password = password_hash($password, PASSWORD_DEFAULT);
                    
                    // Add new student to CSV
                    if (($handle = fopen("students.csv", "a")) !== FALSE) {
                        $new_student = [
                            $new_id,
                            $name,
                            $email,
                            $hashed_password,
                            $student_id,
                            $department
                        ];
                        
                        if (fputcsv($handle, $new_student)) {
                            fclose($handle);
                            $success = "Registration successful! Redirecting to login...";
                            
                            // Clear form data
                            $name = $email = $student_id = $department = '';
                            
                            // Redirect to login page after 2 seconds
                            header("refresh:2;url=student_login.php");
                            exit();
                        } else {
                            $error = "Error saving registration data.";
                        }
                    } else {
                        $error = "System error: Cannot write to students.csv file.";
                    }
                }
            } else {
                $error = "System error: Cannot read students.csv file.";
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
    <title>Student Registration - Availability System</title>
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
            max-width: 500px;
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
        
        .form-group input, .form-group select {
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

        .success-message {
            background-color: rgba(46, 204, 113, 0.1);
            color: var(--success-color);
            text-align: center;
            margin-bottom: 1rem;
            padding: 1rem;
            border-radius: 5px;
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
    <div class="login-container">
        <form class="login-form" method="POST" action="">
            <div class="user-type-selector">
                <a href="register.php"><i class="fas fa-user-md"></i> Doctor Registration</a>
                <a href="student_register.php" class="active"><i class="fas fa-user-graduate"></i> Student Registration</a>
            </div>

            <h2><i class="fas fa-user-graduate"></i> Student Registration</h2>
            
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
                <input type="text" id="name" name="name" value="<?php echo htmlspecialchars($name ?? ''); ?>" required>
            </div>
            
            <div class="form-group">
                <label for="email"><i class="fas fa-envelope"></i> Email Address</label>
                <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($email ?? ''); ?>" required>
            </div>
            
            <div class="form-group">
                <label for="student_id"><i class="fas fa-id-card"></i> Student ID</label>
                <input type="text" id="student_id" name="student_id" value="<?php echo htmlspecialchars($student_id ?? ''); ?>" required>
            </div>
            
            <div class="form-group">
                <label for="department"><i class="fas fa-building"></i> Department</label>
                <select id="department" name="department" required>
                    <option value="">Select Department</option>
                    <option value="Computer Science" <?php echo ($department ?? '') === 'Computer Science' ? 'selected' : ''; ?>>Computer Science</option>
                    <option value="Artificial Intelligence" <?php echo ($department ?? '') === 'Artificial Intelligence' ? 'selected' : ''; ?>>Artificial Intelligence</option>
                    <option value="Software Engineering" <?php echo ($department ?? '') === 'Software Engineering' ? 'selected' : ''; ?>>Software Engineering</option>
                    <option value="Cyber Security" <?php echo ($department ?? '') === 'Cyber Security' ? 'selected' : ''; ?>>Cyber Security</option>
                </select>
            </div>
            
            <div class="form-group">
                <label for="password"><i class="fas fa-lock"></i> Password</label>
                <input type="password" id="password" name="password" required>
            </div>
            
            <div class="form-group">
                <label for="confirm_password"><i class="fas fa-lock"></i> Confirm Password</label>
                <input type="password" id="confirm_password" name="confirm_password" required>
            </div>
            
            <button type="submit" class="login-button">
                <i class="fas fa-user-plus"></i> Register
            </button>
            
            <div style="text-align: center; margin-top: 1rem;">
                <p>Already have an account? <a href="student_login.php" style="color: var(--secondary-color); text-decoration: none;">
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
        document.querySelector('.login-form').addEventListener('submit', function() {
            const button = this.querySelector('.login-button');
            button.innerHTML = '<span class="loading"></span> Registering...';
            button.disabled = true;
        });
    </script>
</body>
</html> 