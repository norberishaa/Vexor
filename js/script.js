// hamburger menu per nav bar
const hamburger = document.getElementById('hamburger');
const navLinks = document.getElementById('navLinks');

hamburger.addEventListener('click', () => {
    hamburger.classList.toggle('active');
    navLinks.classList.toggle('active');
});

// Close menu when clicking on a link (mobile)
const links = navLinks.querySelectorAll('a');
links.forEach(link => {
    link.addEventListener('click', () => {
        hamburger.classList.remove('active');
        navLinks.classList.remove('active');
    });
});

// Close menu when clicking outside (mobile)
document.addEventListener('click', (e) => {
    if (!hamburger.contains(e.target) && !navLinks.contains(e.target)) {
        hamburger.classList.remove('active');
        navLinks.classList.remove('active');
    }
});

// Prevent body scroll when menu is open on mobile
const observer = new MutationObserver((mutations) => {
    mutations.forEach((mutation) => {
        if (mutation.attributeName === 'class') {
            if (navLinks.classList.contains('active') && window.innerWidth <= 768) {
                document.body.style.overflow = 'hidden';
            } else {
                document.body.style.overflow = '';
            }
        }
    });
});

observer.observe(navLinks, {
    attributes: true
});


document.querySelectorAll('.cve-table tbody tr').forEach(row => {
    row.addEventListener('click', () => {
        const cveId = row.dataset.cve;
        window.location.href = `dashboard.php?cve=${encodeURIComponent(cveId)}`;
    });
});

const wrapper = document.querySelector('.cve-description-wrapper');
if (wrapper) {
    wrapper.addEventListener('click', () => {
        window.location.href = 'dashboard.php';
    });

    // prevent close when clicking inside
    const container = wrapper.querySelector('.cve-description-container');
    container.addEventListener('click', e => e.stopPropagation());
}