<?= $this->extend('template/header') ?>

<?= $this->section('title') ?>Patient Details<?= $this->endSection() ?>

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
        color: #2e7d32;
        margin-bottom: 20px;
        display: flex;
        align-items: center;
        gap: 10px;
    }
    
    .info-section-title::before {
        content: '';
        width: 4px;
        height: 20px;
        background: #2e7d32;
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
        background: linear-gradient(135deg, #2e7d32 0%, #4caf50 100%);
        color: white;
        box-shadow: 0 4px 12px rgba(46, 125, 50, 0.3);
    }
    
    .btn-modern-secondary {
        background: #64748b;
        color: white;
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

<div class="doctor-page-container">
    <div class="page-header">
        <h1>
            <i class="fas fa-user-circle"></i>
            Patient Details
        </h1>
        <div style="display: flex; gap: 12px; flex-wrap: wrap;">
            <a href="<?= site_url('doctor/patients/edit/' . $patient['id']) ?>" class="btn-modern btn-modern-warning">
                <i class="fas fa-edit"></i>
                Edit Patient
            </a>
            <a href="<?= site_url('doctor/patients') ?>" class="btn-modern btn-modern-secondary">
                <i class="fas fa-arrow-left"></i>
                Back to List
            </a>
        </div>
    </div>
    
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
                            <i class="fas fa-info-circle"></i>
                            Registration Information
                        </div>
                        <table class="info-table">
                            <tr>
                                <td>Registered Date:</td>
                                <td><?= date('F d, Y h:i A', strtotime($patient['created_at'])) ?></td>
                            </tr>
                            <tr>
                                <td>Last Updated:</td>
                                <td><?= date('F d, Y h:i A', strtotime($patient['updated_at'])) ?></td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Consultation History -->
    <div class="modern-card">
        <div class="card-header-modern">
            <h5>
                <i class="fas fa-calendar-check"></i>
                Consultation History
            </h5>
        </div>
        <div class="card-body-modern">
            <?php if (!empty($consultations)): ?>
                <div class="table-responsive">
                    <table class="table-modern">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Time</th>
                                <th>Type</th>
                                <th>Status</th>
                                <th>Notes</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($consultations as $consultation): ?>
                                <tr>
                                    <td><?= date('M d, Y', strtotime($consultation['consultation_date'])) ?></td>
                                    <td><?= date('h:i A', strtotime($consultation['consultation_time'])) ?></td>
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
                                    <td><?= esc(substr($consultation['notes'] ?? 'No notes', 0, 100)) ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <div class="empty-state">
                    <i class="fas fa-calendar-check"></i>
                    <p style="margin: 0; color: #64748b;">No consultation history found.</p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
