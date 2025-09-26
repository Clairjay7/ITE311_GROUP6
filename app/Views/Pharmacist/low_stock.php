<?= $this->extend('templates/template') ?>

<?= $this->section('content') ?>
<div class="container">
    <div class="header-section">
        <h2>⚠️ Low Stock Items</h2>
        <div class="actions">
            <a href="<?= base_url('pharmacist/dashboard') ?>" class="btn btn-secondary">Back to Dashboard</a>
        </div>
    </div>

    <div class="low-stock-section">
        <h4>Items Running Low on Stock</h4>
        <p>Monitor and reorder items that are running low on stock.</p>
    </div>
</div>
<?= $this->endSection() ?>
