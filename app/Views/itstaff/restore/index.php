<?= $this->extend('template/header') ?>

<?= $this->section('title') ?>Restore System<?= $this->endSection() ?>

<?= $this->section('content') ?>
<style>
    .it-page-container {
        padding: 0;
    }
    
    .page-header {
        background: linear-gradient(135deg, #f59e0b 0%, #fbbf24 100%);
        border-radius: 16px;
        padding: 24px 32px;
        margin-bottom: 24px;
        box-shadow: 0 4px 20px rgba(245, 158, 11, 0.2);
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
    
    .table-modern {
        width: 100%;
        border-collapse: collapse;
    }
    
    .table-modern thead {
        background: linear-gradient(135deg, #fef3c7 0%, #fde68a 100%);
    }
    
    .table-modern th {
        padding: 16px;
        text-align: left;
        font-weight: 700;
        color: #92400e;
        font-size: 13px;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        border-bottom: 2px solid #fcd34d;
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
    
    .btn-modern {
        padding: 10px 20px;
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
    
    .btn-modern-warning {
        background: linear-gradient(135deg, #f59e0b 0%, #fbbf24 100%);
        color: white;
        box-shadow: 0 4px 12px rgba(245, 158, 11, 0.3);
    }
    
    .btn-modern-warning:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(245, 158, 11, 0.4);
        color: white;
    }
    
    .alert-modern {
        border-radius: 12px;
        padding: 16px 20px;
        margin-bottom: 24px;
        border: none;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
    }
    
    .alert-modern-warning {
        background: linear-gradient(135deg, #fef3c7 0%, #fde68a 100%);
        color: #92400e;
        border-left: 4px solid #f59e0b;
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
    
    .warning-box {
        background: #fef3c7;
        border-left: 4px solid #f59e0b;
        padding: 20px;
        border-radius: 8px;
        margin-bottom: 24px;
    }
    
    .warning-box h4 {
        color: #92400e;
        margin: 0 0 12px 0;
        display: flex;
        align-items: center;
        gap: 8px;
    }
    
    .warning-box ul {
        margin: 0;
        padding-left: 20px;
        color: #92400e;
    }
    
    .warning-box li {
        margin-bottom: 8px;
    }
</style>

<div class="it-page-container">
    <div class="page-header">
        <h1>
            <i class="fas fa-rotate-left"></i>
            Restore System
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

    <div class="warning-box">
        <h4>
            <i class="fas fa-exclamation-triangle"></i>
            Important Warning
        </h4>
        <ul>
            <li><strong>Restoring a backup will overwrite your current system data.</strong></li>
            <li>Make sure to create a backup of your current system before restoring.</li>
            <li>Database restoration will replace all existing database tables and data.</li>
            <li>File restoration will overwrite existing application files.</li>
            <li>This action cannot be undone. Proceed with caution.</li>
        </ul>
    </div>

    <div class="modern-card">
        <h3 style="color: #f59e0b; margin-bottom: 24px; display: flex; align-items: center; gap: 10px;">
            <i class="fas fa-list"></i>
            Available Backups (<?= count($backups) ?>)
        </h3>
        
        <div class="table-responsive">
            <table class="table-modern">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Backup Name</th>
                        <th>Type</th>
                        <th>Size</th>
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
                                <td><?= esc($backup['created_by'] ?? 'N/A') ?></td>
                                <td><?= esc(date('M d, Y h:i A', strtotime($backup['created_at']))) ?></td>
                                <td>
                                    <?php if (file_exists($backup['file_path'])): ?>
                                        <form action="<?= site_url('it/restore/restore/' . $backup['id']) ?>" method="post" style="display: inline;" onsubmit="return confirm('WARNING: This will restore the system from this backup. All current data will be overwritten. Are you absolutely sure you want to proceed?');">
                                            <?= csrf_field() ?>
                                            <button type="submit" class="btn-modern btn-modern-warning">
                                                <i class="fas fa-rotate-left"></i>
                                                Restore
                                            </button>
                                        </form>
                                    <?php else: ?>
                                        <span style="color: #ef4444; font-size: 12px;">File not found</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="7" style="text-align: center; padding: 40px; color: #94a3b8;">
                                <i class="fas fa-database" style="font-size: 48px; margin-bottom: 16px; opacity: 0.4;"></i>
                                <p style="margin: 0;">No completed backups available for restoration.</p>
                                <a href="<?= site_url('it/backup') ?>" style="color: #0288d1; text-decoration: none; font-weight: 600; margin-top: 16px; display: inline-block;">
                                    Create a backup first →
                                </a>
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?= $this->endSection() ?>

