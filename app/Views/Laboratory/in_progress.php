<?= $this->extend('templates/template') ?>

<?= $this->section('content') ?>
<div class="container">
    <div class="header-section">
        <h2>🔬 Tests In Progress</h2>
        <div class="actions">
            <a href="<?= base_url('laboratory/dashboard') ?>" class="btn btn-secondary">Back to Dashboard</a>
        </div>
    </div>

    <div class="in-progress-tests">
        <h4>Currently Running Tests</h4>
        <div class="test-grid">
            <div class="test-card active">
                <div class="test-header">
                    <span class="test-id">LAB-002</span>
                    <span class="status-badge in-progress">IN PROGRESS</span>
                </div>
                <div class="test-details">
                    <h5>Blood Sugar Test</h5>
                    <p><strong>Patient:</strong> Jane Smith</p>
                    <p><strong>Analyzer:</strong> Chemistry Unit 1</p>
                    <p><strong>Started:</strong> 15 minutes ago</p>
                    <p><strong>ETA:</strong> 10 minutes</p>
                </div>
                <div class="progress-bar">
                    <div class="progress-fill" style="width: 60%;"></div>
                </div>
                <div class="test-actions">
                    <button class="btn btn-success">Mark Complete</button>
                    <button class="btn btn-warning">Add Note</button>
                </div>
            </div>

            <div class="test-card active">
                <div class="test-header">
                    <span class="test-id">LAB-004</span>
                    <span class="status-badge in-progress">IN PROGRESS</span>
                </div>
                <div class="test-details">
                    <h5>Lipid Panel</h5>
                    <p><strong>Patient:</strong> Robert Wilson</p>
                    <p><strong>Analyzer:</strong> Chemistry Unit 2</p>
                    <p><strong>Started:</strong> 5 minutes ago</p>
                    <p><strong>ETA:</strong> 20 minutes</p>
                </div>
                <div class="progress-bar">
                    <div class="progress-fill" style="width: 25%;"></div>
                </div>
                <div class="test-actions">
                    <button class="btn btn-success">Mark Complete</button>
                    <button class="btn btn-warning">Add Note</button>
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

.in-progress-tests {
    background: white;
    padding: 1.5rem;
    border-radius: 8px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.test-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(350px, 1fr));
    gap: 1.5rem;
    margin-top: 1rem;
}

.test-card {
    border: 1px solid #e5e7eb;
    border-radius: 8px;
    padding: 1.5rem;
    background: #f9fafb;
}

.test-card.active {
    border-left: 4px solid #3b82f6;
    background: #eff6ff;
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

.status-badge {
    padding: 0.25rem 0.5rem;
    border-radius: 4px;
    font-size: 0.75rem;
    font-weight: 500;
}

.status-badge.in-progress { background: #3b82f6; color: white; }

.test-details h5 {
    margin: 0 0 0.5rem 0;
    color: #1f2937;
}

.test-details p {
    margin: 0.25rem 0;
    color: #6b7280;
    font-size: 0.875rem;
}

.progress-bar {
    width: 100%;
    height: 8px;
    background: #e5e7eb;
    border-radius: 4px;
    overflow: hidden;
    margin: 1rem 0;
}

.progress-fill {
    height: 100%;
    background: #3b82f6;
    transition: width 0.3s ease;
}

.test-actions {
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
.btn-warning { background: #f59e0b; color: white; }
.btn-secondary { background: #6b7280; color: white; }
</style>
<?= $this->endSection() ?>
