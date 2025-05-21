<?php
session_start();
require_once 'models/Doctor.php';

// Check if student is logged in
if(!isset($_SESSION['student_id'])) {
    header("Location: student_login.php");
    exit();
}

$error = '';
$doctor = new Doctor();
$doctors = $doctor->getAllDoctors();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Doctors - Availability System</title>
    <link rel="stylesheet" href="styles.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        body {
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
            min-height: 100vh;
            margin: 0;
            padding: 20px;
            font-family: 'Poppins', sans-serif;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }

        .header {
            text-align: center;
            margin-bottom: 2rem;
            padding: 1rem;
            background: white;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }

        .header h1 {
            color: #2c3e50;
            margin: 0;
            font-size: 2rem;
        }

        .doctors-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 20px;
            margin-top: 2rem;
        }

        .doctor-card {
            background: white;
            border-radius: 10px;
            padding: 1.5rem;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            transition: transform 0.3s ease;
        }

        .doctor-card:hover {
            transform: translateY(-5px);
        }

        .doctor-name {
            font-size: 1.2rem;
            color: #2c3e50;
            margin-bottom: 0.5rem;
            font-weight: 600;
        }

        .doctor-specialization {
            color: #7f8c8d;
            margin-bottom: 1rem;
        }

        .doctor-status {
            display: inline-block;
            padding: 0.5rem 1rem;
            border-radius: 20px;
            font-size: 0.9rem;
            font-weight: 500;
        }

        .status-online {
            background-color: rgba(46, 204, 113, 0.1);
            color: #27ae60;
        }

        .status-offline {
            background-color: rgba(231, 76, 60, 0.1);
            color: #e74c3c;
        }

        .back-link {
            display: inline-block;
            margin-top: 1rem;
            color: #2c3e50;
            text-decoration: none;
            padding: 0.8rem 1.5rem;
            border-radius: 8px;
            background: #f8f9fa;
            transition: all 0.3s ease;
        }

        .back-link:hover {
            background: #e9ecef;
            transform: translateX(-5px);
        }

        .error-message {
            background-color: rgba(231, 76, 60, 0.1);
            color: #e74c3c;
            padding: 1rem;
            border-radius: 8px;
            margin-bottom: 1rem;
            text-align: center;
        }

        .no-doctors {
            text-align: center;
            padding: 2rem;
            background: white;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }

        .no-doctors i {
            font-size: 3rem;
            color: #7f8c8d;
            margin-bottom: 1rem;
        }

        .no-doctors p {
            color: #2c3e50;
            font-size: 1.1rem;
        }

        @media (max-width: 768px) {
            .doctors-grid {
                grid-template-columns: 1fr;
            }
        }

        .search-container {
            margin: 1rem 0;
            width: 100%;
            max-width: 500px;
            margin: 1rem auto;
        }
        
        .search-box {
            position: relative;
            width: 100%;
        }
        
        .search-box input {
            width: 100%;
            padding: 0.8rem 1rem 0.8rem 2.5rem;
            border: 1px solid #ddd;
            border-radius: 25px;
            font-size: 1rem;
            transition: all 0.3s ease;
        }
        
        .search-box input:focus {
            outline: none;
            border-color: var(--primary-color);
            box-shadow: 0 0 0 2px rgba(26, 35, 126, 0.1);
        }
        
        .search-box i {
            position: absolute;
            left: 1rem;
            top: 50%;
            transform: translateY(-50%);
            color: #666;
        }
        
        .doctor-card.hidden {
            display: none;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1><i class="fas fa-user-doctor"></i> Available Doctors</h1>
            <p>View the current status of all doctors</p>
            <div class="search-container">
                <div class="search-box">
                    <i class="fas fa-search"></i>
                    <input type="text" id="searchInput" placeholder="Search doctors by name...">
                </div>
            </div>
        </div>

        <?php if($error): ?>
            <div class="error-message">
                <i class="fas fa-exclamation-circle"></i>
                <?php echo htmlspecialchars($error); ?>
            </div>
        <?php endif; ?>

        <?php if(empty($doctors)): ?>
            <div class="no-doctors">
                <i class="fas fa-user-doctor"></i>
                <p>No doctors available at the moment.</p>
            </div>
        <?php else: ?>
            <div class="doctors-grid">
                <?php foreach($doctors as $doctor): ?>
                    <div class="doctor-card">
                        <div class="doctor-name">
                            <i class="fas fa-user-doctor"></i>
                            <?php echo htmlspecialchars($doctor['name']); ?>
                        </div>
                        <div class="doctor-specialization">
                            <i class="fas fa-door-open"></i>
                            Office: <?php echo htmlspecialchars($doctor['specialization']); ?>
                        </div>
                        <div class="doctor-status <?php echo $doctor['status'] === 'online' ? 'status-online' : 'status-offline'; ?>">
                            <i class="fas fa-circle"></i>
                            <?php echo ucfirst($doctor['status']); ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <a href="index.php" class="back-link">
            <i class="fas fa-arrow-left"></i> Back to Home
        </a>
    </div>

    <script>
        // Auto refresh the page every 30 seconds to update doctor status
        setTimeout(function() {
            window.location.reload();
        }, 30000);

        // Search functionality
        document.getElementById('searchInput').addEventListener('input', function(e) {
            const searchTerm = e.target.value.toLowerCase();
            const doctorCards = document.querySelectorAll('.doctor-card');
            
            doctorCards.forEach(card => {
                const doctorName = card.querySelector('.doctor-name').textContent.toLowerCase();
                if (doctorName.includes(searchTerm)) {
                    card.classList.remove('hidden');
                } else {
                    card.classList.add('hidden');
                }
            });
        });
    </script>
</body>
</html> 