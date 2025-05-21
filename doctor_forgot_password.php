<?php
session_start();
$error = '';
$success = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST['email']);
    
    if (empty($email)) {
        $error = "Please enter your email address";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Invalid email format";
    } else {
        require_once 'models/Doctor.php';
        $doctor = new Doctor();
        
        // Generate a random password
        $new_password = substr(str_shuffle("abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789!@#$%^&*"), 0, 12);
        $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
        
        if ($doctor->updatePassword($email, $hashed_password)) {
            // Send email with new password
            $to = $email;
            $subject = "Your New Password - Doctor Account";
            $message = "Your password has been reset.\n\n";
            $message .= "Your new password is: " . $new_password . "\n\n";
            $message .= "Please login with this password and change it immediately for security reasons.\n\n";
            $message .= "If you did not request this password reset, please contact the administrator immediately.";
            $headers = "From: noreply@zarqauniversity.edu.jo\r\n";
            $headers .= "Reply-To: noreply@zarqauniversity.edu.jo\r\n";
            $headers .= "X-Mailer: PHP/" . phpversion();
            
            if(mail($to, $subject, $message, $headers)) {
                $success = "A new password has been sent to your email.";
            } else {
                $error = "Failed to send email. Please try again.";
            }
        } else {
            $error = "Email not found in our records.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Doctor Forgot Password - Zarqa University</title>
    <link rel="stylesheet" href="styles.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        .forgot-container {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(rgba(0,0,0,0.5), rgba(0,0,0,0.5)), url('https://via.placeholder.com/1920x1080');
            background-size: cover;
            background-position: center;
        }
        
        .forgot-form {
            background: white;
            padding: 2rem;
            border-radius: 10px;
            box-shadow: 0 0 20px rgba(0,0,0,0.1);
            width: 100%;
            max-width: 400px;
        }
        
        .forgot-form h2 {
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
        
        .submit-button {
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
        
        .submit-button:hover {
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
            color: #27ae60;
            padding: 1rem;
            border-radius: 5px;
            margin-bottom: 1.5rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
    </style>
</head>
<body>
    <div class="forgot-container">
        <form class="forgot-form" method="POST" action="">
            <h2><i class="fas fa-user-md"></i> Doctor Password Reset</h2>
            
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
                <label for="email"><i class="fas fa-envelope"></i> Email Address</label>
                <input type="email" id="email" name="email" required value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>">
            </div>
            
            <button type="submit" class="submit-button">
                <i class="fas fa-paper-plane"></i> Reset Password
            </button>
            
            <div style="text-align: center; margin-top: 1rem;">
                <a href="login.php" style="color: var(--secondary-color); text-decoration: none; display: inline-block; margin-top: 0.5rem;">
                    <i class="fas fa-arrow-left"></i> Back to Login
                </a>
            </div>
        </form>
    </div>
</body>
</html> 