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
        
        <!-- Insurance Information -->
        <?php if (!empty($availableInsurances)): ?>
            <div style="margin-top: 20px; padding-top: 20px; border-top: 1px solid rgba(255,255,255,0.3);">
                <h3 style="margin: 0 0 12px 0; font-size: 18px;">
                    <i class="fas fa-shield-alt"></i> Available Insurance
                </h3>
                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 12px;">
                    <?php foreach ($availableInsurances as $insurance): ?>
                        <?php 
                        $usedAmount = $insurance['used_amount'] ?? 0;
                        $insuranceLimits = [
                            'PhilHealth' => 100000,
                            'Maxicare' => 500000,
                            'Medicard' => 300000,
                            'Intellicare' => 500000,
                            'Pacific Cross' => 1000000,
                            'Cocolife' => 250000,
                            'AXA' => 400000,
                            'Sun Life' => 350000,
                            'Pru Life UK' => 300000,
                            'Other' => 200000
                        ];
                        $annualLimit = $insuranceLimits[$insurance['provider']] ?? $insuranceLimits['Other'];
                        $remainingLimit = max(0, $annualLimit - $usedAmount);
                        ?>
                        <div class="insurance-card" data-provider="<?= esc($insurance['provider']) ?>" data-used="<?= $usedAmount ?>" data-limit="<?= $annualLimit ?>" style="background: rgba(255,255,255,0.2); padding: 12px; border-radius: 8px; border: 2px solid transparent; transition: all 0.3s;">
                            <div style="display: flex; justify-content: space-between; align-items: start; margin-bottom: 4px;">
                                <div style="font-weight: 600; font-size: 14px;">
                                    <?= esc($insurance['provider']) ?>
                                </div>
                                <span class="active-badge" style="display: none; background: #10b981; color: white; padding: 2px 6px; border-radius: 4px; font-size: 10px; font-weight: 600;">ACTIVE</span>
                            </div>
                            <?php if (!empty($insurance['number'])): ?>
                                <div style="font-size: 12px; opacity: 0.9; margin-bottom: 4px;">
                                    ID: <?= esc($insurance['number']) ?>
                                </div>
                            <?php endif; ?>
                            <div style="font-size: 11px; opacity: 0.8; color: #d1d5db; margin-bottom: 2px;">
                                <i class="fas fa-info-circle"></i> Annual Limit: <span class="insurance-limit-text">₱<?= number_format($annualLimit, 2) ?></span>
                            </div>
                            <div style="font-size: 11px; opacity: 0.8; color: <?= $remainingLimit > 0 ? '#10b981' : '#ef4444' ?>; font-weight: 600;">
                                <i class="fas fa-wallet"></i> Remaining: <span class="insurance-remaining-text">₱<?= number_format($remainingLimit, 2) ?></span>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php endif; ?>
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
        <?php 
        $pendingBills = array_filter($patientBills ?? [], function($bill) {
            return ($bill['status'] ?? 'pending') == 'pending';
        });
        $pendingAmount = array_sum(array_column($pendingBills, 'amount'));
        ?>
        
        <?php if (!empty($pendingBills)): ?>
            <div style="padding: 16px; background: #fef3c7; border-bottom: 2px solid #fbbf24; display: flex; justify-content: space-between; align-items: center;">
                <div>
                    <strong style="color: #92400e;">Pending Bills: <?= count($pendingBills) ?> item(s)</strong>
                    <div style="color: #92400e; font-size: 14px; margin-top: 4px;">Total Amount: <strong>₱<?= number_format($pendingAmount, 2) ?></strong></div>
                </div>
                <button onclick="openPaymentModal(); return false;" 
                        style="padding: 12px 24px; background: #8b5cf6; color: white; border: none; border-radius: 8px; cursor: pointer; font-weight: 600; font-size: 14px; z-index: 10; position: relative;"
                        id="payAllButton">
                    <i class="fas fa-money-bill-wave"></i> Pay All Pending Bills
                </button>
            </div>
        <?php endif; ?>
        
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

<!-- Payment Modal -->
<div id="paymentModal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 1000; align-items: center; justify-content: center;">
    <div style="background: white; border-radius: 12px; padding: 24px; max-width: 500px; width: 90%; max-height: 90vh; overflow-y: auto;">
        <h2 style="margin: 0 0 20px 0; color: #8b5cf6;">
            <i class="fas fa-money-bill-wave"></i> Process Payment
        </h2>
        
        <form id="paymentForm" onsubmit="processPayment(event)">
            <input type="hidden" id="payment_bill_id" name="bill_id">
            <input type="hidden" id="payment_bill_type" name="bill_type" value="billing">
            <input type="hidden" id="payment_patient_id" name="patient_id" value="<?= $patientId ?? '' ?>">
            
            <div style="margin-bottom: 16px;">
                <label style="display: block; margin-bottom: 8px; font-weight: 600;">Service/Description</label>
                <div id="payment_service" style="padding: 10px; background: #f3f4f6; border-radius: 8px;"></div>
            </div>
            
            <div style="margin-bottom: 16px;">
                <label style="display: block; margin-bottom: 8px; font-weight: 600;">Total Amount</label>
                <div id="payment_total_amount" style="padding: 10px; background: #f3f4f6; border-radius: 8px; font-size: 20px; font-weight: 700; color: #8b5cf6;"></div>
            </div>
            
            <?php if (!empty($availableInsurances)): ?>
                <div style="margin-bottom: 16px;">
                    <label style="display: block; margin-bottom: 8px; font-weight: 600;">Payment Method <span style="color: red;">*</span></label>
                    <select name="payment_method" id="payment_method" class="form-control" required onchange="updateInsuranceFields()">
                        <option value="cash">Cash</option>
                        <option value="insurance">Insurance</option>
                        <option value="credit_card">Credit Card</option>
                        <option value="debit_card">Debit Card</option>
                        <option value="bank_transfer">Bank Transfer</option>
                        <option value="check">Check</option>
                        <option value="other">Other</option>
                    </select>
                </div>
                
                <div id="insurance_selection" style="display: none; margin-bottom: 16px;">
                    <label style="display: block; margin-bottom: 8px; font-weight: 600;">Select Insurance <span style="color: red;">*</span></label>
                    <select name="insurance_provider" id="insurance_provider_select" class="form-control" onchange="calculateInsuranceCoverage()">
                        <option value="">-- Select Insurance --</option>
                        <?php foreach ($availableInsurances as $insurance): ?>
                            <option value="<?= esc($insurance['provider']) ?>" data-number="<?= esc($insurance['number']) ?>">
                                <?= esc($insurance['provider']) ?> <?= !empty($insurance['number']) ? '(' . esc($insurance['number']) . ')' : '' ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div id="insurance_coverage" style="display: none; margin-bottom: 16px; padding: 16px; background: #e0f2fe; border-radius: 8px; border-left: 4px solid #0ea5e9;">
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 12px; padding-bottom: 8px; border-bottom: 1px solid #bae6fd;">
                        <div>
                            <div style="font-weight: 700; color: #0ea5e9; font-size: 16px;" id="active_insurance_name">-</div>
                            <div style="font-size: 11px; color: #64748b; margin-top: 2px;">
                                <span style="background: #10b981; color: white; padding: 2px 6px; border-radius: 4px; font-weight: 600;">ACTIVE</span>
                            </div>
                        </div>
                        <div style="text-align: right;">
                            <div style="font-size: 11px; color: #64748b;">Annual Limit</div>
                            <div style="font-weight: 600; color: #0ea5e9;" id="insurance_limit_display">₱0.00</div>
                        </div>
                    </div>
                    <div style="display: flex; justify-content: space-between; margin-bottom: 8px;">
                        <span style="font-weight: 600;">Insurance Coverage:</span>
                        <span id="insurance_coverage_percent" style="font-weight: 700; color: #0ea5e9;">0%</span>
                    </div>
                    <div style="display: flex; justify-content: space-between; margin-bottom: 8px;">
                        <span>Insurance Amount:</span>
                        <span id="insurance_amount" style="font-weight: 600; color: #0ea5e9;">₱0.00</span>
                    </div>
                    <div style="display: flex; justify-content: space-between; padding-top: 8px; border-top: 1px solid #bae6fd; margin-bottom: 12px;">
                        <span style="font-weight: 700;">Patient Pays:</span>
                        <span id="patient_pays" style="font-weight: 700; color: #8b5cf6; font-size: 18px;">₱0.00</span>
                    </div>
                    <div id="patient_payment_method_container" style="display: none; padding-top: 12px; border-top: 1px solid #bae6fd;">
                        <label style="display: block; margin-bottom: 8px; font-weight: 600;">Patient Secondary Payment Method <span style="color: red;">*</span></label>
                        <select name="patient_payment_method" id="patient_payment_method" class="form-control" onchange="updatePatientPaymentMethod()">
                            <option value="">-- Select Payment Method --</option>
                            <option value="cash">Cash</option>
                            <option value="credit_card">Credit Card</option>
                            <option value="debit_card">Debit Card</option>
                            <option value="bank_transfer">Bank Transfer</option>
                            <option value="check">Check</option>
                            <option value="other">Other</option>
                        </select>
                        <small style="color: #64748b; font-size: 12px; margin-top: 4px; display: block;">
                            <i class="fas fa-info-circle"></i> Para sa remaining amount na dapat bayaran ng patient
                        </small>
                    </div>
                </div>
            <?php else: ?>
                <div style="margin-bottom: 16px;">
                    <label style="display: block; margin-bottom: 8px; font-weight: 600;">Payment Method <span style="color: red;">*</span></label>
                    <select name="payment_method" id="payment_method" class="form-control" required>
                        <option value="cash">Cash</option>
                        <option value="credit_card">Credit Card</option>
                        <option value="debit_card">Debit Card</option>
                        <option value="bank_transfer">Bank Transfer</option>
                        <option value="check">Check</option>
                        <option value="other">Other</option>
                    </select>
                </div>
            <?php endif; ?>
            
            <div style="display: flex; gap: 12px; margin-top: 24px;">
                <button type="button" onclick="closePaymentModal()" style="flex: 1; padding: 12px; background: #e5e7eb; color: #374151; border: none; border-radius: 8px; cursor: pointer; font-weight: 600;">
                    Cancel
                </button>
                <button type="submit" style="flex: 1; padding: 12px; background: #8b5cf6; color: white; border: none; border-radius: 8px; cursor: pointer; font-weight: 600;">
                    <i class="fas fa-check"></i> Process Payment
                </button>
            </div>
        </form>
    </div>
</div>

<script>
// Insurance coverage percentages (can be customized)
const insuranceCoverageRates = {
    'PhilHealth': 80,  // 80% coverage
    'Maxicare': 90,    // 90% coverage
    'Medicard': 85,    // 85% coverage
    'Intellicare': 90, // 90% coverage
    'Pacific Cross': 85, // 85% coverage
    'Cocolife': 85,    // 85% coverage
    'AXA': 80,         // 80% coverage
    'Sun Life': 85,    // 85% coverage
    'Pru Life UK': 85, // 85% coverage
    'Other': 70        // 70% coverage (default for other)
};

// Insurance limits (annual coverage limits in PHP)
const insuranceLimits = {
    'PhilHealth': 100000,      // ₱100,000 annual limit
    'Maxicare': 500000,       // ₱500,000 annual limit
    'Medicard': 300000,       // ₱300,000 annual limit
    'Intellicare': 500000,    // ₱500,000 annual limit
    'Pacific Cross': 1000000, // ₱1,000,000 annual limit
    'Cocolife': 250000,       // ₱250,000 annual limit
    'AXA': 400000,            // ₱400,000 annual limit
    'Sun Life': 350000,       // ₱350,000 annual limit
    'Pru Life UK': 300000,    // ₱300,000 annual limit
    'Other': 200000           // ₱200,000 annual limit (default)
};

let currentBillAmount = 0;

// Store all pending bills data
const pendingBillsData = <?= json_encode(array_values(array_filter($patientBills ?? [], function($bill) {
    return ($bill['status'] ?? 'pending') == 'pending';
}))) ?>;

function openPaymentModal() {
    // Calculate total amount from all pending bills
    const totalAmount = pendingBillsData.reduce((sum, bill) => sum + parseFloat(bill.amount || 0), 0);
    const billsCount = pendingBillsData.length;
    
    // Set bill IDs as comma-separated string
    const billIds = pendingBillsData.map(bill => bill.id).join(',');
    const billTypes = pendingBillsData.map(bill => (bill.type === 'charge' ? 'charge' : 'billing')).join(',');
    
    currentBillAmount = totalAmount;
    
    document.getElementById('payment_bill_id').value = billIds;
    document.getElementById('payment_bill_type').value = billTypes;
    document.getElementById('payment_service').textContent = billsCount + ' pending bill(s)';
    document.getElementById('payment_total_amount').textContent = '₱' + totalAmount.toLocaleString('en-PH', {minimumFractionDigits: 2, maximumFractionDigits: 2});
    document.getElementById('paymentModal').style.display = 'flex';
    document.getElementById('payment_method').value = 'cash';
    
    // Reset insurance fields
    document.getElementById('insurance_provider_select').value = '';
    document.getElementById('insurance_selection').style.display = 'none';
    document.getElementById('insurance_coverage').style.display = 'none';
    
    updateInsuranceFields();
}

function closePaymentModal() {
    document.getElementById('paymentModal').style.display = 'none';
    document.getElementById('paymentForm').reset();
    document.getElementById('insurance_selection').style.display = 'none';
    document.getElementById('insurance_coverage').style.display = 'none';
}

function updateInsuranceFields() {
    const paymentMethod = document.getElementById('payment_method').value;
    const insuranceSelection = document.getElementById('insurance_selection');
    const insuranceCoverage = document.getElementById('insurance_coverage');
    
    if (paymentMethod === 'insurance') {
        insuranceSelection.style.display = 'block';
        document.getElementById('insurance_provider_select').required = true;
    } else {
        insuranceSelection.style.display = 'none';
        insuranceCoverage.style.display = 'none';
        document.getElementById('insurance_provider_select').required = false;
        document.getElementById('insurance_provider_select').value = '';
    }
}

function calculateInsuranceCoverage() {
    const insuranceProvider = document.getElementById('insurance_provider_select').value;
    const insuranceCoverage = document.getElementById('insurance_coverage');
    const patientPaymentMethodContainer = document.getElementById('patient_payment_method_container');
    
    if (!insuranceProvider || currentBillAmount <= 0) {
        if (insuranceCoverage) insuranceCoverage.style.display = 'none';
        if (patientPaymentMethodContainer) patientPaymentMethodContainer.style.display = 'none';
        updateActiveInsurance(); // Reset active status
        return;
    }
    
    // Get coverage percentage and limit
    const coveragePercent = insuranceCoverageRates[insuranceProvider] || insuranceCoverageRates['Other'];
    const insuranceAmount = (currentBillAmount * coveragePercent / 100);
    const patientPays = currentBillAmount - insuranceAmount;
    
    // Get remaining limit from insurance card data
    const insuranceCard = document.querySelector(`.insurance-card[data-provider="${insuranceProvider}"]`);
    let remainingLimit = insuranceLimits[insuranceProvider] || insuranceLimits['Other'];
    if (insuranceCard) {
        const usedAmount = parseFloat(insuranceCard.getAttribute('data-used')) || 0;
        const annualLimit = parseFloat(insuranceCard.getAttribute('data-limit')) || insuranceLimits[insuranceProvider] || insuranceLimits['Other'];
        remainingLimit = Math.max(0, annualLimit - usedAmount);
    }
    
    // Check if insurance amount exceeds remaining limit
    if (insuranceAmount > remainingLimit) {
        alert(`Warning: Insurance coverage amount (₱${insuranceAmount.toLocaleString('en-PH', {minimumFractionDigits: 2})}) exceeds remaining limit (₱${remainingLimit.toLocaleString('en-PH', {minimumFractionDigits: 2})}).`);
    }
    
    // Update display
    document.getElementById('active_insurance_name').textContent = insuranceProvider;
    document.getElementById('insurance_limit_display').textContent = '₱' + remainingLimit.toLocaleString('en-PH', {minimumFractionDigits: 2, maximumFractionDigits: 2}) + ' (Remaining)';
    document.getElementById('insurance_coverage_percent').textContent = coveragePercent + '%';
    document.getElementById('insurance_amount').textContent = '₱' + insuranceAmount.toLocaleString('en-PH', {minimumFractionDigits: 2, maximumFractionDigits: 2});
    document.getElementById('patient_pays').textContent = '₱' + patientPays.toLocaleString('en-PH', {minimumFractionDigits: 2, maximumFractionDigits: 2});
    
    // Highlight if limit is exceeded
    const limitDisplay = document.getElementById('insurance_limit_display');
    if (insuranceAmount > remainingLimit) {
        limitDisplay.style.color = '#ef4444';
        limitDisplay.style.fontWeight = '700';
    } else {
        limitDisplay.style.color = '#0ea5e9';
        limitDisplay.style.fontWeight = '600';
    }
    
    insuranceCoverage.style.display = 'block';
    
    // Show patient payment method if patient has to pay something
    if (patientPays > 0 && patientPaymentMethodContainer) {
        patientPaymentMethodContainer.style.display = 'block';
        document.getElementById('patient_payment_method').required = true;
    } else if (patientPaymentMethodContainer) {
        patientPaymentMethodContainer.style.display = 'none';
        document.getElementById('patient_payment_method').required = false;
    }
    
    // Update active insurance visual indicator
    updateActiveInsurance();
    
    console.log('Insurance calculation:', {
        provider: insuranceProvider,
        coverage: coveragePercent + '%',
        remainingLimit: remainingLimit,
        totalAmount: currentBillAmount,
        insuranceAmount: insuranceAmount,
        patientPays: patientPays
    });
}

function updateActiveInsurance() {
    const insuranceProvider = document.getElementById('insurance_provider_select')?.value;
    
    // Reset all insurance cards
    document.querySelectorAll('.insurance-card').forEach(card => {
        card.style.border = '2px solid transparent';
        card.style.background = 'rgba(255,255,255,0.2)';
        const badge = card.querySelector('.active-badge');
        if (badge) badge.style.display = 'none';
    });
    
    // Highlight active insurance
    if (insuranceProvider) {
        const activeCard = document.querySelector(`.insurance-card[data-provider="${insuranceProvider}"]`);
        if (activeCard) {
            activeCard.style.border = '2px solid #10b981';
            activeCard.style.background = 'rgba(16, 185, 129, 0.15)';
            const badge = activeCard.querySelector('.active-badge');
            if (badge) badge.style.display = 'inline-block';
        }
    }
}

function updatePatientPaymentMethod() {
    const patientPaymentMethod = document.getElementById('patient_payment_method').value;
    console.log('Patient payment method selected:', patientPaymentMethod);
}

async function processPayment(event) {
    event.preventDefault();
    
    const formData = new FormData(event.target);
    const billIds = formData.get('bill_id'); // Comma-separated bill IDs
    const billTypes = formData.get('bill_type'); // Comma-separated bill types
    const paymentMethod = formData.get('payment_method');
    const insuranceProvider = formData.get('insurance_provider');
    const patientPaymentMethod = formData.get('patient_payment_method');
    
    // Validate patient payment method if insurance is selected
    if (paymentMethod === 'insurance' && insuranceProvider) {
        const coveragePercent = insuranceCoverageRates[insuranceProvider] || insuranceCoverageRates['Other'];
        const insuranceAmount = currentBillAmount * coveragePercent / 100;
        const patientPays = currentBillAmount - insuranceAmount;
        
        if (patientPays > 0 && !patientPaymentMethod) {
            alert('Please select Patient Secondary Payment Method for the remaining amount.');
            document.getElementById('patient_payment_method').focus();
            return;
        }
    }
    
    // Calculate final amount based on insurance
    let finalAmount = currentBillAmount;
    if (paymentMethod === 'insurance' && insuranceProvider) {
        const coveragePercent = insuranceCoverageRates[insuranceProvider] || insuranceCoverageRates['Other'];
        finalAmount = currentBillAmount - (currentBillAmount * coveragePercent / 100);
    }
    
    if (!confirm(`Process payment for ${pendingBillsData.length} bill(s) totaling ₱${finalAmount.toLocaleString('en-PH', {minimumFractionDigits: 2})}?`)) {
        return;
    }
    
    try {
        const response = await fetch('<?= site_url('accounting/process-bill-payment') ?>', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                bill_ids: billIds.split(','), // Array of bill IDs
                bill_types: billTypes.split(','), // Array of bill types
                payment_method: paymentMethod,
                insurance_provider: insuranceProvider || null,
                patient_payment_method: patientPaymentMethod || null,
                amount: finalAmount,
                total_amount: currentBillAmount,
            })
        });
        
        const result = await response.json();
        
        if (result.success) {
            alert('Payment processed successfully!');
            location.reload();
        } else {
            alert('Error: ' + (result.message || 'Failed to process payment'));
        }
    } catch (error) {
        alert('Error: ' + error.message);
    }
}

// Ensure Pay All button is clickable
document.addEventListener('DOMContentLoaded', function() {
    const payAllButton = document.getElementById('payAllButton');
    if (payAllButton) {
        payAllButton.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            console.log('Pay All button clicked');
            openPaymentModal();
        });
        
        // Force enable button
        payAllButton.style.pointerEvents = 'auto';
        payAllButton.style.cursor = 'pointer';
        payAllButton.disabled = false;
    }
});

// Close modal when clicking outside
document.getElementById('paymentModal')?.addEventListener('click', function(e) {
    if (e.target === this) {
        closePaymentModal();
    }
});
</script>

<?= $this->endSection() ?>

