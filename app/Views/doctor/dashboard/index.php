<?= $this->extend('template/header') ?>

<?= $this->section('title') ?>Doctor Dashboard<?= $this->endSection() ?>

<?= $this->section('content') ?>
<style>
    .dashboard-container { 
        display: grid; 
        gap: 24px; 
        padding: 0;
    }
    
    .welcome-section {
        background: linear-gradient(135deg, #2e7d32 0%, #4caf50 100%);
        border-radius: 16px;
        padding: 32px;
        box-shadow: 0 4px 20px rgba(46, 125, 50, 0.2);
        color: white;
        position: relative;
        overflow: hidden;
    }
    
    .welcome-section::before {
        content: '';
        position: absolute;
        top: -50%;
        right: -10%;
        width: 300px;
        height: 300px;
        background: rgba(255, 255, 255, 0.1);
        border-radius: 50%;
    }
    
    .welcome-section h2 {
        font-family: 'Playfair Display', serif;
        margin: 0 0 8px;
        font-size: 32px;
        font-weight: 700;
        position: relative;
        z-index: 1;
    }
    
    .welcome-section p { 
        margin: 0;
        opacity: 0.95;
        font-size: 16px;
        position: relative;
        z-index: 1;
    }
    
    .refresh-indicator {
        position: absolute;
        top: 20px;
        right: 20px;
        background: rgba(255, 255, 255, 0.2);
        padding: 8px 16px;
        border-radius: 20px;
        font-size: 12px;
        display: flex;
        align-items: center;
        gap: 8px;
        z-index: 2;
    }
    
    .refresh-indicator .spinner {
        width: 14px;
        height: 14px;
        border: 2px solid rgba(255, 255, 255, 0.3);
        border-top-color: white;
        border-radius: 50%;
        animation: spin 1s linear infinite;
    }
    
    @keyframes spin {
        to { transform: rotate(360deg); }
    }
    
    .stats-container { 
        width: 100%; 
    }
    
    .stats-grid { 
        display: grid; 
        grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); 
        gap: 20px; 
    }
    
    .stat-card {
        background: #ffffff;
        border-radius: 16px;
        border: 1px solid #e5e7eb;
        padding: 24px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
        position: relative;
        overflow: hidden;
        transition: all 0.3s ease;
        cursor: pointer;
    }
    
    .stat-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 4px;
        background: linear-gradient(90deg, #2e7d32, #4caf50);
    }
    
    .stat-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 8px 24px rgba(46, 125, 50, 0.15);
    }
    
    .stat-card.primary::before { background: linear-gradient(90deg, #1976d2, #42a5f5); }
    .stat-card.warning::before { background: linear-gradient(90deg, #f57c00, #ff9800); }
    .stat-card.info::before { background: linear-gradient(90deg, #0288d1, #03a9f4); }
    .stat-card.success::before { background: linear-gradient(90deg, #388e3c, #66bb6a); }
    .stat-card.danger::before { background: linear-gradient(90deg, #d32f2f, #ef5350); }
    
    .stat-icon {
        width: 48px;
        height: 48px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-bottom: 16px;
        font-size: 24px;
    }
    
    .stat-card.primary .stat-icon { background: rgba(25, 118, 210, 0.1); color: #1976d2; }
    .stat-card.warning .stat-icon { background: rgba(245, 124, 0, 0.1); color: #f57c00; }
    .stat-card.info .stat-icon { background: rgba(2, 136, 209, 0.1); color: #0288d1; }
    .stat-card.success .stat-icon { background: rgba(56, 142, 60, 0.1); color: #388e3c; }
    .stat-card.danger .stat-icon { background: rgba(211, 47, 47, 0.1); color: #d32f2f; }
    
    .stat-title {
        margin: 0 0 8px;
        font-size: 14px;
        color: #64748b;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
    
    .stat-value {
        margin: 0;
        font-size: 36px;
        font-weight: 800;
        color: #1e293b;
        line-height: 1;
    }
    
    .patients-section {
        margin-top: 8px;
        background: white;
        border-radius: 16px;
        padding: 24px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
    }
    
    .patients-section h3 {
        color: #2e7d32;
        margin: 0 0 20px;
        font-size: 20px;
        font-weight: 700;
        display: flex;
        align-items: center;
        gap: 12px;
    }
    
    .patients-section h3::before {
        content: '';
        width: 4px;
        height: 24px;
        background: #2e7d32;
        border-radius: 2px;
    }
    
    .table-container {
        background: white;
        border-radius: 12px;
        overflow: hidden;
        border: 1px solid #e5e7eb;
    }
    
    .data-table {
        width: 100%;
        border-collapse: collapse;
    }
    
    .data-table thead {
        background: linear-gradient(135deg, #e8f5e9 0%, #f1f8f4 100%);
    }
    
    .data-table th {
        padding: 16px;
        text-align: left;
        font-weight: 700;
        color: #2e7d32;
        font-size: 13px;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        border-bottom: 2px solid #c8e6c9;
    }
    
    .data-table td {
        padding: 16px;
        border-bottom: 1px solid #f1f5f9;
        color: #475569;
    }
    
    .data-table tbody tr {
        transition: background 0.2s ease;
    }
    
    .data-table tbody tr:hover {
        background: #f8fafc;
    }
    
    .data-table tbody tr:last-child td {
        border-bottom: none;
    }
    
    .btn {
        padding: 8px 16px;
        border-radius: 8px;
        text-decoration: none;
        font-size: 13px;
        font-weight: 600;
        margin-right: 8px;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        transition: all 0.2s ease;
        border: none;
        cursor: pointer;
    }
    
    .btn-primary {
        background: #2e7d32;
        color: white;
    }
    
    .btn-primary:hover {
        background: #1b5e20;
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(46, 125, 50, 0.3);
    }
    
    .btn-warning {
        background: #f59e0b;
        color: white;
    }
    
    .btn-warning:hover {
        background: #d97706;
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(245, 158, 11, 0.3);
    }
    
    .btn-info {
        background: #0288d1;
        color: white;
    }
    
    .btn-info:hover {
        background: #0277bd;
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(2, 136, 209, 0.3);
    }
    
    .empty-state {
        text-align: center;
        padding: 60px 20px;
        color: #94a3b8;
    }
    
    .empty-state i {
        font-size: 64px;
        margin-bottom: 16px;
        opacity: 0.5;
    }
    
    .empty-state h4 {
        margin: 0 0 8px;
        color: #64748b;
    }
    
    @media (max-width: 768px) {
        .welcome-section { padding: 24px; }
        .welcome-section h2 { font-size: 24px; }
        .stat-value { font-size: 28px; }
        .stats-grid { grid-template-columns: 1fr; }
        .data-table { font-size: 14px; }
        .data-table th,
        .data-table td { padding: 12px; }
    }
</style>

<div class="dashboard-container">
    <!-- Welcome Section -->
    <div class="welcome-section">
        <div class="refresh-indicator" id="refreshIndicator">
            <div class="spinner" id="refreshSpinner" style="display: none;"></div>
            <span id="lastUpdate">Auto-updating...</span>
        </div>
        <h2>Welcome back, Dr. <?= esc($name ?? 'Doctor') ?></h2>
        <p>Here's what's happening with your patients today</p>
    </div>

    <!-- Stats Grid -->
    <div class="stats-container">
        <div class="stats-grid">
            <div class="stat-card primary">
                <div class="stat-icon">
                    <i class="fas fa-calendar-check"></i>
                </div>
                <div class="stat-title">Today's Appointments</div>
                <div class="stat-value" id="appointments_count"><?= $appointmentsCount ?? '0' ?></div>
            </div>
            
            <div class="stat-card success">
                <div class="stat-icon">
                    <i class="fas fa-user-check"></i>
                </div>
                <div class="stat-title">Patients Seen Today</div>
                <div class="stat-value" id="patients_seen_today"><?= $patientsSeenToday ?? '0' ?></div>
            </div>
            
            <div class="stat-card warning">
                <div class="stat-icon">
                    <i class="fas fa-clock"></i>
                </div>
                <div class="stat-title">Pending Consultations</div>
                <div class="stat-value" id="pending_consultations">0</div>
            </div>
            
            <div class="stat-card info">
                <div class="stat-icon">
                    <i class="fas fa-calendar-alt"></i>
                </div>
                <div class="stat-title">Upcoming (7 days)</div>
                <div class="stat-value" id="upcoming_consultations">0</div>
            </div>
            
            <div class="stat-card success">
                <div class="stat-icon">
                    <i class="fas fa-users"></i>
                </div>
                <div class="stat-title">My Assigned Patients</div>
                <div class="stat-value" id="assigned_patients_count"><?= $assignedPatientsCount ?? '0' ?></div>
            </div>
        </div>
    </div>

    <!-- Assigned Patients List -->
    <div class="patients-section">
        <h3>
            <i class="fas fa-list"></i>
            My Assigned Patients
        </h3>
        <div class="table-container">
            <div id="patientsTableContainer">
                <?php if (!empty($assignedPatients ?? [])): ?>
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Name</th>
                                <th>Birthdate</th>
                                <th>Gender</th>
                                <th>Contact</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody id="patientsTableBody">
                            <?php foreach ($assignedPatients as $patient): ?>
                                <tr>
                                    <td>#<?= esc($patient['id']) ?></td>
                                    <td><strong><?= esc($patient['firstname'] . ' ' . $patient['lastname']) ?></strong></td>
                                    <td><?= esc(date('M d, Y', strtotime($patient['birthdate']))) ?></td>
                                    <td><?= esc(ucfirst($patient['gender'])) ?></td>
                                    <td><?= esc($patient['contact'] ?? 'N/A') ?></td>
                                    <td>
                                        <a href="<?= site_url('doctor/patients/view/' . $patient['id']) ?>" class="btn btn-info">
                                            <i class="fas fa-eye"></i> View
                                        </a>
                                        <a href="<?= site_url('doctor/patients/edit/' . $patient['id']) ?>" class="btn btn-warning">
                                            <i class="fas fa-edit"></i> Edit
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php else: ?>
                    <div class="empty-state">
                        <i class="fas fa-user-injured"></i>
                        <h4>No Patients Assigned</h4>
                        <p>You don't have any assigned patients yet. Patients assigned from the admin panel will appear here.</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<script>
const endpoint = '<?= site_url('doctor/dashboard/stats') ?>';

async function refreshDashboard() {
    const spinner = document.getElementById('refreshSpinner');
    const lastUpdate = document.getElementById('lastUpdate');
    
    try {
        spinner.style.display = 'block';
        lastUpdate.textContent = 'Updating...';
        
        const res = await fetch(endpoint, { 
            headers: { 'Accept': 'application/json' } 
        });
        
        if (!res.ok) throw new Error('Network error');
        
        const data = await res.json();
        
        // Update statistics
        const setText = (id, val) => {
            const el = document.getElementById(id);
            if (el) el.textContent = val ?? '0';
        };
        
        setText('appointments_count', data.appointments_count);
        setText('patients_seen_today', data.patients_seen_today);
        setText('assigned_patients_count', data.assigned_patients_count);
        setText('pending_consultations', data.pending_consultations);
        setText('upcoming_consultations', data.upcoming_consultations);
        
        // Update patients table
        const tableBody = document.getElementById('patientsTableBody');
        const tableContainer = document.getElementById('patientsTableContainer');
        
        if (data.assigned_patients && data.assigned_patients.length > 0) {
            let tableHTML = `
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Birthdate</th>
                            <th>Gender</th>
                            <th>Contact</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody id="patientsTableBody">
            `;
            
            data.assigned_patients.forEach(patient => {
                const birthdate = new Date(patient.birthdate).toLocaleDateString('en-US', {
                    year: 'numeric',
                    month: 'short',
                    day: 'numeric'
                });
                
                tableHTML += `
                    <tr>
                        <td>#${patient.id}</td>
                        <td><strong>${patient.firstname} ${patient.lastname}</strong></td>
                        <td>${birthdate}</td>
                        <td>${patient.gender.charAt(0).toUpperCase() + patient.gender.slice(1)}</td>
                        <td>${patient.contact || 'N/A'}</td>
                        <td>
                            <a href="<?= site_url('doctor/patients/view/') ?>${patient.id}" class="btn btn-info">
                                <i class="fas fa-eye"></i> View
                            </a>
                            <a href="<?= site_url('doctor/patients/edit/') ?>${patient.id}" class="btn btn-warning">
                                <i class="fas fa-edit"></i> Edit
                            </a>
                        </td>
                    </tr>
                `;
            });
            
            tableHTML += '</tbody></table>';
            tableContainer.innerHTML = tableHTML;
        } else {
            tableContainer.innerHTML = `
                <div class="empty-state">
                    <i class="fas fa-user-injured"></i>
                    <h4>No Patients Assigned</h4>
                    <p>You don't have any assigned patients yet. Patients assigned from the admin panel will appear here.</p>
                </div>
            `;
        }
        
        // Update last refresh time
        const now = new Date();
        lastUpdate.textContent = `Updated: ${now.toLocaleTimeString()}`;
        
    } catch (e) {
        console.error('Dashboard refresh error:', e);
        lastUpdate.textContent = 'Update failed';
    } finally {
        spinner.style.display = 'none';
    }
}

// Initialize on page load
window.addEventListener('DOMContentLoaded', () => {
    refreshDashboard();
    // Auto-refresh every 10 seconds
    setInterval(refreshDashboard, 10000);
});

// Also refresh when page becomes visible again
document.addEventListener('visibilitychange', () => {
    if (!document.hidden) {
        refreshDashboard();
    }
});
</script>

<?= $this->endSection() ?>
