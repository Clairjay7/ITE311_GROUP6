<?= $this->extend('template/header') ?>

<?= $this->section('title') ?>Medication Administration<?= $this->endSection() ?>

<?= $this->section('content') ?>
<style>
    .medication-container {
        background: #fff;
        border-radius: 12px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        padding: 24px;
        margin-bottom: 24px;
    }
    .medication-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 20px;
        padding-bottom: 16px;
        border-bottom: 2px solid #0288d1;
    }
    .medication-title {
        font-size: 24px;
        font-weight: 700;
        color: #0288d1;
    }
    .tabs-container {
        display: flex;
        gap: 12px;
        margin-bottom: 20px;
        border-bottom: 2px solid #e0e0e0;
    }
    .tab-btn {
        padding: 12px 24px;
        background: transparent;
        border: none;
        border-bottom: 3px solid transparent;
        cursor: pointer;
        font-size: 14px;
        font-weight: 600;
        color: #666;
        transition: all 0.3s;
    }
    .tab-btn.active {
        color: #0288d1;
        border-bottom-color: #0288d1;
    }
    .tab-btn:hover {
        color: #0288d1;
    }
    .tab-content {
        display: none;
    }
    .tab-content.active {
        display: block;
    }
    .medication-table {
        width: 100%;
        border-collapse: collapse;
    }
    .medication-table th {
        background: #e3f2fd;
        padding: 12px;
        text-align: left;
        font-weight: 600;
        color: #0288d1;
        border-bottom: 2px solid #0288d1;
    }
    .medication-table td {
        padding: 12px;
        border-bottom: 1px solid #e0e0e0;
    }
    .medication-table tr:hover {
        background: #f5f5f5;
    }
    .status-badge {
        padding: 4px 12px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: 600;
        text-transform: uppercase;
    }
    .status-waiting {
        background: #fff3cd;
        color: #856404;
    }
    .status-ready {
        background: #d1fae5;
        color: #065f46;
    }
    .status-administered {
        background: #dbeafe;
        color: #1e40af;
    }
    .btn-administer {
        background: linear-gradient(135deg, #0288d1, #03a9f4);
        color: white;
        border: none;
        padding: 8px 16px;
        border-radius: 6px;
        cursor: pointer;
        font-size: 14px;
        font-weight: 600;
        transition: all 0.3s;
    }
    .btn-administer:hover {
        background: linear-gradient(135deg, #0277bd, #0288d1);
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(2, 136, 209, 0.3);
    }
    .btn-administer:disabled {
        background: #ccc;
        cursor: not-allowed;
        transform: none;
    }
    .btn-view {
        background: #2196f3;
        color: white;
        border: none;
        padding: 6px 12px;
        border-radius: 6px;
        cursor: pointer;
        font-size: 12px;
        margin-right: 8px;
    }
    .empty-state {
        text-align: center;
        padding: 40px;
        color: #666;
    }
    .patients-list {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
        gap: 16px;
        margin-bottom: 24px;
    }
    .patient-card {
        background: #f5f5f5;
        padding: 16px;
        border-radius: 8px;
        border-left: 4px solid #0288d1;
    }
</style>

<div class="medication-container">
    <div class="medication-header">
        <h1 class="medication-title">💊 Medication Administration</h1>
        <div>
            <span style="color: #666;">Waiting: <strong><?= count($waitingForPharmacy) ?></strong></span> |
            <span style="color: #666;">Ready: <strong><?= count($readyToAdminister) ?></strong></span> |
            <span style="color: #666;">Administered: <strong><?= count($administered) ?></strong></span>
        </div>
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

    <!-- Assigned Patients -->
    <?php if (!empty($assignedPatients)): ?>
        <div style="margin-bottom: 24px;">
            <h3 style="color: #0288d1; margin-bottom: 12px;">Assigned Patients</h3>
            <div class="patients-list">
                <?php foreach ($assignedPatients as $patient): ?>
                    <div class="patient-card">
                        <strong><?= esc($patient['firstname'] . ' ' . $patient['lastname']) ?></strong>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    <?php endif; ?>

    <div class="tabs-container">
        <button class="tab-btn active" onclick="showTab('waiting')">
            Waiting for Pharmacy (<?= count($waitingForPharmacy) ?>)
        </button>
        <button class="tab-btn" onclick="showTab('ready')">
            Ready to Administer (<?= count($readyToAdminister) ?>)
        </button>
        <button class="tab-btn" onclick="showTab('administered')">
            Administered (<?= count($administered) ?>)
        </button>
    </div>

    <!-- Waiting for Pharmacy Tab -->
    <div id="tab-waiting" class="tab-content active">
        <?php if (empty($waitingForPharmacy)): ?>
            <div class="empty-state">
                <i class="fas fa-check-circle" style="font-size: 48px; color: #ccc; margin-bottom: 16px;"></i>
                <h3>No Orders Waiting</h3>
                <p>All medication orders have been processed by Pharmacy.</p>
            </div>
        <?php else: ?>
            <table class="medication-table">
                <thead>
                    <tr>
                        <th>Order ID</th>
                        <th>Patient</th>
                        <th>Doctor</th>
                        <th>Medicine</th>
                        <th>Dosage</th>
                        <th>Frequency</th>
                        <th>Pharmacy Status</th>
                        <th>Order Date</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($waitingForPharmacy as $order): ?>
                        <tr>
                            <td>#<?= $order['id'] ?></td>
                            <td><strong><?= esc($order['firstname'] . ' ' . $order['lastname']) ?></strong></td>
                            <td><?= esc($order['doctor_name']) ?></td>
                            <td><strong><?= esc($order['medicine_name'] ?? $order['order_description']) ?></strong></td>
                            <td><?= esc($order['dosage'] ?? 'N/A') ?></td>
                            <td><?= esc($order['frequency'] ?? 'N/A') ?></td>
                            <td>
                                <span class="status-badge status-waiting">
                                    <?php 
                                        $pharmacyStatus = $order['pharmacy_status'] ?? 'pending';
                                        $statusText = [
                                            'pending' => 'Pending (Waiting for Pharmacy)',
                                            'approved' => 'Approved (Waiting for Pharmacy)',
                                            'prepared' => 'Prepared (Waiting for Pharmacy)'
                                        ];
                                        echo $statusText[$pharmacyStatus] ?? ucfirst($pharmacyStatus);
                                    ?>
                                </span>
                            </td>
                            <td><?= date('M d, Y', strtotime($order['order_date'])) ?></td>
                            <td>
                                <button class="btn-view" onclick="viewOrder(<?= $order['id'] ?>)">
                                    <i class="fas fa-eye"></i> View
                                </button>
                                <button class="btn-administer" disabled title="Waiting for Pharmacy to dispense. Cannot administer until Pharmacy marks as 'Dispensed'.">
                                    <i class="fas fa-lock"></i> Administer
                                </button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>

    <!-- Ready to Administer Tab -->
    <div id="tab-ready" class="tab-content">
        <?php if (empty($readyToAdminister)): ?>
            <div class="empty-state">
                <i class="fas fa-check-circle" style="font-size: 48px; color: #ccc; margin-bottom: 16px;"></i>
                <h3>No Medications Ready</h3>
                <p>No medications are ready for administration yet.</p>
            </div>
        <?php else: ?>
            <table class="medication-table">
                <thead>
                    <tr>
                        <th>Order ID</th>
                        <th>Patient</th>
                        <th>Doctor</th>
                        <th>Medicine</th>
                        <th>Dosage</th>
                        <th>Frequency</th>
                        <th>Duration</th>
                        <th>Dispensed At</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($readyToAdminister as $order): ?>
                        <tr>
                            <td>#<?= $order['id'] ?></td>
                            <td><strong><?= esc($order['firstname'] . ' ' . $order['lastname']) ?></strong></td>
                            <td><?= esc($order['doctor_name']) ?></td>
                            <td><strong><?= esc($order['medicine_name'] ?? $order['order_description']) ?></strong></td>
                            <td><?= esc($order['dosage'] ?? 'N/A') ?></td>
                            <td><?= esc($order['frequency'] ?? 'N/A') ?></td>
                            <td><?= esc($order['duration'] ?? 'N/A') ?></td>
                            <td>
                                <?= $order['pharmacy_dispensed_at'] ? date('M d, Y h:i A', strtotime($order['pharmacy_dispensed_at'])) : 'N/A' ?>
                                <br><small style="color: #10b981; font-weight: 600;">
                                    <i class="fas fa-check-circle"></i> Ready for Administration
                                </small>
                            </td>
                            <td>
                                <button class="btn-view" onclick="viewOrder(<?= $order['id'] ?>)">
                                    <i class="fas fa-eye"></i> View
                                </button>
                                <button class="btn-administer" onclick="administerMedication(<?= $order['id'] ?>)">
                                    <i class="fas fa-syringe"></i> Administer
                                </button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>

    <!-- Administered Tab -->
    <div id="tab-administered" class="tab-content">
        <?php if (empty($administered)): ?>
            <div class="empty-state">
                <i class="fas fa-check-circle" style="font-size: 48px; color: #ccc; margin-bottom: 16px;"></i>
                <h3>No Administered Medications</h3>
                <p>No medications have been administered yet.</p>
            </div>
        <?php else: ?>
            <table class="medication-table">
                <thead>
                    <tr>
                        <th>Order ID</th>
                        <th>Patient</th>
                        <th>Doctor</th>
                        <th>Medicine</th>
                        <th>Dosage</th>
                        <th>Frequency</th>
                        <th>Administered At</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($administered as $order): ?>
                        <tr>
                            <td>#<?= $order['id'] ?></td>
                            <td><strong><?= esc($order['firstname'] . ' ' . $order['lastname']) ?></strong></td>
                            <td><?= esc($order['doctor_name']) ?></td>
                            <td><strong><?= esc($order['medicine_name'] ?? $order['order_description']) ?></strong></td>
                            <td><?= esc($order['dosage'] ?? 'N/A') ?></td>
                            <td><?= esc($order['frequency'] ?? 'N/A') ?></td>
                            <td>
                                <?= $order['completed_at'] ? date('M d, Y h:i A', strtotime($order['completed_at'])) : 'N/A' ?>
                                <br><small style="color: #1e40af;">
                                    <i class="fas fa-check-circle"></i> Completed
                                </small>
                            </td>
                            <td>
                                <button class="btn-view" onclick="viewOrder(<?= $order['id'] ?>)">
                                    <i class="fas fa-eye"></i> View Details
                                </button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>
</div>

<!-- Administer Medication Modal -->
<div id="administerModal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 1000; align-items: center; justify-content: center;">
    <div style="background: white; padding: 24px; border-radius: 12px; max-width: 500px; width: 90%;">
        <h3 style="margin-top: 0; color: #0288d1;">Administer Medication</h3>
        <form id="administerForm">
            <input type="hidden" id="orderId" name="order_id">
            <div style="margin-bottom: 16px;">
                <label style="display: block; margin-bottom: 8px; font-weight: 600;">Administered Time *</label>
                <input type="datetime-local" id="administeredTime" name="administered_time" class="form-control" required style="width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 6px;">
            </div>
            <div style="margin-bottom: 16px;">
                <label style="display: block; margin-bottom: 8px; font-weight: 600;">Dosage Confirmation</label>
                <input type="text" id="dosageConfirmation" name="dosage_confirmation" class="form-control" placeholder="Confirm the dosage given" style="width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 6px;">
            </div>
            <div style="margin-bottom: 16px;">
                <label style="display: block; margin-bottom: 8px; font-weight: 600;">Remarks</label>
                <textarea id="remarks" name="remarks" class="form-control" rows="3" placeholder="Any additional notes..." style="width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 6px;"></textarea>
            </div>
            <div style="display: flex; gap: 12px; justify-content: flex-end;">
                <button type="button" onclick="closeAdministerModal()" style="padding: 8px 16px; background: #f5f5f5; border: none; border-radius: 6px; cursor: pointer;">Cancel</button>
                <button type="submit" style="padding: 8px 16px; background: #0288d1; color: white; border: none; border-radius: 6px; cursor: pointer; font-weight: 600;">Confirm Administration</button>
            </div>
        </form>
    </div>
</div>

<script>
function showTab(tabName) {
    document.querySelectorAll('.tab-content').forEach(tab => tab.classList.remove('active'));
    document.querySelectorAll('.tab-btn').forEach(btn => btn.classList.remove('active'));
    
    document.getElementById('tab-' + tabName).classList.add('active');
    event.target.classList.add('active');
}

function viewOrder(orderId) {
    window.location.href = `<?= site_url('nurse/medications/view/') ?>${orderId}`;
}

function administerMedication(orderId) {
    document.getElementById('orderId').value = orderId;
    document.getElementById('administeredTime').value = new Date().toISOString().slice(0, 16);
    document.getElementById('administerModal').style.display = 'flex';
}

function closeAdministerModal() {
    document.getElementById('administerModal').style.display = 'none';
}

document.getElementById('administerForm').addEventListener('submit', async function(e) {
    e.preventDefault();
    
    const orderId = document.getElementById('orderId').value;
    const formData = new FormData(this);
    
    try {
        const response = await fetch(`<?= site_url('nurse/medications/administer/') ?>${orderId}`, {
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
        alert('Failed to administer medication');
    }
});

// Close modal when clicking outside
document.getElementById('administerModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeAdministerModal();
    }
});
</script>

<?= $this->endSection() ?>

