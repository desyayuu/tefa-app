<div class="sidebar">
    <div class="brand">
        <svg width="48" height="34" viewBox="0 0 58 44" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path d="M17.6063 40.419C12.5737 45.4549 4.0503 45.4348 1.66995 38.7232C1.20391 37.4092 0.831297 36.0581 0.557239 34.6795C-0.561731 29.0503 0.0125652 23.2158 2.20751 17.9134C4.40245 12.611 8.11944 8.07902 12.8885 4.89045C17.6576 1.70189 23.2644 -2.45848e-07 29.0001 0C34.7357 2.45848e-07 40.3426 1.70189 45.1115 4.89045C49.8806 8.07902 53.5976 12.6111 55.7925 17.9135C57.9875 23.2158 58.5617 29.0503 57.4428 34.6795C57.1688 36.0581 56.7961 37.4092 56.33 38.7232C53.9496 45.4348 45.4263 45.4549 40.3936 40.419L32.625 32.6455C31.8828 31.9028 31.9595 30.678 32.1642 29.648C32.2886 29.0218 32.2247 28.3728 31.9805 27.7829C31.7363 27.193 31.3229 26.6887 30.7924 26.3341C30.2619 25.9795 29.6381 25.7901 29.0001 25.7901C28.362 25.7901 27.7383 25.9795 27.2077 26.3341C26.677 26.6887 26.2636 27.193 26.0194 27.7829C25.7752 28.3728 25.7113 29.0218 25.8359 29.648C26.0406 30.678 26.1172 31.9028 25.375 32.6455L17.6063 40.419Z" fill="url(#paint0_linear_232_5597)"/>
            <defs>
            <linearGradient id="paint0_linear_232_5597" x1="55.3603" y1="14.5091" x2="3.88528" y2="14.5091" gradientUnits="userSpaceOnUse">
            <stop stop-color="#64C2DB"/>
            <stop offset="0.510417" stop-color="#7476ED"/>
            <stop offset="1" stop-color="#E56F8C"/>
            </linearGradient>
            </defs>
        </svg>
        <div class="brand-text">
            <span class="title-sidebar">TEFA Management</span>
            <span class="subtitle">{{ session('role') }}</span>
        </div>
    </div>
    <ul class="sidebar-menu">
        <li>
            <a href="{{ route('dosen.dashboard') }}" class="{{ request()->routeIs('dosen.dashboard') ? 'active' : '' }}">
                <span class="icon">
                    <svg width="12" height="12" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M7.14373 18.7821V15.7152C7.14372 14.9381 7.77567 14.3067 8.55844 14.3018H11.4326C12.2189 14.3018 12.8563 14.9346 12.8563 15.7152V18.7732C12.8562 19.4473 13.404 19.9951 14.0829 20H16.0438C16.9596 20.0023 17.8388 19.6428 18.4872 19.0007C19.1356 18.3586 19.5 17.4868 19.5 16.5775V7.86585C19.5 7.13139 19.1721 6.43471 18.6046 5.9635L11.943 0.674268C10.7785 -0.250877 9.11537 -0.220992 7.98539 0.745384L1.46701 5.9635C0.872741 6.42082 0.517552 7.11956 0.5 7.86585V16.5686C0.5 18.4637 2.04738 20 3.95617 20H5.87229C6.19917 20.0023 6.51349 19.8751 6.74547 19.6464C6.97746 19.4178 7.10793 19.1067 7.10792 18.7821H7.14373Z" fill="#878787"/>
                    </svg>
                </span>
                <span class="menu-text">Dashboard</span>
            </a>
        </li>
        <li class="has-submenu" id="projectMenu">
            <a href="javascript:void(0);" class="main-menu-item">
                <span class="icon">
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path fill-rule="evenodd" clip-rule="evenodd" d="M15.2428 4.73756C15.2428 6.95855 17.0459 8.75902 19.2702 8.75902C19.5151 8.75782 19.7594 8.73431 20 8.68878V16.6615C20 20.0156 18.0215 22 14.6624 22H7.34636C3.97851 22 2 20.0156 2 16.6615V9.3561C2 6.00195 3.97851 4 7.34636 4H15.3131C15.2659 4.243 15.2423 4.49001 15.2428 4.73756ZM13.15 14.8966L16.0078 11.2088V11.1912C16.2525 10.8625 16.1901 10.3989 15.8671 10.1463C15.7108 10.0257 15.5122 9.97345 15.3167 10.0016C15.1211 10.0297 14.9453 10.1358 14.8295 10.2956L12.4201 13.3951L9.6766 11.2351C9.51997 11.1131 9.32071 11.0592 9.12381 11.0856C8.92691 11.1121 8.74898 11.2166 8.63019 11.3756L5.67562 15.1863C5.57177 15.3158 5.51586 15.4771 5.51734 15.6429C5.5002 15.9781 5.71187 16.2826 6.03238 16.3838C6.35288 16.485 6.70138 16.3573 6.88031 16.0732L9.35125 12.8771L12.0948 15.0283C12.2508 15.1541 12.4514 15.2111 12.6504 15.1863C12.8494 15.1615 13.0297 15.0569 13.15 14.8966Z" fill="#878787"/>
                        <circle opacity="0.4" cx="19.5" cy="4.5" r="2.5" fill="#878787"/>
                    </svg>
                </span>
                <span class="menu-text">My Projects</span>
                <span class="submenu-arrow">
                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none">
                        <path d="M19 9l-7 7-7-7" stroke="#878787" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                </span>
            </a>
            <ul class="submenu">
                <li>
                    <a href="{{ route('dosen.getDataProyek') }}" class="submenu-item {{ request()->routeIs('dosen.getDataProyek') ? 'active' : '' }}">
                        <span class="submenu-icon">
                            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M4 21V3a1 1 0 0 1 1-1h14a1 1 0 0 1 1 1v18a1 1 0 0 1-1 1H5a1 1 0 0 1-1-1z" stroke="#878787" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" fill="none"/>
                                <path d="M8 10h8M8 14h8M8 18h5" stroke="#878787" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                        </span>
                        <span class="submenu-text">Data Proyek</span>
                    </a>
                </li>
                <li>
                    <a href="" class="submenu-item">
                        <span class="submenu-icon">
                            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M21 14H3a1 1 0 0 1-1-1V5a1 1 0 0 1 1-1h18a1 1 0 0 1 1 1v8a1 1 0 0 1-1 1z" stroke="#878787" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" fill="none"/>
                                <path d="M22 14v4a1 1 0 0 1-1 1H3a1 1 0 0 1-1-1v-4" stroke="#878787" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                <path d="M12 10V6m0 4l3-2m-3 2L9 8" stroke="#878787" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                        </span>
                        <span class="submenu-text">Laporan Keuangan</span>
                    </a>
                </li>
            </ul>
        </li>
        <li>
            <a href="">
                <span class="icon">
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <g id="Iconly/Bulk/Setting">
                        <g id="Setting">
                        <path id="Path" d="M12.012 14.8301C10.4075 14.8301 9.10962 13.5801 9.10962 12.0101C9.10962 10.4401 10.4075 9.1801 12.012 9.1801C13.6165 9.1801 14.8837 10.4401 14.8837 12.0101C14.8837 13.5801 13.6165 14.8301 12.012 14.8301Z" fill="#878787"/>
                        <path id="Path_2" opacity="0.4" d="M21.23 14.3701C21.0358 14.0701 20.7599 13.7701 20.4022 13.5801C20.1161 13.4401 19.9321 13.2101 19.7686 12.9401C19.2474 12.0801 19.554 10.9501 20.4227 10.4401C21.4446 9.8701 21.7716 8.6001 21.1789 7.6101L20.4942 6.4301C19.9117 5.4401 18.6342 5.0901 17.6225 5.6701C16.7232 6.1501 15.5684 5.8301 15.0472 4.9801C14.8837 4.7001 14.7917 4.4001 14.8121 4.1001C14.8428 3.7101 14.7201 3.3401 14.5362 3.0401C14.1581 2.4201 13.4734 2.0001 12.7171 2.0001H11.2761C10.5301 2.0201 9.8454 2.4201 9.46728 3.0401C9.27311 3.3401 9.16069 3.7101 9.18113 4.1001C9.20157 4.4001 9.10959 4.7001 8.94608 4.9801C8.42488 5.8301 7.27007 6.1501 6.38097 5.6701C5.35901 5.0901 4.09178 5.4401 3.49905 6.4301L2.81434 7.6101C2.23182 8.6001 2.55885 9.8701 3.57059 10.4401C4.43925 10.9501 4.74584 12.0801 4.23486 12.9401C4.06112 13.2101 3.87717 13.4401 3.59102 13.5801C3.24356 13.7701 2.93697 14.0701 2.77346 14.3701C2.39533 14.9901 2.41577 15.7701 2.7939 16.4201L3.49905 17.6201C3.87717 18.2601 4.58232 18.6601 5.31813 18.6601C5.6656 18.6601 6.07438 18.5601 6.40141 18.3601C6.6569 18.1901 6.96348 18.1301 7.30073 18.1301C8.31247 18.1301 9.16069 18.9601 9.18113 19.9501C9.18113 21.1001 10.1213 22.0001 11.3068 22.0001H12.6967C13.8719 22.0001 14.8121 21.1001 14.8121 19.9501C14.8428 18.9601 15.691 18.1301 16.7027 18.1301C17.0298 18.1301 17.3364 18.1901 17.6021 18.3601C17.9291 18.5601 18.3277 18.6601 18.6853 18.6601C19.4109 18.6601 20.1161 18.2601 20.4942 17.6201L21.2096 16.4201C21.5775 15.7501 21.6081 14.9901 21.23 14.3701Z" fill="#878787"/>
                        </g>
                        </g>
                    </svg>
                </span>
                <span class="menu-text">Pengaturan</span>
            </a>
        </li>
        <li>
            <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                @csrf
            </form>
            <a href="javascript:void(0)" onclick="event.preventDefault(); document.getElementById('logout-form').submit();" class="{{ request()->routeIs('logout') ? 'active' : '' }}">
                <span class="icon">
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <g id="Iconly/Bulk/Login">
                        <g id="Login">
                        <path id="Fill 1" opacity="0.4" d="M7.2962 6.446C7.2962 3.995 9.356 2 11.8876 2H16.9199C19.4454 2 21.5 3.99 21.5 6.436V17.552C21.5 20.004 19.4413 22 16.9096 22H11.8773C9.35187 22 7.2962 20.009 7.2962 17.562V16.622V6.446Z" fill="#878787"/>
                        <path id="Fill 4" d="M16.0374 11.4538L13.0695 8.54479C12.7627 8.24479 12.2691 8.24479 11.9634 8.54679C11.6587 8.84879 11.6597 9.33579 11.9654 9.63579L13.5905 11.2288H3.2821C2.85042 11.2288 2.5 11.5738 2.5 11.9998C2.5 12.4248 2.85042 12.7688 3.2821 12.7688H13.5905L11.9654 14.3628C11.6597 14.6628 11.6587 15.1498 11.9634 15.4518C12.1168 15.6028 12.3168 15.6788 12.518 15.6788C12.717 15.6788 12.9171 15.6028 13.0695 15.4538L16.0374 12.5448C16.1847 12.3998 16.268 12.2038 16.268 11.9998C16.268 11.7948 16.1847 11.5988 16.0374 11.4538Z" fill="#878787"/>
                        </g>
                        </g>
                    </svg>
                </span>
                <span class="menu-text">Keluar</span>
            </a>
        </li>
    </ul>
</div>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Toggle untuk membuka/menutup submenu
    const projectMenu = document.getElementById('projectMenu');
    const mainMenuItem = document.querySelector('.main-menu-item');
    
    if (projectMenu && mainMenuItem) {
        // Tambahkan event listener untuk menu utama
        mainMenuItem.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            toggleSubmenu(projectMenu);
        });
        
        // Tambahkan event listener untuk submenu items
        const submenuItems = projectMenu.querySelectorAll('.submenu-item');
        submenuItems.forEach(function(item) {
            // Biarkan submenu item bisa diklik untuk navigasi
            item.addEventListener('click', function(e) {
                e.stopPropagation();
                // Pastikan menu tetap terbuka saat mengklik item submenu
                if (!projectMenu.classList.contains('open')) {
                    projectMenu.classList.add('open');
                }
            });
        });
    }
    
    // Toggle sidebar pada tampilan mobile
    const sidebar = document.querySelector('.sidebar');
    document.body.addEventListener('click', function(e) {
        // Tutup sidebar jika klik di luar sidebar
        if (window.innerWidth <= 992 && sidebar.classList.contains('expanded') && !sidebar.contains(e.target)) {
            sidebar.classList.remove('expanded');
        }
    });
    
    // Toggle sidebar dengan tombol hamburger (jika ada)
    const toggleBtn = document.querySelector('.sidebar-toggle');
    if (toggleBtn) {
        toggleBtn.addEventListener('click', function() {
            sidebar.classList.toggle('expanded');
        });
    }
    
    // Auto-expand submenu jika halaman saat ini adalah submenu item
    const activeSubmenuItem = document.querySelector('.submenu-item.active');
    if (activeSubmenuItem) {
        const parentMenu = activeSubmenuItem.closest('.has-submenu');
        if (parentMenu) {
            parentMenu.classList.add('open');
        }
    }
    
    // Auto-expand submenu berdasarkan URL
    if (window.location.href.includes('getDataProyek')) {
        const projectMenu = document.getElementById('projectMenu');
        if (projectMenu) {
            projectMenu.classList.add('open');
        }
    }
    
    // Responsive handling
    function handleResponsive() {
        const isMobile = window.matchMedia('(max-width: 992px)').matches;
        
        // Pada mode mobile, tambahkan toggle sidebar saat klik ikon
        if (isMobile) {
            const icons = document.querySelectorAll('.sidebar-menu > li > a > .icon');
            icons.forEach(icon => {
                icon.addEventListener('click', function(e) {
                    if (!sidebar.classList.contains('expanded')) {
                        e.preventDefault();
                        e.stopPropagation();
                        sidebar.classList.add('expanded');
                    }
                });
            });
        }
    }
    
    // Panggil saat halaman dimuat
    handleResponsive();
    
    // Fungsi toggle submenu
    function toggleSubmenu(menu) {
        menu.classList.toggle('open');
    }
    
    // Tambahkan tombol toggle sidebar jika belum ada
    if (!document.querySelector('.sidebar-toggle') && window.matchMedia('(max-width: 992px)').matches) {
        const toggleBtn = document.createElement('button');
        toggleBtn.className = 'sidebar-toggle';
        toggleBtn.innerHTML = `
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M3 12h18M3 6h18M3 18h18" stroke="#333" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
        `;
        document.body.appendChild(toggleBtn);
        
        toggleBtn.addEventListener('click', function() {
            sidebar.classList.toggle('expanded');
        });
    }
    
    // CSS tambahan untuk tombol toggle
    const style = document.createElement('style');
    style.textContent = `
        .sidebar-toggle {
            position: fixed;
            top: 15px;
            left: 15px;
            z-index: 1001;
            background-color: white;
            border: none;
            border-radius: 4px;
            padding: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            display: none;
        }
        
        @media (max-width: 992px) {
            .sidebar-toggle {
                display: block;
            }
            
            .content-wrapper {
                margin-left: 60px;
            }
            
            .sidebar.expanded + .content-wrapper {
                margin-left: 240px;
            }
        }
        
        @media (max-width: 576px) {
            .content-wrapper {
                margin-left: 50px;
            }
            
            .sidebar.expanded + .content-wrapper {
                margin-left: 220px;
            }
        }
    `;
    document.head.appendChild(style);
    
    // Tambahkan event untuk menutup submenu saat klik di luar
    document.addEventListener('click', function(e) {
        // Periksa apakah klik di luar submenu dan menu proyek
        if (
            !e.target.closest('.has-submenu') && 
            !e.target.closest('.submenu') && 
            !e.target.closest('.main-menu-item')
        ) {
            // Tutup semua submenu
            const submenus = document.querySelectorAll('.has-submenu');
            submenus.forEach(submenu => {
                if (submenu.classList.contains('open')) {
                    // Jangan tutup jika menu saat ini aktif
                    const hasActiveItem = submenu.querySelector('.submenu-item.active');
                    if (!hasActiveItem) {
                        submenu.classList.remove('open');
                    }
                }
            });
        }
    });
    
    // Tambahkan hover event untuk menampilkan teks pada mode mobile
    if (window.matchMedia('(max-width: 992px)').matches) {
        sidebar.addEventListener('mouseenter', function() {
            this.classList.add('expanded');
        });
        
        sidebar.addEventListener('mouseleave', function() {
            // Jangan tutup jika ada interaksi yang sedang berlangsung
            if (!this.querySelector('.has-submenu.open')) {
                this.classList.remove('expanded');
            }
        });
    }
    
    // Ketika window resize, update UI
    window.addEventListener('resize', function() {
        handleResponsive();
        
        // Reset expanded class pada sidebar jika layar menjadi besar
        if (window.innerWidth > 992) {
            sidebar.classList.remove('expanded');
        }
    });
});
</script>