<?= $this->extend('template/header') ?>

<?= $this->section('title') ?><?= esc($title) ?><?= $this->endSection() ?>

<?= $this->section('content') ?>
<style>
    .stock-container {
        background: #fff;
        border-radius: 12px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        padding: 24px;
        margin-bottom: 24px;
    }
    .stock-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 20px;
        padding-bottom: 16px;
        border-bottom: 2px solid #4caf50;
    }
    .stock-title {
        font-size: 24px;
        font-weight: 700;
        color: #2e7d32;
        margin: 0;
    }
    .btn-add {
        background: linear-gradient(135deg, #4caf50, #66bb6a);
        color: white;
        border: none;
        padding: 10px 20px;
        border-radius: 8px;
        cursor: pointer;
        font-size: 14px;
        font-weight: 600;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 8px;
    }
    .btn-add:hover {
        background: linear-gradient(135deg, #388e3c, #4caf50);
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(76, 175, 80, 0.3);
    }
    .stock-tabs {
        display: flex;
        gap: 12px;
        margin-bottom: 20px;
        border-bottom: 2px solid #e0e0e0;
    }
    .tab {
        padding: 12px 24px;
        background: transparent;
        border: none;
        border-bottom: 3px solid transparent;
        cursor: pointer;
        font-size: 14px;
        font-weight: 600;
        color: #666;
        transition: all 0.3s;
        text-decoration: none;
        display: inline-block;
    }
    .tab.active {
        color: #2e7d32;
        border-bottom-color: #4caf50;
    }
    .tab:hover {
        color: #2e7d32;
    }
    .pagination {
        display: flex;
        justify-content: center;
        align-items: center;
        gap: 8px;
        margin-top: 24px;
        padding: 16px;
    }
    .pagination a, .pagination span {
        padding: 8px 12px;
        border-radius: 6px;
        text-decoration: none;
        font-size: 14px;
        font-weight: 600;
        color: #475569;
        background: #f1f5f9;
        transition: all 0.3s;
        display: inline-block;
        min-width: 40px;
        text-align: center;
    }
    .pagination a:hover {
        background: #2e7d32;
        color: white;
    }
    .pagination .active {
        background: #2e7d32;
        color: white;
    }
    .pagination .disabled {
        opacity: 0.5;
        cursor: not-allowed;
        pointer-events: none;
    }
    .pagination-info {
        margin: 0 16px;
        color: #64748b;
        font-size: 14px;
    }
    .stock-table {
        width: 100%;
        border-collapse: collapse;
    }
    .stock-table th {
        background: #f1f8e9;
        padding: 12px;
        text-align: left;
        font-weight: 600;
        color: #2e7d32;
        border-bottom: 2px solid #4caf50;
    }
    .stock-table td {
        padding: 12px;
        border-bottom: 1px solid #e0e0e0;
    }
    .stock-table tr:hover {
        background: #f9fbe7;
    }
    .stock-level {
        padding: 4px 12px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: 600;
        text-transform: uppercase;
    }
    .stock-critical {
        background: #ffebee;
        color: #c62828;
    }
    .stock-low {
        background: #fff3e0;
        color: #e65100;
    }
    .stock-normal {
        background: #e8f5e9;
        color: #2e7d32;
    }
    .tab-content {
        display: none;
    }
    .tab-content.active {
        display: block;
    }
    .modern-card {
        background: white;
        border-radius: 8px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    }
    .form-control-modern {
        width: 100%;
        padding: 12px 16px;
        border: 2px solid #e0e0e0;
        border-radius: 8px;
        font-size: 14px;
        transition: border-color 0.3s;
    }
    .form-control-modern:focus {
        outline: none;
        border-color: #2e7d32;
    }
    .btn-modern {
        padding: 12px 20px;
        border: none;
        border-radius: 8px;
        cursor: pointer;
        font-size: 14px;
        font-weight: 600;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        transition: all 0.3s;
    }
    .btn-modern:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0,0,0,0.1);
    }
    .alert {
        padding: 12px;
        border-radius: 6px;
        margin-bottom: 16px;
    }
    .alert-success {
        background: #d4edda;
        color: #155724;
    }
</style>

<div class="admin-module">
    <div class="stock-container">
        <div class="stock-header">
            <h1 class="stock-title">📊 Stock Monitoring</h1>
            <a href="<?= base_url('admin/pharmacy/create') ?>" class="btn-add">
                <i class="fas fa-plus"></i> Add New Medicine
            </a>
        </div>

        <?php if (session()->getFlashdata('success')): ?>
            <div class="alert alert-success">
                <?= session()->getFlashdata('success') ?>
            </div>
        <?php endif; ?>

        <!-- Search Bar and Category Filter -->
        <div class="modern-card" style="margin-bottom: 20px; padding: 20px;">
            <div style="display: flex; align-items: center; gap: 16px; flex-wrap: wrap;">
                <div style="flex: 1; min-width: 300px; position: relative;">
                    <i class="fas fa-search" style="position: absolute; left: 16px; top: 50%; transform: translateY(-50%); color: #94a3b8; font-size: 16px;"></i>
                    <input type="text" id="stockSearchInput" class="form-control-modern" placeholder="🔍 Search medicines by name, category, or batch number..." style="padding-left: 45px; font-size: 14px;">
                </div>
                <div style="min-width: 250px;">
                    <select id="categoryFilter" class="form-control-modern" style="font-size: 14px; cursor: pointer;">
                        <option value="">All Categories</option>
                        <?php foreach ($validCategories ?? [] as $category): ?>
                            <option value="<?= esc($category) ?>" <?= ($selectedCategory ?? '') === $category ? 'selected' : '' ?>>
                                <?= esc($category) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <button type="button" id="clearSearchBtn" class="btn-modern" style="background: #f1f5f9; color: #475569; display: none;">
                    <i class="fas fa-times"></i> Clear
                </button>
            </div>
        </div>

        <div class="stock-tabs">
            <?php
            $categoryParam = !empty($selectedCategory) ? '&category=' . urlencode($selectedCategory) : '';
            ?>
            <a href="<?= base_url('admin/stock?tab=all&page=1' . $categoryParam) ?>" class="tab <?= ($currentTab ?? 'all') === 'all' ? 'active' : '' ?>">
                All Medicines (<?= $totalAll ?? 0 ?>)
            </a>
            <a href="<?= base_url('admin/stock?tab=critical&page=1' . $categoryParam) ?>" class="tab <?= ($currentTab ?? '') === 'critical' ? 'active' : '' ?>">
                Critical (<?= $totalCritical ?? 0 ?>)
            </a>
            <a href="<?= base_url('admin/stock?tab=low&page=1' . $categoryParam) ?>" class="tab <?= ($currentTab ?? '') === 'low' ? 'active' : '' ?>">
                Low Stock (<?= $totalLow ?? 0 ?>)
            </a>
        </div>

        <!-- All Medicines Tab -->
        <div id="stock-all" class="tab-content <?= ($currentTab ?? 'all') === 'all' ? 'active' : '' ?>">
            <table class="stock-table">
                <thead>
                    <tr>
                        <th>Medicine Name</th>
                        <th>Category</th>
                        <th>Quantity</th>
                        <th>Batch Number</th>
                        <th>Expiration Date</th>
                        <th>Supplier</th>
                        <th>Price</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($allMedicines ?? [])): ?>
                        <tr>
                            <td colspan="8" style="text-align: center; padding: 40px; color: #666;">
                                No medicines found
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach (($allMedicines ?? []) as $medicine): ?>
                            <?php
                                $quantity = $medicine['quantity'] ?? 0;
                                $reorderLevel = $medicine['reorder_level'] ?? 10;
                                $statusClass = 'stock-normal';
                                $statusText = 'NORMAL';
                                $qtyColor = '#2e7d32';
                                if ($quantity == 0 || $quantity <= $reorderLevel) {
                                    $statusClass = 'stock-critical';
                                    $statusText = 'CRITICAL';
                                    $qtyColor = '#ef4444';
                                } elseif ($quantity < ($reorderLevel * 2)) {
                                    $statusClass = 'stock-low';
                                    $statusText = 'LOW';
                                    $qtyColor = '#f59e0b';
                                }
                                
                                $expDate = !empty($medicine['expiration_date']) ? new \DateTime($medicine['expiration_date']) : null;
                                $isExpiringSoon = $expDate && $expDate <= new \DateTime('+30 days');
                                $isExpired = $expDate && $expDate < new \DateTime();
                                $expColor = $isExpired ? '#ef4444' : ($isExpiringSoon ? '#f59e0b' : '#2e7d32');
                            ?>
                            <tr class="stock-row" data-tab="all">
                                <td><strong><?= esc($medicine['item_name']) ?></strong></td>
                                <td><?= esc($medicine['category'] ?? 'N/A') ?></td>
                                <td><strong style="color: <?= $qtyColor ?>;"><?= $medicine['quantity'] ?></strong></td>
                                <td><span style="font-family: monospace; color: #2e7d32;"><?= esc($medicine['batch_number'] ?? 'N/A') ?></span></td>
                                <td style="color: <?= $expColor ?>;">
                                    <?= $expDate ? $expDate->format('M d, Y') : 'N/A' ?>
                                    <?= $isExpired ? ' <i class="fas fa-exclamation-circle"></i>' : ($isExpiringSoon ? ' <i class="fas fa-exclamation-triangle"></i>' : '') ?>
                                </td>
                                <td><?= esc($medicine['supplier_name'] ?? 'N/A') ?></td>
                                <td>₱<?= number_format($medicine['price'] ?? 0, 2) ?></td>
                                <td>
                                    <span class="stock-level <?= $statusClass ?>"><?= $statusText ?></span>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
            <?php if (($currentTab ?? 'all') === 'all' && !empty($pager) && $pager['totalPages'] > 1): ?>
                <div class="pagination">
                    <?php
                    $categoryParam = !empty($selectedCategory) ? '&category=' . urlencode($selectedCategory) : '';
                    ?>
                    <?php if ($pager['hasPrev']): ?>
                        <a href="<?= base_url('admin/stock?tab=all&page=' . ($pager['currentPage'] - 1) . $categoryParam) ?>">
                            <i class="fas fa-chevron-left"></i> Prev
                        </a>
                    <?php else: ?>
                        <span class="disabled"><i class="fas fa-chevron-left"></i> Prev</span>
                    <?php endif; ?>
                    
                    <?php
                    $startPage = max(1, $pager['currentPage'] - 2);
                    $endPage = min($pager['totalPages'], $pager['currentPage'] + 2);
                    
                    if ($startPage > 1): ?>
                        <a href="<?= base_url('admin/stock?tab=all&page=1' . $categoryParam) ?>">1</a>
                        <?php if ($startPage > 2): ?>
                            <span>...</span>
                        <?php endif; ?>
                    <?php endif; ?>
                    
                    <?php for ($i = $startPage; $i <= $endPage; $i++): ?>
                        <?php if ($i == $pager['currentPage']): ?>
                            <span class="active"><?= $i ?></span>
                        <?php else: ?>
                            <a href="<?= base_url('admin/stock?tab=all&page=' . $i . $categoryParam) ?>"><?= $i ?></a>
                        <?php endif; ?>
                    <?php endfor; ?>
                    
                    <?php if ($endPage < $pager['totalPages']): ?>
                        <?php if ($endPage < $pager['totalPages'] - 1): ?>
                            <span>...</span>
                        <?php endif; ?>
                        <a href="<?= base_url('admin/stock?tab=all&page=' . $pager['totalPages'] . $categoryParam) ?>"><?= $pager['totalPages'] ?></a>
                    <?php endif; ?>
                    
                    <?php if ($pager['hasNext']): ?>
                        <a href="<?= base_url('admin/stock?tab=all&page=' . ($pager['currentPage'] + 1) . $categoryParam) ?>">
                            Next <i class="fas fa-chevron-right"></i>
                        </a>
                    <?php else: ?>
                        <span class="disabled">Next <i class="fas fa-chevron-right"></i></span>
                    <?php endif; ?>
                    
                    <span class="pagination-info">
                        Showing <?= (($pager['currentPage'] - 1) * $pager['perPage']) + 1 ?>-<?= min($pager['currentPage'] * $pager['perPage'], $pager['totalItems']) ?> of <?= $pager['totalItems'] ?>
                    </span>
                </div>
            <?php endif; ?>
        </div>

        <!-- Critical Stock Tab -->
        <div id="stock-critical" class="tab-content <?= ($currentTab ?? '') === 'critical' ? 'active' : '' ?>">
            <table class="stock-table">
                <thead>
                    <tr>
                        <th>Medicine Name</th>
                        <th>Category</th>
                        <th>Quantity</th>
                        <th>Batch Number</th>
                        <th>Expiration Date</th>
                        <th>Supplier</th>
                        <th>Price</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($criticalStock ?? [])): ?>
                        <tr>
                            <td colspan="8" style="text-align: center; padding: 40px; color: #666;">
                                No critical stock items
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach (($criticalStock ?? []) as $medicine): ?>
                            <?php
                                $expDate = !empty($medicine['expiration_date']) ? new \DateTime($medicine['expiration_date']) : null;
                                $isExpiringSoon = $expDate && $expDate <= new \DateTime('+30 days');
                                $isExpired = $expDate && $expDate < new \DateTime();
                                $expColor = $isExpired ? '#ef4444' : ($isExpiringSoon ? '#f59e0b' : '#2e7d32');
                            ?>
                            <tr class="stock-row" data-tab="critical">
                                <td><strong><?= esc($medicine['item_name']) ?></strong></td>
                                <td><?= esc($medicine['category'] ?? 'N/A') ?></td>
                                <td><strong style="color: #ef4444;"><?= $medicine['quantity'] ?></strong></td>
                                <td><span style="font-family: monospace; color: #2e7d32;"><?= esc($medicine['batch_number'] ?? 'N/A') ?></span></td>
                                <td style="color: <?= $expColor ?>;">
                                    <?= $expDate ? $expDate->format('M d, Y') : 'N/A' ?>
                                    <?= $isExpired ? ' <i class="fas fa-exclamation-circle"></i>' : ($isExpiringSoon ? ' <i class="fas fa-exclamation-triangle"></i>' : '') ?>
                                </td>
                                <td><?= esc($medicine['supplier_name'] ?? 'N/A') ?></td>
                                <td>₱<?= number_format($medicine['price'] ?? 0, 2) ?></td>
                                <td>
                                    <span class="stock-level stock-critical">CRITICAL</span>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
            <?php if (($currentTab ?? 'critical') === 'critical' && !empty($pager) && $pager['totalPages'] > 1): ?>
                <div class="pagination">
                    <?php
                    $categoryParam = !empty($selectedCategory) ? '&category=' . urlencode($selectedCategory) : '';
                    ?>
                    <?php if ($pager['hasPrev']): ?>
                        <a href="<?= base_url('admin/stock?tab=critical&page=' . ($pager['currentPage'] - 1) . $categoryParam) ?>">
                            <i class="fas fa-chevron-left"></i> Prev
                        </a>
                    <?php else: ?>
                        <span class="disabled"><i class="fas fa-chevron-left"></i> Prev</span>
                    <?php endif; ?>
                    
                    <?php
                    $startPage = max(1, $pager['currentPage'] - 2);
                    $endPage = min($pager['totalPages'], $pager['currentPage'] + 2);
                    
                    if ($startPage > 1): ?>
                        <a href="<?= base_url('admin/stock?tab=critical&page=1' . $categoryParam) ?>">1</a>
                        <?php if ($startPage > 2): ?>
                            <span>...</span>
                        <?php endif; ?>
                    <?php endif; ?>
                    
                    <?php for ($i = $startPage; $i <= $endPage; $i++): ?>
                        <?php if ($i == $pager['currentPage']): ?>
                            <span class="active"><?= $i ?></span>
                        <?php else: ?>
                            <a href="<?= base_url('admin/stock?tab=critical&page=' . $i . $categoryParam) ?>"><?= $i ?></a>
                        <?php endif; ?>
                    <?php endfor; ?>
                    
                    <?php if ($endPage < $pager['totalPages']): ?>
                        <?php if ($endPage < $pager['totalPages'] - 1): ?>
                            <span>...</span>
                        <?php endif; ?>
                        <a href="<?= base_url('admin/stock?tab=critical&page=' . $pager['totalPages'] . $categoryParam) ?>"><?= $pager['totalPages'] ?></a>
                    <?php endif; ?>
                    
                    <?php if ($pager['hasNext']): ?>
                        <a href="<?= base_url('admin/stock?tab=critical&page=' . ($pager['currentPage'] + 1) . $categoryParam) ?>">
                            Next <i class="fas fa-chevron-right"></i>
                        </a>
                    <?php else: ?>
                        <span class="disabled">Next <i class="fas fa-chevron-right"></i></span>
                    <?php endif; ?>
                    
                    <span class="pagination-info">
                        Showing <?= (($pager['currentPage'] - 1) * $pager['perPage']) + 1 ?>-<?= min($pager['currentPage'] * $pager['perPage'], $pager['totalItems']) ?> of <?= $pager['totalItems'] ?>
                    </span>
                </div>
            <?php endif; ?>
        </div>

        <!-- Low Stock Tab -->
        <div id="stock-low" class="tab-content <?= ($currentTab ?? '') === 'low' ? 'active' : '' ?>">
            <table class="stock-table">
                <thead>
                    <tr>
                        <th>Medicine Name</th>
                        <th>Category</th>
                        <th>Quantity</th>
                        <th>Batch Number</th>
                        <th>Expiration Date</th>
                        <th>Supplier</th>
                        <th>Price</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($lowStock ?? [])): ?>
                        <tr>
                            <td colspan="8" style="text-align: center; padding: 40px; color: #666;">
                                No low stock items
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach (($lowStock ?? []) as $medicine): ?>
                            <?php
                                $expDate = !empty($medicine['expiration_date']) ? new \DateTime($medicine['expiration_date']) : null;
                                $isExpiringSoon = $expDate && $expDate <= new \DateTime('+30 days');
                                $isExpired = $expDate && $expDate < new \DateTime();
                                $expColor = $isExpired ? '#ef4444' : ($isExpiringSoon ? '#f59e0b' : '#2e7d32');
                            ?>
                            <tr class="stock-row" data-tab="low">
                                <td><strong><?= esc($medicine['item_name']) ?></strong></td>
                                <td><?= esc($medicine['category'] ?? 'N/A') ?></td>
                                <td><strong style="color: #f59e0b;"><?= $medicine['quantity'] ?></strong></td>
                                <td><span style="font-family: monospace; color: #2e7d32;"><?= esc($medicine['batch_number'] ?? 'N/A') ?></span></td>
                                <td style="color: <?= $expColor ?>;">
                                    <?= $expDate ? $expDate->format('M d, Y') : 'N/A' ?>
                                    <?= $isExpired ? ' <i class="fas fa-exclamation-circle"></i>' : ($isExpiringSoon ? ' <i class="fas fa-exclamation-triangle"></i>' : '') ?>
                                </td>
                                <td><?= esc($medicine['supplier_name'] ?? 'N/A') ?></td>
                                <td>₱<?= number_format($medicine['price'] ?? 0, 2) ?></td>
                                <td>
                                    <span class="stock-level stock-low">LOW</span>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
            <?php if (($currentTab ?? '') === 'low' && !empty($pager) && $pager['totalPages'] > 1): ?>
                <div class="pagination">
                    <?php
                    $categoryParam = !empty($selectedCategory) ? '&category=' . urlencode($selectedCategory) : '';
                    ?>
                    <?php if ($pager['hasPrev']): ?>
                        <a href="<?= base_url('admin/stock?tab=low&page=' . ($pager['currentPage'] - 1) . $categoryParam) ?>">
                            <i class="fas fa-chevron-left"></i> Prev
                        </a>
                    <?php else: ?>
                        <span class="disabled"><i class="fas fa-chevron-left"></i> Prev</span>
                    <?php endif; ?>
                    
                    <?php
                    $startPage = max(1, $pager['currentPage'] - 2);
                    $endPage = min($pager['totalPages'], $pager['currentPage'] + 2);
                    
                    if ($startPage > 1): ?>
                        <a href="<?= base_url('admin/stock?tab=low&page=1' . $categoryParam) ?>">1</a>
                        <?php if ($startPage > 2): ?>
                            <span>...</span>
                        <?php endif; ?>
                    <?php endif; ?>
                    
                    <?php for ($i = $startPage; $i <= $endPage; $i++): ?>
                        <?php if ($i == $pager['currentPage']): ?>
                            <span class="active"><?= $i ?></span>
                        <?php else: ?>
                            <a href="<?= base_url('admin/stock?tab=low&page=' . $i . $categoryParam) ?>"><?= $i ?></a>
                        <?php endif; ?>
                    <?php endfor; ?>
                    
                    <?php if ($endPage < $pager['totalPages']): ?>
                        <?php if ($endPage < $pager['totalPages'] - 1): ?>
                            <span>...</span>
                        <?php endif; ?>
                        <a href="<?= base_url('admin/stock?tab=low&page=' . $pager['totalPages'] . $categoryParam) ?>"><?= $pager['totalPages'] ?></a>
                    <?php endif; ?>
                    
                    <?php if ($pager['hasNext']): ?>
                        <a href="<?= base_url('admin/stock?tab=low&page=' . ($pager['currentPage'] + 1) . $categoryParam) ?>">
                            Next <i class="fas fa-chevron-right"></i>
                        </a>
                    <?php else: ?>
                        <span class="disabled">Next <i class="fas fa-chevron-right"></i></span>
                    <?php endif; ?>
                    
                    <span class="pagination-info">
                        Showing <?= (($pager['currentPage'] - 1) * $pager['perPage']) + 1 ?>-<?= min($pager['currentPage'] * $pager['perPage'], $pager['totalItems']) ?> of <?= $pager['totalItems'] ?>
                    </span>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<script>
// Tab switching is now handled by links, so this function is not needed
// But keeping it for backward compatibility if needed

document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('stockSearchInput');
    const clearSearchBtn = document.getElementById('clearSearchBtn');
    const categoryFilter = document.getElementById('categoryFilter');
    const stockRows = document.querySelectorAll('.stock-row');
    
    // Category filter change handler
    if (categoryFilter) {
        categoryFilter.addEventListener('change', function() {
            const selectedCategory = this.value;
            const currentTab = '<?= $currentTab ?? "all" ?>';
            const currentPage = 1; // Reset to page 1 when category changes
            
            let url = '<?= base_url("admin/stock") ?>?tab=' + currentTab + '&page=' + currentPage;
            if (selectedCategory) {
                url += '&category=' + encodeURIComponent(selectedCategory);
            }
            
            window.location.href = url;
        });
    }
    
    // Function to get searchable text from a row
    function getRowSearchText(row) {
        const cells = row.querySelectorAll('td');
        if (cells.length < 8) return '';
        
        const medicineName = cells[0]?.textContent?.trim() || '';
        const category = cells[1]?.textContent?.trim() || '';
        const quantity = cells[2]?.textContent?.trim() || '';
        const batchNumber = cells[3]?.textContent?.trim() || '';
        const expirationDate = cells[4]?.textContent?.trim() || '';
        const supplier = cells[5]?.textContent?.trim() || '';
        const price = cells[6]?.textContent?.trim() || '';
        const status = cells[7]?.textContent?.trim() || '';
        
        return `${medicineName} ${category} ${quantity} ${batchNumber} ${expirationDate} ${supplier} ${price} ${status}`.toLowerCase();
    }
    
    // Function to filter rows based on search
    window.filterStockTable = function() {
        const searchTerm = searchInput.value.toLowerCase().trim();
        const hasSearch = searchTerm.length > 0;
        
        // Show/hide clear button
        if (hasSearch) {
            clearSearchBtn.style.display = 'inline-flex';
        } else {
            clearSearchBtn.style.display = 'none';
        }
        
        // Get active tab
        const activeTab = document.querySelector('.tab.active');
        const activeTabName = activeTab ? activeTab.textContent.toLowerCase() : '';
        let currentTab = 'all';
        if (activeTabName.includes('critical')) {
            currentTab = 'critical';
        } else if (activeTabName.includes('low')) {
            currentTab = 'low';
        }
        
        let visibleCount = 0;
        
        stockRows.forEach(row => {
            const rowTab = row.getAttribute('data-tab');
            const rowText = getRowSearchText(row);
            
            // Check if row belongs to active tab
            const tabMatch = (currentTab === 'all' || rowTab === currentTab);
            
            // Check search filter
            let searchMatch = true;
            if (hasSearch) {
                searchMatch = rowText.includes(searchTerm);
            }
            
            // Show/hide row
            if (tabMatch && searchMatch) {
                row.style.display = '';
                visibleCount++;
            } else {
                row.style.display = 'none';
            }
        });
    };
    
    // Search functionality
    if (searchInput) {
        searchInput.addEventListener('input', function() {
            filterStockTable();
        });
        
        // Clear search
        if (clearSearchBtn) {
            clearSearchBtn.addEventListener('click', function() {
                searchInput.value = '';
                filterStockTable();
                searchInput.focus();
            });
        }
    }
});
</script>
<?= $this->endSection() ?>
