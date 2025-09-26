<?= $this->extend('templates/template') ?>

<?= $this->section('content') ?>
<div class="container">
    <div class="header-section">
        <h2>💸 Expense Management</h2>
        <div class="actions">
            <a href="<?= base_url('accountant/dashboard') ?>" class="btn btn-secondary">Back to Dashboard</a>
        </div>
    </div>

    <div class="expenses-section">
        <h4>Track Hospital Expenses</h4>
        <p>Monitor and manage hospital operational expenses.</p>
    </div>
</div>
<?= $this->endSection() ?>
