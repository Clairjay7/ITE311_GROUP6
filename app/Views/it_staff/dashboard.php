<?= $this->extend('templates/template') ?>

<?= $this->section('content') ?>
<h1>💻 IT Staff Dashboard</h1>
<p>Welcome back, <?= session()->get('username') ?? 'IT Staff' ?>! Today is <?= date('F j, Y') ?></p>

<div class="spacer"></div>
<div class="grid grid-4">
    <div class="card">
        <h5>👥 Active Users</h5>
        <h3><?= esc($activeUsers ?? 45) ?></h3>
    </div>
    <div class="card">
        <h5>🚨 System Alerts</h5>
        <h3><?= esc($systemAlerts ?? 3) ?></h3>
    </div>
    <div class="card">
        <h5>💾 Last Backup</h5>
        <h3><?= esc($backupStatus ?? 'Completed') ?></h3>
    </div>
    <div class="card">
        <h5>⏱️ Server Uptime</h5>
        <h3><?= esc($serverUptime ?? '99.8%') ?></h3>
    </div>
</div>

<h2 class="section-title">Quick Actions</h2>
<div class="grid grid-4">
    <div class="card">
        <h5>👥 User Management</h5>
        <p>Manage system users and accounts</p>
        <div class="actions-row">
            <a href="<?= base_url('it/users') ?>" class="btn">Manage Users</a>
        </div>
    </div>
    <div class="card">
        <h5>🖥️ System Management</h5>
        <p>Monitor and configure systems</p>
        <div class="actions-row">
            <a href="<?= base_url('it/systems') ?>" class="btn">Manage Systems</a>
        </div>
    </div>
    <div class="card">
        <h5>🛡️ Security Center</h5>
        <p>Security settings and monitoring</p>
        <div class="actions-row">
            <a href="<?= base_url('it/security') ?>" class="btn">Security</a>
        </div>
    </div>
    <div class="card">
        <h5>💾 Backup Management</h5>
        <p>Create and manage backups</p>
        <div class="actions-row">
            <a href="<?= base_url('it/backups') ?>" class="btn">Manage Backups</a>
        </div>
    </div>
</div>

<div class="spacer"></div>
<div class="grid grid-4">
    <div class="card">
        <h5>📊 System Monitoring</h5>
        <p>Monitor system performance</p>
        <div class="actions-row">
            <a href="<?= base_url('it/monitoring') ?>" class="btn">Monitor</a>
        </div>
    </div>
    <div class="card">
        <h5>🔧 Maintenance</h5>
        <p>System maintenance tasks</p>
        <div class="actions-row">
            <a href="<?= base_url('it/maintenance') ?>" class="btn">Maintenance</a>
        </div>
    </div>
    <div class="card">
        <h5>📋 System Logs</h5>
        <p>View system and error logs</p>
        <div class="actions-row">
            <a href="<?= base_url('it/logs') ?>" class="btn">View Logs</a>
        </div>
    </div>
    <div class="card">
        <h5>⚙️ Settings</h5>
        <p>Configure system settings</p>
        <div class="actions-row">
            <a href="<?= base_url('it/settings') ?>" class="btn">Configure</a>
        </div>
    </div>
</div>

<?= $this->endSection() ?>
