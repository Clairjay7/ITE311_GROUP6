<?= $this->extend('templates/template') ?>

<?= $this->section('content') ?>
<div class="container">
    <div class="header-section">
        <h2>📊 Financial Reports</h2>
        <div class="actions">
            <a href="<?= base_url('accountant/dashboard') ?>" class="btn btn-secondary">Back to Dashboard</a>
        </div>
    </div>

    <div class="reports-section">
        <h4>Generate Financial Reports</h4>
        <p>Create comprehensive financial reports and analytics.</p>
    </div>
</div>
<?= $this->endSection() ?>
