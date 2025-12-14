<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class RemoveDuplicateBeds extends Migration
{
    public function up()
    {
        $db = \Config\Database::connect();
        
        if (!$db->tableExists('beds')) {
            log_message('error', 'Beds table does not exist.');
            return;
        }
        
        // Find duplicate beds (same room_id and bed_number)
        $duplicates = $db->query("
            SELECT room_id, bed_number, COUNT(*) as count
            FROM beds
            GROUP BY room_id, bed_number
            HAVING count > 1
        ")->getResultArray();
        
        if (empty($duplicates)) {
            log_message('info', 'No duplicate beds found.');
            return;
        }
        
        log_message('info', 'Found ' . count($duplicates) . ' duplicate bed combinations.');
        
        // For each duplicate bed, keep the first one (lowest ID) and delete the rest
        foreach ($duplicates as $dup) {
            $roomId = $dup['room_id'];
            $bedNumber = $dup['bed_number'];
            
            // Get all beds with this room_id and bed_number, ordered by ID
            $beds = $db->table('beds')
                ->where('room_id', $roomId)
                ->where('bed_number', $bedNumber)
                ->orderBy('id', 'ASC')
                ->get()
                ->getResultArray();
            
            if (count($beds) <= 1) {
                continue; // Skip if only one exists now
            }
            
            // Keep the first bed (lowest ID)
            $keepBed = $beds[0];
            $keepId = $keepBed['id'];
            
            // Delete all other duplicates
            $deleteIds = [];
            for ($i = 1; $i < count($beds); $i++) {
                $deleteIds[] = $beds[$i]['id'];
            }
            
            if (!empty($deleteIds)) {
                // If duplicate has patient, transfer to the kept bed
                foreach ($beds as $bed) {
                    if ($bed['id'] != $keepId && !empty($bed['current_patient_id'])) {
                        if (empty($keepBed['current_patient_id'])) {
                            // Transfer patient to kept bed
                            $db->table('beds')
                                ->where('id', $keepId)
                                ->update([
                                    'current_patient_id' => $bed['current_patient_id'],
                                    'status' => $bed['status'],
                                    'updated_at' => date('Y-m-d H:i:s')
                                ]);
                        }
                    }
                }
                
                // Delete duplicate beds
                $db->table('beds')
                    ->whereIn('id', $deleteIds)
                    ->delete();
                
                log_message('info', "Removed " . count($deleteIds) . " duplicate(s) of bed {$bedNumber} in room ID {$roomId}. Kept bed ID: {$keepId}");
            }
        }
        
        log_message('info', 'Duplicate beds cleanup completed.');
    }

    public function down()
    {
        // Cannot restore deleted duplicates, so leave empty
    }
}

