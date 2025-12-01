<?= $this->extend('template/header') ?>

<?= $this->section('title') ?>
    Accountant Dashboard
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<style>
    .dashboard-header { 
        background: #ffffff; border: 1px solid #e5e7eb; border-radius: 12px; padding: 20px; 
        box-shadow: 0 2px 6px rgba(15, 23, 42, 0.08);
        background-image: linear-gradient(135deg, rgba(76,175,80,0.06), rgba(46,125,50,0.06));
        margin-bottom: 16px;
    }
    .dashboard-header h1 { 
        margin: 0 0 6px; color: #2e7d32; font-family: 'Playfair Display', serif; letter-spacing: -0.01em; 
        font-size: 26px;
    }
    .dashboard-subtitle { margin: 0; color: #64748b; }
    .overview-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 16px; }
    .overview-card { 
        background: #ffffff; border: 1px solid #e5e7eb; border-radius: 12px; padding: 18px; 
        box-shadow: 0 2px 6px rgba(15, 23, 42, 0.08); position: relative; overflow: hidden; 
        transition: all 0.25s ease;
    }
    .overview-card::before { content: ''; position: absolute; top: 0; left: 0; right: 0; height: 4px; background: linear-gradient(90deg, #2e7d32, #43a047); }
    .overview-card:hover { transform: translateY(-2px); box-shadow: 0 8px 24px rgba(16,24,40,0.12); }
    .card-content h3 { margin: 0; font-size: 14px; font-weight: 700; color: #2e7d32; }
    .card-value { margin-top: 10px; font-size: 28px; font-weight: 800; color: #1f2937; }
    @media (max-width: 600px) { .card-value { font-size: 24px; } }
    /* Print friendly */
    @media print { .overview-card { box-shadow: none; } }
</style>
    <div class="dashboard-header">
        <h1>Accountant Dashboard</h1>
        <p class="dashboard-subtitle">Financial Management & Billing</p>
    </div>

    <!-- Quick Actions -->
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 16px; margin-bottom: 24px;">
        <a href="<?= site_url('accounting/finance') ?>" style="background: #2e7d32; color: white; padding: 16px; border-radius: 12px; text-decoration: none; text-align: center; font-weight: 600; box-shadow: 0 2px 6px rgba(0,0,0,0.08);">
            <i class="fas fa-chart-line" style="font-size: 24px; margin-bottom: 8px; display: block;"></i>
            Finance Overview
        </a>
        <a href="<?= site_url('accounting/payments') ?>" style="background: #0288d1; color: white; padding: 16px; border-radius: 12px; text-decoration: none; text-align: center; font-weight: 600; box-shadow: 0 2px 6px rgba(0,0,0,0.08);">
            <i class="fas fa-money-bill-wave" style="font-size: 24px; margin-bottom: 8px; display: block;"></i>
            Payment Reports
        </a>
        <a href="<?= site_url('accounting/medication-billing') ?>" style="background: #10b981; color: white; padding: 16px; border-radius: 12px; text-decoration: none; text-align: center; font-weight: 600; box-shadow: 0 2px 6px rgba(0,0,0,0.08);">
            <i class="fas fa-pills" style="font-size: 24px; margin-bottom: 8px; display: block;"></i>
            Medication Billing
        </a>
        <a href="<?= site_url('accounting/expenses') ?>" style="background: #f59e0b; color: white; padding: 16px; border-radius: 12px; text-decoration: none; text-align: center; font-weight: 600; box-shadow: 0 2px 6px rgba(0,0,0,0.08);">
            <i class="fas fa-receipt" style="font-size: 24px; margin-bottom: 8px; display: block;"></i>
            Expense Tracking
        </a>
    </div>

    <!-- Overview Cards -->
    <div class="overview-grid">
        <div class="overview-card">
            <div class="card-content">
                <h3>Today's Revenue</h3>
                <div class="card-value" id="todayRevenue">₱<?= number_format($todayRevenue ?? 0, 2) ?></div>
            </div>
        </div>
        <div class="overview-card">
            <div class="card-content">
                <h3>Total Revenue (All Sources)</h3>
                <div class="card-value" id="totalRevenue">₱<?= number_format($todayRevenue ?? 0, 2) ?></div>
            </div>
        </div>
        <div class="overview-card">
            <div class="card-content">
                <h3>Pending Bills</h3>
                <div class="card-value" id="pendingBills"><?= is_array($pendingBills) ? count($pendingBills) : (int)($pendingBills ?? 0) ?></div>
            </div>
        </div>
        <div class="overview-card" style="border-left: 4px solid #10b981;">
            <div class="card-content">
                <h3 style="color: #10b981;">Medication Bills (Pending)</h3>
                <div class="card-value" id="medicationBillsPending" style="color: #10b981;">0</div>
                <div style="font-size: 12px; color: #64748b; margin-top: 4px;">Amount: <span id="medicationBillsAmount">₱0.00</span></div>
            </div>
        </div>
        <div class="overview-card">
            <div class="card-content">
                <h3>Outstanding Balance</h3>
                <div class="card-value" id="outstandingBalance">₱<?= number_format($outstandingBalance ?? 0, 2) ?></div>
            </div>
        </div>
    </div>

    <!-- Cross-Role Statistics -->
    <div style="margin-top: 24px;">
        <h2 style="color: #2e7d32; margin-bottom: 16px; font-size: 20px;">Cross-Role Financial Overview</h2>
        <div class="overview-grid">
            <!-- Receptionist → Patient Payments -->
            <div class="overview-card" style="border-left: 4px solid #10b981;">
                <div class="card-content">
                    <h3 style="color: #10b981;">Patient Payments (Receptionist)</h3>
                    <div class="card-value" id="patientPaymentsToday" style="color: #10b981;">₱0.00</div>
                    <div style="font-size: 12px; color: #64748b; margin-top: 4px;">Total: <span id="totalPatientPayments">0</span> payments</div>
                </div>
            </div>

            <!-- Doctor/Nurse → Treatment & Lab Charges -->
            <div class="overview-card" style="border-left: 4px solid #0288d1;">
                <div class="card-content">
                    <h3 style="color: #0288d1;">Treatment Charges (Doctor/Nurse)</h3>
                    <div class="card-value" id="treatmentCharges" style="color: #0288d1;">₱0.00</div>
                    <div style="font-size: 12px; color: #64748b; margin-top: 4px;">Consultations: <span id="consultationCharges">₱0.00</span></div>
                </div>
            </div>

            <!-- Lab Staff → Lab Test Charges -->
            <div class="overview-card" style="border-left: 4px solid #ec4899;">
                <div class="card-content">
                    <h3 style="color: #ec4899;">Lab Test Charges</h3>
                    <div class="card-value" id="labTestRevenue" style="color: #ec4899;">₱0.00</div>
                    <div style="font-size: 12px; color: #64748b; margin-top: 4px;">Total Tests: <span id="totalLabTests">0</span></div>
                </div>
            </div>

            <!-- Pharmacy → Medication Expenses -->
            <div class="overview-card" style="border-left: 4px solid #ef4444;">
                <div class="card-content">
                    <h3 style="color: #ef4444;">Pharmacy Inventory</h3>
                    <div class="card-value" id="pharmacyExpenses" style="color: #ef4444;">₱0.00</div>
                    <div style="font-size: 12px; color: #64748b; margin-top: 4px;">Revenue: <span id="pharmacyRevenue">₱0.00</span></div>
                </div>
            </div>

            <!-- Finance Admin → Budgets -->
            <div class="overview-card" style="border-left: 4px solid #8b5cf6;">
                <div class="card-content">
                    <h3 style="color: #8b5cf6;">Finance Reports</h3>
                    <div class="card-value" id="activeReports" style="color: #8b5cf6;">0</div>
                    <div style="font-size: 12px; color: #64748b; margin-top: 4px;">Total Budgets: <span id="totalBudgets">0</span></div>
                </div>
            </div>

            <!-- IT Staff → System Management -->
            <div class="overview-card" style="border-left: 4px solid #64748b;">
                <div class="card-content">
                    <h3 style="color: #64748b;">System Users (IT Staff)</h3>
                    <div class="card-value" id="activeUsers" style="color: #64748b;">0</div>
                    <div style="font-size: 12px; color: #64748b; margin-top: 4px;">Total: <span id="totalUsers">0</span> users | Logs: <span id="systemLogs">0</span></div>
                </div>
            </div>
        </div>
    </div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const accountantStatsEndpoint = '<?= site_url('accountant/dashboard/stats') ?>';
    
    async function refreshAccountantDashboard() {
        try {
            const response = await fetch(accountantStatsEndpoint, {
                headers: { 'Accept': 'application/json' }
            });
            
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            
            const data = await response.json();
            
            // Update stat cards
            const setText = (id, value, isCurrency = false) => {
                const element = document.getElementById(id);
                if (element) {
                    if (isCurrency) {
                        element.textContent = '₱' + parseFloat(value || 0).toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
                    } else {
                        element.textContent = value ?? '0';
                    }
                }
            };
            
            setText('todayRevenue', data.today_revenue ?? '0', true);
            setText('totalRevenue', data.total_revenue ?? '0', true);
            setText('pendingBills', data.pending_bills ?? '0');
            setText('outstandingBalance', data.outstanding_balance ?? '0', true);
            
            // Cross-role data
            setText('patientPaymentsToday', data.patient_payments_today ?? '0', true);
            setText('totalPatientPayments', data.total_patient_payments ?? '0');
            setText('treatmentCharges', data.treatment_charges ?? '0', true);
            setText('consultationCharges', data.consultation_charges ?? '0', true);
            setText('labTestRevenue', data.lab_test_revenue ?? '0', true);
            setText('totalLabTests', data.total_lab_tests ?? '0');
            setText('pharmacyExpenses', data.pharmacy_expenses ?? '0', true);
            setText('pharmacyRevenue', data.pharmacy_revenue ?? '0', true);
            setText('activeReports', data.active_reports ?? '0');
            setText('totalBudgets', data.total_budgets ?? '0');
            setText('activeUsers', data.active_users ?? '0');
            setText('totalUsers', data.total_users ?? '0');
            setText('systemLogs', data.system_logs ?? '0');
            setText('medicationBillsPending', data.medication_bills_pending ?? '0');
            setText('medicationBillsAmount', data.medication_bills_amount ?? '0', true);
        } catch (error) {
            console.error('Error fetching Accountant Dashboard stats:', error);
        }
    }
    
    // Initial fetch
    refreshAccountantDashboard();
    
    // Refresh every 10 seconds
    setInterval(refreshAccountantDashboard, 10000);
    
    // Refresh when page becomes visible again
    document.addEventListener('visibilitychange', () => {
        if (document.visibilityState === 'visible') {
            refreshAccountantDashboard();
        }
    });
});
</script>
<?= $this->endSection() ?>
