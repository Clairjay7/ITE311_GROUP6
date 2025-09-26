<?= $this->extend('templates/template') ?>

<?= $this->section('content') ?>
<div class="container">
    <div class="header-section">
        <h2>💾 Backup Management</h2>
        <div class="actions">
            <a href="<?= base_url('it/dashboard') ?>" class="btn btn-secondary">Back to Dashboard</a>
        </div>
    </div>

    <div class="backups-section">
        <h4>System Backups</h4>
        <p>Create and manage system backups.</p>
    </div>
</div>
<?= $this->endSection() ?>
