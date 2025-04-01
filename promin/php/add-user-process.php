<?php
session_start();
include "../db_conn.php";

// Check if user is admin
if (isset($_SESSION['username']) && isset($_SESSION['id']) && $_SESSION['role'] == 'admin') {
    
    // Check if form is submitted
    if (isset($_POST['username']) && isset($_POST['password']) && isset($_POST['name']) && isset($_POST['role'])) {
        
        function validate($data) {
            $data = trim($data);
            $data = stripslashes($data);
            $data = htmlspecialchars($data);
            return $data;
        }

        $username = validate($_POST['username']);
        $password = validate($_POST['password']);
        $name = validate($_POST['name']);
        $role = validate($_POST['role']);
        $profile_image = null;

        // Simple validation
        if (empty($username)) {
            header("Location: ../add-user.php?error=Username is required");
            exit();
        } else if (empty($password)) {
            header("Location: ../add-user.php?error=Password is required");
            exit();
        } else if (empty($name)) {
            header("Location: ../add-user.php?error=Name is required");
            exit();
        } else if (empty($role)) {
            header("Location: ../add-user.php?error=Role is required");
            exit();
        } else if ($role != 'admin' && $role != 'user') {
            header("Location: ../add-user.php?error=Invalid role");
            exit();
        } else {
            // Check if username already exists
            $check_sql = "SELECT * FROM users WHERE username = '$username'";
            $check_result = mysqli_query($conn, $check_sql);
            
            if (mysqli_num_rows($check_result) > 0) {
                header("Location: ../add-user.php?error=Username already exists");
                exit();
            }

            // Process image upload if provided
            if (isset($_FILES['profile_image']) && $_FILES['profile_image']['error'] == 0) {
                $allowed_types = array('jpg', 'jpeg', 'png', 'gif');
                $file_name = $_FILES['profile_image']['name'];
                $file_size = $_FILES['profile_image']['size'];
                $file_tmp = $_FILES['profile_image']['tmp_name'];
                $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
                
                // Validate file extension
                if (!in_array($file_ext, $allowed_types)) {
                    header("Location: ../add-user.php?error=Only JPG, PNG, and GIF files are allowed");
                    exit();
                }
                
                // Validate file size (max 2MB)
                if ($file_size > 2097152) {
                    header("Location: ../add-user.php?error=File size must be less than 2MB");
                    exit();
                }
                
                // Create uploads directory if it doesn't exist
                $upload_dir = "../uploads/profile/";
                if (!file_exists($upload_dir)) {
                    mkdir($upload_dir, 0777, true);
                }
                
                // Generate unique filename
                $new_file_name = uniqid() . '.' . $file_ext;
                
                // Move uploaded file to target directory
                if (move_uploaded_file($file_tmp, $upload_dir . $new_file_name)) {
                    $profile_image = $new_file_name;
                } else {
                    header("Location: ../add-user.php?error=Failed to upload image");
                    exit();
                }
            }

            // Hash the password
            $hashed_password = md5($password);
            
            // Insert new user
            if ($profile_image) {
                $sql = "INSERT INTO users (username, password, name, role, profile_image) 
                        VALUES ('$username', '$hashed_password', '$name', '$role', '$profile_image')";
            } else {
                $sql = "INSERT INTO users (username, password, name, role) 
                        VALUES ('$username', '$hashed_password', '$name', '$role')";
            }
            
            $result = mysqli_query($conn, $sql);
            
            if ($result) {
                header("Location: ../add-user.php?success=User added successfully");
                exit();
            } else {
                header("Location: ../add-user.php?error=Unknown error occurred: " . mysqli_error($conn));
                exit();
            }
        }
    } else {
        header("Location: ../add-user.php");
        exit();
    }
} else {
    header("Location: ../index.php");
    exit();
}