<?= $this->extend('template/header') ?>

<?= $this->section('title') ?>Log Details<?= $this->endSection() ?>

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
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 16px;
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
    
    .info-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 24px;
        margin-bottom: 32px;
    }
    
    .info-item {
        padding: 16px;
        background: #f8fafc;
        border-radius: 12px;
        border-left: 4px solid #0288d1;
    }
    
    .info-label {
        font-size: 12px;
        font-weight: 600;
        color: #64748b;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        margin-bottom: 8px;
    }
    
    .info-value {
        font-size: 16px;
        font-weight: 700;
        color: #1e293b;
    }
    
    .badge-modern {
        padding: 6px 12px;
        border-radius: 8px;
        font-weight: 600;
        font-size: 12px;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
    
    .section-title {
        font-size: 20px;
        font-weight: 700;
        color: #1e293b;
        margin-bottom: 16px;
        padding-bottom: 12px;
        border-bottom: 2px solid #e5e7eb;
        display: flex;
        align-items: center;
        gap: 10px;
    }
    
    .code-block {
        background: #1e293b;
        color: #e2e8f0;
        padding: 20px;
        border-radius: 12px;
        font-family: 'Courier New', monospace;
        font-size: 13px;
        line-height: 1.6;
        overflow-x: auto;
        white-space: pre-wrap;
        word-wrap: break-word;
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
    
    .btn-modern-secondary {
        background: #f1f5f9;
        color: #475569;
    }
    
    .btn-modern-secondary:hover {
        background: #e2e8f0;
        color: #475569;
    }
</style>

<div class="it-page-container">
    <div class="page-header">
        <h1>
            <i class="fas fa-file-alt"></i>
            Log Details #<?= esc($log['id']) ?>
        </h1>
        <a href="<?= base_url('admin/system/logs') ?>" class="btn-modern btn-modern-secondary">
            <i class="fas fa-arrow-left"></i>
            Back to Logs
        </a>
    </div>

    <div class="modern-card">
        <div class="info-grid">
            <div class="info-item">
                <div class="info-label">Level</div>
                <div class="info-value">
                    <span class="badge-modern" style="background: <?= 
                        in_array($log['level'], ['error', 'critical', 'alert', 'emergency']) ? '#fee2e2' : 
                        ($log['level'] == 'warning' ? '#fef3c7' : '#dbeafe'); 
                    ?>; color: <?= 
                        in_array($log['level'], ['error', 'critical', 'alert', 'emergency']) ? '#991b1b' : 
                        ($log['level'] == 'warning' ? '#92400e' : '#1e40af'); 
                    ?>;">
                        <?= esc(ucfirst($log['level'])) ?>
                    </span>
                </div>
            </div>
            
            <div class="info-item">
                <div class="info-label">Module</div>
                <div class="info-value"><?= esc($log['module'] ?? 'N/A') ?></div>
            </div>
            
            <div class="info-item">
                <div class="info-label">Action</div>
                <div class="info-value"><?= esc($log['action'] ?? 'N/A') ?></div>
            </div>
            
            <div class="info-item">
                <div class="info-label">User</div>
                <div class="info-value"><?= esc($log['user_name'] ?? 'System') ?></div>
            </div>
            
            <div class="info-item">
                <div class="info-label">IP Address</div>
                <div class="info-value"><?= esc($log['ip_address'] ?? 'N/A') ?></div>
            </div>
            
            <div class="info-item">
                <div class="info-label">Date & Time</div>
                <div class="info-value"><?= esc(date('M d, Y h:i:s A', strtotime($log['created_at']))) ?></div>
            </div>
        </div>
        
        <div class="section-title">
            <i class="fas fa-comment"></i>
            Message
        </div>
        <div style="padding: 16px; background: #f8fafc; border-radius: 12px; color: #475569; line-height: 1.6;">
            <?= nl2br(esc($log['message'])) ?>
        </div>
        
        <?php if (!empty($log['context'])): ?>
        <div class="section-title" style="margin-top: 32px;">
            <i class="fas fa-code"></i>
            Context
        </div>
        <div class="code-block"><?= esc($log['context']) ?></div>
        <?php endif; ?>
        
        <?php if (!empty($log['user_agent'])): ?>
        <div class="section-title" style="margin-top: 32px;">
            <i class="fas fa-desktop"></i>
            User Agent
        </div>
        <div style="padding: 16px; background: #f8fafc; border-radius: 12px; color: #475569; font-size: 13px;">
            <?= esc($log['user_agent']) ?>
        </div>
        <?php endif; ?>
    </div>
</div>
<?= $this->endSection() ?>


