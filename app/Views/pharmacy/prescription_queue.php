<?= $this->extend('template/header') ?>

<?= $this->section('title') ?>
Prescription Queue
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<style>
    .prescription-container {
        background: #fff;
        border-radius: 12px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        padding: 24px;
        margin-bottom: 24px;
    }
    .prescription-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 20px;
        padding-bottom: 16px;
        border-bottom: 2px solid #4caf50;
    }
    .prescription-title {
        font-size: 24px;
        font-weight: 700;
        color: #2e7d32;
    }
    .prescription-table {
        width: 100%;
        border-collapse: collapse;
    }
    .prescription-table th {
        background: #f1f8e9;
        padding: 12px;
        text-align: left;
        font-weight: 600;
        color: #2e7d32;
        border-bottom: 2px solid #4caf50;
    }
    .prescription-table td {
        padding: 12px;
        border-bottom: 1px solid #e0e0e0;
    }
    .prescription-table tr:hover {
        background: #f9fbe7;
    }
    .status-badge {
        padding: 4px 12px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: 600;
        text-transform: uppercase;
    }
    .status-pending {
        background: #fff3cd;
        color: #856404;
    }
    .status-in-progress {
        background: #cfe2ff;
        color: #084298;
    }
    .btn-dispense {
        background: linear-gradient(135deg, #4caf50, #66bb6a);
        color: white;
        border: none;
        padding: 6px 16px;
        border-radius: 6px;
        cursor: pointer;
        font-size: 14px;
        transition: all 0.3s;
    }
    .btn-dispense:hover {
        background: linear-gradient(135deg, #388e3c, #4caf50);
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(76, 175, 80, 0.3);
    }
    .btn-view {
        background: #2196f3;
        color: white;
        border: none;
        padding: 6px 16px;
        border-radius: 6px;
        cursor: pointer;
        font-size: 14px;
        margin-right: 8px;
    }
    .empty-state {
        text-align: center;
        padding: 40px;
        color: #666;
    }
    .empty-state i {
        font-size: 48px;
        color: #ccc;
        margin-bottom: 16px;
    }
    .tab-btn {
        padding: 8px 16px;
        border: none;
        background: #f5f5f5;
        color: #666;
        border-radius: 6px;
        cursor: pointer;
        font-weight: 600;
        transition: all 0.3s;
    }
    .tab-btn.active {
        background: #4caf50;
        color: white;
    }
    .tab-btn:hover {
        background: #e0e0e0;
    }
    .tab-btn.active:hover {
        background: #388e3c;
    }
    .tab-content {
        display: none;
    }
    .tab-content.active {
        display: block;
    }
    .pharmacy-status-badge {
        padding: 4px 12px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: 600;
        text-transform: uppercase;
    }
    .pharmacy-status-pending {
        background: #fee2e2;
        color: #991b1b;
    }
    .pharmacy-status-approved {
        background: #dbeafe;
        color: #1e40af;
    }
    .pharmacy-status-prepared {
        background: #fef3c7;
        color: #92400e;
    }
    .pharmacy-status-dispensed {
        background: #d1fae5;
        color: #065f46;
    }
    .pharmacy-status-administered {
        background: #10b981;
        color: white;
    }
    .btn-approve {
        background: #2196f3;
        color: white;
        border: none;
        padding: 6px 16px;
        border-radius: 6px;
        cursor: pointer;
        font-size: 14px;
        margin-right: 8px;
    }
    .btn-prepare {
        background: #ff9800;
        color: white;
        border: none;
        padding: 6px 16px;
        border-radius: 6px;
        cursor: pointer;
        font-size: 14px;
        margin-right: 8px;
    }
</style>

<div class="prescription-container">
    <div class="prescription-header">
        <h1 class="prescription-title">📋 Prescription Queue</h1>
        <div style="display: flex; gap: 16px;">
            <span style="color: #666;">Pending: <strong><?= count($pendingPrescriptions ?? []) ?></strong></span>
            <span style="color: #666;">Approved: <strong><?= count($approvedPrescriptions ?? []) ?></strong></span>
            <span style="color: #666;">Prepared: <strong><?= count($preparedPrescriptions ?? []) ?></strong></span>
            <span style="color: #666;">Dispensed: <strong><?= count($dispensedPrescriptions ?? []) ?></strong></span>
            <span style="color: #10b981;">Administered: <strong><?= count($administeredPrescriptions ?? []) ?></strong></span>
        </div>
    </div>

    <div style="display: flex; gap: 12px; margin-bottom: 20px; border-bottom: 2px solid #e0e0e0; padding-bottom: 12px; flex-wrap: wrap;">
        <button class="tab-btn active" onclick="showTab('pending')" id="tab-pending-btn">Pending (<?= count($pendingPrescriptions ?? []) ?>)</button>
        <button class="tab-btn" onclick="showTab('approved')" id="tab-approved-btn">Approved (<?= count($approvedPrescriptions ?? []) ?>)</button>
        <button class="tab-btn" onclick="showTab('prepared')" id="tab-prepared-btn">Prepared (<?= count($preparedPrescriptions ?? []) ?>)</button>
        <button class="tab-btn" onclick="showTab('dispensed')" id="tab-dispensed-btn">Dispensed (<?= count($dispensedPrescriptions ?? []) ?>)</button>
        <button class="tab-btn" onclick="showTab('administered')" id="tab-administered-btn" style="background: #d1fae5; color: #065f46;">Administered (<?= count($administeredPrescriptions ?? []) ?>)</button>
    </div>

    <?php if (session()->getFlashdata('success')): ?>
        <div class="alert alert-success" style="padding: 12px; background: #d4edda; color: #155724; border-radius: 8px; margin-bottom: 16px;">
            <?= session()->getFlashdata('success') ?>
        </div>
    <?php endif; ?>

    <?php if (session()->getFlashdata('error')): ?>
        <div class="alert alert-danger" style="padding: 12px; background: #f8d7da; color: #721c24; border-radius: 8px; margin-bottom: 16px;">
            <?= session()->getFlashdata('error') ?>
        </div>
    <?php endif; ?>

    <!-- Pending Tab -->
    <div id="tab-pending" class="tab-content active">
        <?php if (empty($pendingPrescriptions)): ?>
            <div class="empty-state">
                <i class="fas fa-check-circle"></i>
                <h3>No Pending Prescriptions</h3>
                <p>All prescriptions have been processed.</p>
            </div>
        <?php else: ?>
            <table class="prescription-table">
                <thead>
                    <tr>
                        <th>Order ID</th>
                        <th>Patient</th>
                        <th>Doctor</th>
                        <th>Nurse</th>
                        <th>Medicine</th>
                        <th>Dosage</th>
                        <th>Frequency</th>
                        <th>Duration</th>
                        <th>Order Date</th>
                        <th>Pharmacy Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($pendingPrescriptions as $prescription): ?>
                        <tr>
                            <td>#<?= $prescription['id'] ?></td>
                            <td><strong><?= esc($prescription['patient_first'] . ' ' . $prescription['patient_last']) ?></strong></td>
                            <td><?= esc($prescription['doctor_name']) ?></td>
                            <td><?= esc($prescription['nurse_name'] ?? 'N/A') ?></td>
                            <td><strong><?= esc($prescription['medicine_name'] ?? $prescription['order_description']) ?></strong></td>
                            <td><?= esc($prescription['dosage'] ?? 'N/A') ?></td>
                            <td><?= esc($prescription['frequency'] ?? 'N/A') ?></td>
                            <td><?= esc($prescription['duration'] ?? 'N/A') ?></td>
                            <td><?= date('M d, Y', strtotime($prescription['order_date'])) ?></td>
                            <td>
                                <span class="pharmacy-status-badge pharmacy-status-<?= strtolower($prescription['pharmacy_status'] ?? 'pending') ?>">
                                    <?= ucfirst($prescription['pharmacy_status'] ?? 'Pending') ?>
                                </span>
                            </td>
                            <td>
                                <button class="btn-approve" onclick="updatePharmacyStatus(<?= $prescription['id'] ?>, 'approve')">
                                    <i class="fas fa-check"></i> Approve
                                </button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>

    <!-- Approved Tab -->
    <div id="tab-approved" class="tab-content">
        <?php if (empty($approvedPrescriptions)): ?>
            <div class="empty-state">
                <i class="fas fa-check-circle"></i>
                <h3>No Approved Prescriptions</h3>
                <p>No prescriptions are ready for preparation.</p>
            </div>
        <?php else: ?>
            <table class="prescription-table">
                <thead>
                    <tr>
                        <th>Order ID</th>
                        <th>Patient</th>
                        <th>Doctor</th>
                        <th>Nurse</th>
                        <th>Medicine</th>
                        <th>Dosage</th>
                        <th>Frequency</th>
                        <th>Duration</th>
                        <th>Approved At</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($approvedPrescriptions as $prescription): ?>
                        <tr>
                            <td>#<?= $prescription['id'] ?></td>
                            <td><strong><?= esc($prescription['patient_first'] . ' ' . $prescription['patient_last']) ?></strong></td>
                            <td><?= esc($prescription['doctor_name']) ?></td>
                            <td><?= esc($prescription['nurse_name'] ?? 'N/A') ?></td>
                            <td><strong><?= esc($prescription['medicine_name'] ?? $prescription['order_description']) ?></strong></td>
                            <td><?= esc($prescription['dosage'] ?? 'N/A') ?></td>
                            <td><?= esc($prescription['frequency'] ?? 'N/A') ?></td>
                            <td><?= esc($prescription['duration'] ?? 'N/A') ?></td>
                            <td><?= $prescription['pharmacy_approved_at'] ? date('M d, Y h:i A', strtotime($prescription['pharmacy_approved_at'])) : 'N/A' ?></td>
                            <td>
                                <button class="btn-prepare" onclick="updatePharmacyStatus(<?= $prescription['id'] ?>, 'prepare')">
                                    <i class="fas fa-box"></i> Prepare
                                </button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>

    <!-- Prepared Tab -->
    <div id="tab-prepared" class="tab-content">
        <?php if (empty($preparedPrescriptions)): ?>
            <div class="empty-state">
                <i class="fas fa-check-circle"></i>
                <h3>No Prepared Prescriptions</h3>
                <p>No prescriptions are ready for dispensing.</p>
            </div>
        <?php else: ?>
            <table class="prescription-table">
                <thead>
                    <tr>
                        <th>Order ID</th>
                        <th>Patient</th>
                        <th>Doctor</th>
                        <th>Nurse</th>
                        <th>Medicine</th>
                        <th>Dosage</th>
                        <th>Frequency</th>
                        <th>Duration</th>
                        <th>Prepared At</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($preparedPrescriptions as $prescription): ?>
                        <tr>
                            <td>#<?= $prescription['id'] ?></td>
                            <td><strong><?= esc($prescription['patient_first'] . ' ' . $prescription['patient_last']) ?></strong></td>
                            <td><?= esc($prescription['doctor_name']) ?></td>
                            <td><?= esc($prescription['nurse_name'] ?? 'N/A') ?></td>
                            <td><strong><?= esc($prescription['medicine_name'] ?? $prescription['order_description']) ?></strong></td>
                            <td><?= esc($prescription['dosage'] ?? 'N/A') ?></td>
                            <td><?= esc($prescription['frequency'] ?? 'N/A') ?></td>
                            <td><?= esc($prescription['duration'] ?? 'N/A') ?></td>
                            <td><?= $prescription['pharmacy_prepared_at'] ? date('M d, Y h:i A', strtotime($prescription['pharmacy_prepared_at'])) : 'N/A' ?></td>
                            <td>
                                <button class="btn-dispense" onclick="updatePharmacyStatus(<?= $prescription['id'] ?>, 'dispense')">
                                    <i class="fas fa-check-circle"></i> Dispense
                                </button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>

    <!-- Dispensed Tab -->
    <div id="tab-dispensed" class="tab-content">
        <?php if (empty($dispensedPrescriptions)): ?>
            <div class="empty-state">
                <i class="fas fa-check-circle"></i>
                <h3>No Dispensed Prescriptions</h3>
                <p>No prescriptions are waiting for nurse administration.</p>
            </div>
        <?php else: ?>
            <table class="prescription-table">
                <thead>
                    <tr>
                        <th>Order ID</th>
                        <th>Patient</th>
                        <th>Doctor</th>
                        <th>Nurse</th>
                        <th>Medicine</th>
                        <th>Dosage</th>
                        <th>Frequency</th>
                        <th>Duration</th>
                        <th>Dispensed At</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($dispensedPrescriptions as $prescription): ?>
                        <tr>
                            <td>#<?= $prescription['id'] ?></td>
                            <td><strong><?= esc($prescription['patient_first'] . ' ' . $prescription['patient_last']) ?></strong></td>
                            <td><?= esc($prescription['doctor_name']) ?></td>
                            <td><?= esc($prescription['nurse_name'] ?? 'N/A') ?></td>
                            <td><strong><?= esc($prescription['medicine_name'] ?? $prescription['order_description']) ?></strong></td>
                            <td><?= esc($prescription['dosage'] ?? 'N/A') ?></td>
                            <td><?= esc($prescription['frequency'] ?? 'N/A') ?></td>
                            <td><?= esc($prescription['duration'] ?? 'N/A') ?></td>
                            <td><?= $prescription['pharmacy_dispensed_at'] ? date('M d, Y h:i A', strtotime($prescription['pharmacy_dispensed_at'])) : 'N/A' ?></td>
                            <td>
                                <span class="pharmacy-status-badge pharmacy-status-dispensed">
                                    Waiting for Nurse
                                </span>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>

    <!-- Administered Tab -->
    <div id="tab-administered" class="tab-content">
        <?php if (empty($administeredPrescriptions)): ?>
            <div class="empty-state">
                <i class="fas fa-check-circle"></i>
                <h3>No Administered Prescriptions</h3>
                <p>No medications have been administered to patients yet.</p>
            </div>
        <?php else: ?>
            <table class="prescription-table">
                <thead>
                    <tr>
                        <th>Order ID</th>
                        <th>Patient</th>
                        <th>Doctor</th>
                        <th>Nurse</th>
                        <th>Medicine</th>
                        <th>Dosage</th>
                        <th>Frequency</th>
                        <th>Duration</th>
                        <th>Dispensed At</th>
                        <th>Administered By</th>
                        <th>Administered At</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($administeredPrescriptions as $prescription): ?>
                        <tr style="background: #f0fdf4;">
                            <td>#<?= $prescription['id'] ?></td>
                            <td><strong><?= esc($prescription['patient_first'] . ' ' . $prescription['patient_last']) ?></strong></td>
                            <td><?= esc($prescription['doctor_name']) ?></td>
                            <td><?= esc($prescription['nurse_name'] ?? 'N/A') ?></td>
                            <td><strong><?= esc($prescription['medicine_name'] ?? $prescription['order_description']) ?></strong></td>
                            <td><?= esc($prescription['dosage'] ?? 'N/A') ?></td>
                            <td><?= esc($prescription['frequency'] ?? 'N/A') ?></td>
                            <td><?= esc($prescription['duration'] ?? 'N/A') ?></td>
                            <td><?= $prescription['pharmacy_dispensed_at'] ? date('M d, Y h:i A', strtotime($prescription['pharmacy_dispensed_at'])) : 'N/A' ?></td>
                            <td>
                                <strong style="color: #065f46;">
                                    <i class="fas fa-user-nurse"></i> <?= esc($prescription['administered_by_name'] ?? 'N/A') ?>
                                </strong>
                            </td>
                            <td>
                                <strong style="color: #065f46;">
                                    <?= $prescription['completed_at'] ? date('M d, Y h:i A', strtotime($prescription['completed_at'])) : 'N/A' ?>
                                </strong>
                            </td>
                            <td>
                                <span class="pharmacy-status-badge pharmacy-status-administered">
                                    <i class="fas fa-check-circle"></i> ADMINISTERED
                                </span>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>
</div>

<script>
function showTab(tabName) {
    // Hide all tabs
    document.querySelectorAll('.tab-content').forEach(tab => {
        tab.classList.remove('active');
    });
    document.querySelectorAll('.tab-btn').forEach(btn => {
        btn.classList.remove('active');
    });
    
    // Show selected tab
    document.getElementById('tab-' + tabName).classList.add('active');
    document.getElementById('tab-' + tabName + '-btn').classList.add('active');
}

async function updatePharmacyStatus(orderId, action) {
    const actionNames = {
        'approve': 'approve',
        'prepare': 'prepare',
        'dispense': 'dispense'
    };
    
    const actionText = actionNames[action] || action;
    
    if (!confirm(`Are you sure you want to ${actionText} this prescription?`)) {
        return;
    }

    try {
        const formData = new FormData();
        formData.append('action', action);

        const response = await fetch(`<?= site_url('pharmacy/update-pharmacy-status/') ?>${orderId}`, {
            method: 'POST',
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        });

        const data = await response.json();

        if (data.success) {
            alert(data.message);
            location.reload();
        } else {
            alert('Error: ' + data.message);
        }
    } catch (error) {
        console.error('Error:', error);
        alert('Failed to update pharmacy status');
    }
}
</script>

<?= $this->endSection() ?>

