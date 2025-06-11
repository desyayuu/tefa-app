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
            <a href="{{ route('profesional.dashboard') }}" class="{{ request()->routeIs('profesional.dashboard') ? 'active' : '' }}">
                <span class="icon">
                    <svg width="12" height="12" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M7.14373 18.7821V15.7152C7.14372 14.9381 7.77567 14.3067 8.55844 14.3018H11.4326C12.2189 14.3018 12.8563 14.9346 12.8563 15.7152V18.7732C12.8562 19.4473 13.404 19.9951 14.0829 20H16.0438C16.9596 20.0023 17.8388 19.6428 18.4872 19.0007C19.1356 18.3586 19.5 17.4868 19.5 16.5775V7.86585C19.5 7.13139 19.1721 6.43471 18.6046 5.9635L11.943 0.674268C10.7785 -0.250877 9.11537 -0.220992 7.98539 0.745384L1.46701 5.9635C0.872741 6.42082 0.517552 7.11956 0.5 7.86585V16.5686C0.5 18.4637 2.04738 20 3.95617 20H5.87229C6.19917 20.0023 6.51349 19.8751 6.74547 19.6464C6.97746 19.4178 7.10793 19.1067 7.10792 18.7821H7.14373Z" fill="#878787"/>
                    </svg>
                </span>
                <span class="menu-text">Dashboard</span>
            </a>
        </li>
        <li class="has-submenu" id="dataKeuanganMenu">
            <a href="javascript:void(0);" class="main-menu-item">
                <span class="icon">
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path opacity="0.4" d="M16.6756 2H7.33333C3.92889 2 2 3.92889 2 7.33333V16.6667C2 20.0711 3.92889 22 7.33333 22H16.6756C20.08 22 22 20.0711 22 16.6667V7.33333C22 3.92889 20.08 2 16.6756 2Z" fill="#878787"/>
                        <path d="M7.36866 9.36914C6.91533 9.36914 6.54199 9.74247 6.54199 10.2047V17.0758C6.54199 17.5291 6.91533 17.9025 7.36866 17.9025C7.83088 17.9025 8.20421 17.5291 8.20421 17.0758V10.2047C8.20421 9.74247 7.83088 9.36914 7.36866 9.36914Z" fill="#878787"/>
                        <path d="M12.0354 6.08984C11.5821 6.08984 11.2087 6.46318 11.2087 6.9254V17.0765C11.2087 17.5298 11.5821 17.9032 12.0354 17.9032C12.4976 17.9032 12.871 17.5298 12.871 17.0765V6.9254C12.871 6.46318 12.4976 6.08984 12.0354 6.08984Z" fill="#878787"/>
                        <path d="M16.64 12.9961C16.1778 12.9961 15.8044 13.3694 15.8044 13.8316V17.0761C15.8044 17.5294 16.1778 17.9028 16.6311 17.9028C17.0933 17.9028 17.4667 17.5294 17.4667 17.0761V13.8316C17.4667 13.3694 17.0933 12.9961 16.64 12.9961Z" fill="#878787"/>
                    </svg>
                </span>
                <span class="menu-text {{ request()->routeIs('profesional.dataProyek') ? 'active' : '' }}">Data Proyek</span>
                <span class="submenu-arrow">
                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none">
                        <path d="M19 9l-7 7-7-7" stroke="#878787" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                </span>
            </a>
            <ul class="submenu">
                <li>
                    <a href="{{ route('profesional.dataProyek') }}" class="submenu-item {{ request()->routeIs('profesional.dataProyek') || request()->routeIs('profesional.detailProyek')? 'active' : '' }}">
                        <span class="submenu-text">Proyek</span>
                    </a>
                </li>
                <li class="has-nested-submenu">
                    <a href="javascript:void(0);" class="submenu-item nested-menu-toggle">
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
    const dataKeuanganMenu = document.getElementById('dataKeuanganMenu');
    
    if (dataKeuanganMenu) {
        // Tambahkan hover event untuk menu utama
        dataKeuanganMenu.addEventListener('mouseenter', function() {
            this.classList.add('open');
        });
        
        // Toggle submenu ketika menu utama diklik (untuk mobile)
        const mainMenuItem = dataKeuanganMenu.querySelector('.main-menu-item');
        if (mainMenuItem) {
            mainMenuItem.addEventListener('click', function(e) {
                e.preventDefault();
                e.stopPropagation();
                toggleSubmenu(dataKeuanganMenu);
            });
        }
        
        // Event listener khusus untuk nested submenu toggle
        const nestedMenuToggle = dataKeuanganMenu.querySelector('.nested-menu-toggle');
        if (nestedMenuToggle) {
            nestedMenuToggle.addEventListener('click', function(e) {
                e.preventDefault();
                e.stopPropagation();
                
                // Pastikan parent menu tetap terbuka
                dataKeuanganMenu.classList.add('open');
                
                // Toggle nested submenu
                const parentLi = this.closest('.has-nested-submenu');
                if (parentLi) {
                    parentLi.classList.toggle('open');
                }
            });
        }
        
        // Tambahkan event listener untuk submenu items
        const submenuItems = dataKeuanganMenu.querySelectorAll('.submenu-item:not(.nested-menu-toggle)');
        submenuItems.forEach(function(item) {
            item.addEventListener('click', function(e) {
                e.stopPropagation();
                // Pastikan menu tetap terbuka saat mengklik item submenu
                dataKeuanganMenu.classList.add('open');
            });
        });
        
        // Khusus untuk nested menu
        const nestedMenuParent = dataKeuanganMenu.querySelector('.has-nested-submenu');
        if (nestedMenuParent) {
            // Ketika hover di Keuangan Proyek
            nestedMenuParent.addEventListener('mouseenter', function() {
                this.classList.add('open');
                // Pastikan parent menu juga terbuka
                dataKeuanganMenu.classList.add('open');
            });
            
            // Ketika meninggalkan Keuangan Proyek tapi tidak ke submenu-nya
            nestedMenuParent.addEventListener('mouseleave', function(e) {
                const nestedSubmenu = this.querySelector('.nested-submenu');
                if (nestedSubmenu) {
                    const rect = nestedSubmenu.getBoundingClientRect();
                    // Cek apakah cursor masih di dalam nested submenu
                    if (
                        e.clientX < rect.left || 
                        e.clientX > rect.right || 
                        e.clientY < rect.top || 
                        e.clientY > rect.bottom
                    ) {
                        // Hanya tutup nested submenu jika bukan mengarah ke submenu-nya
                        if (!nestedSubmenu.contains(e.relatedTarget)) {
                            // Cek apakah ada item aktif di dalam nested submenu
                            const hasActiveNestedItem = this.querySelector('.nested-submenu-item.active');
                            if (!hasActiveNestedItem) {
                                this.classList.remove('open');
                            }
                        }
                    }
                }
            });
        }
        
        // Event khusus untuk nested submenu items
        const nestedSubmenuItems = document.querySelectorAll('.nested-submenu-item');
        nestedSubmenuItems.forEach(function(item) {
            item.addEventListener('click', function(e) {
                e.stopPropagation();
                
                // Pastikan seluruh hierarki menu tetap terbuka
                const parentLi = this.closest('.has-nested-submenu');
                if (parentLi) {
                    parentLi.classList.add('open');
                }
                
                const mainMenu = this.closest('.has-submenu');
                if (mainMenu) {
                    mainMenu.classList.add('open');
                }
            });
        });
        
        // Tambahkan event listener untuk menutup submenu saat klik di luar
        document.addEventListener('click', function(e) {
            // Periksa apakah klik di luar submenu
            if (
                !e.target.closest('.has-submenu') && 
                !e.target.closest('.submenu') && 
                !e.target.closest('.main-menu-item') &&
                !e.target.closest('.nested-submenu-item') &&
                !e.target.closest('.nested-menu-toggle')
            ) {
                // Tutup semua submenu jika tidak ada item aktif
                if (!dataKeuanganMenu.querySelector('.submenu-item.active') && 
                    !dataKeuanganMenu.querySelector('.nested-submenu-item.active')) {
                    dataKeuanganMenu.classList.remove('open');
                    
                    // Tutup juga semua nested submenu
                    const nestedSubmenus = dataKeuanganMenu.querySelectorAll('.has-nested-submenu');
                    nestedSubmenus.forEach(function(menu) {
                        if (!menu.querySelector('.nested-submenu-item.active')) {
                            menu.classList.remove('open');
                        }
                    });
                }
            }
        });
    }
    
    // Auto-expand submenu jika halaman saat ini adalah submenu item
    const activeSubmenuItem = document.querySelector('.submenu-item.active');
    if (activeSubmenuItem) {
        const parentMenu = activeSubmenuItem.closest('.has-submenu');
        if (parentMenu) {
            parentMenu.classList.add('open');
        }
        
        // Jika active item adalah nested submenu item
        if (activeSubmenuItem.closest('.has-nested-submenu')) {
            activeSubmenuItem.closest('.has-nested-submenu').classList.add('open');
        }
    }
    
    // Auto-expand nested submenu jika halaman saat ini adalah nested submenu item
    const activeNestedItem = document.querySelector('.nested-submenu-item.active');
    if (activeNestedItem) {
        const parentNestedMenu = activeNestedItem.closest('.has-nested-submenu');
        if (parentNestedMenu) {
            parentNestedMenu.classList.add('open');
            
            // Juga buka parent menu utama
            const mainParentMenu = parentNestedMenu.closest('.has-submenu');
            if (mainParentMenu) {
                mainParentMenu.classList.add('open');
            }
        }
    }
    
    // Fungsi toggle submenu
    function toggleSubmenu(menu) {
        menu.classList.toggle('open');
        
        // Jika menu utama ditutup, tutup juga semua nested submenu
        if (!menu.classList.contains('open')) {
            const nestedMenus = menu.querySelectorAll('.has-nested-submenu');
            nestedMenus.forEach(function(nestedMenu) {
                nestedMenu.classList.remove('open');
            });
        }
    }
    
    // CSS untuk memastikan nested submenu tetap terbuka ketika diklik
    const style = document.createElement('style');
    style.textContent = `
        /* Forcefully keep submenu open when child is toggled */
        .nested-menu-toggle:focus + .nested-submenu,
        .nested-menu-toggle:active + .nested-submenu {
            display: block !important;
        }
        
        /* Ensure parent menu stays open when nested submenu is active */
        .has-submenu.open .has-nested-submenu.open .nested-submenu {
            display: block !important;
        }
        
        /* Make nested menu toggle more clickable */
        .nested-menu-toggle {
            cursor: pointer;
            display: block;
            width: 100%;
        }
    `;
    document.head.appendChild(style);
});
</script>