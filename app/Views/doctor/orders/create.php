<?= $this->extend('template/header') ?>

<?= $this->section('title') ?>Create Medical Order<?= $this->endSection() ?>

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
        padding: 32px;
        margin-bottom: 24px;
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
        transition: all 0.3s ease;
        background: white;
    }
    
    .form-control-modern:focus {
        outline: none;
        border-color: #2e7d32;
        box-shadow: 0 0 0 3px rgba(46, 125, 50, 0.1);
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
        background: linear-gradient(135deg, #2e7d32 0%, #4caf50 100%);
        color: white;
        box-shadow: 0 4px 12px rgba(46, 125, 50, 0.3);
    }
    
    .btn-modern-primary:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(46, 125, 50, 0.4);
        color: white;
    }
    
    .btn-modern-secondary {
        background: #f1f5f9;
        color: #475569;
    }
    
    .btn-modern-secondary:hover {
        background: #e2e8f0;
        color: #475569;
    }
    
    .text-danger {
        color: #ef4444;
        font-size: 13px;
        margin-top: 4px;
    }
    
    .alert-modern {
        border-radius: 12px;
        padding: 16px 20px;
        margin-bottom: 24px;
        border: none;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
    }
    
    .alert-modern-danger {
        background: linear-gradient(135deg, #fee2e2 0%, #fecaca 100%);
        color: #991b1b;
        border-left: 4px solid #ef4444;
    }
</style>

<div class="doctor-page-container">
    <div class="page-header">
        <h1>
            <i class="fas fa-prescription"></i>
            Create Medical Order
        </h1>
    </div>
    
    <?php if (session()->getFlashdata('error')): ?>
        <div class="alert-modern alert-modern-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-circle me-2"></i>
            <?= session()->getFlashdata('error') ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>
    
    <?php if (session()->getFlashdata('errors')): ?>
        <div class="alert-modern alert-modern-danger">
            <i class="fas fa-exclamation-circle me-2"></i>
            Please fix the following errors:
            <ul class="mb-0 mt-2">
                <?php foreach (session()->getFlashdata('errors') as $error): ?>
                    <li><?= esc($error) ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <div class="modern-card">
        <form action="<?= site_url('doctor/orders/store') ?>" method="post">
            <?= csrf_field() ?>
            
            <div class="form-group-modern">
                <label class="form-label-modern" for="patient_id">
                    <i class="fas fa-user-injured me-2"></i>
                    Patient <span class="text-danger">*</span>
                </label>
                <select name="patient_id" id="patient_id" class="form-control-modern" required>
                    <option value="">Select Patient</option>
                    <?php foreach ($patients as $patient): ?>
                        <option value="<?= esc($patient['id']) ?>" <?= old('patient_id') == $patient['id'] ? 'selected' : '' ?>>
                            <?= esc(ucfirst($patient['firstname']) . ' ' . ucfirst($patient['lastname'])) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <?php if (session()->getFlashdata('errors') && isset(session()->getFlashdata('errors')['patient_id'])): ?>
                    <div class="text-danger"><?= esc(session()->getFlashdata('errors')['patient_id']) ?></div>
                <?php endif; ?>
            </div>
            
            <div class="form-group-modern">
                <label class="form-label-modern" for="nurse_id">
                    <i class="fas fa-user-nurse me-2"></i>
                    Assign to Nurse <span class="text-danger">*</span>
                </label>
                <select name="nurse_id" id="nurse_id" class="form-control-modern" required>
                    <option value="">Select Nurse</option>
                    <?php foreach ($nurses as $nurse): ?>
                        <option value="<?= esc($nurse['id']) ?>" <?= old('nurse_id') == $nurse['id'] ? 'selected' : '' ?>>
                            <?= esc(ucfirst($nurse['username'])) ?> (<?= esc($nurse['email']) ?>)
                        </option>
                    <?php endforeach; ?>
                </select>
                <?php if (session()->getFlashdata('errors') && isset(session()->getFlashdata('errors')['nurse_id'])): ?>
                    <div class="text-danger"><?= esc(session()->getFlashdata('errors')['nurse_id']) ?></div>
                <?php endif; ?>
            </div>
            
            <div class="form-group-modern">
                <label class="form-label-modern" for="order_type">
                    <i class="fas fa-tag me-2"></i>
                    Order Type <span class="text-danger">*</span>
                </label>
                <select name="order_type" id="order_type" class="form-control-modern" required>
                    <option value="">Select Order Type</option>
                    <option value="medication" <?= old('order_type') == 'medication' ? 'selected' : '' ?>>Medication</option>
                    <option value="lab_test" <?= old('order_type') == 'lab_test' ? 'selected' : '' ?>>Lab Test</option>
                    <option value="procedure" <?= old('order_type') == 'procedure' ? 'selected' : '' ?>>Procedure</option>
                    <option value="diet" <?= old('order_type') == 'diet' ? 'selected' : '' ?>>Diet</option>
                    <option value="activity" <?= old('order_type') == 'activity' ? 'selected' : '' ?>>Activity</option>
                    <option value="other" <?= old('order_type') == 'other' ? 'selected' : '' ?>>Other</option>
                </select>
                <?php if (session()->getFlashdata('errors') && isset(session()->getFlashdata('errors')['order_type'])): ?>
                    <div class="text-danger"><?= esc(session()->getFlashdata('errors')['order_type']) ?></div>
                <?php endif; ?>
            </div>
            
            <div class="form-group-modern">
                <label class="form-label-modern" for="order_description">
                    <i class="fas fa-file-medical me-2"></i>
                    Order Description <span class="text-danger">*</span>
                </label>
                <textarea name="order_description" id="order_description" class="form-control-modern" rows="4" required placeholder="Describe the medical order in detail..."><?= old('order_description') ?></textarea>
                <?php if (session()->getFlashdata('errors') && isset(session()->getFlashdata('errors')['order_description'])): ?>
                    <div class="text-danger"><?= esc(session()->getFlashdata('errors')['order_description']) ?></div>
                <?php endif; ?>
            </div>
            
            <div class="form-group-modern">
                <label class="form-label-modern" for="instructions">
                    <i class="fas fa-clipboard-list me-2"></i>
                    Instructions (Optional)
                </label>
                <textarea name="instructions" id="instructions" class="form-control-modern" rows="3" placeholder="Additional instructions for nurses..."><?= old('instructions') ?></textarea>
            </div>
            
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group-modern">
                        <label class="form-label-modern" for="frequency">
                            <i class="fas fa-clock me-2"></i>
                            Frequency (Optional)
                        </label>
                        <input type="text" name="frequency" id="frequency" class="form-control-modern" value="<?= old('frequency') ?>" placeholder="e.g., Every 8 hours, Once daily">
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group-modern">
                        <label class="form-label-modern" for="start_date">
                            <i class="fas fa-calendar me-2"></i>
                            Start Date (Optional)
                        </label>
                        <input type="date" name="start_date" id="start_date" class="form-control-modern" value="<?= old('start_date') ?>">
                    </div>
                </div>
            </div>
            
            <div class="form-group-modern">
                <label class="form-label-modern" for="end_date">
                    <i class="fas fa-calendar-times me-2"></i>
                    End Date (Optional)
                </label>
                <input type="date" name="end_date" id="end_date" class="form-control-modern" value="<?= old('end_date') ?>">
            </div>
            
            <div style="display: flex; gap: 12px; margin-top: 32px;">
                <button type="submit" class="btn-modern btn-modern-primary">
                    <i class="fas fa-save"></i>
                    Create Order
                </button>
                <a href="<?= site_url('doctor/orders') ?>" class="btn-modern btn-modern-secondary">
                    <i class="fas fa-times"></i>
                    Cancel
                </a>
            </div>
        </form>
    </div>
</div>
<?= $this->endSection() ?>

