<?= $this->extend('template/header') ?>

<?= $this->section('title') ?><?= esc($title) ?><?= $this->endSection() ?>

<?= $this->section('content') ?>
<style>
    .admin-module { padding: 24px; }
    .module-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px; }
    .module-header h2 { margin: 0; color: #2e7d32; }
    .btn { padding: 10px 20px; border-radius: 6px; text-decoration: none; display: inline-block; border: none; cursor: pointer; }
    .btn-primary { background: #2e7d32; color: white; }
    .btn-secondary { background: #6b7280; color: white; }
    .form-container { background: white; padding: 24px; border-radius: 8px; max-width: 900px; }
    .form-section { margin-bottom: 32px; padding-bottom: 24px; border-bottom: 2px solid #e5e7eb; }
    .form-section:last-child { border-bottom: none; }
    .section-title { font-size: 18px; font-weight: 700; color: #2e7d32; margin-bottom: 20px; display: flex; align-items: center; gap: 8px; }
    .section-title::before { content: ''; width: 4px; height: 24px; background: #2e7d32; border-radius: 2px; }
    .form-row { display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 16px; margin-bottom: 16px; }
    .form-group { margin-bottom: 16px; }
    .form-group.full-width { grid-column: 1 / -1; }
    .form-group label { display: block; margin-bottom: 6px; font-weight: 600; color: #374151; }
    .form-group label .required { color: #ef4444; }
    .form-group label .optional { color: #64748b; font-weight: 400; font-size: 12px; }
    .form-control, .form-select { width: 100%; padding: 10px; border: 1px solid #d1d5db; border-radius: 6px; font-size: 14px; }
    .form-control:focus, .form-select:focus { outline: none; border-color: #2e7d32; box-shadow: 0 0 0 3px rgba(46, 125, 50, 0.1); }
    .form-hint { font-size: 12px; color: #64748b; margin-top: 4px; }
    .form-actions { display: flex; gap: 12px; margin-top: 32px; padding-top: 24px; border-top: 2px solid #e5e7eb; }
    .alert { padding: 12px; border-radius: 6px; margin-bottom: 16px; }
    .alert-danger { background: #fee2e2; color: #991b1b; }
    .alert-danger ul { margin: 0; padding-left: 20px; }
</style>

<div class="admin-module">
    <div class="module-header">
        <h2><?= esc($title) ?></h2>
        <a href="<?= base_url('admin/pharmacy') ?>" class="btn btn-secondary">Back to List</a>
    </div>

    <?php if (session()->getFlashdata('errors')): ?>
        <div class="alert alert-danger">
            <ul>
                <?php foreach (session()->getFlashdata('errors') as $error): ?>
                    <li><?= esc($error) ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <form method="POST" action="<?= base_url('admin/pharmacy/store') ?>" class="form-container" id="pharmacyCreateForm">
        <?= csrf_field() ?>
        <!-- 1. Item Information -->
        <div class="form-section">
            <h3 class="section-title">1. Item Information</h3>
            
            <div class="form-row">
                <div class="form-group full-width">
                    <label for="category">Category <span class="required">*</span></label>
                    <select id="category" name="category" class="form-select" required>
                        <option value="">-- Select Category --</option>
                        <option value="Analgesics / Antipyretics" <?= old('category') === 'Analgesics / Antipyretics' ? 'selected' : '' ?>>1. Analgesics / Antipyretics (Pain + Fever)</option>
                        <option value="Anti-inflammatory (NSAIDs)" <?= old('category') === 'Anti-inflammatory (NSAIDs)' ? 'selected' : '' ?>>2. Anti-inflammatory (NSAIDs) (Pamamaga + pain)</option>
                        <option value="Antibiotics" <?= old('category') === 'Antibiotics' ? 'selected' : '' ?>>3. Antibiotics (Pang-infection)</option>
                        <option value="Antihistamines" <?= old('category') === 'Antihistamines' ? 'selected' : '' ?>>4. Antihistamines (Pang-allergy)</option>
                        <option value="Cough & Cold (Respiratory)" <?= old('category') === 'Cough & Cold (Respiratory)' ? 'selected' : '' ?>>5. Cough & Cold (Respiratory) (Ubo, sipon, bronchitis)</option>
                        <option value="Gastrointestinal Medicines" <?= old('category') === 'Gastrointestinal Medicines' ? 'selected' : '' ?>>6. Gastrointestinal Medicines (Antacid, PPI, Antiemetic, Anti-diarrheal)</option>
                        <option value="Cardiovascular Medicines" <?= old('category') === 'Cardiovascular Medicines' ? 'selected' : '' ?>>7. Cardiovascular Medicines (Antihypertensive, anti-cholesterol)</option>
                        <option value="Diabetic Medicines" <?= old('category') === 'Diabetic Medicines' ? 'selected' : '' ?>>8. Diabetic Medicines (Insulin + oral meds like Metformin)</option>
                        <option value="Vitamins & Supplements" <?= old('category') === 'Vitamins & Supplements' ? 'selected' : '' ?>>9. Vitamins & Supplements</option>
                        <option value="IV Fluids / Electrolytes" <?= old('category') === 'IV Fluids / Electrolytes' ? 'selected' : '' ?>>10. IV Fluids / Electrolytes (NSS, D5LR, LR, etc.)</option>
                        <option value="Emergency Drugs" <?= old('category') === 'Emergency Drugs' ? 'selected' : '' ?>>11. Emergency Drugs (Crash cart essentials: Epinephrine, Atropine, etc.)</option>
                        <option value="Medical Supplies" <?= old('category') === 'Medical Supplies' ? 'selected' : '' ?>>12. Medical Supplies (Syringe, gauze, catheter, etc.)</option>
                    </select>
                    <div class="form-hint">Select a category to view existing medicines in that category</div>
                </div>
            </div>
            
            <!-- Existing Medicines Display -->
            <div id="existingMedicinesContainer" style="display: none; margin-top: 20px; padding: 16px; background: #f9fafb; border-radius: 8px; border: 1px solid #e5e7eb;">
                <h4 style="margin: 0 0 12px 0; color: #374151; font-size: 16px; font-weight: 600;">
                    <i class="fas fa-pills" style="margin-right: 8px; color: #2e7d32;"></i>
                    Existing Medicines in this Category
                </h4>
                <div id="existingMedicinesList" style="max-height: 300px; overflow-y: auto;">
                    <!-- Medicines will be loaded here via AJAX -->
                </div>
            </div>
        </div>

        <!-- 2. Dosage & Strength (Read-Only) -->
        <div class="form-section" id="dosageStrengthSection" style="display: none;">
            <h3 class="section-title">2. Dosage & Strength Information</h3>
            
            <div style="padding: 20px; background: #f9fafb; border-radius: 8px; border: 1px solid #e5e7eb;">
                <div style="margin-bottom: 16px;">
                    <h4 style="margin: 0 0 8px 0; color: #374151; font-size: 16px; font-weight: 600;">
                        <i class="fas fa-tags" style="margin-right: 8px; color: #2e7d32;"></i>
                        Selected Category: <span id="selectedCategoryDisplay" style="color: #2e7d32;">-</span>
                    </h4>
                </div>
                
                <div id="medicinesWithDosageStrength" style="margin-bottom: 20px;">
                    <!-- Medicines with dosage forms and strengths will be displayed here -->
                </div>
                
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                    <div>
                        <h5 style="margin: 0 0 12px 0; color: #4b5563; font-size: 14px; font-weight: 600;">
                            <i class="fas fa-capsules" style="margin-right: 6px; color: #2e7d32;"></i>
                            Common Dosage Forms
                        </h5>
                        <div id="dosageFormsDisplay" style="min-height: 60px; padding: 12px; background: white; border-radius: 6px; border: 1px solid #d1d5db;">
                            <div style="color: #9ca3af; font-size: 13px; text-align: center;">
                                <i class="fas fa-info-circle"></i> Select a category to view dosage forms
                            </div>
                        </div>
                    </div>
                    
                    <div>
                        <h5 style="margin: 0 0 12px 0; color: #4b5563; font-size: 14px; font-weight: 600;">
                            <i class="fas fa-weight" style="margin-right: 6px; color: #2e7d32;"></i>
                            Common Strengths
                        </h5>
                        <div id="strengthsDisplay" style="min-height: 60px; padding: 12px; background: white; border-radius: 6px; border: 1px solid #d1d5db;">
                            <div style="color: #9ca3af; font-size: 13px; text-align: center;">
                                <i class="fas fa-info-circle"></i> Select a category to view strengths
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>


        <!-- 3. Inventory Information -->
        <div class="form-section" id="inventoryInfoSection" style="display: none;">
            <h3 class="section-title">3. Inventory Information</h3>
            
            <div style="padding: 20px; background: #f9fafb; border-radius: 8px; border: 1px solid #e5e7eb;">
                
                <div style="margin-bottom: 20px;">
                    <h4 style="margin: 0 0 12px 0; color: #374151; font-size: 16px; font-weight: 600;">
                        <i class="fas fa-boxes" style="margin-right: 8px; color: #2e7d32;"></i>
                        Category Inventory Summary
                    </h4>
                    <div id="inventorySummary" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 12px; margin-bottom: 20px;">
                        <!-- Summary will be populated here -->
                    </div>
                </div>
                
                <div style="margin-bottom: 20px;">
                    <h5 style="margin: 0 0 12px 0; color: #4b5563; font-size: 14px; font-weight: 600;">
                        <i class="fas fa-list" style="margin-right: 6px; color: #2e7d32;"></i>
                        Existing Medicines in this Category
                    </h5>
                    <div style="padding: 12px; background: #e8f5e9; border-radius: 6px; margin-bottom: 12px; border-left: 3px solid #2e7d32;">
                        <p style="margin: 0; color: #2e7d32; font-size: 13px;">
                            <i class="fas fa-info-circle" style="margin-right: 6px;"></i>
                            <strong>Note:</strong> Select medicines to update their pricing. Quantity will be set when you click "Create Item" button.
                        </p>
                    </div>
                    <div style="margin-bottom: 12px; padding: 10px; background: #f3f4f6; border-radius: 6px; border: 1px solid #e5e7eb;">
                        <label style="display: flex; align-items: center; cursor: pointer; margin: 0; font-weight: 600; color: #374151; font-size: 14px;">
                            <input type="checkbox" id="selectAllMedicines" style="width: 18px; height: 18px; cursor: pointer; margin-right: 8px;" title="Select/Deselect All Medicines">
                            <span><i class="fas fa-check-square" style="margin-right: 6px; color: #2e7d32;"></i>Select All Medicines</span>
                        </label>
                    </div>
                    <div id="inventoryMedicinesList" style="max-height: 400px; overflow-y: auto;">
                        <!-- Medicines list will be populated here -->
                    </div>
                </div>
                
                <!-- Quantity Field for New Item -->
                <div style="margin-top: 20px; padding: 16px; background: white; border-radius: 8px; border: 1px solid #e5e7eb;">
                    <div style="display: flex; gap: 12px; align-items: end;">
                        <div style="flex: 1;">
                            <label style="display: block; margin-bottom: 6px; font-size: 13px; font-weight: 500; color: #374151;">
                                Quantity / Stock for New Item <span style="color: #ef4444;">*</span>
                            </label>
                            <input type="number" id="quantity" name="quantity" min="0" class="form-control" value="<?= old('quantity') ?>" required placeholder="Enter initial stock quantity">
                            <div class="form-hint" style="margin-top: 4px; font-size: 12px; color: #6b7280;">This quantity will be set when you create the item</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>


        <!-- 4. Pricing -->
        <div class="form-section" id="pricingSection" style="display: none;">
            <h3 class="section-title">4. Individual Pricing</h3>
            
            <div style="padding: 20px; background: #f9fafb; border-radius: 8px; border: 1px solid #e5e7eb;">
                <div style="margin-bottom: 16px;">
                    <p style="margin: 0; color: #6b7280; font-size: 14px;">
                        <i class="fas fa-info-circle" style="margin-right: 6px; color: #3b82f6;"></i>
                        Select medicines from the inventory list above to view and edit their individual pricing.
                    </p>
        </div>

                <div id="pricingTableContainer" style="background: white; border-radius: 6px; overflow: hidden;">
                    <div id="pricingTable" style="display: none;">
                        <table style="width: 100%; border-collapse: collapse;">
                            <thead>
                                <tr style="background: #f3f4f6; border-bottom: 2px solid #e5e7eb;">
                                    <th style="padding: 12px; text-align: left; font-weight: 600; color: #374151; font-size: 13px;">Medicine</th>
                                    <th style="padding: 12px; text-align: right; font-weight: 600; color: #374151; font-size: 13px;">Unit Price</th>
                                    <th style="padding: 12px; text-align: right; font-weight: 600; color: #374151; font-size: 13px;">Selling Price</th>
                                    <th style="padding: 12px; text-align: right; font-weight: 600; color: #374151; font-size: 13px;">Price</th>
                                    <th style="padding: 12px; text-align: right; font-weight: 600; color: #374151; font-size: 13px;">Markup %</th>
                                    <th style="padding: 12px; text-align: center; font-weight: 600; color: #374151; font-size: 13px;">Actions</th>
                                </tr>
                            </thead>
                            <tbody id="pricingTableBody">
                                <!-- Pricing rows will be populated here -->
                            </tbody>
                        </table>
                    </div>
                    <div id="pricingEmptyMessage" style="padding: 40px; text-align: center; color: #9ca3af;">
                        <i class="fas fa-inbox" style="font-size: 32px; margin-bottom: 12px; display: block;"></i>
                        <p style="margin: 0; font-size: 14px;">No medicines selected. Select medicines from the inventory list to view pricing.</p>
                    </div>
        </div>

                <div style="margin-top: 16px; padding: 12px; background: #e8f5e9; border-radius: 6px; border-left: 3px solid #2e7d32;">
                    <p style="margin: 0; color: #2e7d32; font-size: 13px;">
                        <i class="fas fa-info-circle" style="margin-right: 6px;"></i>
                        <strong>Note:</strong> Pricing updates and quantity addition will be applied when you click "Create Item" button.
                    </p>
                </div>
            </div>
        </div>

        <div class="form-actions">
            <button type="submit" class="btn btn-primary">Create Item</button>
            <a href="<?= base_url('admin/pharmacy') ?>" class="btn btn-secondary">Cancel</a>
        </div>
    </form>
</div>

<script>
// Store all generic names for fallback
const allGenericNames = <?= json_encode($genericNames ?? []) ?>;

// Load existing medicines and auto-detect generic names when category is selected
document.getElementById('category')?.addEventListener('change', function() {
    const category = this.value;
    const container = document.getElementById('existingMedicinesContainer');
    const list = document.getElementById('existingMedicinesList');
    if (!category) {
        container.style.display = 'none';
        // Hide Dosage & Strength section
        const dosageStrengthSection = document.getElementById('dosageStrengthSection');
        if (dosageStrengthSection) {
            dosageStrengthSection.style.display = 'none';
        }
        // Hide Inventory Info section
        const inventorySection = document.getElementById('inventoryInfoSection');
        if (inventorySection) {
            inventorySection.style.display = 'none';
        }
        // Reset generic name dropdown to show all options
        if (genericNameSelect) {
            genericNameSelect.innerHTML = '<option value="">-- Select Generic Name --</option>';
            allGenericNames.forEach(name => {
                const option = document.createElement('option');
                option.value = name;
                option.textContent = name;
                genericNameSelect.appendChild(option);
            });
        }
        return;
    }
    
    // Show loading
    list.innerHTML = '<div style="padding: 20px; text-align: center; color: #64748b;"><i class="fas fa-spinner fa-spin"></i> Loading medicines...</div>';
    container.style.display = 'block';
    
    // Fetch medicines, generic names, dosage/strength, inventory info, and pricing by category
    Promise.all([
        fetch(`<?= base_url('admin/pharmacy/get-medicines-by-category') ?>?category=${encodeURIComponent(category)}`)
            .then(r => r.json())
            .catch(err => {
                console.error('Error fetching medicines:', err);
                return { success: false, message: 'Failed to fetch medicines', medicines: [] };
            }),
        fetch(`<?= base_url('admin/pharmacy/get-generic-names-by-category') ?>?category=${encodeURIComponent(category)}`)
            .then(r => r.json())
            .catch(err => {
                console.error('Error fetching generic names:', err);
                return { success: false, message: 'Failed to fetch generic names', genericNames: [] };
            }),
        fetch(`<?= base_url('admin/pharmacy/get-dosage-strength-by-category') ?>?category=${encodeURIComponent(category)}`)
            .then(r => r.json())
            .catch(err => {
                console.error('Error fetching dosage/strength:', err);
                return { success: false, message: 'Failed to fetch dosage/strength', dosageForms: [], strengths: [] };
            }),
        fetch(`<?= base_url('admin/pharmacy/get-inventory-info-by-category') ?>?category=${encodeURIComponent(category)}`)
            .then(r => r.json())
            .catch(err => {
                console.error('Error fetching inventory info:', err);
                return { success: false, message: 'Failed to fetch inventory info', medicines: [] };
            }),
        fetch(`<?= base_url('admin/pharmacy/get-average-pricing-by-category') ?>?category=${encodeURIComponent(category)}`)
            .then(r => r.json())
            .catch(err => {
                console.error('Error fetching pricing:', err);
                return { success: false, message: 'Failed to fetch pricing' };
            })
    ])
        .then(([medicinesData, genericNamesData, dosageStrengthData, inventoryData, pricingData]) => {
            // Populate inventory information section
            const inventorySection = document.getElementById('inventoryInfoSection');
            const inventorySummary = document.getElementById('inventorySummary');
            const inventoryMedicinesList = document.getElementById('inventoryMedicinesList');
            
            if (inventorySection && inventoryData.success) {
                inventorySection.style.display = 'block';
                
                // Display summary
                let summaryHtml = `
                    <div style="padding: 12px; background: white; border-radius: 6px; border-left: 3px solid #2e7d32;">
                        <div style="font-size: 12px; color: #6b7280; margin-bottom: 4px;">Total Stock</div>
                        <div style="font-size: 20px; font-weight: 600; color: #2e7d32;">${inventoryData.totalQuantity || 0}</div>
                    </div>
                    <div style="padding: 12px; background: white; border-radius: 6px; border-left: 3px solid #3b82f6;">
                        <div style="font-size: 12px; color: #6b7280; margin-bottom: 4px;">Total Medicines</div>
                        <div style="font-size: 20px; font-weight: 600; color: #3b82f6;">${inventoryData.medicines?.length || 0}</div>
                    </div>
                    <div style="padding: 12px; background: white; border-radius: 6px; border-left: 3px solid #8b5cf6;">
                        <div style="font-size: 12px; color: #6b7280; margin-bottom: 4px;">Unique Batch Numbers</div>
                        <div style="font-size: 20px; font-weight: 600; color: #8b5cf6;">${inventoryData.batchNumbers?.length || 0}</div>
                    </div>
                    <div style="padding: 12px; background: white; border-radius: 6px; border-left: 3px solid #f59e0b;">
                        <div style="font-size: 12px; color: #6b7280; margin-bottom: 4px;">Suppliers</div>
                        <div style="font-size: 20px; font-weight: 600; color: #f59e0b;">${inventoryData.suppliers?.length || 0}</div>
                    </div>
                `;
                inventorySummary.innerHTML = summaryHtml;
                
                
                // Display medicines list with checkboxes for pricing selection only
                if (inventoryData.medicines && inventoryData.medicines.length > 0) {
                    let medicinesHtml = '<div style="display: grid; gap: 12px;">';
                    inventoryData.medicines.forEach(medicine => {
                        const expDate = new Date(medicine.expiration_date);
                        const isExpiringSoon = expDate <= new Date(Date.now() + 30 * 24 * 60 * 60 * 1000);
                        const isExpired = expDate < new Date();
                        const expColor = isExpired ? '#ef4444' : (isExpiringSoon ? '#f59e0b' : '#2e7d32');
                        
                        medicinesHtml += `
                            <div style="padding: 12px; background: white; border-radius: 6px; border: 1px solid #e5e7eb; display: grid; grid-template-columns: auto 2fr 1fr 1fr 1fr 1fr; gap: 12px; align-items: center;">
                                <div style="display: flex; align-items: center; justify-content: center;">
                                    <input type="checkbox" class="medicine-checkbox" value="${medicine.id || ''}" id="medicine-${medicine.id || ''}" data-medicine='${JSON.stringify(medicine)}' style="width: 18px; height: 18px; cursor: pointer;" title="Select to update pricing">
                                </div>
                                <div>
                                    <label for="medicine-${medicine.id || ''}" style="cursor: pointer; margin: 0;">
                                        <div style="font-weight: 600; color: #1f2937; margin-bottom: 4px;">${medicine.item_name || 'N/A'}</div>
                                        <div style="font-size: 11px; color: #6b7280;">Stock: <strong style="color: #2e7d32;">${medicine.quantity || 0}</strong></div>
                                    </label>
                                </div>
                                <div>
                                    <div style="font-size: 11px; color: #6b7280; margin-bottom: 4px;">Batch Number</div>
                                    <div style="font-weight: 600; color: #2e7d32; font-family: monospace; font-size: 12px;">${medicine.batch_number || 'N/A'}</div>
                                </div>
                                <div>
                                    <div style="font-size: 11px; color: #6b7280; margin-bottom: 4px;">Expiration Date</div>
                                    <div style="font-weight: 600; color: ${expColor}; font-size: 12px;">
                                        ${medicine.expiration_date ? new Date(medicine.expiration_date).toLocaleDateString() : 'N/A'}
                                        ${isExpired ? ' <i class="fas fa-exclamation-circle"></i>' : ''}
                                    </div>
                                </div>
                                <div>
                                    <div style="font-size: 11px; color: #6b7280; margin-bottom: 4px;">Supplier</div>
                                    <div style="font-weight: 600; color: #1f2937; font-size: 12px;">${medicine.supplier_name || 'N/A'}</div>
                                </div>
                                <div>
                                    <div style="font-size: 11px; color: #6b7280; margin-bottom: 4px;">Contact</div>
                                    <div style="font-size: 12px; color: #6b7280;">${medicine.supplier_contact || '-'}</div>
                                </div>
                            </div>
                        `;
                    });
                    medicinesHtml += '</div>';
                    inventoryMedicinesList.innerHTML = medicinesHtml;
                    
                    // Add event listeners to checkboxes for pricing updates
                    document.querySelectorAll('.medicine-checkbox').forEach(checkbox => {
                        checkbox.addEventListener('change', function() {
                            updatePricingTable();
                            updateSelectAllCheckbox();
                        });
                    });
                    
                    // Add Select All functionality
                    const selectAllCheckbox = document.getElementById('selectAllMedicines');
                    if (selectAllCheckbox) {
                        selectAllCheckbox.addEventListener('change', function() {
                            const allCheckboxes = document.querySelectorAll('.medicine-checkbox');
                            allCheckboxes.forEach(checkbox => {
                                checkbox.checked = this.checked;
                            });
                            updatePricingTable();
                        });
                        
                        // Update Select All checkbox state when individual checkboxes change
                        function updateSelectAllCheckbox() {
                            const allCheckboxes = document.querySelectorAll('.medicine-checkbox');
                            const checkedCount = document.querySelectorAll('.medicine-checkbox:checked').length;
                            selectAllCheckbox.checked = allCheckboxes.length > 0 && checkedCount === allCheckboxes.length;
                            selectAllCheckbox.indeterminate = checkedCount > 0 && checkedCount < allCheckboxes.length;
                        }
                        
                        // Make updateSelectAllCheckbox available globally
                        window.updateSelectAllCheckbox = updateSelectAllCheckbox;
                    }
                } else {
                    inventoryMedicinesList.innerHTML = '<div style="padding: 20px; text-align: center; color: #64748b;"><i class="fas fa-inbox"></i> No medicines found in this category</div>';
                }
                
            } else if (inventorySection) {
                inventorySection.style.display = 'none';
            }
            
            // Show pricing section if category is selected
            const pricingSection = document.getElementById('pricingSection');
            if (pricingSection && category) {
                pricingSection.style.display = 'block';
            } else if (pricingSection) {
                pricingSection.style.display = 'none';
            }
            // Show and populate Dosage & Strength section
            const dosageStrengthSection = document.getElementById('dosageStrengthSection');
            const selectedCategoryDisplay = document.getElementById('selectedCategoryDisplay');
            const dosageFormsDisplay = document.getElementById('dosageFormsDisplay');
            const strengthsDisplay = document.getElementById('strengthsDisplay');
            const medicinesWithDosageStrength = document.getElementById('medicinesWithDosageStrength');
            
            if (dosageStrengthSection && dosageStrengthData.success) {
                dosageStrengthSection.style.display = 'block';
                selectedCategoryDisplay.textContent = dosageStrengthData.category;
                
                // Display medicines with their dosage forms and strengths
                if (medicinesData.success && medicinesData.medicines && medicinesData.medicines.length > 0) {
                    let medicinesHtml = '<div style="margin-bottom: 20px;"><h5 style="margin: 0 0 12px 0; color: #4b5563; font-size: 14px; font-weight: 600;"><i class="fas fa-pills" style="margin-right: 6px; color: #2e7d32;"></i>Medicines with Dosage Forms & Strengths</h5>';
                    medicinesHtml += '<div style="display: grid; gap: 12px; max-height: 400px; overflow-y: auto;">';
                    
                    medicinesData.medicines.forEach(medicine => {
                        const dosageForm = medicine.dosage_form || 'N/A';
                        const strength = medicine.strength || 'N/A';
                        
                        medicinesHtml += `
                            <div style="padding: 12px; background: white; border-radius: 6px; border: 1px solid #e5e7eb; display: grid; grid-template-columns: 2fr 1fr 1fr; gap: 12px; align-items: center;">
                                <div>
                                    <div style="font-weight: 600; color: #1f2937; margin-bottom: 4px;">${medicine.item_name || 'N/A'}</div>
                                    ${medicine.generic_name ? `<div style="font-size: 11px; color: #6b7280;">${medicine.generic_name}</div>` : ''}
                                </div>
                                <div>
                                    <div style="font-size: 11px; color: #6b7280; margin-bottom: 4px;">Dosage Form</div>
                                    <span style="display: inline-block; padding: 4px 10px; background: #e8f5e9; color: #2e7d32; border-radius: 12px; font-size: 12px; font-weight: 500;">${dosageForm}</span>
                                </div>
                                <div>
                                    <div style="font-size: 11px; color: #6b7280; margin-bottom: 4px;">Strength</div>
                                    <span style="display: inline-block; padding: 4px 10px; background: #e3f2fd; color: #1976d2; border-radius: 12px; font-size: 12px; font-weight: 500;">${strength}</span>
                                </div>
                            </div>
                        `;
                    });
                    
                    medicinesHtml += '</div></div>';
                    medicinesWithDosageStrength.innerHTML = medicinesHtml;
                } else {
                    medicinesWithDosageStrength.innerHTML = '<div style="color: #9ca3af; font-size: 13px; text-align: center; padding: 20px;"><i class="fas fa-info-circle"></i> No medicines found in this category</div>';
                }
                
                // Display dosage forms
                if (dosageStrengthData.dosageForms && dosageStrengthData.dosageForms.length > 0) {
                    let dosageFormsHtml = '<div style="display: flex; flex-wrap: wrap; gap: 6px;">';
                    dosageStrengthData.dosageForms.forEach(form => {
                        dosageFormsHtml += `<span style="display: inline-block; padding: 4px 10px; background: #e8f5e9; color: #2e7d32; border-radius: 12px; font-size: 12px; font-weight: 500;">${form}</span>`;
                    });
                    dosageFormsHtml += '</div>';
                    dosageFormsDisplay.innerHTML = dosageFormsHtml;
                } else {
                    dosageFormsDisplay.innerHTML = '<div style="color: #9ca3af; font-size: 13px; text-align: center;"><i class="fas fa-info-circle"></i> No dosage forms found</div>';
                }
                
                // Display strengths
                if (dosageStrengthData.strengths && dosageStrengthData.strengths.length > 0) {
                    let strengthsHtml = '<div style="display: flex; flex-wrap: wrap; gap: 6px;">';
                    dosageStrengthData.strengths.forEach(strength => {
                        strengthsHtml += `<span style="display: inline-block; padding: 4px 10px; background: #e3f2fd; color: #1976d2; border-radius: 12px; font-size: 12px; font-weight: 500;">${strength}</span>`;
                    });
                    strengthsHtml += '</div>';
                    strengthsDisplay.innerHTML = strengthsHtml;
                } else {
                    strengthsDisplay.innerHTML = '<div style="color: #9ca3af; font-size: 13px; text-align: center;"><i class="fas fa-info-circle"></i> No strengths found</div>';
                }
            } else if (dosageStrengthSection) {
                dosageStrengthSection.style.display = 'none';
            }
            
            // Auto-populate pricing fields based on category
            if (pricingData.success) {
                const unitPriceField = document.getElementById('unit_price');
                const sellingPriceField = document.getElementById('selling_price');
                const priceField = document.getElementById('price');
                const markupField = document.getElementById('markup_percent');
                
                // Only auto-fill if fields are empty
                if (unitPriceField && (!unitPriceField.value || unitPriceField.value == 0)) {
                    unitPriceField.value = pricingData.unit_price.toFixed(2);
                }
                
                if (sellingPriceField && (!sellingPriceField.value || sellingPriceField.value == 0)) {
                    sellingPriceField.value = pricingData.selling_price.toFixed(2);
                }
                
                if (priceField && (!priceField.value || priceField.value == 0)) {
                    priceField.value = pricingData.price.toFixed(2);
                }
                
                if (markupField && (!markupField.value || markupField.value == 0)) {
                    markupField.value = pricingData.markup_percent.toFixed(2);
                }
                
                // Trigger markup calculation if both prices are set
                if (unitPriceField && sellingPriceField && unitPriceField.value && sellingPriceField.value) {
                    calculateMarkup();
                }
                
                // Show pricing hint
                if (pricingData.is_default) {
                    console.log('Using default pricing for category:', category);
                } else {
                    console.log('Using average pricing from existing medicines in category:', category);
                }
            }
            
            // Display medicines
            if (medicinesData && medicinesData.success) {
                if (medicinesData.medicines && medicinesData.medicines.length > 0) {
                    let html = `<div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(300px, 1fr)); gap: 12px;">`;
                    
                    medicinesData.medicines.forEach(medicine => {
                        const stock = medicine.quantity || 0;
                        const reorderLevel = medicine.reorder_level || 10;
                        const isLowStock = stock <= reorderLevel;
                        const stockColor = isLowStock ? '#ef4444' : '#2e7d32';
                        
                        html += `
                            <div style="padding: 12px; background: white; border-radius: 6px; border-left: 3px solid #2e7d32; box-shadow: 0 1px 3px rgba(0,0,0,0.1);">
                                <div style="font-weight: 600; color: #1f2937; margin-bottom: 4px;">
                                    ${medicine.item_name || 'N/A'}
                                </div>
                                ${medicine.generic_name ? `<div style="font-size: 12px; color: #64748b; margin-bottom: 4px;">${medicine.generic_name}</div>` : ''}
                                <div style="display: flex; justify-content: space-between; align-items: center; margin-top: 8px;">
                                    <div style="font-size: 12px; color: #64748b;">
                                        <i class="fas fa-box" style="margin-right: 4px;"></i>
                                        Stock: <strong style="color: ${stockColor};">${stock}</strong>
                                    </div>
                                    ${medicine.strength ? `<div style="font-size: 12px; color: #64748b;">${medicine.strength}</div>` : ''}
                                </div>
                                ${medicine.batch_number ? `<div style="font-size: 11px; color: #9ca3af; margin-top: 4px;">Batch: ${medicine.batch_number}</div>` : ''}
                            </div>
                        `;
                    });
                    
                    html += `</div>`;
                    html += `<div style="margin-top: 12px; padding: 8px; background: #e8f5e9; border-radius: 4px; text-align: center; color: #2e7d32; font-size: 13px; font-weight: 600;">
                        <i class="fas fa-info-circle"></i> Total: ${medicinesData.count || medicinesData.medicines.length} medicine(s) in this category
                        ${genericNamesData && genericNamesData.success && genericNamesData.count > 0 ? ` | ${genericNamesData.count} unique generic name(s)` : ''}
                    </div>`;
                    
                    list.innerHTML = html;
                } else {
                    list.innerHTML = '<div style="padding: 20px; text-align: center; color: #64748b;"><i class="fas fa-inbox"></i> No medicines found in this category</div>';
                }
            } else {
                const errorMsg = medicinesData?.message || 'Unknown error';
                console.error('Error loading medicines:', errorMsg, medicinesData);
                list.innerHTML = `<div style="padding: 20px; text-align: center; color: #ef4444;"><i class="fas fa-exclamation-triangle"></i> Error loading medicines: ${errorMsg}</div>`;
            }
        })
        .catch(error => {
            console.error('Error:', error);
            list.innerHTML = `<div style="padding: 20px; text-align: center; color: #ef4444;"><i class="fas fa-exclamation-triangle"></i> Error loading medicines: ${error.message || 'Network error'}</div>`;
        });
});

// Auto-calculate markup if unit_price and selling_price are provided
document.getElementById('unit_price')?.addEventListener('input', calculateMarkup);
document.getElementById('selling_price')?.addEventListener('input', calculateMarkup);

function calculateMarkup() {
    const unitPrice = parseFloat(document.getElementById('unit_price')?.value || 0);
    const sellingPrice = parseFloat(document.getElementById('selling_price')?.value || 0);
    
    if (unitPrice > 0 && sellingPrice > 0) {
        const markup = ((sellingPrice - unitPrice) / unitPrice) * 100;
        document.getElementById('markup_percent').value = markup.toFixed(2);
    }
}

// Auto-fill price with selling_price if price is empty
document.getElementById('selling_price')?.addEventListener('input', function() {
    const sellingPrice = parseFloat(this.value || 0);
    const priceField = document.getElementById('price');
    if (sellingPrice > 0 && (!priceField.value || priceField.value == 0)) {
        priceField.value = sellingPrice.toFixed(2);
    }
});


// Update Pricing Table based on selected medicines
function updatePricingTable() {
    const selectedCheckboxes = document.querySelectorAll('.medicine-checkbox:checked');
    const pricingTable = document.getElementById('pricingTable');
    const pricingTableBody = document.getElementById('pricingTableBody');
    const pricingEmptyMessage = document.getElementById('pricingEmptyMessage');
    
    if (selectedCheckboxes.length === 0) {
        pricingTable.style.display = 'none';
        pricingEmptyMessage.style.display = 'block';
        return;
    }
    
    pricingTable.style.display = 'table';
    pricingEmptyMessage.style.display = 'none';
    
    let tableHtml = '';
    selectedCheckboxes.forEach(checkbox => {
        const medicine = JSON.parse(checkbox.getAttribute('data-medicine'));
        const unitPrice = medicine.unit_price || 0;
        const sellingPrice = medicine.selling_price || medicine.price || 0;
        // Price should always match Selling Price (read-only, auto-synced)
        const price = sellingPrice || 0;
        const markup = unitPrice > 0 && sellingPrice > 0 ? ((sellingPrice - unitPrice) / unitPrice * 100).toFixed(2) : 0;
        
        tableHtml += `
            <tr style="border-bottom: 1px solid #e5e7eb;">
                <td style="padding: 12px;">
                    <div style="font-weight: 600; color: #1f2937;">${medicine.item_name || 'N/A'}</div>
                    <div style="font-size: 11px; color: #6b7280;">ID: ${medicine.id || 'N/A'}</div>
                </td>
                <td style="padding: 12px; text-align: right;">
                    <input type="number" class="pricing-input unit-price-input" data-medicine-id="${medicine.id || ''}" step="0.01" min="0" value="${unitPrice}" readonly style="width: 100px; padding: 6px; border: 1px solid #d1d5db; border-radius: 4px; text-align: right; background-color: #f3f4f6; cursor: not-allowed;">
                </td>
                <td style="padding: 12px; text-align: right;">
                    <input type="number" class="pricing-input selling-price-input" data-medicine-id="${medicine.id || ''}" step="0.01" min="0" value="${sellingPrice}" style="width: 100px; padding: 6px; border: 1px solid #d1d5db; border-radius: 4px; text-align: right;">
                </td>
                <td style="padding: 12px; text-align: right;">
                    <input type="number" class="pricing-input price-input" data-medicine-id="${medicine.id || ''}" step="0.01" min="0" value="${price}" readonly style="width: 100px; padding: 6px; border: 1px solid #d1d5db; border-radius: 4px; text-align: right; background-color: #f3f4f6; cursor: not-allowed;">
                </td>
                <td style="padding: 12px; text-align: right;">
                    <span class="markup-display" data-medicine-id="${medicine.id || ''}" style="font-weight: 600; color: #2e7d32;">${markup}%</span>
                </td>
                <td style="padding: 12px; text-align: center;">
                    <button type="button" class="btn btn-sm btn-outline-danger remove-pricing-btn" data-medicine-id="${medicine.id || ''}" style="font-size: 11px; padding: 4px 8px;">
                        <i class="fas fa-times"></i> Remove
                    </button>
                </td>
            </tr>
        `;
    });
    
    pricingTableBody.innerHTML = tableHtml;
    
    // Add event listeners for auto-calculation
    // Only Selling Price is editable - Unit Price and Price are read-only
    
    // Handle Selling Price changes - auto-sync Price and calculate Markup
    document.querySelectorAll('.selling-price-input').forEach(input => {
        input.addEventListener('input', function() {
            const medicineId = this.getAttribute('data-medicine-id');
            if (!medicineId) return;
            
            const unitPrice = parseFloat(document.querySelector(`.unit-price-input[data-medicine-id="${medicineId}"]`)?.value || 0);
            const sellingPrice = parseFloat(this.value || 0);
            const priceInput = document.querySelector(`.price-input[data-medicine-id="${medicineId}"]`);
            const markupDisplay = document.querySelector(`.markup-display[data-medicine-id="${medicineId}"]`);
            
            // Auto-sync Price with Selling Price (Price is read-only, so we update it automatically)
            if (priceInput) {
                priceInput.value = sellingPrice > 0 ? sellingPrice.toFixed(2) : '';
            }
            
            // Auto-calculate Markup % based on Unit Price and Selling Price
            if (unitPrice > 0 && sellingPrice > 0 && markupDisplay) {
                const markup = ((sellingPrice - unitPrice) / unitPrice) * 100;
                markupDisplay.textContent = markup.toFixed(2) + '%';
                markupDisplay.style.color = parseFloat(markup) >= 0 ? '#2e7d32' : '#ef4444';
            } else if (markupDisplay) {
                markupDisplay.textContent = '0%';
                markupDisplay.style.color = '#6b7280';
            }
        });
    });
    
    // Unit Price and Price are read-only, but we prevent any input events just in case
    document.querySelectorAll('.unit-price-input, .price-input').forEach(input => {
        input.addEventListener('input', function(e) {
            // Prevent changes to read-only fields
            e.preventDefault();
            e.stopPropagation();
        });
        
        input.addEventListener('keydown', function(e) {
            // Prevent keyboard input on read-only fields
            if (e.key !== 'Tab' && e.key !== 'Enter') {
                e.preventDefault();
            }
        });
    });
    
    // Add remove button functionality
    document.querySelectorAll('.remove-pricing-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const medicineId = this.getAttribute('data-medicine-id');
            const checkbox = document.querySelector(`.medicine-checkbox[value="${medicineId}"]`);
            if (checkbox) {
                checkbox.checked = false;
                updatePricingTable();
                // Update Select All checkbox state
                if (typeof window.updateSelectAllCheckbox === 'function') {
                    window.updateSelectAllCheckbox();
                }
            }
        });
    });
}

// Handle form submission - add quantity to selected medicines and update pricing
document.querySelector('form[action*="pharmacy/store"]')?.addEventListener('submit', function(e) {
    const quantity = document.getElementById('quantity')?.value;
    const category = document.getElementById('category')?.value;
    
    if (!category) {
        e.preventDefault();
        alert('Please select a category first');
        return false;
    }
    
    if (!quantity || quantity < 0) {
        e.preventDefault();
        alert('Please enter a valid quantity');
        document.getElementById('quantity')?.focus();
        return false;
    }
    
    // Check if any medicines are selected
    const selectedCheckboxes = document.querySelectorAll('.medicine-checkbox:checked');
    if (selectedCheckboxes.length > 0) {
        // Collect data for selected medicines: quantity addition and pricing updates
        const selectedMedicinesData = [];
        selectedCheckboxes.forEach(checkbox => {
            const medicine = JSON.parse(checkbox.getAttribute('data-medicine'));
            const medicineId = medicine.id;
            
            // Get pricing from pricing table if available
            const unitPriceInput = document.querySelector(`.unit-price-input[data-medicine-id="${medicineId}"]`);
            const sellingPriceInput = document.querySelector(`.selling-price-input[data-medicine-id="${medicineId}"]`);
            const priceInput = document.querySelector(`.price-input[data-medicine-id="${medicineId}"]`);
            
            // Get quantity value - ensure it's a number
            const qtyToAdd = parseFloat(quantity || 0);
            
            // Get pricing values - only include if they have values
            const unitPrice = unitPriceInput && unitPriceInput.value && unitPriceInput.value !== '' ? parseFloat(unitPriceInput.value) : null;
            const sellingPrice = sellingPriceInput && sellingPriceInput.value && sellingPriceInput.value !== '' ? parseFloat(sellingPriceInput.value) : null;
            const price = priceInput && priceInput.value && priceInput.value !== '' ? parseFloat(priceInput.value) : null;
            
            console.log('Adding medicine to selectedMedicinesData:', {
                medicine_id: medicineId,
                quantity_to_add: qtyToAdd,
                unit_price: unitPrice,
                selling_price: sellingPrice,
                price: price
            });
            
            selectedMedicinesData.push({
                medicine_id: medicineId,
                quantity_to_add: qtyToAdd,
                unit_price: unitPrice,
                selling_price: sellingPrice,
                price: price
            });
        });
        
        console.log('Total selectedMedicinesData:', selectedMedicinesData);
        console.log('Number of medicines to update:', selectedMedicinesData.length);
        
        // Store selected medicines data in hidden input to send to server
        // Remove any existing hidden input first
        const existingInput = this.querySelector('input[name="selected_medicines_data"]');
        if (existingInput) {
            existingInput.remove();
        }
        
        const hiddenInput = document.createElement('input');
        hiddenInput.type = 'hidden';
        hiddenInput.name = 'selected_medicines_data';
        hiddenInput.value = JSON.stringify(selectedMedicinesData);
        this.appendChild(hiddenInput);
        
        console.log('Form submitting with selectedMedicinesData:', selectedMedicinesData);
        console.log('Hidden input value:', hiddenInput.value);
        console.log('Hidden input name:', hiddenInput.name);
        console.log('Hidden input will be submitted:', hiddenInput.name + '=' + hiddenInput.value.substring(0, 100) + '...');
    } else {
        console.log('No medicines selected, removing any existing selected_medicines_data input');
        // Remove hidden input if no medicines selected
        const existingInput = this.querySelector('input[name="selected_medicines_data"]');
        if (existingInput) {
            existingInput.remove();
        }
    }
    
    console.log('Form is submitting...');
    console.log('Form action:', this.action);
    console.log('Form method:', this.method);
    
    // Don't prevent default - let form submit normally
});
</script>

<?= $this->endSection() ?>
