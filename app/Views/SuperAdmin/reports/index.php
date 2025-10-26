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
    .report-actions {
        display: flex;
        flex-wrap: wrap;
        gap: 16px;
    }
    .card {
        background: #ffffff;
        border-radius: 12px;
        border: 1px solid #e5e7eb;
        padding: 20px;
        box-shadow: 0 4px 16px rgba(15, 23, 42, 0.08);
    }
    .card h3 {
        margin-top: 0;
        margin-bottom: 16px;
        font-size: 18px;
        color: #1f2937;
    }
    .filter-form,
    .generate-form {
        display: flex;
        flex-wrap: wrap;
        gap: 12px;
        align-items: center;
    }
    .filter-form input,
    .filter-form select,
    .generate-form input,
    .generate-form select {
        padding: 10px 12px;
        border-radius: 8px;
        border: 1px solid #d1d5db;
        font-size: 14px;
    }
    .btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        padding: 10px 18px;
        border-radius: 8px;
        border: none;
        cursor: pointer;
        font-size: 14px;
        font-weight: 600;
        transition: background 0.2s ease;
    }
    .btn-primary { background: #2563eb; color: #fff; }
    .btn-primary:hover { background: #1d4ed8; }
    .btn-secondary {
        background: #4b5563;
        color: #ffffff;
    }
    .btn-secondary:hover {
        background: #374151;
    }
    .btn-outline {
        background: transparent;
        color: #2563eb;
        border: 1px solid #2563eb;
    }
    .btn-outline:hover { background: rgba(37, 99, 235, 0.08); }
    .alert {
        padding: 14px 18px;
        border-radius: 10px;
        margin-bottom: 16px;
        border: 1px solid transparent;
        display: flex;
        align-items: center;
        gap: 10px;
        font-size: 14px;
    }
    .alert-success { background: #ecfdf5; border-color: #34d399; color: #047857; }
    .alert-error { background: #fef2f2; border-color: #f87171; color: #b91c1c; }
    .table-wrapper { margin-top: 24px; }
    table {
        width: 100%;
        border-collapse: collapse;
        background: white;
        border-radius: 12px;
        overflow: hidden;
        border: 1px solid #e5e7eb;
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
        padding: 4px 8px;
        border-radius: 999px;
        font-size: 12px;
        font-weight: 600;
        text-transform: capitalize;
    }
    .badge-dispensing { background: #dbeafe; color: #1d4ed8; }
    .badge-usage { background: #dcfce7; color: #166534; }
    .badge-inventory { background: #fef3c7; color: #92400e; }
    .badge-compliance { background: #fce7f3; color: #be185d; }
    .empty-state {
        text-align: center;
        padding: 60px 20px;
        border: 1px dashed #d1d5db;
        border-radius: 12px;
        color: #6b7280;
        margin-top: 24px;
        background: #ffffff;
    }
    dialog {
        border: none;
        padding: 0;
        border-radius: 12px;
        max-width: 640px;
        width: 90%;
        box-shadow: 0 24px 48px rgba(15, 23, 42, 0.25);
    }
    dialog::backdrop {
        background: rgba(15, 23, 42, 0.55);
    }
    .modal-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 18px 24px;
        border-bottom: 1px solid #e5e7eb;
    }
    .modal-body {
        padding: 24px;
        max-height: 65vh;
        overflow-y: auto;
    }
    .modal-footer {
        display: flex;
        justify-content: flex-end;
        padding: 16px 24px;
        border-top: 1px solid #e5e7eb;
    }
    pre.report-json {
        background: #f9fafb;
        border: 1px solid #e5e7eb;
        border-radius: 8px;
        padding: 16px;
        font-size: 13px;
        white-space: pre-wrap;
        word-break: break-word;
    }
</style>

<div class="page-header">
    <h1 class="page-title">📊 Reports & Analytics</h1>
    <div class="report-actions">
        <a href="<?= base_url('super-admin/unified') ?>" class="btn btn-secondary">← Back to Dashboard</a>
        <form class="filter-form" method="get" action="">
            <select name="type">
                <option value="">All Types</option>
                <?php foreach ($reportTypes as $key => $label): ?>
                    <option value="<?= esc($key) ?>" <?= ($filters['type'] ?? '') === $key ? 'selected' : '' ?>>
                        <?= esc($label) ?>
                    </option>
                <?php endforeach; ?>
            </select>
            <input type="date" name="from" value="<?= esc($filters['from'] ?? '') ?>">
            <input type="date" name="to" value="<?= esc($filters['to'] ?? '') ?>">
            <button type="submit" class="btn btn-outline">Apply Filters</button>
            <a href="<?= base_url('super-admin/reports') ?>" class="btn btn-outline">Reset</a>
        </form>
    </div>
</div>

<?php if (session()->getFlashdata('message')): ?>
    <div class="alert alert-success">✅ <?= esc(session()->getFlashdata('message')) ?></div>
<?php endif; ?>

<?php if (session()->getFlashdata('error')): ?>
    <div class="alert alert-error">⚠️ <?= esc(session()->getFlashdata('error')) ?></div>
<?php endif; ?>

<div class="card" style="margin-bottom: 24px;">
    <h3>Generate New Report</h3>
    <form class="generate-form" method="post" action="<?= base_url('super-admin/reports/generate') ?>" id="generate-report-form">
        <?= csrf_field() ?>
        <select name="report_type" required>
            <?php foreach ($reportTypes as $key => $label): ?>
                <option value="<?= esc($key) ?>"><?= esc($label) ?></option>
            <?php endforeach; ?>
        </select>
        <label>From
            <input type="date" name="from_date">
        </label>
        <label>To
            <input type="date" name="to_date">
        </label>
        <button type="submit" class="btn btn-primary">Generate Report</button>
    </form>
</div>

<?php if (empty($reports)): ?>
    <div class="empty-state">
        <h3>No reports yet</h3>
        <p>Generate a report using the form above to populate analytics insights.</p>
    </div>
<?php else: ?>
    <div class="table-wrapper">
        <table id="reports-table">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Title</th>
                    <th>Type</th>
                    <th>Generated By</th>
                    <th>Created At</th>
                    <th>Summary</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($reports as $report): ?>
                    <tr data-report='<?= esc(json_encode($report), 'attr') ?>'>
                        <td>#<?= esc($report['id']) ?></td>
                        <td><?= esc($report['title']) ?></td>
                        <td>
                            <span class="badge badge-<?= esc($report['report_type']) ?>">
                                <?= esc($reportTypes[$report['report_type']] ?? ucfirst($report['report_type'])) ?>
                            </span>
                        </td>
                        <td><?= esc($report['generated_by'] ?? 'System') ?></td>
                        <td><?= esc(date('M d, Y h:i A', strtotime($report['created_at']))) ?></td>
                        <td><?= esc($report['summary'] ?? '—') ?></td>
                        <td>
                            <button type="button" class="btn btn-outline" data-view-report>View</button>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
<?php endif; ?>

<dialog id="report-modal">
    <div class="modal-header">
        <h3 id="report-modal-title">Report</h3>
        <button type="button" class="btn btn-outline" id="close-report-modal">Close</button>
    </div>
    <div class="modal-body">
        <p><strong>Type:</strong> <span id="report-modal-type"></span></p>
        <p><strong>Generated By:</strong> <span id="report-modal-author"></span></p>
        <p><strong>Created At:</strong> <span id="report-modal-created"></span></p>
        <p><strong>Filters:</strong> <span id="report-modal-filters"></span></p>
        <p><strong>Summary:</strong></p>
        <p id="report-modal-summary"></p>
        <p><strong>Report Data:</strong></p>
        <pre class="report-json" id="report-modal-data">{}</pre>
    </div>
    <div class="modal-footer">
        <button type="button" class="btn btn-primary" id="download-report">Download JSON</button>
    </div>
</dialog>

<script>
(function() {
    const modal = document.getElementById('report-modal');
    if (!modal) return;

    const titleEl = document.getElementById('report-modal-title');
    const typeEl = document.getElementById('report-modal-type');
    const authorEl = document.getElementById('report-modal-author');
    const createdEl = document.getElementById('report-modal-created');
    const filtersEl = document.getElementById('report-modal-filters');
    const summaryEl = document.getElementById('report-modal-summary');
    const dataEl = document.getElementById('report-modal-data');
    const downloadBtn = document.getElementById('download-report');
    const feedbackEl = document.querySelector('.alert-success, .alert-error');
    const generateForm = document.getElementById('generate-report-form');
    const reportsTable = document.getElementById('reports-table');
    const emptyState = document.querySelector('.empty-state');

    let currentReport = null;

    const createTableRow = (report, typeLabel) => {
        if (!reportsTable) return null;

        const tbody = reportsTable.querySelector('tbody') || reportsTable.createTBody();
        const row = document.createElement('tr');
        row.dataset.report = JSON.stringify(report);

        row.innerHTML = `
            <td>#${report.id}</td>
            <td>${report.title ?? ''}</td>
            <td><span class="badge badge-${report.report_type}">${typeLabel}</span></td>
            <td>${report.generated_by ?? 'System'}</td>
            <td>${report.created_at ? new Date(report.created_at).toLocaleString() : '—'}</td>
            <td>${report.summary ?? '—'}</td>
            <td><button type="button" class="btn btn-outline" data-view-report>View</button></td>
        `;

        tbody.prepend(row);
        return row;
    };

    const showInlineFeedback = (message, type = 'success') => {
        let alertEl = document.querySelector('#reports-feedback');
        if (!alertEl) {
            alertEl = document.createElement('div');
            alertEl.id = 'reports-feedback';
            alertEl.className = 'alert';
            const card = document.querySelector('.card');
            if (card) {
                card.insertAdjacentElement('beforebegin', alertEl);
            }
        }

        alertEl.className = `alert ${type === 'success' ? 'alert-success' : 'alert-error'}`;
        alertEl.textContent = message;

        if (type === 'success') {
            setTimeout(() => {
                if (alertEl) alertEl.remove();
            }, 3000);
        }
    };

    const refreshViewButtons = () => {
        document.querySelectorAll('[data-view-report]').forEach((button) => {
            button.removeEventListener('click', handleViewReport);
            button.addEventListener('click', handleViewReport);
        });
    };

    const handleViewReport = (event) => {
        const button = event.currentTarget;
        const row = button.closest('tr');
        if (!row) return;

        const report = JSON.parse(row.dataset.report || '{}');
        currentReport = report;

        titleEl.textContent = report.title || 'Report';
        typeEl.textContent = report.report_type || '—';
        authorEl.textContent = report.generated_by || 'System';
        createdEl.textContent = report.created_at ? new Date(report.created_at).toLocaleString() : '—';
        summaryEl.textContent = report.summary || '—';

        try {
            const filterText = report.filters ? JSON.stringify(report.filters, null, 2) : 'None';
            filtersEl.textContent = filterText;
        } catch (error) {
            filtersEl.textContent = 'None';
        }

        try {
            const reportData = report.report_data ? JSON.stringify(report.report_data, null, 2) : '{}';
            dataEl.textContent = reportData;
        } catch (error) {
            dataEl.textContent = '{}';
        }

        modal.showModal();
    };

    document.querySelectorAll('[data-view-report]').forEach((button) => {
        button.addEventListener('click', handleViewReport);
    });

    document.getElementById('close-report-modal')?.addEventListener('click', () => {
        modal.close();
    });

    downloadBtn?.addEventListener('click', () => {
        if (!currentReport) return;
        const blob = new Blob([JSON.stringify(currentReport, null, 2)], { type: 'application/json' });
        const url = URL.createObjectURL(blob);
        const link = document.createElement('a');
        link.href = url;
        link.download = `${(currentReport.title || 'report').replace(/\s+/g, '_').toLowerCase()}.json`;
        link.click();
        URL.revokeObjectURL(url);
    });

    generateForm?.addEventListener('submit', async (event) => {
        event.preventDefault();

        const submitButton = generateForm.querySelector('button[type="submit"]');
        const formData = new FormData(generateForm);

        if (submitButton) {
            submitButton.disabled = true;
            submitButton.textContent = 'Generating...';
        }

        try {
            const response = await fetch(generateForm.action, {
                method: 'POST',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json',
                },
                body: formData,
            });

            const result = await response.json();

            if (result?.csrfToken && result?.csrfHash) {
                const csrfField = generateForm.querySelector('input[name="' + result.csrfToken + '"]');
                if (csrfField) {
                    csrfField.value = result.csrfHash;
                }
            }

            if (!response.ok || !result.success) {
                showInlineFeedback(result?.error || 'Failed to generate report.', 'error');
                return;
            }

            if (emptyState) {
                emptyState.remove();
            }

            const row = createTableRow(result.report, result.reportTypeLabel || 'Report');
            if (row) {
                refreshViewButtons();
            }

            showInlineFeedback(result.message || 'Report generated successfully.', 'success');
        } catch (error) {
            showInlineFeedback('Unexpected error generating report.', 'error');
        } finally {
            if (submitButton) {
                submitButton.disabled = false;
                submitButton.textContent = 'Generate Report';
            }
        }
    });
})();
</script>

<?= $this->endSection() ?>
