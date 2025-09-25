<?= $this->extend('templates/template') ?>

<?= $this->section('content') ?>
<h1>👤 Profile</h1>
<p>Personal information at preferences.</p>

<div class="card">
    <h5>Personal Info</h5>
    <div class="actions-row"><span>Name: <?= esc(session()->get('first_name') . ' ' . session()->get('last_name')) ?></span></div>
    <div class="actions-row"><span>Email: <?= esc(session()->get('email')) ?></span></div>
</div>
<?= $this->endSection() ?>


