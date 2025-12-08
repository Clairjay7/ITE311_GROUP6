<?= $this->extend('template/header') ?>

<?= $this->section('title') ?>Patient Billing<?= $this->endSection() ?>

<?= $this->section('content') ?>
<style>
    .page-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 24px;
    }
    .page-header h1 {
        margin: 0;
        color: #8b5cf6;
        font-size: 28px;
    }
    .search-container {
        background: white;
        border-radius: 12px;
        padding: 24px;
        box-shadow: 0 2px 6px rgba(0,0,0,0.08);
        margin-bottom: 24px;
    }
    .form-group {
        margin-bottom: 20px;
    }
    .form-label {
        display: block;
        margin-bottom: 8px;
        font-weight: 600;
        color: #374151;
    }
    .form-control {
        width: 100%;
        padding: 10px;
        border: 1px solid #d1d5db;
        border-radius: 8px;
        font-size: 14px;
    }
    .btn-primary {
        background: #8b5cf6;
        color: white;
        padding: 10px 24px;
        border: none;
        border-radius: 8px;
        font-weight: 600;
        cursor: pointer;
    }
    .patient-info-card {
        background: linear-gradient(135deg, #8b5cf6 0%, #a78bfa 100%);
        border-radius: 16px;
        padding: 24px;
        margin-bottom: 24px;
        color: white;
        box-shadow: 0 4px 20px rgba(139, 92, 246, 0.2);
    }
    .summary-cards {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 16px;
        margin-bottom: 24px;
    }
    .summary-card {
        background: white;
        border-radius: 12px;
        padding: 20px;
        box-shadow: 0 2px 6px rgba(0,0,0,0.08);
        border-left: 4px solid #8b5cf6;
    }
    .summary-card h3 {
        margin: 0 0 8px 0;
        font-size: 14px;
        font-weight: 600;
        color: #64748b;
    }
    .summary-card .value {
        font-size: 24px;
        font-weight: 800;
        color: #1f2937;
    }
    .table-container {
        background: white;
        border-radius: 12px;
        overflow: hidden;
        box-shadow: 0 2px 6px rgba(0,0,0,0.08);
    }
    .data-table {
        width: 100%;
        border-collapse: collapse;
    }
    .data-table th {
        background: #f3f4f6;
        padding: 12px;
        text-align: left;
        font-weight: 600;
        color: #374151;
    }
    .data-table td {
        padding: 12px;
        border-bottom: 1px solid #e5e7eb;
    }
    .status-badge {
        padding: 4px 12px;
        border-radius: 8px;
        font-size: 12px;
        font-weight: 600;
    }
</style>

<div class="page-header">
    <h1><i class="fas fa-user-injured"></i> Patient Billing</h1>
</div>

<div class="search-container">
    <form method="GET" action="<?= site_url('accounting/patient-billing') ?>">
        <div class="form-group">
            <label class="form-label">Select Patient <span style="color: red;">*</span></label>
            <select name="patient_id" class="form-control" required onchange="this.form.submit()">
                <option value="">-- Select Patient --</option>
                <?php foreach ($patients as $patient): ?>
                    <option value="<?= $patient['id'] ?>" <?= ($patientId ?? '') == $patient['id'] ? 'selected' : '' ?>>
                        <?= esc($patient['firstname'] . ' ' . $patient['lastname']) ?> 
                        <?php if (!empty($patient['patient_id'])): ?>
                            (ID: <?= esc($patient['patient_id']) ?>)
                        <?php endif; ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
    </form>
</div>

<?php if ($selectedPatient): ?>
    <!-- Patient Information -->
    <div class="patient-info-card">
        <h2 style="margin: 0 0 8px 0; font-size: 24px;">
            <i class="fas fa-user"></i> <?= esc(ucwords(trim($selectedPatient['firstname'] . ' ' . $selectedPatient['lastname']))) ?>
        </h2>
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 16px; margin-top: 16px;">
            <?php if (!empty($selectedPatient['patient_id'])): ?>
                <div>
                    <div style="font-size: 12px; opacity: 0.9;">Patient ID</div>
                    <div style="font-weight: 600; font-size: 16px;"><?= esc($selectedPatient['patient_id']) ?></div>
                </div>
            <?php endif; ?>
            <?php if (!empty($selectedPatient['contact'])): ?>
                <div>
                    <div style="font-size: 12px; opacity: 0.9;">Contact</div>
                    <div style="font-weight: 600; font-size: 16px;"><?= esc($selectedPatient['contact']) ?></div>
                </div>
            <?php endif; ?>
            <?php if (!empty($selectedPatient['birthdate'])): ?>
                <div>
                    <div style="font-size: 12px; opacity: 0.9;">Date of Birth</div>
                    <div style="font-weight: 600; font-size: 16px;"><?= esc(date('M d, Y', strtotime($selectedPatient['birthdate']))) ?></div>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Summary Cards -->
    <div class="summary-cards">
        <div class="summary-card">
            <h3>Total Bills</h3>
            <div class="value"><?= count($patientBills) ?></div>
        </div>
        <div class="summary-card" style="border-left-color: #10b981;">
            <h3 style="color: #10b981;">Total Amount</h3>
            <div class="value" style="color: #10b981;">₱<?= number_format($totalAmount, 2) ?></div>
        </div>
        <div class="summary-card" style="border-left-color: #10b981;">
            <h3 style="color: #10b981;">Paid Amount</h3>
            <div class="value" style="color: #10b981;">₱<?= number_format($paidAmount, 2) ?></div>
        </div>
        <div class="summary-card" style="border-left-color: #ef4444;">
            <h3 style="color: #ef4444;">Pending Amount</h3>
            <div class="value" style="color: #ef4444;">₱<?= number_format($pendingAmount, 2) ?></div>
        </div>
    </div>

    <!-- Bills Table -->
    <div class="table-container">
        <table class="data-table">
            <thead>
                <tr>
                    <th>Bill/Charge #</th>
                    <th>Type</th>
                    <th>Service/Description</th>
                    <th>Date</th>
                    <th>Amount</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($patientBills)): ?>
                    <?php foreach ($patientBills as $bill): ?>
                        <tr>
                            <td>
                                <?php if (isset($bill['type']) && $bill['type'] === 'charge'): ?>
                                    <strong><?= esc($bill['charge_number'] ?? 'CHG-' . str_replace('CHG-', '', $bill['id'])) ?></strong>
                                <?php else: ?>
                                    <strong>#<?= esc($bill['id']) ?></strong>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if (isset($bill['type']) && $bill['type'] === 'charge'): ?>
                                    <span style="background: #dbeafe; color: #1e40af; padding: 4px 8px; border-radius: 4px; font-size: 11px; font-weight: 600;">CHARGE</span>
                                <?php else: ?>
                                    <span style="background: #f3f4f6; color: #374151; padding: 4px 8px; border-radius: 4px; font-size: 11px; font-weight: 600;">BILL</span>
                                <?php endif; ?>
                            </td>
                            <td><?= esc($bill['service'] ?? 'N/A') ?></td>
                            <td><?= esc(date('M d, Y', strtotime($bill['created_at'] ?? date('Y-m-d')))) ?></td>
                            <td style="font-weight: 600;">₱<?= number_format($bill['amount'] ?? 0, 2) ?></td>
                            <td>
                                <span class="status-badge" style="background: <?= 
                                    ($bill['status'] ?? 'pending') == 'paid' ? '#d1fae5' : 
                                    (($bill['status'] ?? 'pending') == 'pending' ? '#fef3c7' : '#fee2e2'); 
                                ?>; color: <?= 
                                    ($bill['status'] ?? 'pending') == 'paid' ? '#065f46' : 
                                    (($bill['status'] ?? 'pending') == 'pending' ? '#92400e' : '#991b1b'); 
                                ?>;">
                                    <?= esc(ucfirst($bill['status'] ?? 'pending')) ?>
                                </span>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="6" style="text-align: center; padding: 40px; color: #94a3b8;">
                            No bills found for this patient.
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
<?php else: ?>
    <div style="background: white; border-radius: 12px; padding: 40px; text-align: center; box-shadow: 0 2px 6px rgba(0,0,0,0.08);">
        <i class="fas fa-search" style="font-size: 48px; color: #cbd5e1; margin-bottom: 16px;"></i>
        <h3 style="color: #64748b; margin: 0 0 8px 0;">Select a Patient</h3>
        <p style="color: #94a3b8; margin: 0;">Please select a patient from the dropdown above to view their billing history.</p>
    </div>
<?php endif; ?>
<?= $this->endSection() ?>

