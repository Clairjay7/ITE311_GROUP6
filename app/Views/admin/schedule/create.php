<?= $this->extend('template/header') ?>

<?= $this->section('title') ?><?= esc($title) ?><?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="admin-module">
    <div class="module-header">
        <h2><?= esc($title) ?></h2>
        <a href="<?= base_url('admin/schedule') ?>" class="btn btn-secondary">
            <i class="fa-solid fa-arrow-left"></i> Back
        </a>
    </div>

    <div class="schedule-type-selection">
        <div class="selection-card" onclick="window.location.href='<?= base_url('admin/schedule/create-doctor') ?>'">
            <div class="card-icon" style="background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);">
                <i class="fa-solid fa-user-doctor"></i>
            </div>
            <h3>Doctor Schedule</h3>
            <p>Create working schedule for doctors</p>
            <div class="card-arrow">
                <i class="fa-solid fa-arrow-right"></i>
            </div>
        </div>

        <div class="selection-card" onclick="window.location.href='<?= base_url('admin/schedule/create-nurse') ?>'">
            <div class="card-icon" style="background: linear-gradient(135deg, #10b981 0%, #059669 100%);">
                <i class="fa-solid fa-user-nurse"></i>
            </div>
            <h3>Nurse Schedule</h3>
            <p>Create working schedule for nurses</p>
            <div class="card-arrow">
                <i class="fa-solid fa-arrow-right"></i>
            </div>
        </div>
    </div>
</div>

<style>
.admin-module { 
    padding: 24px; 
    background: #f8fafc;
    min-height: 100vh;
}

.module-header { 
    display: flex; 
    justify-content: space-between; 
    align-items: center; 
    margin-bottom: 32px;
}

.module-header h2 { 
    margin: 0; 
    color: #2e7d32; 
    font-size: 28px;
}

.btn { 
    padding: 10px 20px; 
    border-radius: 6px; 
    text-decoration: none; 
    display: inline-flex;
    align-items: center;
    gap: 8px;
    font-weight: 600;
    transition: all 0.3s;
}

.btn-secondary {
    background: #6b7280;
    color: white;
}

.btn-secondary:hover {
    background: #4b5563;
}

.schedule-type-selection {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
    gap: 24px;
    max-width: 800px;
    margin: 0 auto;
}

.selection-card {
    background: white;
    border-radius: 16px;
    padding: 32px;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
    cursor: pointer;
    transition: all 0.3s;
    position: relative;
    overflow: hidden;
}

.selection-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 8px 24px rgba(0, 0, 0, 0.12);
}

.selection-card:hover .card-arrow {
    transform: translateX(8px);
}

.card-icon {
    width: 80px;
    height: 80px;
    border-radius: 16px;
    display: flex;
    align-items: center;
    justify-content: center;
    margin-bottom: 20px;
    color: white;
    font-size: 36px;
}

.selection-card h3 {
    margin: 0 0 8px 0;
    color: #1e293b;
    font-size: 24px;
    font-weight: 700;
}

.selection-card p {
    margin: 0 0 20px 0;
    color: #64748b;
    font-size: 14px;
}

.card-arrow {
    position: absolute;
    bottom: 24px;
    right: 24px;
    color: #94a3b8;
    font-size: 20px;
    transition: all 0.3s;
}

@media (max-width: 768px) {
    .schedule-type-selection {
        grid-template-columns: 1fr;
    }
}
</style>
<?= $this->endSection() ?>
