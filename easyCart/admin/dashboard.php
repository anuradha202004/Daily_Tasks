<?php
require_once dirname(__DIR__) . '/app/bootstrap.php';

// Ensure user is admin
requireAdmin();

$pageTitle = 'Admin Dashboard';

// Include Header (which opens <html>, <head>, <body>, and <header>)
include dirname(__DIR__) . '/resources/templates/header.php'; 
?>

<!-- Internal Styles for Dashboard -->
<style>
    /* Admin Dashboard Specific Styles */
    body { background-color: #f3f4f6; } /* Override default if needed */
    .admin-container { max-width: 1000px; margin: 40px auto; padding: 20px; }
    .dashboard-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px; }
    .dashboard-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(300px, 1fr)); gap: 25px; }
    
    .dashboard-card { 
        background: white; 
        padding: 30px; 
        border-radius: 16px; 
        box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05); 
        border: 1px solid #f0f0f0;
        transition: all 0.2s ease; 
        text-decoration: none; 
        color: inherit; 
        display: flex;
        flex-direction: column;
        height: 100%;
    }
    .dashboard-card:hover { 
        transform: translateY(-5px); 
        box-shadow: 0 20px 25px -5px rgba(0,0,0,0.1), 0 10px 10px -5px rgba(0,0,0,0.04); 
        border-color: #e5e7eb;
    }
    
    .card-icon-wrapper {
        margin-bottom: 20px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 60px;
        height: 60px;
        border-radius: 12px;
        font-size: 28px;
    }
    
    .card-title { font-size: 19px; font-weight: 700; margin: 0 0 10px 0; color: #111827; }
    .card-desc { font-size: 15px; color: #6b7280; margin: 0; line-height: 1.6; flex-grow: 1; }
    .card-footer { margin-top: 20px; font-size: 14px; font-weight: 600; color: #2563eb; display: flex; align-items: center; }
    .card-footer svg { width: 16px; height: 16px; margin-left: 5px; transition: transform 0.2s; }
    .dashboard-card:hover .card-footer svg { transform: translateX(3px); }

    .btn-back-store { 
        background: white; 
        color: #374151; 
        padding: 10px 16px; 
        border-radius: 8px; 
        text-decoration: none; 
        font-weight: 500; 
        border: 1px solid #e5e7eb; 
        display: inline-flex;
        align-items: center;
        transition: all 0.2s;
    }
    .btn-back-store:hover { background: #f9fafb; border-color: #d1d5db; }
</style>

<div class="admin-container">
    <div class="dashboard-header">
        <div>
            <h1 style="margin: 0; color: #111827; font-size: 28px;">🛠️ Admin Dashboard</h1>
            <p style="margin: 8px 0 0 0; color: #6b7280; font-size: 16px;">Welcome back, <?php echo htmlspecialchars($currentUser['name']); ?>.</p>
        </div>
        <a href="../products" class="btn-back-store">
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

<?php 
// Footer
include dirname(__DIR__) . '/resources/templates/footer.php'; 
?>
