<?= $this->extend('template/header') ?>

<?= $this->section('title') ?>Order Details<?= $this->endSection() ?>

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
        border-left: 4px solid #2e7d32;
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
    
    .audit-trail {
        margin-top: 32px;
    }
    
    .audit-item {
        padding: 16px;
        background: #f8fafc;
        border-radius: 12px;
        border-left: 4px solid #0288d1;
        margin-bottom: 12px;
    }
    
    .audit-item-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 8px;
    }
    
    .audit-item-status {
        font-weight: 700;
        color: #0288d1;
    }
    
    .audit-item-date {
        font-size: 12px;
        color: #64748b;
    }
    
    .audit-item-notes {
        color: #475569;
        font-size: 14px;
        margin-top: 8px;
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
    
    .btn-modern-warning {
        background: #f59e0b;
        color: white;
    }
    
    .btn-modern-warning:hover {
        background: #d97706;
        color: white;
    }
</style>

<div class="doctor-page-container">
    <div class="page-header">
        <h1>
            <i class="fas fa-prescription"></i>
            Order Details #<?= esc($order['id']) ?>
        </h1>
        <div style="display: flex; gap: 12px;">
            <?php if (in_array($order['status'], ['pending', 'in_progress']) && $order['order_type'] !== 'medication'): ?>
                <a href="<?= site_url('doctor/orders/edit/' . $order['id']) ?>" class="btn-modern btn-modern-warning">
                    <i class="fas fa-edit"></i>
                    Edit Order
                </a>
            <?php endif; ?>
            <?php if ($order['order_type'] === 'medication'): ?>
                <span style="padding: 10px 20px; background: #fee2e2; color: #991b1b; border-radius: 10px; font-size: 14px; font-weight: 600;">
                    <i class="fas fa-lock"></i> Read-Only (Managed by Pharmacy)
                </span>
            <?php endif; ?>
            <a href="<?= site_url('doctor/orders') ?>" class="btn-modern btn-modern-secondary">
                <i class="fas fa-arrow-left"></i>
                Back to Orders
            </a>
        </div>
    </div>

    <div class="modern-card">
        <div class="info-grid">
            <div class="info-item">
                <div class="info-label">Patient</div>
                <div class="info-value">
                    <?= esc(ucfirst($order['firstname']) . ' ' . ucfirst($order['lastname'])) ?>
                </div>
            </div>
            
            <div class="info-item">
                <div class="info-label">Assigned Nurse</div>
                <div class="info-value">
                    <i class="fas fa-user-nurse" style="color: #0288d1; margin-right: 8px;"></i>
                    <?= esc($order['nurse_name'] ?? 'Not Assigned') ?>
                </div>
            </div>
            
            <div class="info-item">
                <div class="info-label">Order Type</div>
                <div class="info-value">
                    <span class="badge-modern" style="background: #e0f2fe; color: #0369a1;">
                        <?= esc(ucfirst(str_replace('_', ' ', $order['order_type']))) ?>
                    </span>
                </div>
            </div>
            
            <div class="info-item">
                <div class="info-label">Status</div>
                <div class="info-value">
                    <span class="badge-modern" style="background: <?= 
                        $order['status'] == 'completed' ? '#d1fae5' : 
                        ($order['status'] == 'in_progress' ? '#fef3c7' : 
                        ($order['status'] == 'cancelled' ? '#fee2e2' : '#dbeafe')); 
                    ?>; color: <?= 
                        $order['status'] == 'completed' ? '#065f46' : 
                        ($order['status'] == 'in_progress' ? '#92400e' : 
                        ($order['status'] == 'cancelled' ? '#991b1b' : '#1e40af')); 
                    ?>;">
                        <?= esc(ucfirst(str_replace('_', ' ', $order['status']))) ?>
                    </span>
                    <small style="display: block; margin-top: 4px; color: #64748b; font-weight: normal;">(Read-Only)</small>
                </div>
            </div>
            
            <?php if ($order['order_type'] === 'medication'): ?>
            <div class="info-item">
                <div class="info-label">Pharmacy Status</div>
                <div class="info-value">
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
                    <small style="display: block; margin-top: 4px; color: #64748b; font-weight: normal;">(Managed by Pharmacy)</small>
                </div>
            </div>
            <?php endif; ?>
            
            <div class="info-item">
                <div class="info-label">Created Date</div>
                <div class="info-value"><?= esc(date('M d, Y h:i A', strtotime($order['created_at']))) ?></div>
            </div>
            
            <?php if ($order['frequency']): ?>
            <div class="info-item">
                <div class="info-label">Frequency</div>
                <div class="info-value"><?= esc($order['frequency']) ?></div>
            </div>
            <?php endif; ?>
            
            <?php if ($order['start_date']): ?>
            <div class="info-item">
                <div class="info-label">Start Date</div>
                <div class="info-value"><?= esc(date('M d, Y', strtotime($order['start_date']))) ?></div>
            </div>
            <?php endif; ?>
            
            <?php if ($order['end_date']): ?>
            <div class="info-item">
                <div class="info-label">End Date</div>
                <div class="info-value"><?= esc(date('M d, Y', strtotime($order['end_date']))) ?></div>
            </div>
            <?php endif; ?>
            
            <?php if ($order['order_type'] === 'medication' && ($order['status'] ?? 'pending') === 'completed'): ?>
            <div class="info-item" style="border-left-color: #10b981; background: #d1fae5;">
                <div class="info-label">Administered Status</div>
                <div class="info-value" style="color: #065f46;">
                    <i class="fas fa-check-circle"></i> <strong>ADMINISTERED TO PATIENT</strong>
                </div>
            </div>
            <?php endif; ?>
            
            <?php if ($order['completed_by_name']): ?>
            <div class="info-item">
                <div class="info-label"><?= $order['order_type'] === 'medication' ? 'Administered By' : 'Completed By' ?></div>
                <div class="info-value">
                    <i class="fas fa-user-nurse" style="color: #0288d1; margin-right: 8px;"></i>
                    <?= esc($order['completed_by_name']) ?>
                </div>
            </div>
            <?php endif; ?>
            
            <?php if ($order['completed_at']): ?>
            <div class="info-item">
                <div class="info-label"><?= $order['order_type'] === 'medication' ? 'Administered At' : 'Completed At' ?></div>
                <div class="info-value" style="color: <?= $order['order_type'] === 'medication' ? '#065f46' : '#1e293b' ?>; font-weight: 700;">
                    <i class="fas fa-clock" style="margin-right: 8px;"></i>
                    <?= esc(date('M d, Y h:i A', strtotime($order['completed_at']))) ?>
                </div>
            </div>
            <?php endif; ?>
        </div>
        
        <?php if ($order['order_type'] === 'medication' && !empty($order['medicine_name'])): ?>
        <div class="section-title">
            <i class="fas fa-pills"></i>
            Medication Details
        </div>
        <div style="padding: 16px; background: #f8fafc; border-radius: 12px; margin-bottom: 24px;">
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 16px;">
                <div>
                    <div style="font-size: 12px; color: #64748b; font-weight: 600; margin-bottom: 4px;">Medicine Name</div>
                    <div style="font-size: 16px; font-weight: 700; color: #1e293b;"><?= esc($order['medicine_name']) ?></div>
                </div>
                <?php if (!empty($order['dosage'])): ?>
                <div>
                    <div style="font-size: 12px; color: #64748b; font-weight: 600; margin-bottom: 4px;">Dosage</div>
                    <div style="font-size: 16px; font-weight: 700; color: #1e293b;"><?= esc($order['dosage']) ?></div>
                </div>
                <?php endif; ?>
                <?php if (!empty($order['frequency'])): ?>
                <div>
                    <div style="font-size: 12px; color: #64748b; font-weight: 600; margin-bottom: 4px;">Frequency</div>
                    <div style="font-size: 16px; font-weight: 700; color: #1e293b;"><?= esc($order['frequency']) ?></div>
                </div>
                <?php endif; ?>
                <?php if (!empty($order['duration'])): ?>
                <div>
                    <div style="font-size: 12px; color: #64748b; font-weight: 600; margin-bottom: 4px;">Duration</div>
                    <div style="font-size: 16px; font-weight: 700; color: #1e293b;"><?= esc($order['duration']) ?></div>
                </div>
                <?php endif; ?>
            </div>
        </div>
        <?php endif; ?>
        
        <div class="section-title">
            <i class="fas fa-file-medical"></i>
            Order Description
        </div>
        <div style="padding: 16px; background: #f8fafc; border-radius: 12px; color: #475569; line-height: 1.6;">
            <?= nl2br(esc($order['order_description'])) ?>
        </div>
        
        <?php if ($order['instructions']): ?>
        <div class="section-title" style="margin-top: 32px;">
            <i class="fas fa-clipboard-list"></i>
            Instructions
        </div>
        <div style="padding: 16px; background: #f8fafc; border-radius: 12px; color: #475569; line-height: 1.6;">
            <?= nl2br(esc($order['instructions'])) ?>
        </div>
        <?php endif; ?>
        
    <?php if (!empty($order['remarks'])): ?>
    <div class="section-title" style="margin-top: 32px;">
        <i class="fas fa-sticky-note"></i>
        Remarks
    </div>
    <div style="padding: 16px; background: #f8fafc; border-radius: 12px; color: #475569; line-height: 1.6;">
        <?= nl2br(esc($order['remarks'])) ?>
    </div>
    <?php endif; ?>

    <!-- Administration Status (for medication orders) -->
    <?php if ($order['order_type'] === 'medication' && ($order['status'] ?? 'pending') === 'completed'): ?>
    <div class="section-title" style="margin-top: 32px; color: #065f46;">
        <i class="fas fa-check-circle"></i>
        Medication Administration
    </div>
    <div style="padding: 20px; background: linear-gradient(135deg, #d1fae5 0%, #a7f3d0 100%); border-radius: 12px; border-left: 4px solid #10b981;">
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 16px;">
            <div>
                <div style="font-size: 12px; color: #065f46; font-weight: 600; margin-bottom: 4px;">Status</div>
                <div style="font-size: 18px; font-weight: 700; color: #065f46;">
                    <i class="fas fa-check-circle"></i> ADMINISTERED TO PATIENT
                </div>
            </div>
            <?php if ($order['completed_by_name']): ?>
            <div>
                <div style="font-size: 12px; color: #065f46; font-weight: 600; margin-bottom: 4px;">Administered By</div>
                <div style="font-size: 16px; font-weight: 700; color: #065f46;">
                    <i class="fas fa-user-nurse"></i> <?= esc($order['completed_by_name']) ?>
                </div>
            </div>
            <?php endif; ?>
            <?php if ($order['completed_at']): ?>
            <div>
                <div style="font-size: 12px; color: #065f46; font-weight: 600; margin-bottom: 4px;">Administered At</div>
                <div style="font-size: 16px; font-weight: 700; color: #065f46;">
                    <i class="fas fa-clock"></i> <?= esc(date('M d, Y h:i A', strtotime($order['completed_at']))) ?>
                </div>
            </div>
            <?php endif; ?>
        </div>
    </div>
    <?php elseif ($order['order_type'] === 'medication' && ($order['pharmacy_status'] ?? 'pending') === 'dispensed'): ?>
    <div class="section-title" style="margin-top: 32px; color: #f59e0b;">
        <i class="fas fa-hourglass-half"></i>
        Medication Status
    </div>
    <div style="padding: 20px; background: #fef3c7; border-radius: 12px; border-left: 4px solid #f59e0b;">
        <div style="color: #92400e; font-weight: 600;">
            <i class="fas fa-info-circle"></i> Medication has been dispensed by Pharmacy. Waiting for Nurse to administer to patient.
        </div>
    </div>
    <?php endif; ?>
        
        <div class="audit-trail">
            <div class="section-title">
                <i class="fas fa-history"></i>
                Audit Trail
            </div>
            <?php if (!empty($auditTrail)): ?>
                <?php foreach ($auditTrail as $log): ?>
                    <div class="audit-item">
                        <div class="audit-item-header">
                            <div>
                                <span class="audit-item-status"><?= esc(ucfirst(str_replace('_', ' ', $log['status']))) ?></span>
                                <span style="color: #64748b; margin-left: 12px;">by <?= esc($log['changed_by_name'] ?? 'System') ?></span>
                            </div>
                            <div class="audit-item-date">
                                <?= esc(date('M d, Y h:i A', strtotime($log['created_at']))) ?>
                            </div>
                        </div>
                        <?php if ($log['notes']): ?>
                            <div class="audit-item-notes">
                                <?= esc($log['notes']) ?>
                            </div>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div style="padding: 20px; text-align: center; color: #94a3b8;">
                    <i class="fas fa-info-circle"></i>
                    No audit trail entries found.
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>
<?= $this->endSection() ?>

