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
        margin: 6px 0 0;
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
    .settings-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
        gap: 24px;
    }
    .settings-card {
        background: #ffffff;
        border-radius: 16px;
        border: 1px solid #e5e7eb;
        padding: 20px;
        box-shadow: 0 6px 18px rgba(15, 23, 42, 0.08);
        display: flex;
        flex-direction: column;
        gap: 16px;
    }
    .settings-card h3 {
        margin: 0;
        font-size: 20px;
        color: #1f2937;
        display: flex;
        align-items: center;
        gap: 8px;
    }
    .settings-card p {
        margin: 0;
        color: #6b7280;
        font-size: 13px;
    }
    .setting-field {
        display: flex;
        flex-direction: column;
        gap: 6px;
    }
    .setting-field label {
        font-size: 13px;
        font-weight: 600;
        color: #1f2937;
        display: flex;
        justify-content: space-between;
        gap: 8px;
    }
    .setting-field label span {
        font-size: 12px;
        font-weight: 400;
        color: #94a3b8;
    }
    .setting-field input,
    .setting-field textarea,
    .setting-field select {
        padding: 10px 12px;
        border-radius: 10px;
        border: 1px solid #d1d5db;
        font-size: 14px;
    }
    .setting-field textarea {
        min-height: 90px;
        resize: vertical;
    }
    .setting-actions {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 12px;
        margin-top: 10px;
    }
    .save-status {
        font-size: 12px;
        color: #64748b;
    }
    .section-description {
        background: #f8fafc;
        border-radius: 12px;
        padding: 12px 14px;
        border: 1px solid #e2e8f0;
        font-size: 13px;
        color: #475569;
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
        <h1 class="page-title">⚙️ Laboratory Settings</h1>
        <p class="page-subtitle">Configure laboratory operations, test standards, staff permissions, and integrations.</p>
    </div>
    <div class="header-actions">
        <a href="<?= base_url('super-admin/laboratory') ?>" class="btn btn-outline">← Back to Laboratory</a>
        <a href="<?= base_url('super-admin/unified') ?>" class="btn btn-secondary">Back to Dashboard</a>
    </div>
</div>

<div id="lab-settings-feedback" class="alert"></div>

<div class="settings-grid">
    <?php foreach ($settings as $category => $items): ?>
        <form class="settings-card lab-settings-form" data-category="<?= esc($category) ?>" action="<?= base_url('super-admin/laboratory-settings/update') ?>" method="post">
            <?= csrf_field() ?>
            <input type="hidden" name="category" value="<?= esc($category) ?>">
            <?php if ($category === 'test_parameters'): ?>
                <h3>🧪 Test Parameters</h3>
                <div class="section-description">Manage measurement units and automated rules for laboratory tests.</div>
            <?php elseif ($category === 'reference_ranges'): ?>
                <h3>📏 Reference Ranges</h3>
                <div class="section-description">Set default clinical reference ranges used in lab reports.</div>
            <?php elseif ($category === 'staff_permissions'): ?>
                <h3>👩‍🔬 Staff Permissions</h3>
                <div class="section-description">Control staff capabilities for editing, verifying, and signing-off results.</div>
            <?php elseif ($category === 'integrations'): ?>
                <h3>🔗 Integration Settings</h3>
                <div class="section-description">Manage external systems connected to the laboratory (EMR, LIS, etc.).</div>
            <?php endif; ?>

            <div class="settings-fields" style="display: grid; gap: 16px;">
                <?php foreach ($items as $name => $data): ?>
                    <?php
                        $value = $data['value'];
                        $description = $data['description'] ?? '';
                        $fieldId = $category . '_' . $name;
                        $isArray = is_array($value);
                    ?>
                    <div class="setting-field">
                        <label for="<?= esc($fieldId) ?>">
                            <?= esc(ucwords(str_replace(['_', '-'], ' ', $name))) ?>
                            <?php if ($description): ?>
                                <span><?= esc($description) ?></span>
                            <?php endif; ?>
                        </label>
                        <?php if (is_bool($value)): ?>
                            <select id="<?= esc($fieldId) ?>" data-setting-name="<?= esc($name) ?>" data-setting-type="boolean">
                                <option value="1" <?= $value ? 'selected' : '' ?>>Enabled</option>
                                <option value="0" <?= !$value ? 'selected' : '' ?>>Disabled</option>
                            </select>
                        <?php elseif ($isArray): ?>
                            <textarea id="<?= esc($fieldId) ?>" data-setting-name="<?= esc($name) ?>" data-setting-type="json" placeholder='{"key":"value"}'><?= esc(json_encode($value, JSON_PRETTY_PRINT)) ?></textarea>
                        <?php else: ?>
                            <input id="<?= esc($fieldId) ?>" type="text" value="<?= esc($value) ?>" data-setting-name="<?= esc($name) ?>" data-setting-type="string">
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            </div>

            <div class="setting-actions">
                <span class="save-status" data-save-status>Last updated just now</span>
                <button type="submit" class="btn btn-primary">Save Changes</button>
            </div>
        </form>
    <?php endforeach; ?>
</div>

<script>
(function() {
    const feedbackEl = document.getElementById('lab-settings-feedback');

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

    const collectSettings = (form) => {
        const settings = {};
        form.querySelectorAll('[data-setting-name]').forEach((input) => {
            const name = input.dataset.settingName;
            const type = input.dataset.settingType;
            let value = input.value;
            if (type === 'boolean') {
                value = input.value === '1' || input.value === 'true';
            } else if (type === 'json') {
                try {
                    value = JSON.parse(input.value || '{}');
                } catch (error) {
                    throw new Error(`Invalid JSON for ${name}.`);
                }
            }
            settings[name] = { value };
        });
        return settings;
    };

    document.querySelectorAll('.lab-settings-form').forEach((form) => {
        form.addEventListener('submit', async (event) => {
            event.preventDefault();
            const category = form.dataset.category;
            const submitButton = form.querySelector('button[type="submit"]');
            const statusLabel = form.querySelector('[data-save-status]');

            try {
                const settings = collectSettings(form);
                if (submitButton) {
                    submitButton.disabled = true;
                    submitButton.textContent = 'Saving...';
                }

                const response = await fetch(form.action, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json',
                    },
                    body: JSON.stringify({
                        category,
                        settings,
                    }),
                });
                const result = await response.json();

                updateCsrfTokens(result?.csrfToken, result?.csrfHash);

                if (!response.ok || !result.success) {
                    showFeedback(result?.error || 'Failed to update settings.', 'error');
                    return;
                }

                showFeedback(result.message || 'Settings updated successfully.', 'success');
                if (statusLabel) {
                    statusLabel.textContent = 'Saved just now';
                }
            } catch (error) {
                showFeedback(error.message || 'Unexpected error updating settings.', 'error');
            } finally {
                if (submitButton) {
                    submitButton.disabled = false;
                    submitButton.textContent = 'Save Changes';
                }
            }
        });
    });
})();
</script>

<?= $this->endSection() ?>
