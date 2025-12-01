<?= $this->extend('template/header') ?>

<?= $this->section('title') ?>Completed Tests<?= $this->endSection() ?>

<?= $this->section('content') ?>
<style>
    .modern-card {
        background: #fff;
        border: 1px solid #e5e7eb;
        border-radius: 12px;
        padding: 24px;
        box-shadow: 0 2px 6px rgba(15,23,42,.08);
        margin-bottom: 24px;
    }
    .page-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 24px;
    }
    .page-header h1 {
        margin: 0;
        color: #2e7d32;
        font-size: 28px;
    }
    .data-table {
        width: 100%;
        border-collapse: collapse;
    }
    .data-table th {
        background: #e8f5e9;
        color: #2e7d32;
        font-weight: 600;
        padding: 12px;
        text-align: left;
        border-bottom: 2px solid #4caf50;
    }
    .data-table td {
        padding: 12px;
        border-bottom: 1px solid #e5e7eb;
    }
    .data-table tr:hover {
        background: #f9fafb;
    }
    .badge-completed { 
        background: #d1fae5; 
        color: #065f46; 
        padding: 4px 12px;
        border-radius: 12px;
        font-size: 12px;
        font-weight: 600;
    }
    .result-preview {
        max-width: 300px;
        word-wrap: break-word;
    }
</style>

<div class="container py-4">
    <div class="page-header">
        <h1><i class="fas fa-check-circle"></i> Completed Tests</h1>
        <a href="<?= site_url('labstaff/dashboard') ?>" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left"></i> Back to Dashboard
        </a>
    </div>

    <?php if (session()->getFlashdata('success')): ?>
        <div class="alert alert-success"><?= esc(session()->getFlashdata('success')) ?></div>
    <?php endif; ?>
    <?php if (session()->getFlashdata('error')): ?>
        <div class="alert alert-danger"><?= esc(session()->getFlashdata('error')) ?></div>
    <?php endif; ?>

    <div class="modern-card">
        <?php if (empty($completedTests)): ?>
            <div class="text-center py-5">
                <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                <p class="lead">No completed tests found.</p>
            </div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Request ID</th>
                            <th>Patient</th>
                            <th>Test Type</th>
                            <th>Test Name</th>
                            <th>Requested By</th>
                            <th>Result</th>
                            <th>Interpretation</th>
                            <th>Completed By</th>
                            <th>Completed At</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($completedTests as $test): ?>
                            <tr>
                                <td>#<?= esc($test['id']) ?></td>
                                <td>
                                    <strong><?= esc(($test['patient_firstname'] ?? '') . ' ' . ($test['patient_lastname'] ?? '')) ?></strong>
                                    <?php if (!empty($test['patient_contact'])): ?>
                                        <br><small class="text-muted"><?= esc($test['patient_contact']) ?></small>
                                    <?php endif; ?>
                                </td>
                                <td><?= esc($test['test_type']) ?></td>
                                <td><?= esc($test['test_name']) ?></td>
                                <td>
                                    <?php if ($test['requested_by'] === 'doctor' && !empty($test['doctor_name'])): ?>
                                        <i class="fas fa-user-md"></i> Dr. <?= esc($test['doctor_name']) ?>
                                    <?php elseif ($test['requested_by'] === 'nurse' && !empty($test['nurse_name'])): ?>
                                        <i class="fas fa-user-nurse"></i> <?= esc($test['nurse_name']) ?>
                                    <?php else: ?>
                                        <?= esc(ucfirst($test['requested_by'])) ?>
                                    <?php endif; ?>
                                </td>
                                <td class="result-preview">
                                    <?php if (!empty($test['test_result'])): ?>
                                        <?= esc(substr($test['test_result'], 0, 100)) ?><?= strlen($test['test_result']) > 100 ? '...' : '' ?>
                                    <?php else: ?>
                                        <span class="text-muted">-</span>
                                    <?php endif; ?>
                                </td>
                                <td class="result-preview">
                                    <?php if (!empty($test['interpretation'])): ?>
                                        <?= esc(substr($test['interpretation'], 0, 100)) ?><?= strlen($test['interpretation']) > 100 ? '...' : '' ?>
                                    <?php else: ?>
                                        <span class="text-muted">-</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if (!empty($test['completed_by_name'])): ?>
                                        <?= esc($test['completed_by_name']) ?>
                                    <?php else: ?>
                                        <span class="text-muted">-</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if (!empty($test['completed_at'])): ?>
                                        <?= esc(date('M d, Y H:i', strtotime($test['completed_at']))) ?>
                                    <?php else: ?>
                                        <span class="text-muted">-</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</div>
<?= $this->endSection() ?>

