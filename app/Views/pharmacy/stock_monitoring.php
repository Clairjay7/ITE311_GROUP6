<?= $this->extend('template/header') ?>

<?= $this->section('title') ?>
Stock Monitoring
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<style>
    .stock-container {
        background: #fff;
        border-radius: 12px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        padding: 24px;
        margin-bottom: 24px;
    }
    .stock-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 20px;
        padding-bottom: 16px;
        border-bottom: 2px solid #4caf50;
    }
    .stock-title {
        font-size: 24px;
        font-weight: 700;
        color: #2e7d32;
    }
    .btn-add {
        background: linear-gradient(135deg, #4caf50, #66bb6a);
        color: white;
        border: none;
        padding: 10px 20px;
        border-radius: 8px;
        cursor: pointer;
        font-size: 14px;
        font-weight: 600;
        text-decoration: none;
        display: inline-block;
    }
    .btn-add:hover {
        background: linear-gradient(135deg, #388e3c, #4caf50);
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(76, 175, 80, 0.3);
    }
    .stock-tabs {
        display: flex;
        gap: 12px;
        margin-bottom: 20px;
        border-bottom: 2px solid #e0e0e0;
    }
    .tab {
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
    .tab.active {
        color: #2e7d32;
        border-bottom-color: #4caf50;
    }
    .tab:hover {
        color: #2e7d32;
    }
    .stock-table {
        width: 100%;
        border-collapse: collapse;
    }
    .stock-table th {
        background: #f1f8e9;
        padding: 12px;
        text-align: left;
        font-weight: 600;
        color: #2e7d32;
        border-bottom: 2px solid #4caf50;
    }
    .stock-table td {
        padding: 12px;
        border-bottom: 1px solid #e0e0e0;
    }
    .stock-table tr:hover {
        background: #f9fbe7;
    }
    .stock-level {
        padding: 4px 12px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: 600;
        text-transform: uppercase;
    }
    .stock-critical {
        background: #ffebee;
        color: #c62828;
    }
    .stock-low {
        background: #fff3e0;
        color: #e65100;
    }
    .stock-normal {
        background: #e8f5e9;
        color: #2e7d32;
    }
    .btn-action {
        padding: 6px 12px;
        border: none;
        border-radius: 6px;
        cursor: pointer;
        font-size: 12px;
        margin-right: 4px;
    }
    .btn-edit {
        background: #2196f3;
        color: white;
    }
    .btn-delete {
        background: #f44336;
        color: white;
    }
    .btn-restock {
        background: #4caf50;
        color: white;
    }
    .tab-content {
        display: none;
    }
    .tab-content.active {
        display: block;
    }
</style>

<div class="stock-container">
    <div class="stock-header">
        <h1 class="stock-title">📦 Stock Monitoring</h1>
        <a href="<?= site_url('pharmacy/add-medicine') ?>" class="btn-add">
            <i class="fas fa-plus"></i> Add New Medicine
        </a>
    </div>

    <?php if (session()->getFlashdata('success')): ?>
        <div class="alert alert-success" style="padding: 12px; background: #d4edda; color: #155724; border-radius: 8px; margin-bottom: 16px;">
            <?= session()->getFlashdata('success') ?>
        </div>
    <?php endif; ?>

    <div class="stock-tabs">
        <button class="tab active" onclick="showTab('critical')">
            Critical (<?= count($criticalStock ?? []) ?>)
        </button>
        <button class="tab" onclick="showTab('low')">
            Low Stock (<?= count($lowStock ?? []) ?>)
        </button>
        <button class="tab" onclick="showTab('all')">
            All Medicines (<?= count($allMedicines ?? []) ?>)
        </button>
    </div>

    <!-- Critical Stock Tab -->
    <div id="critical-tab" class="tab-content active">
        <table class="stock-table">
            <thead>
                <tr>
                    <th>Medicine Name</th>
                    <th>Description</th>
                    <th>Quantity</th>
                    <th>Price</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($criticalStock ?? [])): ?>
                    <tr>
                        <td colspan="6" style="text-align: center; padding: 40px; color: #666;">
                            No critical stock items
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach (($criticalStock ?? []) as $medicine): ?>
                        <tr>
                            <td><strong><?= esc($medicine['item_name']) ?></strong></td>
                            <td><?= esc($medicine['description'] ?? 'N/A') ?></td>
                            <td><strong><?= $medicine['quantity'] ?></strong></td>
                            <td>₱<?= number_format($medicine['price'], 2) ?></td>
                            <td>
                                <span class="stock-level stock-critical">CRITICAL</span>
                            </td>
                            <td>
                                <button class="btn-action btn-restock" onclick="restockMedicine(<?= $medicine['id'] ?>, '<?= esc($medicine['item_name']) ?>')">
                                    <i class="fas fa-plus"></i> Restock
                                </button>
                                <button class="btn-action btn-edit" onclick="editMedicine(<?= $medicine['id'] ?>)">
                                    <i class="fas fa-edit"></i>
                                </button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <!-- Low Stock Tab -->
    <div id="low-tab" class="tab-content">
        <table class="stock-table">
            <thead>
                <tr>
                    <th>Medicine Name</th>
                    <th>Description</th>
                    <th>Quantity</th>
                    <th>Price</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($lowStock ?? [])): ?>
                    <tr>
                        <td colspan="6" style="text-align: center; padding: 40px; color: #666;">
                            No low stock items
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach (($lowStock ?? []) as $medicine): ?>
                        <tr>
                            <td><strong><?= esc($medicine['item_name']) ?></strong></td>
                            <td><?= esc($medicine['description'] ?? 'N/A') ?></td>
                            <td><strong><?= $medicine['quantity'] ?></strong></td>
                            <td>₱<?= number_format($medicine['price'], 2) ?></td>
                            <td>
                                <span class="stock-level stock-low">LOW</span>
                            </td>
                            <td>
                                <button class="btn-action btn-restock" onclick="restockMedicine(<?= $medicine['id'] ?>, '<?= esc($medicine['item_name']) ?>')">
                                    <i class="fas fa-plus"></i> Restock
                                </button>
                                <button class="btn-action btn-edit" onclick="editMedicine(<?= $medicine['id'] ?>)">
                                    <i class="fas fa-edit"></i>
                                </button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <!-- All Medicines Tab -->
    <div id="all-tab" class="tab-content">
        <table class="stock-table">
            <thead>
                <tr>
                    <th>Medicine Name</th>
                    <th>Description</th>
                    <th>Quantity</th>
                    <th>Price</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach (($allMedicines ?? []) as $medicine): ?>
                    <?php
                        $quantity = $medicine['quantity'];
                        $statusClass = 'stock-normal';
                        $statusText = 'NORMAL';
                        if ($quantity == 0 || $quantity < 10) {
                            $statusClass = 'stock-critical';
                            $statusText = 'CRITICAL';
                        } elseif ($quantity < 20) {
                            $statusClass = 'stock-low';
                            $statusText = 'LOW';
                        }
                    ?>
                    <tr>
                        <td><strong><?= esc($medicine['item_name']) ?></strong></td>
                        <td><?= esc($medicine['description'] ?? 'N/A') ?></td>
                        <td><strong><?= $quantity ?></strong></td>
                        <td>₱<?= number_format($medicine['price'], 2) ?></td>
                        <td>
                            <span class="stock-level <?= $statusClass ?>"><?= $statusText ?></span>
                        </td>
                        <td>
                            <button class="btn-action btn-restock" onclick="restockMedicine(<?= $medicine['id'] ?>, '<?= esc($medicine['item_name']) ?>')">
                                <i class="fas fa-plus"></i> Restock
                            </button>
                            <button class="btn-action btn-edit" onclick="editMedicine(<?= $medicine['id'] ?>)">
                                <i class="fas fa-edit"></i>
                            </button>
                            <button class="btn-action btn-delete" onclick="deleteMedicine(<?= $medicine['id'] ?>, '<?= esc($medicine['item_name']) ?>')">
                                <i class="fas fa-trash"></i>
                            </button>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<script>
function showTab(tabName) {
    // Hide all tabs
    document.querySelectorAll('.tab-content').forEach(content => {
        content.classList.remove('active');
    });
    document.querySelectorAll('.tab').forEach(tab => {
        tab.classList.remove('active');
    });

    // Show selected tab
    document.getElementById(tabName + '-tab').classList.add('active');
    event.target.classList.add('active');
}

function editMedicine(medicineId) {
    window.location.href = `<?= site_url('pharmacy/edit-medicine/') ?>${medicineId}`;
}

async function deleteMedicine(medicineId, medicineName) {
    if (!confirm(`Are you sure you want to delete ${medicineName}?`)) {
        return;
    }

    try {
        const response = await fetch(`<?= site_url('pharmacy/delete-medicine/') ?>${medicineId}`, {
            method: 'POST',
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
        alert('Failed to delete medicine');
    }
}

async function restockMedicine(medicineId, medicineName) {
    const quantity = prompt(`Restock ${medicineName}\n\nEnter quantity to add:`);
    
    if (quantity === null || quantity === '') {
        return;
    }

    const qty = parseInt(quantity);
    
    if (isNaN(qty) || qty <= 0) {
        alert('Please enter a valid quantity');
        return;
    }

    try {
        const formData = new FormData();
        formData.append('quantity', qty);
        formData.append('action', 'add');

        const response = await fetch(`<?= site_url('pharmacy/update-stock/') ?>${medicineId}`, {
            method: 'POST',
            body: formData
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
        alert('Failed to restock medicine');
    }
}
</script>

<?= $this->endSection() ?>

