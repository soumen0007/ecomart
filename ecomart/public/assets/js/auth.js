/**
 * auth.js
 * -----------------------------------------------------------------
 * Session management for the EcoMart storefront.
 *
 * On every page load:
 *   1. Calls the API to check if user is logged in
 *   2. Updates the navbar (login vs. user dropdown)
 *   3. Updates the shopping list badge count
 *
 * Also exposes a helper `requireAuth()` that pages can call before
 * performing actions that need a logged-in user.
 * -----------------------------------------------------------------
 */

(function () {
    'use strict';

    var sessionState = {
        loggedIn: false,
        user: null,
        cartCount: 0,
        loaded: false
    };

    /**
     * Fetch current session from the API.
     * Returns a promise resolving to the session object.
     */
    async function loadSession() {
        var resp = await EcoMartAPI.getSession();
        if (resp && resp.success) {
            sessionState.loggedIn = resp.logged_in;
            sessionState.user = resp.user;
            sessionState.cartCount = resp.cart_count || 0;
            sessionState.loaded = true;
            if (resp.csrf_token) EcoMartAPI._setCsrfToken(resp.csrf_token);
            updateUI();
        }
        return sessionState;
    }

    /**
     * Update navbar elements based on session state.
     * Elements are tagged with data attributes:
     *   #nav-user-name         - shows user's first name
     *   #nav-user-email        - shows user's email in dropdown
     *   #cart-badge            - shows cart count
     *   .nav-logged-out        - shown when not logged in
     *   .nav-logged-in         - shown when logged in
     */
    function updateUI() {
        var loggedOutEls = document.querySelectorAll('.nav-logged-out');
        var loggedInEls  = document.querySelectorAll('.nav-logged-in');
        var userNameEls  = document.querySelectorAll('#nav-user-name, .nav-user-name');
        var userEmailEls = document.querySelectorAll('.nav-user-email');
        var cartBadges   = document.querySelectorAll('.cart-badge');

        if (sessionState.loggedIn) {
            loggedOutEls.forEach(function (el) { el.classList.add('d-none'); });
            loggedInEls.forEach(function (el) { el.classList.remove('d-none'); });
            userNameEls.forEach(function (el) {
                el.textContent = sessionState.user ? sessionState.user.first_name : '';
            });
            userEmailEls.forEach(function (el) {
                el.textContent = sessionState.user ? sessionState.user.email : '';
            });
        } else {
            loggedOutEls.forEach(function (el) { el.classList.remove('d-none'); });
            loggedInEls.forEach(function (el) { el.classList.add('d-none'); });
        }

        // Update cart badge
        cartBadges.forEach(function (el) {
            if (sessionState.cartCount > 0) {
                el.textContent = sessionState.cartCount;
                el.classList.remove('d-none');
            } else {
                el.classList.add('d-none');
            }
        });
    }

    /** Returns true if user is logged in (after session is loaded). */
    function isLoggedIn() {
        return sessionState.loggedIn;
    }

    /** Returns current user object or null. */
    function getUser() {
        return sessionState.user;
    }

    /** Returns current cart count. */
    function getCartCount() {
        return sessionState.cartCount;
    }

    /** Refresh cart count from server (after add/update/remove). */
    async function refreshCartCount() {
        var resp = await EcoMartAPI.getSession();
        if (resp && resp.success) {
            sessionState.cartCount = resp.cart_count || 0;
            updateUI();
        }
        return sessionState.cartCount;
    }

    /** Log out the user, then redirect to homepage. */
    async function logout() {
        await EcoMartAPI.logout();
        sessionState.loggedIn = false;
        sessionState.user = null;
        sessionState.cartCount = 0;
        window.location.href = EcoMartAPI.siteBase + 'index.html';
    }

    /* ---- Public API ---- */
    window.EcoMartAuth = {
        loadSession: loadSession,
        isLoggedIn: isLoggedIn,
        getUser: getUser,
        getCartCount: getCartCount,
        refreshCartCount: refreshCartCount,
        logout: logout,
        state: sessionState
    };

    // Auto-load session on DOMContentLoaded
    document.addEventListener('DOMContentLoaded', function () {
        loadSession().catch(function (err) {
            console.error('Session load failed:', err);
        });
    });
})();
