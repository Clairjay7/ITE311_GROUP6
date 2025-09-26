<?= $this->extend('templates/template') ?>

<?= $this->section('content') ?>
<h1>🧪 Laboratory Dashboard</h1>
<p>Welcome back, <?= session()->get('username') ?? 'Lab Tech' ?>! Today is <?= date('F j, Y') ?></p>

<div class="spacer"></div>
<div class="grid grid-4">
    <div class="card">
        <h5>⏳ Pending Tests</h5>
        <h3><?= esc($pendingTests ?? 6) ?></h3>
    </div>
    <div class="card">
        <h5>🔬 In Progress</h5>
        <h3><?= esc($inProgressTests ?? 5) ?></h3>
    </div>
    <div class="card">
        <h5>✅ Completed Today</h5>
        <h3><?= esc($completedToday ?? 12) ?></h3>
    </div>
    <div class="card">
        <h5>🚨 Urgent Tests</h5>
        <h3><?= esc($urgentTests ?? 2) ?></h3>
    </div>
</div>

<h2 class="section-title">Quick Actions</h2>
<div class="grid grid-4">
    <div class="card">
        <h5>🧪 All Tests</h5>
        <p>View and manage all lab tests</p>
        <div class="actions-row">
            <a href="<?= base_url('laboratory/tests') ?>" class="btn">View Tests</a>
        </div>
    </div>
    <div class="card">
        <h5>⏳ Pending Tests</h5>
        <p>Tests waiting to be processed</p>
        <div class="actions-row">
            <a href="<?= base_url('laboratory/pending') ?>" class="btn">View Pending</a>
        </div>
    </div>
    <div class="card">
        <h5>📊 Results</h5>
        <p>Enter and manage test results</p>
        <div class="actions-row">
            <a href="<?= base_url('laboratory/results') ?>" class="btn">Manage Results</a>
        </div>
    </div>
    <div class="card">
        <h5>📈 Reports</h5>
        <p>Generate laboratory reports</p>
        <div class="actions-row">
            <a href="<?= base_url('laboratory/reports') ?>" class="btn">Generate</a>
        </div>
    </div>
</div>

<?= $this->endSection() ?>
