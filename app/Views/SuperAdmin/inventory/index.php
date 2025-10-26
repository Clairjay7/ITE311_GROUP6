<?= $this->extend('templates/template') ?>

<?= $this->section('content') ?>
<style>
    .page-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 16px;
        margin-bottom: 24px;
    }
    .page-title {
        font-size: 28px;
        font-weight: 700;
        color: #1f2937;
        margin: 0;
    }
    .alert {
        padding: 14px 18px;
        border-radius: 10px;
        margin-bottom: 20px;
        border: 1px solid transparent;
        display: flex;
        align-items: center;
        gap: 10px;
        font-size: 14px;
    }
    .alert-success { background: #ecfdf5; border-color: #34d399; color: #065f46; }
    .alert-error { background: #fef2f2; border-color: #f87171; color: #991b1b; }
    .table-responsive { overflow-x: auto; }
    .inventory-table {
        width: 100%;
        border-collapse: collapse;
        background: white;
        border: 1px solid #e5e7eb;
        border-radius: 12px;
        overflow: hidden;
    }
    .inventory-table thead { background: #f8fafc; }
    .inventory-table th,
    .inventory-table td {
        padding: 14px 16px;
        border-bottom: 1px solid #e5e7eb;
        font-size: 14px;
        color: #374151;
        text-align: left;
    }
    .inventory-table tbody tr:last-child td { border-bottom: none; }
    .row-low-stock { background: #fef3c7 !important; }
    .row-expiring { background: #fee2e2 !important; }
    .status-badge {
        display: inline-block;
        padding: 4px 8px;
        border-radius: 6px;
        font-size: 12px;
        font-weight: 600;
        text-transform: capitalize;
    }
    .status-available { background: #dcfce7; color: #166534; }
    .status-low_stock { background: #fef08a; color: #92400e; }
    .status-out_of_stock { background: #fee2e2; color: #b91c1c; }
    .status-expired { background: #fecaca; color: #7f1d1d; }
    .status-inactive { background: #e2e8f0; color: #475569; }
    .action-form {
        display: flex;
        flex-wrap: wrap;
        gap: 8px;
        align-items: center;
    }
    .action-form input[type=number],
    .action-form select,
    .action-form input[type=date] {
        padding: 6px 10px;
        border-radius: 6px;
        border: 1px solid #d1d5db;
        font-size: 13px;
    }
    .btn-update {
        background: #2563eb;
        color: white;
        border: none;
        border-radius: 6px;
        padding: 8px 14px;
        cursor: pointer;
        font-size: 13px;
        transition: background 0.2s ease;
    }
    .btn-update:hover { background: #1d4ed8; }
    .empty-state {
        text-align: center;
        padding: 60px 20px;
        background: white;
        border: 1px dashed #d1d5db;
        border-radius: 12px;
        color: #6b7280;
    }
</style>

<div class="page-header">
    <h1 class="page-title">📦 Inventory Management</h1>
    <a href="<?= base_url('super-admin/unified') ?>" class="btn btn-secondary">← Back to Dashboard</a>
</div>

<?php if (session()->getFlashdata('message')): ?>
    <div class="alert alert-success">
        ✅ <?= esc(session()->getFlashdata('message')) ?>
    </div>
<?php endif; ?>

<?php if (session()->getFlashdata('error')): ?>
    <div class="alert alert-error">
        ⚠️ <?= esc(session()->getFlashdata('error')) ?>
    </div>
<?php endif; ?>

<div id="inventory-feedback" class="alert alert-success" role="alert" style="display:none;"></div>

<?php if (isset($tableExists) && !$tableExists): ?>
    <div class="alert alert-error">
        ⚠️ Inventory data is unavailable because the <strong>inventory</strong> table is missing. Please run the migration or seed data to enable tracking.
    </div>
<?php elseif (empty($items)): ?>
    <div class="empty-state">
        <h3>No inventory records yet.</h3>
        <p>Items entered through pharmacy or admin workflows will appear here for monitoring.</p>
    </div>
<?php else: ?>
    <div class="table-responsive">
        <table class="inventory-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Item Name</th>
                    <th>Description</th>
                    <th>Quantity</th>
                    <th>Supplier</th>
                    <th>Expiration Date</th>
                    <th>Status</th>
                    <th>Last Updated</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($items as $item): ?>
                    <?php
                        $rowClasses = [];
                        if (!empty($item['is_low_stock'])) {
                            $rowClasses[] = 'row-low-stock';
                        }
                        if (!empty($item['is_expiring_soon'])) {
                            $rowClasses[] = 'row-expiring';
                        }
                    ?>
                    <tr class="<?= esc(implode(' ', $rowClasses)) ?>" data-item-id="<?= esc($item['id']) ?>">
                        <td>#<?= esc($item['id']) ?></td>
                        <td><?= esc($item['item_name']) ?></td>
                        <td><?= esc($item['description'] ?? '—') ?></td>
                        <td data-field="quantity-display"><?= esc($item['quantity'] ?? 0) ?></td>
                        <td><?= esc($item['supplier'] ?? 'Unknown') ?></td>
                        <td data-field="expiration-display">
                            <?php if (!empty($item['expiration_date'])): ?>
                                <?= esc(date('M d, Y', strtotime($item['expiration_date']))) ?>
                            <?php else: ?>
                                —
                            <?php endif; ?>
                        </td>
                        <td>
                            <span class="status-badge status-<?= esc(strtolower($item['status'] ?? 'inactive')) ?>" data-status-badge>
                                <?= esc(ucwords(str_replace('_', ' ', $item['status'] ?? 'inactive'))) ?>
                            </span>
                        </td>
                        <td data-field="updated-at">
                            <?= !empty($item['updated_at']) ? esc(date('M d, Y h:i A', strtotime($item['updated_at']))) : '—' ?>
                        </td>
                        <td>
                            <form action="<?= base_url('super-admin/inventory/update/' . $item['id']) ?>" method="post" class="action-form" data-item-id="<?= esc($item['id']) ?>">
                                <?= csrf_field() ?>
                                <input type="number" name="quantity" value="<?= esc($item['quantity'] ?? 0) ?>" min="0" placeholder="Qty">
                                <select name="status">
                                    <option value="">-- Status --</option>
                                    <?php foreach ($statusOptions as $status): ?>
                                        <option value="<?= esc($status) ?>" <?= ($item['status'] ?? '') === $status ? 'selected' : '' ?>>
                                            <?= esc(ucwords(str_replace('_', ' ', $status))) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                                <input type="date" name="expiration_date" value="<?= !empty($item['expiration_date']) ? esc(date('Y-m-d', strtotime($item['expiration_date']))) : '' ?>">
                                <button type="submit" class="btn-update">Update</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
<?php endif; ?>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const forms = document.querySelectorAll('.action-form');
    const feedbackEl = document.getElementById('inventory-feedback');

    const updateCsrf = (tokenName, tokenHash) => {
        if (!tokenName || !tokenHash) return;
        document.querySelectorAll(`input[name="${tokenName}"]`).forEach((input) => {
            input.value = tokenHash;
        });
    };

    const showFeedback = (message, type = 'success') => {
        if (!feedbackEl) return;
        feedbackEl.textContent = message;
        feedbackEl.className = `alert ${type === 'success' ? 'alert-success' : 'alert-error'}`;
        feedbackEl.style.display = 'flex';

        if (type === 'success') {
            setTimeout(() => {
                feedbackEl.style.display = 'none';
            }, 3000);
        }
    };

    forms.forEach((form) => {
        form.addEventListener('submit', async (event) => {
            event.preventDefault();

            const submitButton = form.querySelector('button[type="submit"]');
            const originalText = submitButton ? submitButton.textContent : '';
            if (submitButton) {
                submitButton.disabled = true;
                submitButton.textContent = 'Saving...';
            }

            const formData = new FormData(form);

            try {
                const response = await fetch(form.action, {
                    method: 'POST',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json',
                    },
                    body: formData,
                });

                const contentType = response.headers.get('content-type');
                if (!contentType || !contentType.includes('application/json')) {
                    throw new Error('Unexpected server response.');
                }

                const result = await response.json();

                updateCsrf(result?.csrfToken, result?.csrfHash);

                if (!response.ok || !result.success) {
                    showFeedback(result?.error || 'Failed to update inventory item.', 'error');
                    return;
                }

                const item = result.item || {};
                const row = form.closest('tr');
                if (row) {
                    row.classList.remove('row-low-stock', 'row-expiring');
                    if (Array.isArray(item.row_classes)) {
                        item.row_classes.forEach((cls) => {
                            if (cls) row.classList.add(cls);
                        });
                    }

                    const quantityDisplay = row.querySelector('[data-field="quantity-display"]');
                    if (quantityDisplay) {
                        quantityDisplay.textContent = item.quantity ?? quantityDisplay.textContent;
                    }

                    const statusBadge = row.querySelector('[data-status-badge]');
                    if (statusBadge && item.status_badge_class) {
                        statusBadge.className = `status-badge ${item.status_badge_class}`;
                        statusBadge.textContent = item.status_label ?? statusBadge.textContent;
                    }

                    const expirationDisplay = row.querySelector('[data-field="expiration-display"]');
                    if (expirationDisplay && Object.prototype.hasOwnProperty.call(item, 'formatted_expiration')) {
                        expirationDisplay.textContent = item.formatted_expiration || '—';
                    }

                    const updatedDisplay = row.querySelector('[data-field="updated-at"]');
                    if (updatedDisplay && item.formatted_updated_at) {
                        updatedDisplay.textContent = item.formatted_updated_at;
                    }
                }

                const quantityInput = form.querySelector('input[name="quantity"]');
                if (quantityInput && Object.prototype.hasOwnProperty.call(item, 'quantity')) {
                    quantityInput.value = item.quantity;
                }

                const statusSelect = form.querySelector('select[name="status"]');
                if (statusSelect && item.status) {
                    statusSelect.value = item.status;
                }

                const expirationInput = form.querySelector('input[name="expiration_date"]');
                if (expirationInput && Object.prototype.hasOwnProperty.call(item, 'raw_expiration')) {
                    expirationInput.value = item.raw_expiration;
                }

                showFeedback(result.message || 'Inventory item updated successfully.', 'success');
            } catch (error) {
                showFeedback('Unexpected error updating inventory item.', 'error');
            } finally {
                if (submitButton) {
                    submitButton.disabled = false;
                    submitButton.textContent = originalText;
                }
            }
        });
    });
});
</script>

<?= $this->endSection() ?>
