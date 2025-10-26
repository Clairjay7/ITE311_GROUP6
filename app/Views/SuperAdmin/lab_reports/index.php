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
    .btn-sm { padding: 8px 12px; border-radius: 8px; font-size: 13px; }
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
    .generate-card {
        background: linear-gradient(120deg, #1e40af 0%, #3b82f6 100%);
        color: white;
        border-radius: 16px;
        padding: 24px;
        display: flex;
        flex-wrap: wrap;
        gap: 24px;
        align-items: center;
        margin-bottom: 24px;
    }
    .generate-card h3 {
        margin: 0 0 8px;
        font-size: 22px;
    }
    .generate-card form {
        background: rgba(255,255,255,0.1);
        padding: 16px;
        border-radius: 12px;
        display: flex;
        gap: 12px;
        flex-wrap: wrap;
    }
    .generate-card label {
        font-size: 12px;
        display: flex;
        flex-direction: column;
        gap: 6px;
    }
    .generate-card input,
    .generate-card select {
        padding: 10px 12px;
        border-radius: 10px;
        border: none;
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
        background: #eef2ff;
        color: #3730a3;
    }
    .empty-state {
        padding: 50px 20px;
        text-align: center;
        color: #6b7280;
        border: 2px dashed #d1d5db;
        border-radius: 12px;
        background: #ffffff;
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
    .report-meta {
        display: flex;
        flex-direction: column;
        gap: 4px;
    }
</style>

<div class="page-header">
    <div>
        <h1 class="page-title">📊 Lab Reports & Analytics</h1>
        <p class="page-subtitle">Generate insights from laboratory tests, quality metrics, and equipment data.</p>
    </div>
    <div class="header-actions">
        <a href="<?= base_url('super-admin/laboratory') ?>" class="btn btn-outline">← Back to Laboratory</a>
        <a href="<?= base_url('super-admin/unified') ?>" class="btn btn-secondary">Back to Dashboard</a>
    </div>
</div>

<div id="lab-reports-feedback" class="alert"></div>

<div class="generate-card">
    <div>
        <h3>Generate New Report</h3>
        <p style="margin: 0; opacity: 0.9;">Select a report type and optional date range to produce fresh analytics.</p>
    </div>
    <form id="generate-report-form" method="post" action="<?= base_url('super-admin/lab-reports/generate') ?>">
        <?= csrf_field() ?>
        <label>
            Report Type
            <select name="report_type" required>
                <option value="">Select report type</option>
                <?php foreach ($reportTypes as $key => $label): ?>
                    <option value="<?= esc($key) ?>"><?= esc($label) ?></option>
                <?php endforeach; ?>
            </select>
        </label>
        <label>
            Period Start
            <input type="date" name="period_start">
        </label>
        <label>
            Period End
            <input type="date" name="period_end">
        </label>
        <button type="submit" class="btn btn-secondary">Generate Report</button>
    </form>
</div>

<div class="filters-card">
    <form class="filters-form" method="get" action="">
        <label>
            Search
            <input type="search" name="search" value="<?= esc($filters['search'] ?? '') ?>" placeholder="Type, description, or author">
        </label>
        <label>
            Report Type
            <select name="type">
                <option value="">All</option>
                <?php foreach ($reportTypes as $key => $label): ?>
                    <option value="<?= esc($key) ?>" <?= ($filters['type'] ?? '') === $key ? 'selected' : '' ?>><?= esc($label) ?></option>
                <?php endforeach; ?>
            </select>
        </label>
        <label>
            Period Start
            <input type="date" name="period_start" value="<?= esc($filters['period_start'] ?? '') ?>">
        </label>
        <label>
            Period End
            <input type="date" name="period_end" value="<?= esc($filters['period_end'] ?? '') ?>">
        </label>
        <button type="submit" class="btn btn-outline">Apply Filters</button>
        <a href="<?= base_url('super-admin/lab-reports') ?>" class="btn btn-outline">Reset</a>
    </form>
</div>

<?php if (empty($reports)): ?>
    <div class="empty-state">
        <h3>No reports generated yet</h3>
        <p>Use the generator above to create your first laboratory analytics report.</p>
    </div>
<?php else: ?>
    <div class="table-wrapper">
        <table id="lab-reports-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Report Type</th>
                    <th>Description</th>
                    <th>Period</th>
                    <th>Generated By</th>
                    <th>Created</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($reports as $report): ?>
                    <tr data-report='<?= esc(json_encode($report), 'attr') ?>'>
                        <td>#<?= esc($report['id']) ?></td>
                        <td><span class="badge"><?= esc($report['report_type_label']) ?></span></td>
                        <td><?= esc($report['description'] ?? '—') ?></td>
                        <td><?= esc($report['period_label']) ?></td>
                        <td><?= esc($report['generated_by_label']) ?></td>
                        <td><?= esc($report['created_at_formatted']) ?></td>
                        <td>
                            <button class="btn btn-outline btn-sm" data-view-report>View</button>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
<?php endif; ?>

<dialog id="report-detail-modal">
    <div class="modal-header">
        <h3 id="report-detail-title">Report Details</h3>
        <button type="button" class="btn btn-outline" data-close-modal>Close</button>
    </div>
    <div class="modal-body" style="padding: 24px; display: grid; gap: 16px;">
        <div class="report-meta">
            <strong>Report Type:</strong>
            <span id="detail-type"></span>
        </div>
        <div class="report-meta">
            <strong>Description:</strong>
            <span id="detail-description"></span>
        </div>
        <div class="report-meta">
            <strong>Period Covered:</strong>
            <span id="detail-period"></span>
        </div>
        <div class="report-meta">
            <strong>Generated By:</strong>
            <span id="detail-author"></span>
        </div>
        <div class="report-meta">
            <strong>Generated At:</strong>
            <span id="detail-created"></span>
        </div>
        <div class="report-meta">
            <strong>Insights:</strong>
            <pre id="detail-data" style="background:#f9fafb; border-radius: 12px; padding: 16px; margin:0; max-height: 320px; overflow:auto; font-size: 13px;"></pre>
        </div>
    </div>
    <div class="modal-footer">
        <button type="button" class="btn btn-outline" data-close-modal>Close</button>
    </div>
</dialog>

<script>
(function() {
    const feedbackEl = document.getElementById('lab-reports-feedback');
    const generateForm = document.getElementById('generate-report-form');
    const reportsTable = document.getElementById('lab-reports-table');
    const detailModal = document.getElementById('report-detail-modal');

    const detailTitle = document.getElementById('report-detail-title');
    const detailType = document.getElementById('detail-type');
    const detailDescription = document.getElementById('detail-description');
    const detailPeriod = document.getElementById('detail-period');
    const detailAuthor = document.getElementById('detail-author');
    const detailCreated = document.getElementById('detail-created');
    const detailData = document.getElementById('detail-data');

    const showModal = (modal) => { if (modal && typeof modal.showModal === 'function') modal.showModal(); };
    const closeModal = (modal) => { if (modal && typeof modal.close === 'function') modal.close(); };

    const showFeedback = (message, type = 'success') => {
        if (!feedbackEl) return;
        feedbackEl.textContent = message;
        feedbackEl.className = `alert ${type === 'success' ? 'alert-success' : 'alert-error'}`;
        feedbackEl.style.display = 'block';

        if (type === 'success') {
            setTimeout(() => { feedbackEl.style.display = 'none'; }, 3000);
        }
    };

    const updateCsrfTokens = (tokenName, tokenHash) => {
        if (!tokenName || !tokenHash) return;
        document.querySelectorAll(`input[name="${tokenName}"]`).forEach((input) => {
            input.value = tokenHash;
        });
    };

    const renderReportRow = (report) => {
        const row = document.createElement('tr');
        row.dataset.report = JSON.stringify(report);
        row.innerHTML = `
            <td>#${report.id}</td>
            <td><span class="badge">${report.report_type_label}</span></td>
            <td>${report.description || '—'}</td>
            <td>${report.period_label}</td>
            <td>${report.generated_by_label}</td>
            <td>${report.created_at_formatted || '—'}</td>
            <td><button class="btn btn-outline btn-sm" data-view-report>View</button></td>
        `;
        return row;
    };

    const bindViewButtons = () => {
        document.querySelectorAll('[data-view-report]').forEach((button) => {
            button.removeEventListener('click', handleViewReport);
            button.addEventListener('click', handleViewReport);
        });
    };

    const handleViewReport = (event) => {
        const row = event.currentTarget.closest('tr');
        if (!row) return;
        const report = JSON.parse(row.dataset.report || '{}');

        detailTitle.textContent = report.report_type_label || 'Report Details';
        detailType.textContent = report.report_type_label || '—';
        detailDescription.textContent = report.description || '—';
        detailPeriod.textContent = report.period_label || 'All Time';
        detailAuthor.textContent = report.generated_by_label || 'Super Admin';
        detailCreated.textContent = report.created_at_formatted || '—';
        detailData.textContent = report.report_data ? JSON.stringify(report.report_data, null, 2) : 'No data available';

        showModal(detailModal);
    };

    document.querySelectorAll('[data-close-modal]').forEach((button) => {
        button.addEventListener('click', (event) => {
            closeModal(event.currentTarget.closest('dialog'));
        });
    });

    generateForm?.addEventListener('submit', async (event) => {
        event.preventDefault();
        const formData = new FormData(generateForm);
        const submitButton = generateForm.querySelector('button[type="submit"]');
        if (submitButton) {
            submitButton.disabled = true;
            submitButton.textContent = 'Generating...';
        }

        try {
            const response = await fetch(generateForm.action, {
                method: 'POST',
                headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' },
                body: formData,
            });
            const result = await response.json();

            updateCsrfTokens(result?.csrfToken, result?.csrfHash);

            if (!response.ok || !result.success) {
                showFeedback(result?.error || 'Failed to generate report.', 'error');
                return;
            }

            if (reportsTable) {
                const tbody = reportsTable.querySelector('tbody');
                const row = renderReportRow(result.report);
                if (tbody && row) {
                    tbody.prepend(row);
                    bindViewButtons();
                }
            }

            showFeedback(result.message || 'Report generated successfully.', 'success');
            generateForm.reset();
        } catch (error) {
            showFeedback('Unexpected error generating report.', 'error');
        } finally {
            if (submitButton) {
                submitButton.disabled = false;
                submitButton.textContent = 'Generate Report';
            }
        }
    });

    bindViewButtons();
})();
</script>

<?= $this->endSection() ?>
