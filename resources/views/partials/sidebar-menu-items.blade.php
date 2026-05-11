<ul class="main-menu" id="all-menu-items" role="menu" style="padding-bottom:20px;">
    <li class="menu-title" role="presentation" data-lang="hr-title-applications">
        <span class="menu-title-text">Dashboard</span>
    </li>
    <li class="slide">
        <a href="{{ route('dashboard') }}" 
           class="side-menu__item {{ request()->is('dashboard') ? 'active' : '' }}" 
           role="menuitem" 
           data-lang="hr-apps-dashboard"
           title="Dashboard Utama">
            <span class="side_menu_icon"><i class="ri-dashboard-3-line"></i></span>
            <span class="side-menu__label">Dashboard Talenta</span>
        </a>
    </li>

    <li class="menu-title" role="presentation" data-lang="hr-title-applications">
        <span class="menu-title-text">Menu Utama</span>
    </li>
    <li class="slide">
        <a href="{{ route('mappingpegawaim') }}" 
           class="side-menu__item {{ request()->is('mappingpegawaim') ? 'active' : '' }}" 
           role="menuitem" 
           data-lang="hr-apps-mappingpegawaim"
           title="Mapping Pegawai">
            <span class="side_menu_icon"><i class="ri-group-line"></i></span>
            <span class="side-menu__label">Mapping Pegawai</span>
        </a>
    </li>
    <li class="slide">
        <a href="{{ route('datacascadingm') }}" 
           class="side-menu__item {{ request()->is('datacascadingm') ? 'active' : '' }}" 
           role="menuitem" 
           data-lang="hr-apps-lapsppd"
           title="Cascading KPI">
            <span class="side_menu_icon"><i class="ri-checkbox-multiple-line"></i></span>
            <span class="side-menu__label">Cascading KPI</span>
        </a>
    </li>
    <li class="slide">
        <a href="{{ route('mappingkpim') }}" 
           class="side-menu__item {{ request()->is('mappingkpim') ? 'active' : '' }}" 
           role="menuitem"
           title="Mapping KPI Pegawai">
            <span class="side_menu_icon"><i class="ri-user-settings-line"></i></span>
            <span class="side-menu__label">Mapping KPI Pegawai</span>
        </a>
    </li>

    <li class="menu-title" role="presentation" data-lang="hr-title-applications">
        <span class="menu-title-text">Admin SIMKP</span>
    </li>
    <li class="slide">
        <a href="{{ route('datapencapaianm') }}" 
           class="side-menu__item {{ request()->is('datapencapaianm') ? 'active' : '' }}" 
           role="menuitem"
           title="Pencapaian KPI">
            <span class="side_menu_icon"><i class="ri-crosshair-2-line"></i></span>
            <span class="side-menu__label">Pencapaian KPI</span>
        </a>
    </li>
    <li class="slide">
        <a href="{{ route('kinerjapegawaim') }}" 
           class="side-menu__item {{ request()->is('kinerjapegawaim') ? 'active' : '' }}" 
           role="menuitem"
           title="Penilaian Talenta">
            <span class="side_menu_icon"><i class="ri-crosshair-2-line"></i></span>
            <span class="side-menu__label">Penilaian Talenta</span>
        </a>
    </li>
    <li class="slide">
        <a href="{{ route('laptalentam') }}" 
           class="side-menu__item {{ request()->is('laptalentam') ? 'active' : '' }}" 
           role="menuitem"
           title="Rekap Talenta">
            <span class="side_menu_icon"><i class="ri-list-settings-line"></i></span>
            <span class="side-menu__label">Rekap Talenta</span>
        </a>
    </li>
    <li class="slide">
        <a href="{{ route('riwayatgradem') }}" 
           class="side-menu__item {{ request()->is('riwayatgradem') ? 'active' : '' }}" 
           role="menuitem"
           title="Riwayat Grade">
            <span class="side_menu_icon"><i class="ri-history-line"></i></span>
            <span class="side-menu__label">Riwayat Grade</span>
        </a>
    </li>
    <li class="slide">
        <a href="{{ route('kenaikangradem') }}" 
           class="side-menu__item {{ request()->is('kenaikangradem') ? 'active' : '' }}" 
           role="menuitem"
           title="Kenaikan Grade">
            <span class="side_menu_icon"><i class="ri-arrow-right-up-line"></i></span>
            <span class="side-menu__label">Kenaikan Grade</span>
        </a>
    </li>
    <li class="slide">
        <a href="{{ route('historytalentam') }}" 
           class="side-menu__item {{ request()->is('historytalentam') ? 'active' : '' }}" 
           role="menuitem"
           title="History Talenta">
            <span class="side_menu_icon"><i class="ri-history-line"></i></span>
            <span class="side-menu__label">History Talenta</span>
        </a>
    </li>
    <li class="slide">
        <a href="{{ route('mappingskorm') }}" 
           class="side-menu__item {{ request()->is('mappingskorm') ? 'active' : '' }}" 
           role="menuitem"
           title="Matriks Skor">
            <span class="side_menu_icon"><i class="ri-bubble-chart-line"></i></span>
            <span class="side-menu__label">Matriks Skor</span>
        </a>
    </li>
    <li class="slide">
        <a href="{{ route('mappingpengukuranm') }}" 
           class="side-menu__item {{ request()->is('mappingpengukuranm') ? 'active' : '' }}" 
           role="menuitem"
           title="Matriks Pengukuran">
            <span class="side_menu_icon"><i class="ri-bubble-chart-line"></i></span>
            <span class="side-menu__label">Matriks pengukuran</span>
        </a>
    </li>
    
    <li class="menu-title" role="presentation" data-lang="hr-title-applications">
        <span class="menu-title-text">Data Master</span>
    </li>
    <li class="slide">
        <a href="{{ route('datauserm') }}" 
           class="side-menu__item {{ request()->is('datauserm') ? 'active' : '' }}" 
           role="menuitem"
           title="Manajemen User">
            <span class="side_menu_icon"><i class="ri-user-settings-line"></i></span>
            <span class="side-menu__label">Manajemen User</span>
        </a>
    </li>
    <li class="slide">
        <a href="{{ route('periodekinerja') }}" 
           class="side-menu__item {{ request()->is('periodekinerja') ? 'active' : '' }}" 
           role="menuitem"
           title="Manajemen User">
            <span class="side_menu_icon"><i class="ri-calendar-schedule-line"></i></span>
            <span class="side-menu__label">Periode Kinerja</span>
        </a>
    </li>
    <li class="slide">
        <a href="{{ route('masterregionm') }}" 
           class="side-menu__item {{ request()->is('masterregionm') ? 'active' : '' }}" 
           role="menuitem"
           title="Master Region">
            <span class="side_menu_icon"><i class="ri-database-2-line"></i></span>
            <span class="side-menu__label">Master Region</span>
        </a>
    </li>
    <li class="slide">
        <a href="{{ route('masteraream') }}" 
           class="side-menu__item {{ request()->is('masteraream') ? 'active' : '' }}" 
           role="menuitem" 
           data-lang="hr-apps-datasppd"
           title="Master Area/Site">
            <span class="side_menu_icon"><i class="ri-database-2-line"></i></span>
            <span class="side-menu__label">Master Area/Site</span>
        </a>
    </li>
    <li class="slide">
        <a href="{{ route('divisim') }}" 
           class="side-menu__item {{ request()->is('divisim') ? 'active' : '' }}" 
           role="menuitem" 
           data-lang="hr-apps-datasppd"
           title="Master Divisi">
            <span class="side_menu_icon"><i class="ri-database-2-line"></i></span>
            <span class="side-menu__label">Master Divisi</span>
        </a>
    </li>
    <li class="slide">
        <a href="{{ route('masterlevelm') }}" 
           class="side-menu__item {{ request()->is('masterlevelm') ? 'active' : '' }}" 
           role="menuitem" 
           data-lang="hr-apps-datasppd"
           title="Master Level">
            <span class="side_menu_icon"><i class="ri-database-2-line"></i></span>
            <span class="side-menu__label">Master Level</span>
        </a>
    </li>

</ul>
<style>
/* Hide text elements when sidebar is collapsed */
.sidebar-collapsed .side-menu__label,
.sidebar-collapsed .side-menu__angle,
.sidebar-collapsed .menu-title-text,
.sidebar-collapsed .submenu-text {
    display: none;
}

/* Show only icons when collapsed */
.sidebar-collapsed .side_menu_icon {
    display: flex;
    justify-content: center;
    align-items: center;
    width: 100%;
}

/* Adjust menu item spacing when collapsed - REDUCED PADDING */
.sidebar-collapsed .side-menu__item {
    justify-content: center;
    padding: 0.35rem 0.5rem !important; /* Reduced from 0.75rem */
    margin-bottom: 0.15rem; /* Add small margin between items */
}

/* Reduce padding for menu titles when collapsed */
.sidebar-collapsed .menu-title {
    padding: 0.25rem 0 !important;
    margin: 0.25rem 0 !important;
}

/* Hide submenu when collapsed */
.sidebar-collapsed .slide-menu {
    display: none !important;
}

/* Tooltip on hover when collapsed */
.sidebar-collapsed .side-menu__item {
    position: relative;
}

.sidebar-collapsed .side-menu__item:hover::after {
    content: attr(title);
    position: absolute;
    left: 100%;
    top: 50%;
    transform: translateY(-50%);
    background: #333;
    color: #fff;
    padding: 0.5rem 1rem;
    border-radius: 4px;
    white-space: nowrap;
    margin-left: 0.5rem;
    z-index: 1000;
    font-size: 0.875rem;
    box-shadow: 0 2px 8px rgba(0,0,0,0.15);
}

/* Add small arrow to tooltip */
.sidebar-collapsed .side-menu__item:hover::before {
    content: '';
    position: absolute;
    left: 100%;
    top: 50%;
    transform: translateY(-50%);
    width: 0;
    height: 0;
    border-top: 6px solid transparent;
    border-bottom: 6px solid transparent;
    border-right: 6px solid #333;
    margin-left: 0.25rem;
    z-index: 1001;
}

/* Default padding for normal and submenu items */
.side-menu__item,
.slide-menu li a {
    padding: 3px 10px !important;
}

/* Optional: Make icons slightly smaller in collapsed mode for tighter spacing */
.sidebar-collapsed .side_menu_icon i {
    font-size: 1.1rem; /* Adjust as needed */
}

/* Optional: Reduce overall sidebar width when collapsed */
.sidebar-collapsed {
    width: 60px !important; /* Adjust as needed */
}    
</style>
