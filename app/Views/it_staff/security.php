<?= $this->extend('templates/template') ?>

<?= $this->section('content') ?>
<div class="container">
    <div class="header-section">
        <h2>🛡️ Security Center</h2>
        <div class="actions">
            <a href="<?= base_url('it/dashboard') ?>" class="btn btn-secondary">Back to Dashboard</a>
        </div>
    </div>

    <div class="security-section">
        <h4>Security Management</h4>
        <p>Monitor security and manage access controls.</p>
    </div>
</div>
<?= $this->endSection() ?>
