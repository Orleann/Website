<?php include 'connection.php'; 
    $connection -> query("select * from notes");
    $title -> query("SELECT title FROM notes WHERE id = 1");
?>

<div class="glass-dark rounded-2xl p-8 shadow-2xl">
    <h2 class="text-3xl font-bold mb-6 bg-gradient-to-r from-purple-400 to-pink-400 bg-clip-text text-transparent">Notatki</h2>
    <div class="space-y-4">
        <div class="glass rounded-xl p-4 hover:scale-105 transition-transform duration-300 cursor-pointer">
            <h3 class="text-white font-semibold mb-2"><?php echo $title;?></h3>
        </div>
        <div class="glass rounded-xl p-4 hover:scale-105 transition-transform duration-300 cursor-pointer">
            <h3 class="text-white font-semibold mb-2">Notatka 1</h3>
        </div>
        <div class="glass rounded-xl p-4 hover:scale-105 transition-transform duration-300 cursor-pointer">
            <h3 class="text-white font-semibold mb-2">Notatka 2</h3>
        </div>
        <div class="glass rounded-xl p-4 hover:scale-105 transition-transform duration-300 cursor-pointer">
            <h3 class="text-white font-semibold mb-2">Notatka 3</h3>
        </div>
    </div>
</div>