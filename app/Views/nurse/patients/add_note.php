<?= $this->extend('template/header') ?>

<?= $this->section('title') ?>Add Nurse Note<?= $this->endSection() ?>

<?= $this->section('content') ?>
<style>
    .nurse-page-container {
        padding: 0;
    }
    
    .page-header {
        background: linear-gradient(135deg, #0288d1 0%, #03a9f4 100%);
        border-radius: 16px;
        padding: 24px 32px;
        margin-bottom: 24px;
        box-shadow: 0 4px 20px rgba(2, 136, 209, 0.2);
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
    
    .form-group {
        margin-bottom: 24px;
    }
    
    .form-label {
        font-weight: 600;
        color: #1e293b;
        margin-bottom: 8px;
        display: block;
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
        border-color: #0288d1;
        box-shadow: 0 0 0 3px rgba(2, 136, 209, 0.1);
    }
    
    .btn-modern {
        padding: 12px 24px;
        border-radius: 10px;
        font-weight: 600;
        font-size: 14px;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        transition: all 0.3s ease;
        border: none;
        text-decoration: none;
        cursor: pointer;
    }
    
    .btn-modern-primary {
        background: linear-gradient(135deg, #0288d1 0%, #03a9f4 100%);
        color: white;
        box-shadow: 0 4px 12px rgba(2, 136, 209, 0.3);
    }
    
    .btn-modern-secondary {
        background: #64748b;
        color: white;
    }
    
    .text-danger {
        color: #ef4444;
        font-size: 13px;
        margin-top: 4px;
    }
</style>

<div class="nurse-page-container">
    <div class="page-header">
        <h1>
            <i class="fas fa-sticky-note"></i>
            Add Nurse Note - <?= esc(ucfirst($patient['firstname']) . ' ' . ucfirst($patient['lastname'])) ?>
        </h1>
    </div>
    
    <div class="modern-card">
        <div class="card-body-modern">
            <form action="<?= site_url('nurse/patients/store-note/' . $patient['id']) ?>" method="post">
                <?= csrf_field() ?>
                
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="note_type" class="form-label">Note Type *</label>
                            <select class="form-select" id="note_type" name="note_type" required>
                                <option value="">Select Type</option>
                                <option value="progress" <?= old('note_type') == 'progress' ? 'selected' : '' ?>>Progress</option>
                                <option value="observation" <?= old('note_type') == 'observation' ? 'selected' : '' ?>>Observation</option>
                                <option value="medication" <?= old('note_type') == 'medication' ? 'selected' : '' ?>>Medication</option>
                                <option value="incident" <?= old('note_type') == 'incident' ? 'selected' : '' ?>>Incident</option>
                                <option value="other" <?= old('note_type') == 'other' ? 'selected' : '' ?>>Other</option>
                            </select>
                            <?php if (isset($validation) && $validation->getError('note_type')): ?>
                                <div class="text-danger"><?= $validation->getError('note_type') ?></div>
                            <?php endif; ?>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="priority" class="form-label">Priority *</label>
                            <select class="form-select" id="priority" name="priority" required>
                                <option value="">Select Priority</option>
                                <option value="low" <?= old('priority') == 'low' ? 'selected' : '' ?>>Low</option>
                                <option value="normal" <?= old('priority') == 'normal' ? 'selected' : '' ?>>Normal</option>
                                <option value="high" <?= old('priority') == 'high' ? 'selected' : '' ?>>High</option>
                                <option value="urgent" <?= old('priority') == 'urgent' ? 'selected' : '' ?>>Urgent</option>
                            </select>
                            <?php if (isset($validation) && $validation->getError('priority')): ?>
                                <div class="text-danger"><?= $validation->getError('priority') ?></div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                
                <div class="form-group">
                    <label for="note" class="form-label">Note *</label>
                    <textarea class="form-control" id="note" name="note" rows="8" required placeholder="Enter your note here..."><?= old('note') ?></textarea>
                    <?php if (isset($validation) && $validation->getError('note')): ?>
                        <div class="text-danger"><?= $validation->getError('note') ?></div>
                    <?php endif; ?>
                </div>
                
                <div style="display: flex; gap: 12px; justify-content: flex-end; margin-top: 32px;">
                    <a href="<?= site_url('nurse/patients/details/' . $patient['id']) ?>" class="btn-modern btn-modern-secondary">
                        <i class="fas fa-times"></i>
                        Cancel
                    </a>
                    <button type="submit" class="btn-modern btn-modern-primary">
                        <i class="fas fa-save"></i>
                        Save Note
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
<?= $this->endSection() ?>

