<?= $this->extend('templates/template') ?>

<?= $this->section('content') ?>
<div class="container">
    <div class="header-section">
        <h2>📊 System Monitoring</h2>
        <div class="actions">
            <a href="<?= base_url('it/dashboard') ?>" class="btn btn-secondary">Back to Dashboard</a>
        </div>
    </div>

    <div class="monitoring-section">
        <h4>Performance Monitoring</h4>
        <p>Monitor system performance and health.</p>
    </div>
</div>
<?= $this->endSection() ?>
