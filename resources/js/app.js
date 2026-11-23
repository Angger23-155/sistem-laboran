import './bootstrap';

document.addEventListener('DOMContentLoaded', function() {
    // Memberikan efek pulse pada badge merah secara otomatis saat halaman dimuat
    const badges = document.querySelectorAll('.badge-danger');
    badges.forEach(badge => {
        badge.classList.add('pulse');
    });

    // Inisialisasi Tooltip AdminLTE
    if (typeof $ !== 'undefined') {
        $('[data-toggle="tooltip"]').tooltip();
    }
});