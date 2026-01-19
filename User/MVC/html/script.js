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
    // LOGIN & REGISTER FORM SUBMIT
    // ===============================
    const loginForm = document.getElementById('loginForm');
    const signupForm = document.getElementById('signupForm');

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
                const emailError = document.getElementById('loginEmailError');
                const passError = document.getElementById('loginPasswordError');
                if (emailError) emailError.innerText = '';
                if (passError) passError.innerText = '';

                if (data.success) {
                    // ✅ SUCCESS → REDIRECT TO DASHBOARD
                    if (data.role && data.role.toLowerCase() === 'admin') {
                        alert("Hello Admin! You have successfully logged in.");
                        console.log("Redirecting to admin dashboard...");
                        window.location.href = '../../../Admin/MVC/html/admin_dashboard.php';
                    } else {
                        alert("Hello User! You have successfully logged in.");
                        window.location.href = 'home.php';
                    }
                } else {
                    if (emailError) emailError.innerText = data.emailError || '';
                    if (passError) passError.innerText = data.passwordError || '';
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

    if (signupForm) {
            signupForm.addEventListener('submit', function (e) {
                e.preventDefault();

                const btn = signupForm.querySelector('button[type="submit"]');
                const originalText = btn.textContent;

                btn.disabled = true;
                btn.textContent = 'Signing Up...';

                const formData = new FormData(signupForm);

                fetch('login.php', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.text())
                .then(text => {
                    try {
                        return JSON.parse(text);
                    } catch (e) {
                        throw new Error('Server Error: ' + text);
                    }
                })
                .then(data => {

                    // Clear previous errors
                    ['signupNameError', 'signupEmailError', 'signupPasswordError', 'signupConfirmError'].forEach(id => {
                        const el = document.getElementById(id);
                        if (el) el.innerText = '';
                    });

                    if (data.success) {
                        alert(data.message);
                        if (data.role && data.role.toLowerCase() === 'admin') {
                            window.location.href = 'admin_dashboard.php';
                        } else {
                            window.location.href = 'home.php';
                        }
                    } else {
                        const nameErr = document.getElementById('signupNameError');
                        const emailErr = document.getElementById('signupEmailError');
                        const passErr = document.getElementById('signupPasswordError');
                        const confErr = document.getElementById('signupConfirmError');

                        if (nameErr) nameErr.innerText = data.nameError || '';
                        if (emailErr) emailErr.innerText = data.emailError || '';
                        if (passErr) passErr.innerText = data.passwordError || '';
                        if (confErr) confErr.innerText = data.confirmError || '';

                        // If there is a general error message (like DB failure) and no specific field errors
                        if (data.message && !data.nameError && !data.emailError && !data.passwordError && !data.confirmError) {
                            alert(data.message);
                        }
                    }
                })
                .catch(error => {
                    console.error('Signup Error:', error);
                    alert(error.message || 'An error occurred. Please try again.');
                })
                .finally(() => {
                    btn.disabled = false;
                    btn.textContent = originalText;
                });
            });
    }
});
