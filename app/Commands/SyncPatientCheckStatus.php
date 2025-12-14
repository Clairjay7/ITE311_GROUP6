<?php

namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;

class SyncPatientCheckStatus extends BaseCommand
{
    protected $group       = 'Database';
    protected $name        = 'patients:sync-check-status';
    protected $description = 'Sync all patient check statuses - unlock Check button if all orders are completed';

    public function run(array $params)
    {
        $db = \Config\Database::connect();
        
        if (!$db->tableExists('admin_patients') || !$db->tableExists('doctor_orders')) {
            CLI::write('Required tables do not exist.', 'red');
            return;
        }
        
        CLI::write('Syncing patient check statuses...', 'yellow');
        
        // Get all patients with pending_order status
        $patientsWithPendingOrder = $db->table('admin_patients')
            ->where('doctor_check_status', 'pending_order')
            ->get()
            ->getResultArray();
        
        CLI::write("Found " . count($patientsWithPendingOrder) . " patients with 'pending_order' status.", 'cyan');
        
        $unlockedCount = 0;
        $stillPendingCount = 0;
        
        foreach ($patientsWithPendingOrder as $patient) {
            $patientId = $patient['id'];
            
            // Check if there are any pending orders for this patient
            $pendingOrders = $db->table('doctor_orders')
                ->where('patient_id', $patientId)
                ->where('status !=', 'completed')
                ->where('status !=', 'cancelled')
                ->countAllResults();
            
            // If no pending orders, unlock the Check button
            if ($pendingOrders == 0) {
                $updateData = [
                    'is_doctor_checked' => 0,
                    'doctor_check_status' => 'available', // Unlock Check button
                    'nurse_vital_status' => 'completed',
                ];
                
                // Add doctor_order_status only if column exists
                if ($db->fieldExists('doctor_order_status', 'admin_patients')) {
                    $updateData['doctor_order_status'] = 'not_required';
                }
                
                if ($db->table('admin_patients')->where('id', $patientId)->update($updateData)) {
                    // Also update patients table if corresponding record exists
                    if ($db->tableExists('patients')) {
                        $nameParts = [
                            $patient['firstname'] ?? '',
                            $patient['lastname'] ?? ''
                        ];
                        
                        if (!empty($nameParts[0]) && !empty($nameParts[1])) {
                            $hmsPatient = $db->table('patients')
                                ->where('first_name', $nameParts[0])
                                ->where('last_name', $nameParts[1])
                                ->where('doctor_id', $patient['doctor_id'] ?? null)
                                ->get()
                                ->getRowArray();
                            
                            if ($hmsPatient) {
                                $db->table('patients')
                                    ->where('patient_id', $hmsPatient['patient_id'])
                                    ->update($updateData);
                            }
                        }
                    }
                    
                    $unlockedCount++;
                    $patientName = ($patient['firstname'] ?? '') . ' ' . ($patient['lastname'] ?? '');
                    CLI::write("  ✓ Unlocked Check button for patient: {$patientName} (ID: {$patientId})", 'green');
                } else {
                    CLI::write("  ✗ Failed to unlock patient ID: {$patientId}", 'red');
                }
            } else {
                $stillPendingCount++;
                $patientName = ($patient['firstname'] ?? '') . ' ' . ($patient['lastname'] ?? '');
                CLI::write("  ⚠ Patient {$patientName} (ID: {$patientId}) still has {$pendingOrders} pending order(s)", 'yellow');
            }
        }
        
        CLI::newLine();
        CLI::write("Sync completed!", 'green');
        CLI::write("  - Unlocked: {$unlockedCount} patients", 'green');
        CLI::write("  - Still pending: {$stillPendingCount} patients", 'yellow');
    }
}

