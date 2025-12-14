<?php

namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;

class SyncLabTestOrders extends BaseCommand
{
    protected $group       = 'Database';
    protected $name        = 'orders:sync-lab-tests';
    protected $description = 'Sync all completed lab test results with their corresponding doctor orders';

    public function run(array $params)
    {
        $db = \Config\Database::connect();
        
        if (!$db->tableExists('lab_requests') || !$db->tableExists('lab_results') || !$db->tableExists('doctor_orders')) {
            CLI::write('Required tables do not exist.', 'red');
            return;
        }
        
        CLI::write('Syncing completed lab tests with doctor orders...', 'yellow');
        
        // Get all completed lab_requests that have results
        $completedLabRequests = $db->table('lab_requests lr')
            ->select('lr.*, lr_result.completed_by as result_completed_by, lr_result.completed_at as result_completed_at')
            ->join('lab_results lr_result', 'lr_result.lab_request_id = lr.id', 'inner')
            ->where('lr.status', 'completed')
            ->get()
            ->getResultArray();
        
        CLI::write("Found " . count($completedLabRequests) . " completed lab requests with results.", 'cyan');
        
        $updatedCount = 0;
        $notFoundCount = 0;
        $alreadyCompletedCount = 0;
        
        foreach ($completedLabRequests as $request) {
            $doctorOrderId = null;
            
            // Try to extract doctor_order_id from instructions
            $instructions = $request['instructions'] ?? '';
            
            // Method 1: Check for "Doctor Order #123" format
            if (preg_match('/Doctor Order #(\d+)/', $instructions, $orderMatches)) {
                $doctorOrderId = (int)$orderMatches[1];
            }
            // Method 2: Check for JSON link format
            elseif (preg_match('/\| LINK:(.+?)(?:\s*\|)/', $instructions, $matches)) {
                $linkingInfo = json_decode(trim($matches[1]), true);
                if ($linkingInfo && isset($linkingInfo['doctor_order_id'])) {
                    $doctorOrderId = (int)$linkingInfo['doctor_order_id'];
                }
            }
            
            // Method 3: Match by patient_id, doctor_id, order_type, and test_name in order_description
            if (!$doctorOrderId) {
                $testName = $request['test_name'];
                $matchingOrder = $db->table('doctor_orders')
                    ->where('patient_id', $request['patient_id'])
                    ->where('doctor_id', $request['doctor_id'])
                    ->where('order_type', 'lab_test')
                    ->like('order_description', $testName, 'both')
                    ->whereIn('status', ['pending', 'in_progress'])
                    ->orderBy('created_at', 'DESC')
                    ->limit(1)
                    ->get()
                    ->getRowArray();
                
                if ($matchingOrder) {
                    $doctorOrderId = $matchingOrder['id'];
                }
            }
            
            if ($doctorOrderId) {
                // Check if order is already completed
                $existingOrder = $db->table('doctor_orders')
                    ->where('id', $doctorOrderId)
                    ->get()
                    ->getRowArray();
                
                if ($existingOrder && $existingOrder['status'] === 'completed') {
                    $alreadyCompletedCount++;
                    continue;
                }
                
                // Update doctor_order
                $updateData = [
                    'status' => 'completed',
                    'updated_at' => date('Y-m-d H:i:s')
                ];
                
                // Set completed_by if available from lab_result
                if (!empty($request['result_completed_by'])) {
                    $updateData['completed_by'] = $request['result_completed_by'];
                }
                
                // Set completed_at from lab_result if available, otherwise use current time
                if (!empty($request['result_completed_at'])) {
                    $updateData['completed_at'] = $request['result_completed_at'];
                } else {
                    $updateData['completed_at'] = date('Y-m-d H:i:s');
                }
                
                if ($db->table('doctor_orders')->where('id', $doctorOrderId)->update($updateData)) {
                    $updatedCount++;
                    CLI::write("  ✓ Updated doctor_order #{$doctorOrderId} for test: {$request['test_name']}", 'green');
                } else {
                    CLI::write("  ✗ Failed to update doctor_order #{$doctorOrderId} for test: {$request['test_name']}", 'red');
                }
            } else {
                $notFoundCount++;
                CLI::write("  ⚠ Could not find doctor_order for lab_request #{$request['id']} (Test: {$request['test_name']})", 'yellow');
            }
        }
        
        CLI::newLine();
        CLI::write("Sync completed!", 'green');
        CLI::write("  - Updated: {$updatedCount} orders", 'green');
        CLI::write("  - Already completed: {$alreadyCompletedCount} orders", 'cyan');
        CLI::write("  - Not found: {$notFoundCount} orders", 'yellow');
    }
}

