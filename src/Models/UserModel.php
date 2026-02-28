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
}