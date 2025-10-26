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
        font-size: 30px;
        font-weight: 700;
        color: #1f2937;
        margin: 0;
    }
    .page-subtitle {
        margin: 6px 0 0;
        color: #6b7280;
        font-size: 14px;
    }
    .header-actions {
        display: flex;
        flex-wrap: wrap;
        gap: 12px;
    }
    .btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        padding: 10px 16px;
        border-radius: 10px;
        border: none;
        font-weight: 600;
        font-size: 14px;
        cursor: pointer;
        transition: background 0.2s ease;
        text-decoration: none;
    }
    .btn-primary { background: #2563eb; color: #ffffff; }
    .btn-primary:hover { background: #1d4ed8; }
    .btn-secondary { background: #111827; color: #ffffff; }
    .btn-secondary:hover { background: #030712; }
    .btn-outline { background: transparent; border: 1px solid #2563eb; color: #2563eb; }
    .btn-outline:hover { background: rgba(37,99,235,0.08); }
    .filters-card, .table-wrapper {
        background: #ffffff;
        border-radius: 14px;
        border: 1px solid #e5e7eb;
        padding: 20px;
        box-shadow: 0 6px 18px rgba(15,23,42,0.08);
        margin-bottom: 24px;
    }
    .filters-form {
        display: flex;
        flex-wrap: wrap;
        gap: 12px;
        align-items: flex-end;
    }
    .filters-form label {
        display: flex;
        flex-direction: column;
        gap: 6px;
        font-size: 12px;
        font-weight: 600;
        color: #1f2937;
    }
    .filters-form input,
    .filters-form select {
        padding: 10px 12px;
        border-radius: 10px;
        border: 1px solid #d1d5db;
        font-size: 14px;
        min-width: 180px;
    }
    table {
        width: 100%;
        border-collapse: collapse;
        border-radius: 12px;
        overflow: hidden;
    }
    thead { background: #f8fafc; }
    th, td {
        padding: 14px 16px;
        border-bottom: 1px solid #e5e7eb;
        text-align: left;
        font-size: 14px;
    }
    tbody tr:last-child td { border-bottom: none; }
    .badge {
        display: inline-flex;
        align-items: center;
        padding: 4px 10px;
        border-radius: 999px;
        font-size: 12px;
        font-weight: 600;
    }
    .badge-status-available { background: #dcfce7; color: #166534; }
    .badge-status-in_use { background: #dbeafe; color: #1d4ed8; }
    .badge-status-under_maintenance { background: #fef3c7; color: #92400e; }
    .badge-status-out_of_service { background: #fee2e2; color: #b91c1c; }
    .badge-condition-good { background: #ecfdf5; color: #047857; }
    .badge-condition-needs_service { background: #fef9c3; color: #b45309; }
    .badge-condition-damaged { background: #fee2e2; color: #b91c1c; }
    .empty-state {
        padding: 50px 20px;
        text-align: center;
        color: #6b7280;
        border: 2px dashed #d1d5db;
        border-radius: 12px;
        background: #ffffff;
    }
    dialog {
        border: none;
        border-radius: 14px;
        padding: 0;
        max-width: 720px;
        width: 92%;
        box-shadow: 0 24px 48px rgba(15,23,42,0.25);
    }
    dialog::backdrop { background: rgba(15,23,42,0.5); }
    .modal-header,
    .modal-footer {
        padding: 18px 24px;
        border-bottom: 1px solid #e5e7eb;
    }
    .modal-footer {
        border-bottom: none;
        border-top: 1px solid #e5e7eb;
        display: flex;
        justify-content: flex-end;
        gap: 12px;
    }
    .modal-body {
        padding: 24px;
        display: grid;
        gap: 16px;
    }
    .form-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
        gap: 16px;
    }
    .form-field {
        display: flex;
        flex-direction: column;
        gap: 8px;
    }
    .form-field label {
        font-weight: 600;
        color: #1f2937;
        font-size: 13px;
    }
    .form-field input,
    .form-field select,
    .form-field textarea {
        padding: 10px 12px;
        border-radius: 10px;
        border: 1px solid #d1d5db;
        font-size: 14px;
    }
    .form-field textarea {
        min-height: 120px;
        resize: vertical;
    }
    .alert {
        padding: 14px 18px;
        border-radius: 10px;
        border: 1px solid transparent;
        font-size: 14px;
        margin-bottom: 18px;
        display: none;
    }
    .alert-success { background: #ecfdf5; border-color: #34d399; color: #047857; }
    .alert-error { background: #fef2f2; border-color: #f87171; color: #b91c1c; }
</style>

<div class="page-header">
    <div>
        <h1 class="page-title">🔬 Equipment Management</h1>
        <p class="page-subtitle">Monitor laboratory devices, maintenance schedules, and calibration history.</p>
    </div>
    <div class="header-actions">
        <a href="<?= base_url('super-admin/laboratory') ?>" class="btn btn-outline">← Back to Laboratory</a>
        <a href="<?= base_url('super-admin/unified') ?>" class="btn btn-secondary">Back to Dashboard</a>
        <button class="btn btn-primary" id="open-create-modal">＋ New Equipment</button>
    </div>
</div>

<div id="equipment-feedback" class="alert"></div>

<div class="filters-card">
    <form class="filters-form" method="get" action="">
        <label>
            Search
            <input type="search" name="search" value="<?= esc($filters['search'] ?? '') ?>" placeholder="Name, type, or serial">
        </label>
        <label>
            Status
            <select name="status">
                <option value="">All</option>
                <?php foreach ($statusOptions as $key => $label): ?>
                    <option value="<?= esc($key) ?>" <?= ($filters['status'] ?? '') === $key ? 'selected' : '' ?>><?= esc($label) ?></option>
                <?php endforeach; ?>
            </select>
        </label>
        <label>
            Condition
            <select name="condition">
                <option value="">All</option>
                <?php foreach ($conditionOptions as $key => $label): ?>
                    <option value="<?= esc($key) ?>" <?= ($filters['condition'] ?? '') === $key ? 'selected' : '' ?>><?= esc($label) ?></option>
                <?php endforeach; ?>
            </select>
        </label>
        <button type="submit" class="btn btn-outline">Apply Filters</button>
        <a href="<?= base_url('super-admin/equipment') ?>" class="btn btn-outline">Reset</a>
    </form>
</div>

<?php if (empty($equipment)): ?>
    <div class="empty-state">
        <h3>No equipment records yet</h3>
        <p>Add your first laboratory equipment item to begin tracking maintenance and calibration.</p>
    </div>
<?php else: ?>
    <div class="table-wrapper">
        <table id="equipment-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Type</th>
                    <th>Serial Number</th>
                    <th>Status</th>
                    <th>Condition</th>
                    <th>Maintenance</th>
                    <th>Calibration</th>
                    <th>Usage Hours</th>
                    <th>Updated</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($equipment as $item): ?>
                    <?php
                        $formatted = [
                            'id' => $item['id'],
                            'equipment_name' => $item['equipment_name'],
                            'equipment_type' => $item['equipment_type'],
                            'serial_number' => $item['serial_number'],
                            'status' => $item['status'],
                            'condition' => $item['condition'],
                            'last_maintenance_date' => $item['last_maintenance_date'],
                            'next_maintenance_date' => $item['next_maintenance_date'],
                            'last_calibration_date' => $item['last_calibration_date'],
                            'next_calibration_date' => $item['next_calibration_date'],
                            'usage_hours' => $item['usage_hours'],
                            'updated_at' => $item['updated_at'],
                        ];
                    ?>
                    <tr data-equipment='<?= esc(json_encode($formatted), 'attr') ?>'>
                        <td>#<?= esc($item['id']) ?></td>
                        <td><?= esc($item['equipment_name']) ?></td>
                        <td><?= esc($item['equipment_type'] ?? '—') ?></td>
                        <td><?= esc($item['serial_number'] ?? '—') ?></td>
                        <td><span class="badge badge-status-<?= esc($item['status']) ?>"><?= esc($statusOptions[$item['status']] ?? ucfirst($item['status'])) ?></span></td>
                        <td><span class="badge badge-condition-<?= esc($item['condition']) ?>"><?= esc($conditionOptions[$item['condition']] ?? ucfirst($item['condition'])) ?></span></td>
                        <td>
                            <div>Last: <?= $item['last_maintenance_date'] ? esc(date('M d, Y', strtotime($item['last_maintenance_date']))) : '—' ?></div>
                            <div>Next: <?= $item['next_maintenance_date'] ? esc(date('M d, Y', strtotime($item['next_maintenance_date']))) : '—' ?></div>
                        </td>
                        <td>
                            <div>Last: <?= $item['last_calibration_date'] ? esc(date('M d, Y', strtotime($item['last_calibration_date']))) : '—' ?></div>
                            <div>Next: <?= $item['next_calibration_date'] ? esc(date('M d, Y', strtotime($item['next_calibration_date']))) : '—' ?></div>
                        </td>
                        <td><?= esc(number_format((float) $item['usage_hours'], 1)) ?> hrs</td>
                        <td><?= $item['updated_at'] ? esc(date('M d, Y h:i A', strtotime($item['updated_at']))) : '—' ?></td>
                        <td><button class="btn btn-outline btn-sm" data-edit-equipment>Edit</button></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
<?php endif; ?>

<dialog id="create-equipment-modal">
    <div class="modal-header">
        <h3>＋ New Equipment</h3>
        <button type="button" class="btn btn-outline" data-close-modal>Close</button>
    </div>
    <form id="create-equipment-form" method="post" action="<?= base_url('super-admin/equipment/add') ?>">
        <?= csrf_field() ?>
        <div class="modal-body">
            <div class="form-grid">
                <div class="form-field">
                    <label>Equipment Name<span style="color:#ef4444;">*</span></label>
                    <input type="text" name="equipment_name" required placeholder="e.g. Hematology Analyzer">
                </div>
                <div class="form-field">
                    <label>Equipment Type</label>
                    <input type="text" name="equipment_type" placeholder="e.g. Analyzer">
                </div>
                <div class="form-field">
                    <label>Serial Number</label>
                    <input type="text" name="serial_number" placeholder="e.g. SN-2025-001">
                </div>
                <div class="form-field">
                    <label>Status<span style="color:#ef4444;">*</span></label>
                    <select name="status" required>
                        <?php foreach ($statusOptions as $key => $label): ?>
                            <option value="<?= esc($key) ?>"><?= esc($label) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-field">
                    <label>Condition<span style="color:#ef4444;">*</span></label>
                    <select name="condition" required>
                        <?php foreach ($conditionOptions as $key => $label): ?>
                            <option value="<?= esc($key) ?>"><?= esc($label) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-field">
                    <label>Usage Hours</label>
                    <input type="number" step="0.1" name="usage_hours" placeholder="Total hours used">
                </div>
                <div class="form-field">
                    <label>Last Maintenance</label>
                    <input type="date" name="last_maintenance_date">
                </div>
                <div class="form-field">
                    <label>Next Maintenance</label>
                    <input type="date" name="next_maintenance_date">
                </div>
                <div class="form-field">
                    <label>Last Calibration</label>
                    <input type="date" name="last_calibration_date">
                </div>
                <div class="form-field">
                    <label>Next Calibration</label>
                    <input type="date" name="next_calibration_date">
                </div>
            </div>
            <div class="form-field">
                <label>Notes</label>
                <textarea name="result" placeholder="Optional notes (not stored)"></textarea>
            </div>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-outline" data-close-modal>Cancel</button>
            <button type="submit" class="btn btn-primary">Save Equipment</button>
        </div>
    </form>
</dialog>

<dialog id="edit-equipment-modal">
    <div class="modal-header">
        <h3>Update Equipment</h3>
        <button type="button" class="btn btn-outline" data-close-modal>Close</button>
    </div>
    <form id="edit-equipment-form" method="post">
        <?= csrf_field() ?>
        <div class="modal-body">
            <input type="hidden" id="edit-equipment-id" name="equipment_id">
            <div class="form-grid">
                <div class="form-field">
                    <label>Equipment</label>
                    <input type="text" id="edit-equipment-name" disabled>
                </div>
                <div class="form-field">
                    <label>Status<span style="color:#ef4444;">*</span></label>
                    <select name="status" id="edit-status" required>
                        <?php foreach ($statusOptions as $key => $label): ?>
                            <option value="<?= esc($key) ?>"><?= esc($label) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-field">
                    <label>Condition<span style="color:#ef4444;">*</span></label>
                    <select name="condition" id="edit-condition" required>
                        <?php foreach ($conditionOptions as $key => $label): ?>
                            <option value="<?= esc($key) ?>"><?= esc($label) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-field">
                    <label>Usage Hours</label>
                    <input type="number" step="0.1" name="usage_hours" id="edit-usage-hours" placeholder="Total hours used">
                </div>
                <div class="form-field">
                    <label>Last Maintenance</label>
                    <input type="date" name="last_maintenance_date" id="edit-last-maintenance">
                </div>
                <div class="form-field">
                    <label>Next Maintenance</label>
                    <input type="date" name="next_maintenance_date" id="edit-next-maintenance">
                </div>
                <div class="form-field">
                    <label>Last Calibration</label>
                    <input type="date" name="last_calibration_date" id="edit-last-calibration">
                </div>
                <div class="form-field">
                    <label>Next Calibration</label>
                    <input type="date" name="next_calibration_date" id="edit-next-calibration">
                </div>
            </div>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-outline" data-close-modal>Cancel</button>
            <button type="submit" class="btn btn-primary">Update Equipment</button>
        </div>
    </form>
</dialog>

<script>
(function() {
    const createModal = document.getElementById('create-equipment-modal');
    const editModal = document.getElementById('edit-equipment-modal');
    const openCreateBtn = document.getElementById('open-create-modal');
    const feedbackEl = document.getElementById('equipment-feedback');
    const equipmentTable = document.getElementById('equipment-table');

    const createForm = document.getElementById('create-equipment-form');
    const editForm = document.getElementById('edit-equipment-form');

    const editIdField = document.getElementById('edit-equipment-id');
    const editNameField = document.getElementById('edit-equipment-name');
    const editStatusField = document.getElementById('edit-status');
    const editConditionField = document.getElementById('edit-condition');
    const editUsageField = document.getElementById('edit-usage-hours');
    const editLastMaintField = document.getElementById('edit-last-maintenance');
    const editNextMaintField = document.getElementById('edit-next-maintenance');
    const editLastCalField = document.getElementById('edit-last-calibration');
    const editNextCalField = document.getElementById('edit-next-calibration');

    const showModal = (modal) => { if (modal && typeof modal.showModal === 'function') modal.showModal(); };
    const closeModal = (modal) => { if (modal && typeof modal.close === 'function') modal.close(); };

    const showFeedback = (message, type = 'success') => {
        if (!feedbackEl) return;
        feedbackEl.textContent = message;
        feedbackEl.className = `alert ${type === 'success' ? 'alert-success' : 'alert-error'}`;
        feedbackEl.style.display = 'block';

        if (type === 'success') {
            setTimeout(() => {
                feedbackEl.style.display = 'none';
            }, 3000);
        }
    };

    const updateCsrfTokens = (tokenName, tokenHash) => {
        if (!tokenName || !tokenHash) return;
        document.querySelectorAll(`input[name="${tokenName}"]`).forEach((input) => {
            input.value = tokenHash;
        });
    };

    const renderStatusBadge = (status) => `<span class="badge badge-status-${status}">${status.replace(/_/g, ' ').replace(/\b\w/g, c => c.toUpperCase())}</span>`;
    const renderConditionBadge = (condition) => `<span class="badge badge-condition-${condition}">${condition.replace(/_/g, ' ').replace(/\b\w/g, c => c.toUpperCase())}</span>`;

    const createRow = (record) => {
        if (!equipmentTable || !record) return null;
        const tbody = equipmentTable.querySelector('tbody');
        if (!tbody) return null;

        const row = document.createElement('tr');
        row.dataset.equipment = JSON.stringify(record);
        row.innerHTML = `
            <td>#${record.id}</td>
            <td>${record.equipment_name}</td>
            <td>${record.equipment_type || '—'}</td>
            <td>${record.serial_number || '—'}</td>
            <td>${renderStatusBadge(record.status)}</td>
            <td>${renderConditionBadge(record.condition)}</td>
            <td>
                <div>Last: ${formatDate(record.last_maintenance_date)}</div>
                <div>Next: ${formatDate(record.next_maintenance_date)}</div>
            </td>
            <td>
                <div>Last: ${formatDate(record.last_calibration_date)}</div>
                <div>Next: ${formatDate(record.next_calibration_date)}</div>
            </td>
            <td>${(record.usage_hours ?? 0).toFixed(1)} hrs</td>
            <td>${record.updated_at_formatted || '—'}</td>
            <td><button class="btn btn-outline btn-sm" data-edit-equipment>Edit</button></td>
        `;

        tbody.prepend(row);
        return row;
    };

    const updateRow = (row, record) => {
        if (!row || !record) return;
        row.dataset.equipment = JSON.stringify(record);
        row.cells[1].textContent = record.equipment_name;
        row.cells[2].textContent = record.equipment_type || '—';
        row.cells[3].textContent = record.serial_number || '—';
        row.cells[4].innerHTML = renderStatusBadge(record.status);
        row.cells[5].innerHTML = renderConditionBadge(record.condition);
        row.cells[6].innerHTML = `<div>Last: ${formatDate(record.last_maintenance_date)}</div><div>Next: ${formatDate(record.next_maintenance_date)}</div>`;
        row.cells[7].innerHTML = `<div>Last: ${formatDate(record.last_calibration_date)}</div><div>Next: ${formatDate(record.next_calibration_date)}</div>`;
        row.cells[8].textContent = `${(record.usage_hours ?? 0).toFixed(1)} hrs`;
        row.cells[9].textContent = record.updated_at_formatted || '—';
    };

    const formatDate = (value) => {
        if (!value) return '—';
        const date = new Date(value);
        if (Number.isNaN(date.getTime())) return '—';
        return date.toLocaleDateString(undefined, { month: 'short', day: 'numeric', year: 'numeric' });
    };

    const bindEditButtons = () => {
        document.querySelectorAll('[data-edit-equipment]').forEach((button) => {
            button.removeEventListener('click', handleEditClick);
            button.addEventListener('click', handleEditClick);
        });
    };

    const handleEditClick = (event) => {
        const row = event.currentTarget.closest('tr');
        if (!row) return;
        const record = JSON.parse(row.dataset.equipment || '{}');

        editForm.dataset.rowIndex = Array.from(row.parentElement.children).indexOf(row);
        editForm.dataset.recordId = record.id;
        editIdField.value = record.id;
        editNameField.value = record.equipment_name || '';
        editStatusField.value = record.status || 'available';
        editConditionField.value = record.condition || 'good';
        editUsageField.value = record.usage_hours ?? '';
        editLastMaintField.value = record.last_maintenance_date || '';
        editNextMaintField.value = record.next_maintenance_date || '';
        editLastCalField.value = record.last_calibration_date || '';
        editNextCalField.value = record.next_calibration_date || '';
        editForm.action = `<?= base_url('super-admin/equipment/update') ?>/` + record.id;

        showModal(editModal);
    };

    openCreateBtn?.addEventListener('click', () => showModal(createModal));

    document.querySelectorAll('[data-close-modal]').forEach((button) => {
        button.addEventListener('click', (event) => {
            closeModal(event.currentTarget.closest('dialog'));
        });
    });

    createForm?.addEventListener('submit', async (event) => {
        event.preventDefault();
        const formData = new FormData(createForm);
        const submitButton = createForm.querySelector('button[type="submit"]');
        if (submitButton) {
            submitButton.disabled = true;
            submitButton.textContent = 'Saving...';
        }

        try {
            const response = await fetch(createForm.action, {
                method: 'POST',
                headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' },
                body: formData,
            });
            const result = await response.json();

            updateCsrfTokens(result?.csrfToken, result?.csrfHash);

            if (!response.ok || !result.success) {
                showFeedback(result?.error || 'Failed to add equipment.', 'error');
                return;
            }

            const record = result.record || {};
            const row = createRow(record);
            if (row) bindEditButtons();

            showFeedback(result.message || 'Equipment added successfully.', 'success');
            createForm.reset();
            closeModal(createModal);
        } catch (error) {
            showFeedback('Unexpected error adding equipment.', 'error');
        } finally {
            if (submitButton) {
                submitButton.disabled = false;
                submitButton.textContent = 'Save Equipment';
            }
        }
    });

    editForm?.addEventListener('submit', async (event) => {
        event.preventDefault();
        const formData = new FormData(editForm);
        const submitButton = editForm.querySelector('button[type="submit"]');
        if (submitButton) {
            submitButton.disabled = true;
            submitButton.textContent = 'Updating...';
        }

        try {
            const response = await fetch(editForm.action, {
                method: 'POST',
                headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' },
                body: formData,
            });
            const result = await response.json();

            updateCsrfTokens(result?.csrfToken, result?.csrfHash);

            if (!response.ok || !result.success) {
                showFeedback(result?.error || 'Failed to update equipment.', 'error');
                return;
            }

            const record = result.record || {};
            const row = Array.from(equipmentTable?.querySelectorAll('tbody tr') || []).find((tr) => {
                const data = tr.dataset.equipment ? JSON.parse(tr.dataset.equipment) : null;
                return data && data.id === record.id;
            });
            updateRow(row, record);

            showFeedback(result.message || 'Equipment updated successfully.', 'success');
            closeModal(editModal);
        } catch (error) {
            showFeedback('Unexpected error updating equipment.', 'error');
        } finally {
            if (submitButton) {
                submitButton.disabled = false;
                submitButton.textContent = 'Update Equipment';
            }
        }
    });

    bindEditButtons();
})();
</script>

<?= $this->endSection() ?>
