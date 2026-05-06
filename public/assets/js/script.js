/* ============================================================
   CUSTOM JAVASCRIPT — POS App
   ============================================================
   Tambahkan custom JS di sini.
   Bootstrap 5 JS sudah di-load via CDN di layout.
   ============================================================ */

// Auto-dismiss alert setelah 5 detik
document.addEventListener('DOMContentLoaded', function() {
    const alerts = document.querySelectorAll('.alert-dismissible');
    alerts.forEach(function(alert) {
        setTimeout(function() {
            const bsAlert = bootstrap.Alert.getOrCreateInstance(alert);
            bsAlert.close();
        }, 5000);
    });
});
