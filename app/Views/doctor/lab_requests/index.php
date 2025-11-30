<?= $this->extend('template/header') ?>

<?= $this->section('title') ?>Lab Requests from Nurses<?= $this->endSection() ?>

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
        margin-bottom: 24px;
    }
    
    .card-header-modern {
        background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
        padding: 20px 24px;
        border-bottom: 2px solid #e5e7eb;
    }
    
    .card-header-modern h5 {
        margin: 0;
        font-size: 18px;
        font-weight: 700;
        color: #1e293b;
        display: flex;
        align-items: center;
        gap: 10px;
    }
    
    .card-body-modern {
        padding: 24px;
    }
    
    .table-modern {
        width: 100%;
        border-collapse: collapse;
    }
    
    .table-modern thead {
        background: linear-gradient(135deg, #e8f5e9 0%, #f1f8f4 100%);
    }
    
    .table-modern th {
        padding: 16px;
        text-align: left;
        font-weight: 700;
        color: #2e7d32;
        font-size: 13px;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        border-bottom: 2px solid #c8e6c9;
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
        padding: 8px 16px;
        border-radius: 10px;
        font-weight: 600;
        font-size: 13px;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        transition: all 0.3s ease;
        border: none;
        text-decoration: none;
        cursor: pointer;
    }
    
    .btn-modern-success {
        background: linear-gradient(135deg, #10b981 0%, #34d399 100%);
        color: white;
        box-shadow: 0 4px 12px rgba(16, 185, 129, 0.3);
    }
    
    .btn-modern-success:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(16, 185, 129, 0.4);
        color: white;
    }
    
    .btn-modern-danger {
        background: linear-gradient(135deg, #ef4444 0%, #f87171 100%);
        color: white;
        box-shadow: 0 4px 12px rgba(239, 68, 68, 0.3);
    }
    
    .btn-modern-danger:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(239, 68, 68, 0.4);
        color: white;
    }
    
    .btn-modern-info {
        background: #0288d1;
        color: white;
    }
    
    .btn-modern-info:hover {
        background: #0277bd;
        color: white;
        transform: translateY(-2px);
    }
    
    .empty-state {
        text-align: center;
        padding: 60px 20px;
        color: #94a3b8;
    }
    
    .empty-state i {
        font-size: 72px;
        margin-bottom: 20px;
        opacity: 0.4;
        color: #cbd5e1;
    }
    
    .empty-state h5 {
        margin: 0 0 12px;
        color: #64748b;
        font-size: 20px;
        font-weight: 600;
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
    
    .modal-content {
        border-radius: 16px;
        border: none;
        box-shadow: 0 10px 40px rgba(0, 0, 0, 0.2);
    }
    
    .modal-header {
        background: linear-gradient(135deg, #fee2e2 0%, #fecaca 100%);
        border-bottom: 2px solid #ef4444;
        border-radius: 16px 16px 0 0;
        padding: 20px 24px;
    }
    
    .modal-title {
        color: #991b1b;
        font-weight: 700;
    }
    
    .form-control {
        border-radius: 10px;
        border: 2px solid #e5e7eb;
        padding: 12px 16px;
    }
    
    .form-control:focus {
        border-color: #ef4444;
        box-shadow: 0 0 0 3px rgba(239, 68, 68, 0.1);
    }
</style>

<div class="doctor-page-container">
    <div class="page-header">
        <h1>
            <i class="fas fa-vial"></i>
            Lab Requests from Nurses
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

    <!-- Pending Requests -->
    <div class="modern-card">
        <div class="card-header-modern">
            <h5>
                <i class="fas fa-clock"></i>
                Pending Requests (Awaiting Your Confirmation)
            </h5>
        </div>
        <div class="card-body-modern">
            <?php if (!empty($pendingRequests)): ?>
                <div class="table-responsive">
                    <table class="table-modern">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Patient</th>
                                <th>Test Type</th>
                                <th>Test Name</th>
                                <th>Priority</th>
                                <th>Requested By</th>
                                <th>Date</th>
                                <th>Instructions</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($pendingRequests as $request): ?>
                                <tr>
                                    <td><strong>#<?= esc($request['id']) ?></strong></td>
                                    <td>
                                        <strong style="color: #1e293b;">
                                            <?= esc(ucfirst($request['firstname']) . ' ' . ucfirst($request['lastname'])) ?>
                                        </strong>
                                    </td>
                                    <td><?= esc($request['test_type']) ?></td>
                                    <td><?= esc($request['test_name']) ?></td>
                                    <td>
                                        <span class="badge-modern" style="background: <?= 
                                            $request['priority'] == 'stat' ? '#fee2e2' : 
                                            ($request['priority'] == 'urgent' ? '#fef3c7' : '#d1fae5'); 
                                        ?>; color: <?= 
                                            $request['priority'] == 'stat' ? '#991b1b' : 
                                            ($request['priority'] == 'urgent' ? '#92400e' : '#065f46'); 
                                        ?>;">
                                            <?= esc(ucfirst($request['priority'])) ?>
                                        </span>
                                    </td>
                                    <td><?= esc($request['nurse_name'] ?? 'N/A') ?></td>
                                    <td><?= esc(date('M d, Y', strtotime($request['created_at']))) ?></td>
                                    <td><?= esc(substr($request['instructions'] ?? 'N/A', 0, 50)) ?><?= strlen($request['instructions'] ?? '') > 50 ? '...' : '' ?></td>
                                    <td>
                                        <div style="display: flex; gap: 8px; flex-wrap: wrap;">
                                            <form action="<?= site_url('doctor/lab-requests/confirm/' . $request['id']) ?>" method="post" style="display: inline;">
                                                <?= csrf_field() ?>
                                                <button type="submit" class="btn-modern btn-modern-success" onclick="return confirm('Confirm this lab request? It will be sent to the laboratory.')">
                                                    <i class="fas fa-check"></i>
                                                    Confirm
                                                </button>
                                            </form>
                                            <button type="button" class="btn-modern btn-modern-danger" data-bs-toggle="modal" data-bs-target="#rejectModal<?= $request['id'] ?>">
                                                <i class="fas fa-times"></i>
                                                Reject
                                            </button>
                                        </div>
                                        
                                        <!-- Reject Modal -->
                                        <div class="modal fade" id="rejectModal<?= $request['id'] ?>" tabindex="-1">
                                            <div class="modal-dialog">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title">
                                                            <i class="fas fa-exclamation-triangle"></i>
                                                            Reject Lab Request
                                                        </h5>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                    </div>
                                                    <form action="<?= site_url('doctor/lab-requests/reject/' . $request['id']) ?>" method="post">
                                                        <?= csrf_field() ?>
                                                        <div class="modal-body">
                                                            <p>Are you sure you want to reject this lab request?</p>
                                                            <div class="mb-3">
                                                                <label for="rejection_notes<?= $request['id'] ?>" class="form-label">Rejection Reason (Optional)</label>
                                                                <textarea class="form-control" id="rejection_notes<?= $request['id'] ?>" name="rejection_notes" rows="3" placeholder="Enter reason for rejection..."></textarea>
                                                            </div>
                                                        </div>
                                                        <div class="modal-footer">
                                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                                            <button type="submit" class="btn btn-danger">
                                                                <i class="fas fa-times"></i>
                                                                Reject Request
                                                            </button>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <div class="empty-state">
                    <i class="fas fa-check-circle"></i>
                    <h5>No Pending Requests</h5>
                    <p>All lab requests from nurses have been processed.</p>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Confirmed/In Progress Requests -->
    <div class="modern-card">
        <div class="card-header-modern">
            <h5>
                <i class="fas fa-check-circle"></i>
                Confirmed Requests (In Progress / Completed)
            </h5>
        </div>
        <div class="card-body-modern">
            <?php if (!empty($confirmedRequests)): ?>
                <div class="table-responsive">
                    <table class="table-modern">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Patient</th>
                                <th>Test Type</th>
                                <th>Test Name</th>
                                <th>Priority</th>
                                <th>Requested By</th>
                                <th>Status</th>
                                <th>Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($confirmedRequests as $request): ?>
                                <tr>
                                    <td><strong>#<?= esc($request['id']) ?></strong></td>
                                    <td>
                                        <strong style="color: #1e293b;">
                                            <?= esc(ucfirst($request['firstname']) . ' ' . ucfirst($request['lastname'])) ?>
                                        </strong>
                                    </td>
                                    <td><?= esc($request['test_type']) ?></td>
                                    <td><?= esc($request['test_name']) ?></td>
                                    <td>
                                        <span class="badge-modern" style="background: <?= 
                                            $request['priority'] == 'stat' ? '#fee2e2' : 
                                            ($request['priority'] == 'urgent' ? '#fef3c7' : '#d1fae5'); 
                                        ?>; color: <?= 
                                            $request['priority'] == 'stat' ? '#991b1b' : 
                                            ($request['priority'] == 'urgent' ? '#92400e' : '#065f46'); 
                                        ?>;">
                                            <?= esc(ucfirst($request['priority'])) ?>
                                        </span>
                                    </td>
                                    <td><?= esc($request['nurse_name'] ?? 'N/A') ?></td>
                                    <td>
                                        <span class="badge-modern" style="background: <?= 
                                            $request['status'] == 'completed' ? '#d1fae5' : '#fef3c7'; 
                                        ?>; color: <?= 
                                            $request['status'] == 'completed' ? '#065f46' : '#92400e'; 
                                        ?>;">
                                            <?= esc(ucfirst(str_replace('_', ' ', $request['status']))) ?>
                                        </span>
                                    </td>
                                    <td><?= esc(date('M d, Y', strtotime($request['created_at']))) ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <div class="empty-state">
                    <i class="fas fa-vial"></i>
                    <h5>No Confirmed Requests</h5>
                    <p>No lab requests have been confirmed yet.</p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>
<?= $this->endSection() ?>

