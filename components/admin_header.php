<?php
// include database connection and ensure tutor_id is set
include_once __DIR__ . '/connect.php';
$tutor_id = $_COOKIE['tutor_id'] ?? '';
// initialize message array if not already
$message = $message ?? [];

// display any flash messages
if (!empty($message)) {
   foreach ($message as $msg) {
      echo "<div class='message'><span>" . htmlspecialchars($msg) . "</span><i class='bx bx-x' onclick='this.parentElement.remove();'></i></div>";
   }
}
?>

<header class="header flex">
   <section class="flex">

      <!-- Logo -->
      <a href="dashboard.php">
         <img src="../image/logo.png" width="130px" alt="Logo">
      </a>

      <!-- Search Form -->
      <form action="search_page.php" method="post" class="search-form flex">
         <input type="text" name="search" placeholder="search here.." required maxlength="100">
         <button type="submit" name="search_btn">
            <i class="bx bx-search-alt-2"></i>
         </button>
      </form>

      <!-- Icons -->
      <div class="icons">
         <div id="menu-btn" class="bx bx-list-plus"></div>
         <div id="search-btn" class="bx bx-search-alt-2-plus"></div>
         <div id="user-btn" class="bx bxs-user"></div>
      </div>

      <!-- Profile Box -->
      <div class="profile">
         <?php
            $select_profile = $conn->prepare("SELECT * FROM tutors WHERE id = ?");
            $select_profile->execute([$tutor_id]);

            if ($select_profile->rowCount() > 0) {
               $fetch_profile = $select_profile->fetch(PDO::FETCH_ASSOC);
         ?>
               <img src="../uploaded_files/<?= $fetch_profile['image']; ?>" alt="<?= htmlspecialchars($fetch_profile['name']); ?>'s profile image">
               <h3><?= $fetch_profile['name']; ?></h3>
               <span><?= $fetch_profile['profession']; ?></span><br>

               <div id="flex-btn">
                  <a href="profile.php" class="btn">view profile</a>
                  <a href="../components/admin_logout.php" onclick="return confirm('logout from this website?');" class="btn">logout</a>
               </div>

         <?php } else { ?>
               <h3>please login or register</h3>
               <div id="flex-btn">
                  <a href="login.php" class="btn">login</a>
                  <a href="register.php" class="btn">register</a>
               </div>
         <?php } ?>
      </div>

   </section>
</header>


<div class="side-bar">
   <div class="profile">
      <?php
      $select_profile = $conn->prepare("SELECT * FROM tutors WHERE id = ?");
      $select_profile->execute([$tutor_id]);

      if ($select_profile->rowCount() > 0) {
         $fetch_profile = $select_profile->fetch(PDO::FETCH_ASSOC);
      ?>
         <img src="../uploaded_files/<?= $fetch_profile['image']; ?>" alt="Profile Image">
         <h3><?= $fetch_profile['name']; ?></h3>
         <p><?= $fetch_profile['profession']; ?></p>
         <a href="profile.php" class="btn">view profile</a>
      <?php } else { ?>
         <h3>please login or register</h3>
         <div id="flex-btn">
            <a href="login.php" class="btn">SignIn</a>
            <a href="register.php" class="btn">Signup</a>
         </div>
      <?php } ?>
   </div>

   <nav class="navbar">
      <a href="dashboard.php"><i class="bx bxs-home-heart"></i><span>home</span></a>
      <a href="playlists.php"><i class="bx bxs-receipt"></i><span>playlist</span></a>
      <a href="contents.php"><i class="bx bxs-graduation"></i><span>contents</span></a>
      <a href="comments.php"><i class="bx bxs-chat"></i><span>comments</span></a>
      <a href="../components/admin_logout.php" onclick="return confirm('logout from this website?');"><i class="bx bxs-log-in-circle"></i><span>logout</span></a>
      
   </nav>
</div>