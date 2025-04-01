<?php
session_start();
include "../db_conn.php";

// Check if user is admin
if (isset($_SESSION['username']) && isset($_SESSION['id']) && $_SESSION['role'] == 'admin') {
    
    // Check if ID is provided
    if (isset($_GET['id'])) {
        $id = $_GET['id'];
        
        // Prevent deleting yourself
        if ($id == $_SESSION['id']) {
            header("Location: ../home.php?error=You cannot delete your own account");
            exit();
        }
        
        // Delete user
        $sql = "DELETE FROM users WHERE id = $id";
        $result = mysqli_query($conn, $sql);
        
        if ($result) {
            header("Location: ../home.php?success=User deleted successfully");
            exit();
        } else {
            header("Location: ../home.php?error=Unknown error occurred: " . mysqli_error($conn));
            exit();
        }
    } else {
        header("Location: ../home.php");
        exit();
    }
} else {
    header("Location: ../index.php");
    exit();
}