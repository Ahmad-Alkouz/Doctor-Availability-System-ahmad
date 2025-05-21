<?php
session_start();
require_once 'models/Doctor.php';

$doctor = new Doctor();
$doctors = $doctor->getAllDoctors();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Zarqa University - Doctor Availability System</title>
    <link rel="stylesheet" href="styles.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="https://eservices.zu.edu.jo/StudentPortal2/Login/loginPage">
</head>
<body>
    <header>
        <nav>
            <div class="logo">
                <a href="https://zu.edu.jo" target="_blank" style="text-decoration: none; color: inherit;">
                    <h1><i class="fas fa-university"></i> Zarqa University</h1>
                </a>
            </div>
            <ul class="nav-links">
                <li><a href="index.php" class="active"><i class="fas fa-home"></i> Home</a></li>
                <li><a href="#doctors"><i class="fas fa-user-doctor"></i> Available Doctors</a></li>
                <?php if(isset($_SESSION['doctor_id'])): ?>
                    <li class="user-info">
                        <span class="logged-in-as">
                            <i class="fas fa-user"></i> Logged in as: <?php echo htmlspecialchars($_SESSION['doctor_name']); ?>
                        </span>
                    </li>
                    <li><a href="doctor_profile.php"><i class="fas fa-user"></i> My Profile</a></li>
                    <li><a href="logout.php" class="logout-link"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
                <?php elseif(isset($_SESSION['student_id'])): ?>
                    <li class="user-info">
                        <span class="logged-in-as">
                            <i class="fas fa-user-graduate"></i> Logged in as: <?php echo htmlspecialchars($_SESSION['student_name']); ?>
                        </span>
                    </li>
                    <li><a href="student_profile.php"><i class="fas fa-user"></i> My Profile</a></li>
                    <li><a href="view_doctors.php"><i class="fas fa-user-doctor"></i> View Doctors</a></li>
                    <li><a href="logout.php" class="logout-link"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
                <?php else: ?>
                    <li><a href="login.php" class="login-link"><i class="fas fa-user-doctor"></i> Doctor Login</a></li>
                    <li><a href="student_login.php" class="login-link"><i class="fas fa-user-graduate"></i> Student Login</a></li>
                    <li><a href="register.php" class="login-link" style="background-color: var(--success-color);"><i class="fas fa-user-plus"></i> Register</a></li>
                <?php endif; ?>
            </ul>
        </nav>
    </header>

    <section class="hero">
        <div class="hero-content">
            <h1>Zarqa University</h1>
            <p>Doctor Availability System - Real-time tracking of doctor availability and status updates</p>
            <div class="hero-buttons">
                <a href="#doctors" class="cta-button">
                    <i class="fas fa-user-doctor"></i> View Available Doctors
                </a>
                <?php if(!isset($_SESSION['doctor_id']) && !isset($_SESSION['student_id'])): ?>
                    <div class="login-options">
                        <a href="login.php" class="cta-button secondary">
                            <i class="fas fa-user-doctor"></i> Doctor Login
                        </a>
                        <a href="student_login.php" class="cta-button secondary">
                            <i class="fas fa-user-graduate"></i> Student Login
                        </a>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </section>

    <section id="doctors" class="doctors">
        <h2>Available Doctors</h2>
        <div class="search-container">
            <div class="search-box">
                <i class="fas fa-search"></i>
                <input type="text" id="searchInput" placeholder="Search doctors by name...">
            </div>
        </div>
        <div class="doctors-grid">
            <?php
            if (!empty($doctors)) {
                foreach($doctors as $doctor) {
                    $statusClass = $doctor['status'] == 'online' ? 'online' : 'offline';
                    $statusText = ucfirst($doctor['status']);
                    ?>
                    <div class="doctor-card <?php echo $statusClass; ?>">
                        <i class="fas fa-user-doctor"></i>
                        <h3><?php echo htmlspecialchars($doctor['name']); ?></h3>
                        <p>Office: <?php echo htmlspecialchars($doctor['officeno']); ?></p>
                        <p>Specialization: <?php echo htmlspecialchars($doctor['specialization']); ?></p>
                        <span class="status-indicator">
                            <i class="fas fa-circle"></i> <?php echo $statusText; ?>
                        </span>
                    </div>
                    <?php
                }
            } else {
                echo "<p>No doctors available at the moment.</p>";
            }
            ?>
        </div>
    </section>

    <footer>
        <div class="footer-content">
            <div class="footer-section">
                <h3>Quick Links</h3>
                <ul>
                    <li><a href="index.php">Home</a></li>
                    <li><a href="#doctors">Available Doctors</a></li>
                    <?php if(isset($_SESSION['doctor_id'])): ?>
                        <li><a href="doctor_profile.php">Profile</a></li>
                        <li><a href="logout.php">Logout</a></li>
                    <?php else: ?>
                        <li><a href="login.php">Doctor Login</a></li>
                    <?php endif; ?>
                </ul>
            </div>
            <div class="footer-section">
                <h3>Contact Support</h3>
                <p><i class="fas fa-envelope"></i> amedameen830@gmail.com</p>
                <p><i class="fas fa-phone"></i> +962770913911</p>
                <p><i class="fas fa-clock"></i> 24/7 Support</p>
            </div>
        </div>
        <div class="footer-bottom">
            <p>&copy; <?php echo date('Y'); ?> Zarqa University - Doctor Availability System. All rights reserved.</p>
            <p>Developed by: Ahmad Ameen Alkouz</p>
        </div>
    </footer>

    <style>
        /* ... existing styles ... */
        
        .search-container {
            margin: 1rem auto 2rem;
            width: 100%;
            max-width: 500px;
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
        
        .user-info {
            margin-right: 1rem;
        }
        
        .logged-in-as {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.5rem 1rem;
            background-color: rgba(46, 204, 113, 0.1);
            color: var(--success-color);
            border-radius: 20px;
            font-size: 0.9rem;
            font-weight: 500;
        }
        
        .logged-in-as i {
            font-size: 1rem;
        }
        
        @media (max-width: 768px) {
            .user-info {
                margin: 0.5rem 0;
                width: 100%;
                text-align: center;
            }
            
            .logged-in-as {
                justify-content: center;
            }
        }
        
        .footer-bottom {
            text-align: center;
            padding: 1rem;
            background: rgba(0, 0, 0, 0.1);
            margin-top: 2rem;
        }
        
        .footer-bottom p {
            margin: 0.5rem 0;
            color: #2c3e50;
            font-size: 0.9rem;
        }
        
        .footer-bottom p:last-child {
            font-weight: 500;
            color: var(--primary-color);
        }
        
        .logo h1 {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            font-size: 1.2rem;
            color: var(--primary-color);
        }
        
        .logo h1 i {
            font-size: 1.4rem;
        }
        
        .hero-content h1 {
            font-size: 2.5rem;
            margin-bottom: 1rem;
            font-weight: 700;
            text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.3);
        }
        
        .nav-link {
            color: #333;
            text-decoration: none;
            padding: 0.5rem 1rem;
            border-radius: 5px;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        
        .nav-link:hover {
            background: rgba(0, 0, 0, 0.05);
        }
        
        .logout-btn {
            color: #dc3545;
        }
        
        .logout-btn:hover {
            background: rgba(220, 53, 69, 0.1);
        }
        
        .nav-link i {
            font-size: 1.1rem;
        }
    </style>

    <script>
        // Add smooth scrolling for navigation links
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();
                document.querySelector(this.getAttribute('href')).scrollIntoView({
                    behavior: 'smooth'
                });
            });
        });

        // Search functionality
        document.getElementById('searchInput').addEventListener('input', function(e) {
            const searchTerm = e.target.value.toLowerCase();
            const doctorCards = document.querySelectorAll('.doctor-card');
            
            doctorCards.forEach(card => {
                const doctorName = card.querySelector('h3').textContent.toLowerCase();
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