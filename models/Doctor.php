<?php
require_once __DIR__ . '/../config/database.php';

class Doctor {
    private $conn;
    
    public function __construct() {
        $database = new Database();
        $this->conn = $database->getConnection();
    }

    public function getAllDoctors() {
        try {
            $stmt = $this->conn->query("SELECT * FROM doctors");
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch(PDOException $e) {
            error_log("Error getting all doctors: " . $e->getMessage());
            return [];
        }
    }

    public function authenticate($email, $password) {
        try {
            $query = "SELECT * FROM doctors WHERE email = :email";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(":email", $email);
            $stmt->execute();
            
            if($stmt->rowCount() > 0) {
                $row = $stmt->fetch(PDO::FETCH_ASSOC);
                if(password_verify($password, $row['password'])) {
                    return $row;
                }
            }
            return false;
        } catch(PDOException $e) {
            error_log("Authentication error: " . $e->getMessage());
            return false;
        }
    }

    public function getDoctorById($id) {
        try {
            $query = "SELECT * FROM doctors WHERE id = :id";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(":id", $id);
            $stmt->execute();
            
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch(PDOException $e) {
            error_log("Error getting doctor by ID: " . $e->getMessage());
            return false;
        }
    }

    public function getDoctorByEmail($email) {
        try {
            $query = "SELECT * FROM doctors WHERE email = :email";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(":email", $email);
            $stmt->execute();
            
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch(PDOException $e) {
            error_log("Error getting doctor by email: " . $e->getMessage());
            return false;
        }
    }

    public function updateDoctorStatus($id, $status) {
        try {
            $query = "UPDATE doctors SET status = :status WHERE id = :id";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(":status", $status);
            $stmt->bindParam(":id", $id);
            
            return $stmt->execute();
        } catch(PDOException $e) {
            error_log("Error updating doctor status: " . $e->getMessage());
            return false;
        }
    }

    public function updateDoctor($id, $data) {
        try {
            $query = "UPDATE doctors SET name = :name, email = :email, officeno = :officeno, 
                     specialization = :specialization WHERE id = :id";
            $stmt = $this->conn->prepare($query);
            
            $stmt->bindParam(":name", $data['name']);
            $stmt->bindParam(":email", $data['email']);
            $stmt->bindParam(":officeno", $data['officeno']);
            $stmt->bindParam(":specialization", $data['specialization']);
            $stmt->bindParam(":id", $id);
            
            return $stmt->execute();
        } catch(PDOException $e) {
            error_log("Error updating doctor: " . $e->getMessage());
            return false;
        }
    }

    public function updatePassword($email, $hashed_password) {
        try {
            $query = "UPDATE doctors SET password = :password WHERE email = :email";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(":password", $hashed_password);
            $stmt->bindParam(":email", $email);
            
            if($stmt->execute()) {
                return $stmt->rowCount() > 0;
            }
            return false;
        } catch(PDOException $e) {
            error_log("Password update error: " . $e->getMessage());
            return false;
        }
    }

    public function verifyPassword($email, $password) {
        try {
            $query = "SELECT * FROM doctors WHERE email = :email";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(":email", $email);
            $stmt->execute();
            
            if($stmt->rowCount() > 0) {
                $row = $stmt->fetch(PDO::FETCH_ASSOC);
                if(password_verify($password, $row['password'])) {
                    return $row;
                }
            }
            return false;
        } catch(PDOException $e) {
            error_log("Password verification error: " . $e->getMessage());
            return false;
        }
    }

    public function registerDoctor($name, $email, $password, $officeno, $specialization) {
        try {
            // Check if email already exists
            $query = "SELECT id FROM doctors WHERE email = :email";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(":email", $email);
            $stmt->execute();
            
            if($stmt->rowCount() > 0) {
                return false; // Email already exists
            }
            
            // Hash password
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            
            // Insert new doctor
            $query = "INSERT INTO doctors (name, email, password, officeno, specialization) 
                     VALUES (:name, :email, :password, :officeno, :specialization)";
            
            $stmt = $this->conn->prepare($query);
            
            // Bind parameters
            $stmt->bindParam(":name", $name);
            $stmt->bindParam(":email", $email);
            $stmt->bindParam(":password", $hashed_password);
            $stmt->bindParam(":officeno", $officeno);
            $stmt->bindParam(":specialization", $specialization);
            
            if($stmt->execute()) {
                return true;
            }
            return false;
        } catch(PDOException $e) {
            error_log("Registration error: " . $e->getMessage());
            return false;
        }
    }

    public function storeResetToken($email, $token, $expiry) {
        try {
            // Check if email exists
            $stmt = $this->conn->prepare("SELECT id FROM doctors WHERE email = ?");
            $stmt->execute([$email]);
            
            if ($stmt->rowCount() > 0) {
                // Store reset token
                $stmt = $this->conn->prepare("UPDATE doctors SET reset_token = ?, reset_expiry = ? WHERE email = ?");
                return $stmt->execute([$token, $expiry, $email]);
            }
            return false;
        } catch(PDOException $e) {
            error_log("Error storing reset token: " . $e->getMessage());
            return false;
        }
    }

    public function resetPassword($token, $new_password) {
        try {
            // Check if token is valid and not expired
            $stmt = $this->conn->prepare("SELECT id FROM doctors WHERE reset_token = ? AND reset_expiry > NOW()");
            $stmt->execute([$token]);
            
            if ($stmt->rowCount() > 0) {
                // Update password and clear reset token
                $stmt = $this->conn->prepare("UPDATE doctors SET password = ?, reset_token = NULL, reset_expiry = NULL WHERE reset_token = ?");
                return $stmt->execute([$new_password, $token]);
            }
            return false;
        } catch(PDOException $e) {
            error_log("Error resetting password: " . $e->getMessage());
            return false;
        }
    }
}
?> 