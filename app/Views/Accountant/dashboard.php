<?= $this->extend('templates/template') ?>

<?= $this->section('content') ?>
<h1>💼 Accountant Dashboard</h1>
<p>Financial Management & Billing</p>

<div class="spacer"></div>
<div class="grid grid-2">
    <div class="card"><h5>Today's Revenue</h5><h3>₱<?= number_format($todayRevenue, 2) ?></h3></div>
    <div class="card"><h5>Pending Bills</h5><h3><?= count($pendingBills) ?></h3></div>
    <div class="card"><h5>Insurance Claims</h5><h3><?= count($insuranceClaims) ?></h3></div>
    <div class="card"><h5>Outstanding Balance</h5><h3>₱<?= number_format($outstandingBalance, 2) ?></h3></div>
</div>
<?= $this->endSection() ?>
