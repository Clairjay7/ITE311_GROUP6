<?= $this->extend('templates/template') ?>

<?= $this->section('content') ?>
<div class="unified-dashboard">
    <div class="dashboard-header">
        <h1>🔑 Super Admin - Unified Dashboard</h1>
        <p>Complete hospital management system in one place</p>
    </div>

    <!-- Quick Statistics -->
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-icon">🩺</div>
            <div class="stat-content">
                <h3><?= esc($totalDoctors ?? 0) ?></h3>
                <p>Total Doctors</p>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon">👥</div>
            <div class="stat-content">
                <h3><?= esc($totalPatients ?? 0) ?></h3>
                <p>Total Patients</p>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon">📅</div>
            <div class="stat-content">
                <h3><?= esc($todaysAppointments ?? 0) ?></h3>
                <p>Today's Appointments</p>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon">💵</div>
            <div class="stat-content">
                <h3><?= esc($pendingBills ?? 0) ?></h3>
                <p>Pending Bills</p>
            </div>
        </div>
    </div>

    <!-- Navigation Tabs -->
    <div class="tab-navigation">
        <button class="tab-btn active" onclick="showTab('users')">👥 Users & Roles</button>
        <button class="tab-btn" onclick="showTab('appointments')">📅 Appointments</button>
        <button class="tab-btn" onclick="showTab('patients')">🏥 Patients</button>
        <button class="tab-btn" onclick="showTab('medical')">🧪 Medical Services</button>
        <button class="tab-btn" onclick="showTab('finance')">💰 Finance</button>
        <button class="tab-btn" onclick="showTab('facilities')">🛏️ Facilities</button>
        <button class="tab-btn" onclick="showTab('reports')">📊 Reports</button>
        <button class="tab-btn" onclick="showTab('settings')">⚙️ Settings</button>
    </div>

    <!-- Tab Contents -->
    
    <!-- Users & Roles Tab -->
    <div id="users-tab" class="tab-content active">
        <div class="section-header">
            <h2>👥 User Management & Roles</h2>
            <div class="section-actions">
                <button class="btn btn-primary" onclick="showModal('addUserModal')">➕ Add User</button>
                <button class="btn btn-secondary" onclick="showModal('addRoleModal')">🔑 Add Role</button>
                <button class="btn btn-info" onclick="loadAllUsers()">👥 View All Users</button>
            </div>
        </div>
        
        <div class="content-grid">
            <div class="content-card">
                <h3>User Management</h3>
                <div class="search-bar">
                    <input type="text" id="user-search" placeholder="Search users..." onkeyup="searchUsers()">
                </div>
                <div class="table-container">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Username</th>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Role</th>
                                <th>Phone</th>
                                <th>Status</th>
                                <th>Last Login</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody id="users-table">
                            <!-- Users will be loaded here -->
                        </tbody>
                    </table>
                </div>
            </div>
            
            <div class="content-card">
                <h3>Roles & Permissions</h3>
                <div class="roles-list" id="roles-list">
                    <!-- Roles will be loaded here -->
                </div>
                <div class="role-actions">
                    <button class="btn btn-sm btn-primary" onclick="showModal('addRoleModal')">Add Role</button>
                    <button class="btn btn-sm btn-secondary" onclick="loadRoles()">Refresh</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Appointments Tab -->
    <div id="appointments-tab" class="tab-content">
        <div class="section-header">
            <h2>📅 Appointments & Scheduling</h2>
            <div class="section-actions">
                <button class="btn btn-primary" onclick="showModal('addAppointmentModal')">➕ New Appointment</button>
                <button class="btn btn-secondary" onclick="toggleCalendarView()">📅 Calendar View</button>
                <button class="btn btn-info" onclick="loadAppointments()">🔄 Refresh</button>
            </div>
        </div>
        
        <div class="content-grid">
            <div class="content-card">
                <h3>Appointment Management</h3>
                <div class="search-bar">
                    <input type="text" id="appointment-search" placeholder="Search appointments..." onkeyup="searchAppointments()">
                </div>
                <div class="table-container">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Appointment ID</th>
                                <th>Patient Name</th>
                                <th>Patient Phone</th>
                                <th>Doctor Name</th>
                                <th>Department</th>
                                <th>Date</th>
                                <th>Time</th>
                                <th>Type</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody id="appointments-table">
                            <!-- Appointments will be loaded here -->
                        </tbody>
                    </table>
                </div>
            </div>
            
            <div class="content-card">
                <h3>Calendar Overview</h3>
                <div id="calendar-widget">
                    <!-- Mini calendar will be loaded here -->
                </div>
                <div class="appointment-stats">
                    <div class="stat-item">
                        <span class="stat-number" id="today-appointments">0</span>
                        <span class="stat-label">Today</span>
                    </div>
                    <div class="stat-item">
                        <span class="stat-number" id="pending-appointments">0</span>
                        <span class="stat-label">Pending</span>
                    </div>
                    <div class="stat-item">
                        <span class="stat-number" id="completed-appointments">0</span>
                        <span class="stat-label">Completed</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Patients Tab -->
    <div id="patients-tab" class="tab-content">
        <div class="section-header">
            <h2>🏥 Patient Management</h2>
            <div class="section-actions">
                <button class="btn btn-primary" onclick="showModal('addPatientModal')">➕ Add Patient</button>
                <button class="btn btn-secondary" onclick="showModal('admissionModal')">🏥 Admit Patient</button>
                <button class="btn btn-info" onclick="loadPatients()">🔄 Refresh</button>
            </div>
        </div>
        
        <div class="content-grid">
            <div class="content-card">
                <h3>Patient Records</h3>
                <div class="search-bar">
                    <input type="text" id="patient-search" placeholder="Search patients..." onkeyup="searchPatients()">
                </div>
                <div class="table-container">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Patient ID</th>
                                <th>Name</th>
                                <th>Contact Number</th>
                                <th>Phone</th>
                                <th>Email</th>
                                <th>Gender</th>
                                <th>Blood Type</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody id="patients-table">
                            <!-- Patients will be loaded here -->
                        </tbody>
                    </table>
                </div>
            </div>
            
            <div class="content-card">
                <h3>Patient Statistics</h3>
                <div class="patient-stats">
                    <div class="stat-item">
                        <span class="stat-number" id="total-patients">0</span>
                        <span class="stat-label">Total Patients</span>
                    </div>
                    <div class="stat-item">
                        <span class="stat-number" id="active-patients">0</span>
                        <span class="stat-label">Active</span>
                    </div>
                    <div class="stat-item">
                        <span class="stat-number" id="admitted-patients">0</span>
                        <span class="stat-label">Admitted</span>
                    </div>
                </div>
                
                <h4>Quick Actions</h4>
                <div class="quick-actions">
                    <button class="btn btn-sm btn-primary" onclick="showModal('addPatientModal')">Add New Patient</button>
                    <button class="btn btn-sm btn-info" onclick="exportPatients()">Export List</button>
                    <button class="btn btn-sm btn-warning" onclick="showPatientReports()">View Reports</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Medical Services Tab -->
    <div id="medical-tab" class="tab-content">
        <div class="section-header">
            <h2>🧪 Medical Services Management</h2>
            <div class="section-actions">
                <button class="btn btn-primary" onclick="window.location.href='<?= base_url('medical-services') ?>'">📋 Manage Services</button>
                <button class="btn btn-secondary" onclick="window.location.href='<?= base_url('medical-services/create') ?>'">➕ Add Service</button>
                <button class="btn btn-info" onclick="loadMedicalServices()">🔄 Refresh Data</button>
            </div>
        </div>
        
        <div class="services-grid">
            <div class="service-card">
                <div class="service-icon">🧪</div>
                <h3>Medical Services</h3>
                <p>Manage hospital services, pricing, and categories</p>
                <div class="service-stats" id="medical-services-stats">
                    <span>Total Services: <span id="total-services">Loading...</span></span>
                    <span>Active Services: <span id="active-services">Loading...</span></span>
                </div>
                <div class="service-actions">
                    <button class="btn btn-sm" onclick="window.location.href='<?= base_url('medical-services') ?>'">View All</button>
                </div>
            </div>
            
            <div class="service-card">
                <div class="service-icon">📊</div>
                <h3>Service Analytics</h3>
                <p>Pricing insights and service performance metrics</p>
                <div class="service-stats" id="analytics-stats">
                    <span>Avg. Consultation: <span id="avg-consultation-price">Loading...</span></span>
                    <span>Avg. Lab Test: <span id="avg-laboratory-price">Loading...</span></span>
                </div>
                <div class="service-actions">
                    <button class="btn btn-sm" onclick="showServiceAnalytics()">View Analytics</button>
                </div>
            </div>
            
            <div class="service-card">
                <div class="service-icon">📑</div>
                <h3>Reports & Analytics <span class="badge badge-soon">Coming Soon</span></h3>
                <p>Generate reports on medication usage, costs, and compliance.</p>
                <ul class="service-list">
                    <li>Dispensing performance insights</li>
                    <li>Cost trend analysis</li>
                    <li>Medication usage statistics</li>
                    <li>Compliance tracking summaries</li>
                </ul>
                <div class="service-actions">
                    <button class="btn btn-sm btn-primary" onclick="window.location.href='<?= base_url('super-admin/reports') ?>'">View Reports</button>
                </div>
            </div>

            <div class="service-card">
                <div class="service-icon">📦</div>
                <h3>Inventory Management <span class="badge badge-soon">Coming Soon</span></h3>
                <p>Track stock levels, expiration alerts, and supplier orders.</p>
                <ul class="service-list">
                    <li>Monitor current inventory</li>
                    <li>Highlight low-stock items</li>
                    <li>Watch upcoming expirations</li>
                    <li>Coordinate supplier replenishment</li>
                </ul>
                <div class="service-actions">
                    <button class="btn btn-sm btn-primary" onclick="window.location.href='<?= base_url('super-admin/inventory') ?>'">Manage Inventory</button>
                </div>
            </div>

            <div class="service-card">
                <div class="service-icon">💊</div>
                <h3>Pharmacy</h3>
                <p>Medicine inventory, prescriptions, and stock alerts</p>
                <div class="service-stats">
                    <span>Low Stock Items: 0</span>
                    <span>Expiring Soon: 0</span>
                </div>
                <div class="service-actions">
                    <button class="btn btn-sm btn-primary" onclick="window.location.href='<?= base_url('admin/prescriptions') ?>'">Manage Prescriptions</button>
                    <button class="btn btn-sm" onclick="window.location.href='<?= base_url('super-admin/pharmacy') ?>'">Manage Pharmacy</button>
                </div>
            </div>
            
            <div class="service-card">
                <div class="service-icon">🔬</div>
                <h3>Laboratory</h3>
                <p>Test requests, results, and equipment management</p>
                <div class="service-stats">
                    <span>Pending Tests: 0</span>
                    <span>Completed Today: 0</span>
                </div>
                <div class="service-actions">
                    <button class="btn btn-sm" onclick="window.location.href='<?= base_url('super-admin/laboratory') ?>'">Manage Lab</button>
                </div>
            </div>

            <div class="service-card">
                <div class="service-icon">🧪</div>
                <h3>Test Management <span class="badge badge-soon">Coming Soon</span></h3>
                <p>Oversee lab tests, patient samples, and quality workflows.</p>
                <ul class="service-list">
                    <li>Process laboratory test requests</li>
                    <li>Track sample IDs and custody</li>
                    <li>Record and publish test results</li>
                    <li>Monitor quality control checkpoints</li>
                </ul>
                <div class="service-actions">
                    <button class="btn btn-sm btn-primary" onclick="window.location.href='<?= base_url('super-admin/tests') ?>'">Manage Tests</button>
                </div>
            </div>

            <div class="service-card">
                <div class="service-icon">🔬</div>
                <h3>Equipment Management <span class="badge badge-soon">Coming Soon</span></h3>
                <p>Monitor laboratory devices, maintenance, and calibration schedules.</p>
                <ul class="service-list">
                    <li>Track operational status and availability</li>
                    <li>Schedule preventive maintenance tasks</li>
                    <li>Record calibration activities and reminders</li>
                    <li>Review usage hours and condition updates</li>
                </ul>
                <div class="service-actions">
                    <button class="btn btn-sm btn-primary" onclick="window.location.href='<?= base_url('super-admin/equipment') ?>'">Manage Equipment</button>
                </div>
            </div>

            <div class="service-card">
                <div class="service-icon">📊</div>
                <h3>Lab Reports &amp; Analytics <span class="badge badge-soon">Coming Soon</span></h3>
                <p>Generate data-driven lab reports and monitor testing performance.</p>
                <ul class="service-list">
                    <li>Summaries of completed and pending tests</li>
                    <li>Turnaround time tracking and benchmarks</li>
                    <li>Quality and compliance metrics</li>
                    <li>Equipment utilization insights</li>
                </ul>
                <div class="service-actions">
                    <button class="btn btn-sm btn-primary" onclick="window.location.href='<?= base_url('super-admin/lab-reports') ?>'">View Reports</button>
                </div>
            </div>
        </div>

        <!-- Quick Services Overview -->
        <div class="content-card" style="margin-top: 20px;">
            <h3>Recent Medical Services</h3>
            <div class="table-container">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Service Name</th>
                            <th>Category</th>
                            <th>Price</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody id="recent-services-table">
                        <tr>
                            <td colspan="5" style="text-align: center; padding: 20px;">
                                Loading medical services...
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Finance Tab -->
    <div id="finance-tab" class="tab-content">
        <div class="section-header">
            <h2>💰 Financial Management</h2>
        </div>
        
        <div class="finance-grid">
            <div class="finance-card">
                <h3>Revenue Overview</h3>
                <div class="revenue-stats">
                    <div class="revenue-item">
                        <span class="amount">₱0.00</span>
                        <span class="label">Today's Revenue</span>
                    </div>
                    <div class="revenue-item">
                        <span class="amount">₱0.00</span>
                        <span class="label">This Month</span>
                    </div>
                </div>
            </div>
            
            <div class="finance-card">
                <h3>Billing Status</h3>
                <div class="billing-stats">
                    <div class="billing-item">
                        <span class="count">0</span>
                        <span class="label">Pending Bills</span>
                    </div>
                    <div class="billing-item">
                        <span class="count">0</span>
                        <span class="label">Paid Today</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Facilities Tab -->
    <div id="facilities-tab" class="tab-content">
        <div class="section-header">
            <h2>🛏️ Facilities Management</h2>
            <div class="section-actions">
                <button class="btn btn-primary" onclick="showModal('addRoomModal')">➕ Add Room</button>
            </div>
        </div>
        
        <div class="facilities-grid">
            <div class="facility-card">
                <h3>Room Occupancy</h3>
                <div class="occupancy-chart">
                    <div class="occupancy-item">
                        <span class="room-type">General Wards</span>
                        <div class="occupancy-bar">
                            <div class="occupancy-fill" style="width: 0%"></div>
                        </div>
                        <span class="occupancy-text">0/0</span>
                    </div>
                    <div class="occupancy-item">
                        <span class="room-type">ICU</span>
                        <div class="occupancy-bar">
                            <div class="occupancy-fill" style="width: 0%"></div>
                        </div>
                        <span class="occupancy-text">0/0</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Reports Tab -->
    <div id="reports-tab" class="tab-content">
        <div class="section-header">
            <h2>📊 Reports & Analytics</h2>
        </div>
        
        <div class="reports-grid">
            <div class="report-card">
                <h3>📈 Performance Reports</h3>
                <ul>
                    <li><a href="#" onclick="generateReport('daily')">Daily Operations Report</a></li>
                    <li><a href="#" onclick="generateReport('financial')">Financial Summary</a></li>
                    <li><a href="#" onclick="generateReport('patient')">Patient Statistics</a></li>
                </ul>
            </div>
            
            <div class="report-card">
                <h3>📋 System Analytics</h3>
                <ul>
                    <li><a href="#" onclick="generateReport('usage')">System Usage</a></li>
                    <li><a href="#" onclick="generateReport('performance')">Performance Metrics</a></li>
                    <li><a href="#" onclick="generateReport('audit')">Audit Logs</a></li>
                </ul>
            </div>
        </div>
    </div>

    <!-- Settings Tab -->
    <div id="settings-tab" class="tab-content">
        <div class="section-header">
            <h2>⚙️ System Settings</h2>
        </div>
        
        <div class="settings-grid">
            <div class="settings-card">
                <h3>🏥 Hospital Configuration</h3>
                <div class="setting-item">
                    <label>Hospital Name:</label>
                    <input type="text" value="Hospital Management System" class="setting-input">
                </div>
                <div class="setting-item">
                    <label>Contact Number:</label>
                    <input type="text" value="+63 123 456 7890" class="setting-input">
                </div>
            </div>
            
            <div class="settings-card">
                <h3>🔒 Security Settings</h3>
                <div class="setting-item">
                    <label>Session Timeout (minutes):</label>
                    <input type="number" value="30" class="setting-input">
                </div>
                <div class="setting-item">
                    <label>Password Policy:</label>
                    <select class="setting-input">
                        <option>Standard</option>
                        <option>Strong</option>
                        <option>Custom</option>
                    </select>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modals -->
<div id="modal-overlay" class="modal-overlay" onclick="closeModal()"></div>

<!-- Add User Modal -->
<div id="addUserModal" class="modal">
    <div class="modal-header">
        <h3>➕ Add New User</h3>
        <button class="modal-close" onclick="closeModal()">&times;</button>
    </div>
    <div class="modal-body">
        <form id="addUserForm">
            <div class="form-row">
                <div class="form-group">
                    <label>First Name:</label>
                    <input type="text" name="first_name" required>
                </div>
                <div class="form-group">
                    <label>Last Name:</label>
                    <input type="text" name="last_name" required>
                </div>
            </div>
            <div class="form-group">
                <label>Email:</label>
                <input type="email" name="email" required>
            </div>
            <div class="form-group">
                <label>Username:</label>
                <input type="text" name="username" required>
            </div>
            <div class="form-group">
                <label>Password:</label>
                <input type="password" name="password" required>
            </div>
            <div class="form-group">
                <label>Phone:</label>
                <input type="text" name="phone" placeholder="Contact number">
            </div>
            <div class="form-group">
                <label>Address:</label>
                <textarea name="address" rows="2" placeholder="Complete address"></textarea>
            </div>
            <div class="form-group">
                <label>Role:</label>
                <select name="role" required>
                    <option value="">Select Role</option>
                    <option value="doctor">Doctor</option>
                    <option value="nurse">Nurse</option>
                    <option value="receptionist">Receptionist</option>
                    <option value="laboratory">Laboratory</option>
                    <option value="pharmacist">Pharmacist</option>
                    <option value="accountant">Accountant</option>
                    <option value="it_staff">IT Staff</option>
                </select>
            </div>
            <div class="form-actions">
                <button type="submit" class="btn btn-primary">Add User</button>
                <button type="button" class="btn btn-secondary" onclick="closeModal()">Cancel</button>
            </div>
        </form>
    </div>
</div>

<!-- Edit User Modal -->
<div id="editUserModal" class="modal">
    <div class="modal-header">
        <h3>✏️ Edit User</h3>
        <button class="modal-close" onclick="closeModal()">&times;</button>
    </div>
    <div class="modal-body">
        <form id="editUserForm">
            <div class="form-row">
                <div class="form-group">
                    <label>First Name:</label>
                    <input type="text" name="first_name" required>
                </div>
                <div class="form-group">
                    <label>Last Name:</label>
                    <input type="text" name="last_name" required>
                </div>
            </div>
            <div class="form-group">
                <label>Email:</label>
                <input type="email" name="email" required>
            </div>
            <div class="form-group">
                <label>Username:</label>
                <input type="text" name="username" required>
            </div>
            <div class="form-group">
                <label>Phone:</label>
                <input type="text" name="phone" placeholder="Contact number">
            </div>
            <div class="form-group">
                <label>Address:</label>
                <textarea name="address" rows="2" placeholder="Complete address"></textarea>
            </div>
            <div class="form-group">
                <label>Role:</label>
                <select name="role" required>
                    <option value="">Select Role</option>
                    <option value="superadmin">Super Admin</option>
                    <option value="doctor">Doctor</option>
                    <option value="nurse">Nurse</option>
                    <option value="receptionist">Receptionist</option>
                    <option value="laboratory">Laboratory</option>
                    <option value="pharmacist">Pharmacist</option>
                    <option value="accountant">Accountant</option>
                    <option value="it_staff">IT Staff</option>
                </select>
            </div>
            <div class="form-group">
                <label>Status:</label>
                <select name="status" required>
                    <option value="active">Active</option>
                    <option value="inactive">Inactive</option>
                    <option value="suspended">Suspended</option>
                </select>
            </div>
            <div class="form-actions">
                <button type="submit" class="btn btn-primary">Update User</button>
                <button type="button" class="btn btn-secondary" onclick="closeModal()">Cancel</button>
            </div>
        </form>
    </div>
</div>

<!-- Edit Appointment Modal -->
<div id="editAppointmentModal" class="modal">
    <div class="modal-header">
        <h3>✏️ Edit Appointment</h3>
        <button class="modal-close" onclick="closeModal()">&times;</button>
    </div>
    <div class="modal-body">
        <form id="editAppointmentForm">
            <div class="form-row">
                <div class="form-group">
                    <label>Patient Name:</label>
                    <input type="text" name="patient_name" required placeholder="Full name of patient">
                </div>
                <div class="form-group">
                    <label>Patient Phone:</label>
                    <input type="text" name="patient_phone" placeholder="Contact number">
                </div>
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label>Doctor Name:</label>
                    <input type="text" name="doctor_name" required placeholder="Attending doctor">
                </div>
                <div class="form-group">
                    <label>Department:</label>
                    <select name="department">
                        <option value="">Select Department</option>
                        <option value="General Medicine">General Medicine</option>
                        <option value="Cardiology">Cardiology</option>
                        <option value="Pediatrics">Pediatrics</option>
                        <option value="Orthopedics">Orthopedics</option>
                        <option value="Neurology">Neurology</option>
                        <option value="Emergency">Emergency</option>
                        <option value="Surgery">Surgery</option>
                    </select>
                </div>
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label>Date:</label>
                    <input type="date" name="appointment_date" required>
                </div>
                <div class="form-group">
                    <label>Time:</label>
                    <input type="time" name="appointment_time" required>
                </div>
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label>Appointment Type:</label>
                    <select name="appointment_type" required>
                        <option value="">Select Type</option>
                        <option value="consultation">Consultation</option>
                        <option value="follow-up">Follow-up</option>
                        <option value="emergency">Emergency</option>
                        <option value="surgery">Surgery</option>
                        <option value="therapy">Therapy</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Status:</label>
                    <select name="status">
                        <option value="pending">Pending</option>
                        <option value="scheduled">Scheduled</option>
                        <option value="confirmed">Confirmed</option>
                        <option value="completed">Completed</option>
                        <option value="cancelled">Cancelled</option>
                        <option value="no_show">No Show</option>
                    </select>
                </div>
            </div>
            <div class="form-group">
                <label>Reason/Notes:</label>
                <textarea name="reason" rows="3" placeholder="Reason for appointment or additional notes..."></textarea>
            </div>
            <div class="form-actions">
                <button type="submit" class="btn btn-primary">Update Appointment</button>
                <button type="button" class="btn btn-secondary" onclick="closeModal()">Cancel</button>
            </div>
        </form>
    </div>
</div>

<!-- Add Role Modal -->
<div id="addRoleModal" class="modal">
    <div class="modal-header">
        <h3>🔑 Add New Role</h3>
        <button class="modal-close" onclick="closeModal()">&times;</button>
    </div>
    <div class="modal-body">
        <form id="addRoleForm">
            <div class="form-group">
                <label>Role Name:</label>
                <input type="text" name="role_name" required>
            </div>
            <div class="form-group">
                <label>Description:</label>
                <textarea name="role_description" rows="3"></textarea>
            </div>
            <div class="form-group">
                <label>Permissions:</label>
                <div class="permissions-list">
                    <label><input type="checkbox" name="permissions[]" value="users_manage"> Manage Users</label>
                    <label><input type="checkbox" name="permissions[]" value="patients_manage"> Manage Patients</label>
                    <label><input type="checkbox" name="permissions[]" value="appointments_manage"> Manage Appointments</label>
                    <label><input type="checkbox" name="permissions[]" value="reports_view"> View Reports</label>
                    <label><input type="checkbox" name="permissions[]" value="settings_manage"> Manage Settings</label>
                </div>
            </div>
            <div class="form-actions">
                <button type="submit" class="btn btn-primary">Add Role</button>
                <button type="button" class="btn btn-secondary" onclick="closeModal()">Cancel</button>
            </div>
        </form>
    </div>
</div>

<!-- Add Patient Modal -->
<div id="addPatientModal" class="modal">
    <div class="modal-header">
        <h3>➕ Add New Patient</h3>
        <button class="modal-close" onclick="closeModal()">&times;</button>
    </div>
    <div class="modal-body">
        <form id="addPatientForm">
            <div class="form-row">
                <div class="form-group">
                    <label>First Name:</label>
                    <input type="text" name="first_name" required>
                </div>
                <div class="form-group">
                    <label>Last Name:</label>
                    <input type="text" name="last_name" required>
                </div>
            </div>
            <div class="form-group">
                <label>Middle Name:</label>
                <input type="text" name="middle_name" placeholder="Optional">
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label>Contact Number:</label>
                    <input type="text" name="contact_number" placeholder="Primary contact">
                </div>
                <div class="form-group">
                    <label>Phone:</label>
                    <input type="text" name="phone" placeholder="Alternative phone">
                </div>
            </div>
            <div class="form-group">
                <label>Email:</label>
                <input type="email" name="email">
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label>Date of Birth:</label>
                    <input type="date" name="date_of_birth">
                </div>
                <div class="form-group">
                    <label>Gender:</label>
                    <select name="gender">
                        <option value="">Select Gender</option>
                        <option value="male">Male</option>
                        <option value="female">Female</option>
                        <option value="other">Other</option>
                    </select>
                </div>
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label>Blood Type:</label>
                    <select name="blood_type">
                        <option value="">Select Blood Type</option>
                        <option value="A+">A+</option>
                        <option value="A-">A-</option>
                        <option value="B+">B+</option>
                        <option value="B-">B-</option>
                        <option value="AB+">AB+</option>
                        <option value="AB-">AB-</option>
                        <option value="O+">O+</option>
                        <option value="O-">O-</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Government ID:</label>
                    <input type="text" name="government_id" placeholder="SSS, PhilHealth, etc.">
                </div>
            </div>
            <div class="form-group">
                <label>Address:</label>
                <textarea name="address" rows="2" placeholder="Complete address"></textarea>
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label>Emergency Contact Name:</label>
                    <input type="text" name="emergency_contact_name">
                </div>
                <div class="form-group">
                    <label>Emergency Contact Number:</label>
                    <input type="text" name="emergency_contact_number">
                </div>
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label>Medical History:</label>
                    <textarea name="medical_history" rows="2" placeholder="Previous conditions"></textarea>
                </div>
                <div class="form-group">
                    <label>Allergies:</label>
                    <textarea name="allergies" rows="2" placeholder="Known allergies"></textarea>
                </div>
            </div>
            <div class="form-actions">
                <button type="submit" class="btn btn-primary">Add Patient</button>
                <button type="button" class="btn btn-secondary" onclick="closeModal()">Cancel</button>
            </div>
        </form>
    </div>
</div>

<!-- Edit Patient Modal -->
<div id="editPatientModal" class="modal">
    <div class="modal-header">
        <h3>✏️ Edit Patient</h3>
        <button class="modal-close" onclick="closeModal()">&times;</button>
    </div>
    <div class="modal-body">
        <form id="editPatientForm">
            <div class="form-row">
                <div class="form-group">
                    <label>First Name:</label>
                    <input type="text" name="first_name" required>
                </div>
                <div class="form-group">
                    <label>Last Name:</label>
                    <input type="text" name="last_name" required>
                </div>
            </div>
            <div class="form-group">
                <label>Middle Name:</label>
                <input type="text" name="middle_name" placeholder="Optional">
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label>Contact Number:</label>
                    <input type="text" name="contact_number" placeholder="Primary contact">
                </div>
                <div class="form-group">
                    <label>Phone:</label>
                    <input type="text" name="phone" placeholder="Alternative phone">
                </div>
            </div>
            <div class="form-group">
                <label>Email:</label>
                <input type="email" name="email">
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label>Date of Birth:</label>
                    <input type="date" name="date_of_birth">
                </div>
                <div class="form-group">
                    <label>Gender:</label>
                    <select name="gender">
                        <option value="">Select Gender</option>
                        <option value="male">Male</option>
                        <option value="female">Female</option>
                        <option value="other">Other</option>
                    </select>
                </div>
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label>Blood Type:</label>
                    <select name="blood_type">
                        <option value="">Select Blood Type</option>
                        <option value="A+">A+</option>
                        <option value="A-">A-</option>
                        <option value="B+">B+</option>
                        <option value="B-">B-</option>
                        <option value="AB+">AB+</option>
                        <option value="AB-">AB-</option>
                        <option value="O+">O+</option>
                        <option value="O-">O-</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Government ID:</label>
                    <input type="text" name="government_id" placeholder="SSS, PhilHealth, etc.">
                </div>
            </div>
            <div class="form-group">
                <label>Address:</label>
                <textarea name="address" rows="2" placeholder="Complete address"></textarea>
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label>Emergency Contact Name:</label>
                    <input type="text" name="emergency_contact_name">
                </div>
                <div class="form-group">
                    <label>Emergency Contact Number:</label>
                    <input type="text" name="emergency_contact_number">
                </div>
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label>Medical History:</label>
                    <textarea name="medical_history" rows="2" placeholder="Previous conditions"></textarea>
                </div>
                <div class="form-group">
                    <label>Allergies:</label>
                    <textarea name="allergies" rows="2" placeholder="Known allergies"></textarea>
                </div>
            </div>
            <div class="form-actions">
                <button type="submit" class="btn btn-primary">Update Patient</button>
                <button type="button" class="btn btn-secondary" onclick="closeModal()">Cancel</button>
            </div>
        </form>
    </div>
</div>

<!-- Add Appointment Modal -->
<div id="addAppointmentModal" class="modal">
    <div class="modal-header">
        <h3>📅 Schedule New Appointment</h3>
        <button class="modal-close" onclick="closeModal()">&times;</button>
    </div>
    <div class="modal-body">
        <form id="addAppointmentForm">
            <div class="form-row">
                <div class="form-group">
                    <label>Patient Name:</label>
                    <input type="text" name="patient_name" required placeholder="Full name of patient">
                </div>
                <div class="form-group">
                    <label>Patient Phone:</label>
                    <input type="text" name="patient_phone" placeholder="Contact number">
                </div>
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label>Doctor Name:</label>
                    <input type="text" name="doctor_name" required placeholder="Attending doctor">
                </div>
                <div class="form-group">
                    <label>Department:</label>
                    <select name="department">
                        <option value="">Select Department</option>
                        <option value="General Medicine">General Medicine</option>
                        <option value="Cardiology">Cardiology</option>
                        <option value="Pediatrics">Pediatrics</option>
                        <option value="Orthopedics">Orthopedics</option>
                        <option value="Neurology">Neurology</option>
                        <option value="Emergency">Emergency</option>
                        <option value="Surgery">Surgery</option>
                    </select>
                </div>
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label>Date:</label>
                    <input type="date" name="appointment_date" required>
                </div>
                <div class="form-group">
                    <label>Time:</label>
                    <input type="time" name="appointment_time" required>
                </div>
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label>Appointment Type:</label>
                    <select name="appointment_type" required>
                        <option value="">Select Type</option>
                        <option value="consultation">Consultation</option>
                        <option value="follow-up">Follow-up</option>
                        <option value="emergency">Emergency</option>
                        <option value="surgery">Surgery</option>
                        <option value="therapy">Therapy</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Status:</label>
                    <select name="status">
                        <option value="pending">Pending</option>
                        <option value="scheduled">Scheduled</option>
                        <option value="confirmed">Confirmed</option>
                    </select>
                </div>
            </div>
            <div class="form-group">
                <label>Reason/Notes:</label>
                <textarea name="reason" rows="3" placeholder="Reason for appointment or additional notes..."></textarea>
            </div>
            <div class="form-actions">
                <button type="submit" class="btn btn-primary">Schedule Appointment</button>
                <button type="button" class="btn btn-secondary" onclick="closeModal()">Cancel</button>
            </div>
        </form>
    </div>
</div>

<style>
.unified-dashboard {
    max-width: 1400px;
    margin: 0 auto;
    padding: 20px;
}

.dashboard-header {
    text-align: center;
    margin-bottom: 30px;
}

.dashboard-header h1 {
    color: #2c3e50;
    margin-bottom: 10px;
}

.stats-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 20px;
    margin-bottom: 30px;
}

.stat-card {
    background: #f8f9fa;
    border: 1px solid #dee2e6;
    color: #333;
    padding: 20px;
    border-radius: 5px;
    display: flex;
    align-items: center;
}

.stat-icon {
    font-size: 2rem;
    margin-right: 15px;
    color: #007bff;
}

.stat-content h3 {
    font-size: 2rem;
    margin: 0;
    font-weight: bold;
}

.stat-content p {
    margin: 5px 0 0 0;
    opacity: 0.9;
}

.tab-navigation {
    display: flex;
    background: #ffffff;
    border: 1px solid #dee2e6;
    border-radius: 5px;
    margin-bottom: 20px;
    overflow-x: auto;
}

.tab-btn {
    flex: 1;
    padding: 10px 15px;
    border: none;
    background: #f8f9fa;
    cursor: pointer;
    white-space: nowrap;
    min-width: 120px;
    border-right: 1px solid #dee2e6;
}

.tab-btn:hover {
    background: #e9ecef;
}

.tab-btn.active {
    background: #007bff;
    color: white;
}

.tab-btn:last-child {
    border-right: none;
}

.tab-content {
    display: none;
}

.tab-content.active {
    display: block;
}

.section-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 25px;
    padding-bottom: 15px;
    border-bottom: 2px solid #e9ecef;
}

.section-actions {
    display: flex;
    gap: 10px;
}

.content-grid {
    display: grid;
    grid-template-columns: 2fr 1fr;
    gap: 25px;
}

.content-card {
    background: white;
    border: 1px solid #dee2e6;
    border-radius: 5px;
    padding: 20px;
}

.content-card.full-width {
    grid-column: 1 / -1;
}

.services-grid, .finance-grid, .facilities-grid, .reports-grid, .settings-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
    gap: 25px;
}

.service-card, .finance-card, .facility-card, .report-card, .settings-card {
    background: white;
    border: 1px solid #dee2e6;
    border-radius: 5px;
    padding: 20px;
    text-align: center;
}

.service-icon {
    font-size: 3rem;
    margin-bottom: 15px;
}

.btn {
    padding: 8px 15px;
    border: 1px solid #dee2e6;
    border-radius: 3px;
    cursor: pointer;
    font-weight: normal;
    text-decoration: none;
    display: inline-block;
    background: #f8f9fa;
}

.btn-primary {
    background: #007bff;
    color: white;
    border-color: #007bff;
}

.btn-primary:hover {
    background: #0056b3;
    border-color: #0056b3;
}

.btn-secondary {
    background: #6c757d;
    color: white;
    border-color: #6c757d;
}

.btn-secondary:hover {
    background: #545b62;
    border-color: #545b62;
}

.btn:hover {
    background: #e9ecef;
}

/* Ensure buttons are properly styled and clickable */
.btn {
    position: relative;
    z-index: 10;
    pointer-events: auto;
    cursor: pointer;
    border: 1px solid #ddd;
    display: inline-block;
    text-decoration: none;
    transition: all 0.2s ease;
}

.btn:active {
    transform: scale(0.95);
}

/* Table buttons styling */
table .btn {
    min-width: 60px;
    min-height: 30px;
    line-height: 1.2;
    padding: 4px 8px;
}

.btn-danger {
    background: #dc3545;
    color: white;
    border-color: #dc3545;
}

.btn-danger:hover {
    background: #c82333;
    border-color: #bd2130;
}

.data-table {
    width: 100%;
    border-collapse: collapse;
    margin-top: 15px;
}

.data-table th, .data-table td {
    padding: 8px 12px;
    text-align: left;
    border: 1px solid #dee2e6;
}

.data-table th {
    background: #f8f9fa;
    font-weight: bold;
}

.search-bar input {
    width: 100%;
    padding: 8px 10px;
    border: 1px solid #dee2e6;
    border-radius: 3px;
    margin-bottom: 15px;
}

.modal-overlay {
    display: none;
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0,0,0,0.5);
    z-index: 1000;
}

.modal {
    display: none;
    position: fixed;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    background: white;
    border: 2px solid #dee2e6;
    border-radius: 8px;
    box-shadow: 0 4px 20px rgba(0,0,0,0.3);
    z-index: 1001;
    width: 90%;
    max-width: 700px;
    max-height: 90vh;
    overflow-y: auto;
}

.modal-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 20px 25px;
    border-bottom: 1px solid #e9ecef;
}

.modal-close {
    background: none;
    border: none;
    font-size: 1.5rem;
    cursor: pointer;
}

.modal-body {
    padding: 20px;
}

.form-group {
    margin-bottom: 15px;
}

.form-group label {
    display: block;
    margin-bottom: 5px;
    font-weight: 500;
}

.form-group input, .form-group select, .form-group textarea {
    width: 100%;
    padding: 8px;
    border: 1px solid #dee2e6;
    border-radius: 3px;
    box-sizing: border-box;
}

.form-actions {
    display: flex;
    gap: 10px;
    justify-content: flex-end;
    margin-top: 20px;
    padding-top: 15px;
    border-top: 1px solid #dee2e6;
    position: sticky;
    bottom: 0;
    background: white;
}

.form-row {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 15px;
}

.permissions-list {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 10px;
    margin-top: 10px;
}

.permissions-list label {
    display: flex;
    align-items: center;
    gap: 8px;
    font-weight: normal;
}

.stat-item {
    text-align: center;
    padding: 15px;
    background: #f8f9fa;
    border-radius: 8px;
    margin-bottom: 10px;
}

.stat-number {
    display: block;
    font-size: 1.5rem;
    font-weight: bold;
    color: #007bff;
}

.stat-label {
    font-size: 0.9rem;
    color: #666;
}

.quick-actions {
    display: flex;
    flex-direction: column;
    gap: 8px;
    margin-top: 15px;
}

.btn-sm {
    padding: 8px 15px;
    font-size: 0.875rem;
}

.role-actions {
    margin-top: 15px;
    display: flex;
    gap: 10px;
}

.role-item {
    background: #f8f9fa;
    padding: 15px;
    border-radius: 8px;
    margin-bottom: 10px;
    border-left: 4px solid #007bff;
}

.role-item h5 {
    margin: 0 0 5px 0;
    color: #007bff;
}

.role-item p {
    margin: 0;
    color: #666;
    font-size: 0.9rem;
}

.badge {
    padding: 4px 8px;
    border-radius: 4px;
    font-size: 0.75rem;
    font-weight: 500;
}

.badge-success {
    background-color: #28a745;
    color: white;
}

.badge-danger {
    background-color: #dc3545;
    color: white;
}

.badge-info {
    background-color: #17a2b8;
    color: white;
}

.badge-warning {
    background-color: #ffc107;
    color: #212529;
}

.badge-secondary {
    background-color: #6c757d;
    color: white;
}

.text-center {
    text-align: center;
}

.patient-stats, .appointment-stats {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(120px, 1fr));
    gap: 10px;
    margin-bottom: 20px;
}

/* Medical Services Styles */
.services-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
    gap: 20px;
    margin-bottom: 25px;
}

.service-card {
    background: #ffffff;
    border: 1px solid #dee2e6;
    border-radius: 8px;
    padding: 20px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    transition: transform 0.2s ease, box-shadow 0.2s ease;
}

.service-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 8px rgba(0,0,0,0.15);
}

.service-icon {
    font-size: 2.5rem;
    margin-bottom: 15px;
    color: #007bff;
}

.service-card h3 {
    margin: 0 0 10px 0;
    color: #2c3e50;
    font-size: 1.25rem;
}

.service-card p {
    color: #666;
    margin: 0 0 15px 0;
    font-size: 0.9rem;
    line-height: 1.4;
}

.service-stats {
    display: flex;
    flex-direction: column;
    gap: 5px;
    margin-bottom: 15px;
}

.service-stats span {
    font-size: 0.85rem;
    color: #495057;
}

.service-actions {
    margin-top: 15px;
}

.category-badge {
    display: inline-block;
    padding: 4px 8px;
    border-radius: 6px;
    font-size: 12px;
    font-weight: 500;
    text-transform: capitalize;
}

.category-consultation { background: #dbeafe; color: #1e40af; }
.category-laboratory { background: #dcfce7; color: #166534; }
.category-imaging { background: #fef3c7; color: #92400e; }
.category-surgery { background: #fecaca; color: #991b1b; }
.category-therapy { background: #e0e7ff; color: #3730a3; }
.category-emergency { background: #fee2e2; color: #dc2626; }
.category-other { background: #f3f4f6; color: #374151; }

.status-badge {
    display: inline-block;
    padding: 4px 8px;
    border-radius: 6px;
    font-size: 12px;
    font-weight: 500;
    text-transform: capitalize;
}

.status-active { background: #dcfce7; color: #166534; }
.status-inactive { background: #fef3c7; color: #92400e; }
.status-discontinued { background: #fecaca; color: #991b1b; }

@media (max-width: 768px) {
    .content-grid {
        grid-template-columns: 1fr;
    }
    
    .tab-navigation {
        flex-direction: column;
    }
    
    .tab-btn {
        min-width: auto;
    }
    
    .services-grid {
        grid-template-columns: 1fr;
    }
}
</style>

<script>
function showTab(tabName) {
    // Hide all tabs
    document.querySelectorAll('.tab-content').forEach(tab => {
        tab.classList.remove('active');
    });
    
    // Remove active class from all buttons
    document.querySelectorAll('.tab-btn').forEach(btn => {
        btn.classList.remove('active');
    });
    
    // Show selected tab
    document.getElementById(tabName + '-tab').classList.add('active');
    
    // Add active class to clicked button
    event.target.classList.add('active');
    
    // Load tab-specific data
    loadTabData(tabName);
}

function loadTabData(tabName) {
    switch(tabName) {
        case 'users':
            loadUsers();
            loadRoles();
            break;
        case 'appointments':
            loadAppointments();
            break;
        case 'patients':
            loadPatients();
            break;
        // Add other cases as needed
    }
}

function showModal(modalId) {
    document.getElementById('modal-overlay').style.display = 'block';
    document.getElementById(modalId).style.display = 'block';
}

function closeModal() {
    document.getElementById('modal-overlay').style.display = 'none';
    document.querySelectorAll('.modal').forEach(modal => {
        modal.style.display = 'none';
    });
}

function loadUsers() {
    const tbody = document.getElementById('users-table');
    
    // Show loading message
    tbody.innerHTML = `
        <tr>
            <td colspan="9" style="text-align: center;">Loading users...</td>
        </tr>
    `;
    
    // Try to fetch users from the new reliable API
    fetch('<?= base_url("api/dashboard/users") ?>')
        .then(response => {
            console.log('Dashboard API response status:', response.status);
            if (!response.ok) {
                // If new API fails, try the quick test API
                return fetch('<?= base_url("quick-test-api") ?>');
            }
            return response;
        })
        .then(response => {
            console.log('API Response status:', response.status);
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            return response.text(); // Get as text first to debug
        })
        .then(text => {
            console.log('Raw API response:', text);
            try {
                return JSON.parse(text);
            } catch (e) {
                console.error('JSON parse error:', e);
                console.error('Response text:', text);
                throw new Error('Invalid JSON response from server');
            }
        })
        .then(data => {
            console.log('Users data received:', data);
            
            // Handle different response formats
            let users = [];
            if (data.success && Array.isArray(data.data)) {
                users = data.data;
            } else if (Array.isArray(data)) {
                users = data;
            }
            
            if (users.length > 0) {
                tbody.innerHTML = users.map(user => `
                    <tr>
                        <td>${user.id}</td>
                        <td>${user.username}</td>
                        <td>${user.first_name} ${user.last_name}</td>
                        <td>${user.email}</td>
                        <td><span class="badge badge-info">${user.role}</span></td>
                        <td>${user.phone || 'N/A'}</td>
                        <td><span class="badge ${user.status === 'active' ? 'badge-success' : user.status === 'suspended' ? 'badge-warning' : 'badge-danger'}">${user.status ? user.status.charAt(0).toUpperCase() + user.status.slice(1) : 'Active'}</span></td>
                        <td>${user.last_login ? new Date(user.last_login).toLocaleDateString() : 'Never'}</td>
                        <td>
                            <button class="btn btn-sm" onclick="editUser(${user.id})">✏️ Edit</button>
                            <button class="btn btn-sm btn-secondary" onclick="viewUser(${user.id})">👁️ View</button>
                            <button class="btn btn-sm btn-danger" onclick="deleteUser(${user.id})">🗑️ Delete</button>
                        </td>
                    </tr>
                `).join('');
            } else {
                tbody.innerHTML = `
                    <tr>
                        <td colspan="9" style="text-align: center;">No users found</td>
                    </tr>
                `;
            }
        })
        .catch(error => {
            console.error('Error loading users:', error);
            
            // Show sample data if API fails
            tbody.innerHTML = `
                <tr>
                    <td>1</td>
                    <td>admin</td>
                    <td>Admin User</td>
                    <td>admin@hospital.com</td>
                    <td><span class="badge badge-info">superadmin</span></td>
                    <td>09123456789</td>
                    <td><span class="badge badge-success">Active</span></td>
                    <td>Never</td>
                    <td>
                        <button class="btn btn-sm" onclick="editUser(1)">✏️ Edit</button>
                        <button class="btn btn-sm btn-secondary" onclick="viewUser(1)">👁️ View</button>
                        <button class="btn btn-sm btn-danger" onclick="deleteUser(1)">🗑️ Delete</button>
                    </td>
                </tr>
                <tr>
                    <td>2</td>
                    <td>doctor1</td>
                    <td>Dr. John Smith</td>
                    <td>doctor@hospital.com</td>
                    <td><span class="badge badge-info">doctor</span></td>
                    <td>09987654321</td>
                    <td><span class="badge badge-success">Active</span></td>
                    <td>Never</td>
                    <td>
                        <button class="btn btn-sm" onclick="editUser(2)">✏️ Edit</button>
                        <button class="btn btn-sm btn-secondary" onclick="viewUser(2)">👁️ View</button>
                        <button class="btn btn-sm btn-danger" onclick="deleteUser(2)">🗑️ Delete</button>
                    </td>
                </tr>
                <tr>
                    <td>3</td>
                    <td>nurse1</td>
                    <td>Nurse Mary Johnson</td>
                    <td>nurse@hospital.com</td>
                    <td><span class="badge badge-info">nurse</span></td>
                    <td>09555666777</td>
                    <td><span class="badge badge-success">Active</span></td>
                    <td>Never</td>
                    <td>
                        <button class="btn btn-sm" onclick="editUser(3)">✏️ Edit</button>
                        <button class="btn btn-sm btn-secondary" onclick="viewUser(3)">👁️ View</button>
                        <button class="btn btn-sm btn-danger" onclick="deleteUser(3)">🗑️ Delete</button>
                    </td>
                </tr>
                <tr>
                    <td colspan="9" style="text-align: center; color: #666; font-style: italic;">
                        ⚠️ API Error: ${error.message}<br>
                        <small>Showing sample data - Please check your database connection</small>
                    </td>
                </tr>
            `;
        });
}

function loadAllUsers() {
    loadUsers();
}

function searchUsers() {
    const searchTerm = document.getElementById('user-search').value.toLowerCase();
    const rows = document.querySelectorAll('#users-table tr');
    
    rows.forEach(row => {
        const text = row.textContent.toLowerCase();
        row.style.display = text.includes(searchTerm) ? '' : 'none';
    });
}

function editUser(id) {
    try {
        
        // Check if modal exists
        const modal = document.getElementById('editUserModal');
        if (!modal) {
            alert('Edit modal not found. Please refresh the page.');
            return;
        }
        
        // For now, just open the modal with empty fields
        // Clear all form fields first
        const form = document.querySelector('#editUserForm');
        if (form) {
            form.reset();
            form.dataset.userId = id;
        }
        
        // Show the modal
        showModal('editUserModal');
        
        // Get user data from the current table row instead of API
        const userRow = document.querySelector(`button[onclick*="editUser(${id})"]`).closest('tr');
        
        if (userRow) {
            const cells = userRow.querySelectorAll('td');
            
            // Extract data from table cells and populate form
            const setFieldValue = (selector, value) => {
                const element = document.querySelector(selector);
                if (element) {
                    element.value = value || '';
                } else {
                    console.warn('Element not found:', selector);
                }
            };
            
            // Parse the name from table (assuming format: "First Last")
            const fullName = cells[2]?.textContent || '';
            const nameParts = fullName.trim().split(' ');
            const firstName = nameParts[0] || '';
            const lastName = nameParts.slice(1).join(' ') || '';
            
            // Populate form fields with table data
            setFieldValue('#editUserModal input[name="first_name"]', firstName);
            setFieldValue('#editUserModal input[name="last_name"]', lastName);
            setFieldValue('#editUserModal input[name="username"]', cells[1]?.textContent || '');
            setFieldValue('#editUserModal input[name="email"]', cells[3]?.textContent || '');
            setFieldValue('#editUserModal select[name="role"]', cells[4]?.textContent?.toLowerCase() || '');
            setFieldValue('#editUserModal input[name="phone"]', cells[5]?.textContent || '');
            
            // Handle status (remove badge formatting)
            const statusText = cells[6]?.textContent?.replace(/^\s+|\s+$/g, '') || '';
            setFieldValue('#editUserModal select[name="status"]', statusText.toLowerCase());
        } else {
            console.warn('Could not find user data in table. User ID: ' + id);
        }
            
    } catch (error) {
        console.error('Edit user error:', error);
        alert('Error: ' + error.message);
    }
}

function viewUser(id) {
    try {
        console.log('Viewing user ID:', id);
        
        // Fetch user data
        fetch(`<?= base_url("super-admin/api/users") ?>/${id}`)
            .then(response => {
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                return response.json();
            })
            .then(result => {
                if (result.success) {
                    const user = result.data;
                    
                    // Create a detailed view window
                    const userDetails = `
                        <h2>👤 User Details</h2>
                        <table style="width: 100%; border-collapse: collapse;">
                            <tr><td style="padding: 8px; border: 1px solid #ddd; font-weight: bold;">ID:</td><td style="padding: 8px; border: 1px solid #ddd;">${user.id}</td></tr>
                            <tr><td style="padding: 8px; border: 1px solid #ddd; font-weight: bold;">Name:</td><td style="padding: 8px; border: 1px solid #ddd;">${user.first_name} ${user.last_name}</td></tr>
                            <tr><td style="padding: 8px; border: 1px solid #ddd; font-weight: bold;">Username:</td><td style="padding: 8px; border: 1px solid #ddd;">${user.username}</td></tr>
                            <tr><td style="padding: 8px; border: 1px solid #ddd; font-weight: bold;">Email:</td><td style="padding: 8px; border: 1px solid #ddd;">${user.email}</td></tr>
                            <tr><td style="padding: 8px; border: 1px solid #ddd; font-weight: bold;">Role:</td><td style="padding: 8px; border: 1px solid #ddd;">${user.role}</td></tr>
                            <tr><td style="padding: 8px; border: 1px solid #ddd; font-weight: bold;">Phone:</td><td style="padding: 8px; border: 1px solid #ddd;">${user.phone || 'N/A'}</td></tr>
                            <tr><td style="padding: 8px; border: 1px solid #ddd; font-weight: bold;">Address:</td><td style="padding: 8px; border: 1px solid #ddd;">${user.address || 'N/A'}</td></tr>
                            <tr><td style="padding: 8px; border: 1px solid #ddd; font-weight: bold;">Status:</td><td style="padding: 8px; border: 1px solid #ddd;">${user.status}</td></tr>
                            <tr><td style="padding: 8px; border: 1px solid #ddd; font-weight: bold;">Last Login:</td><td style="padding: 8px; border: 1px solid #ddd;">${user.last_login || 'Never'}</td></tr>
                            <tr><td style="padding: 8px; border: 1px solid #ddd; font-weight: bold;">Created:</td><td style="padding: 8px; border: 1px solid #ddd;">${user.created_at || 'N/A'}</td></tr>
                        </table>
                    `;
                    
                    // Open in new window
                    const viewWindow = window.open('', '_blank', 'width=600,height=700,scrollbars=yes');
                    viewWindow.document.write(`
                        <html>
                            <head>
                                <title>User Details - ${user.first_name} ${user.last_name}</title>
                                <style>
                                    body { font-family: Arial, sans-serif; padding: 20px; }
                                    h2 { color: #333; border-bottom: 2px solid #007bff; padding-bottom: 10px; }
                                    .btn { padding: 10px 20px; margin: 10px 5px; border: none; border-radius: 4px; cursor: pointer; }
                                    .btn-primary { background: #007bff; color: white; }
                                </style>
                            </head>
                            <body>
                                ${userDetails}
                                <div style="margin-top: 20px; text-align: center;">
                                    <button class="btn btn-primary" onclick="window.close()">Close</button>
                                </div>
                            </body>
                        </html>
                    `);
                } else {
                    alert('Error loading user data: ' + (result.message || 'Unknown error'));
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error loading user data: ' + error.message);
            });
    } catch (error) {
        console.error('View user error:', error);
        alert('Error: ' + error.message);
    }
}

function deleteUser(id) {
    try {
        console.log('Deleting user ID:', id);
        
        if (confirm(`Delete User ID: ${id}\n\nAre you sure you want to delete this user?\n\nThis action cannot be undone.`)) {
            fetch(`<?= base_url("super-admin/users") ?>/${id}`, {
                method: 'DELETE',
                headers: {
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => {
                console.log('Delete response status:', response.status);
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                return response.json();
            })
            .then(result => {
                console.log('Delete result:', result);
                if (result.success) {
                    alert('User deleted successfully');
                    loadUsers(); // Refresh the user list
                } else {
                    alert('Error deleting user: ' + (result.message || 'Unknown error'));
                }
            })
            .catch(error => {
                console.error('Delete error:', error);
                alert('Error deleting user: ' + error.message);
            });
        }
    } catch (error) {
        console.error('Delete user error:', error);
        alert('Error: ' + error.message);
    }
}

function loadRoles() {
    fetch('<?= base_url("super-admin/roles") ?>')
        .then(response => response.text())
        .then(html => {
            // Extract roles data from HTML response
            const parser = new DOMParser();
            const doc = parser.parseFromString(html, 'text/html');
            const rolesList = document.getElementById('roles-list');
            rolesList.innerHTML = `
                <div class="role-item">
                    <h5>Super Admin</h5>
                    <p>Full system access</p>
                </div>
                <div class="role-item">
                    <h5>Doctor</h5>
                    <p>Medical staff access</p>
                </div>
                <div class="role-item">
                    <h5>Nurse</h5>
                    <p>Patient care access</p>
                </div>
                <div class="role-item">
                    <h5>Receptionist</h5>
                    <p>Front desk operations</p>
                </div>
            `;
        })
        .catch(error => console.error('Error loading roles:', error));
}

function loadAppointments() {
    fetch('<?= base_url("api/dashboard/appointments") ?>')
        .then(response => response.json())
        .then(data => {
            // Handle the new API response format
            const appointments = data.success ? data.data : data;
            const tbody = document.getElementById('appointments-table');
            
            if (appointments.length === 0) {
                tbody.innerHTML = `
                    <tr>
                        <td colspan="10" class="text-center">No appointments found</td>
                    </tr>
                `;
                return;
            }
            
            tbody.innerHTML = appointments.map(appointment => `
                <tr>
                    <td>${appointment.appointment_id || appointment.id}</td>
                    <td>${appointment.patient_name || 'N/A'}</td>
                    <td>${appointment.patient_phone || 'N/A'}</td>
                    <td>${appointment.doctor_name || 'N/A'}</td>
                    <td>${appointment.department || 'N/A'}</td>
                    <td>${appointment.appointment_date ? new Date(appointment.appointment_date).toLocaleDateString() : 'N/A'}</td>
                    <td>${appointment.appointment_time || 'N/A'}</td>
                    <td>${appointment.appointment_type || appointment.type || 'N/A'}</td>
                    <td><span class="badge ${getStatusBadge(appointment.status)}">${appointment.status ? appointment.status.charAt(0).toUpperCase() + appointment.status.slice(1) : 'Pending'}</span></td>
                    <td>
                        <button class="btn btn-sm" onclick="editAppointment(${appointment.id})">✏️ Edit</button>
                        <button class="btn btn-sm btn-secondary" onclick="viewAppointment(${appointment.id})">👁️ View</button>
                    </td>
                </tr>
            `).join('');
        })
        .catch(error => {
            console.error('Error loading appointments:', error);
            const tbody = document.getElementById('appointments-table');
            // Show sample appointments for testing
            tbody.innerHTML = `
                <tr>
                    <td>1</td>
                    <td>John Doe</td>
                    <td>09123456789</td>
                    <td>Dr. Smith</td>
                    <td>Cardiology</td>
                    <td>2024-10-05</td>
                    <td>10:00 AM</td>
                    <td>Consultation</td>
                    <td><span class="badge badge-success">Scheduled</span></td>
                    <td>
                        <button class="btn btn-sm" onclick="console.log('Edit clicked: 1'); editAppointment(1)">✏️ Edit</button>
                        <button class="btn btn-sm btn-secondary" onclick="console.log('View clicked: 1'); viewAppointment(1)">👁️ View</button>
                    </td>
                </tr>
                <tr>
                    <td>2</td>
                    <td>Jane Smith</td>
                    <td>09987654321</td>
                    <td>Dr. Johnson</td>
                    <td>General Medicine</td>
                    <td>2024-10-06</td>
                    <td>2:00 PM</td>
                    <td>Follow-up</td>
                    <td><span class="badge badge-info">Confirmed</span></td>
                    <td>
                        <button class="btn btn-sm" onclick="console.log('Edit clicked: 2'); editAppointment(2)">✏️ Edit</button>
                        <button class="btn btn-sm btn-secondary" onclick="console.log('View clicked: 2'); viewAppointment(2)">👁️ View</button>
                    </td>
                </tr>
                <tr>
                    <td colspan="10" style="text-align: center; color: #666; font-style: italic;">
                        ⚠️ API Error: ${error.message}<br>
                        <small>Showing sample data - <a href="<?= base_url('test-buttons') ?>" target="_blank">Test Buttons</a> | 
                        <a href="<?= base_url('api/dashboard/appointments') ?>" target="_blank">Test API</a></small>
                    </td>
                </tr>
            `;
        });
}

function getStatusBadge(status) {
    switch(status) {
        case 'confirmed': return 'badge-success';
        case 'completed': return 'badge-info';
        case 'cancelled': return 'badge-danger';
        case 'no_show': return 'badge-warning';
        default: return 'badge-secondary';
    }
}

function searchAppointments() {
    const searchTerm = document.getElementById('appointment-search').value.toLowerCase();
    const rows = document.querySelectorAll('#appointments-table tr');
    
    rows.forEach(row => {
        const text = row.textContent.toLowerCase();
        row.style.display = text.includes(searchTerm) ? '' : 'none';
    });
}

function loadPatients() {
    fetch('<?= base_url("api/dashboard/patients") ?>')
        .then(response => response.json())
        .then(data => {
            // Handle the new API response format
            const patients = data.success ? data.data : data;
            const tbody = document.getElementById('patients-table');
            
            if (patients.length === 0) {
                tbody.innerHTML = `
                    <tr>
                        <td colspan="9" class="text-center">No patients found</td>
                    </tr>
                `;
                return;
            }
            
            tbody.innerHTML = patients.map(patient => `
                <tr>
                    <td>${patient.patient_id || patient.id}</td>
                    <td>${patient.first_name} ${patient.last_name}${patient.middle_name ? ' ' + patient.middle_name : ''}</td>
                    <td>${patient.contact_number || 'N/A'}</td>
                    <td>${patient.phone || 'N/A'}</td>
                    <td>${patient.email || 'N/A'}</td>
                    <td>${patient.gender ? patient.gender.charAt(0).toUpperCase() + patient.gender.slice(1) : 'N/A'}</td>
                    <td>${patient.blood_type || 'N/A'}</td>
                    <td><span class="badge ${patient.status === 'active' ? 'badge-success' : patient.status === 'inpatient' ? 'badge-info' : 'badge-secondary'}">${patient.status ? patient.status.charAt(0).toUpperCase() + patient.status.slice(1) : 'Active'}</span></td>
                    <td>
                        <button class="btn btn-sm" onclick="editPatient(${patient.id})">✏️ Edit</button>
                        <button class="btn btn-sm btn-secondary" onclick="viewPatient(${patient.id})">👁️ View</button>
                        <button class="btn btn-sm btn-danger" onclick="deletePatient(${patient.id})">🗑️ Delete</button>
                    </td>
                </tr>
            `).join('');
        })
        .catch(error => {
            console.error('Error loading patients:', error);
            const tbody = document.getElementById('patients-table');
            tbody.innerHTML = `
                <tr>
                    <td colspan="9" class="text-center">Error loading patients</td>
                </tr>
            `;
        });
}

function searchPatients() {
    const searchTerm = document.getElementById('patient-search').value.toLowerCase();
    const rows = document.querySelectorAll('#patients-table tr');
    
    rows.forEach(row => {
        const text = row.textContent.toLowerCase();
        row.style.display = text.includes(searchTerm) ? '' : 'none';
    });
}

function exportPatients() {
    window.open('<?= base_url("super-admin/patients/export") ?>', '_blank');
}

function showPatientReports() {
    showTab('reports');
}

function toggleCalendarView() {
    alert('Calendar view feature coming soon!');
}

// Safe versions of action functions that won't cause server errors
function safeEditPatient(id) {
    alert(`Edit Patient ID: ${id}\n\nThis will open the edit form.\nFeature is being implemented.`);
}

function safeViewPatient(id) {
    alert(`View Patient ID: ${id}\n\nThis will show patient details.\nFeature is being implemented.`);
}

function safeDeletePatient(id) {
    if (confirm(`Delete Patient ID: ${id}\n\nAre you sure you want to delete this patient?`)) {
        alert(`Patient ID ${id} would be deleted.\nFeature is being implemented.`);
    }
}

function editPatient(id) {
    try {
        console.log('Editing patient ID:', id);
        
        // Check if modal exists
        const modal = document.getElementById('editPatientModal');
        if (!modal) {
            alert('Edit modal not found. Please refresh the page.');
            return;
        }
        
        // Fetch patient data first
        fetch(`<?= base_url("super-admin/patients/edit") ?>/${id}`)
            .then(response => {
                console.log('Response status:', response.status);
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                return response.json();
            })
            .then(result => {
                console.log('API result:', result);
                
                if (result.success) {
                    const patient = result.data;
                    
                    // Safely populate form fields
                    try {
                        const setFieldValue = (selector, value) => {
                            const element = document.querySelector(selector);
                            if (element) {
                                element.value = value || '';
                            } else {
                                console.warn('Element not found:', selector);
                            }
                        };
                        
                        setFieldValue('#editPatientModal input[name="first_name"]', patient.first_name);
                        setFieldValue('#editPatientModal input[name="last_name"]', patient.last_name);
                        setFieldValue('#editPatientModal input[name="middle_name"]', patient.middle_name);
                        setFieldValue('#editPatientModal input[name="contact_number"]', patient.contact_number);
                        setFieldValue('#editPatientModal input[name="phone"]', patient.phone);
                        setFieldValue('#editPatientModal input[name="email"]', patient.email);
                        setFieldValue('#editPatientModal input[name="date_of_birth"]', patient.date_of_birth);
                        setFieldValue('#editPatientModal select[name="gender"]', patient.gender);
                        setFieldValue('#editPatientModal select[name="blood_type"]', patient.blood_type);
                        setFieldValue('#editPatientModal input[name="government_id"]', patient.government_id);
                        setFieldValue('#editPatientModal textarea[name="address"]', patient.address);
                        setFieldValue('#editPatientModal input[name="emergency_contact_name"]', patient.emergency_contact_name);
                        setFieldValue('#editPatientModal input[name="emergency_contact_number"]', patient.emergency_contact_number);
                        setFieldValue('#editPatientModal textarea[name="medical_history"]', patient.medical_history);
                        setFieldValue('#editPatientModal textarea[name="allergies"]', patient.allergies);
                        
                        // Store patient ID for update
                        const form = document.querySelector('#editPatientForm');
                        if (form) {
                            form.dataset.patientId = id;
                        }
                        
                        // Show the modal
                        showModal('editPatientModal');
                    } catch (formError) {
                        console.error('Error populating form:', formError);
                        alert('Error setting up edit form: ' + formError.message);
                    }
                } else {
                    alert('Error loading patient data: ' + (result.message || 'Unknown error'));
                }
            })
            .catch(error => {
                console.error('Fetch error:', error);
                alert('Error loading patient data: ' + error.message);
            });
    } catch (error) {
        console.error('Edit patient error:', error);
        alert('Error: ' + error.message);
    }
}

function viewPatient(id) {
    try {
        // Get patient data from the current table row
        const patientRow = document.querySelector(`button[onclick*="viewPatient(${id})"]`).closest('tr');
        
        if (patientRow) {
            const cells = patientRow.querySelectorAll('td');
            
            // Extract data from table cells
            const patientData = {
                id: cells[0]?.textContent || id,
                name: cells[1]?.textContent || 'N/A',
                contactNumber: cells[2]?.textContent || 'N/A',
                phone: cells[3]?.textContent || 'N/A',
                email: cells[4]?.textContent || 'N/A',
                gender: cells[5]?.textContent || 'N/A',
                bloodType: cells[6]?.textContent || 'N/A',
                status: cells[7]?.textContent || 'N/A'
            };
            
            // Create a detailed view window
            const patientDetails = `
                <h2>🏥 Patient Details</h2>
                <table style="width: 100%; border-collapse: collapse; margin: 20px 0;">
                    <tr><td style="padding: 12px; border: 1px solid #ddd; font-weight: bold; background: #f5f5f5;">Patient ID:</td><td style="padding: 12px; border: 1px solid #ddd;">${patientData.id}</td></tr>
                    <tr><td style="padding: 12px; border: 1px solid #ddd; font-weight: bold; background: #f5f5f5;">Full Name:</td><td style="padding: 12px; border: 1px solid #ddd;">${patientData.name}</td></tr>
                    <tr><td style="padding: 12px; border: 1px solid #ddd; font-weight: bold; background: #f5f5f5;">Contact Number:</td><td style="padding: 12px; border: 1px solid #ddd;">${patientData.contactNumber}</td></tr>
                    <tr><td style="padding: 12px; border: 1px solid #ddd; font-weight: bold; background: #f5f5f5;">Phone:</td><td style="padding: 12px; border: 1px solid #ddd;">${patientData.phone}</td></tr>
                    <tr><td style="padding: 12px; border: 1px solid #ddd; font-weight: bold; background: #f5f5f5;">Email:</td><td style="padding: 12px; border: 1px solid #ddd;">${patientData.email}</td></tr>
                    <tr><td style="padding: 12px; border: 1px solid #ddd; font-weight: bold; background: #f5f5f5;">Gender:</td><td style="padding: 12px; border: 1px solid #ddd;">${patientData.gender}</td></tr>
                    <tr><td style="padding: 12px; border: 1px solid #ddd; font-weight: bold; background: #f5f5f5;">Blood Type:</td><td style="padding: 12px; border: 1px solid #ddd;">${patientData.bloodType}</td></tr>
                    <tr><td style="padding: 12px; border: 1px solid #ddd; font-weight: bold; background: #f5f5f5;">Status:</td><td style="padding: 12px; border: 1px solid #ddd;">${patientData.status}</td></tr>
                </table>
                
                <div style="background: #f8f9fa; padding: 15px; border-radius: 5px; margin: 20px 0;">
                    <h3 style="margin-top: 0;">👤 Patient Summary</h3>
                    <p><strong>Name:</strong> ${patientData.name}</p>
                    <p><strong>Contact:</strong> ${patientData.contactNumber}</p>
                    <p><strong>Email:</strong> ${patientData.email}</p>
                    <p><strong>Blood Type:</strong> ${patientData.bloodType}</p>
                    <p><strong>Status:</strong> ${patientData.status}</p>
                </div>
            `;
            
            // Open in new window
            const viewWindow = window.open('', '_blank', 'width=700,height=650,scrollbars=yes');
            viewWindow.document.write(`
                <html>
                    <head>
                        <title>Patient Details - ${patientData.name}</title>
                        <style>
                            body { 
                                font-family: Arial, sans-serif; 
                                padding: 30px; 
                                background: #f8f9fa;
                                margin: 0;
                            }
                            h2 { 
                                color: #333; 
                                border-bottom: 3px solid #28a745; 
                                padding-bottom: 10px; 
                                margin-bottom: 20px;
                            }
                            .btn { 
                                padding: 12px 24px; 
                                margin: 20px 5px; 
                                border: none; 
                                border-radius: 5px; 
                                cursor: pointer; 
                                font-size: 16px;
                            }
                            .btn-primary { 
                                background: #28a745; 
                                color: white; 
                            }
                            .btn-primary:hover {
                                background: #218838;
                            }
                            .container {
                                background: white;
                                padding: 30px;
                                border-radius: 10px;
                                box-shadow: 0 2px 10px rgba(0,0,0,0.1);
                            }
                        </style>
                    </head>
                    <body>
                        <div class="container">
                            ${patientDetails}
                            <div style="text-align: center; margin-top: 30px;">
                                <button class="btn btn-primary" onclick="window.close()">✖️ Close Window</button>
                            </div>
                        </div>
                    </body>
                </html>
            `);
        } else {
            alert('Could not find patient data in table. Patient ID: ' + id);
        }
    } catch (error) {
        console.error('View patient error:', error);
        alert('Error: ' + error.message);
    }
}

function deletePatient(id) {
    try {
        
        if (confirm('Are you sure you want to delete this patient? This action cannot be undone.')) {
            fetch(`<?= base_url("super-admin/patients") ?>/${id}`, {
                method: 'DELETE',
                headers: {
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => {
                console.log('Delete response status:', response.status);
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                return response.json();
            })
            .then(result => {
                console.log('Delete result:', result);
                if (result.success) {
                    alert('Patient deleted successfully');
                    loadPatients(); // Refresh the patient list
                } else {
                    alert('Error deleting patient: ' + (result.message || 'Unknown error'));
                }
            })
            .catch(error => {
                console.error('Delete error:', error);
                alert('Error deleting patient: ' + error.message);
            });
        }
    } catch (error) {
        console.error('Delete patient error:', error);
        alert('Error: ' + error.message);
    }
}

function editAppointment(id) {
    try {
        
        // Check if modal exists
        const modal = document.getElementById('editAppointmentModal');
        if (!modal) {
            alert('Edit appointment modal not found. Please refresh the page.');
            return;
        }
        
        // Get appointment data from the current table row
        const appointmentRow = document.querySelector(`button[onclick*="editAppointment(${id})"]`).closest('tr');
        
        if (appointmentRow) {
            const cells = appointmentRow.querySelectorAll('td');
            
            // Clear form first
            const form = document.querySelector('#editAppointmentForm');
            if (form) {
                form.reset();
                form.dataset.appointmentId = id;
            }
            
            // Extract data from table cells and populate form
            const setFieldValue = (selector, value) => {
                const element = document.querySelector(selector);
                if (element) {
                    element.value = value || '';
                } else {
                    console.warn('Element not found:', selector);
                }
            };
            
            // Populate form fields with table data
            setFieldValue('#editAppointmentModal input[name="patient_name"]', cells[1]?.textContent || '');
            setFieldValue('#editAppointmentModal input[name="patient_phone"]', cells[2]?.textContent || '');
            setFieldValue('#editAppointmentModal input[name="doctor_name"]', cells[3]?.textContent || '');
            setFieldValue('#editAppointmentModal select[name="department"]', cells[4]?.textContent || '');
            
            // Handle date - convert from display format to input format
            const dateText = cells[5]?.textContent || '';
            if (dateText && dateText !== 'N/A') {
                try {
                    const date = new Date(dateText);
                    const formattedDate = date.toISOString().split('T')[0];
                    setFieldValue('#editAppointmentModal input[name="appointment_date"]', formattedDate);
                } catch (e) {
                    console.warn('Could not parse date:', dateText);
                }
            }
            
            setFieldValue('#editAppointmentModal input[name="appointment_time"]', cells[6]?.textContent || '');
            setFieldValue('#editAppointmentModal select[name="appointment_type"]', cells[7]?.textContent?.toLowerCase() || '');
            
            // Handle status (remove badge formatting)
            const statusText = cells[8]?.textContent?.replace(/^\s+|\s+$/g, '') || '';
            setFieldValue('#editAppointmentModal select[name="status"]', statusText.toLowerCase());
            
            // Show the modal
            showModal('editAppointmentModal');
        } else {
            alert('Could not find appointment data in table. Appointment ID: ' + id);
        }
        
    } catch (error) {
        console.error('Edit appointment error:', error);
        alert('Error: ' + error.message);
    }
}

function viewAppointment(id) {
    try {
        
        // Get appointment data from the current table row
        const appointmentRow = document.querySelector(`button[onclick*="viewAppointment(${id})"]`).closest('tr');
        
        if (appointmentRow) {
            const cells = appointmentRow.querySelectorAll('td');
            
            // Extract data from table cells
            const appointmentData = {
                id: cells[0]?.textContent || id,
                patientName: cells[1]?.textContent || 'N/A',
                patientPhone: cells[2]?.textContent || 'N/A',
                doctorName: cells[3]?.textContent || 'N/A',
                department: cells[4]?.textContent || 'N/A',
                date: cells[5]?.textContent || 'N/A',
                time: cells[6]?.textContent || 'N/A',
                type: cells[7]?.textContent || 'N/A',
                status: cells[8]?.textContent || 'N/A'
            };
            
            // Create a detailed view window
            const appointmentDetails = `
                <h2>📅 Appointment Details</h2>
                <table style="width: 100%; border-collapse: collapse; margin: 20px 0;">
                    <tr><td style="padding: 12px; border: 1px solid #ddd; font-weight: bold; background: #f5f5f5;">Appointment ID:</td><td style="padding: 12px; border: 1px solid #ddd;">${appointmentData.id}</td></tr>
                    <tr><td style="padding: 12px; border: 1px solid #ddd; font-weight: bold; background: #f5f5f5;">Patient Name:</td><td style="padding: 12px; border: 1px solid #ddd;">${appointmentData.patientName}</td></tr>
                    <tr><td style="padding: 12px; border: 1px solid #ddd; font-weight: bold; background: #f5f5f5;">Patient Phone:</td><td style="padding: 12px; border: 1px solid #ddd;">${appointmentData.patientPhone}</td></tr>
                    <tr><td style="padding: 12px; border: 1px solid #ddd; font-weight: bold; background: #f5f5f5;">Doctor:</td><td style="padding: 12px; border: 1px solid #ddd;">${appointmentData.doctorName}</td></tr>
                    <tr><td style="padding: 12px; border: 1px solid #ddd; font-weight: bold; background: #f5f5f5;">Department:</td><td style="padding: 12px; border: 1px solid #ddd;">${appointmentData.department}</td></tr>
                    <tr><td style="padding: 12px; border: 1px solid #ddd; font-weight: bold; background: #f5f5f5;">Date:</td><td style="padding: 12px; border: 1px solid #ddd;">${appointmentData.date}</td></tr>
                    <tr><td style="padding: 12px; border: 1px solid #ddd; font-weight: bold; background: #f5f5f5;">Time:</td><td style="padding: 12px; border: 1px solid #ddd;">${appointmentData.time}</td></tr>
                    <tr><td style="padding: 12px; border: 1px solid #ddd; font-weight: bold; background: #f5f5f5;">Type:</td><td style="padding: 12px; border: 1px solid #ddd;">${appointmentData.type}</td></tr>
                    <tr><td style="padding: 12px; border: 1px solid #ddd; font-weight: bold; background: #f5f5f5;">Status:</td><td style="padding: 12px; border: 1px solid #ddd;">${appointmentData.status}</td></tr>
                </table>
                
                <div style="background: #f8f9fa; padding: 15px; border-radius: 5px; margin: 20px 0;">
                    <h3 style="margin-top: 0;">📋 Appointment Summary</h3>
                    <p><strong>Patient:</strong> ${appointmentData.patientName}</p>
                    <p><strong>Doctor:</strong> ${appointmentData.doctorName}</p>
                    <p><strong>Scheduled:</strong> ${appointmentData.date} at ${appointmentData.time}</p>
                    <p><strong>Department:</strong> ${appointmentData.department}</p>
                    <p><strong>Status:</strong> ${appointmentData.status}</p>
                </div>
            `;
            
            // Open in new window
            const viewWindow = window.open('', '_blank', 'width=700,height=650,scrollbars=yes');
            viewWindow.document.write(`
                <html>
                    <head>
                        <title>Appointment Details - ${appointmentData.patientName}</title>
                        <style>
                            body { 
                                font-family: Arial, sans-serif; 
                                padding: 30px; 
                                background: #f8f9fa;
                                margin: 0;
                            }
                            h2 { 
                                color: #333; 
                                border-bottom: 3px solid #007bff; 
                                padding-bottom: 10px; 
                                margin-bottom: 20px;
                            }
                            .btn { 
                                padding: 12px 24px; 
                                margin: 20px 5px; 
                                border: none; 
                                border-radius: 5px; 
                                cursor: pointer; 
                                font-size: 16px;
                            }
                            .btn-primary { 
                                background: #007bff; 
                                color: white; 
                            }
                            .btn-primary:hover {
                                background: #0056b3;
                            }
                            .container {
                                background: white;
                                padding: 30px;
                                border-radius: 10px;
                                box-shadow: 0 2px 10px rgba(0,0,0,0.1);
                            }
                        </style>
                    </head>
                    <body>
                        <div class="container">
                            ${appointmentDetails}
                            <div style="text-align: center; margin-top: 30px;">
                                <button class="btn btn-primary" onclick="window.close()">✖️ Close Window</button>
                            </div>
                        </div>
                    </body>
                </html>
            `);
        } else {
            alert('Could not find appointment data in table. Appointment ID: ' + id);
        }
        
    } catch (error) {
        console.error('View appointment error:', error);
        alert('Error viewing appointment: ' + error.message);
    }
}

// Form submission handlers
document.addEventListener('DOMContentLoaded', function() {
    // Add User Form
    document.getElementById('addUserForm').addEventListener('submit', function(e) {
        e.preventDefault();
        const formData = new FormData(this);
        
        fetch('<?= base_url("super-admin/users/add") ?>', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                closeModal();
                loadUsers();
                alert('User added successfully!');
            } else {
                alert('Error adding user: ' + (data.message || 'Unknown error'));
            }
        });
    });

    // Add Patient Form
    document.getElementById('addPatientForm').addEventListener('submit', function(e) {
        e.preventDefault();
        const formData = new FormData(this);
        
        fetch('<?= base_url("super-admin/patients/add") ?>', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                closeModal();
                loadPatients();
                alert('Patient added successfully!');
            } else {
                alert('Error adding patient: ' + (data.message || 'Unknown error'));
            }
        });
    });

    // Add Appointment Form
    document.getElementById('addAppointmentForm').addEventListener('submit', function(e) {
        e.preventDefault();
        const formData = new FormData(this);
        
        fetch('<?= base_url("super-admin/appointments/add") ?>', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                closeModal();
                loadAppointments();
                alert('Appointment scheduled successfully!');
            } else {
                alert('Error scheduling appointment: ' + (data.message || 'Unknown error'));
            }
        });
    });

    // Edit Patient Form
    document.getElementById('editPatientForm').addEventListener('submit', function(e) {
        e.preventDefault();
        const formData = new FormData(this);
        const patientId = this.dataset.patientId;
        
        fetch(`<?= base_url("super-admin/patients/edit") ?>/${patientId}`, {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                closeModal();
                loadPatients();
                alert('Patient updated successfully!');
            } else {
                alert('Error updating patient: ' + (data.message || 'Unknown error'));
            }
        });
    });

    // Edit User Form
    document.getElementById('editUserForm').addEventListener('submit', function(e) {
        e.preventDefault();
        const formData = new FormData(this);
        const userId = this.dataset.userId;
        
        fetch(`<?= base_url("super-admin/users/edit") ?>/${userId}`, {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                closeModal();
                loadUsers();
                alert('User updated successfully!');
            } else {
                alert('Error updating user: ' + (data.message || 'Unknown error'));
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error updating user: ' + error.message);
        });
    });

    // Edit Appointment Form
    document.getElementById('editAppointmentForm').addEventListener('submit', function(e) {
        e.preventDefault();
        
        const formData = new FormData(this);
        const appointmentId = this.dataset.appointmentId;
        
        // For now, just show success message since we don't have backend endpoint yet
        alert('Appointment updated successfully!\n\nNote: This is a demo. Backend integration needed for full functionality.');
        closeModal();
        loadAppointments(); // Refresh the appointments list
        
        // Uncomment this when backend is ready:
        /*
        fetch(`<?= base_url("super-admin/appointments/edit") ?>/${appointmentId}`, {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                closeModal();
                loadAppointments();
                alert('Appointment updated successfully!');
            } else {
                alert('Error updating appointment: ' + (data.message || 'Unknown error'));
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error updating appointment: ' + error.message);
        });
        */
    });
});

// Medical Services Functions
function loadMedicalServices() {
    fetch('<?= base_url("medical-services/api/services") ?>')
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                updateMedicalServicesStats(data.data);
                displayRecentServices(data.data.slice(0, 10)); // Show first 10 services
            } else {
                console.error('Error loading medical services:', data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            document.getElementById('total-services').textContent = 'Error';
            document.getElementById('active-services').textContent = 'Error';
        });
}

function updateMedicalServicesStats(services) {
    const totalServices = services.length;
    const activeServices = services.filter(service => service.status === 'active').length;
    
    // Calculate average prices by category
    const consultationServices = services.filter(service => service.category === 'consultation' && service.status === 'active');
    const laboratoryServices = services.filter(service => service.category === 'laboratory' && service.status === 'active');
    
    const avgConsultationPrice = consultationServices.length > 0 
        ? consultationServices.reduce((sum, service) => sum + parseFloat(service.price), 0) / consultationServices.length 
        : 0;
    
    const avgLaboratoryPrice = laboratoryServices.length > 0 
        ? laboratoryServices.reduce((sum, service) => sum + parseFloat(service.price), 0) / laboratoryServices.length 
        : 0;
    
    document.getElementById('total-services').textContent = totalServices;
    document.getElementById('active-services').textContent = activeServices;
    document.getElementById('avg-consultation-price').textContent = avgConsultationPrice > 0 
        ? '₱' + avgConsultationPrice.toFixed(2) 
        : 'N/A';
    document.getElementById('avg-laboratory-price').textContent = avgLaboratoryPrice > 0 
        ? '₱' + avgLaboratoryPrice.toFixed(2) 
        : 'N/A';
}

function displayRecentServices(services) {
    const tbody = document.getElementById('recent-services-table');
    
    if (services.length === 0) {
        tbody.innerHTML = `
            <tr>
                <td colspan="5" style="text-align: center; padding: 20px;">
                    No medical services found. <a href="<?= base_url('medical-services/create') ?>">Add the first service</a>
                </td>
            </tr>
        `;
        return;
    }
    
    tbody.innerHTML = services.map(service => `
        <tr>
            <td><strong>${escapeHtml(service.service_name)}</strong></td>
            <td>
                <span class="category-badge category-${service.category}">
                    ${service.category.charAt(0).toUpperCase() + service.category.slice(1)}
                </span>
            </td>
            <td style="font-weight: 600; color: var(--primary);">₱${parseFloat(service.price).toLocaleString('en-US', {minimumFractionDigits: 2})}</td>
            <td>
                <span class="status-badge status-${service.status}">
                    ${service.status.charAt(0).toUpperCase() + service.status.slice(1)}
                </span>
            </td>
            <td>
                <div style="display: flex; gap: 8px;">
                    <a href="<?= base_url('medical-services/edit/') ?>${service.id}" 
                       class="btn btn-sm" style="background: var(--warning); color: white; text-decoration: none;">
                        ✏️ Edit
                    </a>
                    <button onclick="viewServiceDetails(${service.id})" 
                            class="btn btn-sm" style="background: var(--info); color: white;">
                        👁️ View
                    </button>
                </div>
            </td>
        </tr>
    `).join('');
}

function showServiceAnalytics() {
    // Create a detailed analytics popup
    fetch('<?= base_url("medical-services/api/services") ?>')
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const services = data.data;
                showAnalyticsPopup(services);
            } else {
                alert('Error loading service analytics: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error loading service analytics.');
        });
}

function showAnalyticsPopup(services) {
    // Calculate detailed analytics
    const categories = ['consultation', 'laboratory', 'imaging', 'surgery', 'therapy', 'emergency', 'other'];
    const analytics = {};
    
    categories.forEach(category => {
        const categoryServices = services.filter(s => s.category === category && s.status === 'active');
        analytics[category] = {
            count: categoryServices.length,
            totalRevenue: categoryServices.reduce((sum, s) => sum + parseFloat(s.price), 0),
            avgPrice: categoryServices.length > 0 
                ? categoryServices.reduce((sum, s) => sum + parseFloat(s.price), 0) / categoryServices.length 
                : 0,
            minPrice: categoryServices.length > 0 
                ? Math.min(...categoryServices.map(s => parseFloat(s.price))) 
                : 0,
            maxPrice: categoryServices.length > 0 
                ? Math.max(...categoryServices.map(s => parseFloat(s.price))) 
                : 0
        };
    });
    
    const popup = window.open('', '_blank', 'width=800,height=600,scrollbars=yes,resizable=yes');
    
    popup.document.write(`
        <!DOCTYPE html>
        <html>
            <head>
                <title>Medical Services Analytics</title>
                <style>
                    body { 
                        font-family: 'Segoe UI', Arial, sans-serif; 
                        margin: 0; 
                        padding: 20px; 
                        background: #f8fafc; 
                    }
                    .container { 
                        background: white; 
                        padding: 30px; 
                        border-radius: 10px; 
                        box-shadow: 0 2px 10px rgba(0,0,0,0.1); 
                    }
                    .header { 
                        text-align: center; 
                        margin-bottom: 30px; 
                        border-bottom: 2px solid #e2e8f0; 
                        padding-bottom: 20px; 
                    }
                    .title { 
                        font-size: 28px; 
                        font-weight: 700; 
                        color: #2563eb; 
                        margin: 0; 
                    }
                    .analytics-grid { 
                        display: grid; 
                        grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); 
                        gap: 20px; 
                        margin-bottom: 30px; 
                    }
                    .category-card { 
                        background: #f8fafc; 
                        border: 1px solid #e2e8f0; 
                        border-radius: 8px; 
                        padding: 20px; 
                    }
                    .category-title { 
                        font-size: 18px; 
                        font-weight: 600; 
                        color: #1f2937; 
                        margin: 0 0 15px 0; 
                        text-transform: capitalize; 
                    }
                    .stat-row { 
                        display: flex; 
                        justify-content: space-between; 
                        margin-bottom: 8px; 
                    }
                    .stat-label { 
                        color: #6b7280; 
                        font-size: 14px; 
                    }
                    .stat-value { 
                        font-weight: 600; 
                        color: #1f2937; 
                        font-size: 14px; 
                    }
                    .price-value { 
                        color: #2563eb; 
                    }
                    .btn { 
                        background: #2563eb; 
                        color: white; 
                        border: none; 
                        padding: 10px 20px; 
                        border-radius: 8px; 
                        cursor: pointer; 
                        font-size: 14px; 
                        margin: 0 auto; 
                        display: block; 
                    }
                    .summary-card { 
                        background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%); 
                        color: white; 
                        border-radius: 8px; 
                        padding: 20px; 
                        margin-bottom: 20px; 
                        text-align: center; 
                    }
                    .summary-title { 
                        font-size: 16px; 
                        margin: 0 0 10px 0; 
                        opacity: 0.9; 
                    }
                    .summary-value { 
                        font-size: 24px; 
                        font-weight: 700; 
                        margin: 0; 
                    }
                </style>
            </head>
            <body>
                <div class="container">
                    <div class="header">
                        <h1 class="title">📊 Medical Services Analytics</h1>
                    </div>
                    
                    <div class="summary-card">
                        <p class="summary-title">Total Active Services</p>
                        <h2 class="summary-value">${services.filter(s => s.status === 'active').length}</h2>
                    </div>
                    
                    <div class="analytics-grid">
                        ${categories.map(category => {
                            const data = analytics[category];
                            if (data.count === 0) return '';
                            
                            return `
                                <div class="category-card">
                                    <h3 class="category-title">${category}</h3>
                                    <div class="stat-row">
                                        <span class="stat-label">Services Count:</span>
                                        <span class="stat-value">${data.count}</span>
                                    </div>
                                    <div class="stat-row">
                                        <span class="stat-label">Average Price:</span>
                                        <span class="stat-value price-value">₱${data.avgPrice.toFixed(2)}</span>
                                    </div>
                                    <div class="stat-row">
                                        <span class="stat-label">Price Range:</span>
                                        <span class="stat-value price-value">₱${data.minPrice.toFixed(2)} - ₱${data.maxPrice.toFixed(2)}</span>
                                    </div>
                                    <div class="stat-row">
                                        <span class="stat-label">Potential Revenue:</span>
                                        <span class="stat-value price-value">₱${data.totalRevenue.toFixed(2)}</span>
                                    </div>
                                </div>
                            `;
                        }).join('')}
                    </div>
                    
                    <div style="text-align: center; margin-top: 30px;">
                        <button class="btn" onclick="window.close()">✖️ Close Window</button>
                    </div>
                </div>
            </body>
        </html>
    `);
}

function viewServiceDetails(serviceId) {
    fetch(`<?= base_url("medical-services/api/service/") ?>${serviceId}`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const service = data.data;
                const popup = window.open('', '_blank', 'width=600,height=500,scrollbars=yes,resizable=yes');
                
                popup.document.write(`
                    <!DOCTYPE html>
                    <html>
                        <head>
                            <title>Medical Service Details</title>
                            <style>
                                body { 
                                    font-family: 'Segoe UI', Arial, sans-serif; 
                                    margin: 0; 
                                    padding: 20px; 
                                    background: #f8fafc; 
                                }
                                .container { 
                                    background: white; 
                                    padding: 30px; 
                                    border-radius: 10px; 
                                    box-shadow: 0 2px 10px rgba(0,0,0,0.1); 
                                }
                                .service-header { 
                                    border-bottom: 2px solid #e2e8f0; 
                                    padding-bottom: 15px; 
                                    margin-bottom: 20px; 
                                }
                                .service-title { 
                                    font-size: 24px; 
                                    font-weight: 700; 
                                    color: #2563eb; 
                                    margin: 0; 
                                }
                                .service-id { 
                                    color: #64748b; 
                                    font-size: 14px; 
                                }
                                .detail-grid { 
                                    display: grid; 
                                    grid-template-columns: 1fr 1fr; 
                                    gap: 20px; 
                                    margin-bottom: 20px; 
                                }
                                .detail-item { 
                                    display: flex; 
                                    flex-direction: column; 
                                    gap: 5px; 
                                }
                                .detail-label { 
                                    font-weight: 600; 
                                    color: #374151; 
                                    font-size: 14px; 
                                }
                                .detail-value { 
                                    color: #1f2937; 
                                    font-size: 16px; 
                                }
                                .price-value { 
                                    font-size: 20px; 
                                    font-weight: 700; 
                                    color: #2563eb; 
                                }
                                .category-badge { 
                                    display: inline-block; 
                                    padding: 4px 8px; 
                                    border-radius: 6px; 
                                    font-size: 12px; 
                                    font-weight: 500; 
                                    text-transform: capitalize; 
                                }
                                .status-badge { 
                                    display: inline-block; 
                                    padding: 4px 8px; 
                                    border-radius: 6px; 
                                    font-size: 12px; 
                                    font-weight: 500; 
                                    text-transform: capitalize; 
                                }
                                .category-consultation { background: #dbeafe; color: #1e40af; }
                                .category-laboratory { background: #dcfce7; color: #166534; }
                                .category-imaging { background: #fef3c7; color: #92400e; }
                                .category-surgery { background: #fecaca; color: #991b1b; }
                                .category-therapy { background: #e0e7ff; color: #3730a3; }
                                .category-emergency { background: #fee2e2; color: #dc2626; }
                                .category-other { background: #f3f4f6; color: #374151; }
                                .status-active { background: #dcfce7; color: #166534; }
                                .status-inactive { background: #fef3c7; color: #92400e; }
                                .status-discontinued { background: #fecaca; color: #991b1b; }
                                .description-section { 
                                    margin-top: 20px; 
                                    padding-top: 20px; 
                                    border-top: 1px solid #e2e8f0; 
                                }
                                .btn { 
                                    background: #2563eb; 
                                    color: white; 
                                    border: none; 
                                    padding: 10px 20px; 
                                    border-radius: 8px; 
                                    cursor: pointer; 
                                    font-size: 14px; 
                                }
                            </style>
                        </head>
                        <body>
                            <div class="container">
                                <div class="service-header">
                                    <h1 class="service-title">${escapeHtml(service.service_name)}</h1>
                                    <div class="service-id">Service ID: #${service.id}</div>
                                </div>
                                
                                <div class="detail-grid">
                                    <div class="detail-item">
                                        <span class="detail-label">Category</span>
                                        <span class="detail-value">
                                            <span class="category-badge category-${service.category}">
                                                ${service.category.charAt(0).toUpperCase() + service.category.slice(1)}
                                            </span>
                                        </span>
                                    </div>
                                    <div class="detail-item">
                                        <span class="detail-label">Status</span>
                                        <span class="detail-value">
                                            <span class="status-badge status-${service.status}">
                                                ${service.status.charAt(0).toUpperCase() + service.status.slice(1)}
                                            </span>
                                        </span>
                                    </div>
                                    <div class="detail-item">
                                        <span class="detail-label">Price</span>
                                        <span class="detail-value price-value">₱${parseFloat(service.price).toLocaleString('en-US', {minimumFractionDigits: 2})}</span>
                                    </div>
                                    <div class="detail-item">
                                        <span class="detail-label">Last Updated</span>
                                        <span class="detail-value">${new Date(service.updated_at).toLocaleDateString('en-US', {year: 'numeric', month: 'long', day: 'numeric'})}</span>
                                    </div>
                                </div>
                                
                                ${service.description ? `
                                    <div class="description-section">
                                        <div class="detail-label">Description</div>
                                        <div class="detail-value" style="margin-top: 10px; line-height: 1.6;">
                                            ${escapeHtml(service.description)}
                                        </div>
                                    </div>
                                ` : ''}
                                
                                <div style="text-align: center; margin-top: 30px;">
                                    <button class="btn" onclick="window.close()">✖️ Close Window</button>
                                </div>
                            </div>
                        </body>
                    </html>
                `);
            } else {
                alert('Error loading service details: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error loading service details.');
        });
}

// Helper function to escape HTML
function escapeHtml(text) {
    const map = {
        '&': '&amp;',
        '<': '&lt;',
        '>': '&gt;',
        '"': '&quot;',
        "'": '&#039;'
    };
    return text ? text.replace(/[&<>"']/g, function(m) { return map[m]; }) : '';
}

// Initialize dashboard
document.addEventListener('DOMContentLoaded', function() {
    loadTabData('users');
    
    // Load medical services data when medical tab is shown
    const medicalTab = document.querySelector('button[onclick="showTab(\'medical\')"]');
    if (medicalTab) {
        medicalTab.addEventListener('click', function() {
            setTimeout(loadMedicalServices, 100); // Small delay to ensure tab is shown
        });
    }
});
</script>
<?= $this->endSection() ?>
