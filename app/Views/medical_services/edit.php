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
    .service-info {
        background: #f8fafc;
        border: 1px solid var(--border);
        border-radius: 8px;
        padding: 16px;
        margin-bottom: 20px;
    }
    .service-meta {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
        gap: 12px;
        font-size: 14px;
    }
    .meta-item {
        display: flex;
        flex-direction: column;
        gap: 2px;
    }
    .meta-label {
        font-weight: 600;
        color: var(--muted);
    }
    .meta-value {
        color: var(--text);
    }
</style>

<div class="page-header">
    <h1 class="page-title">✏️ Edit Medical Service</h1>
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

<!-- Service Information -->
<div class="service-info">
    <h3 style="margin: 0 0 12px 0; color: var(--text);">Service Information</h3>
    <div class="service-meta">
        <div class="meta-item">
            <span class="meta-label">Service ID</span>
            <span class="meta-value">#<?= esc($service['id']) ?></span>
        </div>
        <div class="meta-item">
            <span class="meta-label">Created</span>
            <span class="meta-value"><?= date('M j, Y', strtotime($service['created_at'])) ?></span>
        </div>
        <div class="meta-item">
            <span class="meta-label">Last Updated</span>
            <span class="meta-value"><?= date('M j, Y g:i A', strtotime($service['updated_at'])) ?></span>
        </div>
    </div>
</div>

<div class="form-container">
    <form method="post" action="<?= base_url('medical-services/update/' . $service['id']) ?>">
        <?= csrf_field() ?>
        
        <div class="form-grid">
            <div class="form-group">
                <label for="service_name" class="form-label required">Service Name</label>
                <input type="text" 
                       id="service_name" 
                       name="service_name" 
                       class="form-input" 
                       value="<?= old('service_name', esc($service['service_name'])) ?>"
                       placeholder="e.g., General Consultation"
                       required>
                <div class="help-text">Enter a clear, descriptive name for the medical service</div>
            </div>

            <div class="form-group">
                <label for="category" class="form-label required">Category</label>
                <select id="category" name="category" class="form-select" required>
                    <option value="">Select Category</option>
                    <?php foreach ($categories as $category): ?>
                        <option value="<?= esc($category) ?>" 
                                <?= (old('category', $service['category']) === $category) ? 'selected' : '' ?>>
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
                           value="<?= old('price', number_format($service['price'], 2, '.', '')) ?>"
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
                    <option value="active" <?= (old('status', $service['status']) === 'active') ? 'selected' : '' ?>>Active</option>
                    <option value="inactive" <?= (old('status', $service['status']) === 'inactive') ? 'selected' : '' ?>>Inactive</option>
                    <option value="discontinued" <?= (old('status', $service['status']) === 'discontinued') ? 'selected' : '' ?>>Discontinued</option>
                </select>
                <div class="help-text">Set the availability status of this service</div>
            </div>

            <div class="form-group full-width">
                <label for="description" class="form-label">Description</label>
                <textarea id="description" 
                          name="description" 
                          class="form-textarea" 
                          placeholder="Provide a detailed description of the service, including what it involves, duration, preparation requirements, etc."><?= old('description', esc($service['description'] ?? '')) ?></textarea>
                <div class="help-text">Optional: Add detailed information about the service for staff and billing reference</div>
            </div>
        </div>

        <div class="form-actions">
            <button type="submit" class="btn">
                💾 Update Service
            </button>
            <a href="<?= base_url('medical-services') ?>" class="btn btn-secondary">
                Cancel
            </a>
            <?php if (session()->get('role') === 'superadmin'): ?>
                <button type="button" 
                        onclick="deleteService(<?= $service['id'] ?>, '<?= esc($service['service_name']) ?>')" 
                        class="btn" 
                        style="background: var(--danger); margin-left: auto;">
                    🗑️ Delete Service
                </button>
            <?php endif; ?>
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

// Delete service function
function deleteService(id, name) {
    if (confirm(`Are you sure you want to delete the service "${name}"? This action cannot be undone and may affect existing appointments and billing records.`)) {
        fetch(`<?= base_url('medical-services/delete/') ?>${id}`, {
            method: 'DELETE',
            headers: {
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                window.location.href = '<?= base_url('medical-services') ?>';
            } else {
                alert('Error: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred while deleting the service.');
        });
    }
}

// Warn about status changes
document.getElementById('status').addEventListener('change', function() {
    const originalStatus = '<?= $service['status'] ?>';
    const newStatus = this.value;
    
    if (originalStatus === 'active' && newStatus !== 'active') {
        if (!confirm('Changing this service to inactive or discontinued may affect existing appointments and billing. Are you sure you want to continue?')) {
            this.value = originalStatus;
        }
    }
});
</script>

<?= $this->endSection() ?>
