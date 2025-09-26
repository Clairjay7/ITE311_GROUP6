<?= $this->extend('templates/template') ?>

<?= $this->section('content') ?>
<div class="container">
    <div class="header-section">
        <h2>📝 Pending Prescriptions</h2>
        <div class="actions">
            <a href="<?= base_url('pharmacist/dashboard') ?>" class="btn btn-secondary">Back to Dashboard</a>
        </div>
    </div>

    <div class="pending-prescriptions">
        <h4>Prescriptions Waiting to be Dispensed</h4>
        <div class="prescription-queue">
            <div class="prescription-card urgent">
                <div class="prescription-header">
                    <span class="prescription-id">RX-001</span>
                    <span class="priority-badge urgent">URGENT</span>
                </div>
                <div class="prescription-details">
                    <h5>Amoxicillin 500mg</h5>
                    <p><strong>Patient:</strong> Jane Smith</p>
                    <p><strong>Doctor:</strong> Dr. Johnson</p>
                    <p><strong>Dosage:</strong> 3 times daily for 7 days</p>
                    <p><strong>Prescribed:</strong> 30 minutes ago</p>
                </div>
                <div class="prescription-actions">
                    <button class="btn btn-success">Dispense</button>
                    <button class="btn btn-info">View Details</button>
                </div>
            </div>

            <div class="prescription-card">
                <div class="prescription-header">
                    <span class="prescription-id">RX-003</span>
                    <span class="priority-badge routine">ROUTINE</span>
                </div>
                <div class="prescription-details">
                    <h5>Paracetamol 500mg</h5>
                    <p><strong>Patient:</strong> John Doe</p>
                    <p><strong>Doctor:</strong> Dr. Smith</p>
                    <p><strong>Dosage:</strong> 4 times daily as needed</p>
                    <p><strong>Prescribed:</strong> 2 hours ago</p>
                </div>
                <div class="prescription-actions">
                    <button class="btn btn-success">Dispense</button>
                    <button class="btn btn-info">View Details</button>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.header-section {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 2rem;
}

.pending-prescriptions {
    background: white;
    padding: 1.5rem;
    border-radius: 8px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.prescription-queue {
    display: flex;
    flex-direction: column;
    gap: 1rem;
    margin-top: 1rem;
}

.prescription-card {
    border: 1px solid #e5e7eb;
    border-radius: 8px;
    padding: 1.5rem;
    background: #f9fafb;
}

.prescription-card.urgent {
    border-left: 4px solid #dc2626;
    background: #fef2f2;
}

.prescription-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 1rem;
}

.prescription-id {
    font-weight: 600;
    color: #374151;
}

.priority-badge {
    padding: 0.25rem 0.5rem;
    border-radius: 4px;
    font-size: 0.75rem;
    font-weight: 500;
}

.priority-badge.urgent { background: #dc2626; color: white; }
.priority-badge.routine { background: #16a34a; color: white; }

.prescription-details h5 {
    margin: 0 0 0.5rem 0;
    color: #1f2937;
}

.prescription-details p {
    margin: 0.25rem 0;
    color: #6b7280;
    font-size: 0.875rem;
}

.prescription-actions {
    margin-top: 1rem;
    display: flex;
    gap: 0.5rem;
}

.btn {
    padding: 0.5rem 1rem;
    border: none;
    border-radius: 4px;
    cursor: pointer;
    font-size: 0.875rem;
}

.btn-success { background: #16a34a; color: white; }
.btn-info { background: #06b6d4; color: white; }
.btn-secondary { background: #6b7280; color: white; }
</style>
<?= $this->endSection() ?>
