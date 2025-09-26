<?= $this->extend('templates/template') ?>

<?= $this->section('content') ?>
<div class="container">
    <div class="header-section">
        <h2>📋 System Logs</h2>
        <div class="actions">
            <a href="<?= base_url('it/dashboard') ?>" class="btn btn-secondary">Back to Dashboard</a>
        </div>
    </div>

    <div class="logs-section">
        <h4>System & Error Logs</h4>
        <p>View and analyze system logs.</p>
    </div>
</div>
<?= $this->endSection() ?>
