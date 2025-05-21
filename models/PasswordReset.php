<?php
require_once __DIR__ . '/../config/database.php';

class PasswordReset {
    private $pdo;

    public function __construct() {
        global $pdo;
        $this->pdo = $pdo;
    }

    public function createReset($email, $token, $expiry) {
        $sql = "INSERT INTO password_resets (email, token, expiry) VALUES (?, ?, ?)";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([$email, $token, $expiry]);
    }

    public function getResetByToken($token) {
        $stmt = $this->pdo->prepare("SELECT * FROM password_resets WHERE token = ? AND expiry > NOW()");
        $stmt->execute([$token]);
        return $stmt->fetch();
    }

    public function deleteReset($token) {
        $stmt = $this->pdo->prepare("DELETE FROM password_resets WHERE token = ?");
        return $stmt->execute([$token]);
    }

    public function deleteExpiredResets() {
        $stmt = $this->pdo->prepare("DELETE FROM password_resets WHERE expiry < NOW()");
        return $stmt->execute();
    }
}
?> 