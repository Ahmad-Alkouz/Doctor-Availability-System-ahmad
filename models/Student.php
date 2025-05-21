<?php
require_once __DIR__ . '/../config/database.php';

class Student {
    private $conn;
    
    public function __construct() {
        $database = new Database();
        $this->conn = $database->getConnection();
    }
    
    public function authenticate($email, $password) {
        try {
            $query = "SELECT * FROM students WHERE email = :email";
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
    
    public function getStudentById($id) {
        try {
            $stmt = $this->conn->prepare("SELECT * FROM students WHERE id = :id");
            $stmt->bindParam(":id", $id);
            $stmt->execute();
            
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch(PDOException $e) {
            error_log("Error getting student by ID: " . $e->getMessage());
            return false;
        }
    }
    
    public function registerStudent($name, $email, $password, $student_id, $department) {
        try {
            // Check if email already exists
            $query = "SELECT id FROM students WHERE email = :email";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(":email", $email);
            $stmt->execute();
            
            if($stmt->rowCount() > 0) {
                return false; // Email already exists
            }
            
            // Hash password
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            
            // Insert new student
            $query = "INSERT INTO students (name, email, password, student_id, department) 
                     VALUES (:name, :email, :password, :student_id, :department)";
            
            $stmt = $this->conn->prepare($query);
            
            // Bind parameters
            $stmt->bindParam(":name", $name);
            $stmt->bindParam(":email", $email);
            $stmt->bindParam(":password", $hashed_password);
            $stmt->bindParam(":student_id", $student_id);
            $stmt->bindParam(":department", $department);
            
            if($stmt->execute()) {
                return true;
            }
            return false;
        } catch(PDOException $e) {
            error_log("Registration error: " . $e->getMessage());
            return false;
        }
    }
    
    public function updatePassword($email, $hashed_password) {
        try {
            $query = "UPDATE students SET password = :password WHERE email = :email";
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
    
    public function storeResetToken($email, $token, $expiry) {
        try {
            // First check if email exists
            $query = "SELECT id FROM students WHERE email = :email";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(":email", $email);
            $stmt->execute();
            
            if($stmt->rowCount() == 0) {
                return false; // Email not found
            }
            
            // Update the reset token and expiry
            $query = "UPDATE students SET reset_token = :token, reset_expiry = :expiry WHERE email = :email";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(":token", $token);
            $stmt->bindParam(":expiry", $expiry);
            $stmt->bindParam(":email", $email);
            
            return $stmt->execute();
        } catch(PDOException $e) {
            error_log("Token storage error: " . $e->getMessage());
            return false;
        }
    }
    
    public function resetPassword($token, $new_password) {
        try {
            // Check if token exists and is not expired
            $query = "SELECT id FROM students WHERE reset_token = :token AND reset_expiry > NOW()";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(":token", $token);
            $stmt->execute();
            
            if($stmt->rowCount() == 0) {
                return false; // Token not found or expired
            }
            
            // Hash the new password
            $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
            
            // Update password and clear reset token
            $query = "UPDATE students SET password = :password, reset_token = NULL, reset_expiry = NULL 
                     WHERE reset_token = :token";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(":password", $hashed_password);
            $stmt->bindParam(":token", $token);
            
            return $stmt->execute();
        } catch(PDOException $e) {
            error_log("Password reset error: " . $e->getMessage());
            return false;
        }
    }
}
?> 