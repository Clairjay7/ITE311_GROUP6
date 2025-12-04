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
        <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 16px;">
            <div>
                <h2>Welcome back, Dr. <?= esc($name ?? 'Doctor') ?></h2>
                <p>Here's what's happening with your patients today</p>
            </div>
            <div style="display: flex; gap: 12px; flex-wrap: wrap;">
                <a href="<?= site_url('doctor/admission-orders') ?>" style="background: #0288d1; color: white; padding: 12px 24px; border-radius: 10px; text-decoration: none; font-weight: 600; display: inline-flex; align-items: center; gap: 8px;">
                    <i class="fas fa-hospital"></i> Admitted Patients
                </a>
                <a href="<?= site_url('doctor/discharge') ?>" style="background: #2e7d32; color: white; padding: 12px 24px; border-radius: 10px; text-decoration: none; font-weight: 600; display: inline-flex; align-items: center; gap: 8px;">
                    <i class="fas fa-sign-out-alt"></i> Discharge Patients
                </a>
            </div>
        </div>
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

    <!-- Completed Lab Results Section -->
    <div class="patients-section" style="margin-top: 24px;">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 16px;">
            <h3 style="color: #0288d1; margin: 0;">
                <i class="fas fa-vial"></i> Recent Lab Results
            </h3>
        </div>
        <div id="completedLabResultsContainer" style="background: white; border-radius: 8px; padding: 16px; min-height: 100px;">
            <p style="color: #94a3b8; text-align: center; padding: 20px;">Loading lab results...</p>
        </div>
    </div>

    <!-- Admitted Patients Section -->
    <div class="patients-section" style="margin-top: 24px;">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 16px;">
            <h3 style="color: #2e7d32; margin: 0;">
                <i class="fas fa-hospital"></i> My Admitted Patients
            </h3>
            <a href="<?= site_url('doctor/admission-orders') ?>" style="color: #2e7d32; text-decoration: none; font-weight: 600;">
                View All <i class="fas fa-arrow-right"></i>
            </a>
        </div>
        <div id="admittedPatientsContainer" style="background: white; border-radius: 8px; padding: 16px; min-height: 100px;">
            <p style="color: #94a3b8; text-align: center; padding: 20px;">Loading admitted patients...</p>
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
            
            // Update completed lab results
            updateCompletedLabResults('completedLabResultsContainer', data.completed_lab_results ?? []);
            
            // Update admitted patients
            updateAdmittedPatients('admittedPatientsContainer', data.admitted_patients ?? []);
            
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

    function updateAdmittedPatients(containerId, patients) {
        const container = document.getElementById(containerId);
        if (!container) return;
        
        if (patients && patients.length > 0) {
            let html = '';
            patients.forEach(patient => {
                const admissionDate = patient.admission_date ? new Date(patient.admission_date).toLocaleDateString('en-US', { 
                    year: 'numeric', 
                    month: 'short', 
                    day: 'numeric' 
                }) : 'N/A';
                
                const patientName = (patient.firstname || '') + ' ' + (patient.lastname || '');
                const roomInfo = (patient.room_number || 'N/A') + (patient.ward ? ' - ' + patient.ward : '');
                const admissionReason = patient.admission_reason || 'N/A';
                const diagnosis = patient.diagnosis || 'N/A';
                const admissionId = patient.id || patient.admission_id || '';
                const pendingCount = parseInt(patient.pending_orders_count || 0);
                
                html += `
                    <div style="background: #f8fafc; border: 1px solid #e5e7eb; border-radius: 8px; padding: 16px; margin-bottom: 12px;">
                        <div style="display: flex; justify-content: space-between; align-items: start; flex-wrap: wrap; gap: 12px;">
                            <div style="flex: 1;">
                                <div style="font-weight: 600; color: #1e293b; margin-bottom: 8px; font-size: 16px;">
                                    <i class="fas fa-user-injured"></i> ${patientName.trim() || 'Unknown Patient'}
                                </div>
                                <div style="font-size: 13px; color: #64748b; margin-bottom: 4px;">
                                    <i class="fas fa-bed"></i> Room: ${roomInfo}
                                </div>
                                <div style="font-size: 13px; color: #64748b; margin-bottom: 4px;">
                                    <i class="fas fa-calendar"></i> Admission Date: ${admissionDate}
                                </div>
                                <div style="font-size: 13px; color: #64748b; margin-bottom: 4px;">
                                    <i class="fas fa-info-circle"></i> Admission Reason: ${admissionReason}
                                </div>
                                ${diagnosis !== 'N/A' ? `
                                    <div style="background: #fef3c7; padding: 8px 12px; border-radius: 6px; margin-top: 8px; font-size: 13px; color: #92400e;">
                                        <strong>Diagnosis:</strong> ${diagnosis}
                                    </div>
                                ` : ''}
                            </div>
                            <div style="display: flex; gap: 8px; align-items: center; flex-wrap: wrap;">
                                ${pendingCount > 0 ? `
                                    <span style="background: #f59e0b; color: white; padding: 6px 12px; border-radius: 12px; font-size: 12px; font-weight: 600;">
                                        ${pendingCount} Pending Orders
                                    </span>
                                ` : ''}
                                ${patient.source === 'receptionist' ? 
                                    `<a href="<?= site_url('doctor/consultations/start/') ?>${patient.patient_id}/patients" 
                                       style="background: #0288d1; color: white; padding: 8px 16px; border-radius: 8px; text-decoration: none; font-size: 13px; font-weight: 600; white-space: nowrap; display: inline-flex; align-items: center; gap: 6px; margin-right: 8px;">
                                        <i class="fas fa-stethoscope"></i> Start Consultation
                                    </a>
                                    <a href="<?= site_url('doctor/patients/view/') ?>${patient.patient_id}" 
                                       style="background: #0288d1; color: white; padding: 8px 16px; border-radius: 8px; text-decoration: none; font-size: 13px; font-weight: 600; white-space: nowrap; display: inline-flex; align-items: center; gap: 6px;">
                                        <i class="fas fa-eye"></i> View Patient
                                    </a>
                                    <span style="background: #dbeafe; color: #1e40af; padding: 6px 12px; border-radius: 6px; font-size: 11px; font-weight: 600; margin-left: 8px;">
                                        <i class="fas fa-info-circle"></i> Direct Admission
                                    </span>` :
                                    `<a href="<?= site_url('doctor/consultations/start/') ?>${patient.patient_id}/admin_patients" 
                                       style="background: #0288d1; color: white; padding: 8px 16px; border-radius: 8px; text-decoration: none; font-size: 13px; font-weight: 600; white-space: nowrap; display: inline-flex; align-items: center; gap: 6px; margin-right: 8px;">
                                        <i class="fas fa-stethoscope"></i> Start Consultation
                                    </a>
                                    <a href="<?= site_url('doctor/admission-orders/view/') ?>${admissionId}" 
                                       style="background: #2e7d32; color: white; padding: 8px 16px; border-radius: 8px; text-decoration: none; font-size: 13px; font-weight: 600; white-space: nowrap; display: inline-flex; align-items: center; gap: 6px;">
                                        <i class="fas fa-eye"></i> View & Manage Orders
                                    </a>`}
                            </div>
                        </div>
                    </div>
                `;
            });
            container.innerHTML = html;
        } else {
            container.innerHTML = '<div style="text-align: center; padding: 20px; color: #94a3b8;"><i class="fas fa-hospital" style="font-size: 32px; margin-bottom: 8px; opacity: 0.5;"></i><p style="margin: 0; font-size: 14px;">No admitted patients</p></div>';
        }
    }
    
    function updateCompletedLabResults(containerId, results) {
        const container = document.getElementById(containerId);
        if (results && results.length > 0) {
            let html = '';
            results.forEach(result => {
                html += `
                    <div style="background: #f8fafc; border: 1px solid #e5e7eb; border-left: 4px solid #0288d1; border-radius: 8px; padding: 16px; margin-bottom: 12px;">
                        <div style="display: flex; justify-content: space-between; align-items: start;">
                            <div style="flex: 1;">
                                <strong style="color: #0288d1; font-size: 15px;">
                                    ${result.test_name || 'Lab Test'}
                                </strong>
                                <div style="margin-top: 8px; color: #64748b; font-size: 13px;">
                                    Patient: ${result.firstname} ${result.lastname}
                                </div>
                                ${result.result ? `
                                    <div style="margin-top: 12px; padding: 12px; background: white; border-radius: 6px; border: 1px solid #e5e7eb;">
                                        <strong style="color: #475569; font-size: 13px;">Result:</strong>
                                        <div style="margin-top: 4px; color: #1e293b; font-size: 13px; white-space: pre-wrap;">${result.result.substring(0, 100)}${result.result.length > 100 ? '...' : ''}</div>
                                    </div>
                                ` : ''}
                            </div>
                            <div style="text-align: right;">
                                <span style="padding: 6px 12px; border-radius: 6px; font-size: 12px; font-weight: 600; background: #d1fae5; color: #065f46;">
                                    <i class="fas fa-check-circle"></i> Completed
                                </span>
                            </div>
                        </div>
                        <div style="margin-top: 12px; padding-top: 12px; border-top: 1px solid #e5e7eb; font-size: 12px; color: #64748b;">
                            <i class="fas fa-calendar"></i> 
                            Completed: ${result.completed_at ? new Date(result.completed_at).toLocaleString('en-US', { year: 'numeric', month: 'short', day: 'numeric', hour: 'numeric', minute: '2-digit' }) : 'N/A'}
                        </div>
                    </div>
                `;
            });
            container.innerHTML = html;
        } else {
            container.innerHTML = '<div style="text-align: center; padding: 20px; color: #94a3b8;"><i class="fas fa-vial" style="font-size: 32px; margin-bottom: 8px; opacity: 0.5;"></i><p style="margin: 0; font-size: 14px;">No completed lab results</p></div>';
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
