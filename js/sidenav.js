// Make sure the function is available immediately
document.addEventListener('DOMContentLoaded', function() {
    console.log('DOM loaded, sidenav function ready');
});

// Make the function globally available
window.toggleSidenav = function() {
    console.log('Toggle sidenav called');
    const sidenav = document.getElementById('sidenav-menu');
    console.log('Sidenav element:', sidenav);
    if (sidenav) {
        sidenav.classList.toggle('translate-x-full');
        sidenav.classList.toggle('translate-x-0');
    }
};
