<?= $this->extend('templates/template') ?>

<?= $this->section('content') ?>
<div class="container">
    <div class="row">
        <div class="col-12">
            <h1>SuperAdmin Dashboard</h1>
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Welcome, Super Admin!</h5>
                    <p class="card-text">This is your dashboard where you can manage the system.</p>
                    <!-- Add your dashboard content here -->
                </div>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
