<?= $this->extend('template/header') ?>

<?= $this->section('title') ?>Doctor Orders<?= $this->endSection() ?>

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
    
    .page-header h1 i {
        font-size: 32px;
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
    
    .tabs-container {
        background: white;
        border-radius: 16px;
        box-shadow: 0 2px 12px rgba(0, 0, 0, 0.08);
        border: 1px solid #e5e7eb;
        overflow: hidden;
        margin-bottom: 24px;
    }
    
    .tabs-header {
        display: flex;
        background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
        border-bottom: 2px solid #e5e7eb;
        overflow-x: auto;
    }
    
    .tab-button {
        padding: 16px 24px;
        background: transparent;
        border: none;
        border-bottom: 3px solid transparent;
        font-weight: 600;
        font-size: 14px;
        color: #64748b;
        cursor: pointer;
        transition: all 0.3s ease;
        white-space: nowrap;
    }
    
    .tab-button.active {
        color: #2e7d32;
        border-bottom-color: #2e7d32;
        background: white;
    }
    
    .tab-button:hover {
        background: rgba(46, 125, 50, 0.05);
    }
    
    .tab-content {
        display: none;
        padding: 24px;
    }
    
    .tab-content.active {
        display: block;
    }
    
    .modern-card {
        background: white;
        border-radius: 16px;
        box-shadow: 0 2px 12px rgba(0, 0, 0, 0.08);
        border: 1px solid #e5e7eb;
        overflow: hidden;
        margin-bottom: 24px;
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
    
    .btn-warning {
        background: #f59e0b;
        color: white;
    }
    
    .btn-warning:hover {
        background: #d97706;
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
</style>

<div class="doctor-page-container">
    <div class="page-header">
        <h1>
            <i class="fas fa-prescription"></i>
            Doctor Orders
        </h1>
        <a href="<?= site_url('doctor/orders/create') ?>" class="btn-modern btn-modern-primary">
            <i class="fas fa-plus"></i>
            Create New Order
        </a>
    </div>
    
    <?php if (session()->getFlashdata('success')): ?>
        <div class="alert-modern alert-modern-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle me-2"></i>
            <?= session()->getFlashdata('success') ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>
    
    <?php if (isset($showAdmitInfo) && $showAdmitInfo && isset($consultation)): ?>
        <div class="alert-modern" style="background: linear-gradient(135deg, #fef3c7 0%, #fde68a 100%); color: #92400e; border-left: 4px solid #f59e0b;">
            <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 16px;">
                <div>
                    <i class="fas fa-info-circle me-2"></i>
                    <strong>Patient Marked for Admission</strong>
                    <p style="margin: 8px 0 0 0; font-size: 14px; opacity: 0.9;">
                        This patient has been marked for admission. A Nurse or Receptionist will process the admission and assign a room/bed.
                    </p>
                </div>
            </div>
        </div>
    <?php endif; ?>
    
    <?php if (session()->getFlashdata('error')): ?>
        <div class="alert-modern alert-modern-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-circle me-2"></i>
            <?= session()->getFlashdata('error') ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <div class="tabs-container">
        <div class="tabs-header">
            <button class="tab-button active" onclick="showTab('all')">
                <i class="fas fa-list"></i>
                All Orders (<?= count($allOrders) ?>)
            </button>
            <button class="tab-button" onclick="showTab('pending')">
                <i class="fas fa-clock"></i>
                Pending (<?= count($pendingOrders) ?>)
            </button>
            <button class="tab-button" onclick="showTab('in_progress')">
                <i class="fas fa-spinner"></i>
                In Progress (<?= count($inProgressOrders) ?>)
            </button>
            <button class="tab-button" onclick="showTab('completed')">
                <i class="fas fa-check-circle"></i>
                Completed (<?= count($completedOrders) ?>)
            </button>
            <button class="tab-button" onclick="showTab('cancelled')">
                <i class="fas fa-times-circle"></i>
                Cancelled (<?= count($cancelledOrders) ?>)
            </button>
        </div>

        <!-- All Orders Tab -->
        <div id="tab-all" class="tab-content active">
            <?php if (!empty($allOrders)): ?>
                <div class="table-responsive">
                    <table class="table-modern">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Patient</th>
                                <th>Assigned Nurse</th>
                                <th>Order Type</th>
                                <th>Description</th>
                                <th>Status</th>
                                <th>Pharmacy Status</th>
                                <th>Created</th>
                                <th>Completed By</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($allOrders as $order): ?>
                                <tr>
                                    <td><strong>#<?= esc($order['id']) ?></strong></td>
                                    <td>
                                        <strong style="color: #1e293b;">
                                            <?php 
                                                $firstName = !empty($order['firstname']) ? ucfirst($order['firstname']) : 'Unknown';
                                                $lastName = !empty($order['lastname']) ? ucfirst($order['lastname']) : 'Patient';
                                                echo esc($firstName . ' ' . $lastName);
                                            ?>
                                        </strong>
                                    </td>
                                    <td>
                                        <span style="color: #0288d1; font-weight: 600;">
                                            <i class="fas fa-user-nurse me-1"></i>
                                            <?= esc($order['nurse_name'] ?? 'N/A') ?>
                                        </span>
                                    </td>
                                    <td>
                                        <span class="badge-modern" style="background: #e0f2fe; color: #0369a1;">
                                            <?= esc(ucfirst(str_replace('_', ' ', $order['order_type']))) ?>
                                        </span>
                                    </td>
                                    <td><?= esc(substr($order['order_description'], 0, 60)) ?><?= strlen($order['order_description']) > 60 ? '...' : '' ?></td>
                                    <td>
                                        <span class="badge-modern" style="background: <?= 
                                            $order['status'] == 'completed' ? '#d1fae5' : 
                                            ($order['status'] == 'in_progress' ? '#fef3c7' : 
                                            ($order['status'] == 'cancelled' ? '#fee2e2' : '#dbeafe')); 
                                        ?>; color: <?= 
                                            $order['status'] == 'completed' ? '#065f46' : 
                                            ($order['status'] == 'in_progress' ? '#92400e' : 
                                            ($order['status'] == 'cancelled' ? '#991b1b' : '#1e40af')); 
                                        ?>;">
                                            <?php if ($order['order_type'] === 'medication' && $order['status'] == 'completed'): ?>
                                                <?php if (($order['purchase_location'] ?? '') === 'outside'): ?>
                                                    <i class="fas fa-prescription"></i> Prescribed
                                                <?php else: ?>
                                                    <i class="fas fa-check-circle"></i> Administered
                                                <?php endif; ?>
                                            <?php else: ?>
                                                <?= esc(ucfirst(str_replace('_', ' ', $order['status']))) ?>
                                            <?php endif; ?>
                                        </span>
                                    </td>
                                    <td>
                                        <?php if ($order['order_type'] === 'medication' && ($order['purchase_location'] ?? '') === 'outside'): ?>
                                            <span class="badge-modern" style="background: #f1f5f9; color: #64748b;">
                                                <i class="fas fa-store"></i> N/A
                                            </span>
                                            <small style="display: block; margin-top: 4px; color: #94a3b8; font-size: 11px;">Outside Purchase</small>
                                        <?php elseif ($order['order_type'] === 'medication'): ?>
                                            <span class="badge-modern" style="background: <?= 
                                                ($order['pharmacy_status'] ?? 'pending') == 'dispensed' ? '#d1fae5' : 
                                                (($order['pharmacy_status'] ?? 'pending') == 'prepared' ? '#fef3c7' : 
                                                (($order['pharmacy_status'] ?? 'pending') == 'approved' ? '#dbeafe' : '#fee2e2')); 
                                            ?>; color: <?= 
                                                ($order['pharmacy_status'] ?? 'pending') == 'dispensed' ? '#065f46' : 
                                                (($order['pharmacy_status'] ?? 'pending') == 'prepared' ? '#92400e' : 
                                                (($order['pharmacy_status'] ?? 'pending') == 'approved' ? '#1e40af' : '#991b1b')); 
                                            ?>;">
                                                <?= esc(ucfirst($order['pharmacy_status'] ?? 'Pending')) ?>
                                            </span>
                                        <?php else: ?>
                                            <span style="color: #94a3b8;">—</span>
                                        <?php endif; ?>
                                    </td>
                                    <td><?= esc(date('M d, Y', strtotime($order['created_at']))) ?></td>
                                    <td>
                                        <?php if ($order['order_type'] === 'medication' && ($order['status'] ?? 'pending') === 'completed'): ?>
                                            <div>
                                                <strong style="color: #065f46;">
                                                    <i class="fas fa-check-circle"></i> Administered
                                                </strong>
                                                <br>
                                                <small style="color: #64748b;">
                                                    By: <?= esc($order['completed_by_name'] ?? 'Nurse') ?>
                                                </small>
                                                <?php if ($order['completed_at']): ?>
                                                <br>
                                                <small style="color: #64748b;">
                                                    At: <?= date('M d, Y h:i A', strtotime($order['completed_at'])) ?>
                                                </small>
                                                <?php endif; ?>
                                            </div>
                                        <?php else: ?>
                                            <?= esc($order['completed_by_name'] ?? 'N/A') ?>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <div style="display: flex; gap: 8px; flex-wrap: wrap;">
                                            <a href="<?= site_url('doctor/orders/view/' . $order['id']) ?>" class="btn-sm-modern btn-info">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <?php if (in_array($order['status'], ['pending', 'in_progress']) && $order['order_type'] !== 'medication'): ?>
                                                <a href="<?= site_url('doctor/orders/edit/' . $order['id']) ?>" class="btn-sm-modern btn-warning">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <div class="empty-state">
                    <i class="fas fa-prescription"></i>
                    <h5>No Orders Found</h5>
                    <p>You haven't created any medical orders yet.</p>
                    <a href="<?= site_url('doctor/orders/create') ?>" class="btn-modern btn-modern-primary" style="margin-top: 16px;">
                        <i class="fas fa-plus"></i>
                        Create First Order
                    </a>
                </div>
            <?php endif; ?>
        </div>

        <!-- Pending Orders Tab -->
        <div id="tab-pending" class="tab-content">
            <?php if (!empty($pendingOrders)): ?>
                <div class="table-responsive">
                    <table class="table-modern">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Patient</th>
                                <th>Assigned Nurse</th>
                                <th>Order Type</th>
                                <th>Description</th>
                                <th>Created</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($pendingOrders as $order): ?>
                                <tr>
                                    <td><strong>#<?= esc($order['id']) ?></strong></td>
                                    <td><strong><?= esc(ucfirst($order['firstname']) . ' ' . ucfirst($order['lastname'])) ?></strong></td>
                                    <td>
                                        <span style="color: #0288d1; font-weight: 600;">
                                            <i class="fas fa-user-nurse me-1"></i>
                                            <?= esc($order['nurse_name'] ?? 'N/A') ?>
                                        </span>
                                    </td>
                                    <td><span class="badge-modern" style="background: #e0f2fe; color: #0369a1;"><?= esc(ucfirst(str_replace('_', ' ', $order['order_type']))) ?></span></td>
                                    <td><?= esc(substr($order['order_description'], 0, 60)) ?><?= strlen($order['order_description']) > 60 ? '...' : '' ?></td>
                                    <td><?= esc(date('M d, Y', strtotime($order['created_at']))) ?></td>
                                    <td>
                                        <div style="display: flex; gap: 8px;">
                                            <a href="<?= site_url('doctor/orders/view/' . $order['id']) ?>" class="btn-sm-modern btn-info">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <?php if ($order['order_type'] !== 'medication'): ?>
                                                <a href="<?= site_url('doctor/orders/edit/' . $order['id']) ?>" class="btn-sm-modern btn-warning">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                            <?php endif; ?>
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
                    <h5>No Pending Orders</h5>
                    <p>All orders have been processed.</p>
                </div>
            <?php endif; ?>
        </div>

        <!-- In Progress Orders Tab -->
        <div id="tab-in_progress" class="tab-content">
            <?php if (!empty($inProgressOrders)): ?>
                <div class="table-responsive">
                    <table class="table-modern">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Patient</th>
                                <th>Assigned Nurse</th>
                                <th>Order Type</th>
                                <th>Description</th>
                                <th>Created</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($inProgressOrders as $order): ?>
                                <tr>
                                    <td><strong>#<?= esc($order['id']) ?></strong></td>
                                    <td><strong><?= esc(ucfirst($order['firstname']) . ' ' . ucfirst($order['lastname'])) ?></strong></td>
                                    <td>
                                        <span style="color: #0288d1; font-weight: 600;">
                                            <i class="fas fa-user-nurse me-1"></i>
                                            <?= esc($order['nurse_name'] ?? 'N/A') ?>
                                        </span>
                                    </td>
                                    <td><span class="badge-modern" style="background: #e0f2fe; color: #0369a1;"><?= esc(ucfirst(str_replace('_', ' ', $order['order_type']))) ?></span></td>
                                    <td><?= esc(substr($order['order_description'], 0, 60)) ?><?= strlen($order['order_description']) > 60 ? '...' : '' ?></td>
                                    <td><?= esc(date('M d, Y', strtotime($order['created_at']))) ?></td>
                                    <td>
                                        <a href="<?= site_url('doctor/orders/view/' . $order['id']) ?>" class="btn-sm-modern btn-info">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <div class="empty-state">
                    <i class="fas fa-spinner"></i>
                    <h5>No Orders In Progress</h5>
                    <p>No orders are currently being executed.</p>
                </div>
            <?php endif; ?>
        </div>

        <!-- Completed Orders Tab -->
        <div id="tab-completed" class="tab-content">
            <?php if (!empty($completedOrders)): ?>
                <div class="table-responsive">
                    <table class="table-modern">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Patient</th>
                                <th>Assigned Nurse</th>
                                <th>Order Type</th>
                                <th>Description</th>
                                <th>Completed By</th>
                                <th>Completed At</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($completedOrders as $order): ?>
                                <tr>
                                    <td><strong>#<?= esc($order['id']) ?></strong></td>
                                    <td><strong><?= esc(ucfirst($order['firstname']) . ' ' . ucfirst($order['lastname'])) ?></strong></td>
                                    <td>
                                        <span style="color: #0288d1; font-weight: 600;">
                                            <i class="fas fa-user-nurse me-1"></i>
                                            <?= esc($order['nurse_name'] ?? 'N/A') ?>
                                        </span>
                                    </td>
                                    <td><span class="badge-modern" style="background: #e0f2fe; color: #0369a1;"><?= esc(ucfirst(str_replace('_', ' ', $order['order_type']))) ?></span></td>
                                    <td><?= esc(substr($order['order_description'], 0, 60)) ?><?= strlen($order['order_description']) > 60 ? '...' : '' ?></td>
                                    <td><?= esc($order['completed_by_name'] ?? 'N/A') ?></td>
                                    <td><?= esc($order['completed_at'] ? date('M d, Y h:i A', strtotime($order['completed_at'])) : 'N/A') ?></td>
                                    <td>
                                        <a href="<?= site_url('doctor/orders/view/' . $order['id']) ?>" class="btn-sm-modern btn-info">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <div class="empty-state">
                    <i class="fas fa-check-circle"></i>
                    <h5>No Completed Orders</h5>
                    <p>No orders have been completed yet.</p>
                </div>
            <?php endif; ?>
        </div>

        <!-- Cancelled Orders Tab -->
        <div id="tab-cancelled" class="tab-content">
            <?php if (!empty($cancelledOrders)): ?>
                <div class="table-responsive">
                    <table class="table-modern">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Patient</th>
                                <th>Assigned Nurse</th>
                                <th>Order Type</th>
                                <th>Description</th>
                                <th>Cancelled</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($cancelledOrders as $order): ?>
                                <tr>
                                    <td><strong>#<?= esc($order['id']) ?></strong></td>
                                    <td><strong><?= esc(ucfirst($order['firstname']) . ' ' . ucfirst($order['lastname'])) ?></strong></td>
                                    <td>
                                        <span style="color: #0288d1; font-weight: 600;">
                                            <i class="fas fa-user-nurse me-1"></i>
                                            <?= esc($order['nurse_name'] ?? 'N/A') ?>
                                        </span>
                                    </td>
                                    <td><span class="badge-modern" style="background: #e0f2fe; color: #0369a1;"><?= esc(ucfirst(str_replace('_', ' ', $order['order_type']))) ?></span></td>
                                    <td><?= esc(substr($order['order_description'], 0, 60)) ?><?= strlen($order['order_description']) > 60 ? '...' : '' ?></td>
                                    <td><?= esc(date('M d, Y', strtotime($order['updated_at']))) ?></td>
                                    <td>
                                        <a href="<?= site_url('doctor/orders/view/' . $order['id']) ?>" class="btn-sm-modern btn-info">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <div class="empty-state">
                    <i class="fas fa-times-circle"></i>
                    <h5>No Cancelled Orders</h5>
                    <p>No orders have been cancelled.</p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<script>
function showTab(tabName) {
    // Hide all tabs
    document.querySelectorAll('.tab-content').forEach(tab => {
        tab.classList.remove('active');
    });
    
    // Remove active class from all buttons
    document.querySelectorAll('.tab-button').forEach(btn => {
        btn.classList.remove('active');
    });
    
    // Show selected tab
    document.getElementById('tab-' + tabName).classList.add('active');
    
    // Add active class to clicked button
    event.target.classList.add('active');
}
</script>
<?= $this->endSection() ?>

