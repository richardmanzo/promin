<?php 
   session_start();
   include "db_conn.php";
   if (isset($_SESSION['username']) && isset($_SESSION['id'])) {   ?>

<!DOCTYPE html>
<html>
<head>
	<title>HOME</title>
	<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-giJF6kkoqNQ00vy+HMDP7azOuL0xtbfIcaT9wjKHr8RbDVddVHyTfAAsrekwKmP1" crossorigin="anonymous">
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.2/css/all.min.css">
    <style>
        .profile-img-small {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            object-fit: cover;
        }
        .profile-img-card {
            width: 150px;
            height: 150px;
            border-radius: 50%;
            object-fit: cover;
            margin: 0 auto;
            border: 3px solid #fff;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }
    </style>
</head>
<body>
      <div class="container d-flex justify-content-center align-items-center"
      style="min-height: 100vh">
      	<?php if ($_SESSION['role'] == 'admin') {?>
      		<!-- For Admin -->
      		<div class="card" style="width: 18rem;">
              <?php 
              $admin_img = isset($_SESSION['profile_image']) && !empty($_SESSION['profile_image']) 
                  ? "uploads/profile/" . $_SESSION['profile_image'] 
                  : "img/admin-default.png";
              ?>
			  <img src="<?=$admin_img?>" 
			       class="card-img-top profile-img-card" 
			       alt="admin image">
			  <div class="card-body text-center">
			    <h5 class="card-title">
			    	<?=$_SESSION['name']?>
			    </h5>
			    <a href="logout.php" class="btn btn-dark">Logout</a>
			  </div>
			</div>
			<div class="p-3">
				<?php include 'php/members.php';
                 if (mysqli_num_rows($res) > 0) {?>
                  
				<h1 class="display-4 fs-1">Members</h1>
				<div class="d-flex justify-content-end mb-3">
					<a href="add-user.php" class="btn btn-success"><i class="fas fa-user-plus"></i> Add User</a>
				</div>
				<table class="table" 
				       style="width: 42rem;">
				  <thead>
				    <tr>
				      <th scope="col">#</th>
                      <th scope="col">Image</th>
				      <th scope="col">Name</th>
				      <th scope="col">User name</th>
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
                        <a href="edit-user.php?id=<?=$rows['id']?>" class="btn btn-sm btn-primary"><i class="fas fa-edit"></i> Edit</a>
                        <a href="php/delete-user.php?id=<?=$rows['id']?>" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure you want to delete this user?');"><i class="fas fa-trash"></i> Delete</a>
                      </td>
				    </tr>
				    <?php $i++; }?>
				  </tbody>
				</table>
				<?php }?>
			</div>
      	<?php }else { ?>
      		<!-- FORE USERS -->
      		<div class="card" style="width: 18rem;">
              <?php 
              $user_img = isset($_SESSION['profile_image']) && !empty($_SESSION['profile_image']) 
                  ? "uploads/profile/" . $_SESSION['profile_image'] 
                  : "img/user-default.png";
              ?>
			  <img src="<?=$user_img?>" 
			       class="card-img-top profile-img-card" 
			       alt="user image">
			  <div class="card-body text-center">
			    <h5 class="card-title">
			    	<?=$_SESSION['name']?>
			    </h5>
			    <a href="logout.php" class="btn btn-dark">Logout</a>
			  </div>
			</div>
      	<?php } ?>
      </div>
</body>
</html>
<?php }else{
	header("Location: index.php");
} ?>