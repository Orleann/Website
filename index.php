<?php
    $title = isset($title) ? $title : 'Sigma Notes';
?>

<style>
    html, body {
        height: 100%;
        margin: 0;
        background-color: black;
    }
    .site {
        background-color: black;
        text-align: center;
        display: flex;
        flex-direction: column;
        min-height: 100vh;
    }
    h2{
        color: blueviolet;
    }
    .sidebar-wrapper {
        position: fixed;
        top: 0;
        left: -300px;
        height: 100%;
        width: 260px;
        z-index: 1000;
        transition: all 0.3s ease;
        overflow-y: auto;
    }
    .page-wrapper {
        flex: 1;
        position: relative;
    }
    #show-sidebar {
        position: absolute;
        left: 20px;
        top: 20px;
        z-index: 10;
    }
    .page-wrapper.toggled .sidebar-wrapper {
        left: 0;
    }
</style>

<!doctype html>
<html lang="pl">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width,initial-scale=1">
        <title><?php echo htmlspecialchars($title, ENT_QUOTES, 'UTF-8'); ?></title>
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.6.0/css/bootstrap.min.css">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
        <link rel="stylesheet" href="assets/css/sidebar.css">
        <link rel="stylesheet" href="assets/css/app.css">
        <link rel="stylesheet" href="assets/css/main.css">
        <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
        <script src="assets/script/sidebar.js"></script>
    </head>
    <body>
        <div class="site">
            <div class = "page-wrapper chiller-theme main">
                <a id = "show-sidebar" class = "btn btn-sm btn-dark" href = "#">
                    <i class = "fas fa-bars"></i>
                </a>
                <div class = "sidebar">
                    <?php include 'assets/site/sidebar.php'; ?>
                </div>

                <main class = "content">
                    <div class = "container-fluid">
                        <h2> Sigma Notes</h2>
                        <hr>
                    </div>
                    <div class = "page-content">
                        <?php include 'assets/site/main.php'; ?>
                    </div>
                </main>
            </div>
            <footer>
                Stopka
            </footer>
        </div>
    </body>
</html>