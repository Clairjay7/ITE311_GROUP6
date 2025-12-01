<?= $this->extend('template/header') ?>

<?= $this->section('title') ?>
Medicine Release
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<style>
    .medicine-container {
        background: #fff;
        border-radius: 12px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        padding: 24px;
        margin-bottom: 24px;
    }
    .medicine-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 20px;
        padding-bottom: 16px;
        border-bottom: 2px solid #4caf50;
    }
    .medicine-title {
        font-size: 24px;
        font-weight: 700;
        color: #2e7d32;
    }
    .search-box {
        padding: 10px 16px;
        border: 2px solid #4caf50;
        border-radius: 8px;
        width: 300px;
        font-size: 14px;
    }
    .medicine-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
        gap: 20px;
    }
    .medicine-card {
        background: #fff;
        border: 2px solid #e0e0e0;
        border-radius: 12px;
        padding: 20px;
        transition: all 0.3s;
    }
    .medicine-card:hover {
        border-color: #4caf50;
        box-shadow: 0 4px 12px rgba(76, 175, 80, 0.2);
        transform: translateY(-4px);
    }
    .medicine-name {
        font-size: 18px;
        font-weight: 700;
        color: #2e7d32;
        margin-bottom: 8px;
    }
    .medicine-description {
        font-size: 14px;
        color: #666;
        margin-bottom: 12px;
        min-height: 40px;
    }
    .medicine-info {
        display: flex;
        justify-content: space-between;
        margin-bottom: 12px;
        padding: 8px;
        background: #f5f5f5;
        border-radius: 6px;
    }
    .medicine-quantity {
        font-size: 16px;
        font-weight: 600;
    }
    .quantity-high {
        color: #4caf50;
    }
    .quantity-medium {
        color: #ff9800;
    }
    .quantity-low {
        color: #f44336;
    }
    .medicine-price {
        font-size: 18px;
        font-weight: 700;
        color: #2e7d32;
    }
    .btn-release {
        width: 100%;
        background: linear-gradient(135deg, #4caf50, #66bb6a);
        color: white;
        border: none;
        padding: 10px;
        border-radius: 8px;
        cursor: pointer;
        font-size: 14px;
        font-weight: 600;
        transition: all 0.3s;
    }
    .btn-release:hover {
        background: linear-gradient(135deg, #388e3c, #4caf50);
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(76, 175, 80, 0.3);
    }
    .btn-release:disabled {
        background: #ccc;
        cursor: not-allowed;
    }
</style>

<div class="medicine-container">
    <div class="medicine-header">
        <h1 class="medicine-title">💊 Medicine Release</h1>
        <input type="text" class="search-box" id="searchMedicine" placeholder="Search medicine..." onkeyup="searchMedicines()">
    </div>

    <div class="medicine-grid" id="medicineGrid">
        <?php foreach ($medicines as $medicine): ?>
            <?php
                $quantity = $medicine['quantity'];
                $quantityClass = 'quantity-high';
                if ($quantity == 0) {
                    $quantityClass = 'quantity-low';
                } elseif ($quantity < 10) {
                    $quantityClass = 'quantity-low';
                } elseif ($quantity < 20) {
                    $quantityClass = 'quantity-medium';
                }
            ?>
            <div class="medicine-card" data-name="<?= strtolower(esc($medicine['item_name'])) ?>">
                <div class="medicine-name"><?= esc($medicine['item_name']) ?></div>
                <div class="medicine-description"><?= esc($medicine['description'] ?? 'No description') ?></div>
                <div class="medicine-info">
                    <div>
                        <small>Stock:</small>
                        <div class="medicine-quantity <?= $quantityClass ?>">
                            <?= $quantity ?> units
                        </div>
                    </div>
                    <div>
                        <small>Price:</small>
                        <div class="medicine-price">₱<?= number_format($medicine['price'], 2) ?></div>
                    </div>
                </div>
                <button 
                    class="btn-release" 
                    onclick="releaseMedicine(<?= $medicine['id'] ?>, '<?= esc($medicine['item_name']) ?>', <?= $quantity ?>)"
                    <?= $quantity == 0 ? 'disabled' : '' ?>
                >
                    <?= $quantity == 0 ? 'Out of Stock' : 'Release Medicine' ?>
                </button>
            </div>
        <?php endforeach; ?>
    </div>
</div>

<script>
function searchMedicines() {
    const searchTerm = document.getElementById('searchMedicine').value.toLowerCase();
    const cards = document.querySelectorAll('.medicine-card');
    
    cards.forEach(card => {
        const name = card.getAttribute('data-name');
        if (name.includes(searchTerm)) {
            card.style.display = 'block';
        } else {
            card.style.display = 'none';
        }
    });
}

function releaseMedicine(medicineId, medicineName, currentStock) {
    const quantity = prompt(`Release ${medicineName}\nCurrent Stock: ${currentStock}\n\nEnter quantity to release:`);
    
    if (quantity === null || quantity === '') {
        return;
    }

    const qty = parseInt(quantity);
    
    if (isNaN(qty) || qty <= 0) {
        alert('Please enter a valid quantity');
        return;
    }

    if (qty > currentStock) {
        alert('Insufficient stock! Available: ' + currentStock);
        return;
    }

    if (confirm(`Release ${qty} units of ${medicineName}?`)) {
        updateStock(medicineId, -qty);
    }
}

async function updateStock(medicineId, changeAmount) {
    try {
        const formData = new FormData();
        formData.append('quantity', Math.abs(changeAmount));
        formData.append('action', changeAmount > 0 ? 'add' : 'subtract');

        const response = await fetch(`<?= site_url('pharmacy/update-stock/') ?>${medicineId}`, {
            method: 'POST',
            body: formData
        });

        const data = await response.json();

        if (data.success) {
            alert(data.message);
            location.reload();
        } else {
            alert('Error: ' + data.message);
        }
    } catch (error) {
        console.error('Error:', error);
        alert('Failed to update stock');
    }
}
</script>

<?= $this->endSection() ?>

