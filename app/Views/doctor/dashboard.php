<?= $this->extend('template/header') ?>

<?= $this->section('title') ?>Doctor Dashboard<?= $this->endSection() ?>

<?= $this->section('content') ?>
<style>
    .dashboard-container { display: grid; gap: 24px; }
    .welcome-section {
        background: #ffffff;
        border-radius: 12px;
        border: 1px solid #e5e7eb;
        padding: 24px;
        box-shadow: 0 2px 6px rgba(15, 23, 42, 0.08);
        background-image: linear-gradient(135deg, rgba(76,175,80,0.06), rgba(46,125,50,0.06));
    }
    .welcome-section h2 {
        font-family: 'Playfair Display', serif;
        color: var(--primary-color);
        margin: 0 0 6px;
        font-size: 28px;
        letter-spacing: -0.01em;
    }
    .welcome-section p { color: #64748b; margin: 0; }
    .stats-container { width: 100%; }
    .stats-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 16px; }
    .stat-card {
        background: #ffffff; border-radius: 12px; border: 1px solid #e5e7eb; padding: 20px;
        box-shadow: 0 2px 6px rgba(15, 23, 42, 0.08); position: relative; overflow: hidden;
        transition: var(--transition);
    }
    .stat-card::before { content: ''; position: absolute; top: 0; left: 0; right: 0; height: 4px; background: linear-gradient(90deg, var(--gradient-1), var(--gradient-2)); }
    .stat-card:hover { transform: translateY(-2px); box-shadow: 0 8px 24px rgba(16, 24, 40, 0.12); }
    .stat-title { margin: 0; font-size: 14px; color: #2e7d32; font-weight: 700; }
    .stat-value { margin-top: 10px; font-size: 32px; font-weight: 800; color: #1f2937; }
    @media (max-width: 600px) { .welcome-section { padding: 18px; } .stat-value { font-size: 28px; } }
</style>
<div class="dashboard-container">
    <!-- Welcome Section -->
    <div class="welcome-section">
        <h2>Welcome back, Dr. <?= esc($name ?? 'Doctor') ?></h2>
        <p>Here's what's happening with your patients today</p>
    </div>

    <!-- Stats Grid -->
    <div class="stats-container">
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-title">Today's Appointments</div>
                <div class="stat-value"><?= $appointmentsCount ?? '0' ?></div>
            </div>
            
            <div class="stat-card">
                <div class="stat-title">Patients Seen</div>
                <div class="stat-value"><?= $patientsSeenToday ?? '0' ?></div>
            </div>
            
            <div class="stat-card">
                <div class="stat-title">Pending Results</div>
                <div class="stat-value"><?= $pendingLabResults ?? '0' ?></div>
            </div>
            
            <div class="stat-card">
                <div class="stat-title">Prescriptions</div>
                <div class="stat-value"><?= $prescriptionsCount ?? '0' ?></div>
            </div>
            
            <div class="stat-card">
                <div class="stat-title">My Assigned Patients</div>
                <div class="stat-value"><?= $assignedPatientsCount ?? '0' ?></div>
            </div>
        </div>
    </div>

    <!-- Assigned Patients List -->
    <?php if (!empty($assignedPatients ?? [])): ?>
        <div class="patients-section">
            <h3 style="color: #2e7d32; margin-bottom: 16px;">My Assigned Patients</h3>
            <div class="table-container">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Birthdate</th>
                            <th>Gender</th>
                            <th>Contact</th>
                        </tr>
                    </thead>
                    <tbody id="assignedPatientsTableBody">
                        <?php foreach ($assignedPatients as $patient): ?>
                            <tr>
                                <td>#<?= esc($patient['id']) ?></td>
                                <td><?= esc($patient['firstname'] . ' ' . $patient['lastname']) ?></td>
                                <td><?= esc($patient['birthdate']) ?></td>
                                <td><?= esc(ucfirst($patient['gender'])) ?></td>
                                <td><?= esc($patient['contact'] ?? 'N/A') ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    <?php endif; ?>
</div>

<style>
.patients-section { margin-top: 24px; }
.table-container { background: white; border-radius: 8px; overflow: hidden; }
.data-table { width: 100%; border-collapse: collapse; }
.data-table th { background: #e8f5e9; padding: 12px; text-align: left; font-weight: 600; color: #2e7d32; }
.data-table td { padding: 12px; border-bottom: 1px solid #e5e7eb; }
</style>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialize tooltips if any
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });

    // Real-time dashboard updates
    const doctorStatsEndpoint = '<?= site_url('doctor/dashboard/stats') ?>';
    
    async function refreshDoctorDashboard() {
        try {
            const response = await fetch(doctorStatsEndpoint, {
                headers: { 'Accept': 'application/json' }
            });
            
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            
            const data = await response.json();
            
            // Update stat cards
            const setText = (id, value) => {
                const element = document.getElementById(id);
                if (element) {
                    element.textContent = value ?? '0';
                }
            };
            
            setText('appointmentsCount', data.appointments_count ?? '0');
            setText('patientsSeenToday', data.patients_seen_today ?? '0');
            setText('pendingLabRequestsCount', data.pending_lab_requests_count ?? '0');
            setText('pendingOrders', data.pending_orders ?? '0');
            setText('assignedPatientsCount', data.assigned_patients_count ?? '0');
            
            // Update assigned patients table if exists
            const patientsTableBody = document.getElementById('assignedPatientsTableBody');
            if (patientsTableBody) {
                if (data.assigned_patients && data.assigned_patients.length > 0) {
                    let tableHTML = '';
                    data.assigned_patients.forEach(patient => {
                        tableHTML += `
                            <tr>
                                <td>#${patient.id}</td>
                                <td>${patient.firstname} ${patient.lastname}</td>
                                <td>${patient.birthdate || 'N/A'}</td>
                                <td>${patient.gender ? patient.gender.charAt(0).toUpperCase() + patient.gender.slice(1) : 'N/A'}</td>
                                <td>${patient.contact || 'N/A'}</td>
                            </tr>
                        `;
                    });
                    patientsTableBody.innerHTML = tableHTML;
                } else {
                    patientsTableBody.innerHTML = '<tr><td colspan="5" style="text-align: center; padding: 20px; color: #94a3b8;">No assigned patients</td></tr>';
                }
            }
        } catch (error) {
            console.error('Error fetching Doctor Dashboard stats:', error);
        }
    }
    
    // Initial fetch
    refreshDoctorDashboard();
    
    // Refresh every 10 seconds
    setInterval(refreshDoctorDashboard, 10000);
    
    // Refresh when page becomes visible again
    document.addEventListener('visibilitychange', () => {
        if (document.visibilityState === 'visible') {
            refreshDoctorDashboard();
        }
    });
});
</script>

<?= $this->endSection() ?>
