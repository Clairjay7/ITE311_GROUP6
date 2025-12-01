<?= $this->extend('template/header') ?>

<?= $this->section('title') ?>Medication Order Details<?= $this->endSection() ?>

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
    .info-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 16px;
        margin-bottom: 24px;
    }
    .info-card {
        background: #f8fafc;
        padding: 16px;
        border-radius: 8px;
        border-left: 4px solid #0288d1;
    }
    .info-label {
        font-size: 12px;
        color: #64748b;
        font-weight: 600;
        text-transform: uppercase;
        margin-bottom: 4px;
    }
    .info-value {
        font-size: 16px;
        font-weight: 700;
        color: #1e293b;
    }
    .status-badge {
        padding: 6px 12px;
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
        padding: 12px 24px;
        border-radius: 8px;
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
    .section-title {
        font-size: 18px;
        font-weight: 700;
        color: #0288d1;
        margin: 24px 0 12px;
        padding-bottom: 8px;
        border-bottom: 2px solid #e0e0e0;
    }
    .audit-item {
        background: #f8fafc;
        padding: 12px;
        border-radius: 8px;
        margin-bottom: 8px;
        border-left: 4px solid #0288d1;
    }
</style>

<div class="medication-container">
    <div class="medication-header">
        <h1 class="medication-title">💊 Medication Order #<?= esc($order['id']) ?></h1>
        <a href="<?= site_url('nurse/medications') ?>" style="color: #0288d1; text-decoration: none;">
            <i class="fas fa-arrow-left"></i> Back to Medications
        </a>
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

    <div class="info-grid">
        <div class="info-card">
            <div class="info-label">Patient</div>
            <div class="info-value"><?= esc($order['firstname'] . ' ' . $order['lastname']) ?></div>
        </div>
        <div class="info-card">
            <div class="info-label">Doctor</div>
            <div class="info-value"><?= esc($order['doctor_name']) ?></div>
        </div>
        <div class="info-card">
            <div class="info-label">Order Date</div>
            <div class="info-value"><?= date('M d, Y h:i A', strtotime($order['order_date'])) ?></div>
        </div>
        <div class="info-card">
            <div class="info-label">Pharmacy Status</div>
            <div class="info-value">
                <span class="status-badge status-<?= 
                    ($order['pharmacy_status'] ?? 'pending') === 'dispensed' ? 'ready' : 
                    (($order['pharmacy_status'] ?? 'pending') === 'pending' ? 'waiting' : 'waiting')
                ?>">
                    <?= ucfirst($order['pharmacy_status'] ?? 'Pending') ?>
                </span>
            </div>
        </div>
        <div class="info-card">
            <div class="info-label">Order Status</div>
            <div class="info-value">
                <span class="status-badge status-<?= 
                    ($order['status'] ?? 'pending') === 'completed' ? 'administered' : 'waiting'
                ?>">
                    <?= ucfirst(str_replace('_', ' ', $order['status'] ?? 'Pending')) ?>
                </span>
            </div>
        </div>
    </div>

    <div class="section-title">
        <i class="fas fa-pills"></i> Medication Details
    </div>
    <div class="info-grid">
        <div class="info-card">
            <div class="info-label">Medicine Name</div>
            <div class="info-value"><?= esc($order['medicine_name'] ?? $order['order_description']) ?></div>
        </div>
        <div class="info-card">
            <div class="info-label">Dosage</div>
            <div class="info-value"><?= esc($order['dosage'] ?? 'N/A') ?></div>
        </div>
        <div class="info-card">
            <div class="info-label">Frequency</div>
            <div class="info-value"><?= esc($order['frequency'] ?? 'N/A') ?></div>
        </div>
        <div class="info-card">
            <div class="info-label">Duration</div>
            <div class="info-value"><?= esc($order['duration'] ?? 'N/A') ?></div>
        </div>
    </div>

    <?php if (!empty($order['instructions'])): ?>
    <div class="section-title">
        <i class="fas fa-clipboard-list"></i> Instructions
    </div>
    <div style="background: #f8fafc; padding: 16px; border-radius: 8px; color: #475569;">
        <?= nl2br(esc($order['instructions'])) ?>
    </div>
    <?php endif; ?>

    <?php if (!empty($order['remarks'])): ?>
    <div class="section-title">
        <i class="fas fa-sticky-note"></i> Remarks
    </div>
    <div style="background: #f8fafc; padding: 16px; border-radius: 8px; color: #475569;">
        <?= nl2br(esc($order['remarks'])) ?>
    </div>
    <?php endif; ?>

    <?php if (!empty($order['order_description'])): ?>
    <div class="section-title">
        <i class="fas fa-file-medical"></i> Order Description
    </div>
    <div style="background: #f8fafc; padding: 16px; border-radius: 8px; color: #475569;">
        <?= nl2br(esc($order['order_description'])) ?>
    </div>
    <?php endif; ?>

    <!-- Pharmacy Timeline -->
    <?php if ($order['pharmacy_status']): ?>
    <div class="section-title">
        <i class="fas fa-clock"></i> Pharmacy Timeline
    </div>
    <div style="background: #f8fafc; padding: 16px; border-radius: 8px;">
        <?php if ($order['pharmacy_approved_at']): ?>
            <div style="margin-bottom: 8px;">
                <strong>Approved:</strong> <?= date('M d, Y h:i A', strtotime($order['pharmacy_approved_at'])) ?>
            </div>
        <?php endif; ?>
        <?php if ($order['pharmacy_prepared_at']): ?>
            <div style="margin-bottom: 8px;">
                <strong>Prepared:</strong> <?= date('M d, Y h:i A', strtotime($order['pharmacy_prepared_at'])) ?>
            </div>
        <?php endif; ?>
        <?php if ($order['pharmacy_dispensed_at']): ?>
            <div style="margin-bottom: 8px; color: #065f46; font-weight: 600;">
                <strong>Dispensed:</strong> <?= date('M d, Y h:i A', strtotime($order['pharmacy_dispensed_at'])) ?>
            </div>
        <?php endif; ?>
    </div>
    <?php endif; ?>

    <!-- Administer Button -->
    <?php if (($order['pharmacy_status'] ?? 'pending') === 'dispensed' && ($order['status'] ?? 'pending') !== 'completed'): ?>
    <div style="margin-top: 24px; text-align: center;">
        <button class="btn-administer" onclick="administerMedication(<?= $order['id'] ?>)">
            <i class="fas fa-syringe"></i> Administer Medication to Patient
        </button>
    </div>
    <?php elseif (($order['pharmacy_status'] ?? 'pending') !== 'dispensed'): ?>
    <div style="margin-top: 24px; padding: 16px; background: #fff3cd; border-radius: 8px; text-align: center; color: #856404;">
        <i class="fas fa-lock"></i> <strong>Waiting for Pharmacy to dispense medication</strong>
        <p style="margin: 8px 0 0; font-size: 14px;">Current status: <?= ucfirst($order['pharmacy_status'] ?? 'Pending') ?></p>
    </div>
    <?php elseif (($order['status'] ?? 'pending') === 'completed'): ?>
    <div style="margin-top: 24px; padding: 16px; background: #d1fae5; border-radius: 8px; text-align: center; color: #065f46;">
        <i class="fas fa-check-circle"></i> <strong>Medication Administered</strong>
        <p style="margin: 8px 0 0; font-size: 14px;">
            Administered at: <?= $order['completed_at'] ? date('M d, Y h:i A', strtotime($order['completed_at'])) : 'N/A' ?>
        </p>
    </div>
    <?php endif; ?>

    <!-- Audit Trail -->
    <?php if (!empty($auditTrail)): ?>
    <div class="section-title">
        <i class="fas fa-history"></i> Audit Trail
    </div>
    <?php foreach ($auditTrail as $log): ?>
        <div class="audit-item">
            <div style="display: flex; justify-content: space-between; margin-bottom: 4px;">
                <strong><?= esc($log['changed_by_name'] ?? 'System') ?></strong>
                <small style="color: #64748b;"><?= date('M d, Y h:i A', strtotime($log['created_at'])) ?></small>
            </div>
            <div style="color: #475569;"><?= esc($log['notes'] ?? '') ?></div>
        </div>
    <?php endforeach; ?>
    <?php endif; ?>
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

