/**
 * api.js
 * -----------------------------------------------------------------
 * Shared API helper for the EcoMart frontend.
 *
 * - Wraps fetch() with consistent JSON handling
 * - Attaches CSRF token to mutating requests
 * - Handles 401 (unauthorized) by redirecting to login
 * - Exposes the global `EcoMartAPI` object
 *
 * Used by every HTML page on both storefront and admin portal.
 * -----------------------------------------------------------------
 */

// Detect API base URL relative to current page location.
// Site is served from /ecomart/ on the web server.
// API lives at /ecomart/api/
(function () {
    'use strict';

    // Compute base path from current URL. Works whether deployed at
    // http://localhost/ecomart/ or at the domain root.
    var path = window.location.pathname;
    // Strip /admin/* and any *.html filename to get the project root.
    var base = path.replace(/\/admin\/.*$/, '/').replace(/\/[^/]+\.html$/, '/');
    // Now `base` ends with a single slash; the API is at base + 'api/'
    var API_BASE = base + 'api/';

    // csrf token is populated after the first session call.
    var csrfToken = '';

    /**
     * Core request function.
     * @param {string} endpoint  e.g. 'auth.php?action=login'
     * @param {object} options   fetch options (method, body, etc.)
     */
    async function request(endpoint, options) {
        options = options || {};
        options.headers = options.headers || {};

        var url = API_BASE + endpoint;

        // For POST/PUT/DELETE, send CSRF token in header
        if (options.method && options.method.toUpperCase() !== 'GET') {
            if (csrfToken) {
                options.headers['X-CSRF-Token'] = csrfToken;
            }
        }

        // If body is a plain object and no Content-Type set, send as form-encoded.
        // (PHP's $_POST works out of the box with application/x-www-form-urlencoded)
        if (options.body && typeof options.body === 'object' && !(options.body instanceof FormData)) {
            options.headers['Content-Type'] = 'application/x-www-form-urlencoded';
            options.body = new URLSearchParams(options.body).toString();
        }

        try {
            var resp = await fetch(url, options);
            var data;
            try { data = await resp.json(); } catch (e) {
                throw new Error('Invalid response from server.');
            }

            // Save CSRF token if returned
            if (data && data.csrf_token) {
                csrfToken = data.csrf_token;
            }

            // Handle 401: redirect to login (storefront only)
            if (resp.status === 401) {
                // If on an admin page, redirect to admin login
                if (window.location.pathname.indexOf('/admin/') !== -1) {
                    window.location.href = base + 'admin/login.html';
                } else if (endpoint.indexOf('auth.php') === -1 && endpoint.indexOf('admin/') === -1) {
                    // Storefront page that needs login: redirect to login with return URL
                    var returnUrl = encodeURIComponent(window.location.pathname + window.location.search);
                    window.location.href = base + 'login.html?redirect=' + returnUrl;
                }
                return data;
            }

            return data;
        } catch (err) {
            console.error('API error:', err);
            return { success: false, message: 'Network error: ' + err.message };
        }
    }

    /* ---- Public API ---- */
    window.EcoMartAPI = {
        base: API_BASE,
        siteBase: base,

        /* Customer auth */
        getSession:   function () { return request('auth.php?action=session'); },
        signup:       function (data) { return request('auth.php?action=signup', { method: 'POST', body: data }); },
        login:        function (data) { return request('auth.php?action=login',  { method: 'POST', body: data }); },
        logout:       function () { return request('auth.php?action=logout', { method: 'POST' }); },

        /* Products (public) */
        getProducts:  function (params) {
            var qs = new URLSearchParams(params || {}).toString();
            return request('products.php' + (qs ? '?' + qs : ''));
        },
        getProduct:   function (id) { return request('products.php?id=' + id); },
        getCategories:function () { return request('products.php?categories=1'); },

        /* Shopping list (requires login) */
        getShoppingList:  function () { return request('shopping-list.php'); },
        addToList:        function (productId, qty, note) {
            return request('shopping-list.php?action=add', {
                method: 'POST',
                body: { product_id: productId, quantity: qty || 1, note: note || '' }
            });
        },
        updateListItem:   function (listId, qty) {
            return request('shopping-list.php?action=update', {
                method: 'POST',
                body: { list_id: listId, quantity: qty }
            });
        },
        removeFromList:   function (listId) {
            return request('shopping-list.php?action=remove', {
                method: 'POST',
                body: { list_id: listId }
            });
        },

        /* Contact form */
        sendContact:  function (data) {
            return request('contact.php', { method: 'POST', body: data });
        },

        /* Admin auth */
        admin: {
            getSession: function () { return request('admin/auth.php?action=session'); },
            login:      function (data) { return request('admin/auth.php?action=login', { method: 'POST', body: data }); },
            logout:     function () { return request('admin/auth.php?action=logout', { method: 'POST' }); },

            getProducts: function (params) {
                var qs = new URLSearchParams(params || {}).toString();
                return request('admin/products.php' + (qs ? '?' + qs : ''));
            },
            getProduct:  function (id) { return request('admin/products.php?id=' + id); },
            createProduct: function (formData) {
                return request('admin/products.php?action=create', { method: 'POST', body: formData });
            },
            updateProduct: function (formData) {
                return request('admin/products.php?action=update', { method: 'POST', body: formData });
            },
            deleteProduct: function (id) {
                return request('admin/products.php?action=delete', {
                    method: 'POST',
                    body: { id: id }
                });
            },
            getStats: function () { return request('admin/stats.php'); },
        },

        /* Internal */
        _setCsrfToken: function (t) { csrfToken = t; },
        _getCsrfToken: function () { return csrfToken; }
    };
})();
