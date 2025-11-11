<?= $this->extend('template/header') ?>

<?= $this->section('title') ?>Dashboard<?= $this->endSection() ?>

<?= $this->section('content') ?>
<?php
$role = session('role') ?? null;
$name = session('name') ?? 'User';

switch ($role) {
    case 'admin':
        echo view('Admin/dashboard', ['name' => $name]);
        break;
    case 'doctor':
        echo view('Doctor/dashboard', ['name' => $name]);
        break;
    case 'nurse':
        echo view('Nurse/dashboard', ['name' => $name]);
        break;
    case 'receptionist':
        echo view('Reception/dashboard', ['name' => $name]);
        break;
    case 'accounting':
        echo view('Accountant/dashboard', ['name' => $name]);
        break;
    case 'pharmacist':
        echo view('Pharmacy/dashboard', ['name' => $name]);
        break;
    case 'labstaff':
        echo view('LabStaff/dashboard', ['name' => $name]);
        break;
    case 'itstaff':
        echo view('ITStaff/dashboard', ['name' => $name]);
        break;
    default:
        echo view('errors/html/error_403');
        break;
}
?>
<?= $this->endSection() ?>