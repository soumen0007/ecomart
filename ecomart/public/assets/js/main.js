/**
 * main.js
 * -----------------------------------------------------------------
 * Customer-facing UI logic for the EcoMart storefront.
 *
 * Handles:
 *   - Add-to-list button clicks (AJAX)
 *   - Quantity +/- controls
 *   - Shopping list page (render, update, remove)
 *   - Form validation (signup, login, contact)
 *   - Password strength meter
 *   - Toast notifications
 *
 * BUG FIXES (from previous version):
 *   - Quantity +/- buttons now use type="button" so they don't
 *     accidentally submit the parent form.
 *   - Add-to-list button stops event propagation so navigating
 *     from the homepage never triggers an add.
 *   - No auto-firing of any add action on page load - all adds
 *     are strictly triggered by user click events.
 * -----------------------------------------------------------------
 */

(function () {
    'use strict';

    /* =========================================================
     *  TOAST NOTIFICATIONS
     * ========================================================= */
    function showToast(message, type) {
        type = type || 'success';
        var container = document.getElementById('toast-container');
        if (!container) {
            container = document.createElement('div');
            container.id = 'toast-container';
            container.style.cssText = 'position:fixed;bottom:20px;right:20px;z-index:1090;max-width:350px;';
            document.body.appendChild(container);
        }
        var toast = document.createElement('div');
        toast.className = 'toast align-items-center text-bg-' + type + ' border-0 show mb-2';
        toast.setAttribute('role', 'alert');
        toast.innerHTML =
            '<div class="d-flex">' +
            '<div class="toast-body">' + escapeHtml(message) + '</div>' +
            '<button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>' +
            '</div>';
        container.appendChild(toast);
        // Auto-remove after 3 seconds
        setTimeout(function () {
            toast.classList.remove('show');
            setTimeout(function () { toast.remove(); }, 300);
        }, 3000);

        // Manual close button
        toast.querySelector('.btn-close').addEventListener('click', function () {
            toast.classList.remove('show');
            setTimeout(function () { toast.remove(); }, 300);
        });
    }

    function escapeHtml(str) {
        var div = document.createElement('div');
        div.textContent = str;
        return div.innerHTML;
    }

    /* =========================================================
     *  ADD TO SHOPPING LIST
     *  Fixed: only triggers on actual click, never auto-fires.
     * ========================================================= */
    function initAddToListButtons() {
        // Use click capture with stopPropagation to prevent any parent link click
        document.addEventListener('click', function (e) {
            var btn = e.target.closest('.btn-add-to-list');
            if (!btn) return;

            e.preventDefault();
            e.stopPropagation();

            var productId = parseInt(btn.getAttribute('data-product-id'), 10);
            if (!productId) return;

            // If user is not logged in, redirect to login
            if (!EcoMartAuth.isLoggedIn()) {
                var returnUrl = encodeURIComponent(window.location.pathname + window.location.search);
                window.location.href = EcoMartAPI.siteBase + 'login.html?redirect=' + returnUrl;
                return;
            }

            // Get quantity from a sibling .qty-input if present
            var qtyInput = document.querySelector('.qty-input');
            var qty = qtyInput ? Math.max(1, parseInt(qtyInput.value, 10) || 1) : 1;

            // Get optional note
            var noteInput = document.querySelector('#note');
            var note = noteInput ? noteInput.value.trim() : '';

            // Disable button + show loading state
            var originalHtml = btn.innerHTML;
            btn.disabled = true;
            btn.innerHTML = '<i class="bi bi-hourglass-split"></i> Adding...';

            EcoMartAPI.addToList(productId, qty, note)
                .then(function (resp) {
                    if (resp.success) {
                        showToast(resp.message || 'Added to your shopping list!', 'success');
                        // Update cart badge
                        EcoMartAuth.refreshCartCount();
                        // Show "Added" state for 1.5s
                        btn.innerHTML = '<i class="bi bi-check-lg"></i> Added!';
                        btn.classList.add('btn-success');
                        btn.classList.remove('btn-ecomart');
                        setTimeout(function () {
                            btn.innerHTML = originalHtml;
                            btn.classList.remove('btn-success');
                            btn.classList.add('btn-ecomart');
                            btn.disabled = false;
                        }, 1500);
                    } else {
                        showToast(resp.message || 'Could not add item.', 'danger');
                        btn.innerHTML = originalHtml;
                        btn.disabled = false;
                    }
                })
                .catch(function () {
                    showToast('Network error. Please try again.', 'danger');
                    btn.innerHTML = originalHtml;
                    btn.disabled = false;
                });
        }, true); // use capture phase so we intercept before any link navigation
    }

    /* =========================================================
     *  QUANTITY +/- CONTROLS
     *  Fixed: type="button" prevents form submission.
     * ========================================================= */
    function initQuantityControls() {
        document.addEventListener('click', function (e) {
            var btn = e.target.closest('.qty-btn');
            if (!btn) return;

            e.preventDefault();
            e.stopPropagation();

            // Find the associated input (siblings)
            var container = btn.closest('.qty-control');
            if (!container) return;
            var input = container.querySelector('.qty-input');
            if (!input) return;

            var current = parseInt(input.value, 10) || 1;
            var action = btn.getAttribute('data-action');

            if (action === 'inc') {
                input.value = Math.min(99, current + 1);
            } else if (action === 'dec') {
                input.value = Math.max(1, current - 1);
            }

            // Trigger change event for any listeners
            input.dispatchEvent(new Event('change', { bubbles: true }));
        });
    }

    /* =========================================================
     *  PASSWORD STRENGTH METER
     * ========================================================= */
    function initPasswordStrength() {
        var pwInput = document.getElementById('password');
        if (!pwInput) return;
        var bar = document.querySelector('.password-strength-bar');
        if (!bar) return;

        pwInput.addEventListener('input', function () {
            var pw = pwInput.value;
            var score = 0;
            if (pw.length >= 8) score++;
            if (/[A-Z]/.test(pw)) score++;
            if (/[a-z]/.test(pw)) score++;
            if (/[0-9]/.test(pw)) score++;
            if (/[^A-Za-z0-9]/.test(pw)) score++;

            var pct = score * 20;
            bar.style.width = pct + '%';
            bar.className = 'password-strength-bar';
            if (score >= 4) bar.classList.add('bg-success');
            else if (score === 3) bar.classList.add('bg-warning');
            else if (score === 2) bar.classList.add('bg-info');
            else bar.classList.add('bg-danger');
        });
    }

    /* =========================================================
     *  CONFIRM PASSWORD MATCH
     * ========================================================= */
    function initConfirmPassword() {
        var confirm = document.getElementById('confirm_password');
        var password = document.getElementById('password');
        if (!confirm || !password) return;

        confirm.addEventListener('input', function () {
            if (confirm.value !== password.value) {
                confirm.setCustomValidity('Passwords do not match.');
            } else {
                confirm.setCustomValidity('');
            }
        });
    }

    /* =========================================================
     *  CHARACTER COUNTERS
     * ========================================================= */
    function initCharCounters() {
        document.querySelectorAll('[data-char-count]').forEach(function (el) {
            var targetId = el.getAttribute('data-char-count');
            var target = document.getElementById(targetId);
            if (!target) return;
            var max = parseInt(target.getAttribute('maxlength'), 10) || 1000;
            function update() {
                el.textContent = target.value.length + ' / ' + max;
            }
            target.addEventListener('input', update);
            update();
        });
    }

    /* =========================================================
     *  FORM SUBMISSION HELPERS
     * ========================================================= */

    // Login form
    function initLoginForm() {
        var form = document.getElementById('login-form');
        if (!form) return;
        form.addEventListener('submit', async function (e) {
            e.preventDefault();
            var formData = new FormData(form);
            var data = {
                email: formData.get('email'),
                password: formData.get('password')
            };
            var submitBtn = form.querySelector('button[type="submit"]');
            var original = submitBtn.innerHTML;
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<i class="bi bi-hourglass-split"></i> Signing in...';

            var resp = await EcoMartAPI.login(data);
            if (resp.success) {
                showToast(resp.message || 'Welcome back!', 'success');
                // Redirect to ?redirect URL or homepage
                var params = new URLSearchParams(window.location.search);
                var redirect = params.get('redirect');
                setTimeout(function () {
                    window.location.href = redirect || (EcoMartAPI.siteBase + 'index.html');
                }, 600);
            } else {
                showToast(resp.message || 'Login failed.', 'danger');
                submitBtn.disabled = false;
                submitBtn.innerHTML = original;
            }
        });
    }

    // Signup form
    function initSignupForm() {
        var form = document.getElementById('signup-form');
        if (!form) return;
        form.addEventListener('submit', async function (e) {
            e.preventDefault();
            if (!form.checkValidity()) {
                form.classList.add('was-validated');
                return;
            }
            var formData = new FormData(form);
            var data = {
                first_name: formData.get('first_name'),
                last_name:  formData.get('last_name'),
                email:      formData.get('email'),
                phone:      formData.get('phone') || '',
                password:   formData.get('password')
            };
            var submitBtn = form.querySelector('button[type="submit"]');
            var original = submitBtn.innerHTML;
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<i class="bi bi-hourglass-split"></i> Creating...';

            var resp = await EcoMartAPI.signup(data);
            if (resp.success) {
                showToast(resp.message || 'Account created!', 'success');
                setTimeout(function () {
                    var params = new URLSearchParams(window.location.search);
                    var redirect = params.get('redirect');
                    window.location.href = redirect || (EcoMartAPI.siteBase + 'index.html');
                }, 600);
            } else {
                showToast(resp.message || 'Sign up failed.', 'danger');
                submitBtn.disabled = false;
                submitBtn.innerHTML = original;
            }
        });
    }

    // Contact form
    function initContactForm() {
        var form = document.getElementById('contact-form');
        if (!form) return;
        form.addEventListener('submit', async function (e) {
            e.preventDefault();
            if (!form.checkValidity()) {
                form.classList.add('was-validated');
                return;
            }
            var formData = new FormData(form);
            var data = {
                name:    formData.get('name'),
                email:   formData.get('email'),
                subject: formData.get('subject') || '',
                message: formData.get('message')
            };
            var submitBtn = form.querySelector('button[type="submit"]');
            var original = submitBtn.innerHTML;
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<i class="bi bi-hourglass-split"></i> Sending...';

            var resp = await EcoMartAPI.sendContact(data);
            if (resp.success) {
                showToast(resp.message || 'Message sent!', 'success');
                form.reset();
            } else {
                showToast(resp.message || 'Could not send message.', 'danger');
            }
            submitBtn.disabled = false;
            submitBtn.innerHTML = original;
        });
    }

    /* =========================================================
     *  LOGOUT BUTTONS
     * ========================================================= */
    function initLogoutButtons() {
        document.addEventListener('click', function (e) {
            var btn = e.target.closest('.btn-logout');
            if (!btn) return;
            e.preventDefault();
            EcoMartAuth.logout();
        });
    }

    /* =========================================================
     *  SHOW/HIDE PASSWORD TOGGLE
     * ========================================================= */
    function initPasswordToggles() {
        document.addEventListener('click', function (e) {
            var btn = e.target.closest('.toggle-password');
            if (!btn) return;
            var inputId = btn.getAttribute('data-target');
            var input = document.getElementById(inputId);
            if (!input) return;
            var icon = btn.querySelector('i');
            if (input.type === 'password') {
                input.type = 'text';
                if (icon) { icon.classList.remove('bi-eye'); icon.classList.add('bi-eye-slash'); }
            } else {
                input.type = 'password';
                if (icon) { icon.classList.remove('bi-eye-slash'); icon.classList.add('bi-eye'); }
            }
        });
    }

    /* =========================================================
     *  SMOOTH SCROLL FOR IN-PAGE ANCHORS
     * ========================================================= */
    function initSmoothScroll() {
        document.addEventListener('click', function (e) {
            var link = e.target.closest('a[href^="#"]');
            if (!link) return;
            var href = link.getAttribute('href');
            if (href === '#' || href === '#!') return;
            var target = document.querySelector(href);
            if (!target) return;
            e.preventDefault();
            window.scrollTo({
                top: target.offsetTop - 80,
                behavior: 'smooth'
            });
        });
    }

    /* =========================================================
     *  INIT EVERYTHING
     * ========================================================= */
    document.addEventListener('DOMContentLoaded', function () {
        initAddToListButtons();
        initQuantityControls();
        initPasswordStrength();
        initConfirmPassword();
        initCharCounters();
        initLoginForm();
        initSignupForm();
        initContactForm();
        initLogoutButtons();
        initPasswordToggles();
        initSmoothScroll();
    });

    // Expose showToast globally for inline use
    window.EcoMartUI = {
        showToast: showToast,
        escapeHtml: escapeHtml
    };
})();
