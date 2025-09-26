<?= $this->extend('templates/template') ?>

<?= $this->section('content') ?>
<div class="container">
    <div class="header-section">
        <h2>👥 User Management</h2>
        <div class="actions">
            <a href="<?= base_url('it/dashboard') ?>" class="btn btn-secondary">Back to Dashboard</a>
        </div>
    </div>

    <div class="users-section">
        <h4>System Users</h4>
        <p>Manage user accounts and permissions.</p>
    </div>
</div>
<?= $this->endSection() ?>
