<?= $this->extend('template/header') ?>

<?= $this->section('title') ?>
Edit Medicine
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<style>
    .form-container {
        max-width: 800px;
        margin: 0 auto;
        background: #fff;
        border-radius: 12px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        padding: 32px;
    }
    .form-header {
        margin-bottom: 24px;
        padding-bottom: 16px;
        border-bottom: 2px solid #4caf50;
    }
    .form-title {
        font-size: 24px;
        font-weight: 700;
        color: #2e7d32;
        margin: 0;
    }
    .form-group {
        margin-bottom: 20px;
    }
    .form-label {
        display: block;
        font-weight: 600;
        color: #333;
        margin-bottom: 8px;
    }
    .form-label .required {
        color: #f44336;
    }
    .form-input {
        width: 100%;
        padding: 12px 16px;
        border: 2px solid #e0e0e0;
        border-radius: 8px;
        font-size: 14px;
        transition: all 0.3s;
    }
    .form-input:focus {
        outline: none;
        border-color: #4caf50;
        box-shadow: 0 0 0 3px rgba(76, 175, 80, 0.1);
    }
    .form-textarea {
        min-height: 100px;
        resize: vertical;
    }
    .form-actions {
        display: flex;
        gap: 12px;
        margin-top: 32px;
    }
    .btn {
        padding: 12px 24px;
        border: none;
        border-radius: 8px;
        font-size: 14px;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.3s;
    }
    .btn-primary {
        background: linear-gradient(135deg, #4caf50, #66bb6a);
        color: white;
    }
    .btn-primary:hover {
        background: linear-gradient(135deg, #388e3c, #4caf50);
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(76, 175, 80, 0.3);
    }
    .btn-secondary {
        background: #f5f5f5;
        color: #666;
    }
    .btn-secondary:hover {
        background: #e0e0e0;
    }
    .alert {
        padding: 12px 16px;
        border-radius: 8px;
        margin-bottom: 20px;
    }
    .alert-danger {
        background: #f8d7da;
        color: #721c24;
        border: 1px solid #f5c6cb;
    }
</style>

<div class="form-container">
    <div class="form-header">
        <h1 class="form-title">✏️ Edit Medicine</h1>
    </div>

    <?php if (session()->getFlashdata('error')): ?>
        <div class="alert alert-danger">
            <?= session()->getFlashdata('error') ?>
        </div>
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

    <form method="post" action="<?= site_url('pharmacy/edit-medicine/' . $medicine['id']) ?>" enctype="application/x-www-form-urlencoded">

        <div class="form-group">
            <label class="form-label">
                Medicine Name <span class="required">*</span>
            </label>
            <input 
                type="text" 
                name="item_name" 
                class="form-input" 
                placeholder="Enter medicine name"
                value="<?= old('item_name', $medicine['item_name']) ?>"
                required
            >
        </div>

        <div class="form-group">
            <label class="form-label">
                Description
            </label>
            <textarea 
                name="description" 
                class="form-input form-textarea" 
                placeholder="Enter medicine description, dosage form, etc."
            ><?= old('description', $medicine['description']) ?></textarea>
        </div>

        <div class="form-group">
            <label class="form-label">
                Quantity <span class="required">*</span>
            </label>
            <input 
                type="number" 
                name="quantity" 
                class="form-input" 
                placeholder="Enter quantity"
                value="<?= old('quantity', $medicine['quantity']) ?>"
                min="0"
                required
            >
        </div>

        <div class="form-group">
            <label class="form-label">
                Price (₱) <span class="required">*</span>
            </label>
            <input 
                type="number" 
                name="price" 
                class="form-input" 
                placeholder="Enter price per unit"
                value="<?= old('price', $medicine['price']) ?>"
                min="0"
                step="0.01"
                required
            >
        </div>

        <div class="form-actions">
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-save"></i> Update Medicine
            </button>
            <a href="<?= site_url('pharmacy/stock-monitoring') ?>" class="btn btn-secondary">
                <i class="fas fa-times"></i> Cancel
            </a>
        </div>
    </form>
</div>

<?= $this->endSection() ?>

