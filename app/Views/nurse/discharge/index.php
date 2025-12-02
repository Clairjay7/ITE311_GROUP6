<?= $this->extend('template/header') ?>

<?= $this->section('title') ?>Pending Discharges<?= $this->endSection() ?>

<?= $this->section('content') ?>
<style>
    .page-header {
        background: linear-gradient(135deg, #0288d1 0%, #03a9f4 100%);
        border-radius: 16px;
        padding: 24px 32px;
        margin-bottom: 24px;
        box-shadow: 0 4px 20px rgba(2, 136, 209, 0.2);
        color: white;
    }
    .modern-card {
        background: white;
        border-radius: 16px;
        box-shadow: 0 2px 12px rgba(0, 0, 0, 0.08);
        border: 1px solid #e5e7eb;
        overflow: hidden;
    }
    .table-modern {
        width: 100%;
        border-collapse: collapse;
    }
    .table-modern th {
        padding: 16px;
        text-align: left;
        font-weight: 700;
        color: #0288d1;
        background: #e3f2fd;
        border-bottom: 2px solid #03a9f4;
    }
    .table-modern td {
        padding: 16px;
        border-bottom: 1px solid #f1f5f9;
    }
    .btn-modern {
        padding: 8px 16px;
        border-radius: 8px;
        font-weight: 600;
        font-size: 14px;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 6px;
    }
    .btn-primary {
        background: #0288d1;
        color: white;
    }
</style>

<div class="page-header">
    <h1><i class="fas fa-sign-out-alt"></i> Pending Discharges</h1>
</div>

<div class="modern-card">
    <?php if (!empty($pendingDischarges)): ?>
        <table class="table-modern">
            <thead>
                <tr>
                    <th>Patient</th>
                    <th>Room</th>
                    <th>Doctor</th>
                    <th>Planned Discharge</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($pendingDischarges as $discharge): ?>
                    <tr>
                        <td>
                            <strong><?= esc(ucwords(trim(($discharge['firstname'] ?? '') . ' ' . ($discharge['lastname'] ?? '')))) ?></strong>
                        </td>
                        <td><?= esc($discharge['room_number'] ?? 'N/A') ?> - <?= esc($discharge['ward'] ?? 'N/A') ?></td>
                        <td><?= esc($discharge['doctor_name'] ?? 'N/A') ?></td>
                        <td><?= date('M d, Y', strtotime($discharge['planned_discharge_date'])) ?></td>
                        <td>
                            <a href="<?= site_url('nurse/discharge/view/' . $discharge['id']) ?>" class="btn-modern btn-primary">
                                <i class="fas fa-eye"></i> Prepare Patient
                            </a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php else: ?>
        <div style="text-align: center; padding: 60px 20px; color: #64748b;">
            <i class="fas fa-check-circle" style="font-size: 64px; margin-bottom: 16px; color: #cbd5e1;"></i>
            <h5>No Pending Discharges</h5>
            <p>No patients are currently pending discharge.</p>
        </div>
    <?php endif; ?>
</div>

<?= $this->endSection() ?>

