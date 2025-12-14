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
            <li class="nav-section-title">Admin Panel</li>
            <li class="nav-item">
                <a href="<?= base_url('admin/dashboard') ?>">
                    <i class="fa-solid fa-gauge sidebar-icon"></i>
                    <span class="">Dashboard</span>
                </a>
            </li>

            <!-- Patient Records -->
            <li class="nav-item">
                <a href="<?= base_url('admin/patients') ?>">
                    <i class="fa-solid fa-hospital-user sidebar-icon"></i>
                    <span class="text">Patient Records</span>
                </a>
            </li>

            <!-- Scheduling -->
            <li class="nav-item">
                <a href="<?= base_url('admin/schedule') ?>">
                    <i class="fa-regular fa-calendar-check sidebar-icon"></i>
                    <span class="text">Scheduling</span>
                </a>
            </li>

            <!-- Billing -->
            <li class="nav-item">
                <a href="<?= base_url('admin/billing') ?>">
                    <i class="fa-solid fa-file-invoice-dollar sidebar-icon"></i>
                    <span class="text">Billing Services</span>
                </a>
            </li>

            <!-- Laboratory -->
            <li class="nav-item">
                <a href="<?= base_url('admin/lab') ?>">
                    <i class="fa-solid fa-vials sidebar-icon"></i>
                    <span class="text">Lab Services</span>
                </a>
            </li>

            <!-- Pharmacy -->
            <li class="nav-item">
                <a href="<?= base_url('admin/pharmacy') ?>">
                    <i class="fa-solid fa-pills sidebar-icon"></i>
                    <span class="text">Pharmacy Desk</span>
                </a>
            </li>

            <!-- Inventory -->
            <li class="nav-item">
                <a href="<?= base_url('admin/stock') ?>">
                    <i class="fa-solid fa-boxes-stacked sidebar-icon"></i>
                    <span class="text">Stock Monitoring</span>
                </a>
            </li>

            <!-- System Controls -->
            <li class="nav-item">
                <a href="<?= base_url('admin/system/dashboard') ?>">
                    <i class="fa-solid fa-gear sidebar-icon"></i>
                    <span class="text">System Controls</span>
                </a>
            </li>

            <!-- User Management -->
            <li class="nav-item">
                <a href="<?= base_url('admin/users') ?>">
                    <i class="fa-solid fa-users-cog sidebar-icon"></i>
                    <span class="text">User Management</span>
                </a>
            </li>
        <?php endif; ?>

        <!-- DOCTOR -->
        <?php if ($role === 'doctor'): ?>
            <li class="nav-section-title">Doctor Panel</li>
            <li class="nav-item">
                <a href="<?= site_url('doctor/dashboard') ?>">
                    <i class="fa-solid fa-gauge sidebar-icon"></i>
                    <span class="text">Dashboard</span>
                </a>
            </li>

            <li class="nav-item">
                <a href="<?= site_url('doctor/patients') ?>">
                    <i class="fa-solid fa-hospital-user sidebar-icon"></i>
                    <span class="text">Patient List</span>
                </a>
            </li>


            <?php
            // Check if doctor is a pediatrician
            $isPediatricsDoctor = false;
            if ($role === 'doctor') {
                $db = \Config\Database::connect();
                $doctorId = session()->get('user_id');
                if ($db->tableExists('doctors') && $doctorId) {
                    $doctor = $db->table('doctors')
                        ->where('user_id', $doctorId)
                        ->get()
                        ->getRowArray();
                    if ($doctor && strtolower(trim($doctor['specialization'] ?? '')) === 'pediatrics') {
                        $isPediatricsDoctor = true;
                    }
                }
            }
            ?>
            <?php if ($isPediatricsDoctor): ?>
            <li class="nav-item">
                <a href="<?= site_url('doctor/consultations/pediatrics') ?>">
                    <i class="fa-solid fa-child sidebar-icon"></i>
                    <span class="text">Pediatrics Consultations</span>
                </a>
            </li>
            <?php endif; ?>

            <li class="nav-item">
                <a href="<?= site_url('doctor/lab-requests') ?>">
                    <i class="fa-solid fa-vial sidebar-icon"></i>
                    <span class="text">Lab Requests</span>
                </a>
            </li>

            <li class="nav-item">
                <a href="<?= site_url('doctor/orders') ?>">
                    <i class="fa-solid fa-prescription sidebar-icon"></i>
                    <span class="text">Doctor Orders</span>
                </a>
            </li>


            <li class="nav-item">
                <a href="<?= site_url('doctor/discharge') ?>">
                    <i class="fa-solid fa-sign-out-alt sidebar-icon"></i>
                    <span class="text">Discharge Patients</span>
                </a>
            </li>
        <?php endif; ?>

        <!-- NURSE -->
        <?php if ($role === 'nurse'): ?>
            <li class="nav-section-title">Nurse Panel</li>
            <li class="nav-item">
                <a href="<?= site_url('nurse/dashboard') ?>">
                    <i class="fa-solid fa-user-nurse sidebar-icon"></i>
                    <span class="text">Dashboard</span>
                </a>
            </li>

            <li class="nav-item">
                <a href="<?= site_url('nurse/triage') ?>">
                    <i class="fa-solid fa-stethoscope sidebar-icon"></i>
                    <span class="text">Emergency Triage</span>
                </a>
            </li>

            <li class="nav-item expandable">
                <a href="#" onclick="toggleSubmenu(this)">
                    <span class="text">Patient Handling</span>
                    <span class="arrow">›</span>
                </a>
                <ul class="submenu">
                    <li><a href="<?= site_url('nurse/patients/view') ?>"><span class="text">Patient Information</span></a></li>
                    <li>
                        <a href="<?= site_url('nurse/medications') ?>">
                            <i class="fa-solid fa-pills sidebar-icon"></i>
                            <span class="text">Medication Administration</span>
                        </a>
                    </li>
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

            <li class="nav-item">
                <a href="<?= site_url('nurse/discharge') ?>">
                    <i class="fa-solid fa-sign-out-alt sidebar-icon"></i>
                    <span class="text">Discharge Preparation</span>
                </a>
            </li>
        <?php endif; ?>

        <!-- RECEPTIONIST -->
        <?php if ($role === 'receptionist'): ?>
            <li class="nav-section-title">Reception Desk</li>
            <li class="nav-item">
                <a href="<?= site_url('receptionist/dashboard') ?>">
                    <i class=""></i>
                    <span class="text">Dashboard</span>
                </a>
            </li>

            <li class="nav-item expandable">
                <a href="#" onclick="toggleSubmenu(this)">
                    <span class="text">Patient Registration</span>
                    <span class="arrow">›</span>
                </a>
                <ul class="submenu">
                    <li><a href="<?= site_url('receptionist/patients/register') ?>"><span class="text">Register In-Patient</span></a></li>
                    <li><a href="<?= site_url('receptionist/patients/outpatient') ?>"><span class="text">Register Out-Patient</span></a></li>
                    <li><a href="<?= site_url('receptionist/patients') ?>"><span class="text">Patient Records</span></a></li>
                </ul>
            </li>

            <li class="nav-item expandable">
                <a href="#" onclick="toggleSubmenu(this)">
                    <span class="text">Appointment Desk</span>
                    <span class="arrow">›</span>
                </a>
                <ul class="submenu">
                    <li><a href="<?= site_url('appointments/book') ?>"><span class="text">New Appointment</span></a></li>
                    <li><a href="<?= site_url('appointments/list') ?>"><span class="text">Appointment Tracker</span></a></li>
                </ul>
            </li>

            <li class="nav-item expandable">
                <a href="#" onclick="toggleSubmenu(this)">
                    <span class="text">Room Management</span>
                    <span class="arrow">›</span>
                </a>
                <ul class="submenu">
                    <li><a href="<?= site_url('receptionist/rooms/type/private') ?>"><span class="text">🏠 Private Room</span></a></li>
                    <li><a href="<?= site_url('receptionist/rooms/type/semi-private') ?>"><span class="text">🏘️ Semi-Private Room</span></a></li>
                    <li><a href="<?= site_url('receptionist/rooms/type/ward') ?>"><span class="text">🏥 Ward (General Ward)</span></a></li>
                    <li><a href="<?= site_url('receptionist/rooms/type/icu') ?>"><span class="text">🚨 ICU (Intensive Care Unit)</span></a></li>
                    <li><a href="<?= site_url('receptionist/rooms/type/isolation') ?>"><span class="text">🔒 Isolation Room</span></a></li>
                    <li><a href="<?= site_url('receptionist/rooms/type/nicu') ?>"><span class="text">👶 NICU (Neonatal Intensive Care Unit)</span></a></li>
                    <li><a href="<?= site_url('receptionist/rooms/type/or') ?>"><span class="text">⚕️ OR (Operating Room)</span></a></li>
                </ul>
            </li>

            <li class="nav-item">
                <a href="<?= site_url('receptionist/schedule') ?>">
                    <i class="fa-solid fa-calendar-alt sidebar-icon"></i>
                    <span class="text">View Schedules</span>
                </a>
            </li>
        <?php endif; ?>

        <!-- PATIENT -->
        <?php if ($role === 'patient'): ?>
            <li class="nav-section-title">Patient Portal</li>
            <li class="nav-item">
                <a href="<?= site_url('patient/dashboard') ?>">
                    <i class="fa-solid fa-user sidebar-icon"></i>
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

        <!-- FINANCE -->
        <?php if ($role === 'finance'): ?>
            <li class="nav-section-title">Finance Panel</li>
            <li class="nav-item">
                <a href="<?= site_url('accounting/dashboard') ?>">
                    <i class="fa-solid fa-peso-sign sidebar-icon"></i>
                    <span class="text">Dashboard</span>
                </a>
            </li>

            <li class="nav-item">
                <a href="<?= site_url('accounting/patient-billing') ?>">
                    <i class="fa-solid fa-user-injured sidebar-icon"></i>
                    <span class="text">Patient Billing</span>
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

            <li class="nav-item">
                <a href="<?= site_url('accounting/discharge') ?>">
                    <i class="fa-solid fa-sign-out-alt sidebar-icon"></i>
                    <span class="text">Discharge Billing</span>
                </a>
            </li>
        <?php endif; ?>

        <!-- IT STAFF -->
        <?php if ($role === 'itstaff'): ?>
            <li class="nav-section-title">IT Administration</li>
            <li class="nav-item">
                <a href="<?= site_url('it/dashboard') ?>">
                    <i class="fa-solid fa-computer sidebar-icon"></i>
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
                    <i class="fa-solid fa-users-cog sidebar-icon"></i>
                    <span class="text">User Management</span>
                </a>
            </li>

            <li class="nav-item">
                <a href="<?= site_url('it/backup') ?>">
                    <i class="fa-solid fa-database sidebar-icon"></i>
                    <span class="text">Create Backup</span>
                </a>
            </li>

            <li class="nav-item">
                <a href="<?= site_url('it/restore') ?>">
                    <i class="fa-solid fa-rotate-left sidebar-icon"></i>
                    <span class="text">Restore System</span>
                </a>
            </li>
        <?php endif; ?>

        <!-- LAB STAFF -->
        <?php if ($role === 'labstaff' || $role === 'lab_staff'): ?>
            <li class="nav-section-title">Laboratory Panel</li>
            <li class="nav-item">
                <a href="<?= site_url('labstaff/dashboard') ?>">
                    <i class="fa-solid fa-microscope sidebar-icon"></i>
                    <span class="text">Dashboard</span>
                </a>
            </li>
            <li class="nav-item">
                <a href="<?= site_url('labstaff/test-requests') ?>">
                    <i class="fa-solid fa-flask sidebar-icon"></i>
                    <span class="text">Test Requests</span>
                </a>
            </li>
            <li class="nav-item">
                <a href="<?= site_url('labstaff/pending-specimens') ?>">
                    <i class="fa-solid fa-vial sidebar-icon"></i>
                    <span class="text">Pending Specimens</span>
                </a>
            </li>
            <li class="nav-item">
                <a href="<?= site_url('labstaff/completed-tests') ?>">
                    <i class="fa-solid fa-check-circle sidebar-icon"></i>
                    <span class="text">Completed Tests</span>
                </a>
            </li>
        <?php endif; ?>

        <!-- PHARMACY -->
        <?php if ($role === 'pharmacy'): ?>
            <li class="nav-section-title">Pharmacy Panel</li>
            <li class="nav-item">
                <a href="<?= site_url('pharmacy') ?>">
                    <i class="fa-solid fa-briefcase-medical sidebar-icon"></i>
                    <span class="text">Dashboard</span>
                </a>
            </li>

            <li class="nav-item">
                <a href="<?= site_url('pharmacy/prescription-queue') ?>">
                    <i class="fa-solid fa-prescription sidebar-icon"></i>
                    <span class="text">Prescription Queue</span>
                </a>
            </li>

            <li class="nav-item">
                <a href="<?= site_url('pharmacy/medicine-release') ?>">
                    <i class="fa-solid fa-pills sidebar-icon"></i>
                    <span class="text">Medicine Release</span>
                </a>
            </li>

            <li class="nav-item">
                <a href="<?= site_url('pharmacy/stock-monitoring') ?>">
                    <i class="fa-solid fa-boxes-stacked sidebar-icon"></i>
                    <span class="text">Stock Monitoring</span>
                </a>
            </li>
        <?php endif; ?>

        <!-- COMMON -->
        <li class="nav-item">
            <a href="<?= site_url('auth/logout') ?>">
                <i class="fa-solid fa-sign-out-alt sidebar-icon"></i>
                <span class="text">Logout</span>
            </a>
        </li>
    </ul>
</div>
