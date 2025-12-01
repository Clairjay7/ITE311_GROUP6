<?= $this->extend('template/header') ?>
<?= $this->section('title') ?>Nurse Triage<?= $this->endSection() ?>
<?= $this->section('content') ?>
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2><i class="fas fa-stethoscope"></i> Nurse Triage Dashboard</h2>
        <button onclick="location.reload()" class="btn btn-outline-primary">
            <i class="fas fa-sync-alt"></i> Refresh
        </button>
    </div>

    <!-- Emergency Patients Awaiting Triage -->
    <div class="card mb-4">
        <div class="card-header bg-danger text-white">
            <h5 class="mb-0"><i class="fas fa-exclamation-triangle"></i> Emergency Patients Awaiting Triage</h5>
        </div>
        <div class="card-body">
            <?php if (empty($emergencyPatients)): ?>
                <p class="text-muted text-center py-4">No emergency patients awaiting triage.</p>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-hover">
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
                                           class="btn btn-danger btn-sm">
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
    <div class="card">
        <div class="card-header bg-warning">
            <h5 class="mb-0"><i class="fas fa-user-md"></i> Triaged Patients (Ready for Doctor Assignment)</h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Patient Name</th>
                            <th>Triage Level</th>
                            <th>Chief Complaint</th>
                            <th>Triage Date</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($triagedPatients as $triage): ?>
                            <tr>
                                <td><strong><?= esc($triage['patient_name'] ?? 'N/A') ?></strong></td>
                                <td>
                                    <span class="badge <?= $triage['triage_level'] === 'Critical' ? 'bg-danger' : ($triage['triage_level'] === 'Moderate' ? 'bg-warning' : 'bg-info') ?>">
                                        <?= esc($triage['triage_level']) ?>
                                    </span>
                                </td>
                                <td><?= esc($triage['chief_complaint'] ?? 'N/A') ?></td>
                                <td><?= date('M d, Y H:i', strtotime($triage['created_at'])) ?></td>
                                <td>
                                    <?php if ($triage['triage_level'] !== 'Critical'): ?>
                                        <button onclick="openSendToDoctorModal(<?= $triage['id'] ?>)" 
                                                class="btn btn-primary btn-sm">
                                            <i class="fas fa-user-md"></i> Send to Doctor
                                        </button>
                                    <?php else: ?>
                                        <span class="text-success"><i class="fas fa-check"></i> Auto-assigned</span>
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
                <h5 class="modal-title">Send Patient to Doctor</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="sendToDoctorForm">
                    <input type="hidden" id="triage_id" name="triage_id">
                    <div class="mb-3">
                        <label class="form-label">Select Doctor *</label>
                        <select name="doctor_id" id="doctor_select" class="form-select" required>
                            <option value="">-- Select Doctor --</option>
                            <!-- Will be populated via AJAX -->
                        </select>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" onclick="submitSendToDoctor()">Send to Doctor</button>
            </div>
        </div>
    </div>
</div>

<script>
function openSendToDoctorModal(triageId) {
    document.getElementById('triage_id').value = triageId;
    // Load available doctors
    fetch('<?= site_url('receptionist/assign-doctor/get-available-doctors') ?>?date=<?= date('Y-m-d') ?>')
        .then(r => r.json())
        .then(data => {
            const select = document.getElementById('doctor_select');
            select.innerHTML = '<option value="">-- Select Doctor --</option>';
            if (data.success && data.doctors) {
                data.doctors.forEach(doctor => {
                    const option = document.createElement('option');
                    option.value = doctor.id;
                    option.textContent = `${doctor.name} - ${doctor.specialization}`;
                    select.appendChild(option);
                });
            }
        });
    new bootstrap.Modal(document.getElementById('sendToDoctorModal')).show();
}

function submitSendToDoctor() {
    const form = document.getElementById('sendToDoctorForm');
    const formData = new FormData(form);
    
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
        }
    });
}
</script>
<?= $this->endSection() ?>

