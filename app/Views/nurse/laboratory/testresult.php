<?= $this->extend('template/header') ?>

<?= $this->section('title') ?>Results Inquiry<?= $this->endSection() ?>

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
        margin-bottom: 24px;
    }
    
    .card-body-modern {
        padding: 24px;
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
    
    .btn-modern {
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
    
    .btn-modern-info {
        background: #0288d1;
        color: white;
    }
    
    .empty-state {
        text-align: center;
        padding: 40px 20px;
        color: #94a3b8;
    }
    
    .empty-state i {
        font-size: 48px;
        margin-bottom: 16px;
        opacity: 0.4;
    }
</style>

<div class="nurse-page-container">
    <div class="page-header">
        <h1>
            <i class="fas fa-file-medical"></i>
            Results Inquiry
        </h1>
    </div>
    
    <!-- Completed Results -->
    <div class="modern-card">
        <div class="card-body-modern">
            <h3 style="margin-bottom: 24px; color: #1e293b;">Completed Lab Results</h3>
            <?php if (!empty($completedRequests)): ?>
                <div class="table-responsive">
                    <table class="table-modern">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Patient</th>
                                <th>Test Type</th>
                                <th>Test Name</th>
                                <th>Result</th>
                                <th>Completed</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($completedRequests as $request): ?>
                                <tr>
                                    <td><?= esc(date('M d, Y', strtotime($request['created_at']))) ?></td>
                                    <td><strong><?= esc(ucfirst($request['firstname']) . ' ' . ucfirst($request['lastname'])) ?></strong></td>
                                    <td><?= esc($request['test_type']) ?></td>
                                    <td><?= esc($request['test_name']) ?></td>
                                    <td><?= esc(substr($request['result'] ?? 'N/A', 0, 50)) ?><?= strlen($request['result'] ?? '') > 50 ? '...' : '' ?></td>
                                    <td><?= esc($request['completed_at'] ? date('M d, Y', strtotime($request['completed_at'])) : 'N/A') ?></td>
                                    <td>
                                        <?php if ($request['result_file']): ?>
                                            <a href="<?= base_url('writable/uploads/lab_results/' . $request['result_file']) ?>" target="_blank" class="btn-modern btn-modern-info">
                                                <i class="fas fa-download"></i>
                                                View File
                                            </a>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <div class="empty-state">
                    <i class="fas fa-file-medical"></i>
                    <p style="margin: 0; color: #64748b;">No completed lab results found.</p>
                </div>
            <?php endif; ?>
        </div>
    </div>
    
    <!-- All Lab Requests -->
    <div class="modern-card">
        <div class="card-body-modern">
            <h3 style="margin-bottom: 24px; color: #1e293b;">All Lab Requests</h3>
            <?php if (!empty($labRequests)): ?>
                <div class="table-responsive">
                    <table class="table-modern">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Patient</th>
                                <th>Test Type</th>
                                <th>Test Name</th>
                                <th>Status</th>
                                <th>Result</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($labRequests as $request): ?>
                                <tr>
                                    <td><?= esc(date('M d, Y', strtotime($request['created_at']))) ?></td>
                                    <td><strong><?= esc(ucfirst($request['firstname']) . ' ' . ucfirst($request['lastname'])) ?></strong></td>
                                    <td><?= esc($request['test_type']) ?></td>
                                    <td><?= esc($request['test_name']) ?></td>
                                    <td>
                                        <span class="badge-modern" style="background: <?= 
                                            $request['status'] == 'completed' ? '#d1fae5' : 
                                            ($request['status'] == 'in_progress' ? '#fef3c7' : '#fee2e2'); 
                                        ?>; color: <?= 
                                            $request['status'] == 'completed' ? '#065f46' : 
                                            ($request['status'] == 'in_progress' ? '#92400e' : '#991b1b'); 
                                        ?>;">
                                            <?= esc(ucfirst(str_replace('_', ' ', $request['status']))) ?>
                                        </span>
                                    </td>
                                    <td><?= esc($request['result'] ? substr($request['result'], 0, 50) . '...' : 'Pending') ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <div class="empty-state">
                    <i class="fas fa-vial"></i>
                    <p style="margin: 0; color: #64748b;">No lab requests found.</p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>
<?= $this->endSection() ?>

