<?= $this->extend('template/header') ?>

<?= $this->section('title') ?>Edit Consultation<?= $this->endSection() ?>

<?= $this->section('content') ?>
<style>
    .doctor-page-container {
        padding: 0;
    }
    
    .page-header {
        background: linear-gradient(135deg, #2e7d32 0%, #4caf50 100%);
        border-radius: 16px;
        padding: 24px 32px;
        margin-bottom: 24px;
        box-shadow: 0 4px 20px rgba(46, 125, 50, 0.2);
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
    .form-select-modern {
        width: 100%;
        padding: 12px 16px;
        border: 2px solid #e5e7eb;
        border-radius: 10px;
        font-size: 15px;
        transition: all 0.3s ease;
        background: white;
    }
    
    .form-control-modern:focus,
    .form-select-modern:focus {
        outline: none;
        border-color: #2e7d32;
        box-shadow: 0 0 0 3px rgba(46, 125, 50, 0.1);
    }
    
    .error-message {
        color: #ef4444;
        font-size: 13px;
        margin-top: 6px;
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
        background: linear-gradient(135deg, #2e7d32 0%, #4caf50 100%);
        color: white;
        box-shadow: 0 4px 12px rgba(46, 125, 50, 0.3);
    }
    
    .btn-modern-secondary {
        background: #64748b;
        color: white;
    }
    
    .btn-modern-info {
        background: #0288d1;
        color: white;
    }
    
    .form-section {
        background: #f8fafc;
        padding: 24px;
        border-radius: 12px;
        margin-bottom: 24px;
    }
    
    .form-section-title {
        font-size: 16px;
        font-weight: 700;
        color: #2e7d32;
        margin-bottom: 20px;
        display: flex;
        align-items: center;
        gap: 10px;
    }
    
    .form-section-title::before {
        content: '';
        width: 4px;
        height: 20px;
        background: #2e7d32;
        border-radius: 2px;
    }
    
    .meta-info {
        background: #f1f5f9;
        padding: 16px;
        border-radius: 10px;
        font-size: 13px;
        color: #64748b;
    }
</style>

<div class="doctor-page-container">
    <div class="page-header">
        <h1>
            <i class="fas fa-edit"></i>
            Edit Consultation
        </h1>
    </div>
    
    <div class="modern-card">
        <div class="card-body-modern">
            <?php if (session()->getFlashdata('error')): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert" style="border-radius: 12px; border-left: 4px solid #ef4444;">
                    <i class="fas fa-exclamation-circle me-2"></i>
                    <?= session()->getFlashdata('error') ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <form action="<?= site_url('doctor/consultations/update/' . $consultation['id']) ?>" method="post">
                <?= csrf_field() ?>
                
                <div class="form-section">
                    <div class="form-section-title">
                        <i class="fas fa-calendar-alt"></i>
                        Consultation Details
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="patient_id" class="form-label-modern">Patient *</label>
                            <select class="form-select-modern" id="patient_id" name="patient_id" required>
                                <option value="">Select Patient</option>
                                <?php if (!empty($patients)): ?>
                                    <?php foreach ($patients as $patient): ?>
                                        <option value="<?= $patient['id'] ?>" 
                                                <?= old('patient_id', $consultation['patient_id']) == $patient['id'] ? 'selected' : '' ?>>
                                            <?= esc(ucfirst($patient['firstname']) . ' ' . ucfirst($patient['lastname'])) ?>
                                        </option>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </select>
                            <?php if (isset($validation) && $validation->getError('patient_id')): ?>
                                <div class="error-message">
                                    <i class="fas fa-exclamation-circle"></i>
                                    <?= $validation->getError('patient_id') ?>
                                </div>
                            <?php endif; ?>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="consultation_date" class="form-label-modern">Consultation Date *</label>
                            <input type="date" class="form-control-modern" id="consultation_date" name="consultation_date" 
                                   value="<?= old('consultation_date', $consultation['consultation_date']) ?>" required>
                            <?php if (isset($validation) && $validation->getError('consultation_date')): ?>
                                <div class="error-message">
                                    <i class="fas fa-exclamation-circle"></i>
                                    <?= $validation->getError('consultation_date') ?>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label for="consultation_time" class="form-label-modern">Consultation Time *</label>
                            <input type="time" class="form-control-modern" id="consultation_time" name="consultation_time" 
                                   value="<?= old('consultation_time', $consultation['consultation_time']) ?>" required>
                            <?php if (isset($validation) && $validation->getError('consultation_time')): ?>
                                <div class="error-message">
                                    <i class="fas fa-exclamation-circle"></i>
                                    <?= $validation->getError('consultation_time') ?>
                                </div>
                            <?php endif; ?>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="type" class="form-label-modern">Consultation Type *</label>
                            <select class="form-select-modern" id="type" name="type" required>
                                <option value="">Select Type</option>
                                <option value="upcoming" <?= old('type', $consultation['type']) == 'upcoming' ? 'selected' : '' ?>>Upcoming</option>
                                <option value="completed" <?= old('type', $consultation['type']) == 'completed' ? 'selected' : '' ?>>Completed</option>
                            </select>
                            <?php if (isset($validation) && $validation->getError('type')): ?>
                                <div class="error-message">
                                    <i class="fas fa-exclamation-circle"></i>
                                    <?= $validation->getError('type') ?>
                                </div>
                            <?php endif; ?>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="status" class="form-label-modern">Status *</label>
                            <select class="form-select-modern" id="status" name="status" required>
                                <option value="">Select Status</option>
                                <option value="pending" <?= old('status', $consultation['status']) == 'pending' ? 'selected' : '' ?>>Pending</option>
                                <option value="approved" <?= old('status', $consultation['status']) == 'approved' ? 'selected' : '' ?>>Approved</option>
                                <option value="cancelled" <?= old('status', $consultation['status']) == 'cancelled' ? 'selected' : '' ?>>Cancelled</option>
                            </select>
                            <?php if (isset($validation) && $validation->getError('status')): ?>
                                <div class="error-message">
                                    <i class="fas fa-exclamation-circle"></i>
                                    <?= $validation->getError('status') ?>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="notes" class="form-label-modern">Consultation Notes</label>
                        <textarea class="form-control-modern" id="notes" name="notes" rows="4" 
                                  placeholder="Enter consultation notes, symptoms, diagnosis, etc." style="resize: vertical;"><?= old('notes', $consultation['notes']) ?></textarea>
                        <?php if (isset($validation) && $validation->getError('notes')): ?>
                            <div class="error-message">
                                <i class="fas fa-exclamation-circle"></i>
                                <?= $validation->getError('notes') ?>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="meta-info mb-4">
                    <div class="row">
                        <div class="col-md-6">
                            <strong>Created:</strong> <?= date('M d, Y h:i A', strtotime($consultation['created_at'])) ?>
                        </div>
                        <div class="col-md-6">
                            <strong>Last Updated:</strong> <?= date('M d, Y h:i A', strtotime($consultation['updated_at'])) ?>
                        </div>
                    </div>
                </div>

                <div class="d-flex justify-content-between flex-wrap gap-3">
                    <a href="<?= site_url('doctor/consultations/my-schedule') ?>" class="btn-modern btn-modern-secondary">
                        <i class="fas fa-arrow-left"></i>
                        Back to Schedule
                    </a>
                    <div style="display: flex; gap: 12px;">
                        <a href="<?= site_url('doctor/patients/view/' . $consultation['patient_id']) ?>" class="btn-modern btn-modern-info">
                            <i class="fas fa-user"></i>
                            View Patient
                        </a>
                        <button type="submit" class="btn-modern btn-modern-primary">
                            <i class="fas fa-save"></i>
                            Update Consultation
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const dateInput = document.getElementById('consultation_date');
    const today = new Date().toISOString().split('T')[0];
    dateInput.setAttribute('min', today);
});
</script>
<?= $this->endSection() ?>
