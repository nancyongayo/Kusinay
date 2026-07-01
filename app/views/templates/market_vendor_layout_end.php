</div><!-- .kn-content -->
</main><!-- .kn-main -->

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
// Sidebar toggle for mobile
const toggle = document.getElementById('sidebarToggle');
const sidebar = document.getElementById('sidebar');
const overlay = document.getElementById('sidebarOverlay');
const navItems = document.querySelectorAll('.nav-item');
const body = document.body;

if (toggle && sidebar && overlay) {
    // Toggle sidebar on button click
    toggle.addEventListener('click', (e) => {
        e.stopPropagation();
        sidebar.classList.toggle('open');
        overlay.classList.toggle('open');
    });
    
    // Close sidebar when overlay is clicked
    overlay.addEventListener('click', () => {
        sidebar.classList.remove('open');
        overlay.classList.remove('open');
    });
    
    // Close sidebar when nav item is clicked (mobile only)
    navItems.forEach(item => {
        item.addEventListener('click', () => {
            if (window.innerWidth <= 768) {
                sidebar.classList.remove('open');
                overlay.classList.remove('open');
            }
        });
    });
    
    // Prevent body scroll when sidebar is open on mobile
    const observer = new MutationObserver(() => {
        if (sidebar.classList.contains('open') && window.innerWidth <= 768) {
            body.style.overflow = 'hidden';
        } else {
            body.style.overflow = '';
        }
    });
    observer.observe(sidebar, { attributes: true, attributeFilter: ['class'] });
}
</script>
</body>
</html>
