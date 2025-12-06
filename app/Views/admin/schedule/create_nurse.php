<?php helper('form'); ?>
<?= $this->extend('template/header') ?>

<?= $this->section('title') ?><?= esc($title) ?><?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="admin-module">
    <div class="module-header">
        <h2><?= esc($title) ?></h2>
        <a href="<?= base_url('admin/schedule/create') ?>" class="btn btn-secondary">
            <i class="fa-solid fa-arrow-left"></i> Back
        </a>
    </div>

    <?php if (session()->getFlashdata('error')): ?>
        <div class="alert alert-error"><?= session()->getFlashdata('error') ?></div>
    <?php endif; ?>

    <?php if (isset($validation) && !empty($validation->getErrors())): ?>
        <div class="alert alert-error">
            <ul style="margin: 0; padding-left: 20px;">
                <?php foreach ($validation->getErrors() as $error): ?>
                    <li><?= esc($error) ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <div class="alert alert-info" style="background: #dbeafe; color: #1e40af; border-left: 4px solid #3b82f6; padding: 16px; border-radius: 8px; margin-bottom: 24px;">
        <i class="fa-solid fa-info-circle"></i> 
        <strong>Schedule Duration:</strong> Schedules will be automatically generated for <strong>1 year</strong> starting from today (<?= date('M d, Y') ?>) until <?= date('M d, Y', strtotime('+1 year')) ?>.
    </div>

    <form method="post" action="<?= base_url('admin/schedule/store-nurse') ?>" class="schedule-form">
        <?= csrf_field() ?>

        <!-- 1. Nurse Information -->
        <div class="form-section">
            <h3 class="section-title">
                <span class="section-number">1</span>
                Nurse Information
            </h3>
            <div class="form-grid">
                <div class="form-group">
                    <label>Nurse Name <span class="required">*</span></label>
                    <select name="nurse_id" id="nurse_id" required>
                        <option value="">-- Select Nurse --</option>
                        <?php foreach ($nurses as $nurse): ?>
                            <option value="<?= esc($nurse['id']) ?>">
                                <?= esc($nurse['username']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
        </div>

        <!-- 2. Working Days -->
        <div class="form-section">
            <h3 class="section-title">
                <span class="section-number">2</span>
                Working Days
            </h3>
            <div class="form-group">
                <label>Days of Work <span class="required">*</span></label>
                <div class="checkbox-group">
                    <label class="checkbox-label">
                        <input type="checkbox" name="working_days[]" value="Monday">
                        <span>Monday</span>
                    </label>
                    <label class="checkbox-label">
                        <input type="checkbox" name="working_days[]" value="Tuesday">
                        <span>Tuesday</span>
                    </label>
                    <label class="checkbox-label">
                        <input type="checkbox" name="working_days[]" value="Wednesday">
                        <span>Wednesday</span>
                    </label>
                    <label class="checkbox-label">
                        <input type="checkbox" name="working_days[]" value="Thursday">
                        <span>Thursday</span>
                    </label>
                    <label class="checkbox-label">
                        <input type="checkbox" name="working_days[]" value="Friday">
                        <span>Friday</span>
                    </label>
                    <label class="checkbox-label">
                        <input type="checkbox" name="working_days[]" value="Saturday">
                        <span>Saturday</span>
                    </label>
                    <label class="checkbox-label">
                        <input type="checkbox" name="working_days[]" value="Sunday">
                        <span>Sunday</span>
                    </label>
                </div>
            </div>
        </div>

        <!-- 3. Shift -->
        <div class="form-section">
            <h3 class="section-title">
                <span class="section-number">3</span>
                Shift
            </h3>
            <div class="form-group">
                <label>Shift Type <span class="required">*</span></label>
                <select name="shift_type" id="shift_type" required onchange="setShiftTimes()">
                    <option value="">-- Select Shift Type --</option>
                    <option value="morning">AM Shift</option>
                    <option value="pm">PM Shift</option>
                    <option value="night">Night Shift</option>
                    <option value="whole_day">Whole Day</option>
                </select>
            </div>
        </div>

        <!-- 4. Time Schedule -->
        <div class="form-section">
            <h3 class="section-title">
                <span class="section-number">4</span>
                Time Schedule
            </h3>
            <div class="form-grid">
                <div class="form-group">
                    <label>Time In <span class="required">*</span></label>
                    <input type="time" name="time_in" id="time_in" required>
                </div>
                <div class="form-group">
                    <label>Time Out <span class="required">*</span></label>
                    <input type="time" name="time_out" id="time_out" required>
                </div>
                <div class="form-group">
                    <label>Break Time (Optional)</label>
                    <input type="text" name="break_time" placeholder="e.g., 12:00 PM - 1:00 PM">
                </div>
            </div>
        </div>

        <!-- 5. Duty Type -->
        <div class="form-section">
            <h3 class="section-title">
                <span class="section-number">5</span>
                Duty Type
            </h3>
            <div class="form-grid">
                <div class="form-group">
                    <label>Duty Type <span class="required">*</span></label>
                    <select name="duty_type" id="duty_type" required onchange="toggleStationAssignment()">
                        <option value="regular">Regular Duty</option>
                        <option value="float">Float Nurse</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Standby / On-Call <span class="required">*</span></label>
                    <div class="radio-group">
                        <label class="radio-label">
                            <input type="radio" name="standby" value="yes" required>
                            <span>Yes</span>
                        </label>
                        <label class="radio-label">
                            <input type="radio" name="standby" value="no" checked>
                            <span>No</span>
                        </label>
                    </div>
                </div>
            </div>
        </div>

        <!-- 6. Station Assignment -->
        <div class="form-section" id="stationAssignmentSection">
            <h3 class="section-title">
                <span class="section-number">6</span>
                Station Assignment
            </h3>
            <div class="form-group">
                <label>Station / Ward Assigned <span class="required" id="stationRequired">*</span></label>
                <select name="station_assignment" id="station_assignment">
                    <option value="">-- Select Station / Ward --</option>
                    <option value="Private">Private</option>
                    <option value="Semi-Private">Semi-Private</option>
                    <option value="Ward">Ward</option>
                    <option value="ICU">ICU</option>
                    <option value="Isolation">Isolation</option>
                    <option value="NICU">NICU</option>
                </select>
            </div>
        </div>

        <!-- 7. Status -->
        <div class="form-section">
            <h3 class="section-title">
                <span class="section-number">7</span>
                Status
            </h3>
            <div class="form-group">
                <label>Status <span class="required">*</span></label>
                <div class="radio-group">
                    <label class="radio-label">
                        <input type="radio" name="status" value="active" checked required>
                        <span>Active</span>
                    </label>
                    <label class="radio-label">
                        <input type="radio" name="status" value="inactive">
                        <span>Inactive</span>
                    </label>
                </div>
            </div>
        </div>

        <!-- 8. Submit -->
        <div class="form-actions">
            <button type="submit" class="btn btn-primary">
                <i class="fa-solid fa-save"></i> Save Nurse Schedule
            </button>
            <a href="<?= base_url('admin/schedule/create') ?>" class="btn btn-secondary">
                <i class="fa-solid fa-times"></i> Cancel
            </a>
        </div>
    </form>
</div>

<style>
.admin-module { 
    padding: 24px; 
    background: #f8fafc;
    min-height: 100vh;
}

.module-header { 
    display: flex; 
    justify-content: space-between; 
    align-items: center; 
    margin-bottom: 24px;
}

.module-header h2 { 
    margin: 0; 
    color: #2e7d32; 
    font-size: 28px;
}

.btn { 
    padding: 10px 20px; 
    border-radius: 6px; 
    text-decoration: none; 
    display: inline-flex;
    align-items: center;
    gap: 8px;
    font-weight: 600;
    transition: all 0.3s;
    border: none;
    cursor: pointer;
    font-size: 14px;
}

.btn-primary { 
    background: #2e7d32; 
    color: white; 
}

.btn-primary:hover {
    background: #1e5a22;
}

.btn-secondary {
    background: #6b7280;
    color: white;
}

.btn-secondary:hover {
    background: #4b5563;
}

.alert { 
    padding: 12px 16px; 
    border-radius: 6px; 
    margin-bottom: 16px; 
    font-weight: 500;
}

.alert-error { 
    background: #fee2e2; 
    color: #b91c1c; 
    border-left: 4px solid #ef4444;
}

.schedule-form {
    background: white;
    border-radius: 12px;
    padding: 32px;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
    max-width: 900px;
    margin: 0 auto;
}

.form-section {
    margin-bottom: 32px;
    padding-bottom: 24px;
    border-bottom: 2px solid #e5e7eb;
}

.form-section:last-of-type {
    border-bottom: none;
}

.section-title {
    display: flex;
    align-items: center;
    gap: 12px;
    margin: 0 0 20px 0;
    color: #2e7d32;
    font-size: 20px;
    font-weight: 700;
}

.section-number {
    background: #2e7d32;
    color: white;
    width: 32px;
    height: 32px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 16px;
    font-weight: 700;
}

.form-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 20px;
}

.form-group {
    margin-bottom: 20px;
}

.form-group label {
    display: block;
    margin-bottom: 8px;
    font-weight: 600;
    color: #1e293b;
    font-size: 14px;
}

.required {
    color: #ef4444;
}

.form-group input[type="text"],
.form-group input[type="time"],
.form-group select,
.form-group textarea {
    width: 100%;
    padding: 10px 12px;
    border: 1px solid #d1d5db;
    border-radius: 6px;
    font-size: 14px;
    transition: all 0.3s;
}

.form-group input:focus,
.form-group select:focus,
.form-group textarea:focus {
    outline: none;
    border-color: #2e7d32;
    box-shadow: 0 0 0 3px rgba(46, 125, 50, 0.1);
}

.checkbox-group {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(120px, 1fr));
    gap: 12px;
}

.checkbox-label {
    display: flex;
    align-items: center;
    gap: 8px;
    cursor: pointer;
    padding: 10px;
    border: 1px solid #e5e7eb;
    border-radius: 6px;
    transition: all 0.3s;
}

.checkbox-label:hover {
    background: #f8fafc;
    border-color: #2e7d32;
}

.checkbox-label input[type="checkbox"] {
    width: auto;
    cursor: pointer;
}

.radio-group {
    display: flex;
    gap: 24px;
}

.radio-label {
    display: flex;
    align-items: center;
    gap: 8px;
    cursor: pointer;
}

.radio-label input[type="radio"] {
    width: auto;
    cursor: pointer;
}

.form-actions {
    display: flex;
    gap: 12px;
    justify-content: flex-end;
    margin-top: 32px;
    padding-top: 24px;
    border-top: 2px solid #e5e7eb;
}

@media (max-width: 768px) {
    .schedule-form {
        padding: 20px;
    }
    
    .form-grid {
        grid-template-columns: 1fr;
    }
    
    .checkbox-group {
        grid-template-columns: 1fr;
    }
}
</style>

<script>
function setShiftTimes() {
    const shiftType = document.getElementById('shift_type').value;
    const timeIn = document.getElementById('time_in');
    const timeOut = document.getElementById('time_out');
    
    // Set default times based on shift type
    switch(shiftType) {
        case 'morning':
            // AM Shift: 6:00 AM - 2:00 PM
            timeIn.value = '06:00';
            timeOut.value = '14:00';
            break;
        case 'pm':
            // PM Shift: 2:00 PM - 10:00 PM
            timeIn.value = '14:00';
            timeOut.value = '22:00';
            break;
        case 'night':
            // Night Shift: 10:00 PM - 6:00 AM
            timeIn.value = '22:00';
            timeOut.value = '06:00';
            break;
        case 'whole_day':
            // Whole Day: 8:00 AM - 5:00 PM
            timeIn.value = '08:00';
            timeOut.value = '17:00';
            break;
        default:
            // Clear times if no shift type selected
            timeIn.value = '';
            timeOut.value = '';
    }
}

function toggleStationAssignment() {
    const dutyType = document.getElementById('duty_type').value;
    const stationSection = document.getElementById('stationAssignmentSection');
    const stationSelect = document.getElementById('station_assignment');
    const stationRequired = document.getElementById('stationRequired');
    
    if (dutyType === 'float') {
        // Hide station assignment for Float Nurse
        stationSection.style.display = 'none';
        stationSelect.removeAttribute('required');
        stationSelect.value = ''; // Clear the value
        stationRequired.style.display = 'none';
    } else {
        // Show station assignment for Regular Duty
        stationSection.style.display = 'block';
        stationSelect.setAttribute('required', 'required');
        stationRequired.style.display = 'inline';
    }
}

// Initialize on page load
document.addEventListener('DOMContentLoaded', function() {
    toggleStationAssignment();
});
</script>
<?= $this->endSection() ?>

