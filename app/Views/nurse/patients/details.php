<?= $this->extend('template/header') ?>

<?= $this->section('title') ?>Patient Details<?= $this->endSection() ?>

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
        overflow: hidden;
        margin-bottom: 24px;
    }
    
    .card-header-modern {
        background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
        padding: 20px 24px;
        border-bottom: 2px solid #e5e7eb;
        display: flex;
        justify-content: space-between;
        align-items: center;
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
        padding: 32px;
    }
    
    .info-section {
        background: #f8fafc;
        padding: 24px;
        border-radius: 12px;
        margin-bottom: 24px;
    }
    
    .info-section-title {
        font-size: 16px;
        font-weight: 700;
        color: #0288d1;
        margin-bottom: 20px;
        display: flex;
        align-items: center;
        gap: 10px;
    }
    
    .info-section-title::before {
        content: '';
        width: 4px;
        height: 20px;
        background: #0288d1;
        border-radius: 2px;
    }
    
    .info-table {
        width: 100%;
    }
    
    .info-table tr {
        border-bottom: 1px solid #e5e7eb;
    }
    
    .info-table tr:last-child {
        border-bottom: none;
    }
    
    .info-table td {
        padding: 12px 0;
        vertical-align: top;
    }
    
    .info-table td:first-child {
        width: 180px;
        font-weight: 600;
        color: #64748b;
    }
    
    .info-table td:last-child {
        color: #1e293b;
        font-weight: 500;
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
        background: linear-gradient(135deg, #0288d1 0%, #03a9f4 100%);
        color: white;
        box-shadow: 0 4px 12px rgba(2, 136, 209, 0.3);
    }
    
    .btn-modern-secondary {
        background: #64748b;
        color: white;
    }
    
    .btn-modern-success {
        background: #10b981;
        color: white;
    }
    
    .btn-modern-warning {
        background: #f59e0b;
        color: white;
    }
    
    .btn-sm-modern {
        padding: 6px 12px;
        font-size: 13px;
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
            <i class="fas fa-user-circle"></i>
            Patient Details
        </h1>
        <div style="display: flex; gap: 12px; flex-wrap: wrap;">
            <a href="<?= site_url('nurse/patients/view') ?>" class="btn-modern btn-modern-secondary">
                <i class="fas fa-arrow-left"></i>
                Back to Patients
            </a>
        </div>
    </div>
    
    <!-- Patient Information -->
    <div class="modern-card">
        <div class="card-body-modern">
            <div class="row">
                <div class="col-md-6">
                    <div class="info-section">
                        <div class="info-section-title">
                            <i class="fas fa-user"></i>
                            Personal Information
                        </div>
                        <table class="info-table">
                            <tr>
                                <td>Patient ID:</td>
                                <td><strong>#<?= esc($patient['id']) ?></strong></td>
                            </tr>
                            <tr>
                                <td>Full Name:</td>
                                <td><strong><?= esc(ucfirst($patient['firstname']) . ' ' . ucfirst($patient['lastname'])) ?></strong></td>
                            </tr>
                            <tr>
                                <td>Birthdate:</td>
                                <td><?= esc(date('F d, Y', strtotime($patient['birthdate']))) ?></td>
                            </tr>
                            <tr>
                                <td>Gender:</td>
                                <td>
                                    <span class="badge-modern" style="background: #e0f2fe; color: #0369a1;">
                                        <?= esc(ucfirst($patient['gender'])) ?>
                                    </span>
                                </td>
                            </tr>
                            <tr>
                                <td>Contact:</td>
                                <td><?= esc($patient['contact'] ?? 'N/A') ?></td>
                            </tr>
                            <tr>
                                <td>Address:</td>
                                <td><?= esc($patient['address'] ?? 'N/A') ?></td>
                            </tr>
                        </table>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="info-section">
                        <div class="info-section-title">
                            <i class="fas fa-tools"></i>
                            Quick Actions
                        </div>
                        <div style="display: flex; flex-direction: column; gap: 12px;">
                            <a href="<?= site_url('nurse/patients/add-vitals/' . $patient['id']) ?>" class="btn-modern btn-modern-success">
                                <i class="fas fa-heartbeat"></i>
                                Record Vital Signs
                            </a>
                            <a href="<?= site_url('nurse/patients/add-note/' . $patient['id']) ?>" class="btn-modern btn-modern-warning">
                                <i class="fas fa-sticky-note"></i>
                                Add Progress Note
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Vital Signs -->
    <div class="modern-card">
        <div class="card-header-modern">
            <h5>
                <i class="fas fa-heartbeat"></i>
                Vital Signs History
            </h5>
            <a href="<?= site_url('nurse/patients/add-vitals/' . $patient['id']) ?>" class="btn-modern btn-modern-success btn-sm-modern">
                <i class="fas fa-plus"></i>
                Add Vitals
            </a>
        </div>
        <div class="card-body-modern">
            <?php if (!empty($vitals)): ?>
                <div class="table-responsive">
                    <table class="table-modern">
                        <thead>
                            <tr>
                                <th>Date & Time</th>
                                <th>BP</th>
                                <th>HR</th>
                                <th>Temp</th>
                                <th>O2 Sat</th>
                                <th>RR</th>
                                <th>Weight</th>
                                <th>Height</th>
                                <th>Notes</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($vitals as $vital): ?>
                                <tr>
                                    <td><?= esc(date('M d, Y h:i A', strtotime($vital['recorded_at'] ?? $vital['created_at']))) ?></td>
                                    <td>
                                        <?php if ($vital['blood_pressure_systolic'] && $vital['blood_pressure_diastolic']): ?>
                                            <?= esc($vital['blood_pressure_systolic']) ?>/<?= esc($vital['blood_pressure_diastolic']) ?>
                                        <?php else: ?>
                                            N/A
                                        <?php endif; ?>
                                    </td>
                                    <td><?= esc($vital['heart_rate'] ?? 'N/A') ?></td>
                                    <td><?= esc($vital['temperature'] ? $vital['temperature'] . '°C' : 'N/A') ?></td>
                                    <td><?= esc($vital['oxygen_saturation'] ? $vital['oxygen_saturation'] . '%' : 'N/A') ?></td>
                                    <td><?= esc($vital['respiratory_rate'] ?? 'N/A') ?></td>
                                    <td><?= esc($vital['weight'] ? $vital['weight'] . ' kg' : 'N/A') ?></td>
                                    <td><?= esc($vital['height'] ? $vital['height'] . ' cm' : 'N/A') ?></td>
                                    <td><?= esc(substr($vital['notes'] ?? 'N/A', 0, 50)) ?><?= strlen($vital['notes'] ?? '') > 50 ? '...' : '' ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <div class="empty-state">
                    <i class="fas fa-heartbeat"></i>
                    <p style="margin: 0; color: #64748b;">No vital signs recorded yet.</p>
                    <a href="<?= site_url('nurse/patients/add-vitals/' . $patient['id']) ?>" class="btn-modern btn-modern-success" style="margin-top: 16px;">
                        <i class="fas fa-plus"></i>
                        Record First Vital Signs
                    </a>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Nurse Notes -->
    <div class="modern-card">
        <div class="card-header-modern">
            <h5>
                <i class="fas fa-sticky-note"></i>
                Nurse Notes
            </h5>
            <a href="<?= site_url('nurse/patients/add-note/' . $patient['id']) ?>" class="btn-modern btn-modern-warning btn-sm-modern">
                <i class="fas fa-plus"></i>
                Add Note
            </a>
        </div>
        <div class="card-body-modern">
            <?php if (!empty($notes)): ?>
                <div class="table-responsive">
                    <table class="table-modern">
                        <thead>
                            <tr>
                                <th>Date & Time</th>
                                <th>Type</th>
                                <th>Priority</th>
                                <th>Note</th>
                                <th>Recorded By</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($notes as $note): ?>
                                <tr>
                                    <td><?= esc(date('M d, Y h:i A', strtotime($note['created_at']))) ?></td>
                                    <td>
                                        <span class="badge-modern" style="background: #e0f2fe; color: #0369a1;">
                                            <?= esc(ucfirst($note['note_type'])) ?>
                                        </span>
                                    </td>
                                    <td>
                                        <span class="badge-modern" style="background: <?= 
                                            $note['priority'] == 'urgent' ? '#fee2e2' : 
                                            ($note['priority'] == 'high' ? '#fef3c7' : '#d1fae5'); 
                                        ?>; color: <?= 
                                            $note['priority'] == 'urgent' ? '#991b1b' : 
                                            ($note['priority'] == 'high' ? '#92400e' : '#065f46'); 
                                        ?>;">
                                            <?= esc(ucfirst($note['priority'])) ?>
                                        </span>
                                    </td>
                                    <td><?= esc(substr($note['note'], 0, 100)) ?><?= strlen($note['note']) > 100 ? '...' : '' ?></td>
                                    <td><?= esc($note['nurse_name'] ?? 'N/A') ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <div class="empty-state">
                    <i class="fas fa-sticky-note"></i>
                    <p style="margin: 0; color: #64748b;">No nurse notes recorded yet.</p>
                    <a href="<?= site_url('nurse/patients/add-note/' . $patient['id']) ?>" class="btn-modern btn-modern-warning" style="margin-top: 16px;">
                        <i class="fas fa-plus"></i>
                        Add First Note
                    </a>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Doctor Orders -->
    <div class="modern-card">
        <div class="card-header-modern">
            <h5>
                <i class="fas fa-prescription"></i>
                Doctor Orders
            </h5>
        </div>
        <div class="card-body-modern">
            <?php if (!empty($orders)): ?>
                <div class="table-responsive">
                    <table class="table-modern">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Order Type</th>
                                <th>Description</th>
                                <th>Doctor</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($orders as $order): ?>
                                <tr>
                                    <td><?= esc(date('M d, Y', strtotime($order['created_at']))) ?></td>
                                    <td>
                                        <span class="badge-modern" style="background: #e0f2fe; color: #0369a1;">
                                            <?= esc(ucfirst(str_replace('_', ' ', $order['order_type']))) ?>
                                        </span>
                                    </td>
                                    <td><?= esc(substr($order['order_description'], 0, 80)) ?><?= strlen($order['order_description']) > 80 ? '...' : '' ?></td>
                                    <td><?= esc($order['doctor_name'] ?? 'N/A') ?></td>
                                    <td>
                                        <span class="badge-modern" style="background: <?= 
                                            $order['status'] == 'completed' ? '#d1fae5' : 
                                            ($order['status'] == 'in_progress' ? '#fef3c7' : '#fee2e2'); 
                                        ?>; color: <?= 
                                            $order['status'] == 'completed' ? '#065f46' : 
                                            ($order['status'] == 'in_progress' ? '#92400e' : '#991b1b'); 
                                        ?>;">
                                            <?= esc(ucfirst(str_replace('_', ' ', $order['status']))) ?>
                                        </span>
                                    </td>
                                    <td>
                                        <?php if ($order['status'] !== 'completed'): ?>
                                            <?php if ($order['order_type'] === 'lab_test'): ?>
                                                <?php 
                                                // For lab_test orders, only show "Mark Complete" if lab staff has completed it
                                                $labRequestStatus = $order['lab_request_status'] ?? 'not_found';
                                                $hasLabResult = $order['has_lab_result'] ?? false;
                                                $canMarkComplete = ($labRequestStatus === 'completed' && $hasLabResult);
                                                ?>
                                                <?php if ($canMarkComplete): ?>
                                                    <form action="<?= site_url('nurse/patients/update-order-status/' . $order['id']) ?>" method="post" style="display: inline;">
                                                        <?= csrf_field() ?>
                                                        <input type="hidden" name="status" value="completed">
                                                        <button type="submit" class="btn-modern btn-modern-success btn-sm-modern" onclick="return confirm('Mark this lab test order as completed? Lab result is available.')">
                                                            <i class="fas fa-check"></i>
                                                            Mark Complete
                                                        </button>
                                                    </form>
                                                <?php else: ?>
                                                    <div style="padding: 8px 12px; background: #fef3c7; border-radius: 6px; font-size: 12px; color: #92400e;">
                                                        <i class="fas fa-clock"></i>
                                                        <?php if ($labRequestStatus === 'not_found'): ?>
                                                            Waiting for lab request
                                                        <?php elseif ($labRequestStatus !== 'completed'): ?>
                                                            Lab status: <?= ucfirst(str_replace('_', ' ', $labRequestStatus)) ?>
                                                        <?php elseif (!$hasLabResult): ?>
                                                            Waiting for lab results
                                                        <?php endif; ?>
                                                    </div>
                                                <?php endif; ?>
                                            <?php else: ?>
                                                <!-- For non-lab_test orders, show mark complete button normally -->
                                                <form action="<?= site_url('nurse/patients/update-order-status/' . $order['id']) ?>" method="post" style="display: inline;">
                                                    <?= csrf_field() ?>
                                                    <input type="hidden" name="status" value="completed">
                                                    <button type="submit" class="btn-modern btn-modern-success btn-sm-modern" onclick="return confirm('Mark this order as completed?')">
                                                        <i class="fas fa-check"></i>
                                                        Mark Complete
                                                    </button>
                                                </form>
                                            <?php endif; ?>
                                        <?php else: ?>
                                            <span style="color: #10b981; font-weight: 600;">
                                                <i class="fas fa-check-circle"></i> Completed
                                            </span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <div class="empty-state">
                    <i class="fas fa-prescription"></i>
                    <p style="margin: 0; color: #64748b;">No doctor orders found for this patient.</p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>
<?= $this->endSection() ?>

