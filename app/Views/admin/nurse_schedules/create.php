<?= $this->extend('template/header') ?>

<?= $this->section('title') ?>Create Nurse Schedule<?= $this->endSection() ?>

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
        max-width: 700px;
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
    
    .shift-type-options {
        display: grid;
        grid-template-columns: 1fr 1fr;
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
    
    .time-preview {
        background: #f8fafc;
        padding: 12px;
        border-radius: 8px;
        margin-top: 8px;
        font-size: 13px;
        color: #64748b;
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
        <h2><i class="fas fa-user-nurse"></i> Create Nurse Schedule</h2>
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

    <form method="POST" action="<?= base_url('admin/nurse-schedules/store') ?>" class="form-container" id="scheduleForm">
        <div class="form-group">
            <label for="nurse_id">Nurse <span class="required">*</span></label>
            <select id="nurse_id" name="nurse_id" class="form-control <?= isset($errors['nurse_id']) ? 'is-invalid' : '' ?>" required>
                <option value="">Select Nurse</option>
                <?php foreach ($nurses as $nurse): ?>
                    <option value="<?= esc($nurse['id']) ?>" <?= old('nurse_id') == $nurse['id'] ? 'selected' : '' ?>>
                        <?= esc($nurse['username']) ?> (<?= esc($nurse['email']) ?>)
                    </option>
                <?php endforeach; ?>
            </select>
            <?php if (isset($errors['nurse_id'])): ?>
                <div class="invalid-feedback"><?= esc($errors['nurse_id']) ?></div>
            <?php endif; ?>
        </div>

        <div class="form-group">
            <label for="shift_date">Shift Date <span class="required">*</span></label>
            <input type="date" id="shift_date" name="shift_date" 
                   class="form-control <?= isset($errors['shift_date']) ? 'is-invalid' : '' ?>" 
                   value="<?= old('shift_date', date('Y-m-d')) ?>" 
                   min="<?= date('Y-m-d') ?>" required>
            <?php if (isset($errors['shift_date'])): ?>
                <div class="invalid-feedback"><?= esc($errors['shift_date']) ?></div>
            <?php endif; ?>
        </div>

        <div class="form-group">
            <label>Shift Type <span class="required">*</span></label>
            <div class="shift-type-options">
                <div class="shift-type-card morning" data-type="morning" onclick="selectShiftType('morning')">
                    <i class="fas fa-sun" style="color: #f59e0b;"></i>
                    <h4>Morning Shift</h4>
                    <p>6:00 AM - 12:00 PM</p>
                </div>
                <div class="shift-type-card night" data-type="night" onclick="selectShiftType('night')">
                    <i class="fas fa-moon" style="color: #cbd5e1;"></i>
                    <h4>Night Shift</h4>
                    <p>6:00 PM - 12:00 AM</p>
                </div>
            </div>
            <input type="hidden" id="shift_type" name="shift_type" value="<?= old('shift_type', '') ?>" required>
            <?php if (isset($errors['shift_type'])): ?>
                <div class="invalid-feedback"><?= esc($errors['shift_type']) ?></div>
            <?php endif; ?>
        </div>

        <div class="form-row">
            <div class="form-group">
                <label for="start_time">Start Time <span class="required">*</span></label>
                <input type="time" id="start_time" name="start_time" 
                       class="form-control <?= isset($errors['start_time']) ? 'is-invalid' : '' ?>" 
                       value="<?= old('start_time', '06:00') ?>" required>
                <?php if (isset($errors['start_time'])): ?>
                    <div class="invalid-feedback"><?= esc($errors['start_time']) ?></div>
                <?php endif; ?>
            </div>

            <div class="form-group">
                <label for="end_time">End Time <span class="required">*</span></label>
                <input type="time" id="end_time" name="end_time" 
                       class="form-control <?= isset($errors['end_time']) ? 'is-invalid' : '' ?>" 
                       value="<?= old('end_time', '12:00') ?>" required>
                <?php if (isset($errors['end_time'])): ?>
                    <div class="invalid-feedback"><?= esc($errors['end_time']) ?></div>
                <?php endif; ?>
            </div>
        </div>

        <div class="time-preview" id="durationPreview">
            <strong>Duration:</strong> <span id="durationText">6 hours</span>
        </div>

        <div class="form-group">
            <label for="status">Status</label>
            <select id="status" name="status" class="form-control">
                <option value="active" <?= old('status', 'active') === 'active' ? 'selected' : '' ?>>Active</option>
                <option value="cancelled" <?= old('status') === 'cancelled' ? 'selected' : '' ?>>Cancelled</option>
                <option value="on_leave" <?= old('status') === 'on_leave' ? 'selected' : '' ?>>On Leave</option>
            </select>
        </div>

        <div class="form-actions">
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-save"></i> Create Schedule
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
        
        // Auto-set times based on shift type
        if (type === 'morning') {
            document.getElementById('start_time').value = '06:00';
            document.getElementById('end_time').value = '12:00';
        } else if (type === 'night') {
            document.getElementById('start_time').value = '18:00';
            document.getElementById('end_time').value = '00:00';
        }
        
        updateDuration();
    }
    
    // Calculate and display duration
    function updateDuration() {
        const startTime = document.getElementById('start_time').value;
        const endTime = document.getElementById('end_time').value;
        
        if (startTime && endTime) {
            const start = new Date('2000-01-01T' + startTime + ':00');
            let end = new Date('2000-01-01T' + endTime + ':00');
            
            // Handle next day for night shift
            if (endTime < startTime) {
                end = new Date('2000-01-02T' + endTime + ':00');
            }
            
            const diffMs = end - start;
            const diffHours = diffMs / (1000 * 60 * 60);
            
            document.getElementById('durationText').textContent = diffHours + ' hours';
            
            // Validate 6-hour duration
            if (diffHours !== 6) {
                document.getElementById('durationPreview').style.background = '#fee2e2';
                document.getElementById('durationPreview').style.color = '#991b1b';
            } else {
                document.getElementById('durationPreview').style.background = '#d1fae5';
                document.getElementById('durationPreview').style.color = '#065f46';
            }
        }
    }
    
    // Update duration when times change
    document.getElementById('start_time').addEventListener('change', updateDuration);
    document.getElementById('end_time').addEventListener('change', updateDuration);
    
    // Initialize shift type selection if old value exists
    <?php if (old('shift_type')): ?>
        selectShiftType('<?= old('shift_type') ?>');
    <?php endif; ?>
    
    // Form validation
    document.getElementById('scheduleForm').addEventListener('submit', function(e) {
        const startTime = document.getElementById('start_time').value;
        const endTime = document.getElementById('end_time').value;
        
        if (startTime && endTime) {
            const start = new Date('2000-01-01T' + startTime + ':00');
            let end = new Date('2000-01-01T' + endTime + ':00');
            
            if (endTime < startTime) {
                end = new Date('2000-01-02T' + endTime + ':00');
            }
            
            const diffMs = end - start;
            const diffHours = diffMs / (1000 * 60 * 60);
            
            if (diffHours !== 6) {
                e.preventDefault();
                alert('Ang shift ay dapat eksaktong 6 na oras. Pakitiyak na ang duration ay 6 hours.');
                return false;
            }
        }
    });
</script>

<?= $this->endSection() ?>

