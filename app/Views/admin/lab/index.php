<?= $this->extend('template/header') ?>

<?= $this->section('title') ?><?= esc($title) ?><?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="admin-module">
    <div class="module-header">
        <h2><?= esc($title) ?></h2>
        <a href="<?= base_url('admin/lab/create') ?>" class="btn btn-primary">
            <i class="fa-solid fa-plus"></i> Create Lab Service
        </a>
    </div>

    <?php if (session()->getFlashdata('success')): ?>
        <div class="alert alert-success"><?= session()->getFlashdata('success') ?></div>
    <?php endif; ?>
    
    <?php if (session()->getFlashdata('error')): ?>
        <div class="alert alert-danger"><?= session()->getFlashdata('error') ?></div>
    <?php endif; ?>
    
    <?php if (session()->getFlashdata('errors')): ?>
        <div class="alert alert-danger">
            <ul style="margin: 0; padding-left: 20px;">
                <?php foreach (session()->getFlashdata('errors') as $error): ?>
                    <li><?= esc($error) ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <!-- Search Bar -->
    <div style="margin-bottom: 20px; background: white; padding: 16px; border-radius: 8px; box-shadow: 0 2px 8px rgba(0,0,0,0.1);">
        <input type="text" id="labSearchInput" placeholder="🔍 Search by patient name, test type, status, or contact..." 
               style="width: 100%; padding: 12px 16px; border: 2px solid #e0e0e0; border-radius: 8px; font-size: 14px; transition: border-color 0.3s;"
               onkeyup="filterLabTable()">
    </div>

    <div class="table-container">
        <table class="data-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Patient</th>
                    <th>Type</th>
                    <th>Contact</th>
                    <th>Test Type</th>
                    <th>Status</th>
                    <th>Nurse</th>
                    <th>Doctor</th>
                    <th>Date Created</th>
                    <th>Result</th>
                    <th>Remarks</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($labServices)): ?>
                    <tr><td colspan="12" class="text-center">No lab services found.</td></tr>
                <?php else: ?>
                    <?php foreach ($labServices as $lab): ?>
                        <tr>
                            <td>#<?= esc($lab['id']) ?></td>
                            <td>
                                <strong><?= esc($lab['firstname'] . ' ' . $lab['lastname']) ?></strong>
                            </td>
                            <td>
                                <?php
                                $visitType = $lab['visit_type'] ?? '';
                                $isWalkIn = (strtolower($visitType) === 'walk-in' || strtolower($visitType) === 'walkin' || strtolower($visitType) === 'walk_in');
                                ?>
                                <?php if ($isWalkIn): ?>
                                    <span style="display: inline-flex; align-items: center; gap: 6px; padding: 4px 10px; background: #fef3c7; color: #d97706; border-radius: 12px; font-size: 12px; font-weight: 600;">
                                        <i class="fas fa-walking"></i> Walk-in
                                    </span>
                                <?php else: ?>
                                    <span style="display: inline-flex; align-items: center; gap: 6px; padding: 4px 10px; background: #dbeafe; color: #2563eb; border-radius: 12px; font-size: 12px; font-weight: 600;">
                                        <i class="fas fa-user-injured"></i> Patient
                                    </span>
                                <?php endif; ?>
                            </td>
                            <td><?= esc($lab['contact'] ?? 'N/A') ?></td>
                            <td>
                                <span style="font-weight: 600; color: #2e7d32;"><?= esc($lab['test_type']) ?></span>
                            </td>
                            <td>
                                <?php
                                $status = $lab['request_status'] ?? 'pending';
                                $statusColors = [
                                    'pending' => '#f59e0b',
                                    'specimen_collected' => '#3b82f6',
                                    'in_progress' => '#8b5cf6',
                                    'completed' => '#10b981',
                                    'cancelled' => '#ef4444'
                                ];
                                $statusColor = $statusColors[$status] ?? '#6b7280';
                                ?>
                                <span class="status-badge" style="background: <?= $statusColor ?>20; color: <?= $statusColor ?>; padding: 4px 10px; border-radius: 12px; font-size: 12px; font-weight: 600; text-transform: capitalize;">
                                    <?= esc(ucfirst(str_replace('_', ' ', $status))) ?>
                                </span>
                            </td>
                            <td><?= esc($lab['nurse_name'] ?? 'N/A') ?></td>
                            <td><?= esc($lab['doctor_name'] ?? 'N/A') ?></td>
                            <td>
                                <?php if (!empty($lab['created_at'])): ?>
                                    <?= esc(date('M d, Y', strtotime($lab['created_at']))) ?><br>
                                    <small style="color: #64748b;"><?= esc(date('h:i A', strtotime($lab['created_at']))) ?></small>
                                <?php else: ?>
                                    N/A
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php 
                                // Check if result exists (from lab_results or lab_services)
                                $hasResult = !empty($lab['lab_result']) || !empty($lab['result']);
                                ?>
                                <?php if ($hasResult): ?>
                                    <div style="display: flex; flex-direction: column; gap: 8px; align-items: flex-start;">
                                        <span style="color: #10b981; font-weight: 600;">
                                            <i class="fas fa-check-circle"></i> Available
                                        </span>
                                        <button type="button" 
                                            class="btn-view-result" 
                                            data-lab-id="<?= esc($lab['id']) ?>"
                                            data-patient-name="<?= esc(($lab['firstname'] ?? '') . ' ' . ($lab['lastname'] ?? '')) ?>"
                                            data-contact="<?= esc($lab['contact'] ?? '') ?>"
                                            data-birthdate="<?= esc($lab['birthdate'] ?? '') ?>"
                                            data-test-name="<?= esc($lab['test_name'] ?? $lab['test_type'] ?? '') ?>"
                                            data-test-type="<?= esc($lab['test_type'] ?? '') ?>"
                                            data-requested-date="<?= esc($lab['requested_date'] ?? '') ?>"
                                            data-completed-at="<?= esc($lab['completed_at'] ?? '') ?>"
                                            data-result="<?= esc($lab['lab_result'] ?? $lab['result'] ?? '') ?>"
                                            data-interpretation="<?= esc($lab['interpretation'] ?? $lab['remarks'] ?? '') ?>"
                                            data-completed-by="<?= esc($lab['completed_by_name'] ?? '') ?>"
                                            data-normal-range="<?= esc($lab['normal_range'] ?? '') ?>"
                                            style="padding: 6px 12px; background: #10b981; color: white; border: none; border-radius: 6px; cursor: pointer; font-size: 12px; font-weight: 600; position: relative; z-index: 10; pointer-events: auto !important;">
                                        <i class="fas fa-eye"></i> View Full Result
                                    </button>
                                    </div>
                                <?php else: ?>
                                    <span style="color: #f59e0b;">
                                        <i class="fas fa-clock"></i> Pending
                                    </span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if (!empty($lab['remarks'])): ?>
                                    <small style="color: #64748b;"><?= esc(substr($lab['remarks'], 0, 30)) ?><?= strlen($lab['remarks']) > 30 ? '...' : '' ?></small>
                                <?php else: ?>
                                    <span style="color: #9ca3af;">-</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <div style="display: flex; gap: 6px; flex-wrap: wrap;">
                                    <a href="<?= base_url('admin/lab/view/' . $lab['id']) ?>" class="btn btn-sm btn-view" title="View">
                                        <i class="fas fa-eye"></i> View
                                    </a>
                                    <a href="<?= base_url('admin/lab/delete/' . $lab['id']) ?>" class="btn btn-sm btn-delete" onclick="return confirm('Are you sure?')" title="Delete">
                                        <i class="fas fa-trash"></i> Delete
                                    </a>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<style>
.admin-module { padding: 24px; }
.module-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px; }
.module-header h2 { margin: 0; color: #2e7d32; }
.btn { padding: 10px 20px; border-radius: 6px; text-decoration: none; display: inline-block; }
.btn-primary { background: #2e7d32; color: white; }
.btn-sm { padding: 6px 12px; font-size: 14px; }
.btn-view { background: #3b82f6; color: white; margin-right: 8px; }
.btn-delete { background: #ef4444; color: white; }
.table-container { background: white; border-radius: 8px; overflow: hidden; }
.data-table { width: 100%; border-collapse: collapse; font-size: 14px; }
.data-table th { background: #e8f5e9; padding: 12px; text-align: left; font-weight: 600; color: #2e7d32; white-space: nowrap; }
.data-table td { padding: 12px; border-bottom: 1px solid #e5e7eb; vertical-align: top; position: relative; }
.data-table td button { position: relative !important; z-index: 100 !important; pointer-events: auto !important; }
.data-table tr:hover { background: #f9fafb; }
.text-center { text-align: center; }
.status-badge, .priority-badge { display: inline-block; }
.alert { padding: 12px; border-radius: 6px; margin-bottom: 16px; }
.alert-success { background: #d1fae5; color: #047857; }
.alert-danger { background: #fee2e2; color: #991b1b; padding: 12px; border-radius: 6px; margin-bottom: 16px; }
.btn-view-result { 
    transition: all 0.2s; 
    position: relative !important;
    z-index: 10 !important;
    pointer-events: auto !important;
    cursor: pointer !important;
}
.btn-view-result:hover { 
    background: #059669 !important; 
    transform: translateY(-1px);
    box-shadow: 0 2px 8px rgba(16, 185, 129, 0.3);
}
.btn-view-result:active {
    transform: translateY(0);
}
.data-table td {
    position: relative;
}
</style>

<!-- Result View Modal - Formatted Laboratory Report -->
<div id="resultModal" style="display: none; position: fixed; top: 0; left: 0; right: 0; bottom: 0; background: rgba(0,0,0,0.5); z-index: 1000; align-items: center; justify-content: center;">
    <div style="background: white; border-radius: 12px; padding: 40px; max-width: 900px; width: 90%; max-height: 90vh; overflow-y: auto; box-shadow: 0 10px 40px rgba(0,0,0,0.2);">
        <!-- Header -->
        <div style="text-align: center; border-bottom: 3px solid #2e7d32; padding-bottom: 20px; margin-bottom: 30px;">
            <h1 style="color: #2e7d32; margin: 0 0 10px; font-size: 32px; font-weight: 700;">LABORATORY RESULT</h1>
            <p style="color: #64748b; margin: 5px 0; font-size: 14px;">Hospital Laboratory Department</p>
            <p style="color: #64748b; margin: 5px 0; font-size: 12px;">Result Report</p>
        </div>
        
        <!-- Patient Information -->
        <div style="margin-bottom: 30px;">
            <div style="color: #2e7d32; font-size: 18px; font-weight: 700; margin-bottom: 15px; border-bottom: 2px solid #e5e7eb; padding-bottom: 8px;">
                Patient Information
            </div>
            <div style="display: grid; grid-template-columns: 150px 1fr; gap: 12px; margin-bottom: 12px;">
                <div style="font-weight: 600; color: #374151;">Patient Name:</div>
                <div style="color: #1f2937; font-weight: 600;" id="modalPatientName"></div>
                <div style="font-weight: 600; color: #374151;">Contact:</div>
                <div style="color: #1f2937;" id="modalContact"></div>
                <div style="font-weight: 600; color: #374151;">Date of Birth:</div>
                <div style="color: #1f2937;" id="modalBirthdate"></div>
            </div>
        </div>
        
        <!-- Test Information -->
        <div style="margin-bottom: 30px;">
            <div style="color: #2e7d32; font-size: 18px; font-weight: 700; margin-bottom: 15px; border-bottom: 2px solid #e5e7eb; padding-bottom: 8px;">
                Test Information
            </div>
            <div style="display: grid; grid-template-columns: 150px 1fr; gap: 12px; margin-bottom: 12px;">
                <div style="font-weight: 600; color: #374151;">Test Name:</div>
                <div style="color: #1f2937; font-weight: 600;" id="modalTestName"></div>
                <div style="font-weight: 600; color: #374151;">Test Type:</div>
                <div style="color: #1f2937;" id="modalTestType"></div>
                <div style="font-weight: 600; color: #374151;">Requested Date:</div>
                <div style="color: #1f2937;" id="modalRequestedDate"></div>
                <div style="font-weight: 600; color: #374151;">Completed Date:</div>
                <div style="color: #1f2937;" id="modalCompletedDate"></div>
                <div style="font-weight: 600; color: #374151;" id="normalRangeLabel" style="display: none;">Normal Range:</div>
                <div style="color: #1f2937;" id="modalNormalRange" style="display: none;"></div>
            </div>
        </div>
        
        <!-- Test Result -->
        <div style="margin-bottom: 30px;">
            <div style="color: #2e7d32; font-size: 18px; font-weight: 700; margin-bottom: 15px; border-bottom: 2px solid #e5e7eb; padding-bottom: 8px;">
                Test Result
            </div>
            <div style="background: #f9fafb; border: 2px solid #e5e7eb; border-radius: 8px; padding: 20px; margin: 15px 0;">
                <div id="modalResult" style="color: #1f2937; line-height: 1.8; white-space: pre-wrap; font-family: 'Courier New', monospace; font-size: 14px;">
                </div>
            </div>
        </div>
        
        <!-- Interpretation -->
        <div id="interpretationSection" style="margin-bottom: 30px; display: none;">
            <div style="color: #2e7d32; font-size: 18px; font-weight: 700; margin-bottom: 15px; border-bottom: 2px solid #e5e7eb; padding-bottom: 8px;">
                Interpretation
            </div>
            <div style="background: #eff6ff; border-left: 4px solid #0288d1; padding: 15px; margin: 15px 0; border-radius: 4px;">
                <div id="modalInterpretation" style="color: #1f2937; line-height: 1.8; white-space: pre-wrap; font-size: 14px;">
                </div>
            </div>
        </div>
        
        <!-- Footer -->
        <div style="margin-top: 40px; padding-top: 20px; border-top: 2px solid #e5e7eb; text-align: center; color: #64748b; font-size: 12px;">
            <p style="margin: 5px 0;">This is a computer-generated report. No signature required.</p>
            <p style="margin: 5px 0;">Generated on: <span id="modalGeneratedDate"></span></p>
        </div>
        
        <!-- Close Button -->
        <div style="display: flex; justify-content: flex-end; margin-top: 24px;">
            <button onclick="closeResultModal()" style="background: #6b7280; color: white; border: none; border-radius: 8px; padding: 10px 24px; cursor: pointer; font-size: 14px; font-weight: 600;">
                Close
            </button>
        </div>
    </div>
</div>

<script>
function viewResult(labId, data) {
    console.log('viewResult called with:', labId, data);
    if (!data) {
        alert('Error: No data available');
        return;
    }
    try {
    // Populate Patient Information
    document.getElementById('modalPatientName').textContent = data.patient_name || 'N/A';
    document.getElementById('modalContact').textContent = data.contact || 'N/A';
    
    if (data.birthdate) {
        const birthdate = new Date(data.birthdate);
        document.getElementById('modalBirthdate').textContent = birthdate.toLocaleDateString('en-US', { month: 'short', day: '2-digit', year: 'numeric' });
    } else {
        document.getElementById('modalBirthdate').textContent = 'N/A';
    }
    
    // Populate Test Information
    document.getElementById('modalTestName').textContent = data.test_name || 'N/A';
    document.getElementById('modalTestType').textContent = data.test_type || 'N/A';
    
    if (data.requested_date) {
        const requestedDate = new Date(data.requested_date);
        document.getElementById('modalRequestedDate').textContent = requestedDate.toLocaleDateString('en-US', { month: 'short', day: '2-digit', year: 'numeric' });
    } else {
        document.getElementById('modalRequestedDate').textContent = 'N/A';
    }
    
    if (data.completed_at) {
        const completedDate = new Date(data.completed_at);
        document.getElementById('modalCompletedDate').textContent = completedDate.toLocaleDateString('en-US', { month: 'short', day: '2-digit', year: 'numeric' }) + ' ' + completedDate.toLocaleTimeString('en-US', { hour: '2-digit', minute: '2-digit', hour12: false });
    } else {
        document.getElementById('modalCompletedDate').textContent = 'N/A';
    }
    
    // Normal Range
    if (data.normal_range && data.normal_range.trim() !== '') {
        document.getElementById('normalRangeLabel').style.display = 'block';
        document.getElementById('modalNormalRange').style.display = 'block';
        document.getElementById('modalNormalRange').textContent = data.normal_range;
    } else {
        document.getElementById('normalRangeLabel').style.display = 'none';
        document.getElementById('modalNormalRange').style.display = 'none';
    }
    
    // Populate Test Result
    const resultText = data.result && data.result.trim() !== '' ? data.result : 'No result available';
    if (resultText === 'No result available') {
        document.getElementById('modalResult').innerHTML = '<span style="color: #f59e0b; font-style: italic;">No result available yet. Please wait for lab staff to complete the test.</span>';
    } else {
        document.getElementById('modalResult').textContent = resultText;
    }
    
    // Populate Interpretation
    if (data.interpretation && data.interpretation.trim() !== '') {
        document.getElementById('modalInterpretation').textContent = data.interpretation;
        document.getElementById('interpretationSection').style.display = 'block';
    } else {
        document.getElementById('interpretationSection').style.display = 'none';
    }
    
    // Generated Date
    document.getElementById('modalGeneratedDate').textContent = new Date().toLocaleString('en-US', { month: 'short', day: '2-digit', year: 'numeric', hour: '2-digit', minute: '2-digit', second: '2-digit' });
    
    // Show modal
    document.getElementById('resultModal').style.display = 'flex';
    } catch (error) {
        console.error('Error in viewResult:', error);
        alert('Error displaying result: ' + error.message);
    }
}

function closeResultModal() {
    document.getElementById('resultModal').style.display = 'none';
}

// Close modal when clicking outside
document.getElementById('resultModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeResultModal();
    }
});

// Close modal with Escape key
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        closeResultModal();
    }
});

// Add event listeners to all view result buttons after page load
document.addEventListener('DOMContentLoaded', function() {
    const viewButtons = document.querySelectorAll('.btn-view-result');
    viewButtons.forEach(function(button) {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            try {
                const labId = parseInt(button.getAttribute('data-lab-id'));
                const data = {
                    patient_name: button.getAttribute('data-patient-name') || 'N/A',
                    contact: button.getAttribute('data-contact') || 'N/A',
                    birthdate: button.getAttribute('data-birthdate') || '',
                    test_name: button.getAttribute('data-test-name') || 'N/A',
                    test_type: button.getAttribute('data-test-type') || 'N/A',
                    requested_date: button.getAttribute('data-requested-date') || '',
                    completed_at: button.getAttribute('data-completed-at') || '',
                    result: button.getAttribute('data-result') || '',
                    interpretation: button.getAttribute('data-interpretation') || '',
                    completed_by: button.getAttribute('data-completed-by') || '',
                    normal_range: button.getAttribute('data-normal-range') || ''
                };
                viewResult(labId, data);
            } catch (error) {
                console.error('Error handling button click:', error);
                alert('Error: Could not open result. Please try again.');
            }
        });
    });
});

function filterLabTable() {
    const searchInput = document.getElementById('labSearchInput');
    const searchTerm = searchInput.value.toLowerCase();
    const table = document.querySelector('.data-table tbody');
    
    if (!table) return;
    
    const rows = table.querySelectorAll('tr');
    rows.forEach(row => {
        const text = row.textContent.toLowerCase();
        if (text.includes(searchTerm)) {
            row.style.display = '';
        } else {
            row.style.display = 'none';
        }
    });
}
</script>
<?= $this->endSection() ?>

