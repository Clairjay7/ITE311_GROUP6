<div class="sidebar">
    <div class="logo">
        <h2>St. Peter Hospital
            <span class="subtitle">Healthcare Management</span>
        </h2>
    </div>

    <ul class="nav-menu">
        <?php $role = session()->get('role'); ?>

        <!-- ADMIN -->
        <?php if ($role === 'admin'): ?>
            <li class="nav-item">
                <a href="<?= base_url('admin/dashboard') ?>">
                    <span class="text">Dashboard</span>
                </a>
            </li>

            <!-- Patient Records -->
            <li class="nav-item expandable">
                <a href="#" onclick="toggleSubmenu(this)">
                    <span class="text">Patient Records</span>
                    <span class="arrow">›</span>
                </a>
                <ul class="submenu">
                    <li><a href="<?= base_url('admin/patients/register') ?>"><span class="text">New Out-Patient Entry</span></a></li>
                    <li><a href="<?= base_url('admin/patients/inpatient') ?>"><span class="text">New In-Patient Entry</span></a></li>
                    <li><a href="<?= base_url('patients/view') ?>"><span class="text">Patient Directory</span></a></li>
                </ul>
            </li>

            <!-- Scheduling -->
            <li class="nav-item expandable">
                <a href="#" onclick="toggleSubmenu(this)">
                    <span class="text">Scheduling</span>
                    <span class="arrow">›</span>
                </a>
                <ul class="submenu">
                    <li><a href="<?= base_url('appointments/book') ?>"><span class="text">Create Appointment</span></a></li>
                    <li><a href="<?= site_url('appointments/list') ?>"><span class="text">Appointment Overview</span></a></li>
                    <li><a href="<?= site_url('doctor/schedule') ?>"><span class="text">Staff Timetable</span></a></li>
                </ul>
            </li>

            <!-- Billing -->
            <li class="nav-item expandable">
                <a href="#" onclick="toggleSubmenu(this)">
                    <span class="text">Billing Services</span>
                    <span class="arrow">›</span>
                </a>
                <ul class="submenu">
                    <li><a href="<?= base_url('billing') ?>"><span class="text">Billing Overview</span></a></li>
                    <li><a href="<?= base_url('billing/process') ?>"><span class="text">Process Billing</span></a></li>
                </ul>
            </li>

            <!-- Laboratory -->
            <li class="nav-item expandable">
                <a href="#" onclick="toggleSubmenu(this)">
                    <span class="text">Lab Services</span>
                    <span class="arrow">›</span>
                </a>
                <ul class="submenu">
                    <li><a href="<?= base_url('laboratory/request') ?>"><span class="text">Request Laboratory Test</span></a></li>
                    <li><a href="<?= base_url('laboratory/testresult') ?>"><span class="text">Laboratory Results</span></a></li>
                </ul>
            </li>

            <!-- Pharmacy -->
            <li class="nav-item expandable">
                <a href="#" onclick="toggleSubmenu(this)">
                    <span class="text">Pharmacy Desk</span>
                    <span class="arrow">›</span>
                </a>
                <ul class="submenu">
                    <li><a href="<?= base_url('admin/InventoryMan/PrescriptionDispencing') ?>"><span class="text">Prescription Processing</span></a></li>
                    <li><a href="<?= site_url('admin/pharmacy/transactions') ?>"><span class="text">Pharmacy Transactions</span></a></li>
                </ul>
            </li>

            <!-- Inventory -->
            <li class="nav-item">
                <a href="<?= site_url('admin/inventory/medicine') ?>">
                    <span class="text">Stock Monitoring</span>
                </a>
            </li>

            <!-- System Controls -->
            <li class="nav-item expandable">
                <a href="#" onclick="toggleSubmenu(this)">
                    <span class="text">System Controls</span>
                    <span class="arrow">›</span>
                </a>
                <ul class="submenu">
                    <li><a href="<?= base_url('admin/Administration/ManageUser') ?>"><span class="text">User Accounts</span></a></li>
                    <li><a href="<?= base_url('admin/Administration/RoleManagement') ?>"><span class="text">Access Roles</span></a></li>
                </ul>
            </li>
        <?php endif; ?>

        <!-- DOCTOR -->
        <?php if ($role === 'doctor'): ?>
            <li class="nav-item">
                <a href="<?= site_url('doctor/dashboard') ?>">
                    <span class="text">Dashboard</span>
                </a>
            </li>

            <li class="nav-item expandable">
                <a href="#" onclick="toggleSubmenu(this)">
                    <span class="text">Patient List</span>
                    <span class="arrow">›</span>
                </a>
                <ul class="submenu">
                    <li><a href="<?= site_url('doctor/patients/view') ?>"><span class="text">Patient Records</span></a></li>
                </ul>
            </li>

            <li class="nav-item expandable">
                <a href="#" onclick="toggleSubmenu(this)">
                    <span class="text">Consultation Schedule</span>
                    <span class="arrow">›</span>
                </a>
                <ul class="submenu">
                    <li><a href="<?= site_url('doctor/appointments') ?>"><span class="text">Upcoming Consultations</span></a></li>
                    <li><a href="<?= site_url('doctor/schedule') ?>"><span class="text">My Schedule</span></a></li>
                </ul>
            </li>
        <?php endif; ?>

        <!-- NURSE -->
        <?php if ($role === 'nurse'): ?>
            <li class="nav-item">
                <a href="<?= site_url('nurse/dashboard') ?>">
                    <span class="text">Dashboard</span>
                </a>
            </li>

            <li class="nav-item expandable">
                <a href="#" onclick="toggleSubmenu(this)">
                    <span class="text">Patient Handling</span>
                    <span class="arrow">›</span>
                </a>
                <ul class="submenu">
                    <li><a href="<?= site_url('nurse/patients/view') ?>"><span class="text">Patient Information</span></a></li>
                </ul>
            </li>

            <li class="nav-item expandable">
                <a href="#" onclick="toggleSubmenu(this)">
                    <span class="text">Appointment Queue</span>
                    <span class="arrow">›</span>
                </a>
                <ul class="submenu">
                    <li><a href="<?= site_url('nurse/appointments/list') ?>"><span class="text">Appointment Overview</span></a></li>
                </ul>
            </li>

            <li class="nav-item expandable">
                <a href="#" onclick="toggleSubmenu(this)">
                    <span class="text">Lab Assistance</span>
                    <span class="arrow">›</span>
                </a>
                <ul class="submenu">
                    <li><a href="<?= site_url('nurse/laboratory/request') ?>"><span class="text">Create Lab Request</span></a></li>
                    <li><a href="<?= site_url('nurse/laboratory/testresult') ?>"><span class="text">Results Inquiry</span></a></li>
                </ul>
            </li>
        <?php endif; ?>

        <!-- RECEPTIONIST -->
        <?php if ($role === 'receptionist'): ?>
            <li class="nav-item">
                <a href="<?= site_url('receptionist/dashboard') ?>">
                    <span class="text">Dashboard</span>
                </a>
            </li>

            <li class="nav-item expandable">
                <a href="#" onclick="toggleSubmenu(this)">
                    <span class="text">Patient Registration</span>
                    <span class="arrow">›</span>
                </a>
                <ul class="submenu">
                    <li><a href="<?= site_url('receptionist/patients/register') ?>"><span class="text">Out-Patient Entry</span></a></li>
                    <li><a href="<?= site_url('receptionist/patients/inpatient') ?>"><span class="text">In-Patient Entry</span></a></li>
                    <li><a href="<?= site_url('receptionist/patients/view') ?>"><span class="text">Patient Records</span></a></li>
                </ul>
            </li>

            <li class="nav-item expandable">
                <a href="#" onclick="toggleSubmenu(this)">
                    <span class="text">Appointment Desk</span>
                    <span class="arrow">›</span>
                </a>
                <ul class="submenu">
                    <li><a href="<?= site_url('receptionist/appointments/book') ?>"><span class="text">New Appointment</span></a></li>
                    <li><a href="<?= site_url('receptionist/appointments/list') ?>"><span class="text">Appointment Tracker</span></a></li>
                </ul>
            </li>
        <?php endif; ?>

        <!-- PATIENT -->
        <?php if ($role === 'patient'): ?>
            <li class="nav-item">
                <a href="<?= site_url('patient/dashboard') ?>">
                    <span class="text">Dashboard</span>
                </a>
            </li>

            <li class="nav-item">
                <a href="<?= site_url('patient/appointments') ?>">
                    <span class="text">My Appointments</span>
                </a>
            </li>

            <li class="nav-item">
                <a href="<?= site_url('patient/records') ?>">
                    <span class="text">My Records</span>
                </a>
            </li>

            <li class="nav-item">
                <a href="<?= site_url('patient/billing') ?>">
                    <span class="text">Billing History</span>
                </a>
            </li>
        <?php endif; ?>

        <!-- ACCOUNTING -->
        <?php if ($role === 'accounting'): ?>
            <li class="nav-item">
                <a href="<?= site_url('accounting/dashboard') ?>">
                    <span class="text">Dashboard</span>
                </a>
            </li>

            <li class="nav-item">
                <a href="<?= site_url('accounting/finance') ?>">
                    <span class="text">Finance Overview</span>
                </a>
            </li>

            <li class="nav-item">
                <a href="<?= site_url('accounting/payments') ?>">
                    <span class="text">Payment Reports</span>
                </a>
            </li>

            <li class="nav-item">
                <a href="<?= site_url('accounting/expenses') ?>">
                    <span class="text">Expense Tracking</span>
                </a>
            </li>
        <?php endif; ?>

        <!-- IT STAFF -->
        <?php if ($role === 'itstaff'): ?>
            <li class="nav-item">
                <a href="<?= site_url('it/dashboard') ?>">
                    <span class="text">Dashboard</span>
                </a>
            </li>

            <li class="nav-item">
                <a href="<?= site_url('it/logs') ?>">
                    <span class="text">System Logs</span>
                </a>
            </li>

            <li class="nav-item">
                <a href="<?= site_url('it/users') ?>">
                    <span class="text">User Management</span>
                </a>
            </li>

            <li class="nav-item expandable">
                <a href="#" onclick="toggleSubmenu(this)">
                    <span class="text">Backup/Restore</span>
                    <span class="arrow">›</span>
                </a>
                <ul class="submenu">
                    <li><a href="<?= site_url('it/backup') ?>"><span class="text">Create Backup</span></a></li>
                    <li><a href="<?= site_url('it/restore') ?>"><span class="text">Restore System</span></a></li>
                </ul>
            </li>
        <?php endif; ?>

        <!-- LAB STAFF -->
        <?php if ($role === 'lab_staff'): ?>
            <li class="nav-item">
                <a href="<?= site_url('lab/dashboard') ?>">
                    <span class="text">Dashboard</span>
                </a>
            </li>

            <li class="nav-item">
                <a href="<?= site_url('lab/requests') ?>">
                    <span class="text">Test Requests</span>
                </a>
            </li>

            <li class="nav-item">
                <a href="<?= site_url('lab/pending') ?>">
                    <span class="text">Pending Specimens</span>
                </a>
            </li>

            <li class="nav-item">
                <a href="<?= site_url('lab/completed') ?>">
                    <span class="text">Completed Tests</span>
                </a>
            </li>
        <?php endif; ?>

        <!-- PHARMACIST -->
        <?php if ($role === 'pharmacist'): ?>
            <li class="nav-item">
                <a href="<?= site_url('pharmacy/dashboard') ?>">
                    <span class="text">Dashboard</span>
                </a>
            </li>

            <li class="nav-item">
                <a href="<?= site_url('pharmacy/prescriptions') ?>">
                    <span class="text">Prescription Queue</span>
                </a>
            </li>

            <li class="nav-item">
                <a href="<?= site_url('pharmacy/release') ?>">
                    <span class="text">Medicine Release</span>
                </a>
            </li>

            <li class="nav-item">
                <a href="<?= site_url('pharmacy/stock') ?>">
                    <span class="text">Stock Monitoring</span>
                </a>
            </li>
        <?php endif; ?>

        <!-- COMMON -->
        <li class="nav-item">
            <a href="<?= site_url('auth/logout') ?>">
                <span class="text">Logout</span>
            </a>
        </li>
    </ul>
</div>
