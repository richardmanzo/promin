<?php
session_start();
include "../db_conn.php";

// Check if user is logged in
if (isset($_SESSION['username']) && isset($_SESSION['id'])) {
    
    // Check if form is submitted
    if (isset($_POST['username']) && isset($_POST['name'])) {
        
        function validate($data) {
            $data = trim($data);
            $data = stripslashes($data);
            $data = htmlspecialchars($data);
            return $data;
        }

        $id = $_SESSION['id'];
        $username = validate($_POST['username']);
        $name = validate($_POST['name']);
        $education = isset($_POST['education']) ? validate($_POST['education']) : '';
        $about_me = isset($_POST['about_me']) ? validate($_POST['about_me']) : '';
        $password = isset($_POST['password']) ? validate($_POST['password']) : '';
        $profile_image = null;
        $profile_updated = false;

        // Simple validation
        if (empty($username)) {
            header("Location: ../edit-profile.php?error=Username is required");
            exit();
        } else if (empty($name)) {
            header("Location: ../edit-profile.php?error=Name is required");
            exit();
        } else {
            // Check if username already exists (excluding current user)
            $check_sql = "SELECT * FROM users WHERE username = '$username' AND id != $id";
            $check_result = mysqli_query($conn, $check_sql);
            
            if (mysqli_num_rows($check_result) > 0) {
                header("Location: ../edit-profile.php?error=Username already exists");
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
                    header("Location: ../edit-profile.php?error=Only JPG, PNG, and GIF files are allowed");
                    exit();
                }
                
                // Validate file size (max 2MB)
                if ($file_size > 2097152) {
                    header("Location: ../edit-profile.php?error=File size must be less than 2MB");
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
                    
                    // Update session variable
                    $_SESSION['profile_image'] = $profile_image;
                } else {
                    header("Location: ../edit-profile.php?error=Failed to upload image");
                    exit();
                }
            }

            // Build update SQL query based on what needs to be updated
            $sql_parts = array();
            $sql_parts[] = "username = '$username'";
            $sql_parts[] = "name = '$name'";
            $sql_parts[] = "education = '$education'";
            $sql_parts[] = "about_me = '$about_me'";
            
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
                // Update session variables
                $_SESSION['name'] = $name;
                $_SESSION['username'] = $username;
                
                header("Location: ../edit-profile.php?success=Profile updated successfully");
                exit();
            } else {
                header("Location: ../edit-profile.php?error=Unknown error occurred: " . mysqli_error($conn));
                exit();
            }
        }
    } else {
        header("Location: ../edit-profile.php");
        exit();
    }
} else {
    header("Location: ../index.php");
    exit();
}