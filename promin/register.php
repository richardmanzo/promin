<?php 
   session_start();
   if (!isset($_SESSION['username']) && !isset($_SESSION['id'])) {   ?>
<!DOCTYPE html>
<html>
<head>
	<title>Registration</title>
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
    <div class="container d-flex justify-content-center align-items-center"
      style="min-height: 100vh">
        <form class="border shadow p-3 rounded"
            action="php/register-process.php" 
            method="post" 
            enctype="multipart/form-data"
            style="width: 450px;">
            <h1 class="text-center p-3">REGISTER</h1>
            <?php if (isset($_GET['error'])) { ?>
            <div class="alert alert-danger" role="alert">
                <?=$_GET['error']?>
            </div>
            <?php } ?>
            
            <div class="row mb-4">
                <div class="col-md-12 text-center">
                    <div class="profile-image-container">
                        <img id="preview-image" src="img/user-default.png" alt="Profile Image">
                    </div>
                    
                    <div class="mb-3">
                        <label for="profile_image" class="form-label">Profile Image</label>
                        <input type="file" class="form-control" name="profile_image" id="profile_image" onchange="previewImage(this);">
                        <small class="text-muted">Max file size: 2MB. Accepted formats: JPG, PNG, GIF</small>
                    </div>
                </div>
            </div>
            
            <div class="mb-3">
                <label for="name" class="form-label">Full Name</label>
                <input type="text" class="form-control" name="name" id="name" required>
            </div>
            <div class="mb-3">
                <label for="username" class="form-label">Username</label>
                <input type="text" class="form-control" name="username" id="username" required>
            </div>
            <div class="mb-3">
                <label for="password" class="form-label">Password</label>
                <input type="password" class="form-control" name="password" id="password" required>
            </div>
            <div class="mb-3">
                <label for="confirm_password" class="form-label">Confirm Password</label>
                <input type="password" class="form-control" name="confirm_password" id="confirm_password" required>
            </div>
            <input type="hidden" name="role" value="user">
            
            <button type="submit" class="btn btn-success">REGISTER</button>
            <a href="index.php" class="btn btn-secondary ms-2">Back to Login</a>
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
<?php }else{
	header("Location: home.php");
} ?>