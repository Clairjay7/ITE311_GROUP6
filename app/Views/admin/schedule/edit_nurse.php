<?php helper('form'); ?>
<?= $this->extend('template/header') ?>

<?= $this->section('title') ?><?= esc($title) ?><?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="admin-module">
    <div class="module-header">
        <h2><?= esc($title) ?></h2>
        <a href="<?= base_url('admin/schedule/view/' . $schedule['nurse_id'] . '?role=nurse') ?>" class="btn btn-secondary">
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

    <div class="schedule-form-container">
        <div class="nurse-info-card">
            <h3>
                <i class="fa-solid fa-user-nurse"></i>
                <?= esc($nurse['username']) ?>
            </h3>
            <p><strong>Email:</strong> <?= esc($nurse['email']) ?></p>
        </div>

        <form method="post" action="<?= base_url('admin/schedule/update/' . $schedule['id'] . '?role=nurse') ?>" class="schedule-form">
            <?= csrf_field() ?>

            <div class="form-section">
                <h3 class="section-title">
                    <span class="section-number">1</span>
                    Schedule Details
                </h3>
                <div class="form-grid">
                    <div class="form-group">
                        <label>Date <span class="required">*</span></label>
                        <input type="date" name="shift_date" value="<?= esc($schedule['shift_date']) ?>" required>
                    </div>
                    <div class="form-group">
                        <label>Shift Type <span class="required">*</span></label>
                        <select name="shift_type" required>
                            <option value="morning" <?= ($schedule['shift_type'] ?? '') === 'morning' ? 'selected' : '' ?>>Morning</option>
                            <option value="night" <?= ($schedule['shift_type'] ?? '') === 'night' ? 'selected' : '' ?>>Night</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Start Time <span class="required">*</span></label>
                        <input type="time" name="start_time" value="<?= esc($schedule['start_time']) ?>" required>
                    </div>
                    <div class="form-group">
                        <label>End Time <span class="required">*</span></label>
                        <input type="time" name="end_time" value="<?= esc($schedule['end_time']) ?>" required>
                    </div>
                    <div class="form-group">
                        <label>Status <span class="required">*</span></label>
                        <select name="status" required>
                            <option value="active" <?= ($schedule['status'] ?? '') === 'active' ? 'selected' : '' ?>>Active</option>
                            <option value="cancelled" <?= ($schedule['status'] ?? '') === 'cancelled' ? 'selected' : '' ?>>Cancelled</option>
                            <option value="on_leave" <?= ($schedule['status'] ?? '') === 'on_leave' ? 'selected' : '' ?>>On Leave</option>
                        </select>
                    </div>
                </div>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn btn-primary">
                    <i class="fa-solid fa-save"></i> Update Schedule
                </button>
                <a href="<?= base_url('admin/schedule/view/' . $schedule['nurse_id'] . '?role=nurse') ?>" class="btn btn-secondary">
                    <i class="fa-solid fa-times"></i> Cancel
                </a>
            </div>
        </form>
    </div>
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

.schedule-form-container {
    max-width: 800px;
    margin: 0 auto;
}

.nurse-info-card {
    background: white;
    border-radius: 12px;
    padding: 24px;
    margin-bottom: 24px;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
    border-left: 4px solid #10b981;
}

.nurse-info-card h3 {
    margin: 0 0 16px 0;
    color: #1e293b;
    font-size: 20px;
    display: flex;
    align-items: center;
    gap: 10px;
}

.nurse-info-card p {
    margin: 8px 0;
    color: #64748b;
}

.schedule-form {
    background: white;
    border-radius: 12px;
    padding: 32px;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
}

.form-section {
    margin-bottom: 32px;
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

.form-group input[type="date"],
.form-group input[type="time"],
.form-group select {
    width: 100%;
    padding: 10px 12px;
    border: 1px solid #d1d5db;
    border-radius: 6px;
    font-size: 14px;
    transition: all 0.3s;
}

.form-group input:focus,
.form-group select:focus {
    outline: none;
    border-color: #2e7d32;
    box-shadow: 0 0 0 3px rgba(46, 125, 50, 0.1);
}

.form-actions {
    display: flex;
    gap: 12px;
    justify-content: flex-end;
    margin-top: 32px;
    padding-top: 24px;
    border-top: 2px solid #e5e7eb;
}
</style>
<?= $this->endSection() ?>


