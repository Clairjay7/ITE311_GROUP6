<?= $this->extend('templates/template') ?>

<?= $this->section('content') ?>
<h1>💊 Pharmacist Dashboard</h1>
<p>Welcome back, <?= session()->get('username') ?? 'Pharmacist' ?>! Today is <?= date('F j, Y') ?></p>

<div class="spacer"></div>
<div class="grid grid-4">
    <div class="card">
        <h5>📝 Pending Prescriptions</h5>
        <h3><?= esc($pendingPrescriptions ?? 8) ?></h3>
    </div>
    <div class="card">
        <h5>✅ Dispensed Today</h5>
        <h3><?= esc($dispensedToday ?? 24) ?></h3>
    </div>
    <div class="card">
        <h5>📦 Low Stock Items</h5>
        <h3><?= esc($lowStockItems ?? 3) ?></h3>
    </div>
    <div class="card">
        <h5>⏰ Expiring Soon</h5>
        <h3><?= esc($expiringSoon ?? 5) ?></h3>
    </div>
</div>

<h2 class="section-title">Quick Actions</h2>
<div class="grid grid-4">
    <div class="card">
        <h5>💊 All Prescriptions</h5>
        <p>View and manage all prescriptions</p>
        <div class="actions-row">
            <a href="<?= base_url('pharmacist/prescriptions') ?>" class="btn">View All</a>
        </div>
    </div>
    <div class="card">
        <h5>📝 Pending Prescriptions</h5>
        <p>Prescriptions waiting to be dispensed</p>
        <div class="actions-row">
            <a href="<?= base_url('pharmacist/pending') ?>" class="btn">View Pending</a>
        </div>
    </div>
    <div class="card">
        <h5>✅ Dispensed</h5>
        <p>Recently dispensed prescriptions</p>
        <div class="actions-row">
            <a href="<?= base_url('pharmacist/dispensed') ?>" class="btn">View Dispensed</a>
        </div>
    </div>
    <div class="card">
        <h5>📦 Inventory</h5>
        <p>Manage drug inventory</p>
        <div class="actions-row">
            <a href="<?= base_url('pharmacist/inventory') ?>" class="btn">Manage</a>
        </div>
    </div>
</div>

<div class="spacer"></div>
<div class="grid grid-4">
    <div class="card">
        <h5>⚠️ Low Stock</h5>
        <p>Items running low on stock</p>
        <div class="actions-row">
            <a href="<?= base_url('pharmacist/low-stock') ?>" class="btn">Check Stock</a>
        </div>
    </div>
    <div class="card">
        <h5>⏰ Expiring Items</h5>
        <p>Drugs expiring soon</p>
        <div class="actions-row">
            <a href="<?= base_url('pharmacist/expiring') ?>" class="btn">View Expiring</a>
        </div>
    </div>
    <div class="card">
        <h5>🛒 Orders</h5>
        <p>Manage drug orders</p>
        <div class="actions-row">
            <a href="<?= base_url('pharmacist/orders') ?>" class="btn">Manage Orders</a>
        </div>
    </div>
    <div class="card">
        <h5>📈 Reports</h5>
        <p>Generate pharmacy reports</p>
        <div class="actions-row">
            <a href="<?= base_url('pharmacist/reports') ?>" class="btn">Generate</a>
        </div>
    </div>
</div>

<?= $this->endSection() ?>
