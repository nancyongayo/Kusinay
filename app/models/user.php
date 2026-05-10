<?php
class UserModel {
    private $db;
    private $encryption_key = "KusiNay_Secret_Key_2026"; // Gamit og mas secure nga key

    public function __construct($db_conn) {
        $this->db = $db_conn;
    }

    // Secure Password Hashing [cite: 10, 17]
    public function register($email, $password) {
        $hash = password_hash($password, PASSWORD_BCRYPT);
        $stmt = $this->db->prepare("INSERT INTO users (email, password_hash, is_verified) VALUES (?, ?, 0)");
        return $stmt->execute([$email, $hash]);
    }

    // Encrypted sensitive fields (Address) 
    public function updateProfile($userId, $roleId, $address) {
        $encryptedAddress = openssl_encrypt($address, "AES-128-ECB", $this->encryption_key);
        $stmt = $this->db->prepare("UPDATE users SET role_id = ?, address = ? WHERE id = ?");
        return $stmt->execute([$roleId, $encryptedAddress, $userId]);
    }
}