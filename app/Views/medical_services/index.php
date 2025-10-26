<?= $this->extend('templates/template') ?>

<?= $this->section('content') ?>

<style>
    .page-header {
        display: flex;
        justify-content: between;
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
    .stats-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 16px;
        margin-bottom: 24px;
    }
    .stat-card {
        background: var(--surface);
        border: 1px solid var(--border);
        border-radius: var(--radius);
        padding: 20px;
        text-align: center;
        box-shadow: var(--shadow);
    }
    .stat-number {
        font-size: 32px;
        font-weight: 700;
        color: var(--primary);
        margin-bottom: 8px;
    }
    .stat-label {
        color: var(--muted);
        font-size: 14px;
    }
    .filters-row {
        display: flex;
        gap: 12px;
        margin-bottom: 20px;
        flex-wrap: wrap;
        align-items: center;
    }
    .filter-group {
        display: flex;
        flex-direction: column;
        gap: 4px;
    }
    .filter-group label {
        font-size: 14px;
        color: var(--muted);
        font-weight: 500;
    }
    .filter-select, .search-input {
        padding: 8px 12px;
        border: 1px solid var(--border);
        border-radius: 8px;
        font-size: 14px;
    }
    .search-input {
        min-width: 250px;
    }
    .services-table {
        background: var(--surface);
        border: 1px solid var(--border);
        border-radius: var(--radius);
        overflow: hidden;
        box-shadow: var(--shadow);
    }
    .table {
        width: 100%;
        border-collapse: collapse;
    }
    .table th {
        background: #f8fafc;
        padding: 12px 16px;
        text-align: left;
        font-weight: 600;
        color: var(--text);
        border-bottom: 1px solid var(--border);
    }
    .table td {
        padding: 12px 16px;
        border-bottom: 1px solid var(--border);
    }
    .table tr:hover {
        background: #f8fafc;
    }
    .category-badge {
        display: inline-block;
        padding: 4px 8px;
        border-radius: 6px;
        font-size: 12px;
        font-weight: 500;
        text-transform: capitalize;
    }
    .category-consultation { background: #dbeafe; color: #1e40af; }
    .category-laboratory { background: #dcfce7; color: #166534; }
    .category-imaging { background: #fef3c7; color: #92400e; }
    .category-surgery { background: #fecaca; color: #991b1b; }
    .category-therapy { background: #e0e7ff; color: #3730a3; }
    .category-emergency { background: #fee2e2; color: #dc2626; }
    .category-other { background: #f3f4f6; color: #374151; }
    .status-badge {
        display: inline-block;
        padding: 4px 8px;
        border-radius: 6px;
        font-size: 12px;
        font-weight: 500;
        text-transform: capitalize;
    }
    .status-active { background: #dcfce7; color: #166534; }
    .status-inactive { background: #fef3c7; color: #92400e; }
    .status-discontinued { background: #fecaca; color: #991b1b; }
    .price-cell {
        font-weight: 600;
        color: var(--primary);
    }
    .actions-cell {
        display: flex;
        gap: 8px;
    }
    .btn-sm {
        padding: 6px 10px;
        font-size: 12px;
        border-radius: 6px;
    }
    .btn-edit {
        background: var(--warning);
        color: white;
        text-decoration: none;
    }
    .btn-edit:hover {
        background: #d97706;
    }
    .btn-delete {
        background: var(--danger);
        color: white;
        border: none;
        cursor: pointer;
    }
    .btn-delete:hover {
        background: #dc2626;
    }
    .empty-state {
        text-align: center;
        padding: 40px;
        color: var(--muted);
    }
    .alert {
        padding: 12px 16px;
        border-radius: 8px;
        margin-bottom: 20px;
    }
    .alert-success {
        background: #dcfce7;
        color: #166534;
        border: 1px solid #bbf7d0;
    }
    .alert-error {
        background: #fecaca;
        color: #991b1b;
        border: 1px solid #fca5a5;
    }
</style>

<div class="page-header">
    <h1 class="page-title">🧪 Medical Services</h1>
    <div class="actions-row">
        <?php if (session()->get('role') === 'superadmin' || session()->get('role') === 'doctor'): ?>
            <a href="<?= base_url('medical-services/create') ?>" class="btn">
                ➕ Add New Service
            </a>
        <?php endif; ?>
        <a href="<?= base_url('super-admin/unified') ?>" class="btn btn-secondary">
            ← Back to Dashboard
        </a>
    </div>
</div>

<?php if (session()->getFlashdata('success')): ?>
    <div class="alert alert-success">
        <?= session()->getFlashdata('success') ?>
    </div>
<?php endif; ?>

<?php if (session()->getFlashdata('error')): ?>
    <div class="alert alert-error">
        <?= session()->getFlashdata('error') ?>
    </div>
<?php endif; ?>

<!-- Statistics -->
<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-number"><?= esc($stats['total_services'] ?? 0) ?></div>
        <div class="stat-label">Total Services</div>
    </div>
    <div class="stat-card">
        <div class="stat-number"><?= esc($stats['active_services'] ?? 0) ?></div>
        <div class="stat-label">Active Services</div>
    </div>
    <div class="stat-card">
        <div class="stat-number"><?= esc($stats['category_consultation'] ?? 0) ?></div>
        <div class="stat-label">Consultations</div>
    </div>
    <div class="stat-card">
        <div class="stat-number"><?= esc($stats['category_laboratory'] ?? 0) ?></div>
        <div class="stat-label">Lab Tests</div>
    </div>
</div>

<!-- Filters -->
<div class="filters-row">
    <div class="filter-group">
        <label>Search Services</label>
        <input type="text" class="search-input" id="searchServices" placeholder="Search by name or description...">
    </div>
    <div class="filter-group">
        <label>Category</label>
        <select class="filter-select" id="filterCategory">
            <option value="">All Categories</option>
            <?php foreach ($categories as $category): ?>
                <option value="<?= esc($category) ?>"><?= ucfirst(esc($category)) ?></option>
            <?php endforeach; ?>
        </select>
    </div>
    <div class="filter-group">
        <label>Status</label>
        <select class="filter-select" id="filterStatus">
            <option value="">All Status</option>
            <option value="active">Active</option>
            <option value="inactive">Inactive</option>
            <option value="discontinued">Discontinued</option>
        </select>
    </div>
</div>

<!-- Services Table -->
<div class="services-table">
    <table class="table" id="servicesTable">
        <thead>
            <tr>
                <th>Service Name</th>
                <th>Category</th>
                <th>Price</th>
                <th>Status</th>
                <th>Description</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($services)): ?>
                <tr>
                    <td colspan="6" class="empty-state">
                        <div>No medical services found.</div>
                        <?php if (session()->get('role') === 'superadmin' || session()->get('role') === 'doctor'): ?>
                            <a href="<?= base_url('medical-services/create') ?>" class="btn" style="margin-top: 12px;">
                                Add First Service
                            </a>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php else: ?>
                <?php foreach ($services as $service): ?>
                    <tr data-category="<?= esc($service['category']) ?>" data-status="<?= esc($service['status']) ?>">
                        <td>
                            <strong><?= esc($service['service_name']) ?></strong>
                        </td>
                        <td>
                            <span class="category-badge category-<?= esc($service['category']) ?>">
                                <?= ucfirst(esc($service['category'])) ?>
                            </span>
                        </td>
                        <td class="price-cell">
                            ₱<?= number_format($service['price'], 2) ?>
                        </td>
                        <td>
                            <span class="status-badge status-<?= esc($service['status']) ?>">
                                <?= ucfirst(esc($service['status'])) ?>
                            </span>
                        </td>
                        <td>
                            <?= esc(substr($service['description'] ?? 'No description', 0, 100)) ?>
                            <?= strlen($service['description'] ?? '') > 100 ? '...' : '' ?>
                        </td>
                        <td>
                            <div class="actions-cell">
                                <?php if (session()->get('role') === 'superadmin' || session()->get('role') === 'doctor'): ?>
                                    <a href="<?= base_url('medical-services/edit/' . $service['id']) ?>" 
                                       class="btn btn-sm btn-edit">
                                        ✏️ Edit
                                    </a>
                                <?php endif; ?>
                                <?php if (session()->get('role') === 'superadmin'): ?>
                                    <button onclick="deleteService(<?= $service['id'] ?>, '<?= esc($service['service_name']) ?>')" 
                                            class="btn btn-sm btn-delete">
                                        🗑️ Delete
                                    </button>
                                <?php endif; ?>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<script>
// Search functionality
document.getElementById('searchServices').addEventListener('input', function() {
    const searchTerm = this.value.toLowerCase();
    filterTable();
});

// Filter functionality
document.getElementById('filterCategory').addEventListener('change', filterTable);
document.getElementById('filterStatus').addEventListener('change', filterTable);

function filterTable() {
    const searchTerm = document.getElementById('searchServices').value.toLowerCase();
    const categoryFilter = document.getElementById('filterCategory').value;
    const statusFilter = document.getElementById('filterStatus').value;
    const rows = document.querySelectorAll('#servicesTable tbody tr');

    rows.forEach(row => {
        if (row.querySelector('.empty-state')) return;
        
        const serviceName = row.cells[0].textContent.toLowerCase();
        const description = row.cells[4].textContent.toLowerCase();
        const category = row.dataset.category;
        const status = row.dataset.status;
        
        const matchesSearch = serviceName.includes(searchTerm) || description.includes(searchTerm);
        const matchesCategory = !categoryFilter || category === categoryFilter;
        const matchesStatus = !statusFilter || status === statusFilter;
        
        if (matchesSearch && matchesCategory && matchesStatus) {
            row.style.display = '';
        } else {
            row.style.display = 'none';
        }
    });
}

// Delete service function
function deleteService(id, name) {
    if (confirm(`Are you sure you want to delete the service "${name}"? This action cannot be undone.`)) {
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
                location.reload();
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
</script>

<?= $this->endSection() ?>
