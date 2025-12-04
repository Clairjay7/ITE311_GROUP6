<?php
/** @var string $roomType */
/** @var string $roomTypeDisplay */
/** @var array $rooms */
?>
<?= $this->extend('template/header') ?>
<?= $this->section('title') ?><?= esc($roomTypeDisplay) ?> - Rooms<?= $this->endSection() ?>

<?= $this->section('content') ?>
<style>
    .room-list-container {
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
        flex-wrap: wrap;
        gap: 16px;
    }
    
    .page-header h1 {
        margin: 0;
        font-size: 26px;
        font-weight: 700;
        display: flex;
        align-items: center;
        gap: 12px;
    }
    
    .page-header h1 i {
        font-size: 30px;
    }
    
    .back-btn {
        background: rgba(255, 255, 255, 0.2);
        color: white;
        padding: 10px 20px;
        border-radius: 10px;
        text-decoration: none;
        font-weight: 600;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        transition: all 0.3s ease;
    }
    
    .back-btn:hover {
        background: rgba(255, 255, 255, 0.3);
        color: white;
    }
    
    .room-type-badge {
        display: inline-block;
        padding: 6px 14px;
        border-radius: 20px;
        font-weight: 600;
        font-size: 13px;
        margin-left: 12px;
    }
    
    .room-type-badge.private {
        background: linear-gradient(135deg, #7c3aed 0%, #a78bfa 100%);
        color: white;
    }
    
    .room-type-badge.semi-private {
        background: linear-gradient(135deg, #0288d1 0%, #03a9f4 100%);
        color: white;
    }
    
    .room-type-badge.ward {
        background: linear-gradient(135deg, #10b981 0%, #34d399 100%);
        color: white;
    }
    
    .room-type-badge.icu {
        background: linear-gradient(135deg, #dc2626 0%, #ef4444 100%);
        color: white;
    }
    
    .room-type-badge.isolation {
        background: linear-gradient(135deg, #f59e0b 0%, #fbbf24 100%);
        color: white;
    }
    
    .form-card {
        background: white;
        border-radius: 16px;
        box-shadow: 0 2px 12px rgba(0, 0, 0, 0.08);
        border: 1px solid #e5e7eb;
        overflow: hidden;
    }
    
    .form-card-body {
        padding: 24px;
    }
    
    .alert {
        padding: 16px 20px;
        border-radius: 10px;
        margin-bottom: 20px;
        font-weight: 500;
    }
    
    .alert-danger {
        background: #fee2e2;
        color: #991b1b;
        border-left: 4px solid #ef4444;
    }
    
    .alert-success {
        background: #d1fae5;
        color: #065f46;
        border-left: 4px solid #10b981;
    }
    
    .table-modern {
        width: 100%;
        border-collapse: separate;
        border-spacing: 0;
    }
    
    .table-modern thead {
        background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
    }
    
    .table-modern th {
        padding: 16px;
        text-align: left;
        font-weight: 700;
        color: #1e293b;
        font-size: 13px;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        border-bottom: 2px solid #e5e7eb;
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
    }
    
    .badge {
        padding: 6px 12px;
        border-radius: 8px;
        font-weight: 600;
        font-size: 12px;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
    
    .badge-success {
        background: #d1fae5;
        color: #065f46;
    }
    
    .badge-danger {
        background: #fee2e2;
        color: #991b1b;
    }
    
    .btn {
        padding: 8px 16px;
        border-radius: 8px;
        font-weight: 600;
        font-size: 13px;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        transition: all 0.2s ease;
        border: none;
        cursor: pointer;
    }
    
    .btn-outline-primary {
        background: white;
        color: #0288d1;
        border: 2px solid #0288d1;
    }
    
    .btn-outline-primary:hover {
        background: #0288d1;
        color: white;
    }
    
    .btn-outline-warning {
        background: white;
        color: #f59e0b;
        border: 2px solid #f59e0b;
    }
    
    .btn-outline-warning:hover {
        background: #f59e0b;
        color: white;
    }
    
    .btn-primary {
        background: linear-gradient(135deg, #0288d1 0%, #03a9f4 100%);
        color: white;
    }
    
    .btn-primary:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(2, 136, 209, 0.3);
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
        margin: 0;
        color: #94a3b8;
        font-size: 15px;
    }
</style>

<div class="room-list-container">
    <div class="page-header">
        <h1>
            <i class="fas fa-bed"></i>
            <?= esc($roomTypeDisplay) ?> - Room Management
            <?php
            $badgeClass = 'ward';
            if ($roomType === 'Private') $badgeClass = 'private';
            elseif ($roomType === 'Semi-Private') $badgeClass = 'semi-private';
            elseif ($roomType === 'Ward') $badgeClass = 'ward';
            elseif ($roomType === 'ICU') $badgeClass = 'icu';
            elseif ($roomType === 'Isolation') $badgeClass = 'isolation';
            ?>
            <span class="room-type-badge <?= $badgeClass ?>">
                <?= esc($roomType) ?>
            </span>
        </h1>
        <a href="<?= site_url('receptionist/dashboard') ?>" class="back-btn">
            <i class="fas fa-arrow-left"></i> Back to Dashboard
        </a>
    </div>

    <?php if (session()->getFlashdata('error')): ?>
        <div class="alert alert-danger">
            <i class="fas fa-exclamation-circle"></i> <?= esc(session()->getFlashdata('error')) ?>
        </div>
    <?php endif; ?>
    
    <?php if (session()->getFlashdata('success')): ?>
        <div class="alert alert-success">
            <i class="fas fa-check-circle"></i> <?= esc(session()->getFlashdata('success')) ?>
        </div>
    <?php endif; ?>

    <div class="form-card">
        <div class="form-card-body">
            <?php if (!empty($rooms)): ?>
                <div style="overflow-x: auto;">
                    <table class="table-modern">
                        <thead>
                            <tr>
                                <th>Room Number</th>
                                <th>Ward</th>
                                <th>Price</th>
                                <th>Status</th>
                                <th>Patient</th>
                                <th style="text-align: right;">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($rooms as $room): ?>
                                <tr>
                                    <td>
                                        <strong style="font-size: 16px; color: #1e293b;">
                                            <?= esc($room['room_number']) ?>
                                        </strong>
                                    </td>
                                    <td>
                                        <?= esc($room['ward'] ?? 'N/A') ?>
                                    </td>
                                    <td>
                                        <?php if (!empty($room['price']) && $room['price'] > 0): ?>
                                            <strong style="color: #0288d1;">
                                                ₱<?= number_format((float)$room['price'], 2) ?>/day
                                            </strong>
                                        <?php else: ?>
                                            <span style="color: #94a3b8;">N/A</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php if (($room['status'] ?? '') === 'Occupied' || ($room['status'] ?? '') === 'occupied'): ?>
                                            <span class="badge badge-danger">Occupied</span>
                                        <?php else: ?>
                                            <span class="badge badge-success">Available</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php if (($room['status'] ?? '') === 'Occupied' || ($room['status'] ?? '') === 'occupied'): ?>
                                            <?php if (!empty($room['patient_name'])): ?>
                                                <strong><?= esc($room['patient_name']) ?></strong>
                                            <?php else: ?>
                                                <span style="color: #94a3b8;">Patient Assigned</span>
                                            <?php endif; ?>
                                        <?php else: ?>
                                            <span style="color: #94a3b8;">—</span>
                                        <?php endif; ?>
                                    </td>
                                    <td style="text-align: right;">
                                        <div style="display: flex; gap: 8px; justify-content: flex-end;">
                                            <?php if (($room['status'] ?? '') === 'Occupied' || ($room['status'] ?? '') === 'occupied'): ?>
                                                <?php if (!empty($room['current_patient_id'])): ?>
                                                    <a href="<?= site_url('receptionist/patients/show/' . $room['current_patient_id']) ?>" class="btn btn-outline-primary">
                                                        <i class="fas fa-user"></i> View Patient
                                                    </a>
                                                <?php endif; ?>
                                                <form method="post" action="<?= site_url('receptionist/rooms/vacate/' . $room['id']) ?>" style="display: inline;">
                                                    <?= csrf_field() ?>
                                                    <button type="submit" class="btn btn-outline-warning" onclick="return confirm('Mark this room as available?');">
                                                        <i class="fas fa-door-open"></i> Vacate Room
                                                    </button>
                                                </form>
                                            <?php else: ?>
                                                <a href="<?= site_url('receptionist/rooms/assign/' . $room['id']) ?>" class="btn btn-primary">
                                                    <i class="fas fa-user-plus"></i> Assign Patient
                                                </a>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <div class="empty-state">
                    <i class="fas fa-bed"></i>
                    <h5>No Rooms Found</h5>
                    <p>No rooms defined for <?= esc($roomTypeDisplay) ?>.</p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

