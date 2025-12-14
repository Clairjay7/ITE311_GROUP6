<?= $this->extend('template/header') ?>

<?= $this->section('title') ?>Schedule Surgery<?= $this->endSection() ?>

<?= $this->section('content') ?>
<style>
    .surgery-page-container {
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
    
    .page-header h1 i {
        font-size: 32px;
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
    
    .form-group-modern {
        margin-bottom: 24px;
    }
    
    .form-label-modern {
        display: block;
        font-weight: 600;
        color: #1e293b;
        margin-bottom: 8px;
        font-size: 14px;
    }
    
    .form-control-modern {
        width: 100%;
        padding: 12px 16px;
        border: 2px solid #e5e7eb;
        border-radius: 10px;
        font-size: 14px;
        transition: all 0.2s ease;
    }
    
    .form-control-modern:focus {
        outline: none;
        border-color: #dc2626;
        box-shadow: 0 0 0 3px rgba(220, 38, 38, 0.1);
    }
    
    .btn-modern {
        padding: 12px 24px;
        border-radius: 10px;
        font-weight: 600;
        font-size: 14px;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        transition: all 0.2s ease;
        border: none;
        cursor: pointer;
    }
    
    .btn-modern-primary {
        background: linear-gradient(135deg, #dc2626 0%, #ef4444 100%);
        color: white;
        box-shadow: 0 4px 12px rgba(220, 38, 38, 0.3);
    }
    
    .btn-modern-primary:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 16px rgba(220, 38, 38, 0.4);
    }
    
    .btn-modern-secondary {
        background: #f1f5f9;
        color: #475569;
    }
    
    .btn-modern-secondary:hover {
        background: #e2e8f0;
    }
    
    .alert {
        padding: 16px 20px;
        border-radius: 10px;
        margin-bottom: 20px;
        font-weight: 500;
    }
    
    .alert-danger {
        background: #fee2e2;
        color: #991b1b;
        border-left: 4px solid #dc2626;
    }
    
    .alert-success {
        background: #e8f5e9;
        color: #1b5e20;
        border-left: 4px solid #10b981;
    }
    
    .patient-info-box {
        background: #f8fafc;
        padding: 20px;
        border-radius: 12px;
        margin-bottom: 24px;
        border-left: 4px solid #dc2626;
    }
    
    .patient-info-box h6 {
        margin: 0 0 12px;
        color: #991b1b;
        font-weight: 700;
    }
    
    .patient-info-box p {
        margin: 4px 0;
        color: #475569;
    }
</style>

<div class="surgery-page-container">
    <div class="page-header">
        <h1>
            <i class="fas fa-procedures"></i>
            Schedule Surgery
        </h1>
        <a href="<?= site_url('doctor/patients/view/' . ($patient['id'] ?? $patient['patient_id'])) ?>" class="btn-modern btn-modern-secondary">
            <i class="fas fa-arrow-left"></i>
            Back to Patient
        </a>
    </div>

    <?php if (session()->getFlashdata('error')): ?>
        <div class="alert alert-danger">
            <i class="fas fa-exclamation-circle"></i> <?= esc(session()->getFlashdata('error')) ?>
        </div>
    <?php endif; ?>
    
    <?php if (session()->getFlashdata('success')): ?>
        <div class="alert alert-success">
            <i class="fas fa-check-circle"></i> <?= esc(session()->getFlashdata('success')) ?>
        </div>
    <?php endif; ?>

    <div class="modern-card">
        <div class="card-header-modern">
            <h5>
                <i class="fas fa-user-injured"></i>
                Patient Information
            </h5>
        </div>
        <div class="card-body-modern">
            <div class="patient-info-box">
                <h6>Patient Details</h6>
                <p><strong>Name:</strong> <?= esc($patient['firstname'] ?? $patient['first_name'] ?? '') . ' ' . esc($patient['lastname'] ?? $patient['last_name'] ?? '') ?></p>
                <p><strong>Patient ID:</strong> #<?= esc($patient['id'] ?? $patient['patient_id'] ?? 'N/A') ?></p>
                <?php if (!empty($patient['age'])): ?>
                    <p><strong>Age:</strong> <?= esc($patient['age']) ?></p>
                <?php endif; ?>
                <?php if (!empty($patient['gender'])): ?>
                    <p><strong>Gender:</strong> <?= esc($patient['gender']) ?></p>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <div class="modern-card">
        <div class="card-header-modern">
            <h5>
                <i class="fas fa-calendar-check"></i>
                Surgery Details
            </h5>
        </div>
        <div class="card-body-modern">
            <form action="<?= site_url('doctor/surgery/store') ?>" method="POST">
                <?= csrf_field() ?>
                
                <input type="hidden" name="patient_id" value="<?= esc($patient['id'] ?? $patient['patient_id']) ?>">
                
                <div class="form-group-modern">
                    <label class="form-label-modern">
                        <i class="fas fa-stethoscope"></i> Type of Surgery <span style="color: #dc2626;">*</span>
                    </label>
                    <select name="surgery_type" id="surgery_type" class="form-control-modern" required onchange="filterDoctorsBySurgeryType()">
                        <option value="">-- Select Type of Surgery --</option>
                        <option value="General Surgery" data-duration="3" <?= old('surgery_type') == 'General Surgery' ? 'selected' : '' ?>>General Surgery (3 hrs)</option>
                        <option value="Orthopedic Surgery" data-duration="4" <?= old('surgery_type') == 'Orthopedic Surgery' ? 'selected' : '' ?>>Orthopedic Surgery (4 hrs)</option>
                        <option value="OB-Gyne Surgery" data-duration="2" <?= old('surgery_type') == 'OB-Gyne Surgery' ? 'selected' : '' ?>>OB-Gyne Surgery (2 hrs)</option>
                        <option value="ENT Surgery" data-duration="2" <?= old('surgery_type') == 'ENT Surgery' ? 'selected' : '' ?>>ENT Surgery (2 hrs)</option>
                        <option value="Urologic Surgery" data-duration="3" <?= old('surgery_type') == 'Urologic Surgery' ? 'selected' : '' ?>>Urologic Surgery (3 hrs)</option>
                    </select>
                    <?php if (session()->getFlashdata('validation') && session()->getFlashdata('validation')->hasError('surgery_type')): ?>
                        <div class="text-danger" style="margin-top: 4px; font-size: 13px;">
                            <?= session()->getFlashdata('validation')->getError('surgery_type') ?>
                        </div>
                    <?php endif; ?>
                </div>

                <div class="form-group-modern" id="assigned_doctor_container" style="display: none;">
                    <label class="form-label-modern">
                        <i class="fas fa-user-md"></i> Assign Doctor <span style="color: #dc2626;">*</span>
                    </label>
                    <select name="assigned_doctor_id" id="assigned_doctor_id" class="form-control-modern">
                        <option value="">-- Select Doctor --</option>
                        <?php if (!empty($doctors)): ?>
                            <?php foreach ($doctors as $doctor): ?>
                                <?php 
                                $doctorSpecialization = $doctor['specialization'] ?? '';
                                $doctorName = $doctor['doctor_name'] ?? 'Dr. ' . ($doctor['first_name'] ?? '') . ' ' . ($doctor['last_name'] ?? '');
                                ?>
                                <option value="<?= esc($doctor['user_id']) ?>" 
                                        data-specialization="<?= esc($doctorSpecialization) ?>"
                                        <?= old('assigned_doctor_id') == $doctor['user_id'] ? 'selected' : '' ?>>
                                    <?= esc($doctorName) ?> (<?= esc($doctorSpecialization ?: 'N/A') ?>)
                                </option>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <option value="" disabled>No doctors available. Please add doctors first.</option>
                        <?php endif; ?>
                    </select>
                    <?php if (session()->getFlashdata('validation') && session()->getFlashdata('validation')->hasError('assigned_doctor_id')): ?>
                        <div class="text-danger" style="margin-top: 4px; font-size: 13px;">
                            <?= session()->getFlashdata('validation')->getError('assigned_doctor_id') ?>
                        </div>
                    <?php endif; ?>
                    <small class="text-muted" style="display: block; margin-top: 4px; font-size: 12px; color: #64748b;">
                        <i class="fas fa-info-circle"></i> Only doctors matching the selected surgery type will be shown.
                    </small>
                </div>

                <div class="form-group-modern">
                    <label class="form-label-modern">
                        <i class="fas fa-hospital"></i> Operating Room (OR) <span style="color: #dc2626;">*</span>
                    </label>
                    <select name="or_room_id" id="or_room_id" class="form-control-modern" required>
                        <option value="">-- Select OR Room --</option>
                        <?php if (!empty($orRooms)): ?>
                            <?php foreach ($orRooms as $room): ?>
                                <option value="<?= esc($room['id']) ?>" <?= old('or_room_id') == $room['id'] ? 'selected' : '' ?>>
                                    <?= esc($room['room_number']) ?> - <?= esc($room['ward'] ?? 'N/A') ?>
                                    <?php if (!empty($room['price'])): ?>
                                        (₱<?= number_format((float)$room['price'], 2) ?>/day)
                                    <?php endif; ?>
                                </option>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <option value="" disabled>No available OR rooms</option>
                        <?php endif; ?>
                    </select>
                    <?php if (session()->getFlashdata('validation') && session()->getFlashdata('validation')->hasError('or_room_id')): ?>
                        <div class="text-danger" style="margin-top: 4px; font-size: 13px;">
                            <?= session()->getFlashdata('validation')->getError('or_room_id') ?>
                        </div>
                    <?php endif; ?>
                    <?php if (empty($orRooms)): ?>
                        <div class="text-danger" style="margin-top: 4px; font-size: 13px;">
                            <i class="fas fa-exclamation-triangle"></i> No available OR rooms. Please create OR rooms in Room Management first.
                        </div>
                    <?php endif; ?>
                </div>

                <div class="form-group-modern">
                    <label class="form-label-modern">
                        <i class="fas fa-bed"></i> Bed <span style="color: #dc2626;">*</span>
                    </label>
                    <select name="bed_id" id="bed_id" class="form-control-modern" required>
                        <option value="">-- Select OR Room first --</option>
                    </select>
                    <?php if (session()->getFlashdata('validation') && session()->getFlashdata('validation')->hasError('bed_id')): ?>
                        <div class="text-danger" style="margin-top: 4px; font-size: 13px;">
                            <?= session()->getFlashdata('validation')->getError('bed_id') ?>
                        </div>
                    <?php endif; ?>
                </div>

                <div class="form-group-modern">
                    <label class="form-label-modern">
                        <i class="fas fa-calendar"></i> Surgery Date <span style="color: #dc2626;">*</span>
                    </label>
                    <input type="date" name="surgery_date" id="surgery_date" class="form-control-modern" 
                           value="<?= old('surgery_date', date('Y-m-d')) ?>" 
                           min="<?= date('Y-m-d') ?>" 
                           required>
                    <?php if (session()->getFlashdata('validation') && session()->getFlashdata('validation')->hasError('surgery_date')): ?>
                        <div class="text-danger" style="margin-top: 4px; font-size: 13px;">
                            <?= session()->getFlashdata('validation')->getError('surgery_date') ?>
                        </div>
                    <?php endif; ?>
                    <small class="text-muted" style="display: block; margin-top: 4px; font-size: 12px; color: #64748b;">
                        <i class="fas fa-info-circle"></i> Operation duration will be automatically calculated based on the selected Type of Surgery
                    </small>
                </div>

                <div class="form-group-modern">
                    <label class="form-label-modern">
                        <i class="fas fa-sticky-note"></i> Notes
                    </label>
                    <textarea name="notes" id="notes" class="form-control-modern" rows="4" 
                              placeholder="Additional notes about the surgery..."><?= old('notes') ?></textarea>
                </div>

                <div style="display: flex; gap: 12px; margin-top: 32px;">
                    <button type="submit" class="btn-modern btn-modern-primary" <?= empty($orRooms) ? 'disabled' : '' ?>>
                        <i class="fas fa-save"></i>
                        Schedule Surgery
                    </button>
                    <a href="<?= site_url('doctor/patients/view/' . ($patient['id'] ?? $patient['patient_id'])) ?>" class="btn-modern btn-modern-secondary">
                        <i class="fas fa-times"></i>
                        Cancel
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
// Filter doctors based on surgery type - MUST be global function
function filterDoctorsBySurgeryType() {
    console.log('filterDoctorsBySurgeryType called');
    const surgeryTypeSelect = document.getElementById('surgery_type');
    const doctorSelect = document.getElementById('assigned_doctor_id');
    const doctorContainer = document.getElementById('assigned_doctor_container');
    
    if (!surgeryTypeSelect) {
        console.error('Surgery type select not found');
        return;
    }
    if (!doctorSelect) {
        console.error('Doctor select not found');
        return;
    }
    if (!doctorContainer) {
        console.error('Doctor container not found');
        return;
    }
    
    const surgeryType = surgeryTypeSelect.value;
    console.log('Surgery type selected:', surgeryType);
    
    if (!surgeryType) {
        doctorContainer.style.display = 'none';
        doctorSelect.value = '';
        doctorSelect.removeAttribute('required');
        return;
    }
    
    // Show doctor selection
    doctorContainer.style.display = 'block';
    doctorContainer.style.visibility = 'visible';
    doctorContainer.style.opacity = '1';
    doctorSelect.setAttribute('required', 'required');
    
    console.log('Showing doctor dropdown for surgery type:', surgeryType);
    console.log('Doctor container display:', window.getComputedStyle(doctorContainer).display);
    
    // Filter doctors based on surgery type
    const allOptions = doctorSelect.querySelectorAll('option');
    let hasMatchingDoctor = false;
    console.log('Total doctor options:', allOptions.length);
    
    allOptions.forEach(option => {
        if (option.value === '' || option.disabled) {
            option.style.display = 'block';
            return;
        }
        
        const specialization = option.getAttribute('data-specialization');
        console.log('Checking doctor:', option.textContent, 'Specialization:', specialization);
        // Match surgery type with doctor specialization (exact match)
        if (specialization && specialization.trim().toLowerCase() === surgeryType.trim().toLowerCase()) {
            option.style.display = 'block';
            option.disabled = false;
            hasMatchingDoctor = true;
            console.log('Match found:', option.textContent);
        } else {
            option.style.display = 'none';
        }
    });
    
    // Reset selection
    doctorSelect.value = '';
    
    // Show message if no matching doctors
    if (!hasMatchingDoctor) {
        console.warn('No doctors found for surgery type: ' + surgeryType);
        // Hide dropdown if no matching doctors
        doctorContainer.style.display = 'none';
        doctorSelect.removeAttribute('required');
        alert('Warning: No doctors with schedules found matching ' + surgeryType + '. Please ensure doctors have schedules and are properly assigned.');
    } else {
        console.log('Doctors filtered successfully. Found ' + hasMatchingDoctor + ' matching doctor(s).');
    }
}

document.addEventListener('DOMContentLoaded', function() {
    const orRoomSelect = document.getElementById('or_room_id');
    const bedSelect = document.getElementById('bed_id');
    
    // Initialize doctor dropdown if surgery type is already selected
    const surgeryTypeSelect = document.getElementById('surgery_type');
    if (surgeryTypeSelect && surgeryTypeSelect.value) {
        filterDoctorsBySurgeryType();
    }
    
    // Add change event listener for surgery type
    if (surgeryTypeSelect) {
        surgeryTypeSelect.addEventListener('change', filterDoctorsBySurgeryType);
    }
    
    orRoomSelect.addEventListener('change', function() {
        const roomId = this.value;
        bedSelect.innerHTML = '<option value="">Loading beds...</option>';
        bedSelect.disabled = true;
        
        if (!roomId) {
            bedSelect.innerHTML = '<option value="">-- Select OR Room first --</option>';
            bedSelect.disabled = false;
            return;
        }
        
        // Fetch beds for selected room
        fetch('<?= site_url('doctor/surgery/get-beds/') ?>' + roomId)
            .then(response => response.json())
            .then(data => {
                bedSelect.innerHTML = '<option value="">-- Select Bed --</option>';
                
                if (data.success && data.beds && data.beds.length > 0) {
                    data.beds.forEach(bed => {
                        const option = document.createElement('option');
                        option.value = bed.id;
                        option.textContent = `Bed ${bed.bed_number} - ${bed.status}`;
                        if (bed.status !== 'available') {
                            option.disabled = true;
                            option.textContent += ' (Occupied)';
                        }
                        bedSelect.appendChild(option);
                    });
                } else {
                    bedSelect.innerHTML = '<option value="" disabled>No beds available for this room</option>';
                }
                
                bedSelect.disabled = false;
            })
            .catch(error => {
                console.error('Error fetching beds:', error);
                bedSelect.innerHTML = '<option value="">Error loading beds</option>';
                bedSelect.disabled = false;
            });
    });
    
    // Trigger change if OR room is pre-selected
    if (orRoomSelect.value) {
        orRoomSelect.dispatchEvent(new Event('change'));
    }
});
</script>

<?= $this->endSection() ?>

