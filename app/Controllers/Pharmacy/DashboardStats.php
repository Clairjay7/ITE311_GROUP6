<?php

namespace App\Controllers\Pharmacy;

use App\Controllers\BaseController;

class DashboardStats extends BaseController
{
    public function stats()
    {
        // Check if user is logged in and is pharmacy staff
        if (!session()->get('logged_in') || session()->get('role') !== 'pharmacy') {
            return $this->response->setJSON(['error' => 'Unauthorized'])->setStatusCode(401);
        }

        $db = \Config\Database::connect();
        $today = date('Y-m-d');

        try {
            // Prescriptions today
            $prescriptionsToday = 0;
            if ($db->tableExists('prescriptions')) {
                $prescriptionsToday = $db->table('prescriptions')
                    ->where('DATE(created_at)', $today)
                    ->countAllResults();
            }

            // Pending fulfillment
            $pendingFulfillment = 0;
            if ($db->tableExists('prescriptions')) {
                $pendingFulfillment = $db->table('prescriptions')
                    ->where('status', 'pending')
                    ->countAllResults();
            }

            // Inventory statistics
            $lowStockItems = 0;
            $totalInventory = 0;
            $criticalItems = 0;
            $expiringSoon = 0;
            $outOfStock = 0;
            $categoriesCount = 0;

            if ($db->tableExists('pharmacy')) {
                $totalInventory = $db->table('pharmacy')->countAllResults();
                $lowStockItems = $db->table('pharmacy')
                    ->where('quantity <', 20)
                    ->where('quantity >', 0)
                    ->countAllResults();
                $criticalItems = $db->table('pharmacy')
                    ->where('quantity <', 10)
                    ->where('quantity >', 0)
                    ->countAllResults();
                $outOfStock = $db->table('pharmacy')
                    ->where('quantity', 0)
                    ->countAllResults();
            }

            // Categories count (if categories table exists)
            if ($db->tableExists('pharmacy_categories')) {
                $categoriesCount = $db->table('pharmacy_categories')->countAllResults();
            }

            $data = [
                'prescriptions_today' => $prescriptionsToday,
                'pending_fulfillment' => $pendingFulfillment,
                'low_stock_items' => $lowStockItems,
                'total_inventory' => $totalInventory,
                'critical_items' => $criticalItems,
                'expiring_soon' => $expiringSoon,
                'out_of_stock' => $outOfStock,
                'categories_count' => $categoriesCount,
                'last_updated' => date('Y-m-d H:i:s')
            ];

            return $this->response->setJSON($data);
        } catch (\Throwable $e) {
            log_message('error', 'Error fetching Pharmacy Dashboard Stats: ' . $e->getMessage());
            return $this->response->setJSON(['error' => 'Failed to fetch stats'])->setStatusCode(500);
        }
    }
}

