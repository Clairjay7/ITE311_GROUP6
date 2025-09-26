<?= $this->extend('templates/template') ?>

<?= $this->section('content') ?>
<h1>🧑‍⚕️ Nurse Dashboard</h1>
<p>Welcome back, <?= session()->get('username') ?? 'Nurse' ?>! Current Shift: <?= esc($shiftWindow ?? '07:00 - 15:00') ?></p>

<div class="spacer"></div>
<div class="grid grid-4">
    <div class="card">
        <h5>👥 Assigned Patients</h5>
        <h3><?= esc($assignedPatientsCount ?? 12) ?></h3>
    </div>
    <div class="card">
        <h5>💊 Due Medications</h5>
        <h3><?= esc($dueMedicationsCount ?? 5) ?></h3>
    </div>
    <div class="card">
        <h5>📝 Pending Tasks</h5>
        <h3><?= esc($openTasksCount ?? 8) ?></h3>
    </div>
    <div class="card">
        <h5>🔔 Critical Alerts</h5>
        <h3>3</h3>
    </div>
</div>

<h2 class="section-title">Quick Actions</h2>
<div class="grid grid-4">
    <div class="card">
        <h5>👥 My Patients</h5>
        <p>View and manage assigned patients</p>
        <div class="actions-row">
            <a href="<?= base_url('nurse/patients') ?>" class="btn">View Patients</a>
        </div>
    </div>
    <div class="card">
        <h5>💊 Medications</h5>
        <p>Medication schedule and administration</p>
        <div class="actions-row">
            <a href="<?= base_url('nurse/medications') ?>" class="btn">Manage Meds</a>
        </div>
    </div>
    <div class="card">
        <h5>🩺 Vitals</h5>
        <p>Record and monitor patient vitals</p>
        <div class="actions-row">
            <a href="<?= base_url('nurse/vitals') ?>" class="btn">Record Vitals</a>
        </div>
    </div>
    <div class="card">
        <h5>📋 Tasks</h5>
        <p>View and complete nursing tasks</p>
        <div class="actions-row">
            <a href="<?= base_url('nurse/tasks') ?>" class="btn">View Tasks</a>
        </div>
    </div>
</div>

<?= $this->endSection() ?>
