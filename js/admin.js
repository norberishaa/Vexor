document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('.admin-tab').forEach(adminTab => {
        adminTab.addEventListener('click', () => {

            document.querySelectorAll('.admin-tab').forEach(tab =>
                tab.classList.remove('active')
            );

            adminTab.classList.add('active');

            const tab = adminTab.dataset.admintab;

            window.location.href = `admin.php?tab=${encodeURIComponent(tab)}`;
        });
    });
});

