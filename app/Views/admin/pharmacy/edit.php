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

    <form method="POST" action="<?= base_url('admin/pharmacy/update/' . $pharmacyItem['id']) ?>" class="form-container">
        <!-- 1. Item Information -->
        <div class="form-section">
            <h3 class="section-title">1. Item Information</h3>
            
            <div class="form-row">
                <div class="form-group full-width">
                    <label for="item_name">Medicine Name <span class="required">*</span></label>
                    <input type="text" id="item_name" name="item_name" class="form-control" value="<?= old('item_name', $pharmacyItem['item_name'] ?? '') ?>" required placeholder="e.g., Paracetamol">
                </div>
            </div>
            
            <div class="form-row">
                <div class="form-group">
                    <label for="generic_name">Generic Name <span class="optional">(optional)</span></label>
                    <select id="generic_name" name="generic_name" class="form-select">
                        <option value="">-- Select Generic Name --</option>
                        <?php 
                        $selectedGenericName = old('generic_name', $pharmacyItem['generic_name'] ?? '');
                        foreach (($genericNames ?? []) as $genericName): 
                        ?>
                            <option value="<?= esc($genericName) ?>" <?= $selectedGenericName === $genericName ? 'selected' : '' ?>>
                                <?= esc($genericName) ?>
                            </option>
                        <?php endforeach; ?>
                        <?php if (!empty($selectedGenericName) && !in_array($selectedGenericName, $genericNames ?? [])): ?>
                            <option value="<?= esc($selectedGenericName) ?>" selected>
                                <?= esc($selectedGenericName) ?> (Current)
                            </option>
                        <?php endif; ?>
                    </select>
                    <div class="form-hint">Select from existing generic names or leave blank</div>
                </div>
                
                <div class="form-group">
                    <label for="category">Category <span class="required">*</span></label>
                    <select id="category" name="category" class="form-select" required>
                        <option value="">-- Select Category --</option>
                        <option value="Analgesics / Antipyretics" <?= (old('category', $pharmacyItem['category'] ?? '') === 'Analgesics / Antipyretics') ? 'selected' : '' ?>>1. Analgesics / Antipyretics (Pain + Fever)</option>
                        <option value="Anti-inflammatory (NSAIDs)" <?= (old('category', $pharmacyItem['category'] ?? '') === 'Anti-inflammatory (NSAIDs)') ? 'selected' : '' ?>>2. Anti-inflammatory (NSAIDs) (Pamamaga + pain)</option>
                        <option value="Antibiotics" <?= (old('category', $pharmacyItem['category'] ?? '') === 'Antibiotics') ? 'selected' : '' ?>>3. Antibiotics (Pang-infection)</option>
                        <option value="Antihistamines" <?= (old('category', $pharmacyItem['category'] ?? '') === 'Antihistamines') ? 'selected' : '' ?>>4. Antihistamines (Pang-allergy)</option>
                        <option value="Cough & Cold (Respiratory)" <?= (old('category', $pharmacyItem['category'] ?? '') === 'Cough & Cold (Respiratory)') ? 'selected' : '' ?>>5. Cough & Cold (Respiratory) (Ubo, sipon, bronchitis)</option>
                        <option value="Gastrointestinal Medicines" <?= (old('category', $pharmacyItem['category'] ?? '') === 'Gastrointestinal Medicines') ? 'selected' : '' ?>>6. Gastrointestinal Medicines (Antacid, PPI, Antiemetic, Anti-diarrheal)</option>
                        <option value="Cardiovascular Medicines" <?= (old('category', $pharmacyItem['category'] ?? '') === 'Cardiovascular Medicines') ? 'selected' : '' ?>>7. Cardiovascular Medicines (Antihypertensive, anti-cholesterol)</option>
                        <option value="Diabetic Medicines" <?= (old('category', $pharmacyItem['category'] ?? '') === 'Diabetic Medicines') ? 'selected' : '' ?>>8. Diabetic Medicines (Insulin + oral meds like Metformin)</option>
                        <option value="Vitamins & Supplements" <?= (old('category', $pharmacyItem['category'] ?? '') === 'Vitamins & Supplements') ? 'selected' : '' ?>>9. Vitamins & Supplements</option>
                        <option value="IV Fluids / Electrolytes" <?= (old('category', $pharmacyItem['category'] ?? '') === 'IV Fluids / Electrolytes') ? 'selected' : '' ?>>10. IV Fluids / Electrolytes (NSS, D5LR, LR, etc.)</option>
                        <option value="Emergency Drugs" <?= (old('category', $pharmacyItem['category'] ?? '') === 'Emergency Drugs') ? 'selected' : '' ?>>11. Emergency Drugs (Crash cart essentials: Epinephrine, Atropine, etc.)</option>
                        <option value="Medical Supplies" <?= (old('category', $pharmacyItem['category'] ?? '') === 'Medical Supplies') ? 'selected' : '' ?>>12. Medical Supplies (Syringe, gauze, catheter, etc.)</option>
                    </select>
                </div>
            </div>
            
            <div class="form-row">
                <div class="form-group full-width">
                    <label for="description">Description <span class="optional">(optional)</span></label>
                    <textarea id="description" name="description" class="form-control" rows="3" placeholder="Additional information about the medicine"><?= old('description', $pharmacyItem['description'] ?? '') ?></textarea>
                </div>
            </div>
        </div>

        <!-- 2. Dosage & Strength -->
        <div class="form-section">
            <h3 class="section-title">2. Dosage & Strength</h3>
            
            <div class="form-row">
                <div class="form-group">
                    <label for="strength">Strength <span class="optional">(optional)</span></label>
                    <input type="text" id="strength" name="strength" class="form-control" value="<?= old('strength', $pharmacyItem['strength'] ?? '') ?>" placeholder="e.g., 500mg, 250mg/5ml">
                    <div class="form-hint">Enter the strength of the medicine</div>
                </div>
                
                <div class="form-group">
                    <label for="dosage_form">Dosage Form <span class="optional">(optional)</span></label>
                    <select id="dosage_form" name="dosage_form" class="form-select">
                        <option value="">-- Select Dosage Form --</option>
                        <option value="Tablet" <?= (old('dosage_form', $pharmacyItem['dosage_form'] ?? '') === 'Tablet') ? 'selected' : '' ?>>Tablet</option>
                        <option value="Capsule" <?= (old('dosage_form', $pharmacyItem['dosage_form'] ?? '') === 'Capsule') ? 'selected' : '' ?>>Capsule</option>
                        <option value="Syrup" <?= (old('dosage_form', $pharmacyItem['dosage_form'] ?? '') === 'Syrup') ? 'selected' : '' ?>>Syrup</option>
                        <option value="Injection" <?= (old('dosage_form', $pharmacyItem['dosage_form'] ?? '') === 'Injection') ? 'selected' : '' ?>>Injection</option>
                        <option value="Ointment" <?= (old('dosage_form', $pharmacyItem['dosage_form'] ?? '') === 'Ointment') ? 'selected' : '' ?>>Ointment</option>
                        <option value="Drops" <?= (old('dosage_form', $pharmacyItem['dosage_form'] ?? '') === 'Drops') ? 'selected' : '' ?>>Drops</option>
                        <option value="Other" <?= (old('dosage_form', $pharmacyItem['dosage_form'] ?? '') === 'Other') ? 'selected' : '' ?>>Other</option>
                    </select>
                </div>
            </div>
        </div>

        <!-- 3. Inventory Details -->
        <div class="form-section">
            <h3 class="section-title">3. Inventory Details</h3>
            
            <div class="form-row">
                <div class="form-group">
                    <label for="quantity">Quantity / Stock <span class="required">*</span></label>
                    <input type="number" id="quantity" name="quantity" min="0" class="form-control" value="<?= old('quantity', $pharmacyItem['quantity'] ?? 0) ?>" required>
                </div>
                
                <div class="form-group">
                    <label for="reorder_level">Reorder Level <span class="required">*</span></label>
                    <input type="number" id="reorder_level" name="reorder_level" min="0" class="form-control" value="<?= old('reorder_level', $pharmacyItem['reorder_level'] ?? 10) ?>" required>
                    <div class="form-hint">Alert will be shown when stock reaches this level</div>
                </div>
            </div>
            
            <div class="form-row">
                <div class="form-group">
                    <label for="batch_number">Batch Number <span class="required">*</span></label>
                    <select id="batch_number" name="batch_number" class="form-select" required>
                        <option value="">-- Select Batch Number --</option>
                        <?php 
                        $selectedBatchNumber = old('batch_number', $pharmacyItem['batch_number'] ?? '');
                        foreach (($batchNumbers ?? []) as $batchNumber): 
                        ?>
                            <option value="<?= esc($batchNumber) ?>" <?= $selectedBatchNumber === $batchNumber ? 'selected' : '' ?>>
                                <?= esc($batchNumber) ?>
                            </option>
                        <?php endforeach; ?>
                        <?php if (!empty($selectedBatchNumber) && !in_array($selectedBatchNumber, $batchNumbers ?? [])): ?>
                            <option value="<?= esc($selectedBatchNumber) ?>" selected>
                                <?= esc($selectedBatchNumber) ?> (Current)
                            </option>
                        <?php endif; ?>
                    </select>
                    <div class="form-hint">Required for inventory tracking</div>
                </div>
                
                <div class="form-group">
                    <label for="expiration_date">Expiration Date <span class="required">*</span></label>
                    <input type="date" id="expiration_date" name="expiration_date" class="form-control" value="<?= old('expiration_date', $pharmacyItem['expiration_date'] ?? '') ?>" required>
                    <div class="form-hint">Required for expiration tracking</div>
                </div>
            </div>
            
            <div class="form-row">
                <div class="form-group">
                    <label for="supplier_name">Supplier <span class="required">*</span></label>
                    <select id="supplier_name" name="supplier_name" class="form-select" required>
                        <option value="">-- Select Supplier --</option>
                        <?php 
                        $selectedSupplier = old('supplier_name', $pharmacyItem['supplier_name'] ?? '');
                        foreach (($suppliers ?? []) as $supplier): 
                        ?>
                            <option value="<?= esc($supplier) ?>" <?= $selectedSupplier === $supplier ? 'selected' : '' ?>>
                                <?= esc($supplier) ?>
                            </option>
                        <?php endforeach; ?>
                        <?php if (!empty($selectedSupplier) && !in_array($selectedSupplier, $suppliers ?? [])): ?>
                            <option value="<?= esc($selectedSupplier) ?>" selected>
                                <?= esc($selectedSupplier) ?> (Current)
                            </option>
                        <?php endif; ?>
                    </select>
                    <div class="form-hint">Required for inventory tracking</div>
                </div>
                
                <div class="form-group">
                    <label for="supplier_contact">Supplier Contact <span class="optional">(optional)</span></label>
                    <input type="text" id="supplier_contact" name="supplier_contact" class="form-control" value="<?= old('supplier_contact', $pharmacyItem['supplier_contact'] ?? '') ?>" placeholder="Phone or Email">
                </div>
            </div>
        </div>

        <!-- 4. Pricing -->
        <div class="form-section">
            <h3 class="section-title">4. Pricing</h3>
            
            <div class="form-row">
                <div class="form-group">
                    <label for="unit_price">Unit Price <span class="optional">(optional)</span></label>
                    <input type="number" id="unit_price" name="unit_price" step="0.01" min="0" class="form-control" value="<?= old('unit_price', $pharmacyItem['unit_price'] ?? '') ?>" placeholder="0.00">
                    <div class="form-hint">Cost price per unit</div>
                </div>
                
                <div class="form-group">
                    <label for="selling_price">Selling Price <span class="optional">(optional)</span></label>
                    <input type="number" id="selling_price" name="selling_price" step="0.01" min="0" class="form-control" value="<?= old('selling_price', $pharmacyItem['selling_price'] ?? '') ?>" placeholder="0.00">
                    <div class="form-hint">Price to sell to patients</div>
                </div>
            </div>
            
            <div class="form-row">
                <div class="form-group">
                    <label for="price">Price <span class="required">*</span></label>
                    <input type="number" id="price" name="price" step="0.01" min="0" class="form-control" value="<?= old('price', $pharmacyItem['price'] ?? 0) ?>" required placeholder="0.00">
                    <div class="form-hint">Main price field (used if selling price is not set)</div>
                </div>
                
                <div class="form-group">
                    <label for="markup_percent">Markup % <span class="optional">(optional)</span></label>
                    <input type="number" id="markup_percent" name="markup_percent" step="0.01" min="0" max="1000" class="form-control" value="<?= old('markup_percent', $pharmacyItem['markup_percent'] ?? '') ?>" placeholder="0.00">
                    <div class="form-hint">Markup percentage (auto-calculated if unit and selling price are set)</div>
                </div>
            </div>
        </div>

        <!-- 5. Status -->
        <div class="form-section">
            <h3 class="section-title">5. Status</h3>
            
            <div class="form-row">
                <div class="form-group">
                    <label for="status">Status <span class="required">*</span></label>
                    <select id="status" name="status" class="form-select" required>
                        <option value="active" <?= (old('status', $pharmacyItem['status'] ?? 'active') === 'active') ? 'selected' : '' ?>>Active</option>
                        <option value="inactive" <?= (old('status', $pharmacyItem['status'] ?? '') === 'inactive') ? 'selected' : '' ?>>Inactive</option>
                    </select>
                    <div class="form-hint">Inactive items will not appear in prescription queue</div>
                </div>
            </div>
        </div>

        <div class="form-actions">
            <button type="submit" class="btn btn-primary">Update Item</button>
            <a href="<?= base_url('admin/pharmacy') ?>" class="btn btn-secondary">Cancel</a>
        </div>
    </form>
</div>

<script>
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
</script>

<?= $this->endSection() ?>
