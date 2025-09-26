<?= $this->extend('templates/template') ?>

<?= $this->section('content') ?>
<div class="container">
    <div class="header-section">
        <h2>📄 Invoice Management</h2>
        <div class="actions">
            <a href="<?= base_url('accountant/dashboard') ?>" class="btn btn-secondary">Back to Dashboard</a>
        </div>
    </div>

    <div class="invoices-section">
        <h4>Generate and Manage Invoices</h4>
        <p>Create, send, and track patient invoices.</p>
    </div>
</div>
<?= $this->endSection() ?>
