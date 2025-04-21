<?php
include 'components/connect.php';

if (isset($_COOKIE['user_id'])) {
    $user_id = $_COOKIE['user_id'];
} else {
    $user_id = '';
}

if (isset($_GET['get_id'])) {
    $get_id = $_GET['get_id'];
} else {
    $get_id = '';
    header('location: index.php');
}

if(isset($_POST['save-list'])){
    if($user_id == ''){
        
        $list_id = $_POST['list_id'];
        $list_id = filter_var($list_id, FILTER_SANITIZE_STRING);

        $select_list = $conn->prepare("SELECT * FROM bookmark WHERE user_id = ? AND playlist_id = ? LIMIT 1");
        $select_list->execute([$user_id, $list_id]);

        if($select_list->rowCount() > 0){
            $remove_bookmark = $conn->prepare("DELETE FROM bookmark WHERE user_id = ? AND playlist_id = ?");
            $remove_bookmark->execute([$user_id, $list_id]);
            $message[] = 'Playlist removed from bookmark!';
}       else{
            $insert_bookmark = $conn->prepare("INSERT INTO bookmark (user_id, playlist_id) VALUES (?, ?)");
            $insert_bookmark->execute([$user_id, $list_id]);
            $message[] = 'Playlist added to bookmark!';
        }
    }else{
        $message[] = 'Please login to save playlist!';
    }    
    }

?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>HiStudy- PlaylistPage</title>

    <!-- Boxicons CDN -->
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <link rel="stylesheet" href="css/user_style.css">
</head>
<body>
<?php include 'components/user_header.php'; ?>
<!-- Banner Section -->
<div class="banner">
  <div class="detail">
    <div class="title">
      <a href="index.php">home</a>
      <span> <i class="bx bx-chevron-right"></i>playlist</span>
    </div>
     
<h1>My Playlist</h1>
<p style="font-size: 25px;">Dive in and learn React.js from scratch! Learn Reactjs, Hooks, Redux, React Routing,
Animations, Next.js and way more!</p>
    <div class="flex-btn">
    <a href="login.php" class="btn">login to start</a>
    <a href="contact.php" class="btn">contact us</a>
    </div>
  </div>
  
<img src="image/about.png">
</div>

<!--------------------------Playlist Section------------------->
<section class="watch-video">
    <?php
    $select_content = $conn->prepare("SELECT * FROM `content` WHERE id = ? AND status = ?");
    $select_content->execute([$get_id, 'active ']);

    if ($select_content->rowCount() > 0) {
        while ($row = $select_content->fetch(PDO::FETCH_ASSOC)){
            $content_id = $row['id'];
            $select_likes = $conn->prepare("SELECT * FROM `likes` WHERE content_id = ? AND user_id = ? LIMIT 1");
            $select_likes->execute([$content_id, $user_id]);
            $total_likes = $select_likes->rowCount();

            $verify_likes = $conn->prepare("SELECT * FROM `likes` WHERE content_id = ? AND user_id = ? LIMIT 1");
            $verify_likes->execute([$content_id, $user_id]);

            $select_tutor = $conn->prepare("SELECT * FROM `tutors` WHERE id = ? LIMIT 1");
            $select_tutor->execute([$row['tutor_id']]);
            $fetch_tutor = $select_tutor->fetch(PDO::FETCH_ASSOC);

     ?>
     <div class="video-details">
        <video src="uploaded_files/<?= $row['video'];?>" class="video" poster="uploaded_files/<?= $row['thumb'];?>" controls autoplay></video>
        <h3 class="title"><?= $row['title']; ?></h3>
        <div class="info">
        <p>
        <i class="bx bxs-calendar-alt"></i><span><?= $row['date']; ?></span>
        </p>
        <p>
        <i class="bx bxs-heart"></i><span><?= $total_likes; ?></span>
        </p>
        </div>
        <div class="tutor">
            <img src="uploaded_files/<?= $fetch_tutor['image']; ?>" alt="tutor image">
            <div>
            <h3 class="tutor-name"><?= $fetch_tutor['name']; ?></h3>
            <span><?= $fetch_tutor['profession']; ?></span>
            </div>

        </div>
        <form action="" method ="post" class="flex">
            <input type="hidden" name="content_id" value="<?= $content_id; ?>">
            <a href="playlist.php?get_id=<?= $row['playlist_id']; ?>" class="btn">View Playlist</a>

            <?php
            if ($verify_likes->rowCount() > 0) { ?>
                <button type = "submit" name="like_content"><i class="bx bxs-heart"></i><span>liked</span></button>
            <?php } else { ?>
                <button type = "submit" name="like_content"><i class="bx bxs-heart"></i><span>like</span></button>
            <?php } ?>
        </form>
        </div>
     <?php
        } 
    }else{
        echo '<p class="empty">No video found!</p>';
    }
     ?>

   
</section>
</section>
  <?php include 'components/footer.php'; ?>
    <script type="text/javascript" src="js/user_script.js"></script>
</body>
</html>
