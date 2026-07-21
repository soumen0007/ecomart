/**
 * shopping-list.js
 * -----------------------------------------------------------------
 * Dedicated script for the Shopping List page.
 * Renders the user's list, handles +/- quantity and remove buttons.
 * -----------------------------------------------------------------
 */

(function () {
    'use strict';

    function money(n) {
        return '$' + Number(n).toFixed(2);
    }
    function escapeHtml(s) {
        var div = document.createElement('div');
        div.textContent = s || '';
        return div.innerHTML;
    }
    function showToast(message, type) {
        type = type || 'success';
        if (window.EcoMartUI && EcoMartUI.showToast) return EcoMartUI.showToast(message, type);
        alert(message);
    }

    async function loadShoppingList() {
        var container = document.getElementById('shopping-list-container');
        var summary = document.getElementById('shopping-list-summary');
        if (!container) return;

        // Show loading state
        container.innerHTML = '<div class="text-center py-5"><i class="bi bi-hourglass-spin fs-3"></i><p class="mt-2">Loading your list...</p></div>';

        var resp = await EcoMartAPI.getShoppingList();
        if (!resp.success) {
            container.innerHTML = '<div class="alert alert-danger">Could not load shopping list.</div>';
            return;
        }

        if (!resp.items.length) {
            container.innerHTML =
                '<div class="empty-state bg-white rounded-4 shadow-sm">' +
                '<div class="icon"><i class="bi bi-basket"></i></div>' +
                '<h4>Your shopping list is empty</h4>' +
                '<p>Browse our products and start adding items for your next visit.</p>' +
                '<a href="' + EcoMartAPI.siteBase + 'index.html#products" class="btn btn-ecomart mt-2">' +
                '<i class="bi bi-bag-heart"></i> Browse Products</a></div>';
            if (summary) summary.classList.add('d-none');
            updateCount(0);
            return;
        }
        if (summary) summary.classList.remove('d-none');

        container.innerHTML = resp.items.map(function (item) {
            return '<div class="shopping-list-item" data-list-id="' + item.list_id + '">' +
                '<a href="' + EcoMartAPI.siteBase + 'product.html?id=' + item.product_id + '">' +
                '<img src="' + item.image_url + '" alt="' + escapeHtml(item.name) + '"></a>' +
                '<div class="flex-grow-1">' +
                '<div class="d-flex justify-content-between align-items-start">' +
                '<div>' +
                '<h5 class="mb-0"><a href="' + EcoMartAPI.siteBase + 'product.html?id=' + item.product_id + '" class="text-decoration-none text-reset">' + escapeHtml(item.name) + '</a></h5>' +
                '<small class="text-muted">' + escapeHtml(item.category_name) + ' &middot; ' + item.price_str + ' each</small>' +
                (item.note ? '<div class="small fst-italic text-muted mt-1"><i class="bi bi-sticky"></i> ' + escapeHtml(item.note) + '</div>' : '') +
                '</div>' +
                '<div class="text-end fw-bold text-success line-total-' + item.list_id + '">' + money(item.line_total) + '</div>' +
                '</div>' +
                '<div class="d-flex justify-content-between align-items-center mt-2">' +
                '<div class="qty-control">' +
                '<button type="button" class="qty-btn" data-action="dec">&minus;</button>' +
                '<input type="number" class="qty-input" data-list-id="' + item.list_id + '" value="' + item.quantity + '" min="1" max="99" readonly>' +
                '<button type="button" class="qty-btn" data-action="inc">+</button>' +
                '</div>' +
                '<button type="button" class="btn btn-sm btn-outline-danger btn-remove-item" data-list-id="' + item.list_id + '">' +
                '<i class="bi bi-trash"></i> Remove</button>' +
                '</div>' +
                '</div></div>';
        }).join('');

        // Add "print" button at the bottom
        container.innerHTML += '<div class="d-flex justify-content-between mt-3">' +
            '<a href="' + EcoMartAPI.siteBase + 'index.html#products" class="btn btn-link">Continue browsing</a>' +
            '<button type="button" class="btn btn-outline-ecomart" onclick="window.print()">' +
            '<i class="bi bi-printer"></i> Print List for In-Store Use</button></div>';

        // Update summary
        updateSummary(resp.count, resp.total);

        // Wire up quantity buttons
        container.querySelectorAll('.qty-btn').forEach(function (btn) {
            btn.addEventListener('click', function () {
                var qtyControl = this.closest('.qty-control');
                var input = qtyControl.querySelector('.qty-input');
                var listId = input.getAttribute('data-list-id');
                var current = parseInt(input.value, 10) || 1;
                if (this.getAttribute('data-action') === 'inc') {
                    input.value = Math.min(99, current + 1);
                } else {
                    input.value = Math.max(1, current - 1);
                }
                updateQuantity(listId, parseInt(input.value, 10));
            });
        });

        // Wire up remove buttons
        container.querySelectorAll('.btn-remove-item').forEach(function (btn) {
            btn.addEventListener('click', function () {
                if (!confirm('Remove this item from your shopping list?')) return;
                var listId = this.getAttribute('data-list-id');
                removeItem(listId);
            });
        });
    }

    async function updateQuantity(listId, qty) {
        var resp = await EcoMartAPI.updateListItem(listId, qty);
        if (resp.success) {
            updateSummary(resp.count, resp.total);
            showToast('Quantity updated', 'success');
            // Refresh cart badge
            if (window.EcoMartAuth) EcoMartAuth.refreshCartCount();
        } else {
            showToast(resp.message || 'Could not update quantity.', 'danger');
            loadShoppingList(); // reload to revert
        }
    }

    async function removeItem(listId) {
        var resp = await EcoMartAPI.removeFromList(listId);
        if (resp.success) {
            showToast(resp.message || 'Item removed.', 'success');
            if (window.EcoMartAuth) EcoMartAuth.refreshCartCount();
            loadShoppingList(); // reload entire list
        } else {
            showToast(resp.message || 'Could not remove item.', 'danger');
        }
    }

    function updateSummary(count, total) {
        var totalEl = document.getElementById('list-total');
        if (totalEl) totalEl.textContent = money(total);
        var countEl = document.getElementById('list-count');
        if (countEl) countEl.textContent = count;
        updateCount(count);
    }

    function updateCount(count) {
        var header = document.getElementById('list-count-header');
        if (header) header.textContent = count;
    }

    // Load on page ready (after session is loaded)
    document.addEventListener('DOMContentLoaded', function () {
        // Wait for auth session to load first
        var checkAuth = setInterval(function () {
            if (EcoMartAuth.state.loaded) {
                clearInterval(checkAuth);
                if (!EcoMartAuth.isLoggedIn()) {
                    var returnUrl = encodeURIComponent(window.location.pathname + window.location.search);
                    window.location.href = EcoMartAPI.siteBase + 'login.html?redirect=' + returnUrl;
                    return;
                }
                loadShoppingList();
            }
        }, 50);
    });
})();
