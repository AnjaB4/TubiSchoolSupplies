<?php
require_once '../app/models/User.php'; // include the User model

class AuthController {
    public function login() {
        session_start();
        include_once __DIR__ . '/../../config/db.php';

        $userModel = new User($conn);

        $error = '';

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $username = trim($_POST['username'] ?? '');
            $password = trim($_POST['password'] ?? '');

            if ($username === '' || $password === '') {
                $error = 'Please enter both username and password.';
            } else {
                $user = $userModel->getUserByUsername($username);

                if ($user && password_verify($password, $user['password'])) {
                    $_SESSION['id_user'] = $user['id_user'];
                    $_SESSION['username'] = $username;
                    header("Location: /user"); // redirect to user dashboard
                    exit;
                } else {
                    $error = "Invalid username or password.";
                }
            }
        }

        $conn->close();
        require_once dirname(__DIR__) . '/views/login.php';
    }

    public function register() {
    session_start();
    include_once '../config/db.php';
    $userModel = new User($conn);

    $error = '';
    $success = '';

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $username = trim($_POST['username'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $school_name = trim($_POST['school_name'] ?? '');
        $school_address = trim($_POST['school_address'] ?? '');
        $district = trim($_POST['district'] ?? '');
        $city = trim($_POST['city'] ?? '');
        $password = trim($_POST['password'] ?? '');
        $confirm = trim($_POST['confirm'] ?? '');

        if ($username === '' || $email === '' || $school_name === '' ||
            $school_address === '' || $district === '' || $city === '' || $password === '' || $confirm === '') {
            $error = 'Please fill in all fields.';
        } elseif ($password !== $confirm) {
            $error = 'Passwords do not match.';
        } elseif ($userModel->usernameExists($username)) {
            $error = 'Username is already taken.';
        } else {
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
            if ($userModel->createUser($username, $hashedPassword, $email, $school_name, $school_address, $district, $city)) {
                $success = 'Registration successful. You can now <a href="/auth/login">log in</a>.';
            } else {
                $error = 'Something went wrong. Try again.';
            }
        }
    }

    $conn->close();
    require_once dirname(__DIR__) . '/views/register.php';
}


    public function adminLogin() {
        session_start();
        include_once '../config/db.php';
        require_once '../app/models/Admin.php';

        $error = '';
        $adminModel = new Admin($conn);

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $username = trim($_POST['username'] ?? '');
            $password = trim($_POST['password'] ?? '');

            if ($username === '' || $password === '') {
                $error = 'Please enter both username and password.';
            } else {
                $admin = $adminModel->findByUsername($username);

                if ($admin && password_verify($password, $admin['password'])) {
                    $_SESSION['id_admin'] = $admin['id_admin'];
                    $_SESSION['admin_username'] = $username;
                    $_SESSION['is_admin'] = true;
                    header("Location: /admin");
                    exit;
                } else {
                    $error = "Invalid username or password.";
                }
            }
        }

        $conn->close();
        require_once dirname(__DIR__) . '/views/admin_login.php';
    }

    
}


