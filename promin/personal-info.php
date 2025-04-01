<?php 
   session_start();
   include "db_conn.php";
   if (isset($_SESSION['username']) && isset($_SESSION['id'])) {   
        // Get user data
        $id = $_SESSION['id'];
        $sql = "SELECT * FROM users WHERE id = $id";
        $result = mysqli_query($conn, $sql);
        
        if(mysqli_num_rows($result) > 0) {
            $user = mysqli_fetch_assoc($result);
        } else {
            header("Location: home.php?error=User not found");
            exit();
        }
   ?>

<!DOCTYPE html>
<html>
<head>
    <title>Personal Information</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-giJF6kkoqNQ00vy+HMDP7azOuL0xtbfIcaT9wjKHr8RbDVddVHyTfAAsrekwKmP1" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.2/css/all.min.css">
    <style>
        body {
            background-color: #f0f2f5;
        }
        .info-card {
            max-width: 500px;
            background: white;
            border-radius: 15px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            overflow: hidden;
            margin: 50px auto;
            padding: 20px;
        }
        .info-header {
            color: #0275d8;
            font-size: 24px;
            font-weight: 700;
            margin-bottom: 20px;
            text-align: center;
        }
        .info-item {
            margin-bottom: 15px;
            padding: 10px;
            border-bottom: 1px solid #eee;
        }
        .info-label {
            color: #6c757d;
            font-weight: 600;
        }
        .info-value {
            margin-top: 5px;
        }
        .info-buttons {
            display: flex;
            justify-content: space-between;
            margin-top: 20px;
        }
        .info-icon {
            color: #0275d8;
            margin-right: 10px;
            width: 20px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="info-card">
            <div class="info-header">Personal Information</div>
            
            <?php if (isset($_GET['success'])) { ?>
                <div class="alert alert-success" role="alert">
                    <?=$_GET['success']?>
                </div>
            <?php } ?>
            
            <div class="info-item">
                <div class="info-label"><i class="fas fa-user info-icon"></i> Full Name</div>
                <div class="info-value"><?=$user['name']?></div>
            </div>
            
            <div class="info-item">
                <div class="info-label"><i class="fas fa-user-tag info-icon"></i> Username</div>
                <div class="info-value">@<?=$user['username']?></div>
            </div>
            
            <div class="info-item">
                <div class="info-label"><i class="fas fa-graduation-cap info-icon"></i> Education</div>
                <div class="info-value"><?=isset($user['education']) && !empty($user['education']) ? $user['education'] : 'Not specified'?></div>
            </div>
            
            <div class="info-item">
                <div class="info-label"><i class="fas fa-info-circle info-icon"></i> About</div>
                <div class="info-value"><?=isset($user['about_me']) && !empty($user['about_me']) ? $user['about_me'] : 'Not specified'?></div>
            </div>
            
            <div class="info-buttons">
                <a href="edit-profile.php" class="btn btn-primary"><i class="fas fa-edit"></i> Edit Information</a>
                <a href="home.php" class="btn btn-secondary"><i class="fas fa-home"></i> Back to Profile</a>
            </div>
        </div>
    </div>
</body>
</html>

<?php } else {
    header("Location: index.php");
} ?>