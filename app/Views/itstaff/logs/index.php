<?= $this->extend('template/header') ?>

<?= $this->section('title') ?>System Logs<?= $this->endSection() ?>

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
    
    .filter-card {
        background: white;
        border-radius: 16px;
        box-shadow: 0 2px 12px rgba(0, 0, 0, 0.08);
        border: 1px solid #e5e7eb;
        padding: 24px;
        margin-bottom: 24px;
    }
    
    .filter-form {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 16px;
        align-items: end;
    }
    
    .form-group-modern {
        margin-bottom: 0;
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
        padding: 10px 14px;
        border: 2px solid #e5e7eb;
        border-radius: 8px;
        font-size: 14px;
        transition: all 0.3s ease;
    }
    
    .form-control-modern:focus {
        outline: none;
        border-color: #0288d1;
        box-shadow: 0 0 0 3px rgba(2, 136, 209, 0.1);
    }
    
    .btn-modern {
        padding: 10px 20px;
        border-radius: 8px;
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
    
    .btn-modern-danger {
        background: #ef4444;
        color: white;
    }
    
    .btn-modern-danger:hover {
        background: #dc2626;
        color: white;
        transform: translateY(-2px);
    }
    
    .btn-modern-secondary {
        background: #f1f5f9;
        color: #475569;
    }
    
    .btn-modern-secondary:hover {
        background: #e2e8f0;
        color: #475569;
    }
    
    .modern-card {
        background: white;
        border-radius: 16px;
        box-shadow: 0 2px 12px rgba(0, 0, 0, 0.08);
        border: 1px solid #e5e7eb;
        overflow: hidden;
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
        font-size: 14px;
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
    
    .btn-info {
        background: #0288d1;
        color: white;
    }
    
    .btn-info:hover {
        background: #0277bd;
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
    
    .pagination {
        display: flex;
        justify-content: center;
        align-items: center;
        gap: 8px;
        margin-top: 24px;
    }
    
    .pagination a, .pagination span {
        padding: 8px 12px;
        border-radius: 8px;
        text-decoration: none;
        font-weight: 600;
        font-size: 14px;
    }
    
    .pagination a {
        background: #e3f2fd;
        color: #0288d1;
    }
    
    .pagination a:hover {
        background: #0288d1;
        color: white;
    }
    
    .pagination .current {
        background: #0288d1;
        color: white;
    }
</style>

<div class="it-page-container">
    <div class="page-header">
        <h1>
            <i class="fas fa-file-alt"></i>
            System Logs
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

    <!-- Filters -->
    <div class="filter-card">
        <form method="get" action="<?= site_url('it/logs') ?>" class="filter-form">
            <div class="form-group-modern">
                <label class="form-label-modern" for="level">Level</label>
                <select name="level" id="level" class="form-control-modern">
                    <option value="">All Levels</option>
                    <option value="emergency" <?= ($filters['level'] ?? '') == 'emergency' ? 'selected' : '' ?>>Emergency</option>
                    <option value="alert" <?= ($filters['level'] ?? '') == 'alert' ? 'selected' : '' ?>>Alert</option>
                    <option value="critical" <?= ($filters['level'] ?? '') == 'critical' ? 'selected' : '' ?>>Critical</option>
                    <option value="error" <?= ($filters['level'] ?? '') == 'error' ? 'selected' : '' ?>>Error</option>
                    <option value="warning" <?= ($filters['level'] ?? '') == 'warning' ? 'selected' : '' ?>>Warning</option>
                    <option value="notice" <?= ($filters['level'] ?? '') == 'notice' ? 'selected' : '' ?>>Notice</option>
                    <option value="info" <?= ($filters['level'] ?? '') == 'info' ? 'selected' : '' ?>>Info</option>
                    <option value="debug" <?= ($filters['level'] ?? '') == 'debug' ? 'selected' : '' ?>>Debug</option>
                </select>
            </div>
            
            <div class="form-group-modern">
                <label class="form-label-modern" for="module">Module</label>
                <input type="text" name="module" id="module" class="form-control-modern" value="<?= esc($filters['module'] ?? '') ?>" placeholder="Module name">
            </div>
            
            <div class="form-group-modern">
                <label class="form-label-modern" for="date_from">Date From</label>
                <input type="date" name="date_from" id="date_from" class="form-control-modern" value="<?= esc($filters['date_from'] ?? '') ?>">
            </div>
            
            <div class="form-group-modern">
                <label class="form-label-modern" for="date_to">Date To</label>
                <input type="date" name="date_to" id="date_to" class="form-control-modern" value="<?= esc($filters['date_to'] ?? '') ?>">
            </div>
            
            <div class="form-group-modern">
                <label class="form-label-modern" for="search">Search</label>
                <input type="text" name="search" id="search" class="form-control-modern" value="<?= esc($filters['search'] ?? '') ?>" placeholder="Search message...">
            </div>
            
            <div class="form-group-modern">
                <button type="submit" class="btn-modern btn-modern-primary" style="width: 100%;">
                    <i class="fas fa-filter"></i>
                    Filter
                </button>
            </div>
            
            <div class="form-group-modern">
                <a href="<?= site_url('it/logs') ?>" class="btn-modern btn-modern-secondary" style="width: 100%;">
                    <i class="fas fa-redo"></i>
                    Reset
                </a>
            </div>
        </form>
    </div>

    <!-- Logs Table -->
    <div class="modern-card">
        <div style="padding: 20px; border-bottom: 2px solid #e5e7eb; display: flex; justify-content: space-between; align-items: center;">
            <h3 style="margin: 0; color: #0288d1;">Log Entries (<?= $pager['total_items'] ?? 0 ?>)</h3>
            <form action="<?= site_url('it/logs/clear') ?>" method="post" style="display: inline;" onsubmit="return confirm('Are you sure you want to clear old logs? This action cannot be undone.');">
                <?= csrf_field() ?>
                <div style="display: flex; gap: 8px; align-items: center;">
                    <input type="number" name="days" value="30" min="1" max="365" style="width: 80px; padding: 6px; border: 1px solid #e5e7eb; border-radius: 6px;">
                    <button type="submit" class="btn-modern btn-modern-danger">
                        <i class="fas fa-trash"></i>
                        Clear Old Logs
                    </button>
                </div>
            </form>
        </div>
        
        <div class="table-responsive">
            <table class="table-modern">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Level</th>
                        <th>Message</th>
                        <th>Module</th>
                        <th>User</th>
                        <th>IP Address</th>
                        <th>Date</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($logs)): ?>
                        <?php foreach ($logs as $log): ?>
                            <tr>
                                <td><strong>#<?= esc($log['id']) ?></strong></td>
                                <td>
                                    <span class="badge-modern" style="background: <?= 
                                        in_array($log['level'], ['error', 'critical', 'alert', 'emergency']) ? '#fee2e2' : 
                                        ($log['level'] == 'warning' ? '#fef3c7' : '#dbeafe'); 
                                    ?>; color: <?= 
                                        in_array($log['level'], ['error', 'critical', 'alert', 'emergency']) ? '#991b1b' : 
                                        ($log['level'] == 'warning' ? '#92400e' : '#1e40af'); 
                                    ?>;">
                                        <?= esc(ucfirst($log['level'])) ?>
                                    </span>
                                </td>
                                <td><?= esc(substr($log['message'], 0, 80)) ?><?= strlen($log['message']) > 80 ? '...' : '' ?></td>
                                <td><?= esc($log['module'] ?? 'N/A') ?></td>
                                <td><?= esc($log['user_name'] ?? 'System') ?></td>
                                <td><?= esc($log['ip_address'] ?? 'N/A') ?></td>
                                <td><?= esc(date('M d, Y h:i A', strtotime($log['created_at']))) ?></td>
                                <td>
                                    <div style="display: flex; gap: 8px;">
                                        <a href="<?= site_url('it/logs/view/' . $log['id']) ?>" class="btn-sm-modern btn-info">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="<?= site_url('it/logs/delete/' . $log['id']) ?>" class="btn-sm-modern btn-danger" onclick="return confirm('Are you sure you want to delete this log entry?')">
                                            <i class="fas fa-trash"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="8" style="text-align: center; padding: 40px; color: #94a3b8;">
                                <i class="fas fa-file-alt" style="font-size: 48px; margin-bottom: 16px; opacity: 0.4;"></i>
                                <p style="margin: 0;">No logs found.</p>
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
        
        <!-- Pagination -->
        <?php if (($pager['total_pages'] ?? 0) > 1): ?>
            <div class="pagination">
                <?php if (($pager['current_page'] ?? 1) > 1): ?>
                    <a href="?page=<?= ($pager['current_page'] ?? 1) - 1 ?><?= !empty($filters) ? '&' . http_build_query($filters) : '' ?>">
                        <i class="fas fa-chevron-left"></i> Previous
                    </a>
                <?php endif; ?>
                
                <span class="current">Page <?= $pager['current_page'] ?? 1 ?> of <?= $pager['total_pages'] ?? 1 ?></span>
                
                <?php if (($pager['current_page'] ?? 1) < ($pager['total_pages'] ?? 1)): ?>
                    <a href="?page=<?= ($pager['current_page'] ?? 1) + 1 ?><?= !empty($filters) ? '&' . http_build_query($filters) : '' ?>">
                        Next <i class="fas fa-chevron-right"></i>
                    </a>
                <?php endif; ?>
            </div>
        <?php endif; ?>
    </div>
</div>
<?= $this->endSection() ?>

