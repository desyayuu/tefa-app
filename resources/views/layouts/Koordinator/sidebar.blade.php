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
            <a href="{{ route('koordinator.dashboard') }}" class="{{ request()->routeIs('koordinator.dashboard') ? 'active' : '' }}">
                <span class="icon">
                    <svg width="12" height="12" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M7.14373 18.7821V15.7152C7.14372 14.9381 7.77567 14.3067 8.55844 14.3018H11.4326C12.2189 14.3018 12.8563 14.9346 12.8563 15.7152V18.7732C12.8562 19.4473 13.404 19.9951 14.0829 20H16.0438C16.9596 20.0023 17.8388 19.6428 18.4872 19.0007C19.1356 18.3586 19.5 17.4868 19.5 16.5775V7.86585C19.5 7.13139 19.1721 6.43471 18.6046 5.9635L11.943 0.674268C10.7785 -0.250877 9.11537 -0.220992 7.98539 0.745384L1.46701 5.9635C0.872741 6.42082 0.517552 7.11956 0.5 7.86585V16.5686C0.5 18.4637 2.04738 20 3.95617 20H5.87229C6.19917 20.0023 6.51349 19.8751 6.74547 19.6464C6.97746 19.4178 7.10793 19.1067 7.10792 18.7821H7.14373Z" fill="#878787"/>
                    </svg>
                </span>
                <span class="menu-text">Dashboard</span>
            </a>
        </li>
        <li>
            <a href="{{ route('koordinator.dataProyek') }}" class="{{ request()->routeIs('koordinator.dataProyek') || request()->routeIs('koordinator.detailDataProyek') ? 'active' : ''}} ">
                <span class="icon">
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path fill-rule="evenodd" clip-rule="evenodd" d="M15.2428 4.73756C15.2428 6.95855 17.0459 8.75902 19.2702 8.75902C19.5151 8.75782 19.7594 8.73431 20 8.68878V16.6615C20 20.0156 18.0215 22 14.6624 22H7.34636C3.97851 22 2 20.0156 2 16.6615V9.3561C2 6.00195 3.97851 4 7.34636 4H15.3131C15.2659 4.243 15.2423 4.49001 15.2428 4.73756ZM13.15 14.8966L16.0078 11.2088V11.1912C16.2525 10.8625 16.1901 10.3989 15.8671 10.1463C15.7108 10.0257 15.5122 9.97345 15.3167 10.0016C15.1211 10.0297 14.9453 10.1358 14.8295 10.2956L12.4201 13.3951L9.6766 11.2351C9.51997 11.1131 9.32071 11.0592 9.12381 11.0856C8.92691 11.1121 8.74898 11.2166 8.63019 11.3756L5.67562 15.1863C5.57177 15.3158 5.51586 15.4771 5.51734 15.6429C5.5002 15.9781 5.71187 16.2826 6.03238 16.3838C6.35288 16.485 6.70138 16.3573 6.88031 16.0732L9.35125 12.8771L12.0948 15.0283C12.2508 15.1541 12.4514 15.2111 12.6504 15.1863C12.8494 15.1615 13.0297 15.0569 13.15 14.8966Z" fill="#878787"/>
                        <circle opacity="0.4" cx="19.5" cy="4.5" r="2.5" fill="#878787"/>
                    </svg>
                </span>
                <span class="menu-text">Data Proyek</span>
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
                <span class="menu-text">Data Keuangan</span>
                <span class="submenu-arrow">
                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none">
                        <path d="M19 9l-7 7-7-7" stroke="#878787" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                </span>
            </a>
            <ul class="submenu">
                <li>
                    <a href="{{ route('koordinator.dataKeuanganTefa') }}" class="submenu-item {{ request()->routeIs('koordinator.dataKeuanganTefa') ? 'active' : '' }}">
                        <span class="submenu-text">Keuangan TEFA</span>
                    </a>
                </li>
                <li class="has-nested-submenu">
                    <a href="javascript:void(0);" class="submenu-item nested-menu-toggle">
                        <span class="submenu-text">Keuangan Proyek</span>
                        <span class="nested-submenu-arrow" style="margin-left: 7px;">
                            <svg width="10" height="10" viewBox="0 0 24 24" fill="none">
                                <path d="M19 9l-7 7-7-7" stroke="#878787" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                        </span>
                    </a>
                    <ul class="nested-submenu">
                        <li>
                            <a href="{{ route('koordinator.dataMasukKeuanganProyek') }}" style="margin-left: 40px;" class="nested-submenu-item {{ request()->routeIs('koordinator.dataMasukKeuanganProyek') || request()->routeIs('koordinator.detailDataMasukKeuanganProyek') ? 'active' : '' }}">
                                <span class="nested-submenu-icon">
                                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M12 20V4m0 16l-6-6m6 6l6-6" stroke="#878787" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                    </svg>
                                </span>
                                <span class="nested-submenu-text" style="margin-left: 10px">Dana Masuk</span>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('koordinator.dataKeluarKeuanganProyek')}}" class="nested-submenu-item {{ request()->routeIs('koordinator.dataKeluarKeuanganProyek') || request()->routeIs('koordinator.detailDataKeluarKeuanganProyek') ? 'active' : '' }}" style="margin-left: 40px;">
                                <span class="nested-submenu-icon">
                                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M12 4v16m0-16l-6 6m6-6l6 6" stroke="#878787" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                    </svg>
                                </span>
                                <span class="nested-submenu-text" style="margin-left: 10px">Dana Keluar</span>
                            </a>
                        </li>
                    </ul>
                </li>
            </ul>
        </li>
        <li>
            <a href="{{ route('koordinator.dataUser') }}" class="{{ request()->routeIs('koordinator.dataUser') ? 'active' : '' }}">
                <span class="icon">
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M11.997 15.1743C7.684 15.1743 4 15.8543 4 18.5743C4 21.2953 7.661 21.9993 11.997 21.9993C16.31 21.9993 19.994 21.3203 19.994 18.5993C19.994 15.8783 16.334 15.1743 11.997 15.1743Z" fill="#878787"/>
                        <path opacity="0.4" d="M11.9971 12.5835C14.9351 12.5835 17.2891 10.2285 17.2891 7.29151C17.2891 4.35451 14.9351 1.99951 11.9971 1.99951C9.06008 1.99951 6.70508 4.35451 6.70508 7.29151C6.70508 10.2285 9.06008 12.5835 11.9971 12.5835Z" fill="#878787"/>
                    </svg>
                </span>
                <span class="menu-text">Data User</span>
            </a>
        </li>
        <li>
            <a href="{{ route('koordinator.dataDosen') }}" 
                class="{{ request()->routeIs('koordinator.dataDosen') || request()->routeIs('koordinator.detailDataDosen')  ? 'active' : '' }}">
                <span class="icon">
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M11.997 15.1743C7.684 15.1743 4 15.8543 4 18.5743C4 21.2953 7.661 21.9993 11.997 21.9993C16.31 21.9993 19.994 21.3203 19.994 18.5993C19.994 15.8783 16.334 15.1743 11.997 15.1743Z" fill="#878787"/>
                        <path opacity="0.4" d="M11.9971 12.5835C14.9351 12.5835 17.2891 10.2285 17.2891 7.29151C17.2891 4.35451 14.9351 1.99951 11.9971 1.99951C9.06008 1.99951 6.70508 4.35451 6.70508 7.29151C6.70508 10.2285 9.06008 12.5835 11.9971 12.5835Z" fill="#878787"/>
                    </svg>
                </span>
                <span class="menu-text">Data Dosen</span>
            </a>
        </li>
        <li>
            <a href="{{ route('koordinator.dataProfesional') }}" 
                class="{{ request()->routeIs('koordinator.dataProfesional') || request()->routeIs('koordinator.detailDataProfesional')  ? 'active' : '' }}" >
                <span class="icon">
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M11.997 15.1743C7.684 15.1743 4 15.8543 4 18.5743C4 21.2953 7.661 21.9993 11.997 21.9993C16.31 21.9993 19.994 21.3203 19.994 18.5993C19.994 15.8783 16.334 15.1743 11.997 15.1743Z" fill="#878787"/>
                        <path opacity="0.4" d="M11.9971 12.5835C14.9351 12.5835 17.2891 10.2285 17.2891 7.29151C17.2891 4.35451 14.9351 1.99951 11.9971 1.99951C9.06008 1.99951 6.70508 4.35451 6.70508 7.29151C6.70508 10.2285 9.06008 12.5835 11.9971 12.5835Z" fill="#878787"/>
                    </svg>
                </span>
                <span class="menu-text">Data Profesional</span>
            </a>
        </li>
        <li>
            <a href="{{ route('koordinator.dataMahasiswa') }}" 
                class="{{ request()->routeIs('koordinator.dataMahasiswa') || request()->routeIs('koordinator.detailDataMahasiswa') ? 'active' : '' }}" >
                <span class="icon">
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M11.997 15.1743C7.684 15.1743 4 15.8543 4 18.5743C4 21.2953 7.661 21.9993 11.997 21.9993C16.31 21.9993 19.994 21.3203 19.994 18.5993C19.994 15.8783 16.334 15.1743 11.997 15.1743Z" fill="#878787"/>
                        <path opacity="0.4" d="M11.9971 12.5835C14.9351 12.5835 17.2891 10.2285 17.2891 7.29151C17.2891 4.35451 14.9351 1.99951 11.9971 1.99951C9.06008 1.99951 6.70508 4.35451 6.70508 7.29151C6.70508 10.2285 9.06008 12.5835 11.9971 12.5835Z" fill="#878787"/>
                    </svg>
                </span>
                <span class="menu-text">Data Mahasiswa</span>
            </a>
        </li>
        <li>
            <a href="{{ route('koordinator.dataMitra') }}" class="{{ request()->routeIs('koordinator.dataMitra') ? 'active' : '' }}">
                <span class="icon">
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M16.9967 15.1743C13.6837 15.1743 11.0007 15.8543 11.0007 18.5743C11.0007 21.2953 13.6617 21.9993 16.9967 21.9993C20.3097 21.9993 22.9937 21.3203 22.9937 18.5993C22.9937 15.8783 20.3337 15.1743 16.9967 15.1743Z" fill="#878787"/>
                        <path opacity="0.4" d="M16.9971 12.5835C19.2851 12.5835 21.1381 10.7285 21.1381 8.44051C21.1381 6.15251 19.2851 4.29951 16.9971 4.29951C14.7091 4.29951 12.8561 6.15251 12.8561 8.44051C12.8561 10.7285 14.7091 12.5835 16.9971 12.5835Z" fill="#878787"/>
                        <path d="M6.997 15.1743C3.684 15.1743 1 15.8543 1 18.5743C1 21.2953 3.661 21.9993 6.997 21.9993C10.31 21.9993 12.994 21.3203 12.994 18.5993C12.994 15.8783 10.334 15.1743 6.997 15.1743Z" fill="#878787"/>
                        <path opacity="0.4" d="M6.9971 12.5835C9.2851 12.5835 11.1381 10.7285 11.1381 8.44051C11.1381 6.15251 9.2851 4.29951 6.9971 4.29951C4.7091 4.29951 2.8561 6.15251 2.8561 8.44051C2.8561 10.7285 4.7091 12.5835 6.9971 12.5835Z" fill="#878787"/>
                    </svg>
                </span>
                <span class="menu-text">Data Mitra</span>
            </a>
        </li>
        <li>
            <a href="{{ route('koordinator.getSubKategoriTransaksi') }}" class="{{ request()->routeIs('koordinator.getSubKategoriTransaksi') ? 'active' : '' }}">
                <span class="icon">
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <g id="Iconly/Bulk/Bookmark">
                        <g id="Bookmark">
                        <path id="Bookmark 2" opacity="0.4" d="M11.9912 18.6215L5.49945 21.8641C5.00921 22.1302 4.39768 21.9525 4.12348 21.4643C4.0434 21.3108 4.00106 21.1402 4 20.9668V13.7088C4 14.4284 4.40573 14.8726 5.47299 15.3701L11.9912 18.6215Z" fill="#878787"/>
                        <path id="Bookmark_2" fill-rule="evenodd" clip-rule="evenodd" d="M8.89526 2H15.0695C17.7773 2 19.9735 3.06605 20 5.79337V20.9668C19.9989 21.1374 19.9565 21.3051 19.8765 21.4554C19.7479 21.7007 19.5259 21.8827 19.2615 21.9598C18.997 22.0368 18.7128 22.0023 18.4741 21.8641L11.9912 18.6215L5.47299 15.3701C4.40573 14.8726 4 14.4284 4 13.7088V5.79337C4 3.06605 6.19625 2 8.89526 2ZM8.22492 9.62227H15.7486C16.1822 9.62227 16.5336 9.26828 16.5336 8.83162C16.5336 8.39495 16.1822 8.04096 15.7486 8.04096H8.22492C7.79137 8.04096 7.43991 8.39495 7.43991 8.83162C7.43991 9.26828 7.79137 9.62227 8.22492 9.62227Z" fill="#878787"/>
                        </g>
                        </g>
                    </svg>
                </span>
                <span class="menu-text">Data Kategori Transaksi</span>
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