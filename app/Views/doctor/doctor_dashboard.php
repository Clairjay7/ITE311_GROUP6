<?= $this->extend('templates/template') ?>

<?= $this->section('content') ?>
<h1>👨‍⚕️ Doctor Dashboard</h1>
<p>Manage your patients, appointments, and medical records.</p>

<div class="spacer"></div>
<div class="grid grid-4">
    <div class="card"><h5>👥 Active Patients</h5><h3><?= esc($activePatients ?? 0) ?></h3></div>
    <div class="card"><h5>📅 Today's Appointments</h5><h3><?= esc($todaysAppointments ?? 0) ?></h3></div>
    <div class="card"><h5>📋 Pending Reports</h5><h3><?= esc($pendingReports ?? 0) ?></h3></div>
    <div class="card"><h5>🏥 Emergency Cases</h5><h3><?= esc($emergencyCases ?? 0) ?></h3></div>
    
</div>

<div class="spacer"></div>
<div class="grid grid-2">
    <div class="card">
        <h5>📅 Today's Appointments</h5>
        <div class="actions-row">
            <span>09:00 • John Smith • Follow-up</span>
        </div>
        <div class="actions-row">
            <span>10:30 • Maria Garcia • Consultation</span>
        </div>
        <div class="actions-row">
            <span>14:00 • Robert Johnson • Surgery Prep</span>
        </div>
    </div>
    <div class="card">
        <h5>📊 Recent Patient Updates</h5>
        <div class="actions-row"><span>💊 Sarah Wilson • Medication updated</span></div>
        <div class="actions-row"><span>🔬 Mike Davis • Lab results ready</span></div>
        <div class="actions-row"><span>📝 Lisa Brown • Discharge summary completed</span></div>
    </div>
</div>

<div class="card">
    <h5>⚡ Quick Actions</h5>
    <div class="actions-row">
        <a href="<?= base_url('doctor/patients') ?>" class="btn">View Patients</a>
        <a href="<?= base_url('doctor/appointments') ?>" class="btn btn-secondary">Manage Appointments</a>
        <a href="<?= base_url('doctor/reports') ?>" class="btn btn-secondary">Reports & Analytics</a>
        <a href="<?= base_url('doctor/prescriptions') ?>" class="btn btn-secondary">Prescribe Medication</a>
        <a href="<?= base_url('doctor/notifications') ?>" class="btn btn-secondary">Notifications</a>
        <a href="<?= base_url('doctor/messaging') ?>" class="btn btn-secondary">Messaging</a>
        <a href="<?= base_url('doctor/lab/requests') ?>" class="btn btn-secondary">Lab Requests</a>
        <a href="<?= base_url('doctor/lab/results') ?>" class="btn btn-secondary">Lab Results</a>
        <a href="<?= base_url('doctor/profile') ?>" class="btn btn-secondary">Profile</a>
        <a href="<?= base_url('doctor/settings') ?>" class="btn btn-secondary">Settings</a>
    </div>
    
</div>
<?= $this->endSection() ?>
