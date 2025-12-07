<?= $this->extend('template/header') ?>

<?= $this->section('title') ?><?= esc($title) ?><?= $this->endSection() ?>

<?= $this->section('content') ?>
<style>
    .admin-module { padding: 24px; }
    .module-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px; }
    .module-header h2 { margin: 0; color: #2e7d32; }
    .btn { padding: 10px 20px; border-radius: 6px; text-decoration: none; display: inline-block; }
    .btn-primary { background: #2e7d32; color: white; }
    .btn-sm { padding: 6px 12px; font-size: 14px; }
    .btn-edit { background: #3b82f6; color: white; margin-right: 8px; }
    .btn-delete { background: #ef4444; color: white; }
    .table-container { background: white; border-radius: 8px; overflow: hidden; margin-bottom: 24px; box-shadow: 0 2px 8px rgba(0,0,0,0.1); }
    .data-table { width: 100%; border-collapse: collapse; }
    .data-table th { background: #e8f5e9; padding: 12px; text-align: left; font-weight: 600; color: #2e7d32; }
    .data-table td { padding: 12px; border-bottom: 1px solid #e5e7eb; }
    .text-center { text-align: center; }
    .alert { padding: 12px; border-radius: 6px; margin-bottom: 16px; }
    .alert-success { background: #d1fae5; color: #047857; }
    
    /* Prescription Queue Styles */
    .prescription-container { background: white; border-radius: 12px; box-shadow: 0 2px 8px rgba(0,0,0,0.1); padding: 24px; margin-bottom: 24px; }
    .prescription-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; padding-bottom: 16px; border-bottom: 2px solid #4caf50; }
    .prescription-title { font-size: 24px; font-weight: 700; color: #2e7d32; }
    .prescription-table { width: 100%; border-collapse: collapse; }
    .prescription-table th { background: #f1f8e9; padding: 12px; text-align: left; font-weight: 600; color: #2e7d32; border-bottom: 2px solid #4caf50; }
    .prescription-table td { padding: 12px; border-bottom: 1px solid #e0e0e0; }
    .prescription-table tr:hover { background: #f9fbe7; }
    .pharmacy-status-badge { padding: 4px 12px; border-radius: 20px; font-size: 12px; font-weight: 600; text-transform: uppercase; }
    .pharmacy-status-pending { background: #fff3cd; color: #856404; }
    .pharmacy-status-approved { background: #cfe2ff; color: #084298; }
    .pharmacy-status-prepared { background: #d1ecf1; color: #0c5460; }
    .pharmacy-status-dispensed { background: #d1fae5; color: #065f46; }
    .read-only-badge { background: #e5e7eb; color: #6b7280; padding: 4px 8px; border-radius: 4px; font-size: 11px; font-weight: 600; }
    
    .stock-tabs { display: flex; gap: 12px; margin-bottom: 20px; border-bottom: 2px solid #e0e0e0; }
    .tab { padding: 12px 24px; background: transparent; border: none; border-bottom: 3px solid transparent; cursor: pointer; font-size: 14px; font-weight: 600; color: #666; transition: all 0.3s; }
    .tab.active { color: #2e7d32; border-bottom-color: #4caf50; }
    .tab:hover { color: #2e7d32; }
    .tab-content { display: none; }
    .tab-content.active { display: block; }
    .stock-table { width: 100%; border-collapse: collapse; }
    .stock-table th { background: #f1f8e9; padding: 12px; text-align: left; font-weight: 600; color: #2e7d32; border-bottom: 2px solid #4caf50; }
    .stock-table td { padding: 12px; border-bottom: 1px solid #e0e0e0; }
    .stock-table tr:hover { background: #f9fbe7; }
    .stock-level { padding: 4px 12px; border-radius: 20px; font-size: 12px; font-weight: 600; text-transform: uppercase; }
    .stock-critical { background: #ffebee; color: #c62828; }
    .stock-low { background: #fff3e0; color: #e65100; }
    .stock-normal { background: #e8f5e9; color: #2e7d32; }
    .empty-state { text-align: center; padding: 40px; color: #666; }
    .empty-state i { font-size: 48px; color: #ccc; margin-bottom: 16px; }
    
    /* Medicine Inventory Styles */
    .inventory-container { background: white; border-radius: 12px; box-shadow: 0 2px 8px rgba(0,0,0,0.1); padding: 24px; margin-bottom: 24px; }
    .inventory-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; padding-bottom: 16px; border-bottom: 2px solid #4caf50; }
    .inventory-title { font-size: 24px; font-weight: 700; color: #2e7d32; }
    .inventory-table { width: 100%; border-collapse: collapse; font-size: 13px; }
    .inventory-table th { background: #f1f8e9; padding: 12px 8px; text-align: left; font-weight: 600; color: #2e7d32; border-bottom: 2px solid #4caf50; font-size: 12px; white-space: nowrap; }
    .inventory-table td { padding: 12px 8px; border-bottom: 1px solid #e0e0e0; vertical-align: top; }
    .inventory-table tr:hover { background: #f9fbe7; }
    .reorder-alert { background: #fee2e2; color: #991b1b; padding: 4px 8px; border-radius: 4px; font-size: 11px; font-weight: 600; }
    .expiring-soon { background: #fff3cd; color: #856404; padding: 4px 8px; border-radius: 4px; font-size: 11px; font-weight: 600; }
    .expired { background: #fee2e2; color: #991b1b; padding: 4px 8px; border-radius: 4px; font-size: 11px; font-weight: 600; }
    
    /* Responsive table wrapper */
    .table-wrapper { overflow-x: auto; -webkit-overflow-scrolling: touch; }
    @media (max-width: 1200px) {
        .inventory-table { font-size: 12px; }
        .inventory-table th, .inventory-table td { padding: 8px 6px; }
    }
    
    /* Patient Medication Record Styles */
    .medication-record-container { background: white; border-radius: 12px; box-shadow: 0 2px 8px rgba(0,0,0,0.1); padding: 24px; margin-bottom: 24px; }
    .medication-record-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; padding-bottom: 16px; border-bottom: 2px solid #4caf50; }
    .medication-record-title { font-size: 24px; font-weight: 700; color: #2e7d32; }
    .patient-card { background: #f9fafb; border: 1px solid #e5e7eb; border-radius: 8px; padding: 16px; margin-bottom: 16px; }
    .patient-card-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 12px; }
    .patient-name { font-size: 18px; font-weight: 700; color: #1f2937; }
    .allergy-badge { background: #fee2e2; color: #991b1b; padding: 4px 12px; border-radius: 20px; font-size: 12px; font-weight: 600; margin-left: 12px; }
    .medication-item { background: white; border-left: 4px solid #4caf50; padding: 12px; margin-bottom: 8px; border-radius: 4px; }
    .medication-item-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 8px; }
    .medication-name { font-weight: 600; color: #2e7d32; }
    .medication-date { color: #64748b; font-size: 12px; }
    .medication-details { display: grid; grid-template-columns: repeat(auto-fit, minmax(150px, 1fr)); gap: 8px; font-size: 13px; color: #374151; }
</style>

<div class="admin-module">
    <div class="module-header">
        <h2><?= esc($title) ?></h2>
    </div>

    <?php if (session()->getFlashdata('success')): ?>
        <div class="alert alert-success"><?= session()->getFlashdata('success') ?></div>
    <?php endif; ?>

    <!-- Prescription Queue Section (Read-Only) -->
    <div class="prescription-container">
        <div class="prescription-header">
            <div>
                <h3 class="prescription-title">📋 Prescription Queue</h3>
                <span class="read-only-badge">Read Only</span>
            </div>
        </div>
        
        <div class="stock-tabs">
            <button class="tab active" onclick="showPrescriptionTab('pending')">
                Pending (<?= count($pendingPrescriptions ?? []) ?>)
            </button>
            <button class="tab" onclick="showPrescriptionTab('approved')">
                Approved (<?= count($approvedPrescriptions ?? []) ?>)
            </button>
            <button class="tab" onclick="showPrescriptionTab('prepared')">
                Prepared (<?= count($preparedPrescriptions ?? []) ?>)
            </button>
            <button class="tab" onclick="showPrescriptionTab('dispensed')">
                Dispensed (<?= count($dispensedPrescriptions ?? []) ?>)
            </button>
        </div>

        <!-- Pending Tab -->
        <div id="prescription-pending" class="tab-content active">
            <?php if (empty($pendingPrescriptions ?? [])): ?>
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
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach (($pendingPrescriptions ?? []) as $prescription): ?>
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
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </div>

        <!-- Approved Tab -->
        <div id="prescription-approved" class="tab-content">
            <?php if (empty($approvedPrescriptions ?? [])): ?>
                <div class="empty-state">
                    <i class="fas fa-check-circle"></i>
                    <h3>No Approved Prescriptions</h3>
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
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach (($approvedPrescriptions ?? []) as $prescription): ?>
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
                                    <span class="pharmacy-status-badge pharmacy-status-<?= strtolower($prescription['pharmacy_status'] ?? 'approved') ?>">
                                        <?= ucfirst($prescription['pharmacy_status'] ?? 'Approved') ?>
                                    </span>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </div>

        <!-- Prepared Tab -->
        <div id="prescription-prepared" class="tab-content">
            <?php if (empty($preparedPrescriptions ?? [])): ?>
                <div class="empty-state">
                    <i class="fas fa-check-circle"></i>
                    <h3>No Prepared Prescriptions</h3>
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
                            <th>Prepared Date</th>
                            <th>Pharmacy Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach (($preparedPrescriptions ?? []) as $prescription): ?>
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
                                    <span class="pharmacy-status-badge pharmacy-status-<?= strtolower($prescription['pharmacy_status'] ?? 'prepared') ?>">
                                        <?= ucfirst($prescription['pharmacy_status'] ?? 'Prepared') ?>
                                    </span>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </div>

        <!-- Dispensed Tab -->
        <div id="prescription-dispensed" class="tab-content">
            <?php if (empty($dispensedPrescriptions ?? [])): ?>
                <div class="empty-state">
                    <i class="fas fa-check-circle"></i>
                    <h3>No Dispensed Prescriptions</h3>
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
                            <th>Dispensed Date</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach (($dispensedPrescriptions ?? []) as $prescription): ?>
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
                                        Dispensed
                                    </span>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </div>
    </div>

    <!-- Medicine Inventory Module -->
    <div class="inventory-container">
        <div class="inventory-header">
            <h3 class="inventory-title">💊 Medicine Inventory Module</h3>
            <div style="display: flex; gap: 12px; align-items: center;">
                <span style="color: #64748b; font-size: 14px;">
                    Total: <strong style="color: #2e7d32;"><?= number_format($inventoryPager->total ?? 0) ?></strong> medicines
                </span>
            </div>
        </div>
        
        <!-- Category Statistics -->
        <?php if (!empty($categoryCountMap ?? [])): ?>
            <div style="margin-bottom: 20px; padding: 16px; background: #f9fafb; border-radius: 8px; border: 1px solid #e5e7eb;">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 12px;">
                    <h4 style="margin: 0; color: #374151; font-size: 16px; font-weight: 600;">
                        <i class="fas fa-chart-pie" style="margin-right: 8px; color: #2e7d32;"></i>Category Statistics
                    </h4>
                </div>
                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 12px;">
                    <?php foreach (($validCategories ?? []) as $category): ?>
                        <?php $count = $categoryCountMap[$category] ?? 0; ?>
                        <div style="padding: 12px; background: white; border-radius: 6px; border-left: 4px solid #2e7d32;">
                            <div style="font-size: 12px; color: #64748b; margin-bottom: 4px;">
                                <?= esc($category) ?>
                            </div>
                            <div style="font-size: 20px; font-weight: 700; color: #2e7d32;">
                                <?= $count ?> <span style="font-size: 12px; font-weight: 400; color: #64748b;">medicines</span>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php endif; ?>
        
        <!-- Search Bar and Category Filter -->
        <div style="margin-bottom: 20px;">
            <form method="get" action="<?= base_url('admin/pharmacy') ?>" style="display: flex; gap: 12px; align-items: center; flex-wrap: wrap;">
                <input type="text" name="inventory_search" id="inventorySearchInput" 
                       value="<?= esc($inventorySearch ?? '') ?>"
                       placeholder="🔍 Search by medicine name, generic name, category, strength, dosage form, batch number, or supplier..." 
                       style="flex: 1; min-width: 250px; padding: 12px 16px; border: 2px solid #e0e0e0; border-radius: 8px; font-size: 14px; transition: border-color 0.3s;"
                       onkeyup="if(event.key === 'Enter') this.form.submit();">
                <select name="inventory_category" id="inventoryCategoryFilter" 
                        style="padding: 12px 16px; border: 2px solid #e0e0e0; border-radius: 8px; font-size: 14px; min-width: 200px; background: white; cursor: pointer;"
                        onchange="this.form.submit();">
                    <option value="">All Categories</option>
                    <?php foreach (($validCategories ?? []) as $category): ?>
                        <option value="<?= esc($category) ?>" <?= ($selectedCategory ?? '') === $category ? 'selected' : '' ?>>
                            <?= esc($category) ?> (<?= $categoryCountMap[$category] ?? 0 ?>)
                        </option>
                    <?php endforeach; ?>
                </select>
                <button type="submit" style="padding: 12px 24px; background: #2e7d32; color: white; border: none; border-radius: 8px; font-weight: 600; cursor: pointer; transition: background 0.3s;">
                    <i class="fas fa-search"></i> Search
                </button>
                <?php if (!empty($inventorySearch) || !empty($selectedCategory)): ?>
                    <a href="<?= base_url('admin/pharmacy') ?>" style="padding: 12px 24px; background: #6b7280; color: white; border: none; border-radius: 8px; font-weight: 600; text-decoration: none; transition: background 0.3s;">
                        <i class="fas fa-times"></i> Clear
                    </a>
                <?php endif; ?>
            </form>
        </div>
        
        <div class="table-wrapper">
        <table class="inventory-table">
            <thead>
                <tr>
                    <th>Medicine Name</th>
                    <th>Generic Name</th>
                    <th>Category</th>
                    <th>Strength</th>
                    <th>Dosage Form</th>
                    <th>Available Stock</th>
                    <th>Batch Number</th>
                    <th>Expiration Date</th>
                    <th>Unit Price</th>
                    <th>Selling Price</th>
                    <th>Markup %</th>
                    <th>Reorder Level</th>
                    <th>Alert</th>
                    <th>Supplier</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($inventoryMedicines ?? [])): ?>
                    <tr>
                        <td colspan="14" style="text-align: center; padding: 40px; color: #666;">
                            <i class="fas fa-inbox" style="font-size: 48px; margin-bottom: 16px; display: block; color: #cbd5e1;"></i>
                            No medicines in inventory
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach (($inventoryMedicines ?? []) as $medicine): ?>
                        <?php
                        $quantity = $medicine['quantity'] ?? 0;
                        $reorderLevel = $medicine['reorder_level'] ?? 10;
                        $expirationDate = $medicine['expiration_date'] ?? null;
                        $isLowStock = $quantity <= $reorderLevel;
                        $isExpiring = false;
                        $isExpired = false;
                        
                        if ($expirationDate) {
                            $expDate = new \DateTime($expirationDate);
                            $today = new \DateTime();
                            $daysUntilExpiry = $today->diff($expDate)->days;
                            
                            if ($expDate < $today) {
                                $isExpired = true;
                            } elseif ($daysUntilExpiry <= 30) {
                                $isExpiring = true;
                            }
                        }
                        
                        $unitPrice = $medicine['unit_price'] ?? 0;
                        $sellingPrice = $medicine['selling_price'] ?? $medicine['price'] ?? 0;
                        $markupPercent = $medicine['markup_percent'] ?? 0;
                        ?>
                        <tr>
                            <td>
                                <strong style="color: #1f2937;"><?= esc($medicine['item_name']) ?></strong>
                                <?php if (!empty($medicine['description'])): ?>
                                    <br>
                                    <small style="color: #64748b; font-size: 11px;" title="<?= esc($medicine['description']) ?>">
                                        <i class="fas fa-info-circle"></i> <?= esc(strlen($medicine['description']) > 50 ? substr($medicine['description'], 0, 50) . '...' : $medicine['description']) ?>
                                    </small>
                                <?php endif; ?>
                            </td>
                            <td>
                                <span style="color: #64748b; font-size: 13px;">
                                    <?= esc($medicine['generic_name'] ?? 'N/A') ?>
                                </span>
                            </td>
                            <td>
                                <span style="font-size: 12px; color: #64748b; font-weight: 500;">
                                    <?= esc($medicine['category'] ?? 'N/A') ?>
                                </span>
                            </td>
                            <td>
                                <span style="font-weight: 500; color: #374151;">
                                    <?= esc($medicine['strength'] ?? 'N/A') ?>
                                </span>
                            </td>
                            <td>
                                <span style="font-size: 12px; color: #6b7280;">
                                    <?= esc($medicine['dosage_form'] ?? 'N/A') ?>
                                </span>
                            </td>
                            <td>
                                <strong style="color: <?= $isLowStock ? '#ef4444' : '#2e7d32' ?>; font-size: 16px;">
                                    <?= $quantity ?>
                                </strong>
                            </td>
                            <td>
                                <?php if (!empty($medicine['batch_number'])): ?>
                                    <span style="font-weight: 600; color: #2e7d32; font-family: monospace;"><?= esc($medicine['batch_number']) ?></span>
                                <?php else: ?>
                                    <span style="color: #9ca3af;">N/A</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if ($expirationDate): ?>
                                    <div style="font-weight: 500; color: #374151;">
                                        <?= date('M d, Y', strtotime($expirationDate)) ?>
                                    </div>
                                    <?php if ($isExpired): ?>
                                        <small style="color: #ef4444; font-weight: 600; display: block; margin-top: 4px;">
                                            <i class="fas fa-exclamation-triangle"></i> Expired
                                        </small>
                                    <?php elseif ($isExpiring): ?>
                                        <small style="color: #f59e0b; font-weight: 600; display: block; margin-top: 4px;">
                                            <i class="fas fa-clock"></i> Expiring Soon
                                        </small>
                                    <?php endif; ?>
                                <?php else: ?>
                                    <span style="color: #9ca3af;">N/A</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <span style="color: #6b7280; font-weight: 500;">
                                    ₱<?= number_format($unitPrice, 2) ?>
                                </span>
                            </td>
                            <td>
                                <strong style="color: #059669; font-weight: 600;">
                                    ₱<?= number_format($sellingPrice, 2) ?>
                                </strong>
                            </td>
                            <td>
                                <span style="color: <?= $markupPercent > 50 ? '#dc2626' : ($markupPercent > 40 ? '#f59e0b' : '#059669') ?>; font-weight: 600;">
                                    <?= number_format($markupPercent, 2) ?>%
                                </span>
                            </td>
                            <td>
                                <span style="font-weight: 500; color: #374151;">
                                    <?= $reorderLevel ?>
                                </span>
                            </td>
                            <td>
                                <?php if ($isExpired): ?>
                                    <span class="expired" style="padding: 4px 8px; border-radius: 4px; font-size: 11px; font-weight: 600;">
                                        <i class="fas fa-ban"></i> EXPIRED
                                    </span>
                                <?php elseif ($isExpiring): ?>
                                    <span class="expiring-soon" style="padding: 4px 8px; border-radius: 4px; font-size: 11px; font-weight: 600;">
                                        <i class="fas fa-exclamation-circle"></i> EXPIRING
                                    </span>
                                <?php elseif ($isLowStock): ?>
                                    <span class="reorder-alert" style="padding: 4px 8px; border-radius: 4px; font-size: 11px; font-weight: 600;">
                                        <i class="fas fa-exclamation-triangle"></i> LOW STOCK
                                    </span>
                                <?php else: ?>
                                    <span style="color: #10b981; font-weight: 600; padding: 4px 8px; background: #d1fae5; border-radius: 4px; font-size: 11px;">
                                        <i class="fas fa-check-circle"></i> OK
                                    </span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if (!empty($medicine['supplier_name'])): ?>
                                    <div style="font-weight: 600; color: #1f2937; margin-bottom: 4px;">
                                        <i class="fas fa-building" style="margin-right: 4px; color: #6b7280;"></i>
                                        <?= esc($medicine['supplier_name']) ?>
                                    </div>
                                    <?php if (!empty($medicine['supplier_contact'])): ?>
                                        <small style="color: #64748b; display: block; font-size: 11px;">
                                            <i class="fas fa-phone" style="margin-right: 4px;"></i>
                                            <?= esc($medicine['supplier_contact']) ?>
                                        </small>
                                    <?php endif; ?>
                                <?php else: ?>
                                    <span style="color: #9ca3af;">N/A</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
        </div>
        
        <!-- Pagination -->
        <?php if (!empty($inventoryPager) && $inventoryPager->totalPages > 1): ?>
            <div style="margin-top: 24px; display: flex; justify-content: center; align-items: center; gap: 12px; flex-wrap: wrap;">
                <?php
                $pager = $inventoryPager;
                $currentPage = $pager->currentPage;
                $totalPages = $pager->totalPages;
                $baseUrl = base_url('admin/pharmacy');
                $searchParam = !empty($inventorySearch) ? '&inventory_search=' . urlencode($inventorySearch) : '';
                ?>
                
                <?php if ($currentPage > 1): ?>
                    <a href="<?= $baseUrl ?>?inventory_page=1<?= $searchParam ?>" 
                       style="padding: 8px 16px; background: #2e7d32; color: white; border-radius: 6px; text-decoration: none; font-weight: 600; transition: background 0.3s;"
                       onmouseover="this.style.background='#4caf50'" onmouseout="this.style.background='#2e7d32'">
                        <i class="fas fa-angle-double-left"></i> First
                    </a>
                    <a href="<?= $baseUrl ?>?inventory_page=<?= $currentPage - 1 ?><?= $searchParam ?>" 
                       style="padding: 8px 16px; background: #2e7d32; color: white; border-radius: 6px; text-decoration: none; font-weight: 600; transition: background 0.3s;"
                       onmouseover="this.style.background='#4caf50'" onmouseout="this.style.background='#2e7d32'">
                        <i class="fas fa-angle-left"></i> Prev
                    </a>
                <?php endif; ?>
                
                <div style="display: flex; gap: 8px; align-items: center; flex-wrap: wrap;">
                    <?php
                    $startPage = max(1, $currentPage - 2);
                    $endPage = min($totalPages, $currentPage + 2);
                    
                    for ($i = $startPage; $i <= $endPage; $i++):
                    ?>
                        <a href="<?= $baseUrl ?>?inventory_page=<?= $i ?><?= $searchParam ?>" 
                           style="padding: 8px 16px; background: <?= $i == $currentPage ? '#4caf50' : '#2e7d32' ?>; color: white; border-radius: 6px; text-decoration: none; font-weight: 600; min-width: 40px; text-align: center; transition: background 0.3s;"
                           onmouseover="<?= $i != $currentPage ? "this.style.background='#4caf50'" : '' ?>" 
                           onmouseout="<?= $i != $currentPage ? "this.style.background='#2e7d32'" : '' ?>">
                            <?= $i ?>
                        </a>
                    <?php endfor; ?>
                </div>
                
                <?php if ($currentPage < $totalPages): ?>
                    <a href="<?= $baseUrl ?>?inventory_page=<?= $currentPage + 1 ?><?= $searchParam ?>" 
                       style="padding: 8px 16px; background: #2e7d32; color: white; border-radius: 6px; text-decoration: none; font-weight: 600; transition: background 0.3s;"
                       onmouseover="this.style.background='#4caf50'" onmouseout="this.style.background='#2e7d32'">
                        Next <i class="fas fa-angle-right"></i>
                    </a>
                    <a href="<?= $baseUrl ?>?inventory_page=<?= $totalPages ?><?= $searchParam ?>" 
                       style="padding: 8px 16px; background: #2e7d32; color: white; border-radius: 6px; text-decoration: none; font-weight: 600; transition: background 0.3s;"
                       onmouseover="this.style.background='#4caf50'" onmouseout="this.style.background='#2e7d32'">
                        Last <i class="fas fa-angle-double-right"></i>
                    </a>
                <?php endif; ?>
            </div>
            
            <div style="margin-top: 12px; text-align: center; color: #64748b; font-size: 14px;">
                Showing page <?= $currentPage ?> of <?= $totalPages ?> 
                (<?= count($inventoryMedicines) ?> items on this page)
            </div>
        <?php endif; ?>
    </div>

    <!-- Patient Medication Record -->
    <div class="medication-record-container">
        <div class="medication-record-header">
            <h3 class="medication-record-title">📋 Patient Medication Record</h3>
        </div>
        
        <?php if (empty($patientRecords ?? [])): ?>
            <div class="empty-state">
                <i class="fas fa-inbox"></i>
                <h3>No Patient Medication Records</h3>
                <p>Medication records will appear here after dispensing.</p>
            </div>
        <?php else: ?>
            <?php foreach (($patientRecords ?? []) as $patientId => $patientData): ?>
                <div class="patient-card">
                    <div class="patient-card-header">
                        <div>
                            <span class="patient-name"><?= esc($patientData['patient_name']) ?></span>
                            <?php if (!empty($patientData['allergies'])): ?>
                                <span class="allergy-badge">
                                    <i class="fas fa-exclamation-triangle"></i> Allergies: <?= esc($patientData['allergies']) ?>
                                </span>
                            <?php endif; ?>
                        </div>
                        <div style="color: #64748b; font-size: 14px;">
                            Total Transactions: <strong><?= $patientData['total_transactions'] ?></strong>
                        </div>
                    </div>
                    
                    <div style="margin-top: 16px;">
                        <h4 style="color: #2e7d32; font-size: 16px; font-weight: 600; margin-bottom: 12px;">
                            <i class="fas fa-pills"></i> Medicines Taken
                        </h4>
                        
                        <?php foreach ($patientData['medications'] as $medication): ?>
                            <div class="medication-item">
                                <div class="medication-item-header">
                                    <span class="medication-name">
                                        <?= esc($medication['medicine_name'] ?? $medication['item_name'] ?? 'N/A') ?>
                                    </span>
                                    <span class="medication-date">
                                        <?php if (!empty($medication['pharmacy_dispensed_at'])): ?>
                                            Dispensed: <?= date('M d, Y h:i A', strtotime($medication['pharmacy_dispensed_at'])) ?>
                                        <?php elseif (!empty($medication['dispensed_at'])): ?>
                                            Dispensed: <?= date('M d, Y h:i A', strtotime($medication['dispensed_at'])) ?>
                                        <?php elseif (!empty($medication['created_at'])): ?>
                                            Prescribed: <?= date('M d, Y', strtotime($medication['created_at'])) ?>
                                        <?php endif; ?>
                                    </span>
                                </div>
                                <div class="medication-details">
                                    <div>
                                        <strong>Dosage:</strong> <?= esc($medication['dosage'] ?? 'N/A') ?>
                                    </div>
                                    <div>
                                        <strong>Frequency:</strong> <?= esc($medication['frequency'] ?? 'N/A') ?>
                                    </div>
                                    <div>
                                        <strong>Duration:</strong> <?= esc($medication['duration'] ?? 'N/A') ?>
                                    </div>
                                    <div>
                                        <strong>Prescribed By:</strong> <?= esc($medication['doctor_name'] ?? 'N/A') ?>
                                    </div>
                                    <?php if (!empty($medication['administered_at'])): ?>
                                        <div>
                                            <strong>Administered:</strong> <?= date('M d, Y h:i A', strtotime($medication['administered_at'])) ?>
                                        </div>
                                    <?php endif; ?>
                                    <?php if (!empty($medication['order_status']) && $medication['order_status'] === 'completed'): ?>
                                        <div>
                                            <span style="background: #d1fae5; color: #065f46; padding: 4px 8px; border-radius: 4px; font-size: 11px; font-weight: 600;">
                                                <i class="fas fa-check-circle"></i> Administered
                                            </span>
                                        </div>
                                    <?php endif; ?>
                                </div>
                                <?php if (!empty($medication['notes'])): ?>
                                    <div style="margin-top: 8px; padding: 8px; background: #f9fafb; border-radius: 4px; font-size: 12px; color: #64748b;">
                                        <strong>Notes:</strong> <?= esc($medication['notes']) ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>

<script>
function showPrescriptionTab(tabName) {
    // Hide all prescription tabs
    document.querySelectorAll('#prescription-pending, #prescription-approved, #prescription-prepared, #prescription-dispensed').forEach(tab => {
        tab.classList.remove('active');
    });
    
    // Remove active class from all prescription tab buttons
    document.querySelectorAll('.prescription-container .tab').forEach(btn => {
        btn.classList.remove('active');
    });
    
    // Show selected tab
    document.getElementById('prescription-' + tabName).classList.add('active');
    
    // Add active class to clicked button
    event.target.classList.add('active');
}

</script>

<?= $this->endSection() ?>
