<?= $this->extend('template/header') ?>
<?= $this->section('title') ?>Perform Triage<?= $this->endSection() ?>
<?= $this->section('content') ?>
<div class="container py-4">
    <div class="card">
        <div class="card-header bg-danger text-white">
            <h4 class="mb-0"><i class="fas fa-stethoscope"></i> Emergency Triage</h4>
        </div>
        <div class="card-body">
            <!-- Patient Information -->
            <div class="alert alert-info">
                <h5><i class="fas fa-user-injured"></i> Patient Information</h5>
                <p class="mb-0">
                    <strong>Name:</strong> <?= esc($patient['full_name'] ?? ($patient['firstname'] ?? '') . ' ' . ($patient['lastname'] ?? '')) ?><br>
                    <strong>Age:</strong> <?= esc($patient['age'] ?? 'N/A') ?><br>
                    <strong>Gender:</strong> <?= esc($patient['gender'] ?? 'N/A') ?>
                </p>
            </div>

            <form id="triageForm" method="post" action="<?= site_url('nurse/triage/save') ?>">
                <?= csrf_field() ?>
                <input type="hidden" name="patient_id" value="<?= esc($patient['patient_id'] ?? $patient['id']) ?>">
                <input type="hidden" name="patient_source" value="<?= esc($patientSource) ?>">

                <!-- Vital Signs -->
                <h5 class="mt-4 mb-3"><i class="fas fa-heartbeat"></i> Vital Signs</h5>
                <div class="row g-3 mb-4">
                    <div class="col-md-3">
                        <label class="form-label">Heart Rate (bpm) *</label>
                        <input type="number" name="heart_rate" class="form-control" required min="0" max="250">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Blood Pressure (Systolic) *</label>
                        <input type="number" name="blood_pressure_systolic" class="form-control" required min="0" max="300">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Blood Pressure (Diastolic) *</label>
                        <input type="number" name="blood_pressure_diastolic" class="form-control" required min="0" max="200">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Temperature (°C) *</label>
                        <input type="number" name="temperature" class="form-control" required step="0.1" min="30" max="45">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Oxygen Saturation (%) *</label>
                        <input type="number" name="oxygen_saturation" class="form-control" required min="0" max="100">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Respiratory Rate (bpm) *</label>
                        <input type="number" name="respiratory_rate" class="form-control" required min="0" max="60">
                    </div>
                </div>

                <!-- Triage Level -->
                <h5 class="mt-4 mb-3"><i class="fas fa-exclamation-triangle"></i> Triage Assessment</h5>
                <div class="row g-3 mb-4">
                    <div class="col-md-12">
                        <label class="form-label">Triage Level *</label>
                        <select name="triage_level" id="triage_level" class="form-select" required>
                            <option value="">-- Select Triage Level --</option>
                            <option value="Critical">Critical - Requires immediate medical attention</option>
                            <option value="Moderate">Moderate - Urgent but stable</option>
                            <option value="Minor">Minor - Non-urgent</option>
                        </select>
                        <small class="text-muted">
                            <strong>Critical:</strong> Patient will be automatically assigned to ER doctor<br>
                            <strong>Moderate/Minor:</strong> Nurse will provide initial care, then send to doctor
                        </small>
                    </div>
                    <div class="col-md-12">
                        <label class="form-label">Chief Complaint *</label>
                        <textarea name="chief_complaint" class="form-control" rows="3" required placeholder="Primary reason for visit"></textarea>
                    </div>
                    <div class="col-md-12">
                        <label class="form-label">Triage Notes</label>
                        <textarea name="notes" class="form-control" rows="4" placeholder="Additional observations, symptoms, or relevant information"></textarea>
                    </div>
                </div>

                <div class="alert alert-warning" id="criticalAlert" style="display: none;">
                    <i class="fas fa-exclamation-triangle"></i> 
                    <strong>Critical Triage Level Selected:</strong> This patient will be automatically assigned to an ER doctor or on-duty doctor immediately after triage is completed.
                </div>

                <div class="d-flex gap-2 mt-4">
                    <button type="submit" class="btn btn-danger btn-lg">
                        <i class="fas fa-save"></i> Complete Triage
                    </button>
                    <a href="<?= site_url('nurse/triage') ?>" class="btn btn-outline-secondary btn-lg">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.getElementById('triage_level').addEventListener('change', function() {
    const criticalAlert = document.getElementById('criticalAlert');
    if (this.value === 'Critical') {
        criticalAlert.style.display = 'block';
    } else {
        criticalAlert.style.display = 'none';
    }
});

document.getElementById('triageForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    
    fetch('<?= site_url('nurse/triage/save') ?>', {
        method: 'POST',
        body: formData,
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            if (data.critical) {
                alert('Triage completed! Patient has been automatically assigned to an ER doctor.');
            } else {
                alert('Triage completed successfully! Patient is ready for doctor assignment.');
            }
            window.location.href = '<?= site_url('nurse/triage') ?>';
        } else {
            alert('Error: ' + (data.message || 'Failed to save triage data'));
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('An error occurred. Please try again.');
    });
});
</script>
<?= $this->endSection() ?>

