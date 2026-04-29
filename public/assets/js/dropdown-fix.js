document.addEventListener("DOMContentLoaded", function () {
    // Tutup dropdown saat pindah halaman (saat link di-klik)
    document.querySelectorAll('a:not([data-bs-toggle="dropdown"])').forEach(link => {
        link.addEventListener('click', () => {
            const openDropdowns = document.querySelectorAll('.dropdown-menu.show');
            openDropdowns.forEach(menu => {
                const trigger = menu.closest('.dropdown').querySelector('[data-bs-toggle="dropdown"]');
                if (trigger) {
                    let bsDropdown = bootstrap.Dropdown.getInstance(trigger);
                    if(bsDropdown) bsDropdown.hide();
                }
            });
        });
    });

    // Atasi masalah bfcache (browser cache saat tekan tombol back)
    window.addEventListener('pageshow', function (event) {
        if (event.persisted) {
            document.querySelectorAll('.dropdown-menu.show').forEach(menu => {
                menu.classList.remove('show');
            });
            document.querySelectorAll('.dropdown-toggle.show, [data-bs-toggle="dropdown"].show').forEach(trigger => {
                trigger.classList.remove('show');
                trigger.setAttribute('aria-expanded', 'false');
            });
        }
    });
});
