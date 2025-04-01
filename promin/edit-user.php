<?php 
   session_start();
   include "db_conn.php";
   if (isset($_SESSION['username']) && isset($_SESSION['id']) && $_SESSION['role'] == 'admin') {   
        // Get user data
        if(isset($_GET['id'])) {
            $id = $_GET['id'];
            $sql = "SELECT * FROM users WHERE id = $id";
            $result = mysqli_query($conn, $sql);
            
            if(mysqli_num_rows($result) > 0) {
                $user = mysqli_fetch_assoc($result);
            } else {
                header("Location: home.php?error=User not found");
                exit();
            }
        } else {
            header("Location: home.php");
            exit();
        }
   ?>

<!DOCTYPE html>
<html>
<head>
	<title>Edit User</title>
	<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-giJF6kkoqNQ00vy+HMDP7azOuL0xtbfIcaT9wjKHr8RbDVddVHyTfAAsrekwKmP1" crossorigin="anonymous">
    <style>
        .profile-image-container {
            width: 150px;
            height: 150px;
            overflow: hidden;
            border-radius: 50%;
            margin: 0 auto 20px;
            border: 3px solid #ddd;
        }
        .profile-image-container img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
    </style>
</head>
<body>
    <div class="container mt-5">
        <h1>Edit User</h1>
        <?php if (isset($_GET['error'])) { ?>
            <div class="alert alert-danger" role="alert">
                <?=$_GET['error']?>
            </div>
        <?php } ?>
        <?php if (isset($_GET['success'])) { ?>
            <div class="alert alert-success" role="alert">
                <?=$_GET['success']?>
            </div>
        <?php } ?>
        
        <form action="php/edit-user-process.php" method="post" enctype="multipart/form-data">
            <input type="hidden" name="id" value="<?=$user['id']?>">
            
            <div class="row mb-4">
                <div class="col-md-6 offset-md-3 text-center">
                    <div class="profile-image-container">
                        <?php 
                        $image_path = isset($user['profile_image']) && !empty($user['profile_image']) 
                            ? "uploads/profile/" . $user['profile_image'] 
                            : ($user['role'] == 'admin' ? "img/admin-default.png" : "img/user-default.png");
                        ?>
                        <img id="preview-image" src="<?=$image_path?>" alt="Profile Image">
                    </div>
                    
                    <div class="mb-3">
                        <label for="profile_image" class="form-label">Profile Image</label>
                        <input type="file" class="form-control" name="profile_image" id="profile_image" onchange="previewImage(this);">
                        <small class="text-muted">Leave blank to keep current image. Max file size: 2MB. Accepted formats: JPG, PNG, GIF</small>
                    </div>
                </div>
            </div>
            
            <div class="mb-3">
                <label for="name" class="form-label">Full Name</label>
                <input type="text" class="form-control" name="name" id="name" value="<?=$user['name']?>" required>
            </div>
            <div class="mb-3">
                <label for="username" class="form-label">Username</label>
                <input type="text" class="form-control" name="username" id="username" value="<?=$user['username']?>" required>
            </div>
            <div class="mb-3">
                <label for="password" class="form-label">Password</label>
                <input type="password" class="form-control" name="password" id="password" placeholder="Leave blank to keep current password">
                <small class="text-muted">Leave blank if you don't want to change the password</small>
            </div>
            <div class="mb-3">
                <label for="role" class="form-label">Role</label>
                <select class="form-select" name="role" id="role" required>
                    <option value="user" <?php if($user['role'] == 'user') echo 'selected'; ?>>User</option>
                    <option value="admin" <?php if($user['role'] == 'admin') echo 'selected'; ?>>Admin</option>
                </select>
            </div>
            <button type="submit" class="btn btn-primary">Update User</button>
            <a href="home.php" class="btn btn-secondary">Back to Dashboard</a>
        </form>
    </div>
    
    <script>
        function previewImage(input) {
            if (input.files && input.files[0]) {
                var reader = new FileReader();
                
                reader.onload = function(e) {
                    document.getElementById('preview-image').src = e.target.result;
                }
                
                reader.readAsDataURL(input.files[0]);
            }
        }
    </script>
</body>
</html>

<?php } else {
    header("Location: index.php");
} ?>