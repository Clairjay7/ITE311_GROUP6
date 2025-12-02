<?= $this->extend('template/header') ?>

<?= $this->section('title') ?>My Schedule<?= $this->endSection() ?>

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
    
    .modern-card {
        background: white;
        border-radius: 16px;
        box-shadow: 0 2px 12px rgba(0, 0, 0, 0.08);
        border: 1px solid #e5e7eb;
        overflow: hidden;
    }
    
    .card-body-modern {
        padding: 24px;
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
    
    .btn-modern-success {
        background: #10b981;
        color: white;
    }
    
    .btn-modern-success:hover {
        background: #059669;
        color: white;
        transform: translateY(-2px);
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
    
    .btn-modern-warning {
        background: #f59e0b;
        color: white;
    }
    
    .btn-modern-warning:hover {
        background: #d97706;
        color: white;
        transform: translateY(-2px);
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
    
    .btn-sm-modern {
        padding: 6px 12px;
        font-size: 13px;
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
    
    .table-modern tbody tr {
        transition: all 0.2s ease;
    }
    
    .table-modern tbody tr:hover {
        background: #f8fafc;
        transform: scale(1.01);
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
    
    .empty-state p {
        margin: 0 0 24px;
        color: #94a3b8;
        font-size: 15px;
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
            <i class="fas fa-calendar"></i>
            My Complete Schedule
        </h1>
        <div style="display: flex; gap: 12px; flex-wrap: wrap;">
            <a href="<?= site_url('doctor/consultations/upcoming') ?>" class="btn-modern btn-modern-success">
                <i class="fas fa-clock"></i>
                Upcoming Only
            </a>
            <a href="<?= site_url('doctor/consultations/create') ?>" class="btn-modern btn-modern-primary">
                <i class="fas fa-plus"></i>
                New Consultation
            </a>
        </div>
    </div>
    
    <div class="modern-card">
        <div class="card-body-modern">
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

            <?php if (!empty($consultations)): ?>
                <div class="table-responsive">
                    <table class="table-modern">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Time</th>
                                <th>Patient</th>
                                <th>Type</th>
                                <th>Status</th>
                                <th>Notes</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($consultations as $consultation): ?>
                                <tr>
                                    <td><strong><?= date('M d, Y', strtotime($consultation['consultation_date'])) ?></strong></td>
                                    <td><?= date('h:i A', strtotime($consultation['consultation_time'])) ?></td>
                                    <td>
                                        <strong style="color: #1e293b;">
                                            <?= esc(ucfirst($consultation['firstname']) . ' ' . ucfirst($consultation['lastname'])) ?>
                                        </strong>
                                    </td>
                                    <td>
                                        <span class="badge-modern" style="background: <?= $consultation['type'] == 'upcoming' ? '#dbeafe' : '#d1fae5'; ?>; color: <?= $consultation['type'] == 'upcoming' ? '#1e40af' : '#065f46'; ?>;">
                                            <?= esc(ucfirst($consultation['type'])) ?>
                                        </span>
                                    </td>
                                    <td>
                                        <span class="badge-modern" style="background: <?= 
                                            $consultation['status'] == 'approved' ? '#d1fae5' : 
                                            ($consultation['status'] == 'pending' ? '#fef3c7' : '#fee2e2'); 
                                        ?>; color: <?= 
                                            $consultation['status'] == 'approved' ? '#065f46' : 
                                            ($consultation['status'] == 'pending' ? '#92400e' : '#991b1b'); 
                                        ?>;">
                                            <?= esc(ucfirst($consultation['status'])) ?>
                                        </span>
                                    </td>
                                    <td><?= esc(substr($consultation['notes'] ?? 'No notes', 0, 50)) ?><?= strlen($consultation['notes'] ?? '') > 50 ? '...' : '' ?></td>
                                    <td>
                                        <div style="display: flex; gap: 8px; flex-wrap: wrap;">
                                            <?php if ($consultation['type'] == 'completed' && !empty($consultation['for_admission'])): ?>
                                                <?php
                                                // Check if already admitted
                                                $db = \Config\Database::connect();
                                                $existingAdmission = $db->table('admissions')
                                                    ->where('consultation_id', $consultation['id'])
                                                    ->where('status !=', 'discharged')
                                                    ->where('status !=', 'cancelled')
                                                    ->where('deleted_at', null)
                                                    ->get()
                                                    ->getRowArray();
                                                ?>
                                                <?php if ($existingAdmission): ?>
                                                    <span class="badge-modern" style="background: #d1fae5; color: #065f46;">
                                                        <i class="fas fa-check"></i> Admitted
                                                    </span>
                                                <?php else: ?>
                                                    <span class="badge-modern" style="background: #fee2e2; color: #991b1b;">
                                                        <i class="fas fa-hospital"></i> For Admission
                                                    </span>
                                                    <small style="display: block; color: #64748b; font-size: 11px; margin-top: 4px;">
                                                        Nurse/Receptionist will process
                                                    </small>
                                                <?php endif; ?>
                                            <?php endif; ?>
                                            <a href="<?= site_url('doctor/consultations/edit/' . $consultation['id']) ?>" 
                                               class="btn-modern btn-modern-warning btn-sm-modern" title="Edit Consultation">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <a href="<?= site_url('doctor/patients/view/' . $consultation['patient_id']) ?>" 
                                               class="btn-modern btn-modern-info btn-sm-modern" title="View Patient">
                                                <i class="fas fa-user"></i>
                                            </a>
                                            <?php
                                            // Check if consultation has related records (for warning message)
                                            $db = \Config\Database::connect();
                                            $hasCharges = $db->table('charges')
                                                ->where('consultation_id', $consultation['id'])
                                                ->where('deleted_at', null)
                                                ->countAllResults() > 0;
                                            
                                            $hasAdmission = $db->table('admissions')
                                                ->where('consultation_id', $consultation['id'])
                                                ->where('status !=', 'cancelled')
                                                ->where('deleted_at', null)
                                                ->countAllResults() > 0;
                                            
                                            // Check for discharge orders through admission
                                            $hasDischargeOrder = false;
                                            if ($hasAdmission) {
                                                $admission = $db->table('admissions')
                                                    ->where('consultation_id', $consultation['id'])
                                                    ->where('status !=', 'cancelled')
                                                    ->where('deleted_at', null)
                                                    ->get()
                                                    ->getRowArray();
                                                
                                                if ($admission) {
                                                    $hasDischargeOrder = $db->table('discharge_orders')
                                                        ->where('admission_id', $admission['id'])
                                                        ->countAllResults() > 0;
                                                }
                                            }
                                            
                                            $hasRelatedRecords = $hasCharges || $hasAdmission || $hasDischargeOrder;
                                            
                                            // Build warning message
                                            $warningMsg = 'Are you sure you want to delete this consultation? This action cannot be undone.';
                                            if ($hasRelatedRecords) {
                                                $warnings = [];
                                                if ($hasCharges) $warnings[] = 'billing charges';
                                                if ($hasAdmission) $warnings[] = 'admission record';
                                                if ($hasDischargeOrder) $warnings[] = 'discharge order';
                                                $warningMsg = 'WARNING: This consultation has associated ' . implode(', ', $warnings) . 
                                                            '. Deleting it may affect related records. Are you sure you want to proceed?';
                                            }
                                            $warningMsgJs = addslashes($warningMsg);
                                            ?>
                                            <a href="<?= site_url('doctor/consultations/delete/' . $consultation['id']) ?>" 
                                               class="btn-modern btn-modern-danger btn-sm-modern" 
                                               onclick="return confirm('<?= $warningMsgJs ?>')" 
                                               title="<?= $hasRelatedRecords ? 'Delete Consultation (has related records)' : 'Delete Consultation' ?>">
                                                <i class="fas fa-trash"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <div class="empty-state">
                    <i class="fas fa-calendar-check"></i>
                    <h5>No Consultations Found</h5>
                    <p>You haven't scheduled any consultations yet.</p>
                    <a href="<?= site_url('doctor/consultations/create') ?>" class="btn-modern btn-modern-primary">
                        <i class="fas fa-plus"></i>
                        Schedule First Consultation
                    </a>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
