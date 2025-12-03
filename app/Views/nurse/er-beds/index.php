<?= $this->extend('template/header') ?>

<?= $this->section('title') ?>ER Bed Management<?= $this->endSection() ?>

<?= $this->section('content') ?>
<style>
    .nurse-page-container {
        max-width: 1400px;
        margin: 0 auto;
        padding: 24px;
    }

    .page-header {
        background: linear-gradient(135deg, #dc2626 0%, #ef4444 100%);
        border-radius: 16px;
        padding: 32px;
        margin-bottom: 24px;
        color: white;
        box-shadow: 0 4px 20px rgba(220, 38, 38, 0.3);
    }

    .page-header h1 {
        margin: 0;
        font-size: 32px;
        font-weight: 700;
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .modern-card {
        background: white;
        border-radius: 12px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        margin-bottom: 24px;
        overflow: hidden;
    }

    .card-header-modern {
        background: #f8fafc;
        padding: 20px 24px;
        border-bottom: 2px solid #e5e7eb;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .card-header-modern h5 {
        margin: 0;
        font-size: 18px;
        font-weight: 600;
        color: #1e293b;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .card-body-modern {
        padding: 24px;
    }

    .bed-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
        gap: 20px;
        margin-top: 20px;
    }

    .bed-card {
        border: 2px solid #e5e7eb;
        border-radius: 12px;
        padding: 20px;
        transition: all 0.3s ease;
        background: white;
    }

    .bed-card:hover {
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        transform: translateY(-2px);
    }

    .bed-card.available {
        border-color: #10b981;
        background: #f0fdf4;
    }

    .bed-card.occupied {
        border-color: #ef4444;
        background: #fef2f2;
    }

    .bed-card.maintenance {
        border-color: #f59e0b;
        background: #fffbeb;
    }

    .bed-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 16px;
    }

    .bed-number {
        font-size: 20px;
        font-weight: 700;
        color: #1e293b;
    }

    .bed-status {
        padding: 6px 12px;
        border-radius: 8px;
        font-size: 12px;
        font-weight: 600;
    }

    .bed-status.available {
        background: #10b981;
        color: white;
    }

    .bed-status.occupied {
        background: #ef4444;
        color: white;
    }

    .bed-status.maintenance {
        background: #f59e0b;
        color: white;
    }

    .bed-info {
        margin-top: 12px;
    }

    .bed-info-item {
        display: flex;
        justify-content: space-between;
        padding: 8px 0;
        border-bottom: 1px solid #f1f5f9;
    }

    .bed-info-item:last-child {
        border-bottom: none;
    }

    .bed-info-label {
        font-weight: 500;
        color: #64748b;
        font-size: 14px;
    }

    .bed-info-value {
        font-weight: 600;
        color: #1e293b;
        font-size: 14px;
    }

    .patient-name {
        font-weight: 600;
        color: #1e293b;
        margin-bottom: 8px;
    }

    .triage-badge {
        display: inline-block;
        padding: 4px 10px;
        border-radius: 6px;
        font-size: 11px;
        font-weight: 600;
        margin-top: 8px;
    }

    .triage-badge.critical {
        background: #fee2e2;
        color: #991b1b;
    }

    .triage-badge.moderate {
        background: #fef3c7;
        color: #92400e;
    }

    .triage-badge.minor {
        background: #dbeafe;
        color: #1e40af;
    }

    .btn-modern {
        padding: 10px 20px;
        border-radius: 8px;
        font-weight: 600;
        font-size: 14px;
        border: none;
        cursor: pointer;
        transition: all 0.3s ease;
        display: inline-flex;
        align-items: center;
        gap: 8px;
    }

    .btn-modern-primary {
        background: #3b82f6;
        color: white;
    }

    .btn-modern-primary:hover {
        background: #2563eb;
    }

    .btn-modern-danger {
        background: #ef4444;
        color: white;
    }

    .btn-modern-danger:hover {
        background: #dc2626;
    }

    .btn-modern-success {
        background: #10b981;
        color: white;
    }

    .btn-modern-success:hover {
        background: #059669;
    }

    .patients-list {
        margin-top: 20px;
    }

    .patient-item {
        background: #f8fafc;
        border: 2px solid #e5e7eb;
        border-radius: 8px;
        padding: 16px;
        margin-bottom: 12px;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .patient-info h6 {
        margin: 0 0 8px 0;
        font-size: 16px;
        font-weight: 600;
        color: #1e293b;
    }

    .patient-info p {
        margin: 4px 0;
        font-size: 14px;
        color: #64748b;
    }

    .modal-modern .modal-content {
        border-radius: 12px;
        border: none;
    }

    .modal-modern .modal-header {
        background: linear-gradient(135deg, #dc2626 0%, #ef4444 100%);
        color: white;
        border-radius: 12px 12px 0 0;
        padding: 20px 24px;
    }

    .modal-modern .modal-body {
        padding: 24px;
    }

    .form-group {
        margin-bottom: 20px;
    }

    .form-label {
        display: block;
        font-weight: 600;
        color: #1e293b;
        margin-bottom: 8px;
        font-size: 14px;
    }

    .form-select {
        width: 100%;
        padding: 10px 12px;
        border: 2px solid #e5e7eb;
        border-radius: 8px;
        font-size: 14px;
        transition: border-color 0.3s ease;
    }

    .form-select:focus {
        outline: none;
        border-color: #3b82f6;
    }
</style>

<div class="nurse-page-container">
    <div class="page-header">
        <h1>
            <i class="fas fa-bed"></i>
            ER Bed Management
        </h1>
    </div>

    <!-- ER Beds Grid -->
    <div class="modern-card">
        <div class="card-header-modern">
            <h5>
                <i class="fas fa-hospital"></i>
                ER Beds Status
            </h5>
            <span class="badge-modern badge-info">
                <?= count($erBeds) ?> Bed(s)
            </span>
        </div>
        <div class="card-body-modern">
            <?php if (empty($erBeds)): ?>
                <div style="text-align: center; padding: 40px; color: #64748b;">
                    <i class="fas fa-bed" style="font-size: 48px; margin-bottom: 16px; opacity: 0.3;"></i>
                    <p>No ER beds configured. Please contact administrator to set up ER beds.</p>
                </div>
            <?php else: ?>
                <div class="bed-grid">
                    <?php foreach ($erBeds as $bed): ?>
                        <div class="bed-card <?= strtolower($bed['status']) ?>">
                            <div class="bed-header">
                                <div class="bed-number">
                                    <i class="fas fa-bed"></i> <?= esc($bed['bed_number']) ?>
                                </div>
                                <span class="bed-status <?= strtolower($bed['status']) ?>">
                                    <?= esc(ucfirst($bed['status'])) ?>
                                </span>
                            </div>
                            <div class="bed-info">
                                <div class="bed-info-item">
                                    <span class="bed-info-label">Room:</span>
                                    <span class="bed-info-value"><?= esc($bed['room_number'] ?? 'N/A') ?></span>
                                </div>
                                <div class="bed-info-item">
                                    <span class="bed-info-label">Ward:</span>
                                    <span class="bed-info-value"><?= esc($bed['ward'] ?? 'ER') ?></span>
                                </div>
                                <?php if ($bed['status'] === 'occupied' && $bed['current_patient_id']): ?>
                                    <div class="bed-info-item" style="margin-top: 12px; padding-top: 12px; border-top: 2px solid #e5e7eb;">
                                        <div style="width: 100%;">
                                            <div class="patient-name">
                                                <i class="fas fa-user"></i> 
                                                <?= esc($bed['firstname'] . ' ' . $bed['lastname'] ?? $bed['patient_full_name'] ?? 'Patient ' . $bed['current_patient_id']) ?>
                                            </div>
                                            <?php if ($bed['triage_level']): ?>
                                                <span class="triage-badge <?= strtolower($bed['triage_level']) ?>">
                                                    <?= esc($bed['triage_level']) ?>
                                                </span>
                                            <?php endif; ?>
                                            <?php if ($bed['chief_complaint']): ?>
                                                <p style="margin-top: 8px; font-size: 12px; color: #64748b;">
                                                    <?= esc(substr($bed['chief_complaint'], 0, 50)) ?>...
                                                </p>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                    <div style="margin-top: 16px;">
                                        <button onclick="releaseBed(<?= $bed['id'] ?>, '<?= esc($bed['bed_number']) ?>')" 
                                                class="btn-modern btn-modern-danger" style="width: 100%;">
                                            <i class="fas fa-door-open"></i> Release Bed
                                        </button>
                                    </div>
                                <?php else: ?>
                                    <div style="margin-top: 16px; text-align: center; color: #64748b; font-size: 14px;">
                                        <i class="fas fa-check-circle"></i> Available
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Patients Approved by Doctor (Receptionist assigns ER bed) -->
    <?php if (!empty($doctorApprovedPatients ?? [])): ?>
    <div class="modern-card">
        <div class="card-header-modern" style="background: #dbeafe; border-bottom: 2px solid #3b82f6;">
            <h5 style="color: #1e40af;">
                <i class="fas fa-user-md"></i>
                Doctor-Approved Patients (Ready for ER Bed Assignment)
            </h5>
            <span class="badge-modern badge-info">
                <?= count($doctorApprovedPatients) ?> Patient(s)
            </span>
        </div>
        <div class="card-body-modern">
            <div class="patients-list">
                <?php foreach ($doctorApprovedPatients as $patient): ?>
                    <div class="patient-item" style="border-left: 4px solid #3b82f6;">
                        <div class="patient-info">
                            <h6>
                                <i class="fas fa-user"></i> <?= esc($patient['patient_name']) ?>
                                <span style="background: #3b82f6; color: white; padding: 2px 8px; border-radius: 4px; font-size: 11px; margin-left: 8px;">
                                    <i class="fas fa-check-circle"></i> Doctor Approved
                                </span>
                            </h6>
                            <p>
                                <strong>Triage Level:</strong> 
                                <span class="triage-badge <?= strtolower($patient['triage_level']) ?>">
                                    <?= esc($patient['triage_level']) ?>
                                </span>
                            </p>
                            <p>
                                <strong>Chief Complaint:</strong> <?= esc($patient['chief_complaint'] ?? 'N/A') ?>
                            </p>
                            <p style="font-size: 12px; color: #64748b;">
                                <i class="fas fa-clock"></i> 
                                Approved: <?= date('M d, Y H:i', strtotime($patient['created_at'])) ?>
                            </p>
                        </div>
                        <div>
                            <?php if ($isReceptionist ?? false): ?>
                                <button onclick="assignBed(<?= $patient['patient_id'] ?>, <?= $patient['triage_id'] ?? 0 ?>, '<?= esc($patient['patient_name']) ?>', '<?= esc($patient['patient_source']) ?>', <?= $patient['admission_request_id'] ?? 0 ?>)" 
                                        class="btn-modern btn-modern-primary">
                                    <i class="fas fa-bed"></i> Assign ER Bed
                                </button>
                            <?php else: ?>
                                <span class="badge-modern badge-secondary">
                                    <i class="fas fa-lock"></i> Receptionist Only
                                </span>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <!-- Patients Waiting for Bed Assignment (Direct from ER Triage) -->
    <?php if (!empty($erPatients)): ?>
    <div class="modern-card">
        <div class="card-header-modern">
            <h5>
                <i class="fas fa-user-injured"></i>
                ER Patients Waiting for Bed Assignment
            </h5>
            <span class="badge-modern badge-warning">
                <?= count($erPatients) ?> Patient(s)
            </span>
        </div>
        <div class="card-body-modern">
            <div class="patients-list">
                <?php foreach ($erPatients as $patient): ?>
                    <div class="patient-item">
                        <div class="patient-info">
                            <h6>
                                <i class="fas fa-user"></i> <?= esc($patient['patient_name']) ?>
                            </h6>
                            <p>
                                <strong>Triage Level:</strong> 
                                <span class="triage-badge <?= strtolower($patient['triage_level']) ?>">
                                    <?= esc($patient['triage_level']) ?>
                                </span>
                            </p>
                            <p>
                                <strong>Chief Complaint:</strong> <?= esc($patient['chief_complaint'] ?? 'N/A') ?>
                            </p>
                            <p style="font-size: 12px; color: #64748b;">
                                <i class="fas fa-clock"></i> 
                                <?= date('M d, Y H:i', strtotime($patient['created_at'])) ?>
                            </p>
                        </div>
                        <div>
                            <button onclick="assignBed(<?= $patient['patient_id'] ?>, <?= $patient['triage_id'] ?>, '<?= esc($patient['patient_name']) ?>')" 
                                    class="btn-modern btn-modern-primary">
                                <i class="fas fa-bed"></i> Assign Bed
                            </button>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
    <?php endif; ?>
</div>

<!-- Assign Bed Modal -->
<div class="modal fade modal-modern" id="assignBedModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-bed"></i> Assign Patient to ER Bed
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="assignBedForm">
                    <input type="hidden" id="assign_patient_id" name="patient_id">
                    <input type="hidden" id="assign_triage_id" name="triage_id">
                    <input type="hidden" id="assign_patient_source" name="patient_source" value="patients">
                    <input type="hidden" id="assign_admission_request_id" name="admission_request_id" value="0">
                    
                    <div class="form-group">
                        <label class="form-label">Patient Name</label>
                        <input type="text" id="assign_patient_name" class="form-control" readonly style="background: #f8fafc;">
                    </div>

                    <div class="form-group">
                        <label class="form-label">Select ER Bed *</label>
                        <select name="bed_id" id="bed_select" class="form-select" required>
                            <option value="">Loading available beds...</option>
                        </select>
                        <div class="form-text">Only available ER beds are shown</div>
                    </div>

                    <div style="margin-top: 24px; display: flex; gap: 12px; justify-content: flex-end;">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn-modern btn-modern-primary">
                            <i class="fas fa-check"></i> Assign Bed
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
function assignBed(patientId, triageId, patientName, patientSource = 'patients', admissionRequestId = 0) {
    document.getElementById('assign_patient_id').value = patientId;
    document.getElementById('assign_triage_id').value = triageId || 0;
    document.getElementById('assign_patient_name').value = patientName;
    if (document.getElementById('assign_patient_source')) {
        document.getElementById('assign_patient_source').value = patientSource;
    }
    if (document.getElementById('assign_admission_request_id')) {
        document.getElementById('assign_admission_request_id').value = admissionRequestId;
    }
    
    const bedSelect = document.getElementById('bed_select');
    bedSelect.innerHTML = '<option value="">Loading available beds...</option>';
    
    fetch('<?= site_url('nurse/er-beds/get-available-beds') ?>')
        .then(r => r.json())
        .then(data => {
            bedSelect.innerHTML = '<option value="">-- Select ER Bed --</option>';
            if (data.success && data.beds && data.beds.length > 0) {
                data.beds.forEach(bed => {
                    const option = document.createElement('option');
                    option.value = bed.id;
                    option.textContent = `${bed.bed_number} - Room ${bed.room_number || 'N/A'} (${bed.ward || 'ER'})`;
                    bedSelect.appendChild(option);
                });
            } else {
                bedSelect.innerHTML = '<option value="">No Available ER Beds</option>';
            }
        })
        .catch(error => {
            console.error('Error loading beds:', error);
            bedSelect.innerHTML = '<option value="">Error loading beds</option>';
        });
    
    new bootstrap.Modal(document.getElementById('assignBedModal')).show();
}

function releaseBed(bedId, bedNumber) {
    if (!confirm(`Are you sure you want to release ER Bed ${bedNumber}?`)) {
        return;
    }
    
    const formData = new FormData();
    formData.append('bed_id', bedId);
    
    fetch('<?= site_url('nurse/er-beds/release') ?>', {
        method: 'POST',
        body: formData,
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            alert(data.message);
            location.reload();
        } else {
            alert('Error: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('An error occurred while releasing the bed');
    });
}

// Handle assign bed form submission
document.getElementById('assignBedForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    
    fetch('<?= site_url('nurse/er-beds/assign') ?>', {
        method: 'POST',
        body: formData,
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            alert(data.message);
            bootstrap.Modal.getInstance(document.getElementById('assignBedModal')).hide();
            location.reload();
        } else {
            alert('Error: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('An error occurred while assigning the bed');
    });
});
</script>

<?= $this->endSection() ?>

