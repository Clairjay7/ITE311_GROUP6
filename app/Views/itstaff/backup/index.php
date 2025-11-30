<?= $this->extend('template/header') ?>

<?= $this->section('title') ?>Create Backup<?= $this->endSection() ?>

<?= $this->section('content') ?>
<style>
    .it-page-container {
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
    
    .btn-modern-primary:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(2, 136, 209, 0.4);
        color: white;
    }
    
    .table-modern {
        width: 100%;
        border-collapse: collapse;
    }
    
    .table-modern thead {
        background: linear-gradient(135deg, #e3f2fd 0%, #f1f8ff 100%);
    }
    
    .table-modern th {
        padding: 16px;
        text-align: left;
        font-weight: 700;
        color: #0288d1;
        font-size: 13px;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        border-bottom: 2px solid #90caf9;
    }
    
    .table-modern td {
        padding: 16px;
        border-bottom: 1px solid #f1f5f9;
        color: #475569;
    }
    
    .table-modern tbody tr:hover {
        background: #f8fafc;
    }
    
    .badge-modern {
        padding: 6px 12px;
        border-radius: 8px;
        font-weight: 600;
        font-size: 12px;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
    
    .btn-sm-modern {
        padding: 6px 12px;
        border-radius: 8px;
        font-weight: 600;
        font-size: 12px;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        transition: all 0.3s ease;
        border: none;
        text-decoration: none;
        cursor: pointer;
    }
    
    .btn-success {
        background: #10b981;
        color: white;
    }
    
    .btn-success:hover {
        background: #059669;
        color: white;
        transform: translateY(-2px);
    }
    
    .btn-danger {
        background: #ef4444;
        color: white;
    }
    
    .btn-danger:hover {
        background: #dc2626;
        color: white;
        transform: translateY(-2px);
    }
    
    .alert-modern {
        border-radius: 12px;
        padding: 16px 20px;
        margin-bottom: 24px;
        border: none;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
    }
    
    .alert-modern-success {
        background: linear-gradient(135deg, #d1fae5 0%, #a7f3d0 100%);
        color: #065f46;
        border-left: 4px solid #10b981;
    }
    
    .alert-modern-danger {
        background: linear-gradient(135deg, #fee2e2 0%, #fecaca 100%);
        color: #991b1b;
        border-left: 4px solid #ef4444;
    }
    
    .info-box {
        background: #e3f2fd;
        border-left: 4px solid #0288d1;
        padding: 16px;
        border-radius: 8px;
        margin-bottom: 24px;
    }
    
    .info-box p {
        margin: 0;
        color: #0369a1;
        font-size: 14px;
    }
</style>

<div class="it-page-container">
    <div class="page-header">
        <h1>
            <i class="fas fa-database"></i>
            Create Backup
        </h1>
    </div>
    
    <?php if (session()->getFlashdata('success')): ?>
        <div class="alert-modern alert-modern-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle me-2"></i>
            <?= session()->getFlashdata('success') ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>
    
    <?php if (session()->getFlashdata('error')): ?>
        <div class="alert-modern alert-modern-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-circle me-2"></i>
            <?= session()->getFlashdata('error') ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <!-- Create Backup Form -->
    <div class="modern-card">
        <h3 style="color: #0288d1; margin-bottom: 24px; display: flex; align-items: center; gap: 10px;">
            <i class="fas fa-plus-circle"></i>
            Create New Backup
        </h3>
        
        <div class="info-box">
            <p><i class="fas fa-info-circle me-2"></i><strong>Note:</strong> Database backups include all tables and data. File backups include application files. Full backups include both database and files.</p>
        </div>
        
        <form action="<?= site_url('it/backup/create') ?>" method="post">
            <?= csrf_field() ?>
            
            <div class="form-group-modern">
                <label class="form-label-modern" for="backup_name">
                    <i class="fas fa-tag me-2"></i>
                    Backup Name
                </label>
                <input type="text" name="backup_name" id="backup_name" class="form-control-modern" placeholder="Leave blank for auto-generated name">
                <small style="color: #64748b; font-size: 12px;">If left blank, a name will be auto-generated with timestamp</small>
            </div>
            
            <div class="form-group-modern">
                <label class="form-label-modern" for="backup_type">
                    <i class="fas fa-layer-group me-2"></i>
                    Backup Type <span class="text-danger">*</span>
                </label>
                <select name="backup_type" id="backup_type" class="form-control-modern" required>
                    <option value="">Select Backup Type</option>
                    <option value="database">Database Only</option>
                    <?php if (class_exists('ZipArchive')): ?>
                        <option value="files">Files Only</option>
                        <option value="full">Full Backup (Database + Files)</option>
                    <?php else: ?>
                        <option value="files" disabled title="PHP Zip extension is not enabled">Files Only (Not Available - Zip extension required)</option>
                        <option value="full" disabled title="PHP Zip extension is not enabled">Full Backup (Not Available - Zip extension required)</option>
                    <?php endif; ?>
                </select>
                <?php if (!class_exists('ZipArchive')): ?>
                    <div class="alert-modern alert-modern-danger" style="margin-top: 12px; padding: 12px;">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        <strong>Note:</strong> PHP Zip extension is not enabled. File backups are not available. Only database backups can be created. Please enable php_zip extension in your PHP configuration to enable file backups.
                    </div>
                <?php endif; ?>
            </div>
            
            <div class="form-group-modern">
                <label class="form-label-modern" for="notes">
                    <i class="fas fa-sticky-note me-2"></i>
                    Notes (Optional)
                </label>
                <textarea name="notes" id="notes" class="form-control-modern" rows="3" placeholder="Add any notes about this backup..."></textarea>
            </div>
            
            <div style="display: flex; gap: 12px; margin-top: 32px;">
                <button type="submit" class="btn-modern btn-modern-primary">
                    <i class="fas fa-database"></i>
                    Create Backup
                </button>
            </div>
        </form>
    </div>

    <!-- Existing Backups -->
    <div class="modern-card">
        <h3 style="color: #0288d1; margin-bottom: 24px; display: flex; align-items: center; gap: 10px;">
            <i class="fas fa-list"></i>
            Existing Backups (<?= count($backups) ?>)
        </h3>
        
        <div class="table-responsive">
            <table class="table-modern">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Backup Name</th>
                        <th>Type</th>
                        <th>Size</th>
                        <th>Status</th>
                        <th>Created By</th>
                        <th>Date</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($backups)): ?>
                        <?php foreach ($backups as $backup): ?>
                            <tr>
                                <td><strong>#<?= esc($backup['id']) ?></strong></td>
                                <td><strong><?= esc($backup['backup_name']) ?></strong></td>
                                <td>
                                    <span class="badge-modern" style="background: #e0f2fe; color: #0369a1;">
                                        <?= esc(ucfirst($backup['backup_type'])) ?>
                                    </span>
                                </td>
                                <td><?= $backup['file_size'] ? number_format($backup['file_size'] / 1024 / 1024, 2) . ' MB' : 'N/A' ?></td>
                                <td>
                                    <span class="badge-modern" style="background: <?= 
                                        $backup['status'] == 'completed' ? '#d1fae5' : 
                                        ($backup['status'] == 'failed' ? '#fee2e2' : 
                                        ($backup['status'] == 'in_progress' ? '#fef3c7' : '#dbeafe')); 
                                    ?>; color: <?= 
                                        $backup['status'] == 'completed' ? '#065f46' : 
                                        ($backup['status'] == 'failed' ? '#991b1b' : 
                                        ($backup['status'] == 'in_progress' ? '#92400e' : '#1e40af')); 
                                    ?>;">
                                        <?= esc(ucfirst(str_replace('_', ' ', $backup['status']))) ?>
                                    </span>
                                </td>
                                <td><?= esc($backup['created_by_name'] ?? 'N/A') ?></td>
                                <td><?= esc(date('M d, Y h:i A', strtotime($backup['created_at']))) ?></td>
                                <td>
                                    <div style="display: flex; gap: 8px; flex-wrap: wrap;">
                                        <?php if ($backup['status'] == 'completed' && file_exists($backup['file_path'])): ?>
                                            <a href="<?= site_url('it/backup/download/' . $backup['id']) ?>" class="btn-sm-modern btn-success">
                                                <i class="fas fa-download"></i>
                                                Download
                                            </a>
                                        <?php endif; ?>
                                        <a href="<?= site_url('it/backup/delete/' . $backup['id']) ?>" class="btn-sm-modern btn-danger" onclick="return confirm('Are you sure you want to delete this backup?')">
                                            <i class="fas fa-trash"></i>
                                            Delete
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="8" style="text-align: center; padding: 40px; color: #94a3b8;">
                                <i class="fas fa-database" style="font-size: 48px; margin-bottom: 16px; opacity: 0.4;"></i>
                                <p style="margin: 0;">No backups found.</p>
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?= $this->endSection() ?>

