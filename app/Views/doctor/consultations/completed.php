<?= $this->extend('template/header') ?>

<?= $this->section('title') ?>Completed Consultations<?= $this->endSection() ?>

<?= $this->section('content') ?>

<style>
    .doctor-page-container {
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
    
    .table-container {
        overflow-x: auto;
    }
    
    .table-modern {
        width: 100%;
        border-collapse: collapse;
    }
    
    .table-modern thead {
        background: linear-gradient(135deg, #0288d1 0%, #03a9f4 100%);
        color: white;
    }
    
    .table-modern th {
        padding: 16px;
        text-align: left;
        font-weight: 600;
        font-size: 14px;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
    
    .table-modern td {
        padding: 16px;
        border-bottom: 1px solid #e5e7eb;
        font-size: 14px;
        color: #1e293b;
    }
    
    .table-modern tbody tr:hover {
        background-color: #f8fafc;
    }
    
    .table-modern tbody tr:last-child td {
        border-bottom: none;
    }
    
    .btn-sm-modern {
        padding: 8px 16px;
        border-radius: 8px;
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
    
    .btn-info {
        background: linear-gradient(135deg, #0288d1 0%, #03a9f4 100%);
        color: white;
        box-shadow: 0 2px 8px rgba(2, 136, 209, 0.3);
    }
    
    .btn-info:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(2, 136, 209, 0.4);
        color: white;
    }
    
    .btn-print {
        background: linear-gradient(135deg, #10b981 0%, #34d399 100%);
        color: white;
        box-shadow: 0 2px 8px rgba(16, 185, 129, 0.3);
    }
    
    .btn-print:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(16, 185, 129, 0.4);
        color: white;
    }
    
    .empty-state {
        text-align: center;
        padding: 60px 20px;
        color: #64748b;
    }
    
    .empty-state i {
        font-size: 64px;
        margin-bottom: 16px;
        opacity: 0.5;
    }
    
    .badge {
        display: inline-block;
        padding: 4px 12px;
        border-radius: 12px;
        font-size: 12px;
        font-weight: 600;
        text-transform: uppercase;
    }
    
    .badge-success {
        background-color: #d1fae5;
        color: #065f46;
    }
    
    .patient-name {
        font-weight: 600;
        color: #1e293b;
    }
    
    .consultation-date {
        color: #64748b;
        font-size: 13px;
    }
</style>

<div class="doctor-page-container">
    <div class="page-header">
        <h1>
            <i class="fas fa-check-circle"></i>
            Completed Consultations
        </h1>
    </div>

    <div class="modern-card">
        <div class="card-header-modern">
            <h5>
                <i class="fas fa-list"></i>
                Consultation History
            </h5>
        </div>
        <div class="card-body-modern">
            <?php if (!empty($consultations)): ?>
                <div class="table-container">
                    <table class="table-modern">
                        <thead>
                            <tr>
                                <th>Patient Name</th>
                                <th>Consultation Date</th>
                                <th>Consultation Time</th>
                                <th>Chief Complaint</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($consultations as $item): 
                                $consultation = $item['consultation'];
                                $patient = $item['patient'];
                                $patientName = trim(($patient['firstname'] ?? '') . ' ' . ($patient['lastname'] ?? ''));
                                if (empty($patientName) && !empty($patient['full_name'])) {
                                    $patientName = $patient['full_name'];
                                }
                            ?>
                                <tr>
                                    <td>
                                        <div class="patient-name"><?= esc($patientName) ?></div>
                                        <div style="font-size: 12px; color: #64748b; margin-top: 4px;">
                                            ID: <?= esc($patient['id'] ?? $patient['patient_id'] ?? 'N/A') ?>
                                        </div>
                                    </td>
                                    <td>
                                        <?= !empty($consultation['consultation_date']) ? date('F d, Y', strtotime($consultation['consultation_date'])) : 'N/A' ?>
                                    </td>
                                    <td>
                                        <?= !empty($consultation['consultation_time']) ? date('h:i A', strtotime($consultation['consultation_time'])) : 'N/A' ?>
                                    </td>
                                    <td>
                                        <?= !empty($consultation['chief_complaint']) ? esc(substr($consultation['chief_complaint'], 0, 50)) . (strlen($consultation['chief_complaint']) > 50 ? '...' : '') : 'N/A' ?>
                                    </td>
                                    <td>
                                        <span class="badge badge-success">
                                            <i class="fas fa-check"></i> Completed
                                        </span>
                                    </td>
                                    <td>
                                        <div style="display: flex; gap: 8px;">
                                            <a href="<?= site_url('doctor/consultations/view/' . $consultation['id']) ?>" 
                                               class="btn-sm-modern btn-info" 
                                               title="View Consultation Details">
                                                <i class="fas fa-eye"></i> View
                                            </a>
                                            <a href="<?= site_url('doctor/consultations/view/' . $consultation['id']) ?>" 
                                               onclick="window.print(); return false;"
                                               class="btn-sm-modern btn-print" 
                                               title="Print Consultation">
                                                <i class="fas fa-print"></i> Print
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
                    <i class="fas fa-clipboard-list"></i>
                    <h3 style="margin: 0 0 8px 0; color: #1e293b;">No Completed Consultations</h3>
                    <p style="margin: 0; color: #64748b;">You haven't completed any consultations yet.</p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

