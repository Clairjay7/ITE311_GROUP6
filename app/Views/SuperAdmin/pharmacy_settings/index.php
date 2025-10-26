<?= $this->extend('templates/template') ?>

<?= $this->section('content') ?>
<style>
    .page-header {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        flex-wrap: wrap;
        gap: 16px;
        margin-bottom: 24px;
    }
    .page-header h1 {
        font-size: 30px;
        margin: 0;
        color: #1f2937;
    }
    .page-header p {
        margin: 6px 0 0 0;
        color: #6b7280;
    }
    .header-actions {
        display: flex;
        gap: 12px;
        flex-wrap: wrap;
    }
    .btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        padding: 10px 18px;
        border-radius: 10px;
        border: none;
        font-weight: 600;
        cursor: pointer;
        transition: background 0.2s ease, color 0.2s ease;
        text-decoration: none;
        font-size: 14px;
    }
    .btn-primary { background: #2563eb; color: #ffffff; }
    .btn-primary:hover { background: #1d4ed8; }
    .btn-secondary { background: #111827; color: #ffffff; }
    .btn-secondary:hover { background: #030712; }
    .btn-outline { background: transparent; color: #2563eb; border: 1px solid #2563eb; }
    .btn-outline:hover { background: rgba(37, 99, 235, 0.08); }

    .alert {
        padding: 14px 18px;
        border-radius: 10px;
        margin-bottom: 18px;
        border: 1px solid transparent;
        font-size: 14px;
        display: none;
    }
    .alert-success { background: #ecfdf5; border-color: #34d399; color: #047857; }
    .alert-error { background: #fef2f2; border-color: #f87171; color: #b91c1c; }

    .settings-section {
        margin-bottom: 32px;
    }
    .settings-section h2 {
        display: flex;
        align-items: center;
        gap: 10px;
        font-size: 22px;
        margin-bottom: 12px;
        color: #1f2937;
    }
    .settings-section p.section-hint {
        margin: 0 0 18px;
        color: #6b7280;
        font-size: 14px;
    }
    .settings-grid {
        display: grid;
        gap: 18px;
        grid-template-columns: repeat(auto-fit, minmax(260px, 1fr));
    }
    .setting-card {
        background: #ffffff;
        border-radius: 14px;
        border: 1px solid #e5e7eb;
        padding: 18px;
        box-shadow: 0 6px 18px rgba(15, 23, 42, 0.08);
        display: flex;
        flex-direction: column;
        gap: 12px;
    }
    .setting-card h3 {
        margin: 0;
        font-size: 16px;
        color: #111827;
    }
    .setting-card p.description {
        margin: 0;
        color: #6b7280;
        font-size: 13px;
        min-height: 36px;
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
    .form-field input[type="text"],
    .form-field input[type="number"],
    .form-field input[type="time"],
    .form-field textarea,
    .form-field select {
        padding: 10px 12px;
        border-radius: 10px;
        border: 1px solid #d1d5db;
        font-size: 14px;
        transition: border-color 0.2s ease;
        width: 100%;
    }
    .form-field textarea { min-height: 120px; resize: vertical; }
    .form-field input:focus,
    .form-field textarea:focus,
    .form-field select:focus {
        border-color: #2563eb;
        outline: none;
        box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.15);
    }
    .setting-actions {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 12px;
        margin-top: 8px;
    }
    .save-status {
        font-size: 12px;
        color: #6b7280;
    }
    .empty-placeholder {
        border: 1px dashed #d1d5db;
        border-radius: 12px;
        padding: 24px;
        text-align: center;
        color: #6b7280;
        font-size: 14px;
    }
    .toggle-wrapper {
        display: flex;
        align-items: center;
        gap: 10px;
    }
    .switch {
        position: relative;
        display: inline-block;
        width: 44px;
        height: 24px;
    }
    .switch input { opacity: 0; width: 0; height: 0; }
    .slider {
        position: absolute;
        cursor: pointer;
        top: 0; left: 0;
        right: 0; bottom: 0;
        background-color: #d1d5db;
        transition: 0.3s;
        border-radius: 999px;
    }
    .slider:before {
        position: absolute;
        content: "";
        height: 18px;
        width: 18px;
        left: 3px;
        bottom: 3px;
        background-color: white;
        transition: 0.3s;
        border-radius: 50%;
        box-shadow: 0 2px 6px rgba(0,0,0,0.15);
    }
    .switch input:checked + .slider {
        background-color: #2563eb;
    }
    .switch input:checked + .slider:before {
        transform: translateX(20px);
    }
</style>

<div class="page-header">
    <div>
        <h1>⚙️ Pharmacy Settings</h1>
        <p>Configure permissions, notifications, integrations, and security policies for the pharmacy module.</p>
    </div>
    <div class="header-actions">
        <a href="<?= base_url('super-admin/pharmacy') ?>" class="btn btn-outline">← Back to Pharmacy</a>
        <a href="<?= base_url('super-admin/unified') ?>" class="btn btn-secondary">Back to Dashboard</a>
    </div>
</div>

<?php if (session()->getFlashdata('message')): ?>
    <div class="alert alert-success" style="display:block;">✅ <?= esc(session()->getFlashdata('message')) ?></div>
<?php endif; ?>

<?php if (session()->getFlashdata('error')): ?>
    <div class="alert alert-error" style="display:block;">⚠️ <?= esc(session()->getFlashdata('error')) ?></div>
<?php endif; ?>

<div id="settings-feedback" class="alert"></div>

<?php foreach ($sections as $key => $label): ?>
    <section class="settings-section" id="section-<?= esc($key) ?>">
        <h2><?= esc($label) ?></h2>
        <p class="section-hint">
            <?php if ($key === 'staff'): ?>Manage access and approval workflows for pharmacy personnel.
            <?php elseif ($key === 'notifications'): ?>Configure system alerts and daily digests for pharmacy activity.
            <?php elseif ($key === 'integrations'): ?>Connect pharmacy operations with external systems and providers.
            <?php else: ?>Protect data with backups, encryption, and authentication controls.
            <?php endif; ?>
        </p>

        <?php $sectionSettings = $settings[$key] ?? []; ?>
        <?php if (empty($sectionSettings)): ?>
            <div class="empty-placeholder">No settings found for this category.</div>
        <?php else: ?>
            <div class="settings-grid">
                <?php foreach ($sectionSettings as $setting): ?>
                    <?php
                        $settingName = $setting['setting_name'];
                        $value = $setting['setting_value'];
                        $description = $setting['description'] ?? '';
                        $structure = 'value';
                    ?>
                    <form class="setting-card setting-form" data-category="<?= esc($key) ?>" data-setting="<?= esc($settingName) ?>" action="<?= base_url('super-admin/pharmacy-settings/update') ?>" method="post">
                        <?= csrf_field() ?>
                        <input type="hidden" name="category" value="<?= esc($key) ?>">
                        <input type="hidden" name="setting_name" value="<?= esc($settingName) ?>">

                        <div>
                            <h3><?= esc(ucwords(str_replace('_', ' ', $settingName))) ?></h3>
                            <p class="description"><?= esc($description) ?></p>
                        </div>

                        <div class="form-field">
                            <?php if ($settingName === 'allowed_roles'): ?>
                                <?php
                                    $structure = 'list';
                                    $listValue = is_array($value) ? implode(', ', $value) : (string) $value;
                                ?>
                                <label>Allowed Roles</label>
                                <input type="text" placeholder="e.g. pharmacist, assistant" value="<?= esc($listValue) ?>" data-field="list" data-type="list">
                                <small style="color:#6b7280;">Separate roles with commas.</small>
                            <?php elseif (in_array($settingName, ['require_dual_approval', 'shift_handovers_enabled', 'encryption_at_rest', 'two_factor_required'], true)): ?>
                                <?php $structure = 'boolean'; $isChecked = filter_var($value, FILTER_VALIDATE_BOOLEAN); ?>
                                <label>Status</label>
                                <div class="toggle-wrapper">
                                    <label class="switch">
                                        <input type="checkbox" data-field="value" data-type="boolean" <?= $isChecked ? 'checked' : '' ?>>
                                        <span class="slider"></span>
                                    </label>
                                    <span data-toggle-label data-on-label="Enabled" data-off-label="Disabled"><?= $isChecked ? 'Enabled' : 'Disabled' ?></span>
                                </div>
                            <?php elseif ($settingName === 'low_stock_alert'): ?>
                                <?php
                                    $structure = 'object';
                                    $enabled = (bool) ($value['enabled'] ?? false);
                                    $threshold = $value['threshold'] ?? 10;
                                ?>
                                <label>Low Stock Threshold</label>
                                <div class="toggle-wrapper">
                                    <label class="switch">
                                        <input type="checkbox" data-field="enabled" data-type="boolean" <?= $enabled ? 'checked' : '' ?>>
                                        <span class="slider"></span>
                                    </label>
                                    <span data-toggle-label data-on-label="Alerts Enabled" data-off-label="Alerts Disabled"><?= $enabled ? 'Alerts Enabled' : 'Alerts Disabled' ?></span>
                                </div>
                                <input type="number" min="1" step="1" value="<?= esc($threshold) ?>" data-field="threshold" data-type="number" placeholder="Trigger when quantity ≤">
                            <?php elseif ($settingName === 'expiry_alert'): ?>
                                <?php
                                    $structure = 'object';
                                    $enabled = (bool) ($value['enabled'] ?? false);
                                    $daysBefore = $value['days_before'] ?? 14;
                                ?>
                                <label>Expiry Reminder</label>
                                <div class="toggle-wrapper">
                                    <label class="switch">
                                        <input type="checkbox" data-field="enabled" data-type="boolean" <?= $enabled ? 'checked' : '' ?>>
                                        <span class="slider"></span>
                                    </label>
                                    <span data-toggle-label data-on-label="Alerts Enabled" data-off-label="Alerts Disabled"><?= $enabled ? 'Alerts Enabled' : 'Alerts Disabled' ?></span>
                                </div>
                                <input type="number" min="1" step="1" value="<?= esc($daysBefore) ?>" data-field="days_before" data-type="number" placeholder="Days before expiration">
                            <?php elseif ($settingName === 'daily_summary'): ?>
                                <?php
                                    $structure = 'object';
                                    $enabled = (bool) ($value['enabled'] ?? false);
                                ?>
                                <label>Daily Summary Email</label>
                                <div class="toggle-wrapper">
                                    <label class="switch">
                                        <input type="checkbox" data-field="enabled" data-type="boolean" <?= $enabled ? 'checked' : '' ?>>
                                        <span class="slider"></span>
                                    </label>
                                    <span data-toggle-label data-on-label="Enabled" data-off-label="Disabled"><?= $enabled ? 'Enabled' : 'Disabled' ?></span>
                                </div>
                            <?php elseif ($settingName === 'emr_sync'): ?>
                                <?php
                                    $structure = 'object';
                                    $enabled = (bool) ($value['enabled'] ?? false);
                                    $apiUrl = $value['api_url'] ?? '';
                                ?>
                                <label>EMR Synchronization</label>
                                <div class="toggle-wrapper">
                                    <label class="switch">
                                        <input type="checkbox" data-field="enabled" data-type="boolean" <?= $enabled ? 'checked' : '' ?>>
                                        <span class="slider"></span>
                                    </label>
                                    <span data-toggle-label data-on-label="Sync Enabled" data-off-label="Sync Disabled"><?= $enabled ? 'Sync Enabled' : 'Sync Disabled' ?></span>
                                </div>
                                <input type="text" placeholder="API URL" value="<?= esc($apiUrl) ?>" data-field="api_url" data-type="string">
                            <?php elseif ($settingName === 'sms_notifications'): ?>
                                <?php
                                    $structure = 'object';
                                    $enabled = (bool) ($value['enabled'] ?? false);
                                    $provider = $value['provider'] ?? '';
                                ?>
                                <label>SMS Notifications</label>
                                <div class="toggle-wrapper">
                                    <label class="switch">
                                        <input type="checkbox" data-field="enabled" data-type="boolean" <?= $enabled ? 'checked' : '' ?>>
                                        <span class="slider"></span>
                                    </label>
                                    <span data-toggle-label data-on-label="Enabled" data-off-label="Disabled"><?= $enabled ? 'Enabled' : 'Disabled' ?></span>
                                </div>
                                <input type="text" placeholder="Provider name" value="<?= esc($provider) ?>" data-field="provider" data-type="string">
                            <?php elseif ($settingName === 'inventory_supplier_api'): ?>
                                <?php
                                    $structure = 'object';
                                    $enabled = (bool) ($value['enabled'] ?? false);
                                    $apiKey = $value['api_key'] ?? '';
                                ?>
                                <label>Automated Supplier API</label>
                                <div class="toggle-wrapper">
                                    <label class="switch">
                                        <input type="checkbox" data-field="enabled" data-type="boolean" <?= $enabled ? 'checked' : '' ?>>
                                        <span class="slider"></span>
                                    </label>
                                    <span data-toggle-label data-on-label="Connected" data-off-label="Disconnected"><?= $enabled ? 'Connected' : 'Disconnected' ?></span>
                                </div>
                                <input type="text" placeholder="API Key" value="<?= esc($apiKey) ?>" data-field="api_key" data-type="string">
                            <?php elseif ($settingName === 'auto_backup'): ?>
                                <?php
                                    $structure = 'object';
                                    $enabled = (bool) ($value['enabled'] ?? false);
                                    $time = $value['time'] ?? '02:00';
                                ?>
                                <label>Automated Backup</label>
                                <div class="toggle-wrapper">
                                    <label class="switch">
                                        <input type="checkbox" data-field="enabled" data-type="boolean" <?= $enabled ? 'checked' : '' ?>>
                                        <span class="slider"></span>
                                    </label>
                                    <span data-toggle-label data-on-label="Enabled" data-off-label="Disabled"><?= $enabled ? 'Enabled' : 'Disabled' ?></span>
                                </div>
                                <input type="time" value="<?= esc($time) ?>" data-field="time" data-type="string">
                            <?php else: ?>
                                <?php
                                    $structure = is_array($value) ? 'json' : 'value';
                                    $displayValue = is_array($value) ? json_encode($value, JSON_PRETTY_PRINT) : (string) $value;
                                ?>
                                <label>Value</label>
                                <?php if ($structure === 'json'): ?>
                                    <textarea data-field="json" data-type="json" placeholder='{ "enabled": true }'><?= esc($displayValue) ?></textarea>
                                    <small style="color:#6b7280;">Use valid JSON format.</small>
                                <?php else: ?>
                                    <input type="text" value="<?= esc($displayValue) ?>" data-field="value" data-type="string">
                                <?php endif; ?>
                            <?php endif; ?>
                        </div>

                        <div class="setting-actions">
                            <span class="save-status" data-save-status>Last updated just now</span>
                            <button type="submit" class="btn btn-primary">Save Changes</button>
                        </div>
                    </form>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </section>
<?php endforeach; ?>

<script>
(function() {
    const forms = document.querySelectorAll('.setting-form');
    const feedbackEl = document.getElementById('settings-feedback');

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

    const formatValue = (value) => {
        if (Array.isArray(value)) {
            return value.join(', ');
        }
        if (value && typeof value === 'object') {
            return JSON.stringify(value, null, 2);
        }
        return value;
    };

    const handleFormSubmit = async (event) => {
        event.preventDefault();
        const form = event.currentTarget;
        const submitButton = form.querySelector('button[type="submit"]');
        const statusEl = form.querySelector('[data-save-status]');
        const structure = form.dataset.structure || determineStructure(form);

        if (submitButton) {
            submitButton.disabled = true;
            submitButton.textContent = 'Saving...';
        }
        if (statusEl) {
            statusEl.textContent = 'Saving...';
        }

        try {
            const payloadValue = buildPayloadValue(form, structure);
            if (payloadValue === undefined) {
                throw new Error('invalid');
            }

            const formData = new FormData(form);
            formData.append('setting_value', serializeValue(payloadValue));

            const response = await fetch(form.action || '<?= base_url('super-admin/pharmacy-settings/update') ?>', {
                method: 'POST',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json',
                },
                body: formData,
            });

            const result = await response.json();

            updateCsrfTokens(result?.csrfToken, result?.csrfHash);

            if (!response.ok || !result.success) {
                showFeedback(result?.error || 'Failed to update setting.', 'error');
                if (statusEl) statusEl.textContent = 'Save failed';
                return;
            }

            showFeedback(result.message || 'Setting updated successfully.', 'success');
            if (statusEl) statusEl.textContent = 'Saved just now';
            reflectSavedValue(form, payloadValue);
        } catch (error) {
            showFeedback('Please review your input. Valid JSON or numeric values may be required.', 'error');
            if (statusEl) statusEl.textContent = 'Validation error';
        } finally {
            if (submitButton) {
                submitButton.disabled = false;
                submitButton.textContent = 'Save Changes';
            }
        }
    };

    const determineStructure = (form) => {
        if (form.querySelector('[data-field="json"]')) return 'json';
        if (form.querySelector('[data-field="list"]')) return 'list';

        const fields = Array.from(form.querySelectorAll('[data-field]'));
        const booleanOnly = fields.length === 1 && fields[0].dataset.type === 'boolean';
        if (booleanOnly) return 'boolean';

        if (fields.length > 1) return 'object';
        if (fields.length === 1) {
            return fields[0].dataset.type === 'boolean' ? 'boolean' : 'value';
        }

        return 'value';
    };

    const buildPayloadValue = (form, structure) => {
        if (structure === 'list') {
            const input = form.querySelector('[data-field="list"]');
            if (!input) return [];
            return input.value
                .split(',')
                .map((item) => item.trim())
                .filter((item) => item.length > 0);
        }

        if (structure === 'boolean') {
            const input = form.querySelector('[data-field][data-type="boolean"]');
            return input ? input.checked : false;
        }

        if (structure === 'json') {
            const textarea = form.querySelector('[data-field="json"]');
            if (!textarea) return {};
            const value = textarea.value.trim();
            if (value === '') return {};
            return JSON.parse(value);
        }

        if (structure === 'object') {
            const inputs = form.querySelectorAll('[data-field]');
            const data = {};
            inputs.forEach((input) => {
                const field = input.dataset.field;
                const type = input.dataset.type || 'string';
                if (type === 'boolean') {
                    data[field] = input.checked;
                } else if (type === 'number') {
                    const numeric = input.value === '' ? null : Number(input.value);
                    data[field] = isNaN(numeric) ? null : numeric;
                } else {
                    data[field] = input.value;
                }
            });
            return data;
        }

        const input = form.querySelector('[data-field="value"]');
        return input ? input.value : '';
    };

    const serializeValue = (value) => {
        if (typeof value === 'boolean') {
            return value ? 'true' : 'false';
        }
        if (Array.isArray(value) || (value && typeof value === 'object')) {
            return JSON.stringify(value);
        }
        return value ?? '';
    };

    const reflectSavedValue = (form, value) => {
        if (Array.isArray(value)) {
            const input = form.querySelector('[data-field="list"]');
            if (input) {
                input.value = formatValue(value);
            }
        } else if (value && typeof value === 'object') {
            const jsonField = form.querySelector('[data-field="json"]');
            if (jsonField) {
                jsonField.value = formatValue(value);
            }
            form.querySelectorAll('[data-field][data-type]').forEach((input) => {
                const field = input.dataset.field;
                if (!(field in value)) return;
                const fieldValue = value[field];
                if (input.dataset.type === 'boolean') {
                    input.checked = !!fieldValue;
                    const toggleLabel = input.closest('.toggle-wrapper')?.querySelector('[data-toggle-label]');
                    if (toggleLabel) {
                        toggleLabel.textContent = fieldValue ? (toggleLabel.dataset.onLabel || 'Enabled') : (toggleLabel.dataset.offLabel || 'Disabled');
                    }
                } else {
                    input.value = fieldValue ?? '';
                }
            });
        } else {
            const input = form.querySelector('[data-field="value"]');
            if (input && input.dataset.type === 'boolean') {
                input.checked = !!value;
                const toggleLabel = input.closest('.toggle-wrapper')?.querySelector('[data-toggle-label]');
                if (toggleLabel) {
                    toggleLabel.textContent = value ? (toggleLabel.dataset.onLabel || 'Enabled') : (toggleLabel.dataset.offLabel || 'Disabled');
                }
            } else if (input) {
                input.value = value;
            }
        }
    };

    forms.forEach((form) => {
        const structureOverride = determineStructure(form);
        form.dataset.structure = structureOverride;
        form.addEventListener('submit', handleFormSubmit);

        form.querySelectorAll('[data-field][data-type="boolean"]').forEach((toggle) => {
            toggle.addEventListener('change', (event) => {
                const input = event.currentTarget;
                const toggleLabel = input.closest('.toggle-wrapper')?.querySelector('[data-toggle-label]');
                if (toggleLabel) {
                    const onLabel = toggleLabel.dataset.onLabel || 'Enabled';
                    const offLabel = toggleLabel.dataset.offLabel || 'Disabled';
                    toggleLabel.textContent = input.checked ? onLabel : offLabel;
                }
            });
        });
    });
})();
</script>

<?= $this->endSection() ?>
