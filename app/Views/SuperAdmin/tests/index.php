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
        margin: 6px 0 0 0;
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
        box-shadow: 0 6px 18px rgba(15, 23, 42, 0.08);
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
    thead {
        background: #f8fafc;
    }
    th, td {
        padding: 14px 16px;
        border-bottom: 1px solid #e5e7eb;
        text-align: left;
        font-size: 14px;
    }
    tbody tr:last-child td {
        border-bottom: none;
    }
    .badge {
        display: inline-flex;
        align-items: center;
        padding: 4px 10px;
        border-radius: 999px;
        font-size: 12px;
        font-weight: 600;
    }
    .badge-status-pending { background: #fef3c7; color: #92400e; }
    .badge-status-in_progress { background: #dbeafe; color: #1d4ed8; }
    .badge-status-completed { background: #dcfce7; color: #166534; }
    .badge-quality { background: #eef2ff; color: #3730a3; }
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
        max-width: 640px;
        width: 90%;
        box-shadow: 0 24px 48px rgba(15,23,42,0.25);
    }
    dialog::backdrop {
        background: rgba(15,23,42,0.5);
    }
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
        <h1 class="page-title">🧪 Test Management</h1>
        <p class="page-subtitle">Oversee laboratory test requests, samples, and results.</p>
    </div>
    <div class="header-actions">
        <a href="<?= base_url('super-admin/laboratory') ?>" class="btn btn-outline">← Back to Laboratory</a>
        <a href="<?= base_url('super-admin/unified') ?>" class="btn btn-secondary">Back to Dashboard</a>
        <button class="btn btn-primary" id="open-create-modal">＋ New Test</button>
    </div>
</div>

<div id="tests-feedback" class="alert"></div>

<div class="filters-card">
    <form class="filters-form" method="get" action="">
        <label>
            Search
            <input type="search" name="search" value="<?= esc($filters['search'] ?? '') ?>" placeholder="Patient, test, sample...">
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
            Quality Check
            <select name="quality">
                <option value="">All</option>
                <?php foreach ($qualityOptions as $option): ?>
                    <option value="<?= esc($option) ?>" <?= ($filters['quality_check'] ?? '') === $option ? 'selected' : '' ?>><?= esc($option) ?></option>
                <?php endforeach; ?>
            </select>
        </label>
        <button type="submit" class="btn btn-outline">Apply Filters</button>
        <a href="<?= base_url('super-admin/tests') ?>" class="btn btn-outline">Reset</a>
    </form>
</div>

<?php if (empty($tests)): ?>
    <div class="empty-state">
        <h3>No test records yet</h3>
        <p>Start tracking laboratory tests by adding a new request.</p>
    </div>
<?php else: ?>
    <div class="table-wrapper">
        <table id="tests-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Patient</th>
                    <th>Test Name</th>
                    <th>Test Type</th>
                    <th>Sample ID</th>
                    <th>Status</th>
                    <th>Quality</th>
                    <th>Requested By</th>
                    <th>Created</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($tests as $test): ?>
                    <?php
                        $patientName = trim(($test['first_name'] ?? '') . ' ' . ($test['last_name'] ?? ''));
                        if ($patientName === '') {
                            $patientName = 'Unknown Patient';
                        }
                        $formatted = [
                            'id' => $test['id'],
                            'patient_id' => $test['patient_id'],
                            'patient_name' => $patientName,
                            'test_name' => $test['test_name'],
                            'test_type' => $test['test_type'],
                            'sample_id' => $test['sample_id'],
                            'requested_by' => $test['requested_by'],
                            'status' => $test['status'],
                            'quality_check' => $test['quality_check'],
                            'result' => $test['result'],
                            'created_at' => $test['created_at'],
                            'updated_at' => $test['updated_at'],
                        ];
                    ?>
                    <tr data-test='<?= esc(json_encode($formatted), 'attr') ?>'>
                        <td>#<?= esc($test['id']) ?></td>
                        <td><?= esc($patientName) ?></td>
                        <td><?= esc($test['test_name']) ?></td>
                        <td><?= esc($test['test_type'] ?? '—') ?></td>
                        <td><?= esc($test['sample_id'] ?? '—') ?></td>
                        <td>
                            <span class="badge badge-status-<?= esc($test['status']) ?>">
                                <?= esc($statusOptions[$test['status']] ?? ucfirst($test['status'])) ?>
                            </span>
                        </td>
                        <td>
                            <span class="badge badge-quality">
                                <?= esc($test['quality_check'] ?? 'Not Checked') ?>
                            </span>
                        </td>
                        <td><?= esc($test['requested_by'] ?? '—') ?></td>
                        <td><?= !empty($test['created_at']) ? esc(date('M d, Y h:i A', strtotime($test['created_at']))) : '—' ?></td>
                        <td>
                            <button class="btn btn-outline btn-sm" data-edit-test>Edit</button>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
<?php endif; ?>

<dialog id="create-test-modal">
    <div class="modal-header">
        <h3>＋ New Laboratory Test</h3>
        <button type="button" class="btn btn-outline" data-close-modal>Close</button>
    </div>
    <form id="create-test-form" method="post" action="<?= base_url('super-admin/tests/add') ?>">
        <?= csrf_field() ?>
        <div class="modal-body">
            <div class="form-grid">
                <div class="form-field">
                    <label>Patient<span style="color:#ef4444;">*</span></label>
                    <select name="patient_id" required>
                        <option value="">Select patient</option>
                        <?php foreach ($patients as $patient): ?>
                            <?php $name = trim($patient['last_name'] . ', ' . $patient['first_name']); ?>
                            <option value="<?= esc($patient['id']) ?>"><?= esc($name) ?> (<?= esc($patient['patient_id'] ?? 'ID') ?>)</option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-field">
                    <label>Test Name<span style="color:#ef4444;">*</span></label>
                    <input type="text" name="test_name" required placeholder="e.g. Complete Blood Count">
                </div>
                <div class="form-field">
                    <label>Test Type</label>
                    <input type="text" name="test_type" placeholder="e.g. Hematology">
                </div>
                <div class="form-field">
                    <label>Sample ID</label>
                    <input type="text" name="sample_id" placeholder="e.g. SAMPLE-2025-001">
                </div>
                <div class="form-field">
                    <label>Requested By</label>
                    <input type="text" name="requested_by" placeholder="Doctor or staff name">
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
                    <label>Quality Check</label>
                    <select name="quality_check">
                        <?php foreach ($qualityOptions as $option): ?>
                            <option value="<?= esc($option) ?>"><?= esc($option) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
            <div class="form-field">
                <label>Result Notes</label>
                <textarea name="result" placeholder="Record findings or observations"></textarea>
            </div>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-outline" data-close-modal>Cancel</button>
            <button type="submit" class="btn btn-primary">Save Test</button>
        </div>
    </form>
</dialog>

<dialog id="edit-test-modal">
    <div class="modal-header">
        <h3>Update Test Result</h3>
        <button type="button" class="btn btn-outline" data-close-modal>Close</button>
    </div>
    <form id="edit-test-form" method="post">
        <?= csrf_field() ?>
        <div class="modal-body">
            <input type="hidden" name="test_id" id="edit-test-id">
            <div class="form-grid">
                <div class="form-field">
                    <label>Patient</label>
                    <input type="text" id="edit-patient" disabled>
                </div>
                <div class="form-field">
                    <label>Test Name</label>
                    <input type="text" id="edit-test-name" disabled>
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
                    <label>Quality Check</label>
                    <select name="quality_check" id="edit-quality">
                        <?php foreach ($qualityOptions as $option): ?>
                            <option value="<?= esc($option) ?>"><?= esc($option) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
            <div class="form-field">
                <label>Result Notes</label>
                <textarea name="result" id="edit-result" placeholder="Enter findings"></textarea>
            </div>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-outline" data-close-modal>Cancel</button>
            <button type="submit" class="btn btn-primary">Update Test</button>
        </div>
    </form>
</dialog>

<script>
(function() {
    const createModal = document.getElementById('create-test-modal');
    const editModal = document.getElementById('edit-test-modal');
    const openCreateBtn = document.getElementById('open-create-modal');
    const feedbackEl = document.getElementById('tests-feedback');
    const testsTable = document.getElementById('tests-table');

    const createForm = document.getElementById('create-test-form');
    const editForm = document.getElementById('edit-test-form');

    const editIdField = document.getElementById('edit-test-id');
    const editPatientField = document.getElementById('edit-patient');
    const editTestNameField = document.getElementById('edit-test-name');
    const editStatusField = document.getElementById('edit-status');
    const editQualityField = document.getElementById('edit-quality');
    const editResultField = document.getElementById('edit-result');

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

    const renderBadge = (status) => {
        return `<span class="badge badge-status-${status}">${status.replace('_', ' ').replace(/\b\w/g, c => c.toUpperCase())}</span>`;
    };

    const renderQualityBadge = (quality) => {
        return `<span class="badge badge-quality">${quality || 'Not Checked'}</span>`;
    };

    const createRow = (formatted) => {
        if (!testsTable) return null;
        const tbody = testsTable.querySelector('tbody');
        if (!tbody) return null;

        const row = document.createElement('tr');
        row.dataset.test = JSON.stringify(formatted);
        row.innerHTML = `
            <td>#${formatted.id}</td>
            <td>${formatted.patient_name}</td>
            <td>${formatted.test_name}</td>
            <td>${formatted.test_type || '—'}</td>
            <td>${formatted.sample_id || '—'}</td>
            <td>${renderBadge(formatted.status)}</td>
            <td>${renderQualityBadge(formatted.quality_check)}</td>
            <td>${formatted.requested_by || '—'}</td>
            <td>${formatted.created_at_formatted || '—'}</td>
            <td><button class="btn btn-outline btn-sm" data-edit-test>Edit</button></td>
        `;

        tbody.prepend(row);
        return row;
    };

    const updateRow = (row, formatted) => {
        if (!row) return;
        row.dataset.test = JSON.stringify(formatted);
        row.cells[1].textContent = formatted.patient_name;
        row.cells[2].textContent = formatted.test_name;
        row.cells[3].textContent = formatted.test_type || '—';
        row.cells[4].textContent = formatted.sample_id || '—';
        row.cells[5].innerHTML = renderBadge(formatted.status);
        row.cells[6].innerHTML = renderQualityBadge(formatted.quality_check);
        row.cells[7].textContent = formatted.requested_by || '—';
        row.cells[8].textContent = formatted.updated_at_formatted || formatted.created_at_formatted || '—';
    };

    const bindEditButtons = () => {
        document.querySelectorAll('[data-edit-test]').forEach((button) => {
            button.removeEventListener('click', handleEditClick);
            button.addEventListener('click', handleEditClick);
        });
    };

    const handleEditClick = (event) => {
        const row = event.currentTarget.closest('tr');
        if (!row) return;
        const test = JSON.parse(row.dataset.test || '{}');

        editForm.dataset.rowIndex = Array.from(row.parentElement.children).indexOf(row);
        editForm.dataset.rowHtml = '';
        editForm.dataset.testId = test.id;
        editIdField.value = test.id;
        editPatientField.value = test.patient_name || '';
        editTestNameField.value = test.test_name || '';
        editStatusField.value = test.status || 'pending';
        editQualityField.value = test.quality_check || 'Not Checked';
        editResultField.value = test.result || '';
        editForm.action = `<?= base_url('super-admin/tests/update') ?>/` + test.id;

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
                showFeedback(result?.error || 'Failed to create test.', 'error');
                return;
            }

            const formatted = result.formattedTest || {};
            const row = createRow(formatted);
            if (row) bindEditButtons();

            showFeedback(result.message || 'Test created successfully.', 'success');
            createForm.reset();
            closeModal(createModal);
        } catch (error) {
            showFeedback('Unexpected error creating test.', 'error');
        } finally {
            if (submitButton) {
                submitButton.disabled = false;
                submitButton.textContent = 'Save Test';
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
                showFeedback(result?.error || 'Failed to update test.', 'error');
                return;
            }

            const formatted = result.formattedTest || {};
            const row = Array.from(testsTable?.querySelectorAll('tbody tr') || []).find((tr) => {
                const data = tr.dataset.test ? JSON.parse(tr.dataset.test) : null;
                return data && data.id === formatted.id;
            });
            updateRow(row, formatted);

            showFeedback(result.message || 'Test updated successfully.', 'success');
            closeModal(editModal);
        } catch (error) {
            showFeedback('Unexpected error updating test.', 'error');
        } finally {
            if (submitButton) {
                submitButton.disabled = false;
                submitButton.textContent = 'Update Test';
            }
        }
    });

    bindEditButtons();
})();
</script>

<?= $this->endSection() ?>
