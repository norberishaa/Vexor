// Tab switching functionality
document.querySelectorAll('.admin-tab').forEach(tab => {
    tab.addEventListener('click', function() {
        const targetTab = this.getAttribute('data-admintab');
        
        // Remove active class from all tabs and contents
        document.querySelectorAll('.admin-tab').forEach(t => t.classList.remove('active'));
        document.querySelectorAll('.tab-content').forEach(c => c.classList.remove('active'));
        
        // Add active class to clicked tab and corresponding content
        this.classList.add('active');
        document.querySelector(`.tab-content:has(.admin-${targetTab})`).classList.add('active');
        
        // Update URL without page reload
        const url = new URL(window.location);
        url.searchParams.set('tab', targetTab);
        window.history.pushState({}, '', url);
    });
});

// ==================== USER MODAL ====================
const editUserModal = document.getElementById('edit-user-modal');
const closeUserModal = document.getElementById('close-user-modal');
const deleteUserBtn = document.getElementById('delete-user-modal');

function editUser(userId, name, email, admin) {
    document.getElementById('edit_user_id').value = userId;
    document.getElementById('edit_name').value = name;
    document.getElementById('edit_email').value = email;
    document.getElementById('edit_admin').value = admin;
    
    editUserModal.classList.add('active');
}

closeUserModal.addEventListener('click', function() {
    editUserModal.classList.remove('active');
});

deleteUserBtn.addEventListener('click', function() {
    const userId = document.getElementById('edit_user_id').value;
    const userName = document.getElementById('edit_name').value;
    
    if (confirm(`Are you sure you want to delete user "${userName}"? This action cannot be undone.`)) {
        // Create a form to submit the delete request
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = 'delete-user.php';
        
        const input = document.createElement('input');
        input.type = 'hidden';
        input.name = 'user_id';
        input.value = userId;
        
        form.appendChild(input);
        document.body.appendChild(form);
        form.submit();
    }
});

// ==================== CVE MODAL ====================
const editCveModal = document.getElementById('edit-cve-modal');
const closeCveModal = document.getElementById('close-cve-modal');
const deleteCveBtn = document.getElementById('delete-cve-modal');

function editCve(cveId, name, status, severity, dateReported, description) {
    document.getElementById('edit_cve_id').value = cveId;
    document.getElementById('edit_cve_id_display').value = cveId;
    document.getElementById('edit_cve_name').value = name;
    document.getElementById('edit_cve_status').value = status;
    document.getElementById('edit_cve_severity').value = severity;
    document.getElementById('edit_cve_date_reported').value = dateReported;
    document.getElementById('edit_cve_description').value = description;
    
    editCveModal.classList.add('active');
}

closeCveModal.addEventListener('click', function() {
    editCveModal.classList.remove('active');
});

deleteCveBtn.addEventListener('click', function() {
    const cveId = document.getElementById('edit_cve_id').value;
    const cveName = document.getElementById('edit_cve_name').value;
    
    if (confirm(`Are you sure you want to delete CVE "${cveId} - ${cveName}"? This action cannot be undone.`)) {
        // Create a form to submit the delete request
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = 'delete-cve.php';
        
        const input = document.createElement('input');
        input.type = 'hidden';
        input.name = 'cve_id';
        input.value = cveId;
        
        form.appendChild(input);
        document.body.appendChild(form);
        form.submit();
    }
});

// ==================== NEWS MODAL ====================
const editNewsModal = document.getElementById('edit-news-modal');
const closeNewsModal = document.getElementById('close-news-modal');
const deleteNewsBtn = document.getElementById('delete-news-modal');

function editNews(newsId, title, description, author, articleUrl) {
    document.getElementById('edit_news_id').value = newsId;
    document.getElementById('edit_news_title').value = title;
    document.getElementById('edit_news_description').value = description;
    document.getElementById('edit_news_author').value = author;
    document.getElementById('edit_news_article_url').value = articleUrl;
    
    editNewsModal.classList.add('active');
}

closeNewsModal.addEventListener('click', function() {
    editNewsModal.classList.remove('active');
});

deleteNewsBtn.addEventListener('click', function() {
    const newsId = document.getElementById('edit_news_id').value;
    const newsTitle = document.getElementById('edit_news_title').value;
    
    if (confirm(`Are you sure you want to delete the article "${newsTitle}"? This action cannot be undone.`)) {
        // Create a form to submit the delete request
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = 'delete-news.php';
        
        const input = document.createElement('input');
        input.type = 'hidden';
        input.name = 'news_id';
        input.value = newsId;
        
        form.appendChild(input);
        document.body.appendChild(form);
        form.submit();
    }
});