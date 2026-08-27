<?php
// app/models/Admin.php

class Admin {
    private $conn;

    public function __construct($dbConn) {
        $this->conn = $dbConn;
    }

    public function findByUsername($username) {
        $stmt = $this->conn->prepare("SELECT id_admin, password FROM admin WHERE username = ?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows === 1) {
            $stmt->bind_result($id_admin, $hashed_password);
            $stmt->fetch();
            return [
                'id_admin' => $id_admin,
                'password' => $hashed_password
            ];
        }

        return null;
    }
}
