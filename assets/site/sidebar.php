<div class="sidebar-header">
    <div class="user-info">
        <div class="flex items-center space-x-3 p-4 rounded-xl glass-dark mb-4">
            <div class="w-10 h-10 rounded-full bg-gradient-to-br from-purple-400 to-pink-400 flex items-center justify-center text-white font-bold text-lg">
                N
            </div>
            <span class="user-name text-white font-semibold">Nazwa Użytkownika</span>
        </div>
    </div>
</div>
<div class="sidebar-search">
    <div class="px-4 pb-4">
        <div class="relative">
            <input type="text" class="w-full px-4 py-3 rounded-xl glass-dark border border-white/20 text-white placeholder-gray-300 focus:outline-none focus:ring-2 focus:ring-purple-400 focus:border-transparent transition-all duration-300" placeholder="Szukaj notatki...">
            <i class="fas fa-search absolute right-4 top-1/2 transform -translate-y-1/2 text-gray-300"></i>
        </div>
    </div>
</div>
<div class="sidebar-menu">
    <ul>
        <li class="header-menu px-4 py-2"><span class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Menu</span></li>
        <li class="sidebar-dropdown">
            <a href="?site=main" class="menu-item"><i class="fa fa-house"></i><span>Strona Główna</span></a>
        </li>
        <li class="sidebar-dropdown">
            <a href="#" class="menu-item"><i class="fa fa-book"></i><span>Notatki</span></a>
            <div class="sidebar-submenu">
                <ul>
                    <li><a href="?site=notes">Wszystkie notatki</a></li>
                    <li><a href="?site=subsite/fav">Ulubione notatki</a></li>
                    <li><a href="?site=subsite/new_note">Napisz nową notatkę</a></li>
                </ul>
            </div>
        </li>
        <li class="sidebar-dropdown">
            <a href="#" class="menu-item"><i class="fa fa-list"></i><span>Kategorie</span></a>
        <div class="sidebar-submenu">
            <ul>
                <li><a href="?site=categories">Wszystkie Kategorie</a></li>
                <li><a href="?site=subsite/new_category">Dodaj Kategorie</a></li>
                <li><a href="?site=subsite/category">Konkretna kategoria</a></li>
            </ul>
        </div>
        </li>
        <li class="sidebar-dropdown">
            <a href="?site=stats" class="menu-item"><i class="fa fa-chart-line"></i><span>Statystki</span></a>
        </li>
        <li class="sidebar-dropdown">
            <a href="?site=settings" class="menu-item"><i class="fa fa-cog"></i><span>Ustawienia</span></a>
        </li>
        <li class="sidebar-dropdown">
            <a href="?site=account" class="menu-item"><i class="fa fa-user-circle"></i><span>Konto</span></a>
        </li>
        <li class="sidebar-dropdown">
            <a href="?site=help" class="menu-item"><i class="fa fa-question-circle"></i><span>Pomoc</span></a>
        </li>   
        <li class="sidebar-dropdown">
            <a href="?site=about" class="menu-item"><i class="fa fa-info-circle"></i><span>O Aplikacji</span></a>
        </li>
    </ul>
</div>