<?= $this->extend('template/header') ?>

<?= $this->section('title') ?><?= esc($title) ?><?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="admin-module">
    <div class="module-header">
        <h2><?= esc($title) ?></h2>
        <a href="<?= base_url('admin/patients') ?>" class="btn btn-secondary">Back to List</a>
    </div>

    <?php if (session()->getFlashdata('errors')): ?>
        <div class="alert alert-error">
            <?php foreach (session()->getFlashdata('errors') as $error): ?>
                <p><?= esc($error) ?></p>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <form method="POST" action="<?= base_url('admin/patients/update/' . $patient['id']) ?>" class="form-container">
        <div class="form-group">
            <label for="firstname">First Name *</label>
            <input type="text" id="firstname" name="firstname" class="form-control" value="<?= old('firstname', $patient['firstname']) ?>" required>
        </div>

        <div class="form-group">
            <label for="lastname">Last Name *</label>
            <input type="text" id="lastname" name="lastname" class="form-control" value="<?= old('lastname', $patient['lastname']) ?>" required>
        </div>

        <div class="form-group">
            <label for="birthdate">Birthdate *</label>
            <input type="date" id="birthdate" name="birthdate" class="form-control" value="<?= old('birthdate', $patient['birthdate']) ?>" required>
        </div>

        <div class="form-group">
            <label for="gender">Gender *</label>
            <select id="gender" name="gender" class="form-control" required>
                <option value="">Select Gender</option>
                <option value="male" <?= old('gender', $patient['gender']) === 'male' ? 'selected' : '' ?>>Male</option>
                <option value="female" <?= old('gender', $patient['gender']) === 'female' ? 'selected' : '' ?>>Female</option>
                <option value="other" <?= old('gender', $patient['gender']) === 'other' ? 'selected' : '' ?>>Other</option>
            </select>
        </div>

        <div class="form-group">
            <label for="contact">Contact</label>
            <input type="text" id="contact" name="contact" class="form-control" value="<?= old('contact', $patient['contact']) ?>">
        </div>

        <div class="form-group">
            <label for="address">Address</label>
            <textarea id="address" name="address" class="form-control" rows="3"><?= old('address', $patient['address']) ?></textarea>
        </div>

        <div class="form-group">
            <label for="doctor_id">Assign Doctor</label>
            <select id="doctor_id" name="doctor_id" class="form-control">
                <option value="">Select Doctor (Optional)</option>
                <?php if (!empty($doctors)): ?>
                    <?php foreach ($doctors as $doctor): ?>
                        <option value="<?= esc($doctor['id']) ?>" <?= old('doctor_id', $patient['doctor_id'] ?? null) == $doctor['id'] ? 'selected' : '' ?>>
                            <?= esc($doctor['username']) ?> <?= !empty($doctor['email']) ? '(' . esc($doctor['email']) . ')' : '' ?>
                        </option>
                    <?php endforeach; ?>
                <?php else: ?>
                    <option value="">No doctors available</option>
                <?php endif; ?>
            </select>
        </div>

        <div class="form-actions">
            <button type="submit" class="btn btn-primary">Update Patient</button>
            <a href="<?= base_url('admin/patients') ?>" class="btn btn-secondary">Cancel</a>
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
.alert { padding: 12px; border-radius: 6px; margin-bottom: 16px; }
.alert-error { background: #fee2e2; color: #b91c1c; }
</style>
<?= $this->endSection() ?>

