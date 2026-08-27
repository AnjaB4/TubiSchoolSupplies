<?php
// app/models/User.php

class User {
    private $conn;

    public function __construct($dbConnection) {
        $this->conn = $dbConnection;
    }

    // Get user data by id (for user dashboard)
    public function getUserById($id_user) {
        $stmt = $this->conn->prepare("SELECT * FROM user WHERE id_user = ?");
        $stmt->bind_param("i", $id_user);
        $stmt->execute();
        $result = $stmt->get_result();
        $user = $result->fetch_assoc();
        $stmt->close();
        return $user;
    }


    // Get user data by username (for login)
    public function getUserByUsername(string $username): ?array {
        $stmt = $this->conn->prepare("SELECT id_user, password FROM user WHERE username = ?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows === 1) {
            $stmt->bind_result($id_user, $hashed_password);
            $stmt->fetch();
            $stmt->close();

            return [
                'id_user' => $id_user,
                'password' => $hashed_password
            ];
        }

        $stmt->close();
        return null; // User not found
    }

    // Check if username already exists (for registration)
    public function usernameExists(string $username): bool {
        $stmt = $this->conn->prepare("SELECT id_user FROM user WHERE username = ?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $stmt->store_result();

        $exists = $stmt->num_rows > 0;

        $stmt->close();
        return $exists;
    }

    // Create a new user record
    public function createUser($username, $password, $email, $school_name, $school_address, $district, $city) {
        $stmt = $this->conn->prepare("INSERT INTO user (username, password, email, school_name, school_address, district, city) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("sssssss", $username, $password, $email, $school_name, $school_address, $district, $city);
        return $stmt->execute();
    }

}
