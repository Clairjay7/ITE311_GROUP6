<?= $this->extend('template/header') ?>

<?= $this->section('title') ?>Test Requests<?= $this->endSection() ?>

<?= $this->section('content') ?>
<style>
    .modern-card {
        background: #fff;
        border: 1px solid #e5e7eb;
        border-radius: 12px;
        padding: 24px;
        box-shadow: 0 2px 6px rgba(15,23,42,.08);
        margin-bottom: 24px;
    }
    .page-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 24px;
    }
    .page-header h1 {
        margin: 0;
        color: #2e7d32;
        font-size: 28px;
    }
    .data-table {
        width: 100%;
        border-collapse: collapse;
    }
    .data-table th {
        background: #e8f5e9;
        color: #2e7d32;
        font-weight: 600;
        padding: 12px;
        text-align: left;
        border-bottom: 2px solid #4caf50;
    }
    .data-table td {
        padding: 12px;
        border-bottom: 1px solid #e5e7eb;
    }
    .data-table tr:hover {
        background: #f9fafb;
    }
    .badge {
        padding: 4px 12px;
        border-radius: 12px;
        font-size: 12px;
        font-weight: 600;
    }
    .badge-pending { background: #fef3c7; color: #92400e; }
    .badge-in-progress { background: #dbeafe; color: #1e40af; }
    .badge-completed { background: #d1fae5; color: #065f46; }
    .badge-cancelled { background: #fee2e2; color: #991b1b; }
    .badge-routine { background: #f1f5f9; color: #64748b; }
    .badge-urgent { background: #fef3c7; color: #92400e; }
    .badge-stat { background: #fee2e2; color: #991b1b; }
    .btn-modern {
        padding: 8px 16px;
        border-radius: 8px;
        border: none;
        cursor: pointer;
        font-weight: 600;
        font-size: 14px;
        transition: all 0.2s;
    }
    .btn-primary { background: #2e7d32; color: white; }
    .btn-primary:hover { background: #1b5e20; }
    .btn-info { background: #0288d1; color: white; }
    .btn-info:hover { background: #01579b; }
</style>

<div class="container py-4">
    <div class="page-header">
        <h1><i class="fas fa-flask"></i> Test Requests</h1>
        <a href="<?= site_url('labstaff/dashboard') ?>" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left"></i> Back to Dashboard
        </a>
    </div>

    <?php if (session()->getFlashdata('success')): ?>
        <div class="alert alert-success"><?= esc(session()->getFlashdata('success')) ?></div>
    <?php endif; ?>
    <?php if (session()->getFlashdata('error')): ?>
        <div class="alert alert-danger"><?= esc(session()->getFlashdata('error')) ?></div>
    <?php endif; ?>

    <div class="modern-card">
        <?php if (empty($testRequests)): ?>
            <div class="text-center py-5">
                <i class="fas fa-check-circle fa-3x text-success mb-3"></i>
                <p class="lead">No test requests found.</p>
            </div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Request ID</th>
                            <th>Patient</th>
                            <th>Test Type</th>
                            <th>Test Name</th>
                            <th>Requested By</th>
                            <th>Priority</th>
                            <th>Requested Date</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($testRequests as $request): ?>
                            <tr>
                                <td>#<?= esc($request['id']) ?></td>
                                <td>
                                    <strong><?= esc(($request['patient_firstname'] ?? '') . ' ' . ($request['patient_lastname'] ?? '')) ?></strong>
                                    <?php if (!empty($request['patient_contact'])): ?>
                                        <br><small class="text-muted"><?= esc($request['patient_contact']) ?></small>
                                    <?php endif; ?>
                                </td>
                                <td><?= esc($request['test_type']) ?></td>
                                <td><?= esc($request['test_name']) ?></td>
                                <td>
                                    <?php if ($request['requested_by'] === 'doctor' && !empty($request['doctor_name'])): ?>
                                        <i class="fas fa-user-md"></i> Dr. <?= esc($request['doctor_name']) ?>
                                    <?php elseif ($request['requested_by'] === 'nurse' && !empty($request['nurse_name'])): ?>
                                        <i class="fas fa-user-nurse"></i> <?= esc($request['nurse_name']) ?>
                                    <?php else: ?>
                                        <?= esc(ucfirst($request['requested_by'])) ?>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <span class="badge badge-<?= esc($request['priority']) ?>">
                                        <?= esc(ucfirst($request['priority'])) ?>
                                    </span>
                                </td>
                                <td><?= esc(date('M d, Y', strtotime($request['requested_date']))) ?></td>
                                <td>
                                    <span class="badge badge-<?= esc(str_replace('_', '-', $request['status'])) ?>">
                                        <?= esc(ucfirst(str_replace('_', ' ', $request['status']))) ?>
                                    </span>
                                </td>
                                <td>
                                    <?php if ($request['status'] === 'pending'): ?>
                                        <button onclick="markCollected(<?= $request['id'] ?>)" class="btn-modern btn-primary">
                                            <i class="fas fa-vial"></i> Mark Collected
                                        </button>
                                    <?php elseif ($request['status'] === 'in_progress'): ?>
                                        <button onclick="openCompleteModal(<?= $request['id'] ?>, '<?= esc($request['test_name']) ?>')" class="btn-modern btn-info">
                                            <i class="fas fa-check-circle"></i> Mark Completed
                                        </button>
                                    <?php else: ?>
                                        <span class="text-muted">-</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</div>

<!-- Complete Test Modal -->
<div id="completeModal" style="display: none; position: fixed; top: 0; left: 0; right: 0; bottom: 0; background: rgba(0,0,0,0.5); z-index: 1000; align-items: center; justify-content: center;">
    <div style="background: white; border-radius: 12px; padding: 32px; max-width: 600px; width: 90%; max-height: 90vh; overflow-y: auto;">
        <h3 style="margin-top: 0; color: #2e7d32;">Complete Test</h3>
        <form id="completeForm">
            <input type="hidden" id="complete_request_id" name="request_id">
            <div class="form-group">
                <label>Test Name</label>
                <input type="text" id="complete_test_name" class="form-control" readonly>
            </div>
            <div class="form-group">
                <label>Result *</label>
                <textarea name="result" id="test_result" class="form-control" rows="5" required></textarea>
            </div>
            <div class="form-group">
                <label>Interpretation</label>
                <textarea name="interpretation" id="test_interpretation" class="form-control" rows="3"></textarea>
            </div>
            <div style="display: flex; gap: 12px; margin-top: 24px;">
                <button type="submit" class="btn-modern btn-primary" style="flex: 1;">
                    <i class="fas fa-check"></i> Mark as Completed
                </button>
                <button type="button" onclick="closeCompleteModal()" class="btn-modern" style="flex: 1; background: #64748b; color: white;">
                    Cancel
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function markCollected(requestId) {
    if (!confirm('Mark this specimen as collected?')) return;
    
    fetch('<?= site_url('labstaff/test-requests/mark-collected') ?>/' + requestId, {
        method: 'POST',
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'Content-Type': 'application/json',
            '<?= csrf_header() ?>': '<?= csrf_hash() ?>'
        },
        credentials: 'same-origin'
    })
    .then(async response => {
        const contentType = response.headers.get('content-type');
        if (contentType && contentType.includes('application/json')) {
            return response.json();
        } else {
            // If we get HTML (redirect), the user is not authenticated
            const text = await response.text();
            throw new Error('Access denied. Please refresh the page and try again.');
        }
    })
    .then(data => {
        if (data.success) {
            alert(data.message);
            location.reload();
        } else {
            alert('Error: ' + (data.message || 'Unknown error'));
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error: ' + error.message);
    });
}

function openCompleteModal(requestId, testName) {
    document.getElementById('complete_request_id').value = requestId;
    document.getElementById('complete_test_name').value = testName;
    document.getElementById('test_result').value = '';
    document.getElementById('test_interpretation').value = '';
    document.getElementById('completeModal').style.display = 'flex';
}

function closeCompleteModal() {
    document.getElementById('completeModal').style.display = 'none';
    document.getElementById('completeForm').reset();
}

document.getElementById('completeForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    const requestId = formData.get('request_id');
    
    fetch('<?= site_url('labstaff/test-requests/mark-completed') ?>/' + requestId, {
        method: 'POST',
        body: formData,
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            '<?= csrf_header() ?>': '<?= csrf_hash() ?>'
        },
        credentials: 'same-origin'
    })
    .then(async response => {
        const contentType = response.headers.get('content-type');
        if (contentType && contentType.includes('application/json')) {
            return response.json();
        } else {
            const text = await response.text();
            throw new Error('Access denied. Please refresh the page and try again.');
        }
    })
    .then(data => {
        if (data.success) {
            alert(data.message);
            closeCompleteModal();
            location.reload();
        } else {
            alert('Error: ' + (data.message || 'Unknown error'));
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error: ' + error.message);
    });
});
</script>
<?= $this->endSection() ?>

