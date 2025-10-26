<?= $this->extend('templates/template') ?>

<?= $this->section('content') ?>

<style>
    .page-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 24px;
        flex-wrap: wrap;
        gap: 16px;
    }
    .page-title {
        font-size: 28px;
        font-weight: 700;
        color: var(--text);
        margin: 0;
    }
    .form-container {
        background: var(--surface);
        border: 1px solid var(--border);
        border-radius: var(--radius);
        padding: 24px;
        box-shadow: var(--shadow);
        max-width: 800px;
    }
    .form-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
        gap: 20px;
    }
    .form-group {
        display: flex;
        flex-direction: column;
        gap: 6px;
    }
    .form-group.full-width {
        grid-column: 1 / -1;
    }
    .form-label {
        font-weight: 600;
        color: var(--text);
        font-size: 14px;
    }
    .form-label.required::after {
        content: ' *';
        color: var(--danger);
    }
    .form-input, .form-select, .form-textarea {
        padding: 12px 16px;
        border: 1px solid var(--border);
        border-radius: 8px;
        font-size: 14px;
        transition: border-color 0.2s ease;
    }
    .form-input:focus, .form-select:focus, .form-textarea:focus {
        outline: none;
        border-color: var(--primary);
        box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1);
    }
    .form-textarea {
        resize: vertical;
        min-height: 100px;
    }
    .form-actions {
        display: flex;
        gap: 12px;
        margin-top: 24px;
        flex-wrap: wrap;
    }
    .alert {
        padding: 12px 16px;
        border-radius: 8px;
        margin-bottom: 20px;
    }
    .alert-error {
        background: #fecaca;
        color: #991b1b;
        border: 1px solid #fca5a5;
    }
    .error-list {
        margin: 0;
        padding-left: 20px;
    }
    .help-text {
        font-size: 12px;
        color: var(--muted);
        margin-top: 4px;
    }
    .price-input-group {
        position: relative;
    }
    .price-input-group::before {
        content: '₱';
        position: absolute;
        left: 12px;
        top: 50%;
        transform: translateY(-50%);
        color: var(--muted);
        font-weight: 600;
    }
    .price-input-group .form-input {
        padding-left: 28px;
    }
    .category-info {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 12px;
        margin-top: 8px;
        padding: 16px;
        background: #f8fafc;
        border-radius: 8px;
    }
    .category-item {
        display: flex;
        align-items: center;
        gap: 8px;
        font-size: 12px;
        color: var(--muted);
    }
    .category-dot {
        width: 8px;
        height: 8px;
        border-radius: 50%;
    }
    .dot-consultation { background: #2563eb; }
    .dot-laboratory { background: #16a34a; }
    .dot-imaging { background: #d97706; }
    .dot-surgery { background: #dc2626; }
    .dot-therapy { background: #7c3aed; }
    .dot-emergency { background: #ef4444; }
    .dot-other { background: #6b7280; }
</style>

<div class="page-header">
    <h1 class="page-title">➕ Add Medical Service</h1>
    <div class="actions-row">
        <a href="<?= base_url('medical-services') ?>" class="btn btn-secondary">
            ← Back to Services
        </a>
    </div>
</div>

<?php if (session()->getFlashdata('errors')): ?>
    <div class="alert alert-error">
        <strong>Please fix the following errors:</strong>
        <ul class="error-list">
            <?php foreach (session()->getFlashdata('errors') as $error): ?>
                <li><?= esc($error) ?></li>
            <?php endforeach; ?>
        </ul>
    </div>
<?php endif; ?>

<?php if (session()->getFlashdata('error')): ?>
    <div class="alert alert-error">
        <?= session()->getFlashdata('error') ?>
    </div>
<?php endif; ?>

<div class="form-container">
    <form method="post" action="<?= base_url('medical-services/store') ?>">
        <?= csrf_field() ?>
        
        <div class="form-grid">
            <div class="form-group">
                <label for="service_name" class="form-label required">Service Name</label>
                <input type="text" 
                       id="service_name" 
                       name="service_name" 
                       class="form-input" 
                       value="<?= old('service_name') ?>"
                       placeholder="e.g., General Consultation"
                       required>
                <div class="help-text">Enter a clear, descriptive name for the medical service</div>
            </div>

            <div class="form-group">
                <label for="category" class="form-label required">Category</label>
                <select id="category" name="category" class="form-select" required>
                    <option value="">Select Category</option>
                    <?php foreach ($categories as $category): ?>
                        <option value="<?= esc($category) ?>" <?= old('category') === $category ? 'selected' : '' ?>>
                            <?= ucfirst(esc($category)) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <div class="help-text">Choose the most appropriate category for this service</div>
            </div>

            <div class="form-group">
                <label for="price" class="form-label required">Price</label>
                <div class="price-input-group">
                    <input type="number" 
                           id="price" 
                           name="price" 
                           class="form-input" 
                           value="<?= old('price') ?>"
                           step="0.01" 
                           min="0"
                           placeholder="0.00"
                           required>
                </div>
                <div class="help-text">Enter the service fee in Philippine Pesos (₱)</div>
            </div>

            <div class="form-group">
                <label for="status" class="form-label required">Status</label>
                <select id="status" name="status" class="form-select" required>
                    <option value="active" <?= old('status') === 'active' ? 'selected' : '' ?>>Active</option>
                    <option value="inactive" <?= old('status') === 'inactive' ? 'selected' : '' ?>>Inactive</option>
                    <option value="discontinued" <?= old('status') === 'discontinued' ? 'selected' : '' ?>>Discontinued</option>
                </select>
                <div class="help-text">Set the availability status of this service</div>
            </div>

            <div class="form-group full-width">
                <label for="description" class="form-label">Description</label>
                <textarea id="description" 
                          name="description" 
                          class="form-textarea" 
                          placeholder="Provide a detailed description of the service, including what it involves, duration, preparation requirements, etc."><?= old('description') ?></textarea>
                <div class="help-text">Optional: Add detailed information about the service for staff and billing reference</div>
            </div>
        </div>

        <!-- Category Information -->
        <div class="form-group full-width">
            <label class="form-label">Category Guide</label>
            <div class="category-info">
                <div class="category-item">
                    <div class="category-dot dot-consultation"></div>
                    <span><strong>Consultation:</strong> Doctor visits, check-ups</span>
                </div>
                <div class="category-item">
                    <div class="category-dot dot-laboratory"></div>
                    <span><strong>Laboratory:</strong> Blood tests, urine tests, cultures</span>
                </div>
                <div class="category-item">
                    <div class="category-dot dot-imaging"></div>
                    <span><strong>Imaging:</strong> X-rays, CT scans, MRI, ultrasound</span>
                </div>
                <div class="category-item">
                    <div class="category-dot dot-surgery"></div>
                    <span><strong>Surgery:</strong> Surgical procedures, operations</span>
                </div>
                <div class="category-item">
                    <div class="category-dot dot-therapy"></div>
                    <span><strong>Therapy:</strong> Physical therapy, rehabilitation</span>
                </div>
                <div class="category-item">
                    <div class="category-dot dot-emergency"></div>
                    <span><strong>Emergency:</strong> ER visits, urgent care</span>
                </div>
                <div class="category-item">
                    <div class="category-dot dot-other"></div>
                    <span><strong>Other:</strong> Miscellaneous services</span>
                </div>
            </div>
        </div>

        <div class="form-actions">
            <button type="submit" class="btn">
                💾 Save Service
            </button>
            <a href="<?= base_url('medical-services') ?>" class="btn btn-secondary">
                Cancel
            </a>
        </div>
    </form>
</div>

<script>
// Auto-format price input
document.getElementById('price').addEventListener('input', function() {
    let value = this.value;
    if (value && !isNaN(value)) {
        // Ensure two decimal places for display
        if (value.includes('.')) {
            let parts = value.split('.');
            if (parts[1] && parts[1].length > 2) {
                this.value = parseFloat(value).toFixed(2);
            }
        }
    }
});

// Form validation
document.querySelector('form').addEventListener('submit', function(e) {
    const serviceName = document.getElementById('service_name').value.trim();
    const category = document.getElementById('category').value;
    const price = document.getElementById('price').value;
    const status = document.getElementById('status').value;

    if (!serviceName || !category || !price || !status) {
        e.preventDefault();
        alert('Please fill in all required fields.');
        return;
    }

    if (parseFloat(price) < 0) {
        e.preventDefault();
        alert('Price cannot be negative.');
        return;
    }

    if (serviceName.length < 3) {
        e.preventDefault();
        alert('Service name must be at least 3 characters long.');
        return;
    }
});

// Auto-generate service name suggestions based on category
document.getElementById('category').addEventListener('change', function() {
    const category = this.value;
    const serviceNameInput = document.getElementById('service_name');
    
    if (!serviceNameInput.value && category) {
        const suggestions = {
            'consultation': 'General Consultation',
            'laboratory': 'Complete Blood Count (CBC)',
            'imaging': 'X-Ray Chest',
            'surgery': 'Minor Surgery',
            'therapy': 'Physical Therapy Session',
            'emergency': 'Emergency Room Visit',
            'other': 'Medical Service'
        };
        
        if (suggestions[category]) {
            serviceNameInput.placeholder = `e.g., ${suggestions[category]}`;
        }
    }
});
</script>

<?= $this->endSection() ?>
