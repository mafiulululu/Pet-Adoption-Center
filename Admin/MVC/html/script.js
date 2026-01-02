document.addEventListener('DOMContentLoaded', () => {
    // ===========================
    // TAB SWITCHING LOGIC
    // ===========================
    const tabs = document.querySelectorAll('.auth-tab');
    const forms = document.querySelectorAll('.form-content');

    function switchTab(tabId) {
        // Update tab buttons
        tabs.forEach(tab => {
            if (tab.dataset.tab === tabId) {
                tab.classList.add('active');
            } else {
                tab.classList.remove('active');
            }
        });

        // Update form visibility
        forms.forEach(form => {
            if (form.id === `${tabId}-form`) {
                form.classList.add('active');
            } else {
                form.classList.remove('active');
            }
        });

        // Update URL to reflect current tab
        const url = new URL(window.location);
        url.searchParams.set('tab', tabId);
        window.history.pushState({}, '', url);
    }

    // Click event for tabs
    tabs.forEach(tab => {
        tab.addEventListener('click', () => {
            switchTab(tab.dataset.tab);
        });
    });

    // Handle URL parameters (e.g., redirect from register.php)
    const urlParams = new URLSearchParams(window.location.search);
    if (urlParams.get('tab') === 'register') {
        switchTab('register');
    }

    // Handle "Sign up" and "Back to Login" links
    document.querySelectorAll('.switch-form').forEach(link => {
        link.addEventListener('click', (e) => {
            e.preventDefault();
            switchTab(link.dataset.target);
        });
    });

    // ===========================
    // PASSWORD VISIBILITY TOGGLE
    // ===========================
    document.querySelectorAll('.password-toggle').forEach(btn => {
        btn.addEventListener('click', () => {
            const input = document.getElementById(btn.dataset.target);
            if (input.type === 'password') {
                input.type = 'text';
            } else {
                input.type = 'password';
            }
        });
    });

    // ===========================
    // AJAX FORM SUBMISSION
    // ===========================
    const handleForm = (formId, errorPrefix) => {
        const form = document.getElementById(formId);
        if (!form) return;

        form.addEventListener('submit', async (e) => {
            e.preventDefault();
            
            // Clear previous errors
            document.querySelectorAll('.error-msg').forEach(el => el.textContent = '');
            
            const btn = form.querySelector('button[type="submit"]');
            const originalText = btn.textContent;
            btn.disabled = true;
            btn.textContent = 'Processing...';

            try {
                const formData = new FormData(form);
                const response = await fetch('login.php', {
                    method: 'POST',
                    body: formData
                });
                
                const data = await response.json();

                if (data.success) {
                    window.location.href = 'dashboard.php'; // Redirect on success
                } else {
                    // Display errors
                    if (data.emailError) document.getElementById(`${errorPrefix}EmailError`).textContent = data.emailError;
                    if (data.passwordError) document.getElementById(`${errorPrefix}PasswordError`).textContent = data.passwordError;
                    if (data.nameError) document.getElementById(`${errorPrefix}NameError`).textContent = data.nameError;
                    if (data.confirmError) document.getElementById(`${errorPrefix}ConfirmError`).textContent = data.confirmError;
                    if (data.message && !data.emailError && !data.passwordError) alert(data.message);
                }
            } catch (error) {
                console.error('Error:', error);
                alert('An error occurred. Please try again.');
            } finally {
                btn.disabled = false;
                btn.textContent = originalText;
            }
        });
    };

    handleForm('loginForm', 'login');
    handleForm('signupForm', 'signup');
});