<?php 
   session_start();
   include "db_conn.php";
   if (isset($_SESSION['username']) && isset($_SESSION['id'])) {   ?>

<!DOCTYPE html>
<html>
<head>
	<title>Profile</title>
	<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-giJF6kkoqNQ00vy+HMDP7azOuL0xtbfIcaT9wjKHr8RbDVddVHyTfAAsrekwKmP1" crossorigin="anonymous">
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.2/css/all.min.css">
    <style>
        body {
            background-color: #f0f2f5;
        }
        .profile-card {
            max-width: 400px;
            background: white;
            border-radius: 15px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            overflow: hidden;
            margin: 50px auto;
        }
        .profile-image-container {
            width: 120px;
            height: 120px;
            border-radius: 50%;
            background: #f0f2f5;
            margin: 20px auto;
            position: relative;
            overflow: hidden;
            border: 5px solid #fff;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }
        .profile-image-container img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        .profile-name {
            color: #0275d8;
            font-size: 24px;
            font-weight: 700;
            margin-bottom: 5px;
            text-align: center;
        }
        .profile-username {
            color: #6c757d;
            font-size: 16px;
            text-align: center;
            margin-bottom: 15px;
        }
        .profile-info {
            padding: 15px;
            margin: 10px 15px;
            background-color: #f8f9fa;
            border-radius: 10px;
        }
        .profile-buttons {
            display: flex;
            justify-content: space-between;
            padding: 15px;
        }
        .profile-label {
            display: inline-block;
            padding: 5px 15px;
            background-color: #f8f9fa;
            border-radius: 5px;
            font-size: 14px;
            margin: 10px auto;
        }
        .admin-section {
            margin-top: 30px;
        }
        .profile-img-small {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            object-fit: cover;
        }
		/* Add to the existing style section in home.php
.profile-detail {
    margin-bottom: 20px;
    border-bottom: 1px solid #eee;
    padding-bottom: 10px;
}
.profile-detail h6 {
    color: #0275d8;
    font-weight: 600;
    margin-bottom: 5px;
}
.profile-detail p {
    margin-bottom: 5px;
    padding-left: 10px;
} */
    </style>
</head>
<body>
    <div class="container">
        <?php if ($_SESSION['role'] == 'admin') {?>
            <!-- Admin Profile Card -->
            <div class="profile-card">
                <?php 
                $admin_img = isset($_SESSION['profile_image']) && !empty($_SESSION['profile_image']) 
                    ? "uploads/profile/" . $_SESSION['profile_image'] 
                    : "img/admin-default.png";
                ?>
                <div class="profile-image-container">
                    <img src="<?=$admin_img?>" alt="profile image">
                </div>
                
                <div class="profile-name"><?=$_SESSION['name']?></div>
                <div class="profile-username">@<?=$_SESSION['username']?></div>
                
                <div class="text-center">
                    <span class="profile-label">ADMIN | Active</span>
                </div>
                
                <div class="profile-info">
                    <h6>About Me</h6>
                    <p>Administrator account with full system access.</p>
                </div>
                
				<div class="profile-buttons">
    <a href="personal-info.php" class="btn btn-info">Personal Info</a>
    <a href="edit-profile.php" class="btn btn-primary">Edit Profile</a>
    <a href="logout.php" class="btn btn-warning">Logout</a>
</div>
            </div>
            
            <!-- Admin Dashboard Section -->
            <div class="admin-section">
                <?php include 'php/members.php';
                if (mysqli_num_rows($res) > 0) {?>
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h3>System Users</h3>
                        <a href="add-user.php" class="btn btn-success"><i class="fas fa-user-plus"></i> Add User</a>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th scope="col">#</th>
                                        <th scope="col">Image</th>
                                        <th scope="col">Name</th>
                                        <th scope="col">Username</th>
                                        <th scope="col">Role</th>
                                        <th scope="col">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php 
                                    $i = 1;
                                    while ($rows = mysqli_fetch_assoc($res)) {?>
                                    <tr>
                                        <th scope="row"><?=$i?></th>
                                        <td>
                                            <?php 
                                            $img_path = isset($rows['profile_image']) && !empty($rows['profile_image']) 
                                                ? "uploads/profile/" . $rows['profile_image'] 
                                                : ($rows['role'] == 'admin' ? "img/admin-default.png" : "img/user-default.png");
                                            ?>
                                            <img src="<?=$img_path?>" class="profile-img-small" alt="Profile">
                                        </td>
                                        <td><?=$rows['name']?></td>
                                        <td><?=$rows['username']?></td>
                                        <td><?=$rows['role']?></td>
                                        <td>
                                            <a href="edit-user.php?id=<?=$rows['id']?>" class="btn btn-sm btn-primary"><i class="fas fa-edit"></i></a>
                                          <a href="php/delete-user-process.php?id=<?=$rows['id']?>" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure you want to delete this user?');"><i class="fas fa-trash"></i> Delete</a>
                                        </td>
                                    </tr>
                                    <?php $i++; }?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <?php }?>
            </div>
			<?php } else { ?>
    <!-- Regular User Profile Card -->
    <!-- <div class="row">
        <div class="col-md-6"> -->
            <div class="profile-card">
                <?php 
                $user_img = isset($_SESSION['profile_image']) && !empty($_SESSION['profile_image']) 
                    ? "uploads/profile/" . $_SESSION['profile_image'] 
                    : "img/user-default.png";
                ?>
                <div class="profile-image-container">
                    <img src="<?=$user_img?>" alt="profile image">
                </div>
                
                <div class="profile-name"><?=$_SESSION['name']?></div>
                <div class="profile-username">@<?=$_SESSION['username']?></div>
                
                <div class="text-center">
                    <span class="profile-label">BSCS | 4th Year</span>
                </div>
                
                <div class="profile-info">
                    <h6>About Me</h6>
                    <p>Welcome to my profile! This is where you can add information about yourself.</p>
                </div>
                
				<div class="profile-buttons">
    <a href="personal-info.php" class="btn btn-info">Personal Info</a>
    <a href="edit-profile.php" class="btn btn-primary">Edit Profile</a>
    <a href="logout.php" class="btn btn-warning">Logout</a>
</div>
            </div>
        </div>
        
        <!-- New User Details Box
        <div class="col-md-6">
            <div class="profile-card">
                <div class="p-4">
                    <h4 class="text-center mb-4">Personal Information</h4>
                    
                    <div class="profile-detail">
                        <h6><i class="fas fa-calendar-alt me-2"></i> Age</h6>
                        <p><?= isset($_SESSION['age']) ? $_SESSION['age'] : 'Not specified' ?></p>
                    </div>
                    
                    <div class="profile-detail">
                        <h6><i class="fas fa-venus-mars me-2"></i> Gender</h6>
                        <p><?= isset($_SESSION['gender']) ? $_SESSION['gender'] : 'Not specified' ?></p>
                    </div>
                    
                    <div class="profile-detail">
                        <h6><i class="fas fa-map-marker-alt me-2"></i> Address</h6>
                        <p><?= isset($_SESSION['address']) ? $_SESSION['address'] : 'Not specified' ?></p>
                    </div>
                    
                    <div class="profile-detail">
                        <h6><i class="fas fa-phone me-2"></i> Phone</h6>
                        <p><?= isset($_SESSION['phone']) ? $_SESSION['phone'] : 'Not specified' ?></p>
                    </div>
                    
                    <div class="profile-detail">
                        <h6><i class="fas fa-envelope me-2"></i> Email</h6>
                        <p><?= isset($_SESSION['email']) ? $_SESSION['email'] : 'Not specified' ?></p>
                    </div>
                    
                    <div class="mt-4 text-center">
                        <a href="edit-profile.php#personal-info" class="btn btn-outline-primary">
                            <i class="fas fa-edit me-2"></i> Update Information
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div> -->
<?php } ?>
    </div>
</body>
</html>
<?php } else {
	header("Location: index.php");
} ?>