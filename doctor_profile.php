<?php
session_start();
require_once 'models/Doctor.php';

// Check if doctor is logged in
if(!isset($_SESSION['doctor_id'])) {
    header("Location: login.php");
    exit();
}

$error = '';
$success = '';
$doctor = new Doctor();
$doctor_info = $doctor->getDoctorById($_SESSION['doctor_id']);

// Debug logging
error_log("Doctor ID: " . $_SESSION['doctor_id']);
error_log("Doctor info from database: " . print_r($doctor_info, true));
error_log("Specialization value: " . ($doctor_info['specialization'] ?? 'not set'));

// Handle status update
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['status'])) {
    $new_status = $_POST['status'];
    
    // Debug logging
    error_log("Attempting to update status for doctor ID: " . $_SESSION['doctor_id']);
    error_log("New status: " . $new_status);
    
    if ($doctor->updateDoctorStatus($_SESSION['doctor_id'], $new_status)) {
        $success = "Status updated successfully!";
        // Refresh doctor info from database
        $doctor_info = $doctor->getDoctorById($_SESSION['doctor_id']);
        error_log("Status update successful");
        error_log("Updated doctor info: " . print_r($doctor_info, true));
    } else {
        $error = "Error updating status.";
        error_log("Status update failed");
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Zarqa University - Doctor Profile</title>
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
        
        .status-section {
            text-align: center;
            margin: 2rem 0;
            padding: 2.5rem;
            background: linear-gradient(145deg, #ffffff, #f8f9fa);
            border-radius: 15px;
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 2rem;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.05);
            max-width: 400px;
            margin-left: auto;
            margin-right: auto;
        }
        
        .status-section h2 {
            color: #2c3e50;
            font-size: 1.8rem;
            margin: 0;
            display: flex;
            align-items: center;
            gap: 0.8rem;
            width: 100%;
            justify-content: center;
            padding-bottom: 1rem;
            border-bottom: 2px solid #eee;
        }
        
        .status-section h2 i {
            color: var(--primary-color);
            font-size: 1.6rem;
        }
        
        .status-buttons {
            display: flex;
            flex-direction: column;
            gap: 1.2rem;
            width: 100%;
        }
        
        .radio-group {
            display: flex;
            flex-direction: column;
            gap: 1rem;
            width: 100%;
            padding: 1rem;
            background: white;
            border-radius: 10px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05);
        }
        
        .radio-option {
            display: flex;
            align-items: center;
            gap: 1rem;
            padding: 1rem;
            border-radius: 8px;
            cursor: pointer;
            transition: all 0.3s ease;
        }
        
        .radio-option:hover {
            background: #f8f9fa;
        }
        
        .radio-option input[type="radio"] {
            width: 20px;
            height: 20px;
            cursor: pointer;
        }
        
        .radio-option label {
            display: flex;
            align-items: center;
            gap: 0.8rem;
            font-size: 1.1rem;
            font-weight: 500;
            cursor: pointer;
            flex: 1;
        }
        
        .radio-option.online label {
            color: #27ae60;
        }
        
        .radio-option.offline label {
            color: #c0392b;
        }
        
        .radio-option i {
            font-size: 1.2rem;
        }
        
        .submit-button {
            padding: 1rem 2rem;
            border: none;
            border-radius: 10px;
            background: var(--primary-color);
            color: white;
            font-size: 1.1rem;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.3s ease;
            width: 100%;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.8rem;
        }
        
        .submit-button:hover {
            background: #1a252f;
            transform: translateY(-2px);
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        }
        
        .current-status {
            font-size: 1.3rem;
            padding: 1.2rem 2.5rem;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.8rem;
            font-weight: 600;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05);
            width: 100%;
            margin-bottom: 1rem;
        }
        
        .current-status.online {
            background: linear-gradient(145deg, rgba(46, 204, 113, 0.1), rgba(39, 174, 96, 0.1));
            color: #27ae60;
        }
        
        .current-status.offline {
            background: linear-gradient(145deg, rgba(231, 76, 60, 0.1), rgba(192, 57, 43, 0.1));
            color: #c0392b;
        }

        .current-status i {
            font-size: 1.1rem;
        }

        .status-info {
            display: flex;
            flex-direction: column;
            gap: 1rem;
            width: 100%;
            align-items: center;
        }
        
        .home-link {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.8rem 1.5rem;
            background-color: var(--primary-color);
            color: white;
            text-decoration: none;
            border-radius: 5px;
            transition: all 0.3s ease;
            margin-right: 1rem;
        }
        
        .home-link:hover {
            background-color: #1a252f;
            transform: translateY(-2px);
        }
        
        .home-link i {
            font-size: 1.1rem;
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
        
        .navbar {
            background: white;
            padding: 0.8rem 2rem;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
            display: flex;
            justify-content: space-between;
            align-items: center;
            position: sticky;
            top: 0;
            z-index: 1000;
        }

        .nav-brand {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            font-size: 1.1rem;
            color: var(--primary-color);
            font-weight: 500;
        }
        
        .nav-brand i {
            font-size: 1.2rem;
        }

        .nav-links {
            display: flex;
            align-items: center;
            gap: 1rem;
        }
        
        .home-link {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.5rem 1rem;
            background-color: var(--primary-color);
            color: white;
            text-decoration: none;
            border-radius: 5px;
            transition: all 0.3s ease;
            font-size: 0.9rem;
        }
        
        .home-link:hover {
            background-color: #1a252f;
            transform: translateY(-2px);
        }
        
        .home-link i {
            font-size: 1rem;
        }

        .logout-link {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.5rem 1rem;
            background-color: #f8f9fa;
            color: #dc3545;
            text-decoration: none;
            border-radius: 5px;
            transition: all 0.3s ease;
            font-size: 0.9rem;
            border: 1px solid #dc3545;
        }
        
        .logout-link:hover {
            background-color: #dc3545;
            color: white;
            transform: translateY(-2px);
        }
        
        .logout-link i {
            font-size: 1rem;
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
            <a href="logout.php" class="logout-link"><i class="fas fa-sign-out-alt"></i> Logout</a>
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
            <h1><i class="fas fa-user-doctor"></i> Zarqa University Doctor Profile</h1>
            <p>Manage your availability and profile information</p>
        </div>

        <?php if($doctor_info): ?>
            <div class="profile-info">
                <div class="info-card">
                    <i class="fas fa-user"></i>
                    <h3>Name</h3>
                    <p><?php echo htmlspecialchars($doctor_info['name']); ?></p>
                </div>
                
                <div class="info-card">
                    <i class="fas fa-envelope"></i>
                    <h3>Email</h3>
                    <p><?php echo htmlspecialchars($doctor_info['email']); ?></p>
                </div>
                
                <div class="info-card">
                    <i class="fas fa-door-open"></i>
                    <h3>Office Number</h3>
                    <p><?php echo htmlspecialchars($doctor_info['officeno']); ?></p>
                </div>
                
                <div class="info-card">
                    <i class="fas fa-graduation-cap"></i>
                    <h3>Specialization</h3>
                    <p><?php echo htmlspecialchars($doctor_info['specialization']); ?></p>
                </div>
            </div>

            <div class="status-section">
                <h2><i class="fas fa-user-clock"></i> Update Availability Status</h2>
                <div class="status-info">
                    <div class="current-status <?php echo $doctor_info['status']; ?>">
                        <i class="fas fa-circle"></i>
                        Currently <?php echo ucfirst($doctor_info['status']); ?>
                    </div>
                    
                    <form method="POST" action="" class="status-buttons">
                        <div class="radio-group">
                            <div class="radio-option online">
                                <input type="radio" id="online" name="status" value="online" <?php echo $doctor_info['status'] === 'online' ? 'checked' : ''; ?>>
                                <label for="online">
                                    <i class="fas fa-toggle-on"></i>
                                    Online
                                </label>
                            </div>
                            <div class="radio-option offline">
                                <input type="radio" id="offline" name="status" value="offline" <?php echo $doctor_info['status'] === 'offline' ? 'checked' : ''; ?>>
                                <label for="offline">
                                    <i class="fas fa-toggle-off"></i>
                                    Offline
                                </label>
                            </div>
                        </div>
                        <button type="submit" class="submit-button">
                            <i class="fas fa-save"></i>
                            Update Status
                        </button>
                    </form>
                </div>
            </div>
        <?php else: ?>
            <div class="error-message">
                <i class="fas fa-exclamation-circle"></i>
                Error loading doctor information.
            </div>
        <?php endif; ?>

        <div class="return-home">
            <a href="index.php" class="return-home-button">
                <i class="fas fa-home"></i> Return to Home Page
            </a>
        </div>
    </div>

    <script>
        // Add loading state to status buttons
        document.querySelectorAll('.status-button').forEach(button => {
            button.addEventListener('click', function() {
                // Only disable the clicked button
                this.disabled = true;
                const originalContent = this.innerHTML;
                this.innerHTML = '<span class="loading"></span> Updating...';
                
                // Re-enable the button after 2 seconds if the page hasn't reloaded
                setTimeout(() => {
                    if (this.disabled) {
                        this.disabled = false;
                        this.innerHTML = originalContent;
                    }
                }, 2000);
            });
        });
    </script>
</body>
</html> 