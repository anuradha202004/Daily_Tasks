<?php include TEMPLATES_PATH . '/header.php'; ?>

<div class="admin-container">
    <div class="dashboard-header">
        <div>
            <h1 class="dashboard-title">🛠️ Admin Dashboard</h1>
            <p class="dashboard-subtitle">Welcome back, <?php echo htmlspecialchars($data['user']['name']); ?>.</p>
        </div>
        <a href="<?php echo URL_ROOT; ?>/../products" class="btn-back-store">
            <span style="margin-right: 8px;">←</span> Back to Store
        </a>
    </div>

    <div class="dashboard-grid">
        <!-- Product Management Card -->
        <a href="import_export" class="dashboard-card">
            <div class="card-icon-wrapper" style="background: #eff6ff; color: #2563eb;">📦</div>
            <h3 class="card-title">Product Import / Export</h3>
            <p class="card-desc">Bulk upload products via CSV to update stock and prices, or export your full catalog for backups.</p>
            <div class="card-footer">
                Manage Products 
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"></path></svg>
            </div>
        </a>

        <!-- Placeholder for Orders -->
        <div class="dashboard-card" style="opacity: 0.6; cursor: default; background: #fafafa;">
            <div class="card-icon-wrapper" style="background: #fdf2f8; color: #db2777;">🛍️</div>
            <h3 class="card-title">Order Management</h3>
            <p class="card-desc">View, process, and track customer orders. Update shipping status and handle cancellations.</p>
            <div class="card-footer" style="color: #9ca3af;">Coming Soon</div>
        </div>

        <!-- Placeholder for Users -->
        <div class="dashboard-card" style="opacity: 0.6; cursor: default; background: #fafafa;">
            <div class="card-icon-wrapper" style="background: #f0fdf4; color: #16a34a;">👥</div>
            <h3 class="card-title">User Management</h3>
            <p class="card-desc">Manage registered customers, view purchase history, and handle account roles.</p>
            <div class="card-footer" style="color: #9ca3af;">Coming Soon</div>
        </div>
        
        <!-- Placeholder for Analytics -->
        <div class="dashboard-card" style="opacity: 0.6; cursor: default; background: #fafafa;">
            <div class="card-icon-wrapper" style="background: #fffbeb; color: #d97706;">📊</div>
            <h3 class="card-title">Store Analytics</h3>
            <p class="card-desc">Visual insights into sales performance, revenue trends, and top-selling products.</p>
            <div class="card-footer" style="color: #9ca3af;">Coming Soon</div>
        </div>
    </div>
</div>

<?php include TEMPLATES_PATH . '/footer.php'; ?>
