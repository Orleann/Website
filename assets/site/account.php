<div class="glass-dark rounded-2xl p-8 shadow-2xl max-w-md mx-auto">
    <div class="text-center mb-6">
        <div class="inline-block p-4 bg-gradient-to-br from-purple-500/30 to-pink-500/30 rounded-full mb-4">
            <i class="fas fa-user-circle text-5xl text-white"></i>
        </div>
        <h2 class="text-3xl font-bold bg-gradient-to-r from-purple-400 to-pink-400 bg-clip-text text-transparent">Konto</h2>
    </div>
    
    <div class="glass rounded-xl p-4 mb-4">
        <p class="text-white/70 text-center italic">XD TEMPORARY SPRAWDZAMY CZY DZIAŁA XD</p>
    </div>

    <div class="Login">
        <h3 class="text-xl font-semibold text-white mb-4">Logowanie</h3>
        <form class="space-y-4" action="assets/site/subsite/login.php" method="POST">
            <div>
                <label for="username" class="block text-white/80 mb-2 text-sm font-medium">Login</label>
                <input type="text" id="username" name="username" placeholder="Nazwa użytkownika" 
                    class="w-full px-4 py-3 rounded-xl glass border border-white/20 text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-purple-400 focus:border-transparent transition-all duration-300">
            </div>
            <div>
                <label for="password" class="block text-white/80 mb-2 text-sm font-medium">Hasło</label>
                <input type="password" id="password" name="password" placeholder="Hasło" 
                    class="w-full px-4 py-3 rounded-xl glass border border-white/20 text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-purple-400 focus:border-transparent transition-all duration-300">
            </div>
            <button type="submit" 
                class="w-full py-3 px-6 bg-gradient-to-r from-purple-500 to-pink-500 text-white font-semibold rounded-xl hover:from-purple-600 hover:to-pink-600 transform hover:scale-105 transition-all duration-300 shadow-lg">
                Zaloguj się
            </button>
        </form>
    </div>
</div>