<?php include TEMPLATES_PATH . '/header.php'; ?>

<div class="admin-panel">
    <div class="admin-page-header">
        <h1 class="admin-page-title">📦 Product Import / Export</h1>
        <a href="dashboard" class="btn-secondary">← Back to Dashboard</a>
    </div>

    <?php if ($data['message']): ?>
        <div class="alert alert-success"><?php echo htmlspecialchars($data['message']); ?></div>
    <?php endif; ?>
    
    <?php if ($data['error']): ?>
        <div class="alert alert-error"><?php echo htmlspecialchars($data['error']); ?></div>
    <?php endif; ?>

    <!-- Import Section -->
    <div class="section">
        <h2 class="section-title">📥 Import Products (CSV)</h2>
        <p class="section-desc">
            Upload a CSV file to update existing products or add new ones.<br>
            <small>Required Columns: sku, name, price, description, stock, color, size, brand, emoji, image</small>
        </p>
        
        <form method="POST" enctype="multipart/form-data">
            <div class="form-group">
                <input type="file" name="csv_file" accept=".csv" required class="file-input">
            </div>
            <button type="submit" name="import_csv" class="btn-primary">Upload & Import</button>
        </form>
    </div>

    <!-- Export Section -->
    <div class="section">
        <h2 class="section-title">📤 Export Products</h2>
        <p class="section-desc">
            Download all product data in CSV format.
        </p>
        <form method="POST">
            <button type="submit" name="export_csv" class="btn-primary btn-success">Download CSV</button>
        </form>
    </div>
    
</div>

<?php include TEMPLATES_PATH . '/footer.php'; ?>
