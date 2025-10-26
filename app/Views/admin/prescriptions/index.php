<?= $this->extend('templates/template') ?>

<?= $this->section('content') ?>
<style>
    .page-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 16px;
        margin-bottom: 24px;
    }
    .page-title {
        font-size: 28px;
        font-weight: 700;
        color: #1f2937;
        margin: 0;
    }
    .status-badge {
        display: inline-block;
        padding: 4px 8px;
        border-radius: 6px;
        font-size: 12px;
        font-weight: 600;
        text-transform: capitalize;
    }
    .status-pending { background: #fef3c7; color: #92400e; }
    .status-dispensed { background: #dcfce7; color: #166534; }
    .status-completed { background: #dbeafe; color: #1d4ed8; }
    .status-cancelled { background: #fee2e2; color: #b91c1c; }
    .table-responsive { overflow-x: auto; }
    .prescription-table {
        width: 100%;
        border-collapse: collapse;
        background: white;
        border: 1px solid #e5e7eb;
        border-radius: 12px;
        overflow: hidden;
    }
    .prescription-table thead {
        background: #f8fafc;
    }
    .prescription-table th,
    .prescription-table td {
        padding: 14px 16px;
        border-bottom: 1px solid #e5e7eb;
        text-align: left;
        font-size: 14px;
        color: #374151;
    }
    .prescription-table tbody tr:last-child td {
        border-bottom: none;
    }
    .action-form {
        display: flex;
        flex-wrap: wrap;
        gap: 8px;
        align-items: center;
    }
    .action-form select {
        padding: 6px 10px;
        border-radius: 6px;
        border: 1px solid #d1d5db;
        font-size: 13px;
    }
    .btn-update {
        background: #2563eb;
        color: white;
        border: none;
        border-radius: 6px;
        padding: 8px 14px;
        cursor: pointer;
        font-size: 13px;
        transition: background 0.2s ease;
    }
    .btn-update:hover { background: #1d4ed8; }
    .alert {
        padding: 14px 18px;
        border-radius: 10px;
        margin-bottom: 20px;
        border: 1px solid transparent;
        display: flex;
        align-items: center;
        gap: 10px;
        font-size: 14px;
    }
    .alert-success { background: #ecfdf5; border-color: #34d399; color: #065f46; }
    .alert-error { background: #fef2f2; border-color: #f87171; color: #991b1b; }
    .empty-state {
        text-align: center;
        padding: 60px 20px;
        background: white;
        border: 1px dashed #d1d5db;
        border-radius: 12px;
        color: #6b7280;
    }
</style>

<div class="page-header">
    <h1 class="page-title">📋 Prescription Management</h1>
    <a href="<?= base_url('super-admin/unified') ?>" class="btn btn-secondary">← Back to Super Admin Dashboard</a>
</div>

<?php if (session()->getFlashdata('message')): ?>
    <div class="alert alert-success">
        ✅ <?= esc(session()->getFlashdata('message')) ?>
    </div>
<?php endif; ?>

<?php if (session()->getFlashdata('error')): ?>
    <div class="alert alert-error">
        ⚠️ <?= esc(session()->getFlashdata('error')) ?>
    </div>
<?php endif; ?>

<?php if (empty($prescriptions)): ?>
    <div class="empty-state">
        <h3>No prescriptions found.</h3>
        <p>Prescriptions created by doctors will appear here for administrative monitoring.</p>
    </div>
<?php else: ?>
    <div class="table-responsive">
        <table class="prescription-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Patient ID</th>
                    <th>Doctor ID</th>
                    <th>Medication</th>
                    <th>Dosage</th>
                    <th>Quantity</th>
                    <th>Status</th>
                    <th>Created</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($prescriptions as $prescription): ?>
                    <tr>
                        <td>#<?= esc($prescription['id']) ?></td>
                        <td><?= esc($prescription['patient_id']) ?></td>
                        <td><?= esc($prescription['doctor_id']) ?></td>
                        <td><?= esc($prescription['medication_name'] ?? $prescription['medicine_name'] ?? 'N/A') ?></td>
                        <td><?= esc($prescription['dosage'] ?? 'N/A') ?></td>
                        <td><?= esc($prescription['quantity'] ?? '-') ?></td>
                        <td>
                            <span class="status-badge status-<?= esc(strtolower($prescription['status'] ?? 'pending')) ?>">
                                <?= esc(ucfirst($prescription['status'] ?? 'pending')) ?>
                            </span>
                        </td>
                        <td>
                            <?php if (!empty($prescription['created_at'])): ?>
                                <?= esc(date('M d, Y h:i A', strtotime($prescription['created_at']))) ?>
                            <?php else: ?>
                                —
                            <?php endif; ?>
                        </td>
                        <td>
                            <form action="<?= base_url('admin/prescriptions/update/' . $prescription['id']) ?>" method="post" class="action-form">
                                <?= csrf_field() ?>
                                <select name="status" required>
                                    <?php foreach ($statuses as $status): ?>
                                        <option value="<?= esc($status) ?>" <?= ($prescription['status'] ?? 'pending') === $status ? 'selected' : '' ?>>
                                            <?= esc(ucfirst($status)) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                                <button type="submit" class="btn-update">Update</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
<?php endif; ?>

<?= $this->endSection() ?>
