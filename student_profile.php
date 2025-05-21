<?php
session_start();
require_once 'models/Student.php';

// Check if student is logged in
if(!isset($_SESSION['student_id'])) {
    header("Location: student_login.php");
    exit();
}

$error = '';
$success = '';
$student = new Student();
$student_info = $student->getStudentById($_SESSION['student_id']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Zarqa University - Student Profile</title>
    <link rel="stylesheet" href="styles.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        .profile-container {
            max-width: 800px;
            margin: 2rem auto;
            padding: 2rem;
            background: white;
            border-radius: 10px;
            box-shadow: 0 0 20px rgba(0,0,0,0.1);
        }
        
        .profile-header {
            text-align: center;
            margin-bottom: 2rem;
        }
        
        .profile-info {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 1.5rem;
            margin-bottom: 2rem;
        }
        
        .info-card {
            background: #f8f9fa;
            padding: 1.5rem;
            border-radius: 8px;
            text-align: center;
        }
        
        .info-card i {
            font-size: 2rem;
            color: var(--primary-color);
            margin-bottom: 1rem;
        }
        
        .nav-brand {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            font-size: 1.2rem;
            color: var(--primary-color);
        }
        
        .nav-brand i {
            font-size: 1.4rem;
        }
        
        .return-home {
            text-align: center;
            margin-top: 2rem;
            padding-top: 2rem;
            border-top: 1px solid #eee;
        }
        
        .return-home-button {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 1rem 2rem;
            background-color: var(--primary-color);
            color: white;
            text-decoration: none;
            border-radius: 5px;
            font-size: 1.1rem;
            transition: all 0.3s ease;
        }
        
        .return-home-button:hover {
            background-color: #1a252f;
            transform: translateY(-2px);
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        }
        
        .return-home-button i {
            font-size: 1.2rem;
        }
    </style>
</head>
<body>
    <nav class="navbar">
        <div class="nav-brand">
            <i class="fas fa-university"></i>
            <span>Zarqa University</span>
        </div>
        <div class="nav-links">
            <a href="index.php" class="home-link"><i class="fas fa-home"></i> Home</a>
            <a href="view_doctors.php"><i class="fas fa-user-doctor"></i> View Doctors</a>
            <a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
        </div>
    </nav>

    <div class="profile-container">
        <?php if($error): ?>
            <div class="error-message">
                <i class="fas fa-exclamation-circle"></i>
                <?php echo htmlspecialchars($error); ?>
            </div>
        <?php endif; ?>
        
        <?php if($success): ?>
            <div class="error-message" style="background-color: rgba(46, 204, 113, 0.1); color: var(--success-color);">
                <i class="fas fa-check-circle"></i>
                <?php echo htmlspecialchars($success); ?>
            </div>
        <?php endif; ?>

        <div class="profile-header">
            <h1><i class="fas fa-user-graduate"></i> Student Profile</h1>
            <p>View your profile information</p>
        </div>

        <?php if($student_info): ?>
            <div class="profile-info">
                <div class="info-card">
                    <i class="fas fa-user"></i>
                    <h3>Name</h3>
                    <p><?php echo htmlspecialchars($student_info['name']); ?></p>
                </div>
                
                <div class="info-card">
                    <i class="fas fa-envelope"></i>
                    <h3>Email</h3>
                    <p><?php echo htmlspecialchars($student_info['email']); ?></p>
                </div>
                
                <div class="info-card">
                    <i class="fas fa-id-card"></i>
                    <h3>Student ID</h3>
                    <p><?php echo htmlspecialchars($student_info['student_id']); ?></p>
                </div>
                
                <div class="info-card">
                    <i class="fas fa-building"></i>
                    <h3>Department</h3>
                    <p><?php echo htmlspecialchars($student_info['department']); ?></p>
                </div>
            </div>
        <?php else: ?>
            <div class="error-message">
                <i class="fas fa-exclamation-circle"></i>
                Error loading student information.
            </div>
        <?php endif; ?>

        <div class="return-home">
            <a href="index.php" class="return-home-button">
                <i class="fas fa-home"></i> Return to Home Page
            </a>
        </div>
    </div>
</body>
</html> 