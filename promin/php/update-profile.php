<?php
session_start();
include "../db_conn.php";

if (isset($_SESSION['username']) && isset($_SESSION['id'])) {
    $id = $_SESSION['id'];
    $name = $_POST['name'];
    $username = $_POST['username'];
    $education = $_POST['education'];
    $about_me = $_POST['about_me'];
    $password = $_POST['password'];
    
    // Profile image handling
    if (isset($_FILES['profile_image']) && $_FILES['profile_image']['error'] === 0) {
        $allowed = ['jpg', 'jpeg', 'png', 'gif'];
        $file_name = $_FILES['profile_image']['name'];
        $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
        
        // Check if the file extension is allowed
        if (in_array($file_ext, $allowed)) {
            $new_name = uniqid('profile_') . '.' . $file_ext;
            $destination = '../uploads/profile/' . $new_name;
            
            // Create the uploads/profile directory if it doesn't exist
            if (!file_exists('../uploads/profile/')) {
                mkdir('../uploads/profile/', 0777, true);
            }
            
            if (move_uploaded_file($_FILES['profile_image']['tmp_name'], $destination)) {
                // Update session and database with new image
                $_SESSION['profile_image'] = $new_name;
            } else {
                header("Location: ../edit-profile.php?error=Error uploading image");
                exit();
            }
        } else {
            header("Location: ../edit-profile.php?error=Invalid file type");
            exit();
        }
    }
    
    // Update database based on whether image, password, or both were updated
    if (!empty($password) && isset($_SESSION['profile_image']) && $_SESSION['profile_image'] != '') {
        $sql = "UPDATE users SET 
                name = ?, 
                username = ?, 
                education = ?, 
                about_me = ?, 
                password = ?, 
                profile_image = ? 
                WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssssssi", $name, $username, $education, $about_me, $password, $_SESSION['profile_image'], $id);
    } else if (!empty($password)) {
        $sql = "UPDATE users SET 
                name = ?, 
                username = ?, 
                education = ?, 
                about_me = ?, 
                password = ? 
                WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sssssi", $name, $username, $education, $about_me, $password, $id);
    } else if (isset($_SESSION['profile_image']) && $_SESSION['profile_image'] != '') {
        $sql = "UPDATE users SET 
                name = ?, 
                username = ?, 
                education = ?, 
                about_me = ?, 
                profile_image = ? 
                WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sssssi", $name, $username, $education, $about_me, $_SESSION['profile_image'], $id);
    } else {
        $sql = "UPDATE users SET 
                name = ?, 
                username = ?, 
                education = ?, 
                about_me = ? 
                WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssssi", $name, $username, $education, $about_me, $id);
    }
    
    if ($stmt->execute()) {
        // Update session variables
        $_SESSION['name'] = $name;
        $_SESSION['username'] = $username;
        
        // Redirect to personal info page instead of edit-profile
        header("Location: ../personal-info.php?success=Your profile has been updated successfully");
        exit();
    } else {
        header("Location: ../edit-profile.php?error=Unknown error occurred");
        exit();
    }
} else {
    header("Location: ../index.php");
    exit();
}
?>