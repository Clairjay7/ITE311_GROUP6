<?= $this->extend('templates/template') ?>

<?= $this->section('content') ?>
<div class="container">
    <div class="header-section">
        <h2>⏳ Pending Tests</h2>
        <div class="actions">
            <a href="<?= base_url('laboratory/dashboard') ?>" class="btn btn-secondary">Back to Dashboard</a>
        </div>
    </div>

    <div class="pending-tests">
        <h4>Tests Waiting to be Processed</h4>
        <div class="test-queue">
            <div class="test-card urgent">
                <div class="test-header">
                    <span class="test-id">LAB-001</span>
                    <span class="priority-badge urgent">URGENT</span>
                </div>
                <div class="test-details">
                    <h5>CBC - Complete Blood Count</h5>
                    <p><strong>Patient:</strong> John Doe</p>
                    <p><strong>Doctor:</strong> Dr. Smith</p>
                    <p><strong>Requested:</strong> 30 minutes ago</p>
                </div>
                <div class="test-actions">
                    <button class="btn btn-primary">Start Test</button>
                    <button class="btn btn-info">View Details</button>
                </div>
            </div>

            <div class="test-card">
                <div class="test-header">
                    <span class="test-id">LAB-003</span>
                    <span class="priority-badge routine">ROUTINE</span>
                </div>
                <div class="test-details">
                    <h5>Urinalysis</h5>
                    <p><strong>Patient:</strong> Mary Johnson</p>
                    <p><strong>Doctor:</strong> Dr. Wilson</p>
                    <p><strong>Requested:</strong> 2 hours ago</p>
                </div>
                <div class="test-actions">
                    <button class="btn btn-primary">Start Test</button>
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

.pending-tests {
    background: white;
    padding: 1.5rem;
    border-radius: 8px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.test-queue {
    display: flex;
    flex-direction: column;
    gap: 1rem;
    margin-top: 1rem;
}

.test-card {
    border: 1px solid #e5e7eb;
    border-radius: 8px;
    padding: 1.5rem;
    background: #f9fafb;
}

.test-card.urgent {
    border-left: 4px solid #dc2626;
    background: #fef2f2;
}

.test-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 1rem;
}

.test-id {
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

.test-details h5 {
    margin: 0 0 0.5rem 0;
    color: #1f2937;
}

.test-details p {
    margin: 0.25rem 0;
    color: #6b7280;
    font-size: 0.875rem;
}

.test-actions {
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

.btn-primary { background: #3b82f6; color: white; }
.btn-info { background: #06b6d4; color: white; }
.btn-secondary { background: #6b7280; color: white; }
</style>
<?= $this->endSection() ?>
