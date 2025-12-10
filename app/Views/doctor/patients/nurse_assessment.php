<?= $this->extend('template/header') ?>

<?= $this->section('title') ?>Nurse Assessment<?= $this->endSection() ?>

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
    
    .info-box {
        background: #e0f2fe;
        border-left: 4px solid #0288d1;
        padding: 16px;
        border-radius: 8px;
        margin-bottom: 24px;
    }
    
    .vital-value {
        font-size: 18px;
        font-weight: 700;
        color: #1e293b;
    }
    
    .vital-label {
        font-size: 12px;
        color: #64748b;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
</style>

<div class="doctor-page-container">
    <div class="page-header">
        <h1>
            <i class="fas fa-clipboard-check"></i>
            Nurse Assessment - <?= esc(ucfirst($patient['firstname'] ?? $patient['first_name'] ?? '') . ' ' . ucfirst($patient['lastname'] ?? $patient['last_name'] ?? '')) ?>
        </h1>
        <a href="<?= site_url('doctor/dashboard') ?>" class="btn-modern btn-modern-secondary" style="text-decoration: none; color: white;">
            <i class="fas fa-arrow-left"></i> Back to Dashboard
        </a>
    </div>

    <!-- Patient & Nurse Info -->
    <div class="info-box">
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 16px;">
            <div>
                <div class="vital-label">Patient Name</div>
                <div class="vital-value"><?= esc(ucfirst($patient['firstname'] ?? $patient['first_name'] ?? '') . ' ' . ucfirst($patient['lastname'] ?? $patient['last_name'] ?? '')) ?></div>
            </div>
            <div>
                <div class="vital-label">Assigned Nurse</div>
                <div class="vital-value"><?= esc($assignedNurseName ?? 'Not Assigned') ?></div>
            </div>
            <div>
                <div class="vital-label">Assessment Date</div>
                <div class="vital-value"><?= date('M d, Y') ?></div>
            </div>
        </div>
    </div>

    <!-- Recent Vital Signs -->
    <div class="modern-card">
        <div class="card-header-modern">
            <h5>
                <i class="fas fa-heartbeat"></i>
                Recent Vital Signs (Last 7 Days)
            </h5>
        </div>
        <div class="card-body-modern">
            <?php if (!empty($recentVitals)): ?>
                <div class="table-responsive">
                    <table class="table-modern">
                        <thead>
                            <tr>
                                <th>Time</th>
                                <th>Blood Pressure</th>
                                <th>Heart Rate</th>
                                <th>Temperature</th>
                                <th>O2 Saturation</th>
                                <th>Respiratory Rate</th>
                                <th>Weight</th>
                                <th>Height</th>
                                <th>Notes</th>
                                <th>Recorded By</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($recentVitals as $vital): ?>
                                <?php
                                $nurseName = trim(($vital['nurse_first_name'] ?? '') . ' ' . ($vital['nurse_last_name'] ?? ''));
                                if (empty($nurseName)) {
                                    $nurseName = $vital['nurse_username'] ?? 'Nurse';
                                }
                                ?>
                                <tr>
                                    <td><?= esc(date('M d, Y h:i A', strtotime($vital['recorded_at'] ?? $vital['created_at']))) ?></td>
                                    <td>
                                        <?php if ($vital['blood_pressure_systolic'] && $vital['blood_pressure_diastolic']): ?>
                                            <strong><?= esc($vital['blood_pressure_systolic']) ?>/<?= esc($vital['blood_pressure_diastolic']) ?></strong>
                                        <?php else: ?>
                                            <span style="color: #94a3b8;">N/A</span>
                                        <?php endif; ?>
                                    </td>
                                    <td><strong><?= esc($vital['heart_rate'] ?? 'N/A') ?></strong> bpm</td>
                                    <td><strong><?= esc($vital['temperature'] ? $vital['temperature'] . '°C' : 'N/A') ?></strong></td>
                                    <td><strong><?= esc($vital['oxygen_saturation'] ? $vital['oxygen_saturation'] . '%' : 'N/A') ?></strong></td>
                                    <td><strong><?= esc($vital['respiratory_rate'] ?? 'N/A') ?></strong> /min</td>
                                    <td><?= esc($vital['weight'] ? $vital['weight'] . ' kg' : 'N/A') ?></td>
                                    <td><?= esc($vital['height'] ? $vital['height'] . ' cm' : 'N/A') ?></td>
                                    <td>
                                        <?php if (!empty($vital['notes'])): ?>
                                            <div style="max-width: 200px;" title="<?= esc($vital['notes']) ?>">
                                                <?= esc(substr($vital['notes'], 0, 50)) ?><?= strlen($vital['notes']) > 50 ? '...' : '' ?>
                                            </div>
                                        <?php else: ?>
                                            <span style="color: #94a3b8;">—</span>
                                        <?php endif; ?>
                                    </td>
                                    <td><?= esc($nurseName) ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <div style="text-align: center; padding: 40px; color: #94a3b8;">
                    <i class="fas fa-heartbeat" style="font-size: 48px; margin-bottom: 16px; opacity: 0.3;"></i>
                    <p style="margin: 0;">No vital signs recorded in the last 7 days by the assigned nurse.</p>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Nurse Notes -->
    <?php if (!empty($nurseNotes)): ?>
    <div class="modern-card">
        <div class="card-header-modern">
            <h5>
                <i class="fas fa-sticky-note"></i>
                Recent Nurse Notes (Last 7 Days)
            </h5>
        </div>
        <div class="card-body-modern">
            <div class="table-responsive">
                <table class="table-modern">
                    <thead>
                        <tr>
                            <th>Time</th>
                            <th>Type</th>
                            <th>Priority</th>
                            <th>Note</th>
                            <th>Recorded By</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($nurseNotes as $note): ?>
                            <?php
                            $nurseName = trim(($note['nurse_first_name'] ?? '') . ' ' . ($note['nurse_last_name'] ?? ''));
                            if (empty($nurseName)) {
                                $nurseName = $note['nurse_username'] ?? 'Nurse';
                            }
                            ?>
                            <tr>
                                <td><?= esc(date('M d, Y h:i A', strtotime($note['created_at']))) ?></td>
                                <td>
                                    <span class="badge-modern" style="background: #e0f2fe; color: #0369a1;">
                                        <?= esc(ucfirst($note['note_type'] ?? 'General')) ?>
                                    </span>
                                </td>
                                <td>
                                    <span class="badge-modern" style="background: <?= 
                                        ($note['priority'] ?? 'normal') == 'urgent' ? '#fee2e2' : 
                                        (($note['priority'] ?? 'normal') == 'high' ? '#fef3c7' : '#d1fae5'); 
                                    ?>; color: <?= 
                                        ($note['priority'] ?? 'normal') == 'urgent' ? '#991b1b' : 
                                        (($note['priority'] ?? 'normal') == 'high' ? '#92400e' : '#065f46'); 
                                    ?>;">
                                        <?= esc(ucfirst($note['priority'] ?? 'normal')) ?>
                                    </span>
                                </td>
                                <td><?= esc($note['note'] ?? 'N/A') ?></td>
                                <td><?= esc($nurseName) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <div style="text-align: center; margin-top: 24px;">
        <a href="<?= site_url('doctor/patients/view/' . ($patient['id'] ?? $patient['patient_id'])) ?>" class="btn-modern btn-modern-primary" style="text-decoration: none;">
            <i class="fas fa-user"></i> View Full Patient Details
        </a>
    </div>
</div>

<?= $this->endSection() ?>

