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

// -------------------------------------- CVE TABLE --------------------------------------

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

    const container = wrapper.querySelector('.cve-description-container');
    container.addEventListener('click', e => e.stopPropagation());
}


document.addEventListener('DOMContentLoaded', () => {
    const searchInput = document.getElementById('cveSearch');
    
    if (searchInput) {
        searchInput.addEventListener('keyup', function() {
            let filter = this.value.toLowerCase();
            let rows = document.querySelectorAll('.cve-table tbody tr');

            rows.forEach(row => {
                if (row.cells.length > 1) {
                    let cveId = row.cells[0].textContent.toLowerCase();
                    let name = row.cells[1].textContent.toLowerCase();

                    if (cveId.includes(filter) || name.includes(filter)) {
                        row.style.display = "table-row"; 
                    } else {
                        row.style.display = "none";
                    }
                }
            });
        });
    } else {
        console.error("Search input with ID 'cveSearch' not found!");
    }
});