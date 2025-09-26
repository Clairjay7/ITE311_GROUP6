<?= $this->extend('templates/template') ?>

<?= $this->section('content') ?>
<h1>💼 Accountant Dashboard</h1>
<p>Welcome back, <?= session()->get('username') ?? 'Accountant' ?>! Today is <?= date('F j, Y') ?></p>

<div class="spacer"></div>
<div class="grid grid-4">
    <div class="card">
        <h5>📋 Pending Bills</h5>
        <h3><?= esc($pendingBills ?? 15) ?></h3>
    </div>
    <div class="card">
        <h5>💰 Today's Revenue</h5>
        <h3>₱<?= number_format($todayRevenue ?? 25000, 0) ?></h3>
    </div>
    <div class="card">
        <h5>⚠️ Overdue Payments</h5>
        <h3><?= esc($overduePayments ?? 8) ?></h3>
    </div>
    <div class="card">
        <h5>📈 Monthly Revenue</h5>
        <h3>₱<?= number_format($monthlyRevenue ?? 450000, 0) ?></h3>
    </div>
</div>

<h2 class="section-title">Quick Actions</h2>
<div class="grid grid-4">
    <div class="card">
        <h5>📋 Billing Management</h5>
        <p>Manage patient bills and invoices</p>
        <div class="actions-row">
            <a href="<?= base_url('accountant/billing') ?>" class="btn">Manage Bills</a>
        </div>
    </div>
    <div class="card">
        <h5>💳 Payments</h5>
        <p>Process and track payments</p>
        <div class="actions-row">
            <a href="<?= base_url('accountant/payments') ?>" class="btn">View Payments</a>
        </div>
    </div>
    <div class="card">
        <h5>📄 Invoices</h5>
        <p>Generate and manage invoices</p>
        <div class="actions-row">
            <a href="<?= base_url('accountant/invoices') ?>" class="btn">Manage</a>
        </div>
    </div>
    <div class="card">
        <h5>📊 Financial Reports</h5>
        <p>Generate financial reports</p>
        <div class="actions-row">
            <a href="<?= base_url('accountant/reports') ?>" class="btn">Generate</a>
        </div>
    </div>
</div>

<div class="spacer"></div>
<div class="grid grid-4">
    <div class="card">
        <h5>💸 Expenses</h5>
        <p>Track hospital expenses</p>
        <div class="actions-row">
            <a href="<?= base_url('accountant/expenses') ?>" class="btn">Track Expenses</a>
        </div>
    </div>
    <div class="card">
        <h5>💰 Revenue Analysis</h5>
        <p>Analyze revenue streams</p>
        <div class="actions-row">
            <a href="<?= base_url('accountant/revenue') ?>" class="btn">Analyze</a>
        </div>
    </div>
    <div class="card">
        <h5>🧾 Tax Management</h5>
        <p>Handle tax calculations</p>
        <div class="actions-row">
            <a href="<?= base_url('accountant/taxes') ?>" class="btn">Manage Taxes</a>
        </div>
    </div>
    <div class="card">
        <h5>⚙️ Settings</h5>
        <p>Configure accounting settings</p>
        <div class="actions-row">
            <a href="<?= base_url('accountant/settings') ?>" class="btn">Configure</a>
        </div>
    </div>
</div>

<?= $this->endSection() ?>
