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

  // ===============================
// LOGIN FORM SUBMIT (FINAL VERSION)
// ===============================
document.addEventListener('DOMContentLoaded', function () {

    const loginForm = document.getElementById('loginForm');

    if (loginForm) {
        loginForm.addEventListener('submit', function (e) {
            e.preventDefault();

            const btn = loginForm.querySelector('button[type="submit"]');
            const originalText = btn.textContent;

            btn.disabled = true;
            btn.textContent = 'Signing In...';

            const formData = new FormData(loginForm);

            fetch('login.php', {
                method: 'POST',
                body: formData
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error('Network error');
                }
                return response.json();
            })
            .then(data => {

                // Clear previous errors
                document.getElementById('loginEmailError').innerText = '';
                document.getElementById('loginPasswordError').innerText = '';

                if (data.success) {
                    // ✅ SUCCESS → REDIRECT TO DASHBOARD
                    window.location.href = 'dashboard.php';
                } else {
                    document.getElementById('loginEmailError').innerText = data.emailError || '';
                    document.getElementById('loginPasswordError').innerText = data.passwordError || '';
                }
            })
            .catch(error => {
                console.error(error);
                alert('An error occurred. Please try again.');
            })
            .finally(() => {
                btn.disabled = false;
                btn.textContent = originalText;
            });
        });
    }
});


    handleForm('loginForm', 'login');
    handleForm('signupForm', 'signup');
});
// ===============================
// SIGN UP FORM SUBMIT (AJAX FIX)
// ===============================
document.addEventListener('DOMContentLoaded', function () {

    const signupForm = document.getElementById('signupForm');

    if (signupForm) {
        signupForm.addEventListener('submit', function (e) {
            e.preventDefault(); // ⛔ stop normal form submit

            const formData = new FormData(signupForm);

            fetch('login.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {

                // Clear previous errors
                document.getElementById('signupNameError').innerText = '';
                document.getElementById('signupEmailError').innerText = '';
                document.getElementById('signupPasswordError').innerText = '';
                document.getElementById('signupConfirmError').innerText = '';

                if (data.success) {
                    alert(data.message);
                    window.location.href = 'dashboard.php'; // change if needed
                } else {
                    document.getElementById('signupNameError').innerText = data.nameError;
                    document.getElementById('signupEmailError').innerText = data.emailError;
                    document.getElementById('signupPasswordError').innerText = data.passwordError;
                    document.getElementById('signupConfirmError').innerText = data.confirmError;
                }
            })
            .catch(error => {
                console.error('Error:', error);
            });
        });
    }
});
// ===============================