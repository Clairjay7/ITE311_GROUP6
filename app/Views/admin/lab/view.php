<?= $this->extend('template/header') ?>

<?= $this->section('title') ?><?= esc($title) ?><?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="admin-module">
    <div class="module-header">
        <h2><?= esc($title) ?></h2>
        <a href="<?= base_url('admin/lab') ?>" class="btn btn-secondary">Back to List</a>
    </div>

    <div class="view-container" style="background: white; padding: 24px; border-radius: 8px; max-width: 1000px;">
        <!-- Patient Information -->
        <div style="margin-bottom: 30px;">
            <h3 style="color: #2e7d32; font-size: 20px; font-weight: 700; margin-bottom: 20px; border-bottom: 2px solid #e5e7eb; padding-bottom: 10px;">
                <i class="fas fa-user"></i> Patient Information
            </h3>
            <div style="display: grid; grid-template-columns: 200px 1fr; gap: 16px;">
                <div style="font-weight: 600; color: #374151;">Patient Name:</div>
                <div><?= esc(($labService['firstname'] ?? '') . ' ' . ($labService['lastname'] ?? '')) ?></div>
                
                <div style="font-weight: 600; color: #374151;">Contact:</div>
                <div><?= esc($labService['contact'] ?? 'N/A') ?></div>
                
                <div style="font-weight: 600; color: #374151;">Gender:</div>
                <div><?= esc(ucfirst($labService['gender'] ?? 'N/A')) ?></div>
                
                <?php if (!empty($labService['birthdate'])): ?>
                <div style="font-weight: 600; color: #374151;">Date of Birth:</div>
                <div><?= esc(date('M d, Y', strtotime($labService['birthdate']))) ?></div>
                <?php endif; ?>
                
                <?php if (!empty($labService['address'])): ?>
                <div style="font-weight: 600; color: #374151;">Address:</div>
                <div><?= esc($labService['address']) ?></div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Lab Test Information -->
        <div style="margin-bottom: 30px;">
            <h3 style="color: #2e7d32; font-size: 20px; font-weight: 700; margin-bottom: 20px; border-bottom: 2px solid #e5e7eb; padding-bottom: 10px;">
                <i class="fas fa-vial"></i> Lab Test Information
            </h3>
            <div style="display: grid; grid-template-columns: 200px 1fr; gap: 16px;">
                <div style="font-weight: 600; color: #374151;">Test Name:</div>
                <div><?= esc($labService['test_name'] ?? $labService['test_type'] ?? 'N/A') ?></div>
                
                <div style="font-weight: 600; color: #374151;">Test Type:</div>
                <div><?= esc($labService['test_type'] ?? 'N/A') ?></div>
                
                <?php if (!empty($labService['requested_date'])): ?>
                <div style="font-weight: 600; color: #374151;">Requested Date:</div>
                <div><?= esc(date('M d, Y', strtotime($labService['requested_date']))) ?></div>
                <?php endif; ?>
                
                <div style="font-weight: 600; color: #374151;">Status:</div>
                <div>
                    <span style="background: <?= ($labService['request_status'] ?? 'pending') === 'completed' ? '#d1fae5' : '#fef3c7'; ?>; color: <?= ($labService['request_status'] ?? 'pending') === 'completed' ? '#065f46' : '#92400e'; ?>; padding: 4px 12px; border-radius: 6px; font-weight: 600;">
                        <?= esc(ucfirst($labService['request_status'] ?? 'pending')) ?>
                    </span>
                </div>
                
                <?php if (!empty($labService['nurse_name'])): ?>
                <div style="font-weight: 600; color: #374151;">Assigned Nurse:</div>
                <div><?= esc($labService['nurse_name']) ?></div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Lab Result -->
        <?php if (!empty($labService['lab_result']) || !empty($labService['result'])): ?>
        <div style="margin-bottom: 30px;">
            <h3 style="color: #2e7d32; font-size: 20px; font-weight: 700; margin-bottom: 20px; border-bottom: 2px solid #e5e7eb; padding-bottom: 10px;">
                <i class="fas fa-file-medical"></i> Lab Result
            </h3>
            <div style="background: #f9fafb; border: 2px solid #e5e7eb; border-radius: 8px; padding: 20px; margin: 15px 0;">
                <div style="color: #1f2937; line-height: 1.8; white-space: pre-wrap; font-family: 'Courier New', monospace; font-size: 14px;">
                    <?= esc($labService['lab_result'] ?? $labService['result']) ?>
                </div>
            </div>
            
            <?php if (!empty($labService['interpretation'])): ?>
            <div style="background: #eff6ff; border-left: 4px solid #0288d1; padding: 15px; margin: 15px 0; border-radius: 4px;">
                <div style="font-weight: 600; color: #1e40af; margin-bottom: 8px;">Interpretation:</div>
                <div style="color: #1f2937; line-height: 1.8; white-space: pre-wrap;">
                    <?= esc($labService['interpretation']) ?>
                </div>
            </div>
            <?php endif; ?>
            
            <?php if (!empty($labService['completed_at'])): ?>
            <div style="margin-top: 15px; color: #64748b; font-size: 14px;">
                <i class="fas fa-check-circle"></i> Completed on: <?= esc(date('M d, Y h:i A', strtotime($labService['completed_at']))) ?>
                <?php if (!empty($labService['completed_by_name'])): ?>
                    by <?= esc($labService['completed_by_name']) ?>
                <?php endif; ?>
            </div>
            <?php endif; ?>
        </div>
        <?php else: ?>
        <div style="margin-bottom: 30px; padding: 20px; background: #fef3c7; border-left: 4px solid #f59e0b; border-radius: 8px;">
            <p style="margin: 0; color: #92400e;">
                <i class="fas fa-clock"></i> Lab result is pending. Please wait for lab staff to complete the test.
            </p>
        </div>
        <?php endif; ?>

        <!-- Payment Information -->
        <?php if (!empty($labService['payment_status'])): ?>
        <div style="margin-bottom: 30px;">
            <h3 style="color: #2e7d32; font-size: 20px; font-weight: 700; margin-bottom: 20px; border-bottom: 2px solid #e5e7eb; padding-bottom: 10px;">
                <i class="fas fa-money-bill-wave"></i> Payment Information
            </h3>
            <div style="display: grid; grid-template-columns: 200px 1fr; gap: 16px;">
                <div style="font-weight: 600; color: #374151;">Payment Status:</div>
                <div>
                    <span style="background: <?= ($labService['payment_status'] ?? 'pending') === 'paid' ? '#d1fae5' : '#fee2e2'; ?>; color: <?= ($labService['payment_status'] ?? 'pending') === 'paid' ? '#065f46' : '#991b1b'; ?>; padding: 4px 12px; border-radius: 6px; font-weight: 600;">
                        <?= esc(ucfirst($labService['payment_status'] ?? 'pending')) ?>
                    </span>
                </div>
            </div>
        </div>
        <?php endif; ?>
    </div>
</div>

<style>
.admin-module { padding: 24px; }
.module-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px; }
.module-header h2 { margin: 0; color: #2e7d32; }
.btn { padding: 10px 20px; border-radius: 6px; text-decoration: none; display: inline-block; border: none; cursor: pointer; }
.btn-secondary { background: #6b7280; color: white; }
</style>
<?= $this->endSection() ?>

