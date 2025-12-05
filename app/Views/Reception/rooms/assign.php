<?php
/** @var array $room */
/** @var array $patients */
/** @var array $doctors */
$errors = session()->getFlashdata('errors') ?? [];
?>
<?= $this->extend('template/header') ?>
<?= $this->section('title') ?>Assign Patient to Room<?= $this->endSection() ?>

<?= $this->section('content') ?>
<style>
    .assign-container {
        max-width: 900px;
        margin: 0 auto;
        padding: 0;
    }
    
    .page-header {
        background: linear-gradient(135deg, var(--gradient-1) 0%, var(--gradient-2) 100%);
        border-radius: 16px;
        padding: 24px 32px;
        margin-bottom: 24px;
        box-shadow: 0 4px 20px rgba(46, 125, 50, 0.2);
        color: white;
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 16px;
    }
    
    .page-header h1 {
        margin: 0;
        font-size: 26px;
        font-weight: 700;
        display: flex;
        align-items: center;
        gap: 12px;
    }
    
    .back-btn {
        background: rgba(255, 255, 255, 0.2);
        color: white;
        padding: 10px 20px;
        border-radius: 10px;
        text-decoration: none;
        font-weight: 600;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        transition: all 0.3s ease;
    }
    
    .back-btn:hover {
        background: rgba(255, 255, 255, 0.3);
        color: white;
    }
    
    .form-card {
        background: white;
        border-radius: 16px;
        box-shadow: 0 2px 12px rgba(0, 0, 0, 0.08);
        border: 1px solid #e5e7eb;
        overflow: hidden;
        margin-bottom: 24px;
    }
    
    .form-card-body {
        padding: 24px;
    }
    
    .room-info-box {
        background: linear-gradient(135deg, #e8f5e9 0%, #c8e6c9 100%);
        border-radius: 12px;
        padding: 16px 20px;
        margin-bottom: 24px;
        border-left: 4px solid var(--primary-color);
    }
    
    .room-info-box h4 {
        margin: 0 0 8px;
        color: var(--primary-color);
        font-size: 18px;
    }
    
    .room-info-box .info-row {
        display: flex;
        gap: 24px;
        flex-wrap: wrap;
        margin-top: 12px;
    }
    
    .room-info-box .info-item {
        display: flex;
        align-items: center;
        gap: 8px;
        color: #64748b;
        font-size: 14px;
    }
    
    .room-info-box .info-item strong {
        color: #1e293b;
    }
    
    .toggle-buttons {
        display: flex;
        gap: 12px;
        margin-bottom: 24px;
        padding: 8px;
        background: #f8fafc;
        border-radius: 12px;
    }
    
    .toggle-btn {
        flex: 1;
        padding: 12px 20px;
        border: 2px solid #e5e7eb;
        background: white;
        border-radius: 10px;
        font-weight: 600;
        font-size: 14px;
        cursor: pointer;
        transition: all 0.2s ease;
        text-align: center;
    }
    
    .toggle-btn.active {
        background: linear-gradient(135deg, var(--gradient-1) 0%, var(--gradient-2) 100%);
        border-color: var(--primary-color);
        color: white;
    }
    
    .form-section {
        display: none;
    }
    
    .form-section.active {
        display: block;
    }
    
    .form-group {
        margin-bottom: 16px;
    }
    
    .form-label {
        display: block;
        margin-bottom: 6px;
        font-weight: 600;
        color: #1e293b;
        font-size: 14px;
    }
    
    .form-label .required {
        color: #ef4444;
        margin-left: 2px;
    }
    
    .form-control, .form-select {
        width: 100%;
        padding: 10px 14px;
        border: 2px solid #e5e7eb;
        border-radius: 10px;
        font-size: 14px;
        color: #1e293b;
        background: white;
        transition: all 0.2s ease;
    }
    
    .form-control:focus, .form-select:focus {
        outline: none;
        border-color: var(--primary-color);
        box-shadow: 0 0 0 3px rgba(46, 125, 50, 0.1);
    }
    
    .form-control.is-invalid, .form-select.is-invalid {
        border-color: #ef4444;
    }
    
    .form-row {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 16px;
    }
    
    .form-actions {
        display: flex;
        gap: 12px;
        padding-top: 20px;
        border-top: 2px solid #e5e7eb;
        margin-top: 24px;
    }
    
    .btn {
        padding: 12px 24px;
        border-radius: 10px;
        font-weight: 600;
        font-size: 15px;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        transition: all 0.3s ease;
        border: none;
        text-decoration: none;
    }
    
    .btn-primary {
        background: linear-gradient(135deg, var(--gradient-1) 0%, var(--gradient-2) 100%);
        color: white;
    }
    
    .btn-primary:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(46, 125, 50, 0.3);
    }
    
    .btn-secondary {
        background: #f1f5f9;
        color: #64748b;
        border: 2px solid #e5e7eb;
    }
    
    .btn-secondary:hover {
        background: #e5e7eb;
        color: #475569;
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
        border-left: 4px solid #ef4444;
    }
    
    .alert-success {
        background: #d1fae5;
        color: #065f46;
        border-left: 4px solid #10b981;
    }
    
    .invalid-feedback {
        color: #ef4444;
        font-size: 12px;
        margin-top: 4px;
    }
</style>

<div class="assign-container">
    <div class="page-header">
        <h1>
            <i class="fas fa-user-plus"></i>
            Assign Patient to Room
        </h1>
        <?php
        // Determine back URL based on room type
        $backUrl = site_url('receptionist/rooms');
        if (!empty($room['room_type'])) {
            $slugMap = [
                'Private' => 'private',
                'Semi-Private' => 'semi-private',
                'Ward' => 'ward',
                'ICU' => 'icu',
                'Isolation' => 'isolation',
            ];
            $slug = $slugMap[$room['room_type']] ?? 'ward';
            $backUrl = site_url('receptionist/rooms/type/' . $slug);
        } elseif (!empty($room['ward'])) {
            $wardMap = [
                'Pedia Ward' => 'pedia',
                'Male Ward' => 'male',
                'Female Ward' => 'female',
            ];
            $slug = $wardMap[$room['ward']] ?? 'pedia';
            $backUrl = site_url('receptionist/rooms/ward/' . $slug);
        }
        ?>
        <a href="<?= $backUrl ?>" class="back-btn">
            <i class="fas fa-arrow-left"></i> Back to Rooms
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

    <!-- Room Information -->
    <div class="room-info-box">
        <h4><i class="fas fa-bed"></i> Room Information</h4>
        <div class="info-row">
            <div class="info-item">
                <strong>Room Number:</strong> <?= esc($room['room_number']) ?>
            </div>
            <div class="info-item">
                <strong>Room Type:</strong> <?= esc($room['room_type'] ?? 'N/A') ?>
            </div>
            <div class="info-item">
                <strong>Ward:</strong> <?= esc($room['ward'] ?? 'N/A') ?>
            </div>
            <?php if (!empty($room['price']) && $room['price'] > 0): ?>
                <div class="info-item">
                    <strong>Price:</strong> ₱<?= number_format((float)$room['price'], 2) ?>/day
                </div>
            <?php endif; ?>
        </div>
    </div>

    <div class="form-card">
        <div class="form-card-body">
            <!-- Toggle Buttons -->
            <div class="toggle-buttons">
                <button type="button" class="toggle-btn active" onclick="showSection('existing')">
                    <i class="fas fa-list"></i> Select Existing Patient
                </button>
                <button type="button" class="toggle-btn" onclick="showSection('new')">
                    <i class="fas fa-user-plus"></i> Add New Patient
                </button>
            </div>

            <!-- Existing Patient Selection -->
            <form method="post" action="<?= site_url('receptionist/rooms/assign/' . $room['id']) ?>" id="assignForm" class="form-section active">
                <?= csrf_field() ?>
                <input type="hidden" name="create_new_patient" value="0">
                
                <div class="form-group">
                    <label class="form-label">Select Patient (In-Patient) <span class="required">*</span></label>
                    <select name="patient_id" id="patient_id" class="form-select" required>
            <option value="">-- Select Patient --</option>
            <?php foreach ($patients as $p): ?>
              <option value="<?= esc($p['patient_id']) ?>">
                <?= esc($p['patient_id']) ?> - <?= esc($p['full_name'] ?? ($p['first_name'] . ' ' . $p['last_name'])) ?>
                                <?php if (!empty($p['room_number'])): ?>
                                    (Currently in: <?= esc($p['room_number']) ?>)
                                <?php endif; ?>
              </option>
            <?php endforeach; ?>
          </select>
        </div>
                
                <!-- Bed Selection (if room has multiple beds) -->
                <?php if (($room['bed_count'] ?? 1) > 1): ?>
                    <div class="form-group">
                        <label class="form-label">Bed Number</label>
                        <select name="bed_number" id="bed_number" class="form-select">
                            <option value="">-- Select Bed (Optional) --</option>
                            <!-- Beds will be populated via AJAX or JavaScript -->
                        </select>
                        <input type="hidden" name="bed_id" id="bed_id" value="">
                    </div>
                <?php endif; ?>
                
                <div class="form-actions">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-check"></i> Assign Patient
                    </button>
                    <a href="<?= $backUrl ?>" class="btn btn-secondary">
                        <i class="fas fa-times"></i> Cancel
                    </a>
                </div>
            </form>

            <!-- New Patient Form -->
            <form method="post" action="<?= site_url('receptionist/rooms/assign/' . $room['id']) ?>" id="newPatientForm" class="form-section">
                <?= csrf_field() ?>
                <input type="hidden" name="create_new_patient" value="1">
                <input type="hidden" name="admission_date" value="<?= date('Y-m-d') ?>">
                
                <!-- Patient Information -->
                <h4 style="color: var(--primary-color); margin-bottom: 16px; padding-bottom: 12px; border-bottom: 2px solid #c8e6c9;">
                    <i class="fas fa-user"></i> Patient Information
                </h4>
                
                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">First Name <span class="required">*</span></label>
                        <input type="text" name="first_name" class="form-control <?= isset($errors['first_name']) ? 'is-invalid' : '' ?>" 
                               value="<?= set_value('first_name') ?>" required>
                        <?php if (isset($errors['first_name'])): ?>
                            <div class="invalid-feedback"><?= esc($errors['first_name']) ?></div>
                        <?php endif; ?>
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label">Middle Name</label>
                        <input type="text" name="middle_name" class="form-control" value="<?= set_value('middle_name') ?>">
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label">Last Name <span class="required">*</span></label>
                        <input type="text" name="last_name" class="form-control <?= isset($errors['last_name']) ? 'is-invalid' : '' ?>" 
                               value="<?= set_value('last_name') ?>" required>
                        <?php if (isset($errors['last_name'])): ?>
                            <div class="invalid-feedback"><?= esc($errors['last_name']) ?></div>
                        <?php endif; ?>
                    </div>
                </div>
                
                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">Date of Birth <span class="required">*</span></label>
                        <input type="date" name="date_of_birth" id="date_of_birth" class="form-control" 
                               value="<?= set_value('date_of_birth') ?>" required>
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label">Gender <span class="required">*</span></label>
                        <select name="gender" class="form-select <?= isset($errors['gender']) ? 'is-invalid' : '' ?>" required>
                            <option value="">-- Select Gender --</option>
                            <option value="Male" <?= set_select('gender', 'Male') ?>>Male</option>
                            <option value="Female" <?= set_select('gender', 'Female') ?>>Female</option>
                            <option value="Other" <?= set_select('gender', 'Other') ?>>Other</option>
                        </select>
                        <?php if (isset($errors['gender'])): ?>
                            <div class="invalid-feedback"><?= esc($errors['gender']) ?></div>
                        <?php endif; ?>
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label">Contact Number <span class="required">*</span></label>
                        <input type="text" name="contact" class="form-control" 
                               value="<?= set_value('contact') ?>" required placeholder="09XX-XXX-XXXX">
                    </div>
                </div>
                
                <div class="form-group">
                    <label class="form-label">Complete Address <span class="required">*</span></label>
                    <input type="text" name="address" class="form-control" 
                           value="<?= set_value('address') ?>" required>
                </div>
                
                <!-- Admission Details -->
                <h4 style="color: var(--primary-color); margin: 24px 0 16px; padding-top: 20px; border-top: 2px solid #e5e7eb; padding-bottom: 12px; border-bottom: 2px solid #c8e6c9;">
                    <i class="fas fa-hospital"></i> Admission Details
                </h4>
                
                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">Admitting Doctor <span class="required">*</span></label>
                        <select name="doctor_id" class="form-select" required>
                            <option value="">-- Select Doctor --</option>
                            <?php if (!empty($doctors)): ?>
                                <?php foreach ($doctors as $doctor): ?>
                                    <option value="<?= esc($doctor['id']) ?>" <?= set_select('doctor_id', $doctor['id']) ?>>
                                        Dr. <?= esc($doctor['doctor_name']) ?> - <?= esc($doctor['specialization'] ?? 'General Practice') ?>
                                    </option>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </select>
                    </div>
                </div>
                
                <div class="form-group">
                    <label class="form-label">Reason for Admission <span class="required">*</span></label>
                    <textarea name="purpose" class="form-control" rows="3" required 
                              placeholder="Enter the reason for admission"><?= set_value('purpose') ?></textarea>
                </div>
                
                <!-- Medical Information (Optional) -->
                <h4 style="color: #64748b; margin: 24px 0 16px; padding-top: 20px; border-top: 2px solid #e5e7eb; padding-bottom: 12px; border-bottom: 2px solid #e5e7eb; font-size: 16px;">
                    <i class="fas fa-notes-medical"></i> Medical Information (Optional)
                </h4>
                
                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">Existing Medical Conditions</label>
                        <textarea name="existing_conditions" class="form-control" rows="2" 
                                  placeholder="e.g., Diabetes, Hypertension"><?= set_value('existing_conditions') ?></textarea>
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label">Allergies</label>
                        <textarea name="allergies" class="form-control" rows="2" 
                                  placeholder="e.g., Penicillin, Seafood"><?= set_value('allergies') ?></textarea>
                    </div>
                </div>
                
                <!-- Insurance Information (Optional) -->
                <h4 style="color: #64748b; margin: 24px 0 16px; padding-top: 20px; border-top: 2px solid #e5e7eb; padding-bottom: 12px; border-bottom: 2px solid #e5e7eb; font-size: 16px;">
                    <i class="fas fa-shield-alt"></i> Insurance Information (Optional)
                </h4>
                
                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">Insurance Provider</label>
                        <select name="insurance_provider" class="form-select">
                            <option value="">-- Select Provider --</option>
                            <option value="PhilHealth" <?= set_select('insurance_provider', 'PhilHealth') ?>>PhilHealth</option>
                            <option value="Maxicare" <?= set_select('insurance_provider', 'Maxicare') ?>>Maxicare</option>
                            <option value="Medicard" <?= set_select('insurance_provider', 'Medicard') ?>>Medicard</option>
                            <option value="Other" <?= set_select('insurance_provider', 'Other') ?>>Other</option>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label">Insurance Number</label>
                        <input type="text" name="insurance_number" class="form-control" 
                               value="<?= set_value('insurance_number') ?>" placeholder="Enter insurance number">
                    </div>
                </div>
                
                <!-- Emergency Contact -->
                <h4 style="color: var(--primary-color); margin: 24px 0 16px; padding-top: 20px; border-top: 2px solid #e5e7eb; padding-bottom: 12px; border-bottom: 2px solid #c8e6c9;">
                    <i class="fas fa-phone-alt"></i> Emergency Contact
                </h4>
                
                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">Emergency Contact Name <span class="required">*</span></label>
                        <input type="text" name="emergency_name" class="form-control" 
                               value="<?= set_value('emergency_name') ?>" required>
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label">Relationship <span class="required">*</span></label>
                        <select name="emergency_relationship" class="form-select" required>
                            <option value="">-- Select --</option>
                            <option value="Spouse" <?= set_select('emergency_relationship', 'Spouse') ?>>Spouse</option>
                            <option value="Parent" <?= set_select('emergency_relationship', 'Parent') ?>>Parent</option>
                            <option value="Child" <?= set_select('emergency_relationship', 'Child') ?>>Child</option>
                            <option value="Sibling" <?= set_select('emergency_relationship', 'Sibling') ?>>Sibling</option>
                            <option value="Relative" <?= set_select('emergency_relationship', 'Relative') ?>>Relative</option>
                            <option value="Friend" <?= set_select('emergency_relationship', 'Friend') ?>>Friend</option>
                            <option value="Other" <?= set_select('emergency_relationship', 'Other') ?>>Other</option>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label">Contact Number <span class="required">*</span></label>
                        <input type="text" name="emergency_contact" class="form-control" 
                               value="<?= set_value('emergency_contact') ?>" required placeholder="09XX-XXX-XXXX">
                    </div>
                </div>
                
                <!-- Bed Selection (if room has multiple beds) -->
                <?php if (($room['bed_count'] ?? 1) > 1): ?>
                    <div class="form-group">
                        <label class="form-label">Bed Number</label>
                        <select name="bed_number" id="bed_number_new" class="form-select">
                            <option value="">-- Select Bed (Optional) --</option>
                            <!-- Beds will be populated via AJAX or JavaScript -->
                        </select>
                        <input type="hidden" name="bed_id" id="bed_id_new" value="">
                    </div>
                <?php endif; ?>
                
                <div class="form-actions">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-user-plus"></i> Create & Assign Patient
                    </button>
                    <a href="<?= $backUrl ?>" class="btn btn-secondary">
                        <i class="fas fa-times"></i> Cancel
                    </a>
        </div>
      </form>
    </div>
  </div>
</div>

<script>
function showSection(section) {
    // Hide all sections
    document.querySelectorAll('.form-section').forEach(sec => {
        sec.classList.remove('active');
    });
    
    // Remove active class from all buttons
    document.querySelectorAll('.toggle-btn').forEach(btn => {
        btn.classList.remove('active');
    });
    
    // Show selected section
    if (section === 'existing') {
        document.getElementById('assignForm').classList.add('active');
        document.querySelectorAll('.toggle-btn')[0].classList.add('active');
    } else {
        document.getElementById('newPatientForm').classList.add('active');
        document.querySelectorAll('.toggle-btn')[1].classList.add('active');
    }
}

// Load beds when room is known (for existing patient assignment)
<?php if (($room['bed_count'] ?? 1) > 1): ?>
    const roomId = <?= $room['id'] ?>;
    
    // Load available beds for this room
    fetch('<?= site_url('receptionist/rooms/get-beds/' . $room['id']) ?>')
        .then(response => response.json())
        .then(data => {
            if (data.success && data.beds) {
                const bedSelect = document.getElementById('bed_number');
                const bedSelectNew = document.getElementById('bed_number_new');
                
                if (bedSelect) {
                    data.beds.forEach(bed => {
                        const option = document.createElement('option');
                        option.value = bed.bed_number;
                        option.dataset.bedId = bed.id;
                        bedSelect.appendChild(option);
                        option.textContent = 'Bed ' + bed.bed_number;
                    });
                }
                
                if (bedSelectNew) {
                    data.beds.forEach(bed => {
                        const option = document.createElement('option');
                        option.value = bed.bed_number;
                        option.dataset.bedId = bed.id;
                        bedSelectNew.appendChild(option);
                        option.textContent = 'Bed ' + bed.bed_number;
                    });
                }
            }
        })
        .catch(error => console.error('Error loading beds:', error));
<?php endif; ?>

// Update bed_id when bed is selected
document.addEventListener('change', function(e) {
    if (e.target.id === 'bed_number' || e.target.id === 'bed_number_new') {
        const selectedOption = e.target.options[e.target.selectedIndex];
        const bedIdField = e.target.id === 'bed_number' 
            ? document.getElementById('bed_id')
            : document.getElementById('bed_id_new');
        
        if (bedIdField && selectedOption && selectedOption.dataset.bedId) {
            bedIdField.value = selectedOption.dataset.bedId;
        }
    }
});
</script>

<?= $this->endSection() ?>
