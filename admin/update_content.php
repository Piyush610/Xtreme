<?php
include '../components/connect.php';

$message = [];

if(isset($_COOKIE['tutor_id'])){
   $tutor_id = $_COOKIE['tutor_id'];
}else{
   $tutor_id = ''; // Fixed the space to an empty string
   header('location: login.php');
}

if(isset($_GET['get_id'])){
    $get_id = $_GET['get_id'];
}
else{
    header('location: dashboard.php');
}

if(isset($_POST['update'])){
    $video_id = $_POST['video_id'];
    $video_id = filter_var($video_id, FILTER_SANITIZE_STRING);
    $title = $_POST['title'];
    $title = filter_var($title, FILTER_SANITIZE_STRING);
    $status = $_POST['status'];
    $status = filter_var($status, FILTER_SANITIZE_STRING);
    $description = $_POST['description'];
    $description = filter_var($description, FILTER_SANITIZE_STRING);
    $playlist = $_POST['playlist'];
    $playlist = filter_var($playlist, FILTER_SANITIZE_STRING);
    $update_content = $conn->prepare("UPDATE `content` SET title = ?, description = ?, status = ? WHERE id = ?");
    $update_content->execute([$title, $description, $status, $video_id]);

    if(!empty($playlist)){
        $update_playlist = $conn->prepare("UPDATE `content` SET playlist_id = ? WHERE id = ?");
        $update_playlist->execute([$playlist, $video_id]);
    } 
    
    

    $old_thumb = $_POST['old_thumb'];
    $old_thumb = filter_var($old_thumb, FILTER_SANITIZE_STRING);
    $image = $_FILES['image']['name'];
    $image = filter_var($image, FILTER_SANITIZE_STRING);
    $image_ext = pathinfo($image, PATHINFO_EXTENSION);
    $rename_image = unique_id().'.'.$image_ext;
    $image_size = $_FILES['image']['size'];
    $image_tmp_name = $_FILES['image']['tmp_name'];
    $image_folder = '../uploaded_files/'.$rename_image;
    if(!empty($image)){
        if($image_size > 2000000){
            $message[] = 'Image size is too large!';
        }else{
            $update_thumb = $conn->prepare("UPDATE `content` SET thumb = ? WHERE id = ?");
            $update_thumb->execute([$rename_image, $video_id]);
            move_uploaded_file($image_tmp_name, $image_folder);
            if(!empty($old_thumb) && file_exists('../uploaded_files/' . $old_thumb) && !is_dir('../uploaded_files/' . $old_thumb)){
                unlink('../uploaded_files/' . $old_thumb);
            }
        }
    }

    $old_video = $_POST['old_video'];
    $old_video = filter_var($old_video, FILTER_SANITIZE_STRING);

    $video = $_FILES['video']['name'];
    $video = filter_var($video, FILTER_SANITIZE_STRING);

    $video_ext = pathinfo($video, PATHINFO_EXTENSION);
    $rename_video = unique_id().'.'.$video_ext;
    $video_tmp_name = $_FILES['video']['tmp_name'];
    $video_folder = '../uploaded_files/'.$rename_video;

    if(!empty($video)){
        $update_video = $conn->prepare("UPDATE `content` SET video = ? WHERE id = ?");
        $update_video->execute([$rename_video, $video_id]); 
        move_uploaded_file($video_tmp_name, $video_folder);
        if(!empty($old_video) && file_exists('../uploaded_files/' . $old_video) && !is_dir('../uploaded_files/' . $old_video)){
            unlink('../uploaded_files/' . $old_video);
        }   
    }

    if(empty($message)){
        header("location: update_content.php?get_id=$get_id&updated=1");
        exit;
    }
}

if(isset($_POST['delete_video'])){
    $delete_id = $_POST['video_id'];
    $delete_id = filter_var($delete_id, FILTER_SANITIZE_STRING);
    $delete_video_thumb = $conn->prepare("SELECT thumb FROM `content` WHERE id = ? LIMIT 1");
    $delete_video_thumb->execute([$delete_id]);
    $fetch_thumb = $delete_video_thumb->fetch(PDO::FETCH_ASSOC);
    if(file_exists('../uploaded_files/' . $fetch_thumb['thumb']) && !is_dir('../uploaded_files/' . $fetch_thumb['thumb'])){
        unlink('../uploaded_files/' . $fetch_thumb['thumb']);
    }
    
    $delete_video = $conn->prepare("SELECT video FROM `content` WHERE id = ? LIMIT 1");
    $delete_video->execute([$delete_id]);
    $fetch_thumb = $delete_video->fetch(PDO::FETCH_ASSOC);
    if(file_exists('../uploaded_files/' . $fetch_thumb['video']) && !is_dir('../uploaded_files/' . $fetch_thumb['video'])){
        unlink('../uploaded_files/' . $fetch_thumb['video']);
    }

    $delete_likes = $conn->prepare("DELETE FROM `likes` WHERE content_id = ?");
    $delete_likes->execute([$delete_id]);

    $delete_comments = $conn->prepare("DELETE FROM `comments` WHERE content_id = ?");
    $delete_comments->execute([$delete_id]);

    $delete_content = $conn->prepare("DELETE FROM `content` WHERE id = ? LIMIT 1");
    $delete_content->execute([$delete_id]);

    header("location: update_content.php?get_id=$get_id");
    exit;
}

?>
   <style>
    <?php
    include '../css/admin_style.css'; // Include your CSS file here
    ?>
</style>
<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="utf-8">
   <meta name="viewport" content="width=device-width, initial-scale=1">
   <title>add playlist</title>

   <!-- Boxicons link -->
   <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>

   <!-- Custom CSS link -->
   <link rel="stylesheet" href="../css/admin_style.css">


</head>
<body>

   <?php include '../components/admin_header.php'; ?>
   <?php
   // Display success message after redirect
   if (isset($_GET['updated']) && $_GET['updated'] == 1) {
       echo "<p class='message'>Content updated successfully!</p>";
   }
   if (!empty($message)) {
       if (is_array($message)) {
           foreach($message as $msg) {
               echo "<p class='message'>" . htmlspecialchars($msg) . "</p>";
           }
       } else {
           echo "<p class='message'>" . htmlspecialchars($message) . "</p>";
       }
   }
   ?>
   <section class="video-form">

<h1 class="heading"> Upload Content</h1>
<?php
$select_video = $conn->prepare("SELECT * from `content` where id = ? AND tutor_id=?");
$select_video->execute([$get_id, $tutor_id]);

if($select_video->rowCount()>0){
    while($fetch_video = $select_video->fetch(PDO::FETCH_ASSOC)){
        $video_id = $fetch_video['id'];
?>

<form action="" method="post" enctype="multipart/form-data">
<input type="hidden" name="video_id" value="<?= $fetch_video['id']?>">
<input type="hidden" name="old_thumb" value="<?= $fetch_video['thumb']?>">
<input type="hidden" name="old_video" value="<?= $fetch_video['video']?>">
  <p>Update Status <span>*</span></p>
  <select name="status" class="box" required>
    <option value="<?= $fetch_video['status']?>" selected>--- Select Status ---</option>
    <option value="active">Active</option>
    <option value="deactive">Deactive</option>
  </select>

  <p>Update Title <span>*</span></p>
  <input type="text" name="title" maxlength="150" required placeholder="Enter playlist title" class="box" value="<?= $fetch_video['title']?>">

  <p>Update Description <span>*</span></p>
  <textarea name="description" class="box" placeholder="Write description" maxlength="1000" cols="30" rows="10" value="<?= $fetch_video['description']?>"  required></textarea>

  <p>Update Playlist<span>*</span></p>
  
  <select name="playlist" id="" class="box" required>
   <option selected disabled  value="<?= $fetch_video['id']?>"><?= $fetch_video['title']?></option>
   <?php
   $select_playlist = $conn->prepare("SELECT * FROM `playlist` WHERE tutor_id = ?");
   $select_playlist->execute([$tutor_id]);
   if($select_playlist->rowCount() > 0){
      while($fetch_playlist = $select_playlist->fetch(PDO::FETCH_ASSOC)){
         ?>
         <option value="<?= $fetch_playlist['id']; ?>"><?= $fetch_playlist['title']; ?>></option>
         <?php
      }
   ?>
   <?php
   }else{
      echo '<option value="">No playlist available</option>';
   }
   ?>
  </select>
  <img src="../uploaded_files/<?= htmlspecialchars($fetch_video['thumb']); ?>" alt="Thumbnail" class="thumb">
  <p>Update Thumbnail<span>*</span></p>
  <input type="file" name="image" accept="image/*" required class="box">

  <video src="../uploaded_files/<?= htmlspecialchars($fetch_video['video']); ?>" controls class="video"></video>
  <p>Update Video<span>*</span></p>
  <input type="file" name="video" accept="video/*" required class="box">

  <div class="flex-btn">
      <input type="submit" name="update" value="Update Content" class="btn">
      <a href="view_content.php?get_id=<?= $video_id; ?>" class="btn">View Content</a>
      <input type="submit" name="delete_video" value="Delete Content" class="btn" onclick="return confirm('Delete this content?');">
  </div>

</form>
<?php
    }
}else{
    echo ' <div class="empty">
         <p style="margin-bottom: 1.5rem;">No video added yet</p>
         <a href="add_content.php" class="btn" style="margin-top: 1.5rem;">add Videos</a>
        </div>';
}
?>
</section>

<?php include '../components/footer.php'; ?>
   <script src="../js/admin_script.js"></script>
</body>
</html>