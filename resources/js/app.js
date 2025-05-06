import './bootstrap';
import './Koordinator/data_mitra';
import './Koordinator/data_dosen';
import './Koordinator/data_profesional';
import './Koordinator/data_mahasiswa';
import './Koordinator/data_proyek';
import './Koordinator/detail_data_proyek';

document.addEventListener('DOMContentLoaded', function() {
    const currentPath = window.location.pathname;
    const sidebarLinks = document.querySelectorAll('.sidebar ul li a');
    
    sidebarLinks.forEach(link => {
        if (link.getAttribute('href') === currentPath) {
            link.classList.add('active');
            link.style.color = '#3C21F7';
            link.style.backgroundColor = 'rgba(60, 33, 247, 0.05)';
        }
    });
});

document.querySelectorAll('.has-submenu > a').forEach(item => {
    item.addEventListener('click', e => {
        e.preventDefault();
        const parent = item.parentElement;
        parent.classList.toggle('open');
    });
});


