<?= $this->extend('templates/template') ?>

<?= $this->section('content') ?>
<div class="container">
    <div class="header-section">
        <h2>🧾 Tax Management</h2>
        <div class="actions">
            <a href="<?= base_url('accountant/dashboard') ?>" class="btn btn-secondary">Back to Dashboard</a>
        </div>
    </div>

    <div class="taxes-section">
        <h4>Handle Tax Calculations</h4>
        <p>Manage tax calculations and compliance reporting.</p>
    </div>
</div>
<?= $this->endSection() ?>
