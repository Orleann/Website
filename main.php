<?php
    $title = isset($title) ? $title : 'My Website';
?>
<!doctype html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width,initial-scale=1">
        <title><?php echo htmlspecialchars($title, ENT_QUOTES, 'UTF-8'); ?></title>
        <meta name="description" content="A simple main website template">
        <link rel="stylesheet" href="assets/css/app.css">
    </head>
    <body>
        <div class="site">
            <header>
                <div class="header">
                    Sigma notes
                </div>
            </header>
            <main>
                <div class="main">
                    //Content
                </div>
            </main>
            <footer>
                <div class="container">
                    Stopka (kto wie ten wie)
                </div>
            </footer>
        </div>
    </body>
</html>
