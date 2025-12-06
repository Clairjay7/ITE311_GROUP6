<?= $this->extend('template/header') ?>

<?= $this->section('title') ?>Admit Patient<?= $this->endSection() ?>

<?= $this->section('content') ?>
<style>
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
        color: #dc2626;
    }
    .card-body-modern {
        padding: 32px;
    }
    .form-label-modern {
        font-weight: 600;
        color: #1e293b;
        margin-bottom: 8px;
        font-size: 14px;
        display: block;
    }
    .form-control-modern,
    .form-select-modern,
    textarea.form-control-modern {
        width: 100%;
        padding: 12px 16px;
        border: 2px solid #e5e7eb;
        border-radius: 10px;
        font-size: 15px;
        transition: all 0.3s ease;
        background: white;
        font-family: inherit;
    }
    .form-control-modern:focus,
    .form-select-modern:focus,
    textarea.form-control-modern:focus {
        outline: none;
        border-color: #dc2626;
        box-shadow: 0 0 0 3px rgba(220, 38, 38, 0.1);
    }
    textarea.form-control-modern {
        min-height: 120px;
        resize: vertical;
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
    .btn-modern-primary {
        background: linear-gradient(135deg, #dc2626 0%, #ef4444 100%);
        color: white;
        box-shadow: 0 4px 12px rgba(220, 38, 38, 0.3);
    }
    .btn-modern-primary:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(220, 38, 38, 0.4);
        color: white;
    }
    .btn-modern-secondary {
        background: #f1f5f9;
        color: #475569;
    }
    .info-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 20px;
        margin-bottom: 24px;
    }
    .info-item {
        background: #f8fafc;
        padding: 16px;
        border-radius: 10px;
        border-left: 4px solid #dc2626;
    }
    .info-item label {
        display: block;
        font-size: 12px;
        font-weight: 600;
        color: #64748b;
        text-transform: uppercase;
        margin-bottom: 6px;
    }
    .info-item .value {
        font-size: 16px;
        font-weight: 600;
        color: #1e293b;
    }
</style>

<div class="page-header">
    <h1><i class="fas fa-hospital"></i> Admit Patient</h1>
</div>

<?php if (session()->getFlashdata('error')): ?>
    <div style="background: #fee2e2; color: #991b1b; padding: 12px; border-radius: 8px; margin-bottom: 16px;">
        <?= session()->getFlashdata('error') ?>
    </div>
<?php endif; ?>

<?php if ($patient): ?>
    <div class="modern-card">
        <div class="card-header-modern">
            <h5><i class="fas fa-user-injured"></i> Patient Information</h5>
        </div>
        <div class="card-body-modern">
            <div class="info-grid">
                <div class="info-item">
                    <label>Patient Name</label>
                    <div class="value"><?= esc(ucwords(trim(($patient['firstname'] ?? '') . ' ' . ($patient['lastname'] ?? '')))) ?></div>
                </div>
                <div class="info-item">
                    <label>Age</label>
                    <div class="value"><?= esc($patient['age'] ?? 'N/A') ?></div>
                </div>
                <div class="info-item">
                    <label>Gender</label>
                    <div class="value"><?= esc(ucfirst($patient['gender'] ?? 'N/A')) ?></div>
                </div>
                <?php if ($consultation): ?>
                <div class="info-item">
                    <label>Consultation Date</label>
                    <div class="value"><?= date('M d, Y', strtotime($consultation['consultation_date'])) ?></div>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
<?php endif; ?>

<div class="modern-card">
    <div class="card-header-modern">
        <h5><i class="fas fa-bed"></i> Admission Details</h5>
    </div>
    <div class="card-body-modern">
        <form action="<?= site_url('admission/store') ?>" method="post">
            <?= csrf_field() ?>
            
            <?php if ($consultation): ?>
                <input type="hidden" name="consultation_id" value="<?= esc($consultation['id']) ?>">
            <?php endif; ?>
            <input type="hidden" name="patient_id" value="<?= esc($patient['id'] ?? $consultation['patient_id'] ?? '') ?>">
            
            <div class="form-group-modern" style="margin-bottom: 24px;">
                <label class="form-label-modern" for="room_id">
                    <i class="fas fa-door-open"></i> Room <span style="color: #dc2626;">*</span>
                </label>
                <select name="room_id" id="room_id" class="form-select-modern" required onchange="loadBeds(this.value)">
                    <option value="">Select Room</option>
                    <?php if (!empty($roomsByWard)): ?>
                        <?php foreach ($roomsByWard as $ward => $wardRooms): ?>
                            <optgroup label="<?= esc($ward) ?>">
                                <?php foreach ($wardRooms as $room): ?>
                                    <option value="<?= esc($room['id']) ?>" 
                                            data-room-type="<?= esc($room['room_type'] ?? 'Ward') ?>"
                                            data-ward="<?= esc($room['ward']) ?>">
                                        <?= esc($room['room_number']) ?> - <?= esc($room['room_type'] ?? 'Ward') ?> (<?= esc($room['ward']) ?>)
                                    </option>
                                <?php endforeach; ?>
                            </optgroup>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </select>
            </div>

            <div class="form-group-modern" style="margin-bottom: 24px;">
                <label class="form-label-modern" for="bed_id">
                    <i class="fas fa-bed"></i> Bed (Optional)
                </label>
                <select name="bed_id" id="bed_id" class="form-select-modern">
                    <option value="">No specific bed</option>
                </select>
                <input type="hidden" name="bed_number" id="bed_number" value="">
                <small style="color: #64748b; font-size: 13px; margin-top: 4px; display: block;">
                    Select a room first to see available beds
                </small>
            </div>

            <div class="form-group-modern" style="margin-bottom: 24px;">
                <label class="form-label-modern" for="room_type">
                    <i class="fas fa-building"></i> Room Type
                </label>
                <input type="text" 
                       name="room_type" 
                       id="room_type" 
                       class="form-control-modern" 
                       placeholder="e.g., Private, Semi-Private, Ward"
                       readonly>
                <small style="color: #64748b; font-size: 13px; margin-top: 4px; display: block;">
                    Auto-filled from selected room
                </small>
            </div>

            <div class="form-group-modern" style="margin-bottom: 24px;">
                <label class="form-label-modern" for="admission_reason">
                    <i class="fas fa-file-medical"></i> Admission Reason <span style="color: #dc2626;">*</span>
                </label>
                <textarea name="admission_reason" 
                          id="admission_reason" 
                          class="form-control-modern" 
                          rows="4" 
                          placeholder="Enter the reason for admission..."
                          required><?= old('admission_reason') ?></textarea>
                <?php if (session()->getFlashdata('validation') && session()->getFlashdata('validation')->hasError('admission_reason')): ?>
                    <div class="text-danger" style="margin-top: 4px;">
                        <?= session()->getFlashdata('validation')->getError('admission_reason') ?>
                    </div>
                <?php endif; ?>
            </div>

            <div class="form-group-modern" style="margin-bottom: 24px;">
                <label class="form-label-modern" for="attending_physician_id">
                    <i class="fas fa-user-md"></i> Attending Physician <span style="color: #dc2626;">*</span>
                </label>
                <select name="attending_physician_id" id="attending_physician_id" class="form-select-modern" required>
                    <option value="">Select Attending Physician</option>
                    <?php if (!empty($doctors)): ?>
                        <?php foreach ($doctors as $doctor): ?>
                            <option value="<?= esc($doctor['id']) ?>" <?= old('attending_physician_id') == $doctor['id'] ? 'selected' : '' ?>>
                                <?= esc($doctor['doctor_name'] ?? $doctor['id']) ?>
                                <?php if (!empty($doctor['specialization'])): ?>
                                  - <?= esc($doctor['specialization']) ?>
                                <?php endif; ?>
                            </option>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </select>
                <?php if (session()->getFlashdata('validation') && session()->getFlashdata('validation')->hasError('attending_physician_id')): ?>
                    <div class="text-danger" style="margin-top: 4px;">
                        <?= session()->getFlashdata('validation')->getError('attending_physician_id') ?>
                    </div>
                <?php endif; ?>
            </div>

            <div class="form-group-modern" style="margin-bottom: 24px;">
                <label class="form-label-modern" for="initial_notes">
                    <i class="fas fa-sticky-note"></i> Initial Notes
                </label>
                <textarea name="initial_notes" 
                          id="initial_notes" 
                          class="form-control-modern" 
                          rows="4" 
                          placeholder="Additional notes about the admission..."><?= old('initial_notes') ?></textarea>
            </div>

            <div class="form-group-modern" style="margin-bottom: 24px;">
                <label class="form-label-modern" for="admission_date">
                    <i class="fas fa-calendar"></i> Admission Date <span style="color: #dc2626;">*</span>
                </label>
                <input type="date" 
                       name="admission_date" 
                       id="admission_date" 
                       class="form-control-modern" 
                       value="<?= old('admission_date', date('Y-m-d')) ?>" 
                       required>
                <?php if (session()->getFlashdata('validation') && session()->getFlashdata('validation')->hasError('admission_date')): ?>
                    <div class="text-danger" style="margin-top: 4px;">
                        <?= session()->getFlashdata('validation')->getError('admission_date') ?>
                    </div>
                <?php endif; ?>
            </div>

            <div style="display: flex; gap: 12px; justify-content: flex-end; margin-top: 32px;">
                <a href="<?= site_url('doctor/consultations/my-schedule') ?>" class="btn-modern btn-modern-secondary">
                    <i class="fas fa-times"></i> Cancel
                </a>
                <button type="submit" class="btn-modern btn-modern-primary">
                    <i class="fas fa-hospital"></i> Admit Patient
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function loadBeds(roomId) {
    const bedSelect = document.getElementById('bed_id');
    const bedNumberInput = document.getElementById('bed_number');
    const roomTypeInput = document.getElementById('room_type');
    const roomSelect = document.getElementById('room_id');
    
    // Clear beds
    bedSelect.innerHTML = '<option value="">No specific bed</option>';
    bedNumberInput.value = '';
    
    if (!roomId) {
        roomTypeInput.value = '';
        return;
    }
    
    // Get room type from selected option
    const selectedOption = roomSelect.options[roomSelect.selectedIndex];
    if (selectedOption) {
        roomTypeInput.value = selectedOption.getAttribute('data-room-type') || '';
    }
    
    // Load beds via AJAX
    fetch(`<?= site_url('admission/beds/') ?>${roomId}`, {
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.beds && data.beds.length > 0) {
            bedSelect.innerHTML = '<option value="">No specific bed</option>';
            data.beds.forEach(bed => {
                const option = document.createElement('option');
                option.value = bed.id; // Use bed ID
                option.textContent = `Bed ${bed.bed_number}`;
                option.dataset.bedNumber = bed.bed_number; // Store bed number for later
                bedSelect.appendChild(option);
            });
        }
    })
    .catch(error => {
        console.error('Error loading beds:', error);
    });
}

// Update bed_number hidden field when bed is selected
document.addEventListener('DOMContentLoaded', function() {
    const bedSelect = document.getElementById('bed_id');
    const bedNumberInput = document.getElementById('bed_number');
    
    if (bedSelect) {
        bedSelect.addEventListener('change', function() {
            const selectedOption = this.options[this.selectedIndex];
            if (selectedOption && selectedOption.value) {
                bedNumberInput.value = selectedOption.dataset.bedNumber || '';
            } else {
                bedNumberInput.value = '';
            }
        });
    }
});
</script>

<?= $this->endSection() ?>

