<?= $this->extend('template/header') ?>

<?= $this->section('title') ?>Admission Orders<?= $this->endSection() ?>

<?= $this->section('content') ?>
<style>
    .page-header {
        background: linear-gradient(135deg, #2e7d32 0%, #4caf50 100%);
        border-radius: 16px;
        padding: 24px 32px;
        margin-bottom: 24px;
        box-shadow: 0 4px 20px rgba(46, 125, 50, 0.2);
        color: white;
    }
    .modern-card {
        background: white;
        border-radius: 16px;
        box-shadow: 0 2px 12px rgba(0, 0, 0, 0.08);
        border: 1px solid #e5e7eb;
        margin-bottom: 24px;
        overflow: hidden;
    }
    .card-header {
        background: linear-gradient(135deg, #e8f5e9 0%, #f1f8f4 100%);
        padding: 20px 24px;
        border-bottom: 2px solid #c8e6c9;
    }
    .card-body {
        padding: 24px;
    }
    .btn-modern {
        padding: 10px 20px;
        border-radius: 10px;
        font-weight: 600;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 8px;
    }
    .btn-primary {
        background: #2e7d32;
        color: white;
    }
    .order-section {
        margin-bottom: 32px;
    }
    .order-item {
        background: #f8fafc;
        border: 1px solid #e5e7eb;
        border-radius: 8px;
        padding: 16px;
        margin-bottom: 12px;
    }
</style>

<div class="page-header">
    <div>
        <h1><i class="fas fa-hospital"></i> Admission Orders</h1>
        <p style="margin: 8px 0 0; opacity: 0.9;">
            <?= esc(ucwords($admission['firstname'] . ' ' . $admission['lastname'])) ?> - 
            Room <?= esc($admission['room_number'] ?? 'N/A') ?>
        </p>
    </div>
    <a href="<?= site_url('doctor/admission-orders/create/' . $admission['id']) ?>" class="btn-modern btn-primary">
        <i class="fas fa-plus"></i> Create New Orders
    </a>
</div>

<?php if (session()->getFlashdata('error')): ?>
    <div style="background: #fee2e2; color: #991b1b; padding: 12px; border-radius: 8px; margin-bottom: 16px;">
        <?= session()->getFlashdata('error') ?>
    </div>
<?php endif; ?>

<?php if (session()->getFlashdata('success')): ?>
    <div style="background: #d1fae5; color: #065f46; padding: 12px; border-radius: 8px; margin-bottom: 16px;">
        <?= session()->getFlashdata('success') ?>
    </div>
<?php endif; ?>

<!-- Patient Information -->
<div class="modern-card">
    <div class="card-header">
        <h3 style="margin: 0; color: #2e7d32;"><i class="fas fa-user-injured"></i> Patient Information</h3>
    </div>
    <div class="card-body">
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 16px;">
            <div>
                <small style="color: #64748b;">Name</small>
                <div style="font-weight: 600;"><?= esc(ucwords($admission['firstname'] . ' ' . $admission['lastname'])) ?></div>
            </div>
            <div>
                <small style="color: #64748b;">Room</small>
                <div style="font-weight: 600;"><?= esc($admission['room_number'] ?? 'N/A') ?> - <?= esc($admission['ward'] ?? 'N/A') ?></div>
            </div>
            <div>
                <small style="color: #64748b;">Admission Date</small>
                <div style="font-weight: 600;"><?= date('M d, Y', strtotime($admission['admission_date'])) ?></div>
            </div>
            <div>
                <small style="color: #64748b;">Admission Reason</small>
                <div style="font-weight: 600;"><?= esc($admission['admission_reason'] ?? 'N/A') ?></div>
            </div>
        </div>
        <?php if (!empty($admission['diagnosis'])): ?>
            <div style="margin-top: 16px; padding: 12px; background: #fef3c7; border-radius: 8px;">
                <strong style="color: #92400e;">Diagnosis:</strong>
                <div style="color: #78350f; margin-top: 4px;"><?= esc($admission['diagnosis']) ?></div>
            </div>
        <?php endif; ?>
    </div>
</div>

<!-- Latest Vitals -->
<?php if (!empty($latestVitals)): ?>
<div class="modern-card">
    <div class="card-header">
        <h3 style="margin: 0; color: #2e7d32;"><i class="fas fa-heartbeat"></i> Latest Vitals</h3>
    </div>
    <div class="card-body">
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(150px, 1fr)); gap: 16px;">
            <?php if (!empty($latestVitals['blood_pressure_systolic'])): ?>
                <div>
                    <small style="color: #64748b;">Blood Pressure</small>
                    <div style="font-weight: 600;">
                        <?= $latestVitals['blood_pressure_systolic'] ?>/<?= $latestVitals['blood_pressure_diastolic'] ?? '' ?>
                    </div>
                </div>
            <?php endif; ?>
            <?php if (!empty($latestVitals['temperature'])): ?>
                <div>
                    <small style="color: #64748b;">Temperature</small>
                    <div style="font-weight: 600;"><?= $latestVitals['temperature'] ?>°C</div>
                </div>
            <?php endif; ?>
            <?php if (!empty($latestVitals['heart_rate'])): ?>
                <div>
                    <small style="color: #64748b;">Heart Rate</small>
                    <div style="font-weight: 600;"><?= $latestVitals['heart_rate'] ?> bpm</div>
                </div>
            <?php endif; ?>
        </div>
        <?php if (!empty($latestVitals['recorded_at'])): ?>
            <div style="margin-top: 12px; color: #64748b; font-size: 12px;">
                Recorded: <?= date('M d, Y h:i A', strtotime($latestVitals['recorded_at'])) ?>
            </div>
        <?php endif; ?>
    </div>
</div>
<?php endif; ?>

<!-- Existing Orders -->
<div class="modern-card">
    <div class="card-header">
        <h3 style="margin: 0; color: #2e7d32;"><i class="fas fa-list"></i> Existing Orders</h3>
    </div>
    <div class="card-body">
        <?php if (!empty($existingOrders)): ?>
            <?php foreach ($ordersByType as $type => $orders): ?>
                <?php if (!empty($orders)): ?>
                    <div class="order-section">
                        <h4 style="color: #2e7d32; margin-bottom: 12px;">
                            <?= ucfirst(str_replace('_', ' ', $type)) ?> Orders
                        </h4>
                        <?php foreach ($orders as $order): ?>
                            <div class="order-item">
                                <div style="display: flex; justify-content: space-between; align-items: start;">
                                    <div style="flex: 1;">
                                        <strong><?= esc($order['order_description']) ?></strong>
                                        <?php if (!empty($order['medicine_name'])): ?>
                                            <div style="margin-top: 4px; color: #64748b;">
                                                <small>Medication: <?= esc($order['medicine_name']) ?></small>
                                            </div>
                                        <?php endif; ?>
                                        <?php if (!empty($order['instructions'])): ?>
                                            <div style="margin-top: 8px; color: #475569;">
                                                <?= esc($order['instructions']) ?>
                                            </div>
                                        <?php endif; ?>
                                        <?php if (!empty($order['dosage']) || !empty($order['frequency'])): ?>
                                            <div style="margin-top: 8px; font-size: 13px; color: #64748b;">
                                                <?php if (!empty($order['dosage'])): ?>
                                                    Dosage: <?= esc($order['dosage']) ?>
                                                <?php endif; ?>
                                                <?php if (!empty($order['frequency'])): ?>
                                                    | Frequency: <?= esc($order['frequency']) ?>
                                                <?php endif; ?>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                    <div>
                                        <span style="padding: 4px 12px; border-radius: 6px; font-size: 12px; font-weight: 600;
                                            background: <?= $order['status'] == 'completed' ? '#d1fae5' : ($order['status'] == 'in_progress' ? '#dbeafe' : '#fef3c7'); ?>;
                                            color: <?= $order['status'] == 'completed' ? '#065f46' : ($order['status'] == 'in_progress' ? '#1e40af' : '#92400e'); ?>;">
                                            <?= ucfirst($order['status']) ?>
                                        </span>
                                        <?php if ($order['order_type'] == 'medication' && !empty($order['pharmacy_status'])): ?>
                                            <div style="margin-top: 4px;">
                                                <span style="padding: 4px 12px; border-radius: 6px; font-size: 12px; font-weight: 600;
                                                    background: <?= $order['pharmacy_status'] == 'dispensed' ? '#d1fae5' : ($order['pharmacy_status'] == 'approved' ? '#dbeafe' : '#fef3c7'); ?>;
                                                    color: <?= $order['pharmacy_status'] == 'dispensed' ? '#065f46' : ($order['pharmacy_status'] == 'approved' ? '#1e40af' : '#92400e'); ?>;">
                                                    Pharmacy: <?= ucfirst($order['pharmacy_status']) ?>
                                                </span>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                <div style="margin-top: 8px; font-size: 12px; color: #94a3b8;">
                                    Created: <?= date('M d, Y h:i A', strtotime($order['created_at'])) ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            <?php endforeach; ?>
        <?php else: ?>
            <div style="text-align: center; padding: 40px; color: #94a3b8;">
                <i class="fas fa-clipboard-list" style="font-size: 48px; margin-bottom: 16px; opacity: 0.5;"></i>
                <p>No orders yet. Create admission orders to get started.</p>
            </div>
        <?php endif; ?>
    </div>
</div>

<!-- Lab Results -->
<?php if (!empty($labResults)): ?>
<div class="modern-card">
    <div class="card-header">
        <h3 style="margin: 0; color: #2e7d32;"><i class="fas fa-vial"></i> Completed Lab Results</h3>
    </div>
    <div class="card-body">
        <?php foreach ($labResults as $result): ?>
            <div class="order-item" style="border-left: 4px solid #2e7d32;">
                <div style="display: flex; justify-content: space-between; align-items: start;">
                    <div style="flex: 1;">
                        <strong style="color: #2e7d32; font-size: 16px;">
                            <?= esc($result['test_name']) ?>
                        </strong>
                        <div style="margin-top: 8px; color: #64748b; font-size: 13px;">
                            Type: <?= esc($result['test_type'] ?? 'Laboratory') ?>
                        </div>
                        <?php if (!empty($result['result'])): ?>
                            <div style="margin-top: 12px; padding: 12px; background: #f8fafc; border-radius: 8px;">
                                <strong style="color: #475569;">Result:</strong>
                                <div style="margin-top: 4px; color: #1e293b;">
                                    <?= nl2br(esc($result['result'])) ?>
                                </div>
                            </div>
                        <?php endif; ?>
                        <?php if (!empty($result['interpretation'])): ?>
                            <div style="margin-top: 12px; padding: 12px; background: #e8f5e9; border-radius: 8px;">
                                <strong style="color: #2e7d32;">Interpretation:</strong>
                                <div style="margin-top: 4px; color: #1e293b;">
                                    <?= nl2br(esc($result['interpretation'])) ?>
                                </div>
                            </div>
                        <?php endif; ?>
                        <?php if (!empty($result['result_file'])): ?>
                            <div style="margin-top: 12px;">
                                <a href="<?= base_url('uploads/lab_results/' . $result['result_file']) ?>" 
                                   target="_blank" 
                                   class="btn-modern" 
                                   style="background: #0288d1; color: white; padding: 8px 16px; font-size: 13px;">
                                    <i class="fas fa-file-pdf"></i> View Result File
                                </a>
                            </div>
                        <?php endif; ?>
                    </div>
                    <div style="text-align: right;">
                        <span style="padding: 6px 14px; border-radius: 8px; font-size: 12px; font-weight: 600;
                            background: #d1fae5; color: #065f46;">
                            <i class="fas fa-check-circle"></i> Completed
                        </span>
                    </div>
                </div>
                <div style="margin-top: 12px; padding-top: 12px; border-top: 1px solid #e5e7eb; font-size: 12px; color: #64748b;">
                    <div style="display: flex; justify-content: space-between; flex-wrap: wrap; gap: 8px;">
                        <div>
                            <i class="fas fa-calendar"></i> 
                            Completed: <?= !empty($result['completed_at']) ? date('M d, Y h:i A', strtotime($result['completed_at'])) : 'N/A' ?>
                        </div>
                        <?php if (!empty($result['completed_by_name'])): ?>
                            <div>
                                <i class="fas fa-user"></i> 
                                By: <?= esc($result['completed_by_name']) ?>
                            </div>
                        <?php endif; ?>
                        <div>
                            <i class="fas fa-clock"></i> 
                            Requested: <?= date('M d, Y', strtotime($result['created_at'])) ?>
                        </div>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>
<?php endif; ?>

<div style="text-align: center; margin-top: 24px;">
    <a href="<?= site_url('doctor/admission-orders') ?>" class="btn-modern" style="background: #f1f5f9; color: #475569;">
        <i class="fas fa-arrow-left"></i> Back to Admitted Patients
    </a>
</div>

<?= $this->endSection() ?>

