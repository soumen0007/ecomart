/**
 * admin.js
 * -----------------------------------------------------------------
 * Admin portal UI logic for EcoMart.
 *
 * Handles:
 *   - Admin session check on every admin page
 *   - Login / logout
 *   - Dashboard rendering (stats, recent products, low stock)
 *   - Product list rendering
 *   - Add/Edit/Delete product (with image upload via FormData)
 *   - Search & filter
 *   - Modal-based delete confirmation
 * -----------------------------------------------------------------
 */

(function () {
    'use strict';

    /* =========================================================
     *  TOAST
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
        setTimeout(function () {
            toast.classList.remove('show');
            setTimeout(function () { toast.remove(); }, 300);
        }, 3500);
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
    function money(n) {
        return '$' + Number(n).toFixed(2);
    }
    function escapeAttr(s) {
        return String(s).replace(/&/g, '&amp;').replace(/"/g, '&quot;').replace(/</g, '&lt;').replace(/>/g, '&gt;');
    }

    /* =========================================================
     *  ADMIN SESSION
     * ========================================================= */
    async function loadAdminSession() {
        var resp = await EcoMartAPI.admin.getSession();
        if (!resp || !resp.success || !resp.logged_in) {
            // Not logged in - redirect to admin login (unless we're already there)
            if (!window.location.pathname.endsWith('login.html')) {
                window.location.href = EcoMartAPI.siteBase + 'admin/login.html';
            }
            return null;
        }
        if (resp.csrf_token) EcoMartAPI._setCsrfToken(resp.csrf_token);

        // Update admin name display
        document.querySelectorAll('.admin-name').forEach(function (el) {
            el.textContent = resp.admin.full_name;
        });
        document.querySelectorAll('.admin-role').forEach(function (el) {
            el.textContent = resp.admin.role;
        });
        return resp.admin;
    }

    /* =========================================================
     *  LOGIN PAGE
     * ========================================================= */
    function initLoginPage() {
        var form = document.getElementById('admin-login-form');
        if (!form) return;
        // Already logged in? Go to dashboard
        EcoMartAPI.admin.getSession().then(function (resp) {
            if (resp && resp.success && resp.logged_in) {
                window.location.href = EcoMartAPI.siteBase + 'admin/dashboard.html';
            }
        });

        form.addEventListener('submit', async function (e) {
            e.preventDefault();
            var formData = new FormData(form);
            var data = {
                username: formData.get('username'),
                password: formData.get('password')
            };
            var btn = form.querySelector('button[type="submit"]');
            var original = btn.innerHTML;
            btn.disabled = true;
            btn.innerHTML = '<i class="bi bi-hourglass-split"></i> Signing in...';

            var resp = await EcoMartAPI.admin.login(data);
            if (resp.success) {
                showToast(resp.message || 'Welcome!', 'success');
                setTimeout(function () {
                    window.location.href = EcoMartAPI.siteBase + 'admin/dashboard.html';
                }, 500);
            } else {
                showToast(resp.message || 'Login failed.', 'danger');
                btn.disabled = false;
                btn.innerHTML = original;
            }
        });
    }

    /* =========================================================
     *  LOGOUT
     * ========================================================= */
    function initLogoutButtons() {
        document.addEventListener('click', function (e) {
            var btn = e.target.closest('.btn-admin-logout');
            if (!btn) return;
            e.preventDefault();
            EcoMartAPI.admin.logout().then(function () {
                window.location.href = EcoMartAPI.siteBase + 'admin/login.html';
            });
        });
    }

    /* =========================================================
     *  DASHBOARD
     * ========================================================= */
    async function loadDashboard() {
        var container = document.getElementById('dashboard-content');
        if (!container) return;
        var resp = await EcoMartAPI.admin.getStats();
        if (!resp.success) {
            container.innerHTML = '<div class="alert alert-danger">Could not load dashboard data.</div>';
            return;
        }
        var t = resp.totals;
        // Stat cards
        document.getElementById('stat-products').textContent = t.products;
        document.getElementById('stat-categories').textContent = t.categories;
        document.getElementById('stat-users').textContent = t.users;
        document.getElementById('stat-lowstock').textContent = t.low_stock;

        // Category breakdown
        var catContainer = document.getElementById('category-breakdown');
        if (catContainer) {
            if (!resp.categories_breakdown.length) {
                catContainer.innerHTML = '<p class="text-muted">No categories yet.</p>';
            } else {
                catContainer.innerHTML = resp.categories_breakdown.map(function (c) {
                    var pct = t.products > 0 ? Math.round((c.total / t.products) * 100) : 0;
                    return '<div class="mb-3">' +
                        '<div class="d-flex justify-content-between small">' +
                        '<span>' + escapeHtml(c.name) + '</span>' +
                        '<strong>' + c.total + ' (' + pct + '%)</strong>' +
                        '</div>' +
                        '<div class="progress" style="height: 6px;">' +
                        '<div class="progress-bar bg-success" style="width: ' + pct + '%"></div>' +
                        '</div></div>';
                }).join('');
            }
        }

        // Recent products table
        var recentBody = document.getElementById('recent-products-body');
        if (recentBody) {
            if (!resp.recent_products.length) {
                recentBody.innerHTML = '<tr><td colspan="4" class="text-center text-muted py-4">No products yet.</td></tr>';
            } else {
                recentBody.innerHTML = resp.recent_products.map(function (p) {
                    var stockBadge = p.stock < 20
                        ? '<span class="badge bg-warning text-dark">' + p.stock + ' left</span>'
                        : '<span class="text-muted">' + p.stock + '</span>';
                    return '<tr>' +
                        '<td><img src="' + p.image_url + '" alt="" class="product-thumb-sm me-2">' +
                        '<a href="' + EcoMartAPI.siteBase + 'admin/product-edit.html?id=' + p.id + '">' + escapeHtml(p.name) + '</a></td>' +
                        '<td><span class="badge bg-light text-success">' + escapeHtml(p.category_name) + '</span></td>' +
                        '<td>' + money(p.price) + '</td>' +
                        '<td>' + stockBadge + '</td></tr>';
                }).join('');
            }
        }

        // Low stock alert
        var lowStockContainer = document.getElementById('low-stock-list');
        if (lowStockContainer) {
            if (resp.low_stock_items.length === 0) {
                lowStockContainer.innerHTML = '<div class="text-success"><i class="bi bi-check-circle"></i> All products are well-stocked.</div>';
            } else {
                lowStockContainer.innerHTML = '<div class="row g-2">' + resp.low_stock_items.map(function (p) {
                    return '<div class="col-md-4"><a href="' + EcoMartAPI.siteBase + 'admin/product-edit.html?id=' + p.id + '" class="d-flex align-items-center text-decoration-none p-2 border rounded">' +
                        '<img src="' + p.image_url + '" alt="" class="product-thumb-sm me-2">' +
                        '<div class="flex-grow-1 small">' +
                        '<div class="fw-semibold text-body">' + escapeHtml(p.name) + '</div>' +
                        '<div class="text-warning small">' + p.stock + ' in stock</div>' +
                        '</div></a></div>';
                }).join('') + '</div>';
            }
        }
    }

    /* =========================================================
     *  PRODUCTS LIST
     * ========================================================= */
    async function loadProductsList(filters) {
        var tbody = document.getElementById('products-tbody');
        if (!tbody) return;
        tbody.innerHTML = '<tr><td colspan="7" class="text-center py-4"><i class="bi bi-hourglass-split"></i> Loading...</td></tr>';

        var resp = await EcoMartAPI.admin.getProducts(filters || {});
        if (!resp.success) {
            tbody.innerHTML = '<tr><td colspan="7" class="text-center text-danger py-4">Failed to load products.</td></tr>';
            return;
        }
        if (!resp.products.length) {
            tbody.innerHTML = '<tr><td colspan="7" class="text-center text-muted py-5">' +
                '<i class="bi bi-inbox fs-1 d-block mb-2"></i>' +
                'No products found. <a href="' + EcoMartAPI.siteBase + 'admin/product-edit.html">Add your first product</a>.</td></tr>';
            return;
        }
        tbody.innerHTML = resp.products.map(function (p) {
            var stockBadge = p.stock < 20
                ? '<span class="badge bg-warning text-dark">' + p.stock + '</span>'
                : '<span class="text-muted">' + p.stock + '</span>';
            var organicIcon = p.is_organic == 1
                ? '<i class="bi bi-check-circle-fill text-success"></i>'
                : '<i class="bi bi-dash-circle text-muted"></i>';
            return '<tr>' +
                '<td><img src="' + p.image_url + '" alt="" class="product-thumb-sm"></td>' +
                '<td><strong>' + escapeHtml(p.name) + '</strong><div class="small text-muted">#' + p.id + '</div></td>' +
                '<td><span class="badge bg-light text-success">' + escapeHtml(p.category_name) + '</span></td>' +
                '<td class="fw-semibold">' + money(p.price) + '</td>' +
                '<td>' + stockBadge + '</td>' +
                '<td>' + organicIcon + '</td>' +
                '<td class="text-end">' +
                '<a href="' + EcoMartAPI.siteBase + 'admin/product-edit.html?id=' + p.id + '" class="btn btn-sm btn-outline-success" title="Edit"><i class="bi bi-pencil"></i></a> ' +
                '<a href="' + EcoMartAPI.siteBase + 'product.html?id=' + p.id + '" target="_blank" class="btn btn-sm btn-outline-secondary" title="View"><i class="bi bi-eye"></i></a> ' +
                '<button type="button" class="btn btn-sm btn-outline-danger btn-delete-product" data-id="' + p.id + '" data-name="' + escapeAttr(p.name) + '" title="Delete"><i class="bi bi-trash"></i></button>' +
                '</td></tr>';
        }).join('');

        // Wire up delete buttons
        document.querySelectorAll('.btn-delete-product').forEach(function (btn) {
            btn.addEventListener('click', function () {
                var id = this.getAttribute('data-id');
                var name = this.getAttribute('data-name');
                showDeleteModal(id, name);
            });
        });

        // Update count
        var countEl = document.getElementById('product-count');
        if (countEl) countEl.textContent = resp.count;
    }

    function showDeleteModal(id, name) {
        var modalEl = document.getElementById('deleteModal');
        if (!modalEl) return;
        document.getElementById('delete-name').textContent = name;
        document.getElementById('delete-id').value = id;
        var modal = bootstrap.Modal.getInstance(modalEl) || new bootstrap.Modal(modalEl);
        modal.show();
    }

    function initDeleteModal() {
        var form = document.getElementById('delete-form');
        if (!form) return;
        form.addEventListener('submit', async function (e) {
            e.preventDefault();
            var id = document.getElementById('delete-id').value;
            var resp = await EcoMartAPI.admin.deleteProduct(id);
            if (resp.success) {
                bootstrap.Modal.getInstance(document.getElementById('deleteModal')).hide();
                showToast(resp.message || 'Product deleted.', 'success');
                loadProductsList(getCurrentFilters());
            } else {
                showToast(resp.message || 'Delete failed.', 'danger');
            }
        });
    }

    function getCurrentFilters() {
        var params = new URLSearchParams(window.location.search);
        return {
            category: params.get('category') || '',
            sort: params.get('sort') || 'name'
        };
    }

    function initQuickSearch() {
        var input = document.getElementById('quick-table-search');
        if (!input) return;
        input.addEventListener('keyup', function () {
            var q = this.value.toLowerCase();
            document.querySelectorAll('#products-tbody tr').forEach(function (row) {
                row.style.display = row.textContent.toLowerCase().indexOf(q) !== -1 ? '' : 'none';
            });
        });
    }

    /* =========================================================
     *  PRODUCT EDIT (ADD / EDIT)
     * ========================================================= */
    async function loadProductEditForm() {
        var form = document.getElementById('product-edit-form');
        if (!form) return;

        // Load categories into the dropdown
        var catSelect = document.getElementById('category_id');
        var catResp = await EcoMartAPI.getCategories();
        if (catResp.success) {
            catResp.categories.forEach(function (c) {
                var opt = document.createElement('option');
                opt.value = c.id;
                opt.textContent = c.name;
                catSelect.appendChild(opt);
            });
        }

        // If editing, load product data
        var params = new URLSearchParams(window.location.search);
        var editId = params.get('id');
        if (editId) {
            document.getElementById('form-title').textContent = 'Edit Product';
            var resp = await EcoMartAPI.admin.getProduct(editId);
            if (resp.success) {
                var p = resp.product;
                document.getElementById('product-id').value = p.id;
                document.getElementById('name').value = p.name;
                document.getElementById('category_id').value = p.category_id;
                document.getElementById('price').value = p.price;
                document.getElementById('description').value = p.description || '';
                document.getElementById('stock').value = p.stock;
                document.getElementById('is_organic').checked = p.is_organic == 1;
                var imgPreview = document.getElementById('current-image');
                if (imgPreview) imgPreview.src = p.image_url;
            } else {
                showToast('Product not found.', 'danger');
                setTimeout(function () {
                    window.location.href = EcoMartAPI.siteBase + 'admin/products.html';
                }, 1000);
            }
        }

        // Form submit
        form.addEventListener('submit', async function (e) {
            e.preventDefault();
            if (!form.checkValidity()) {
                form.classList.add('was-validated');
                return;
            }
            var formData = new FormData(form);
            // Make sure is_organic is sent even if unchecked
            if (!formData.has('is_organic')) formData.append('is_organic', '0');

            var btn = form.querySelector('button[type="submit"]');
            var original = btn.innerHTML;
            btn.disabled = true;
            btn.innerHTML = '<i class="bi bi-hourglass-split"></i> Saving...';

            var resp;
            if (formData.get('id')) {
                resp = await EcoMartAPI.admin.updateProduct(formData);
            } else {
                resp = await EcoMartAPI.admin.createProduct(formData);
            }

            if (resp.success) {
                showToast(resp.message || 'Saved successfully.', 'success');
                setTimeout(function () {
                    window.location.href = EcoMartAPI.siteBase + 'admin/products.html';
                }, 800);
            } else {
                showToast(resp.message || 'Save failed.', 'danger');
                btn.disabled = false;
                btn.innerHTML = original;
            }
        });
    }

    /* =========================================================
     *  SEARCH PAGE
     * ========================================================= */
    async function loadSearchResults() {
        var container = document.getElementById('search-results');
        if (!container) return;

        var params = new URLSearchParams(window.location.search);
        var filters = {
            keyword:   params.get('keyword')   || '',
            category:  params.get('category')  || '',
            min_price: params.get('min_price') || '',
            max_price: params.get('max_price') || '',
            sort:      params.get('sort')      || 'name'
        };

        // Fill form fields with current filters
        ['keyword', 'category', 'min_price', 'max_price', 'sort'].forEach(function (k) {
            var el = document.getElementById(k);
            if (el) el.value = filters[k];
        });

        // Load categories dropdown
        var catSelect = document.getElementById('category');
        if (catSelect && !catSelect.options.length) {
            var catResp = await EcoMartAPI.getCategories();
            if (catResp.success) {
                catResp.categories.forEach(function (c) {
                    var opt = document.createElement('option');
                    opt.value = c.slug;
                    opt.textContent = c.name;
                    catSelect.appendChild(opt);
                });
                catSelect.value = filters.category;
            }
        }

        container.innerHTML = '<div class="text-center py-5"><i class="bi bi-hourglass-split fs-3"></i><p class="mt-2">Searching...</p></div>';

        var resp = await EcoMartAPI.admin.getProducts(filters);
        if (!resp.success || !resp.products.length) {
            container.innerHTML =
                '<div class="card"><div class="card-body text-center py-5">' +
                '<i class="bi bi-search fs-1 text-muted"></i>' +
                '<h4 class="mt-3">No products match your search</h4>' +
                '<p class="text-muted">Try removing some filters or using a different keyword.</p>' +
                '<a href="' + EcoMartAPI.siteBase + 'admin/search.html" class="btn btn-outline-success">Reset Filters</a>' +
                '</div></div>';
            var countEl = document.getElementById('search-count');
            if (countEl) countEl.textContent = '0';
            return;
        }

        var countEl = document.getElementById('search-count');
        if (countEl) countEl.textContent = resp.count;

        container.innerHTML = '<div class="row g-3">' + resp.products.map(function (p) {
            return '<div class="col-md-6 col-lg-4">' +
                '<div class="card h-100">' +
                '<div class="row g-0">' +
                '<div class="col-4"><img src="' + p.image_url + '" alt="" class="img-fluid h-100" style="object-fit:cover;background:var(--admin-light);"></div>' +
                '<div class="col-8"><div class="card-body">' +
                '<span class="badge bg-light text-success mb-1">' + escapeHtml(p.category_name) + '</span>' +
                '<h6 class="card-title mb-1"><a href="' + EcoMartAPI.siteBase + 'admin/product-edit.html?id=' + p.id + '" class="text-decoration-none">' + escapeHtml(p.name) + '</a></h6>' +
                '<p class="card-text small text-muted mb-2">' + escapeHtml((p.description || '').substring(0, 80)) + '...</p>' +
                '<div class="d-flex justify-content-between align-items-center">' +
                '<span class="fw-bold text-success">' + money(p.price) + '</span>' +
                '<span class="small text-muted">Stock: ' + p.stock + '</span>' +
                '</div></div></div></div></div></div>';
        }).join('') + '</div>';
    }

    /* =========================================================
     *  ACTIVE SIDEBAR LINK
     * ========================================================= */
    function highlightActiveNav() {
        var path = window.location.pathname.split('/').pop();
        document.querySelectorAll('.admin-sidebar .nav-link').forEach(function (link) {
            var href = link.getAttribute('href') || '';
            var target = href.split('/').pop();
            if (target === path) link.classList.add('active');
            else link.classList.remove('active');
        });
    }

    /* =========================================================
     *  INIT
     * ========================================================= */
    document.addEventListener('DOMContentLoaded', async function () {
        // Don't load session if we're on the login page
        var onPage = window.location.pathname.split('/').pop();

        if (onPage !== 'login.html') {
            await loadAdminSession();
        }

        initLogoutButtons();
        highlightActiveNav();

        if (onPage === 'login.html') {
            initLoginPage();
        } else if (onPage === 'dashboard.html') {
            loadDashboard();
        } else if (onPage === 'products.html') {
            initDeleteModal();
            initQuickSearch();
            loadProductsList(getCurrentFilters());
        } else if (onPage === 'product-edit.html') {
            loadProductEditForm();
        } else if (onPage === 'search.html') {
            loadSearchResults();
        }
    });

    // Expose
    window.EcoMartAdmin = {
        showToast: showToast,
        loadProductsList: loadProductsList,
        getCurrentFilters: getCurrentFilters
    };
})();
