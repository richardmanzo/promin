<?php
session_start();
include "../db_conn.php";

// Check if user is admin
if (isset($_SESSION['username']) && isset($_SESSION['id']) && $_SESSION['role'] == 'admin') {
    
    // Check if form is submitted
    if (isset($_POST['id']) && isset($_POST['username']) && isset($_POST['name']) && isset($_POST['role'])) {
        
        function validate($data) {
            $data = trim($data);
            $data = stripslashes($data);
            $data = htmlspecialchars($data);
            return $data;
        }

        $id = validate($_POST['id']);
        $username = validate($_POST['username']);
        $name = validate($_POST['name']);
        $role = validate($_POST['role']);
        $password = isset($_POST['password']) ? validate($_POST['password']) : '';
        $profile_image = null;
        $profile_updated = false;

        // Simple validation
        if (empty($username)) {
            header("Location: ../edit-user.php?id=$id&error=Username is required");
            exit();
        } else if (empty($name)) {
            header("Location: ../edit-user.php?id=$id&error=Name is required");
            exit();
        } else if (empty($role)) {
            header("Location: ../edit-user.php?id=$id&error=Role is required");
            exit();
        } else if ($role != 'admin' && $role != 'user') {
            header("Location: ../edit-user.php?id=$id&error=Invalid role");
            exit();
        } else {
            // Check if username already exists (excluding current user)
            $check_sql = "SELECT * FROM users WHERE username = '$username' AND id != $id";
            $check_result = mysqli_query($conn, $check_sql);
            
            if (mysqli_num_rows($check_result) > 0) {
                header("Location: ../edit-user.php?id=$id&error=Username already exists");
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
                    header("Location: ../edit-user.php?id=$id&error=Only JPG, PNG, and GIF files are allowed");
                    exit();
                }
                
                // Validate file size (max 2MB)
                if ($file_size > 2097152) {
                    header("Location: ../edit-user.php?id=$id&error=File size must be less than 2MB");
                    exit();
                }
                
                // Create uploads directory if it doesn't exist
                $upload_dir = "../uploads/profile/";
                if (!file_exists($upload_dir)) {
                    mkdir($upload_dir, 0777, true);
                }
                
                // Get current image to delete if it exists
                $current_image_sql = "SELECT profile_image FROM users WHERE id = $id";
                $current_image_result = mysqli_query($conn, $current_image_sql);
                if (mysqli_num_rows($current_image_result) > 0) {
                    $current_image_row = mysqli_fetch_assoc($current_image_result);
                    $current_image = $current_image_row['profile_image'];
                    
                    // Delete current image if it exists
                    if ($current_image && file_exists($upload_dir . $current_image)) {
                        unlink($upload_dir . $current_image);
                    }
                }
                
                // Generate unique filename
                $new_file_name = uniqid() . '.' . $file_ext;
                
                // Move uploaded file to target directory
                if (move_uploaded_file($file_tmp, $upload_dir . $new_file_name)) {
                    $profile_image = $new_file_name;
                    $profile_updated = true;
                } else {
                    header("Location: ../edit-user.php?id=$id&error=Failed to upload image");
                    exit();
                }
            }

            // Build update SQL query based on what needs to be updated
            $sql_parts = array();
            $sql_parts[] = "username = '$username'";
            $sql_parts[] = "name = '$name'";
            $sql_parts[] = "role = '$role'";
            
            if (!empty($password)) {
                $hashed_password = md5($password);
                $sql_parts[] = "password = '$hashed_password'";
            }
            
            if ($profile_updated) {
                $sql_parts[] = "profile_image = '$profile_image'";
            }
            
            $sql = "UPDATE users SET " . implode(", ", $sql_parts) . " WHERE id = $id";
            $result = mysqli_query($conn, $sql);
            
            if ($result) {
                header("Location: ../edit-user.php?id=$id&success=User updated successfully");
                exit();
            } else {
                header("Location: ../edit-user.php?id=$id&error=Unknown error occurred: " . mysqli_error($conn));
                exit();
            }
        }
    } else {
        header("Location: ../home.php");
        exit();
    }
} else {
    header("Location: ../index.php");
    exit();
}