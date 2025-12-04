<?= $this->extend('template/header') ?>

<?= $this->section('title') ?>Bulk Assign Nurse Schedules<?= $this->endSection() ?>

<?= $this->section('content') ?>
<style>
    .admin-module {
        padding: 24px;
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
        border-radius: 8px;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        font-weight: 600;
        border: none;
        cursor: pointer;
        transition: all 0.3s ease;
    }
    
    .btn-primary {
        background: linear-gradient(135deg, #2e7d32 0%, #4caf50 100%);
        color: white;
        box-shadow: 0 4px 12px rgba(46, 125, 50, 0.3);
    }
    
    .btn-primary:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(46, 125, 50, 0.4);
        color: white;
    }
    
    .btn-secondary {
        background: #6b7280;
        color: white;
    }
    
    .form-container {
        background: white;
        padding: 32px;
        border-radius: 12px;
        max-width: 800px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
    }
    
    .form-group {
        margin-bottom: 24px;
    }
    
    .form-group label {
        display: block;
        margin-bottom: 8px;
        font-weight: 600;
        color: #374151;
        font-size: 14px;
    }
    
    .form-group label .required {
        color: #ef4444;
    }
    
    .form-control {
        width: 100%;
        padding: 12px 16px;
        border: 2px solid #e5e7eb;
        border-radius: 8px;
        font-size: 14px;
        transition: border-color 0.3s;
    }
    
    .form-control:focus {
        outline: none;
        border-color: #2e7d32;
        box-shadow: 0 0 0 3px rgba(46, 125, 50, 0.1);
    }
    
    .form-control.is-invalid {
        border-color: #ef4444;
    }
    
    .invalid-feedback {
        color: #ef4444;
        font-size: 13px;
        margin-top: 6px;
    }
    
    .form-row {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 16px;
    }
    
    .nurse-selection {
        border: 2px solid #e5e7eb;
        border-radius: 12px;
        padding: 20px;
        max-height: 300px;
        overflow-y: auto;
        background: #f8fafc;
    }
    
    .nurse-checkbox {
        display: flex;
        align-items: center;
        padding: 12px;
        margin-bottom: 8px;
        background: white;
        border-radius: 8px;
        cursor: pointer;
        transition: all 0.2s;
    }
    
    .nurse-checkbox:hover {
        background: #e8f5e9;
        transform: translateX(4px);
    }
    
    .nurse-checkbox input[type="checkbox"] {
        margin-right: 12px;
        width: 18px;
        height: 18px;
        cursor: pointer;
    }
    
    .nurse-checkbox label {
        margin: 0;
        cursor: pointer;
        flex: 1;
        font-weight: 500;
    }
    
    .shift-type-options {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 16px;
        margin-top: 8px;
    }
    
    .shift-type-card {
        border: 2px solid #e5e7eb;
        border-radius: 12px;
        padding: 20px;
        cursor: pointer;
        transition: all 0.3s ease;
        text-align: center;
    }
    
    .shift-type-card:hover {
        border-color: #2e7d32;
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(46, 125, 50, 0.2);
    }
    
    .shift-type-card.selected {
        border-color: #2e7d32;
        background: linear-gradient(135deg, #e8f5e9 0%, #f1f8f4 100%);
    }
    
    .shift-type-card.morning.selected {
        background: linear-gradient(135deg, #fef3c7 0%, #fde68a 100%);
        border-color: #f59e0b;
    }
    
    .shift-type-card.night.selected {
        background: linear-gradient(135deg, #1e293b 0%, #334155 100%);
        border-color: #475569;
        color: white;
    }
    
    .shift-type-card.both.selected {
        background: linear-gradient(135deg, #ddd6fe 0%, #c4b5fd 100%);
        border-color: #7c3aed;
    }
    
    .shift-type-card i {
        font-size: 32px;
        margin-bottom: 8px;
        display: block;
    }
    
    .shift-type-card h4 {
        margin: 0 0 4px;
        font-size: 16px;
        font-weight: 700;
    }
    
    .shift-type-card p {
        margin: 0;
        font-size: 12px;
        opacity: 0.8;
    }
    
    .info-box {
        background: linear-gradient(135deg, #dbeafe 0%, #bfdbfe 100%);
        border-left: 4px solid #3b82f6;
        padding: 16px;
        border-radius: 8px;
        margin-bottom: 24px;
    }
    
    .info-box i {
        color: #3b82f6;
        margin-right: 8px;
    }
    
    .form-actions {
        display: flex;
        gap: 12px;
        margin-top: 32px;
        padding-top: 24px;
        border-top: 2px solid #e5e7eb;
    }
    
    .alert {
        padding: 12px 16px;
        border-radius: 8px;
        margin-bottom: 16px;
    }
    
    .alert-error {
        background: #fee2e2;
        color: #991b1b;
        border-left: 4px solid #ef4444;
    }
</style>

<div class="admin-module">
    <div class="module-header">
        <h2><i class="fas fa-users"></i> Bulk Assign Nurse Schedules</h2>
        <a href="<?= base_url('admin/nurse-schedules') ?>" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Back to List
        </a>
    </div>

    <?php 
    $errors = session()->getFlashdata('errors') ?? [];
    $error = session()->getFlashdata('error');
    ?>

    <?php if ($error): ?>
        <div class="alert alert-error">
            <i class="fas fa-exclamation-circle"></i> <?= esc($error) ?>
        </div>
    <?php endif; ?>

    <div class="info-box">
        <i class="fas fa-info-circle"></i>
        <strong>Bulk Assignment:</strong> Pumili ng mga nurse at date range. Ang system ay mag-a-assign ng schedules para sa lahat ng working days (Monday-Friday) sa selected date range. Ang weekends ay automatic na skip.
    </div>

    <form method="POST" action="<?= base_url('admin/nurse-schedules/bulk-assign-store') ?>" class="form-container" id="bulkAssignForm">
        <div class="form-row">
            <div class="form-group">
                <label for="start_date">Start Date <span class="required">*</span></label>
                <input type="date" id="start_date" name="start_date" 
                       class="form-control <?= isset($errors['start_date']) ? 'is-invalid' : '' ?>" 
                       value="<?= old('start_date', date('Y-m-d')) ?>" 
                       min="<?= date('Y-m-d') ?>" required>
                <?php if (isset($errors['start_date'])): ?>
                    <div class="invalid-feedback"><?= esc($errors['start_date']) ?></div>
                <?php endif; ?>
            </div>

            <div class="form-group">
                <label for="end_date">End Date <span class="required">*</span></label>
                <input type="date" id="end_date" name="end_date" 
                       class="form-control <?= isset($errors['end_date']) ? 'is-invalid' : '' ?>" 
                       value="<?= old('end_date', date('Y-m-d', strtotime('+7 days'))) ?>" 
                       min="<?= date('Y-m-d') ?>" required>
                <?php if (isset($errors['end_date'])): ?>
                    <div class="invalid-feedback"><?= esc($errors['end_date']) ?></div>
                <?php endif; ?>
            </div>
        </div>

        <div class="form-group">
            <label>Shift Type <span class="required">*</span></label>
            <div class="shift-type-options">
                <div class="shift-type-card morning" data-type="morning" onclick="selectShiftType('morning')">
                    <i class="fas fa-sun" style="color: #f59e0b;"></i>
                    <h4>Morning Only</h4>
                    <p>6:00 AM - 12:00 PM</p>
                </div>
                <div class="shift-type-card night" data-type="night" onclick="selectShiftType('night')">
                    <i class="fas fa-moon" style="color: #cbd5e1;"></i>
                    <h4>Night Only</h4>
                    <p>6:00 PM - 12:00 AM</p>
                </div>
                <div class="shift-type-card both" data-type="both" onclick="selectShiftType('both')">
                    <i class="fas fa-calendar-day" style="color: #7c3aed;"></i>
                    <h4>Both Shifts</h4>
                    <p>Morning + Night</p>
                </div>
            </div>
            <input type="hidden" id="shift_type" name="shift_type" value="<?= old('shift_type', 'both') ?>" required>
            <?php if (isset($errors['shift_type'])): ?>
                <div class="invalid-feedback"><?= esc($errors['shift_type']) ?></div>
            <?php endif; ?>
        </div>

        <div class="form-group">
            <label>Select Nurses <span class="required">*</span></label>
            <div class="nurse-selection">
                <div style="margin-bottom: 12px; padding-bottom: 12px; border-bottom: 2px solid #e5e7eb;">
                    <button type="button" onclick="selectAll()" class="btn btn-sm" style="background: #3b82f6; color: white; padding: 6px 12px; font-size: 12px;">
                        <i class="fas fa-check-double"></i> Select All
                    </button>
                    <button type="button" onclick="deselectAll()" class="btn btn-sm" style="background: #6b7280; color: white; padding: 6px 12px; font-size: 12px; margin-left: 8px;">
                        <i class="fas fa-times"></i> Deselect All
                    </button>
                </div>
                <?php if (empty($nurses)): ?>
                    <p style="text-align: center; color: #94a3b8; padding: 20px;">
                        <i class="fas fa-user-nurse" style="font-size: 48px; display: block; margin-bottom: 12px; opacity: 0.3;"></i>
                        Walang available na nurses. Mag-create muna ng nurse users.
                    </p>
                <?php else: ?>
                    <?php foreach ($nurses as $nurse): ?>
                        <div class="nurse-checkbox">
                            <input type="checkbox" id="nurse_<?= esc($nurse['id']) ?>" 
                                   name="nurse_ids[]" value="<?= esc($nurse['id']) ?>"
                                   <?= old('nurse_ids') && in_array($nurse['id'], old('nurse_ids')) ? 'checked' : '' ?>>
                            <label for="nurse_<?= esc($nurse['id']) ?>">
                                <strong><?= esc($nurse['username']) ?></strong>
                                <span style="color: #64748b; font-size: 12px; margin-left: 8px;"><?= esc($nurse['email']) ?></span>
                            </label>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
            <?php if (isset($errors['nurse_ids'])): ?>
                <div class="invalid-feedback"><?= esc($errors['nurse_ids']) ?></div>
            <?php endif; ?>
        </div>

        <div class="form-actions">
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-save"></i> Assign Schedules
            </button>
            <a href="<?= base_url('admin/nurse-schedules') ?>" class="btn btn-secondary">
                <i class="fas fa-times"></i> Cancel
            </a>
        </div>
    </form>
</div>

<script>
    // Select shift type
    function selectShiftType(type) {
        document.getElementById('shift_type').value = type;
        
        // Update card selection
        document.querySelectorAll('.shift-type-card').forEach(card => {
            card.classList.remove('selected');
        });
        document.querySelector(`.shift-type-card[data-type="${type}"]`).classList.add('selected');
    }
    
    // Select all nurses
    function selectAll() {
        document.querySelectorAll('input[name="nurse_ids[]"]').forEach(checkbox => {
            checkbox.checked = true;
        });
    }
    
    // Deselect all nurses
    function deselectAll() {
        document.querySelectorAll('input[name="nurse_ids[]"]').forEach(checkbox => {
            checkbox.checked = false;
        });
    }
    
    // Initialize shift type selection
    const currentShiftType = document.getElementById('shift_type').value;
    if (currentShiftType) {
        selectShiftType(currentShiftType);
    }
    
    // Form validation
    document.getElementById('bulkAssignForm').addEventListener('submit', function(e) {
        const checkedNurses = document.querySelectorAll('input[name="nurse_ids[]"]:checked');
        if (checkedNurses.length === 0) {
            e.preventDefault();
            alert('Pumili ng kahit isang nurse.');
            return false;
        }
        
        const startDate = document.getElementById('start_date').value;
        const endDate = document.getElementById('end_date').value;
        
        if (new Date(endDate) < new Date(startDate)) {
            e.preventDefault();
            alert('Ang end date ay dapat mas malaki kaysa sa start date.');
            return false;
        }
    });
</script>

<?= $this->endSection() ?>

