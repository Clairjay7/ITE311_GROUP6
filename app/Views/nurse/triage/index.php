3<?= $this->extend('template/header') ?>

<?= $this->section('title') ?>Nurse Triage Dashboard<?= $this->endSection() ?>

<?= $this->section('content') ?>
<style>
    .nurse-page-container {
        padding: 0;
    }
    
    .page-header {
        background: linear-gradient(135deg, #dc2626 0%, #ef4444 100%);
        border-radius: 16px;
        padding: 24px 32px;
        margin-bottom: 24px;
        box-shadow: 0 4px 20px rgba(220, 38, 38, 0.2);
        color: white;
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 16px;
    }
    
    .page-header h1 {
        margin: 0;
        font-size: 28px;
        font-weight: 700;
        display: flex;
        align-items: center;
        gap: 12px;
    }
    
    .btn-refresh {
        background: rgba(255, 255, 255, 0.2);
        color: white;
        border: 2px solid rgba(255, 255, 255, 0.3);
        padding: 10px 20px;
        border-radius: 10px;
        font-weight: 600;
        font-size: 14px;
        cursor: pointer;
        transition: all 0.3s ease;
        display: inline-flex;
        align-items: center;
        gap: 8px;
    }
    
    .btn-refresh:hover {
        background: rgba(255, 255, 255, 0.3);
        border-color: rgba(255, 255, 255, 0.5);
        transform: translateY(-2px);
    }
    
    .modern-card {
        background: white;
        border-radius: 16px;
        box-shadow: 0 2px 12px rgba(0, 0, 0, 0.08);
        border: 1px solid #e5e7eb;
        overflow: hidden;
        margin-bottom: 24px;
    }
    
    .card-header-modern {
        padding: 20px 24px;
        border-bottom: 2px solid #e5e7eb;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    
    .card-header-danger {
        background: linear-gradient(135deg, #fee2e2 0%, #fecaca 100%);
        border-bottom-color: #dc2626;
    }
    
    .card-header-warning {
        background: linear-gradient(135deg, #fef3c7 0%, #fde68a 100%);
        border-bottom-color: #f59e0b;
    }
    
    .card-header-modern h5 {
        margin: 0;
        font-size: 18px;
        font-weight: 700;
        display: flex;
        align-items: center;
        gap: 10px;
    }
    
    .card-header-danger h5 {
        color: #991b1b;
    }
    
    .card-header-warning h5 {
        color: #92400e;
    }
    
    .card-body-modern {
        padding: 24px;
    }
    
    .empty-state {
        text-align: center;
        padding: 60px 20px;
        color: #64748b;
    }
    
    .empty-state i {
        font-size: 64px;
        margin-bottom: 16px;
        opacity: 0.5;
    }
    
    .empty-state p {
        font-size: 16px;
        margin: 0;
    }
    
    .table-modern {
        width: 100%;
        border-collapse: collapse;
    }
    
    .table-modern thead {
        background: #f8fafc;
    }
    
    .table-modern th {
        padding: 16px;
        text-align: left;
        font-weight: 600;
        color: #475569;
        font-size: 14px;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        border-bottom: 2px solid #e5e7eb;
    }
    
    .table-modern td {
        padding: 16px;
        border-bottom: 1px solid #f1f5f9;
        color: #1e293b;
        font-size: 14px;
    }
    
    .table-modern tbody tr {
        transition: all 0.2s ease;
    }
    
    .table-modern tbody tr:hover {
        background: #f8fafc;
    }
    
    .badge-modern {
        padding: 6px 12px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: 600;
        display: inline-flex;
        align-items: center;
        gap: 6px;
    }
    
    .badge-danger {
        background: #fee2e2;
        color: #991b1b;
    }
    
    .badge-warning {
        background: #fef3c7;
        color: #92400e;
    }
    
    .badge-info {
        background: #dbeafe;
        color: #1e40af;
    }
    
    .badge-success {
        background: #d1fae5;
        color: #065f46;
    }
    
    .btn-modern {
        padding: 8px 16px;
        border-radius: 8px;
        font-weight: 600;
        font-size: 13px;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        transition: all 0.3s ease;
        border: none;
        text-decoration: none;
        cursor: pointer;
    }
    
    .btn-modern-danger {
        background: linear-gradient(135deg, #dc2626 0%, #ef4444 100%);
        color: white;
    }
    
    .btn-modern-danger:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(220, 38, 38, 0.3);
    }
    
    .btn-modern-primary {
        background: linear-gradient(135deg, #0288d1 0%, #03a9f4 100%);
        color: white;
    }
    
    .btn-modern-primary:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(2, 136, 209, 0.3);
    }
    
    .status-badge {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        color: #065f46;
        font-weight: 600;
        font-size: 13px;
    }
    
    /* Modal Styles */
    .modal-content {
        border-radius: 16px;
        border: none;
        box-shadow: 0 10px 40px rgba(0, 0, 0, 0.15);
    }
    
    .modal-header {
        background: linear-gradient(135deg, #0288d1 0%, #03a9f4 100%);
        color: white;
        border-radius: 16px 16px 0 0;
        padding: 20px 24px;
        border-bottom: none;
    }
    
    .modal-header .btn-close {
        filter: brightness(0) invert(1);
    }
    
    .modal-body {
        padding: 24px;
    }
    
    .modal-footer {
        border-top: 2px solid #e5e7eb;
        padding: 16px 24px;
    }
    
    .form-label {
        font-weight: 600;
        color: #1e293b;
        margin-bottom: 8px;
        display: block;
        font-size: 14px;
    }
    
    .form-select {
        width: 100%;
        padding: 12px 16px;
        border: 2px solid #e5e7eb;
        border-radius: 10px;
        font-size: 15px;
        transition: all 0.3s ease;
    }
    
    .form-select:focus {
        outline: none;
        border-color: #0288d1;
        box-shadow: 0 0 0 3px rgba(2, 136, 209, 0.1);
    }
</style>

<div class="nurse-page-container">
    <div class="page-header">
        <h1>
            <i class="fas fa-stethoscope"></i>
            Nurse Triage Dashboard
        </h1>
        <button onclick="location.reload()" class="btn-refresh">
            <i class="fas fa-sync-alt"></i> Refresh
        </button>
    </div>

    <!-- Emergency Patients Awaiting Triage -->
    <div class="modern-card">
        <div class="card-header-modern card-header-danger">
            <h5>
                <i class="fas fa-exclamation-triangle"></i>
                Emergency Patients Awaiting Triage
            </h5>
            <?php if (!empty($emergencyPatients)): ?>
                <span class="badge-modern badge-danger">
                    <?= count($emergencyPatients) ?> Patient(s)
                </span>
            <?php endif; ?>
        </div>
        <div class="card-body-modern">
            <?php if (empty($emergencyPatients)): ?>
                <div class="empty-state">
                    <i class="fas fa-user-injured"></i>
                    <p>No emergency patients awaiting triage.</p>
                </div>
            <?php else: ?>
                <div style="overflow-x: auto;">
                    <table class="table-modern">
                        <thead>
                            <tr>
                                <th>Patient Name</th>
                                <th>Age</th>
                                <th>Gender</th>
                                <th>Reason</th>
                                <th>Registered</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($emergencyPatients as $patient): ?>
                                <tr>
                                    <td><strong><?= esc($patient['name']) ?></strong></td>
                                    <td><?= esc($patient['age'] ?? 'N/A') ?></td>
                                    <td><?= esc($patient['gender']) ?></td>
                                    <td><?= esc($patient['purpose']) ?></td>
                                    <td><?= date('M d, Y', strtotime($patient['registration_date'])) ?></td>
                                    <td>
                                        <a href="<?= site_url('nurse/triage/triage/' . $patient['id'] . '/' . $patient['source']) ?>" 
                                           class="btn-modern btn-modern-danger">
                                            <i class="fas fa-stethoscope"></i> Perform Triage
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Triaged Patients (Ready for Doctor) -->
    <?php if (!empty($triagedPatients)): ?>
    <div class="modern-card">
        <div class="card-header-modern card-header-warning">
            <h5>
                <i class="fas fa-user-md"></i>
                Triaged Patients (Ready for Doctor Assignment)
            </h5>
            <div style="display: flex; gap: 12px; align-items: center;">
                <span class="badge-modern badge-warning">
                    <?= count($triagedPatients) ?> Patient(s)
                </span>
                <a href="<?= site_url('nurse/er-beds') ?>" class="btn-modern btn-modern-danger" style="padding: 8px 16px; font-size: 13px;">
                    <i class="fas fa-bed"></i> ER Bed Management
                </a>
            </div>
        </div>
        <div class="card-body-modern">
            <div style="overflow-x: auto;">
                <table class="table-modern">
                    <thead>
                        <tr>
                            <th>Patient Name</th>
                            <th>Triage Level</th>
                            <th>Destination</th>
                            <th>Status</th>
                            <th>Chief Complaint</th>
                            <th>Triage Date</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($triagedPatients as $triage): ?>
                            <?php
                            $disposition = $triage['disposition'] ?? 'Pending';
                            $triageLevel = $triage['triage_level'] ?? 'N/A';
                            $forAdmission = $triage['for_admission'] ?? 0;
                            $sentToDoctor = $triage['sent_to_doctor'] ?? 0;
                            $doctorId = $triage['doctor_id'] ?? null;
                            $opdQueueNumber = $triage['opd_queue_number'] ?? null;
                            
                            // Determine destination/status
                            $destination = 'Pending';
                            $destinationIcon = 'fas fa-clock';
                            $destinationBadge = 'badge-info';
                            $statusText = 'Awaiting Assignment';
                            
                            if ($disposition === 'ER' || $triageLevel === 'Critical') {
                                $destination = 'Emergency Room (ER)';
                                $destinationIcon = 'fas fa-hospital';
                                $destinationBadge = 'badge-danger';
                                if ($sentToDoctor && $doctorId) {
                                    $statusText = 'Assigned to ER Doctor';
                                } else {
                                    $statusText = 'Pending ER Assignment';
                                }
                            } elseif ($disposition === 'OPD') {
                                $destination = 'Out-Patient Department (OPD)';
                                $destinationIcon = 'fas fa-clinic-medical';
                                $destinationBadge = 'badge-warning';
                                if ($opdQueueNumber) {
                                    $statusText = "Queue #{$opdQueueNumber} - Waiting";
                                } elseif ($sentToDoctor && $doctorId) {
                                    $statusText = 'Assigned to OPD Doctor';
                                } else {
                                    $statusText = 'In OPD Queue';
                                }
                            } elseif ($disposition === 'Admission' || $forAdmission) {
                                $destination = 'For Admission';
                                $destinationIcon = 'fas fa-bed';
                                $destinationBadge = 'badge-success';
                                $statusText = 'Pending Doctor Review';
                            } else {
                                $destination = 'Pending Routing';
                                $destinationIcon = 'fas fa-question-circle';
                                $destinationBadge = 'badge-info';
                                $statusText = 'Awaiting Disposition';
                            }
                            ?>
                            <tr>
                                <td><strong><?= esc($triage['patient_name'] ?? 'N/A') ?></strong></td>
                                <td>
                                    <?php
                                    $badgeClass = 'badge-info';
                                    if ($triageLevel === 'Critical') $badgeClass = 'badge-danger';
                                    elseif ($triageLevel === 'Moderate') $badgeClass = 'badge-warning';
                                    ?>
                                    <span class="badge-modern <?= $badgeClass ?>">
                                        <?= esc($triageLevel) ?>
                                    </span>
                                </td>
                                <td>
                                    <span class="badge-modern <?= $destinationBadge ?>" style="display: inline-flex; align-items: center; gap: 6px;">
                                        <i class="<?= $destinationIcon ?>"></i>
                                        <?= esc($destination) ?>
                                    </span>
                                </td>
                                <td>
                                    <span style="font-size: 12px; color: #64748b; font-weight: 500;">
                                        <?= esc($statusText) ?>
                                    </span>
                                    <?php if ($opdQueueNumber): ?>
                                        <br><small style="color: #0288d1; font-weight: 600;">OPD Queue #<?= $opdQueueNumber ?></small>
                                    <?php endif; ?>
                                    <?php if ($doctorId && $sentToDoctor): ?>
                                        <br><small style="color: #065f46; font-weight: 600;">
                                            <i class="fas fa-user-md"></i> Doctor Assigned
                                        </small>
                                    <?php endif; ?>
                                </td>
                                <td><?= esc($triage['chief_complaint'] ?? 'N/A') ?></td>
                                <td><?= date('M d, Y H:i', strtotime($triage['created_at'])) ?></td>
                                <td>
                                    <?php if ($triageLevel !== 'Critical' && $disposition !== 'ER' && !$sentToDoctor): ?>
                                        <button onclick="openSendToDoctorModal(<?= $triage['id'] ?>)" 
                                                class="btn-modern btn-modern-primary">
                                            <i class="fas fa-user-md"></i> Send to Doctor
                                        </button>
                                    <?php elseif ($sentToDoctor && $doctorId): ?>
                                        <span class="status-badge">
                                            <i class="fas fa-check-circle"></i> Sent to Doctor
                                        </span>
                                    <?php elseif ($disposition === 'ER' || $triageLevel === 'Critical'): ?>
                                        <div style="display: flex; gap: 8px; flex-wrap: wrap;">
                                            <span class="status-badge">
                                                <i class="fas fa-check-circle"></i> Auto-assigned to ER
                                            </span>
                                            <a href="<?= site_url('nurse/er-beds') ?>" 
                                               class="btn-modern btn-modern-success" 
                                               style="padding: 6px 12px; font-size: 12px;">
                                                <i class="fas fa-bed"></i> Assign Bed
                                            </a>
                                        </div>
                                    <?php elseif ($disposition === 'Admission' || $forAdmission): ?>
                                        <span class="status-badge" style="color: #065f46;">
                                            <i class="fas fa-bed"></i> Pending Admission
                                        </span>
                                    <?php else: ?>
                                        <span class="status-badge">
                                            <i class="fas fa-clock"></i> Pending
                                        </span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <?php endif; ?>
</div>

<!-- Send to Doctor Modal -->
<div class="modal fade" id="sendToDoctorModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-user-md"></i> Send Patient to Doctor
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="sendToDoctorForm">
                    <input type="hidden" id="triage_id" name="triage_id">
                    <div class="mb-3">
                        <label class="form-label">Select Doctor *</label>
                        <select name="doctor_id" id="doctor_select" class="form-select" required onchange="showDoctorScheduleInfo()">
                            <option value="">-- Select Doctor --</option>
                            <!-- Will be populated via AJAX -->
                        </select>
                        <div id="doctor_schedule_info" style="margin-top: 12px; padding: 12px; background: #f8fafc; border-radius: 8px; display: none;">
                            <div style="font-size: 12px; color: #64748b; margin-bottom: 4px;">Schedule Status:</div>
                            <div id="schedule_status" style="font-weight: 600;"></div>
                            <div id="schedule_details" style="font-size: 12px; color: #475569; margin-top: 4px;"></div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn-modern btn-modern-secondary" data-bs-dismiss="modal">
                    <i class="fas fa-times"></i> Cancel
                </button>
                <button type="button" class="btn-modern btn-modern-primary" onclick="submitSendToDoctor()">
                    <i class="fas fa-paper-plane"></i> Send to Doctor
                </button>
            </div>
        </div>
    </div>
</div>

<script>
function openSendToDoctorModal(triageId) {
    document.getElementById('triage_id').value = triageId;
    const select = document.getElementById('doctor_select');
    select.innerHTML = '<option value="">Loading doctors...</option>';
    select.disabled = true;
    
    // Load available doctors from nurse triage endpoint
    fetch('<?= site_url('nurse/triage/get-doctors') ?>?date=<?= date('Y-m-d') ?>', {
        headers: {
            'Accept': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
        .then(r => {
            if (!r.ok) {
                throw new Error('Failed to load doctors');
            }
            return r.json();
        })
        .then(data => {
            select.innerHTML = '<option value="">-- Select Doctor --</option>';
            select.disabled = false;
            
            if (data.success && data.doctors && data.doctors.length > 0) {
                data.doctors.forEach(doctor => {
                    const option = document.createElement('option');
                    option.value = doctor.id;
                    option.textContent = `${doctor.name} - ${doctor.specialization}`;
                    // Store schedule info in data attributes
                    option.dataset.scheduleStatus = doctor.schedule_status || 'unknown';
                    option.dataset.scheduleTime = doctor.schedule_time || '';
                    option.dataset.currentAppointments = doctor.current_appointments || 0;
                    option.dataset.maxCapacity = doctor.max_capacity || 0;
                    select.appendChild(option);
                });
            } else {
                select.innerHTML = '<option value="">No doctors available</option>';
                console.warn('No doctors found:', data);
            }
        })
        .catch(error => {
            console.error('Error loading doctors:', error);
            select.innerHTML = '<option value="">Error loading doctors</option>';
            select.disabled = false;
            alert('Failed to load doctors. Please try again.');
        });
    
    new bootstrap.Modal(document.getElementById('sendToDoctorModal')).show();
}

function submitSendToDoctor() {
    const form = document.getElementById('sendToDoctorForm');
    const formData = new FormData(form);
    const submitBtn = event.target;
    const originalText = submitBtn.innerHTML;
    
    submitBtn.disabled = true;
    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Sending...';
    
    fetch('<?= site_url('nurse/triage/send-to-doctor') ?>', {
        method: 'POST',
        body: formData,
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            alert('Patient sent to doctor successfully!');
            location.reload();
        } else {
            alert('Error: ' + (data.message || 'Failed to send patient to doctor'));
            submitBtn.disabled = false;
            submitBtn.innerHTML = originalText;
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('An error occurred. Please try again.');
        submitBtn.disabled = false;
        submitBtn.innerHTML = originalText;
    });
}
</script>
<?= $this->endSection() ?>
