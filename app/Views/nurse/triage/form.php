<?= $this->extend('template/header') ?>

<?= $this->section('title') ?>Perform Triage<?= $this->endSection() ?>

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
    }
    
    .page-header h1 {
        margin: 0;
        font-size: 28px;
        font-weight: 700;
        display: flex;
        align-items: center;
        gap: 12px;
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
        background: linear-gradient(135deg, #fee2e2 0%, #fecaca 100%);
        padding: 20px 24px;
        border-bottom: 2px solid #dc2626;
    }
    
    .card-header-modern h5 {
        margin: 0;
        font-size: 18px;
        font-weight: 700;
        color: #991b1b;
        display: flex;
        align-items: center;
        gap: 10px;
    }
    
    .card-body-modern {
        padding: 32px;
    }
    
    .info-section {
        background: linear-gradient(135deg, #eff6ff 0%, #dbeafe 100%);
        padding: 24px;
        border-radius: 12px;
        margin-bottom: 32px;
        border-left: 4px solid #0288d1;
    }
    
    .info-section-title {
        font-size: 16px;
        font-weight: 700;
        color: #0288d1;
        margin-bottom: 16px;
        display: flex;
        align-items: center;
        gap: 10px;
    }
    
    .info-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 16px;
    }
    
    .info-item {
        background: white;
        padding: 12px 16px;
        border-radius: 8px;
        border: 1px solid #e5e7eb;
    }
    
    .info-item strong {
        color: #475569;
        font-size: 13px;
        display: block;
        margin-bottom: 4px;
    }
    
    .info-item span {
        color: #1e293b;
        font-size: 15px;
        font-weight: 600;
    }
    
    .form-section {
        margin-bottom: 32px;
    }
    
    .form-section-title {
        font-size: 18px;
        font-weight: 700;
        color: #1e293b;
        margin-bottom: 20px;
        display: flex;
        align-items: center;
        gap: 10px;
        padding-bottom: 12px;
        border-bottom: 2px solid #e5e7eb;
    }
    
    .form-group {
        margin-bottom: 24px;
    }
    
    .form-label {
        font-weight: 600;
        color: #1e293b;
        margin-bottom: 8px;
        display: block;
        font-size: 14px;
    }
    
    .form-control, .form-select {
        width: 100%;
        padding: 12px 16px;
        border: 2px solid #e5e7eb;
        border-radius: 10px;
        font-size: 15px;
        transition: all 0.3s ease;
    }
    
    .form-control:focus, .form-select:focus {
        outline: none;
        border-color: #dc2626;
        box-shadow: 0 0 0 3px rgba(220, 38, 38, 0.1);
    }
    
    .form-text {
        font-size: 13px;
        color: #64748b;
        margin-top: 6px;
    }
    
    .alert-modern {
        padding: 16px 20px;
        border-radius: 10px;
        margin-bottom: 24px;
        border-left: 4px solid;
        display: flex;
        align-items: start;
        gap: 12px;
    }
    
    .alert-warning {
        background: #fef3c7;
        color: #92400e;
        border-color: #f59e0b;
    }
    
    .alert-info {
        background: #dbeafe;
        color: #1e40af;
        border-color: #3b82f6;
    }
    
    .btn-modern {
        padding: 12px 24px;
        border-radius: 10px;
        font-weight: 600;
        font-size: 15px;
        display: inline-flex;
        align-items: center;
        gap: 8px;
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
    
    .btn-modern-secondary {
        background: #f1f5f9;
        color: #475569;
    }
    
    .btn-modern-secondary:hover {
        background: #e2e8f0;
    }
    
    .btn-group-modern {
        display: flex;
        gap: 12px;
        margin-top: 32px;
        padding-top: 24px;
        border-top: 2px solid #e5e7eb;
    }
</style>

<div class="nurse-page-container">
    <div class="page-header">
        <h1>
            <i class="fas fa-stethoscope"></i>
            Emergency Triage Assessment
        </h1>
    </div>
    
    <!-- Patient Information -->
    <div class="modern-card">
        <div class="card-header-modern">
            <h5><i class="fas fa-user-injured"></i> Patient Information</h5>
        </div>
        <div class="card-body-modern">
            <div class="info-section">
                <div class="info-grid">
                    <div class="info-item">
                        <strong>Patient Name</strong>
                        <span><?= esc($patient['full_name'] ?? ($patient['firstname'] ?? '') . ' ' . ($patient['lastname'] ?? '')) ?></span>
                    </div>
                    <div class="info-item">
                        <strong>Age</strong>
                        <span><?= esc($patient['age'] ?? 'N/A') ?></span>
                    </div>
                    <div class="info-item">
                        <strong>Gender</strong>
                        <span><?= esc($patient['gender'] ?? 'N/A') ?></span>
                    </div>
                    <?php if (isset($patient['patient_id']) || isset($patient['id'])): ?>
                    <div class="info-item">
                        <strong>Patient ID</strong>
                        <span><?= esc($patient['patient_id'] ?? $patient['id']) ?></span>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Triage Form -->
    <div class="modern-card">
        <div class="card-body-modern">
            <form id="triageForm" method="post" action="<?= site_url('nurse/triage/save') ?>">
                <?= csrf_field() ?>
                <input type="hidden" name="patient_id" value="<?= esc($patient['patient_id'] ?? $patient['id']) ?>">
                <input type="hidden" name="patient_source" value="<?= esc($patientSource) ?>">

                <!-- Vital Signs Section -->
                <div class="form-section">
                    <div class="form-section-title">
                        <i class="fas fa-heartbeat" style="color: #dc2626;"></i>
                        Vital Signs
                    </div>
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="form-label">Heart Rate (bpm) *</label>
                                <input type="number" name="heart_rate" class="form-control" required min="0" max="250" placeholder="e.g. 72">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="form-label">Blood Pressure - Systolic *</label>
                                <input type="number" name="blood_pressure_systolic" class="form-control" required min="0" max="300" placeholder="e.g. 120">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="form-label">Blood Pressure - Diastolic *</label>
                                <input type="number" name="blood_pressure_diastolic" class="form-control" required min="0" max="200" placeholder="e.g. 80">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="form-label">Temperature (°C) *</label>
                                <input type="number" name="temperature" class="form-control" required step="0.1" min="30" max="45" placeholder="e.g. 36.5">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="form-label">Oxygen Saturation (%) *</label>
                                <input type="number" name="oxygen_saturation" class="form-control" required min="0" max="100" placeholder="e.g. 98">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="form-label">Respiratory Rate (bpm) *</label>
                                <input type="number" name="respiratory_rate" class="form-control" required min="0" max="60" placeholder="e.g. 16">
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Triage Assessment Section -->
                <div class="form-section">
                    <div class="form-section-title">
                        <i class="fas fa-exclamation-triangle" style="color: #dc2626;"></i>
                        Triage Assessment
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label">Triage Level *</label>
                        <select name="triage_level" id="triage_level" class="form-select" required>
                            <option value="">-- Select Triage Level --</option>
                            <option value="Critical">🔴 Critical - Requires immediate medical attention</option>
                            <option value="Moderate">🟡 Moderate - Urgent but stable</option>
                            <option value="Minor">🟢 Minor - Non-urgent</option>
                        </select>
                        <div class="form-text">
                            <strong>Critical:</strong> Auto-routed to Emergency Room (ER)<br>
                            <strong>Moderate/Minor:</strong> Routed to Out-Patient Department (OPD)
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Disposition *</label>
                        <select name="disposition" id="disposition" class="form-select" required>
                            <option value="">-- Select Disposition --</option>
                            <option value="ER">🏥 Emergency Room (ER/ED) - Receptionist will assign room</option>
                            <option value="Doctor Assign">👨‍⚕️ Doctor Assign - Send to doctor for consultation</option>
                            <option value="OPD">🏥 Out-Patient Department (OPD/Clinic)</option>
                            <option value="Others">📋 Others - In-Patient (Admission) - Select doctor</option>
                        </select>
                        <div class="form-text">
                            <strong>ER:</strong> Patient goes to Emergency Room. Receptionist will assign ER room/bed.<br>
                            <strong>Doctor Assign:</strong> Patient will be sent to a doctor for consultation. Doctor can mark for admission if needed.<br>
                            <strong>OPD:</strong> Patient goes to Out-Patient Department queue.<br>
                            <strong>Others (In-Patient):</strong> Patient will be admitted. Please select an available doctor below.
                        </div>
                    </div>

                    <!-- Doctor Selection for Doctor Assign and In-Patients (Others disposition) -->
                    <div class="form-group" id="doctor_selection_group" style="display: none;">
                        <label class="form-label">Select Doctor *</label>
                        <select name="doctor_id" id="doctor_id" class="form-select">
                            <option value="">-- Loading Available Doctors --</option>
                        </select>
                        <div class="form-text" id="doctor_help_text">
                            <i class="fas fa-info-circle"></i> Select an available doctor.
                        </div>
                        <div id="doctor_schedule_info" style="margin-top: 12px; padding: 12px; background: #f8fafc; border-radius: 8px; display: none;">
                            <div style="font-size: 12px; color: #64748b; margin-bottom: 4px;">Schedule Status:</div>
                            <div id="schedule_status" style="font-weight: 600;"></div>
                            <div id="schedule_details" style="font-size: 12px; color: #475569; margin-top: 4px;"></div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Nurse Recommendation</label>
                        <textarea name="nurse_recommendation" id="nurse_recommendation" class="form-control" rows="3" placeholder="Enter your assessment and recommendation based on vital signs and quick assessment (e.g., 'Patient appears to need close monitoring, recommend admission for observation')"></textarea>
                        <div class="form-text">
                            <i class="fas fa-info-circle"></i> Based on your assessment, provide your professional recommendation for the doctor's review.
                        </div>
                    </div>


                    <div class="form-group">
                        <label class="form-label">Chief Complaint *</label>
                        <textarea name="chief_complaint" class="form-control" rows="3" required placeholder="Primary reason for visit (e.g., chest pain, difficulty breathing, etc.)"></textarea>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Triage Notes</label>
                        <textarea name="notes" class="form-control" rows="4" placeholder="Additional observations, symptoms, or relevant information"></textarea>
                    </div>
                </div>

                <!-- Alerts -->
                <div class="alert-modern alert-warning" id="criticalAlert" style="display: none;">
                    <i class="fas fa-exclamation-triangle"></i>
                    <div>
                        <strong>Critical Triage Level Selected:</strong><br>
                        Patient will be automatically assigned to an ER doctor and routed to Emergency Room immediately after triage completion.
                    </div>
                </div>

                <div class="alert-modern alert-info" id="opdAlert" style="display: none;">
                    <i class="fas fa-info-circle"></i>
                    <div>
                        <strong>OPD Routing:</strong><br>
                        Patient will be added to Out-Patient Department queue for doctor consultation.
                    </div>
                </div>

                <div class="alert-modern alert-info" id="doctorAssignAlert" style="display: none;">
                    <i class="fas fa-user-md"></i>
                    <div>
                        <strong>Doctor Assign Selected:</strong><br>
                        Patient will be sent to a doctor for consultation. The doctor will review the case and can mark for admission if needed.
                    </div>
                </div>

                <div class="alert-modern alert-secondary" id="othersAlert" style="display: none;">
                    <i class="fas fa-info-circle"></i>
                    <div>
                        <strong>Others Selected:</strong><br>
                        Patient will be routed based on special requirements. Please specify in notes if needed.
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="btn-group-modern">
                    <button type="submit" class="btn-modern btn-modern-danger">
                        <i class="fas fa-save"></i> Complete Triage
                    </button>
                    <a href="<?= site_url('nurse/triage') ?>" class="btn-modern btn-modern-secondary">
                        <i class="fas fa-times"></i> Cancel
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const triageLevel = document.getElementById('triage_level');
    const disposition = document.getElementById('disposition');
    const criticalAlert = document.getElementById('criticalAlert');
    const opdAlert = document.getElementById('opdAlert');
    const doctorAssignAlert = document.getElementById('doctorAssignAlert');
    const othersAlert = document.getElementById('othersAlert');
    
    // Auto-suggest disposition based on triage level (but user must still select)
    triageLevel.addEventListener('change', function() {
        const value = this.value;
        hideAllAlerts();
        
        // Suggest ER for Critical, but don't auto-select
        if (value === 'Critical' && !disposition.value) {
            // Just show alert, don't auto-select
            // User must manually select ER
        }
    });
    
    // Update alerts based on disposition
    disposition.addEventListener('change', function() {
        updateAlerts();
        updateDoctorSelection();
    });
    
    function updateAlerts() {
        hideAllAlerts();
        const dispValue = disposition.value;
        const triageValue = triageLevel.value;
        
        if (dispValue === 'ER') {
            criticalAlert.style.display = 'flex';
        } else if (dispValue === 'OPD') {
            opdAlert.style.display = 'flex';
        } else if (dispValue === 'Doctor Assign') {
            doctorAssignAlert.style.display = 'flex';
        } else if (dispValue === 'Others') {
            othersAlert.style.display = 'flex';
        }
    }
    
    function updateDoctorSelection() {
        const doctorGroup = document.getElementById('doctor_selection_group');
        const doctorSelect = document.getElementById('doctor_id');
        const doctorHelpText = document.getElementById('doctor_help_text');
        
        if (disposition.value === 'Others' || disposition.value === 'Doctor Assign') {
            // Show doctor selection for inpatients and doctor assign
            doctorGroup.style.display = 'block';
            doctorSelect.required = true;
            
            // Update help text based on disposition
            if (disposition.value === 'Others') {
                doctorHelpText.innerHTML = '<i class="fas fa-info-circle"></i> Select an available doctor for this in-patient admission.';
            } else if (disposition.value === 'Doctor Assign') {
                doctorHelpText.innerHTML = '<i class="fas fa-info-circle"></i> Select an available doctor for consultation. The doctor will review the case and can mark for admission if needed.';
            }
            
            // Load available doctors
            loadAvailableDoctors();
        } else {
            // Hide doctor selection
            doctorGroup.style.display = 'none';
            doctorSelect.required = false;
            doctorSelect.value = '';
            document.getElementById('doctor_schedule_info').style.display = 'none';
        }
    }
    
    function loadAvailableDoctors() {
        const doctorSelect = document.getElementById('doctor_id');
        doctorSelect.innerHTML = '<option value="">Loading doctors...</option>';
        doctorSelect.disabled = true;
        
        const date = new Date().toISOString().split('T')[0];
        
        fetch('<?= site_url('nurse/triage/get-doctors') ?>?date=' + date, {
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
            doctorSelect.innerHTML = '<option value="">-- Select Doctor --</option>';
            doctorSelect.disabled = false;
            
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
                    doctorSelect.appendChild(option);
                });
            } else {
                doctorSelect.innerHTML = '<option value="">No doctors available</option>';
            }
        })
        .catch(error => {
            console.error('Error loading doctors:', error);
            doctorSelect.innerHTML = '<option value="">Error loading doctors</option>';
            doctorSelect.disabled = false;
        });
    }
    
    // Show doctor schedule info when doctor is selected
    document.getElementById('doctor_id').addEventListener('change', function() {
        const selectedOption = this.options[this.selectedIndex];
        const scheduleInfo = document.getElementById('doctor_schedule_info');
        
        if (selectedOption && selectedOption.value) {
            const status = selectedOption.dataset.scheduleStatus || 'unknown';
            const scheduleTime = selectedOption.dataset.scheduleTime || 'N/A';
            const currentAppointments = selectedOption.dataset.currentAppointments || 0;
            const maxCapacity = selectedOption.dataset.maxCapacity || 0;
            
            document.getElementById('schedule_status').textContent = status === 'on_duty' ? 'On Duty' : (status === 'off_duty' ? 'Off Duty' : 'Unknown');
            document.getElementById('schedule_details').textContent = `Schedule: ${scheduleTime} | Appointments: ${currentAppointments}/${maxCapacity}`;
            scheduleInfo.style.display = 'block';
        } else {
            scheduleInfo.style.display = 'none';
        }
    });
    
    function hideAllAlerts() {
        criticalAlert.style.display = 'none';
        opdAlert.style.display = 'none';
        doctorAssignAlert.style.display = 'none';
        othersAlert.style.display = 'none';
    }
    
        // Form submission
        document.getElementById('triageForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            // Validate doctor selection for inpatients and doctor assign
            if (disposition.value === 'Others' || disposition.value === 'Doctor Assign') {
                const doctorId = document.getElementById('doctor_id').value;
                if (!doctorId) {
                    const message = disposition.value === 'Others' 
                        ? 'Please select a doctor for in-patient admission.'
                        : 'Please select a doctor for consultation.';
                    alert(message);
                    return;
                }
            }
            
            const submitBtn = this.querySelector('button[type="submit"]');
            const originalText = submitBtn.innerHTML;
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Processing...';
            
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
                let message = 'Triage completed successfully!';
                if (disposition.value === 'ER') {
                    message = 'Triage completed! Patient has been routed to Emergency Room. Receptionist will assign ER room/bed.';
                } else if (disposition.value === 'Doctor Assign') {
                    message = 'Triage completed! Patient has been sent to doctor for consultation. Doctor can mark for admission if needed.';
                } else if (disposition.value === 'OPD') {
                    message = 'Triage completed! Patient has been added to OPD queue.';
                } else if (disposition.value === 'Others') {
                    message = 'Triage completed! Patient routing will be handled as specified.';
                }
                
                alert(message);
                window.location.href = '<?= site_url('nurse/triage') ?>';
            } else {
                alert('Error: ' + (data.message || 'Failed to save triage data'));
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
    });
});
</script>
<?= $this->endSection() ?>

