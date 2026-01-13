<script src="https://cdn.tailwindcss.com"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
<link rel="stylesheet" href="assets/css/sidebar.css">

<title>Sigma Notes</title>

<style>
  * {
    font-family: 'Inter', sans-serif;
  }
  
  body {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 25%, #f093fb 50%, #4facfe 75%, #00f2fe 100%);
    background-size: 400% 400%;
    animation: gradientShift 15s ease infinite;
    min-height: 100vh;
  }

  @keyframes gradientShift {
    0% { background-position: 0% 50%; }
    50% { background-position: 100% 50%; }
    100% { background-position: 0% 50%; }
  }

  .glass {
    background: rgba(255, 255, 255, 0.1);
    backdrop-filter: blur(10px);
    -webkit-backdrop-filter: blur(10px);
    border: 1px solid rgba(255, 255, 255, 0.2);
    box-shadow: 0 8px 32px 0 rgba(31, 38, 135, 0.37);
  }

  .glass-dark {
    background: rgba(15, 23, 42, 0.7);
    backdrop-filter: blur(20px);
    -webkit-backdrop-filter: blur(20px);
    border: 1px solid rgba(255, 255, 255, 0.1);
    box-shadow: 0 8px 32px 0 rgba(0, 0, 0, 0.37);
  }

  .page-wrapper {
    min-height: 100vh;
    color: #ffffff;
  }

  .content-container {
    animation: fadeInUp 0.6s ease-out;
  }

  @keyframes fadeInUp {
    from {
      opacity: 0;
      transform: translateY(20px);
    }
    to {
      opacity: 1;
      transform: translateY(0);
    }
  }

  a {
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
  }

  .btn-glass {
    background: rgba(255, 255, 255, 0.15);
    backdrop-filter: blur(10px);
    border: 1px solid rgba(255, 255, 255, 0.3);
    transition: all 0.3s ease;
  }

  .btn-glass:hover {
    background: rgba(255, 255, 255, 0.25);
    transform: translateY(-2px);
    box-shadow: 0 10px 25px rgba(0, 0, 0, 0.2);
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
  <a id="show-sidebar" class="fixed left-4 top-4 z-50 w-12 h-12 flex items-center justify-center glass rounded-full text-white hover:scale-110 transition-transform duration-300 shadow-lg" href="#">
    <i class="fas fa-bars text-lg"></i>
  </a>
  
  <nav id="sidebar" class="sidebar-wrapper glass-dark">
    <div class="sidebar-content">
      <div class="sidebar-brand">
        <div id="close-sidebar" class="cursor-pointer hover:rotate-90 transition-transform duration-300"><i class="fas fa-times text-white text-xl"></i></div>
      </div>
      <?php include 'assets/site/sidebar.php'; ?>
    </div>
  </nav>    
  
  <main class="page-content">
    <div class="w-full py-8 px-4">
      <div class="glass-dark rounded-2xl p-6 mb-6 max-w-4xl mx-auto">
        <h1 class="text-4xl font-bold bg-gradient-to-r from-white via-purple-200 to-pink-200 bg-clip-text text-transparent animate-pulse">
          Sigma Notes
        </h1>
      </div>
    </div>
  </main>
  
  <div class="content-container px-4 pb-8">
    <div class="max-w-6xl mx-auto">
      <?php 
        if(file_exists($path)){
          include $path;
        } else {
          echo '<div class="glass-dark rounded-2xl p-8 text-center"><h2 class="text-2xl font-semibold mb-4">404</h2><p class="text-gray-300">Plik : ' . htmlspecialchars($site) . ' nie istnieje.</p></div>';
        }
      ?>
    </div>
  </div>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.1/umd/popper.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.6.0/js/bootstrap.min.js"></script>
<script src="assets/script/sidebar.js"></script>
<script src="assets/script/account.js"></script>
<script src="assets/script/new_note.js"></script>