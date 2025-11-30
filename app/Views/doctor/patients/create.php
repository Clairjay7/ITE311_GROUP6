8<?= $this->extend('template/header') ?>

<?= $this->section('title') ?>Create New Patient<?= $this->endSection() ?>

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
    
    .page-header h1 i {
        font-size: 32px;
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
    
    .form-control-modern::placeholder {
        color: #94a3b8;
    }
    
    .error-message {
        color: #ef4444;
        font-size: 13px;
        margin-top: 6px;
        display: flex;
        align-items: center;
        gap: 6px;
    }
    
    .error-message i {
        font-size: 14px;
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
    
    .btn-modern-primary:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(46, 125, 50, 0.4);
        color: white;
    }
    
    .btn-modern-secondary {
        background: #64748b;
        color: white;
    }
    
    .btn-modern-secondary:hover {
        background: #475569;
        color: white;
        transform: translateY(-2px);
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
    
    @media (max-width: 768px) {
        .card-body-modern {
            padding: 20px;
        }
    }
</style>

<div class="doctor-page-container">
    <div class="page-header">
        <h1>
            <i class="fas fa-user-plus"></i>
            Add New Patient
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

            <form action="<?= site_url('doctor/patients/store') ?>" method="post">
                <?= csrf_field() ?>
                
                <div class="form-section">
                    <div class="form-section-title">
                        <i class="fas fa-user"></i>
                        Personal Information
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="firstname" class="form-label-modern">First Name *</label>
                            <input type="text" class="form-control-modern" id="firstname" name="firstname" 
                                   value="<?= old('firstname') ?>" required placeholder="Enter first name">
                            <?php if (isset($validation) && $validation->getError('firstname')): ?>
                                <div class="error-message">
                                    <i class="fas fa-exclamation-circle"></i>
                                    <?= $validation->getError('firstname') ?>
                                </div>
                            <?php endif; ?>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="lastname" class="form-label-modern">Last Name *</label>
                            <input type="text" class="form-control-modern" id="lastname" name="lastname" 
                                   value="<?= old('lastname') ?>" required placeholder="Enter last name">
                            <?php if (isset($validation) && $validation->getError('lastname')): ?>
                                <div class="error-message">
                                    <i class="fas fa-exclamation-circle"></i>
                                    <?= $validation->getError('lastname') ?>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label for="birthdate" class="form-label-modern">Birthdate *</label>
                            <input type="date" class="form-control-modern" id="birthdate" name="birthdate" 
                                   value="<?= old('birthdate') ?>" required>
                            <?php if (isset($validation) && $validation->getError('birthdate')): ?>
                                <div class="error-message">
                                    <i class="fas fa-exclamation-circle"></i>
                                    <?= $validation->getError('birthdate') ?>
                                </div>
                            <?php endif; ?>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="gender" class="form-label-modern">Gender *</label>
                            <select class="form-select-modern" id="gender" name="gender" required>
                                <option value="">Select Gender</option>
                                <option value="male" <?= old('gender') == 'male' ? 'selected' : '' ?>>Male</option>
                                <option value="female" <?= old('gender') == 'female' ? 'selected' : '' ?>>Female</option>
                                <option value="other" <?= old('gender') == 'other' ? 'selected' : '' ?>>Other</option>
                            </select>
                            <?php if (isset($validation) && $validation->getError('gender')): ?>
                                <div class="error-message">
                                    <i class="fas fa-exclamation-circle"></i>
                                    <?= $validation->getError('gender') ?>
                                </div>
                            <?php endif; ?>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="contact" class="form-label-modern">Contact Number</label>
                            <input type="tel" class="form-control-modern" id="contact" name="contact" 
                                   value="<?= old('contact') ?>" placeholder="09123456789">
                            <?php if (isset($validation) && $validation->getError('contact')): ?>
                                <div class="error-message">
                                    <i class="fas fa-exclamation-circle"></i>
                                    <?= $validation->getError('contact') ?>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="address" class="form-label-modern">Address</label>
                        <textarea class="form-control-modern" id="address" name="address" rows="3" 
                                  placeholder="Enter complete address" style="resize: vertical;"><?= old('address') ?></textarea>
                        <?php if (isset($validation) && $validation->getError('address')): ?>
                            <div class="error-message">
                                <i class="fas fa-exclamation-circle"></i>
                                <?= $validation->getError('address') ?>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="d-flex justify-content-between flex-wrap gap-3">
                    <a href="<?= site_url('doctor/patients') ?>" class="btn-modern btn-modern-secondary">
                        <i class="fas fa-arrow-left"></i>
                        Back to Patients
                    </a>
                    <button type="submit" class="btn-modern btn-modern-primary">
                        <i class="fas fa-save"></i>
                        Save Patient
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
