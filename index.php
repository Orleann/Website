<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.6.0/css/bootstrap.min.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
<link rel="stylesheet" href="assets/css/sidebar.css">

<title>Sigma Notes</title>

<style>
  .page-wrapper{
    background-color: black;
    color: blueviolet;
    text-align: center;
  }
  a{
    color: blueviolet;
  }

</style>

<?php
    if(!isset($_GET["site"])){
        $site = 'main';
    }
    else { $site = $_GET["site"]; }
    $path = __DIR__ . '/assets/site/' . $site . '.php';
?>

<div class="page-wrapper chiller-theme">
  <a id="show-sidebar" class="btn btn-sm btn-dark" href="#">
    <i class="fas fa-bars"></i>
  </a>
  
  <nav id="sidebar" class="sidebar-wrapper">
    <div class="sidebar-content">
      <div class="sidebar-brand">
        <div id="close-sidebar"><i class="fas fa-times"></i></div>
      </div>
      <?php include 'assets/site/sidebar.php'; ?>
    </div>
  </nav>    
  <main class="page-content">
    <div class="container-fluid">
      <h2>Sigma Notes</h2>
      <hr>
    </div>
  </main>
  <div class = "page-content" style="text-align: center;">
    <?php 
      if(file_exists($path)){
        include $path;
      } else {
        echo "404 - Plik : " . htmlspecialchars($site) . ' nie istnieje.';
      }
    ?>
  </div>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.1/umd/popper.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.6.0/js/bootstrap.min.js"></script>
<script src="assets/script/sidebar.js"></script>
<script src="assets/script/new_note.js"></script>