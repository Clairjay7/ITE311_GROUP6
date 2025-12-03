<?= $this->extend('template/header') ?>

<?= $this->section('title') ?><?= esc($title) ?><?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="admin-module">
    <div class="module-header">
        <h2><?= esc($title) ?></h2>
        <a href="<?= base_url('admin/lab') ?>" class="btn btn-secondary">Back to List</a>
    </div>

    <form method="POST" action="<?= base_url('admin/lab/update/' . $labService['id']) ?>" class="form-container">
        <div class="form-group">
            <label for="patient_id">Patient *</label>
            <select id="patient_id" name="patient_id" class="form-control" required>
                <option value="">Select Patient</option>
                <?php foreach ($patients as $patient): ?>
                    <option value="<?= esc($patient['id']) ?>" <?= old('patient_id', $labService['patient_id']) == $patient['id'] ? 'selected' : '' ?>>
                        <?= esc($patient['firstname'] . ' ' . $patient['lastname']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="form-group" id="nurse_field_group" style="display: none;">
            <label for="nurse_id">Nurse <span id="nurse_required_indicator" style="color: #ef4444;">*</span> <span id="nurse_label_text">(Will collect specimen)</span></label>
            <select id="nurse_id" name="nurse_id" class="form-control">
                <option value="">-- Select Nurse --</option>
                <?php foreach ($nurses as $nurse): ?>
                    <option value="<?= esc($nurse['id']) ?>" 
                            <?= old('nurse_id', $labService['nurse_id'] ?? '') == $nurse['id'] ? 'selected' : '' ?>>
                        <?= esc($nurse['username']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
            <small id="nurse_help_text" style="color: #64748b; font-size: 12px; margin-top: 4px; display: block;">
                <i class="fas fa-info-circle"></i> Select a nurse who will collect the specimen from the patient
            </small>
        </div>

        <div class="form-group">
            <label for="test_type">Lab Test *</label>
            <select id="test_type" name="test_type" class="form-control" required onchange="updateTestInfo()">
                <option value="">-- Select Lab Test --</option>
                <?php
                $categoryLabels = [
                    'with_specimen' => '🔬 With Specimen (Requires Physical Specimen)',
                    'without_specimen' => '📋 Without Specimen (No Physical Specimen Needed)'
                ];
                
                // Ensure both categories are shown, even if empty
                $allCategories = ['with_specimen', 'without_specimen'];
                $currentTestType = old('test_type', $labService['test_type']);
                ?>
                <?php if (!empty($labTests)): ?>
                    <?php foreach ($allCategories as $category): ?>
                        <?php if (isset($labTests[$category]) && is_array($labTests[$category]) && !empty($labTests[$category])): ?>
                            <optgroup label="<?= esc($categoryLabels[$category] ?? ucfirst(str_replace('_', ' ', $category))) ?>">
                                <?php foreach ($labTests[$category] as $testType => $tests): ?>
                                    <?php if (is_array($tests)): ?>
                                        <?php foreach ($tests as $test): ?>
                                            <?php if (is_array($test)): ?>
                                                <option value="<?= esc($test['test_name']) ?>" 
                                                        data-test-type="<?= esc($test['test_type']) ?>"
                                                        data-specimen-category="<?= esc($test['specimen_category'] ?? 'with_specimen') ?>"
                                                        data-description="<?= esc($test['description'] ?? '') ?>"
                                                        data-normal-range="<?= esc($test['normal_range'] ?? '') ?>"
                                                        data-price="<?= esc($test['price'] ?? '0.00') ?>"
                                                        <?= ($currentTestType === $test['test_name']) ? 'selected' : '' ?>>
                                                    <?= esc($test['test_name']) ?> 
                                                    <span style="color: #64748b;">(<?= esc($test['test_type']) ?>)</span>
                                                    <?php if (!empty($test['price'])): ?>
                                                        - ₱<?= number_format($test['price'], 2) ?>
                                                    <?php endif; ?>
                                                </option>
                                            <?php endif; ?>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                <?php endforeach; ?>
                            </optgroup>
                        <?php endif; ?>
                    <?php endforeach; ?>
                <?php else: ?>
                    <option value="" disabled>No lab tests available. Please add lab tests first.</option>
                <?php endif; ?>
            </select>
            <small style="color: #64748b; font-size: 12px; margin-top: 4px; display: block;">
                <i class="fas fa-info-circle"></i> Select a lab test from the dropdown
            </small>
        </div>
        
        <div id="test_info" style="display: none; margin-top: 12px; padding: 12px; background: #f8fafc; border-radius: 8px; border-left: 4px solid #2e7d32;">
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 12px;">
                <div>
                    <div style="font-size: 11px; color: #64748b; text-transform: uppercase; font-weight: 600; margin-bottom: 4px;">Test Type</div>
                    <div id="test_type_display" style="font-weight: 600; color: #1e293b;"></div>
                </div>
                <div>
                    <div style="font-size: 11px; color: #64748b; text-transform: uppercase; font-weight: 600; margin-bottom: 4px;">Price</div>
                    <div id="test_price_display" style="font-weight: 600; color: #2e7d32;"></div>
                </div>
                <div>
                    <div style="font-size: 11px; color: #64748b; text-transform: uppercase; font-weight: 600; margin-bottom: 4px;">Normal Range</div>
                    <div id="test_range_display" style="font-weight: 600; color: #1e293b;"></div>
                </div>
            </div>
            <div id="test_description_display" style="margin-top: 8px; font-size: 13px; color: #475569;"></div>
        </div>

        <div class="form-group">
            <label for="result">Result</label>
            <textarea id="result" name="result" class="form-control" rows="4"><?= old('result', $labService['result']) ?></textarea>
        </div>

        <div class="form-group">
            <label for="remarks">Remarks</label>
            <textarea id="remarks" name="remarks" class="form-control" rows="3"><?= old('remarks', $labService['remarks']) ?></textarea>
        </div>

        <div class="form-actions">
            <button type="submit" class="btn btn-primary">Update Lab Service</button>
            <a href="<?= base_url('admin/lab') ?>" class="btn btn-secondary">Cancel</a>
        </div>
    </form>
</div>

<style>
.admin-module { padding: 24px; }
.module-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px; }
.module-header h2 { margin: 0; color: #2e7d32; }
.btn { padding: 10px 20px; border-radius: 6px; text-decoration: none; display: inline-block; border: none; cursor: pointer; }
.btn-primary { background: #2e7d32; color: white; }
.btn-secondary { background: #6b7280; color: white; }
.form-container { background: white; padding: 24px; border-radius: 8px; max-width: 600px; }
.form-group { margin-bottom: 20px; }
.form-group label { display: block; margin-bottom: 6px; font-weight: 600; color: #374151; }
.form-control { width: 100%; padding: 10px; border: 1px solid #d1d5db; border-radius: 6px; font-size: 14px; }
.form-actions { display: flex; gap: 12px; margin-top: 24px; }
.form-control:focus { outline: none; border-color: #2e7d32; box-shadow: 0 0 0 3px rgba(46, 125, 50, 0.1); }
</style>

<script>
function updateTestInfo() {
    const select = document.getElementById('test_type');
    const selectedOption = select.options[select.selectedIndex];
    const infoDiv = document.getElementById('test_info');
    const nurseField = document.getElementById('nurse_id');
    const nurseFieldGroup = document.getElementById('nurse_field_group');
    const nurseRequiredIndicator = document.getElementById('nurse_required_indicator');
    const nurseLabelText = document.getElementById('nurse_label_text');
    const nurseHelpText = document.getElementById('nurse_help_text');
    
    if (!selectedOption || !selectedOption.value || selectedOption.value === '') {
        infoDiv.style.display = 'none';
        nurseFieldGroup.style.display = 'none';
        nurseField.removeAttribute('required');
        return;
    }
    
    const testType = selectedOption.dataset.testType || '';
    const description = selectedOption.dataset.description || '';
    const normalRange = selectedOption.dataset.normalRange || 'N/A';
    const price = selectedOption.dataset.price || '0.00';
    const specimenCategory = selectedOption.dataset.specimenCategory || 'with_specimen';
    
    document.getElementById('test_type_display').textContent = testType || 'N/A';
    document.getElementById('test_price_display').textContent = price > 0 ? '₱' + parseFloat(price).toFixed(2) : 'Free';
    document.getElementById('test_range_display').textContent = normalRange || 'N/A';
    document.getElementById('test_description_display').textContent = description || 'No description available';
    
    infoDiv.style.display = 'block';
    
    // Update nurse field based on specimen category
    if (specimenCategory === 'with_specimen') {
        // With specimen - nurse is REQUIRED
        nurseFieldGroup.style.display = 'block';
        nurseField.setAttribute('required', 'required');
        nurseRequiredIndicator.style.display = 'inline';
        nurseLabelText.textContent = '(Will collect specimen)';
        nurseHelpText.innerHTML = '<i class="fas fa-info-circle"></i> <span style="color: #ef4444;">Required:</span> Select a nurse who will collect the specimen from the patient';
    } else {
        // Without specimen - nurse is NOT required - HIDE the field
        nurseFieldGroup.style.display = 'none';
        nurseField.removeAttribute('required');
        nurseField.value = ''; // Clear selection
    }
}

// Show info for initially selected test
document.addEventListener('DOMContentLoaded', function() {
    const select = document.getElementById('test_type');
    if (select.value) {
        updateTestInfo();
    } else {
        // If no test selected, hide nurse field
        const nurseFieldGroup = document.getElementById('nurse_field_group');
        if (nurseFieldGroup) {
            nurseFieldGroup.style.display = 'none';
        }
    }
});
</script>
<?= $this->endSection() ?>

