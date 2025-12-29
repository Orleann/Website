<style>
    .close-sidebar{
        color: blueviolet;
    }
    .a{
        color: blueviolet;
    }
    .sidebar-submenu{
        display: none;
    }
    i{
        color: blueviolet;
    }
</style>

<nav id="sidebar" class="sidebar-wrapper">
    <div class="sidebar-content" style="background-color: #000000ff;">
        <div class="sidebar-brand">
            <div id = "close-sidebar" class="close-sidebar">
                <i class="fas fa-times" "></i>
            </div>
        </div>
        <div class="sidebar-header">
            <div class="user-info">
                <span class="user-name" style="color: blueviolet"><strong>Nazwa Użytkownika</strong></span>
            </div>
        </div>
        <div class="sidebar-search">
            <div>
                <div class="input-group">
                    <input type="text" class="form-control search-menu" placeholder="Szukaj notatki...">
                    <div class="input-group-append">
                        <span class="input-group-text"><i class="fa fa-search" aria-hidden="true"></i></span>
                    </div>
                </div>
            </div>
        </div>   
        <div class="sidebar-menu">
        <ul>
            <li class="sidebar-dropdown">
                <a href="#"><i class="fa fa-sticky-note"></i><span>Notatki</span></a>
                <div class="sidebar-submenu">
                    <ul>
                        <li><a href="#">Notatka 1</a></li>
                        <li><a href="#">Notatka 2</a></li>
                        <li><a href="#">Notatka 3</a></li>
                    </ul>
                </div>
            </li>
            <li class="sidebar-dropdown">
                <a href="#"><i class="fa fa-file-alt"></i><span>Kategorie</span></a>
                <div class="sidebar-submenu">
                    <ul>
                        <li><a href="#">Kategoria 1</a></li>
                        <li><a href="#">Kategoria 2</a></li>
                        <li><a href="#">Kategoria 3</a></li>
                    </ul>
                </div>
            </li>
            <li class="sidebar-dropdown">
            <a href="#"><i class="far fa-chart-bar"></i><span>Statystki</span></a>
            </li>
            
            <li class="sidebar-dropdown">
            <a href="#"><i class="fa fa-question"></i><span>Pomoc</span></a>
            <div class="sidebar-submenu">
                
            </div>
            </li>
            
            <li class="sidebar-dropdown">
            <a href="#"><i class="fa fa-wrench"></i><span>Ustawienia</span></a>
            </li>
            <li class="sidebar-dropdown">
            <a href="#"><i class="fa fa-user-circle"></i><span>Konto</span></a>
            </li>

        </ul>
        </div>
    </div>
</nav>