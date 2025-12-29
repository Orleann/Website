<?php
    $title = isset($title) ? $title : 'Sigma Notes';
?>


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
                </main>
                <!-- page conent -->
            </div>
            <footer>
                Stopka
            </footer>
        </div>
    </body>
</html>