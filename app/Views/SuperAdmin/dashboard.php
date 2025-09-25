<?= $this->extend('templates/template') ?>

<?= $this->section('content') ?>
<h1>🔑 Super Admin Dashboard</h1>
<p>Manage the entire hospital system from one place.</p>

<!-- Quick Statistics -->
<div class="spacer"></div>
<div class="grid grid-4">
    <div class="card">
        <h5>👥 Patients Today</h5>
        <h3>—</h3>
        <small>Admitted/Discharged</small>
    </div>
    <div class="card">
        <h5>📅 Appointments</h5>
        <h3>—</h3>
        <small>Today / Upcoming</small>
    </div>
    <div class="card">
        <h5>🩺 Doctors On Duty</h5>
        <h3>—</h3>
        <small>Current Shift</small>
    </div>
    <div class="card">
        <h5>🛏️ Available Rooms</h5>
        <h3>—</h3>
        <small>Wards/ICU</small>
    </div>
</div>

<!-- Management Sections -->
<div class="spacer"></div>
<div class="grid grid-2">
    <div class="card">
        <h5>👥 User Management</h5>
        <p>Manage doctors, nurses, staff, and patient accounts.</p>
        <div class="actions-row">
            <a href="<?= base_url('super-admin/users') ?>" class="btn">Open Users</a>
            <a href="<?= base_url('super-admin/roles') ?>" class="btn btn-secondary">Roles & Permissions</a>
        </div>
    </div>
    <div class="card">
        <h5>📅 Appointments & Scheduling</h5>
        <p>View all appointments, calendars, and manage schedules.</p>
        <div class="actions-row">
            <a href="<?= base_url('super-admin/appointments') ?>" class="btn">Manage Appointments</a>
            <a href="<?= base_url('super-admin/calendars') ?>" class="btn btn-secondary">Calendar View</a>
        </div>
    </div>
</div>

<div class="grid grid-2">
    <div class="card">
        <h5>🏥 Patient Management</h5>
        <p>Records, treatments, admissions, discharges, and bed allocation.</p>
        <div class="actions-row">
            <a href="<?= base_url('super-admin/patients') ?>" class="btn">Patient Records</a>
            <a href="<?= base_url('super-admin/admissions') ?>" class="btn btn-secondary">Admissions & Discharges</a>
        </div>
    </div>
    <div class="card">
        <h5>🩺 Doctor & Staff Management</h5>
        <p>Profiles, schedules, and duty rosters.</p>
        <div class="actions-row">
            <a href="<?= base_url('super-admin/doctors') ?>" class="btn">Doctors</a>
            <a href="<?= base_url('super-admin/staff') ?>" class="btn btn-secondary">Nurses & Staff</a>
        </div>
    </div>
</div>

<div class="grid grid-2">
    <div class="card">
        <h5>💰 Billing & Finance</h5>
        <p>Billing, payments, receipts, insurance claims, and revenue reports.</p>
        <div class="actions-row">
            <a href="<?= base_url('super-admin/billing') ?>" class="btn">Billing</a>
            <a href="<?= base_url('super-admin/finance/reports') ?>" class="btn btn-secondary">Finance Reports</a>
        </div>
    </div>
    <div class="card">
        <h5>🧪 Laboratory & Pharmacy</h5>
        <p>Test requests/results, medicines stock and expiry alerts.</p>
        <div class="actions-row">
            <a href="<?= base_url('super-admin/laboratory') ?>" class="btn">Laboratory</a>
            <a href="<?= base_url('super-admin/pharmacy') ?>" class="btn btn-secondary">Pharmacy</a>
        </div>
    </div>
</div>

<div class="grid grid-2">
    <div class="card">
        <h5>🛏️ Room/Bed Management</h5>
        <p>Ward/room/ICU availability and occupancy status.</p>
        <div class="actions-row">
            <a href="<?= base_url('super-admin/rooms') ?>" class="btn">Manage Rooms</a>
            <a href="<?= base_url('super-admin/occupancy') ?>" class="btn btn-secondary">Occupancy</a>
        </div>
    </div>
    <div class="card">
        <h5>📊 Reports & Analytics</h5>
        <p>Operational, financial, inventory, and performance reports.</p>
        <div class="actions-row">
            <a href="<?= base_url('super-admin/reports') ?>" class="btn">Open Reports</a>
            <a href="<?= base_url('super-admin/analytics') ?>" class="btn btn-secondary">Analytics</a>
        </div>
    </div>
</div>

<div class="spacer"></div>
<div class="card">
    <h5>⚙️ System Settings</h5>
    <p>Departments, fees/rates, backup/restore, and security settings.</p>
    <div class="actions-row">
        <a href="<?= base_url('super-admin/settings') ?>" class="btn">Open Settings</a>
        <a href="<?= base_url('super-admin/security') ?>" class="btn btn-secondary">Security</a>
    </div>
</div>
<?= $this->endSection() ?>
