<?= $this->extend('templates/template') ?>

<?= $this->section('content') ?>
<div class="container">
    <div class="header-section">
        <h2>📈 Pharmacy Reports</h2>
        <div class="actions">
            <a href="<?= base_url('pharmacist/dashboard') ?>" class="btn btn-secondary">Back to Dashboard</a>
        </div>
    </div>

    <div class="reports-section">
        <h4>Generate Pharmacy Reports</h4>
        <p>Create and manage pharmacy reports and analytics.</p>
    </div>
</div>
<?= $this->endSection() ?>
