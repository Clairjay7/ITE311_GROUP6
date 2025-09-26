<?= $this->extend('templates/template') ?>

<?= $this->section('content') ?>
<div class="container">
    <div class="header-section">
        <h2>📈 Laboratory Reports</h2>
        <div class="actions">
            <a href="<?= base_url('laboratory/dashboard') ?>" class="btn btn-secondary">Back to Dashboard</a>
        </div>
    </div>

    <div class="reports-section">
        <h4>Generate Laboratory Reports</h4>
        <p>Create and manage laboratory reports and analytics.</p>
    </div>
</div>
<?= $this->endSection() ?>
