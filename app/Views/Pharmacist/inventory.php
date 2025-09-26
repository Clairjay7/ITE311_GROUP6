<?= $this->extend('templates/template') ?>

<?= $this->section('content') ?>
<div class="container">
    <div class="header-section">
        <h2>📦 Pharmacy Inventory</h2>
        <div class="actions">
            <a href="<?= base_url('pharmacist/dashboard') ?>" class="btn btn-secondary">Back to Dashboard</a>
        </div>
    </div>

    <div class="inventory-section">
        <h4>Drug Inventory Management</h4>
        <p>Manage pharmacy inventory, stock levels, and expiry dates.</p>
    </div>
</div>
<?= $this->endSection() ?>
