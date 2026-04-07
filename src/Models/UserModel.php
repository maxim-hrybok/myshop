<?php

namespace App\Models;

class UserModel {
    private \PDO $pdo;

    public function __construct(\PDO $pdo) {
        $this->pdo = $pdo;
    }

    /**
     * Finds a user by their email address.
     * @param string $email The email to search for.
     * @return array|false The user data as an array, or false if not found.
     */
    public function findUserByEmail(string $email) {
        $stmt = $this->pdo->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute([$email]);
        return $stmt->fetch(\PDO::FETCH_ASSOC);
    }

    /**
     * Creates a new user in the database.
     * @param string $email
     * @param string $password will be hashed.
     * @param string $firstName
     * @return bool True on success, false on failure.
     */
    public function createUser(string $email, string $password, string $firstName): bool {
        // CRITICAL: Always hash passwords before storing them.
        $passwordHash = password_hash($password, PASSWORD_ARGON2ID);

        $stmt = $this->pdo->prepare(
            "INSERT INTO users (email, password_hash, first_name) VALUES (?, ?, ?)"
        );

        return $stmt->execute([$email, $passwordHash, $firstName]);
    }
     /**
     * Gets the current failed login attempts for an IP address.
     * @param string $ip
     */
    public function getLoginAttempts(string $ip): ?array {
        $stmt = $this->pdo->prepare("SELECT attempts, last_attempt FROM login_attempts WHERE ip_address = ?");
        $stmt->execute([$ip]);
        $result = $stmt->fetch(\PDO::FETCH_ASSOC);
        return $result ?: null;
    }

    /**
     * Records a failed login attempt. Uses an atomic UPSERT.
     * If the IP doesn't exist, it INSERTS it with 1 attempt.
     * If the IP DOES exist (because of the UNIQUE KEY), it UPDATES the attempts + 1.
     * @param string $ip
     */
    public function recordFailedLogin(string $ip): void {
        $sql = "INSERT INTO login_attempts (ip_address, attempts, last_attempt) 
                VALUES (?, 1, NOW()) 
                ON DUPLICATE KEY UPDATE 
                attempts = attempts + 1, 
                last_attempt = NOW()";
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$ip]);
    }

    /**
     * Clears the attempts for an IP address upon successful login or after the timeout expires.
     * @param string $ip
     */
    public function clearLoginAttempts(string $ip): void {
        $stmt = $this->pdo->prepare("DELETE FROM login_attempts WHERE ip_address = ?");
        $stmt->execute([$ip]);
    }
}