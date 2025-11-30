<?= $this->extend('template/header') ?>

<?= $this->section('title') ?>Edit User<?= $this->endSection() ?>

<?= $this->section('content') ?>
<style>
    .admin-page-container {
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
    
    .password-note {
        background: #f8fafc;
        border-left: 4px solid #0288d1;
        padding: 12px 16px;
        border-radius: 8px;
        font-size: 13px;
        color: #475569;
        margin-top: 8px;
    }
</style>

<div class="admin-page-container">
    <div class="page-header">
        <h1>
            <i class="fas fa-user-edit"></i>
            Edit User
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
        <form action="<?= site_url('admin/users/update/' . $user['id']) ?>" method="post">
            <?= csrf_field() ?>
            
            <div class="form-group-modern">
                <label class="form-label-modern" for="username">
                    <i class="fas fa-user me-2"></i>
                    Username <span class="text-danger">*</span>
                </label>
                <input type="text" name="username" id="username" class="form-control-modern" value="<?= old('username', $user['username']) ?>" required>
                <?php if (session()->getFlashdata('errors') && isset(session()->getFlashdata('errors')['username'])): ?>
                    <div class="text-danger"><?= esc(session()->getFlashdata('errors')['username']) ?></div>
                <?php endif; ?>
            </div>
            
            <div class="form-group-modern">
                <label class="form-label-modern" for="email">
                    <i class="fas fa-envelope me-2"></i>
                    Email <span class="text-danger">*</span>
                </label>
                <input type="email" name="email" id="email" class="form-control-modern" value="<?= old('email', $user['email']) ?>" required>
                <?php if (session()->getFlashdata('errors') && isset(session()->getFlashdata('errors')['email'])): ?>
                    <div class="text-danger"><?= esc(session()->getFlashdata('errors')['email']) ?></div>
                <?php endif; ?>
            </div>
            
            <div class="form-group-modern">
                <label class="form-label-modern" for="password">
                    <i class="fas fa-lock me-2"></i>
                    Password (Leave blank to keep current password)
                </label>
                <input type="password" name="password" id="password" class="form-control-modern" placeholder="Enter new password (minimum 6 characters)">
                <div class="password-note">
                    <i class="fas fa-info-circle me-2"></i>
                    Leave this field blank if you don't want to change the password.
                </div>
                <?php if (session()->getFlashdata('errors') && isset(session()->getFlashdata('errors')['password'])): ?>
                    <div class="text-danger"><?= esc(session()->getFlashdata('errors')['password']) ?></div>
                <?php endif; ?>
            </div>
            
            <div class="form-group-modern">
                <label class="form-label-modern" for="role_id">
                    <i class="fas fa-user-tag me-2"></i>
                    Role <span class="text-danger">*</span>
                </label>
                <select name="role_id" id="role_id" class="form-control-modern" required>
                    <option value="">Select Role</option>
                    <?php foreach ($roles as $role): ?>
                        <option value="<?= esc($role['id']) ?>" <?= (old('role_id', $user['role_id']) == $role['id']) ? 'selected' : '' ?>>
                            <?= esc(ucfirst(str_replace('_', ' ', $role['name']))) ?>
                            <?php if (!empty($role['description'])): ?>
                                - <?= esc($role['description']) ?>
                            <?php endif; ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <?php if (session()->getFlashdata('errors') && isset(session()->getFlashdata('errors')['role_id'])): ?>
                    <div class="text-danger"><?= esc(session()->getFlashdata('errors')['role_id']) ?></div>
                <?php endif; ?>
            </div>
            
            <div class="form-group-modern">
                <label class="form-label-modern" for="status">
                    <i class="fas fa-toggle-on me-2"></i>
                    Status <span class="text-danger">*</span>
                </label>
                <select name="status" id="status" class="form-control-modern" required>
                    <option value="">Select Status</option>
                    <option value="active" <?= (old('status', $user['status']) == 'active') ? 'selected' : '' ?>>Active</option>
                    <option value="inactive" <?= (old('status', $user['status']) == 'inactive') ? 'selected' : '' ?>>Inactive</option>
                </select>
                <?php if (session()->getFlashdata('errors') && isset(session()->getFlashdata('errors')['status'])): ?>
                    <div class="text-danger"><?= esc(session()->getFlashdata('errors')['status']) ?></div>
                <?php endif; ?>
            </div>
            
            <div style="display: flex; gap: 12px; margin-top: 32px;">
                <button type="submit" class="btn-modern btn-modern-primary">
                    <i class="fas fa-save"></i>
                    Update User
                </button>
                <a href="<?= site_url('admin/users') ?>" class="btn-modern btn-modern-secondary">
                    <i class="fas fa-times"></i>
                    Cancel
                </a>
            </div>
        </form>
    </div>
</div>
<?= $this->endSection() ?>

