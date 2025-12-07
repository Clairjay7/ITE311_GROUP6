<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class PharmacySeeder extends Seeder
{
    public function run()
    {
        $now = date('Y-m-d H:i:s');
        $futureDate = date('Y-m-d', strtotime('+2 years'));
        $nearExpiry = date('Y-m-d', strtotime('+30 days'));
        $expiredDate = date('Y-m-d', strtotime('-10 days'));
        
        $suppliers = ['MedSupply Co.', 'PharmaDist Inc.', 'Healthcare Supplies Ltd.', 'Medical Equipment Corp.'];
        $supplierContacts = ['09123456789', '09234567890', '09345678901', '09456789012'];
        
        $data = [];
        $batchCounter = 1;
        
        // Helper function to generate medicine data
        $generateMedicine = function($itemName, $genericName, $category, $description, $strength, $dosageForm, $quantity, $reorderLevel, $unitPrice, $sellingPrice, $supplierIndex = 0) use (&$batchCounter, $now, $futureDate, $suppliers, $supplierContacts) {
            $batchNumber = 'BATCH-' . str_pad($batchCounter++, 3, '0', STR_PAD_LEFT);
            $markupPercent = $unitPrice > 0 ? (($sellingPrice - $unitPrice) / $unitPrice) * 100 : 0;
            
            return [
                'item_name' => $itemName,
                'generic_name' => $genericName,
                'category' => $category,
                'description' => $description,
                'strength' => $strength,
                'dosage_form' => $dosageForm,
                'quantity' => $quantity,
                'reorder_level' => $reorderLevel,
                'batch_number' => $batchNumber,
                'expiration_date' => $futureDate,
                'supplier_name' => $suppliers[$supplierIndex % count($suppliers)],
                'supplier_contact' => $supplierContacts[$supplierIndex % count($supplierContacts)],
                'unit_price' => $unitPrice,
                'selling_price' => $sellingPrice,
                'markup_percent' => round($markupPercent, 2),
                'price' => $sellingPrice,
                'status' => 'active',
                'created_at' => $now,
                'updated_at' => $now,
            ];
        };
        
        // 1. Analgesics / Antipyretics (30 medicines)
        $analgesics = [
            ['Paracetamol 500mg', 'Acetaminophen', 'Pain reliever and fever reducer', '500mg', 'Tablet', 150, 20, 3.50, 5.00],
            ['Biogesic 500mg', 'Paracetamol', 'Paracetamol tablet', '500mg', 'Tablet', 120, 20, 4.50, 6.50],
            ['Tempra 500mg', 'Paracetamol', 'Fever reducer', '500mg', 'Tablet', 100, 20, 4.00, 6.00],
            ['Calpol 500mg', 'Paracetamol', 'Pain and fever relief', '500mg', 'Tablet', 90, 15, 3.80, 5.50],
            ['Tylenol 500mg', 'Acetaminophen', 'Pain reliever', '500mg', 'Tablet', 110, 20, 5.00, 7.00],
            ['Paracetamol 250mg/5ml', 'Acetaminophen', 'Pediatric syrup', '250mg/5ml', 'Syrup', 80, 15, 45.00, 65.00],
            ['Paracetamol 120mg', 'Acetaminophen', 'Children suspension', '120mg/5ml', 'Syrup', 70, 15, 40.00, 58.00],
            ['Paracetamol 1000mg', 'Acetaminophen', 'Extra strength', '1000mg', 'Tablet', 60, 10, 6.00, 9.00],
            ['Paracetamol IV 1000mg', 'Acetaminophen', 'IV injection', '1000mg/100ml', 'Injection', 40, 10, 120.00, 180.00],
            ['Paracetamol Suppository 500mg', 'Acetaminophen', 'Rectal suppository', '500mg', 'Suppository', 50, 10, 8.00, 12.00],
            ['Tramadol 50mg', 'Tramadol', 'Moderate pain relief', '50mg', 'Capsule', 45, 10, 15.00, 22.00],
            ['Tramadol 100mg', 'Tramadol', 'Strong pain relief', '100mg', 'Tablet', 35, 10, 20.00, 30.00],
            ['Codeine 30mg', 'Codeine', 'Mild opioid analgesic', '30mg', 'Tablet', 25, 5, 25.00, 38.00],
            ['Diclofenac 50mg', 'Diclofenac Sodium', 'Pain and inflammation', '50mg', 'Tablet', 85, 15, 8.00, 12.00],
            ['Diclofenac 75mg', 'Diclofenac Sodium', 'Strong anti-inflammatory', '75mg', 'Tablet', 70, 15, 10.00, 15.00],
            ['Diclofenac Gel 1%', 'Diclofenac Sodium', 'Topical gel', '1%', 'Gel', 55, 10, 85.00, 125.00],
            ['Naproxen 250mg', 'Naproxen', 'NSAID pain reliever', '250mg', 'Tablet', 65, 15, 7.00, 10.00],
            ['Naproxen 500mg', 'Naproxen', 'Extended release', '500mg', 'Tablet', 50, 10, 12.00, 18.00],
            ['Ketorolac 10mg', 'Ketorolac', 'Strong NSAID', '10mg', 'Tablet', 40, 10, 18.00, 28.00],
            ['Ketorolac 30mg/ml', 'Ketorolac', 'IV/IM injection', '30mg/ml', 'Injection', 30, 5, 150.00, 220.00],
            ['Piroxicam 20mg', 'Piroxicam', 'Long-acting NSAID', '20mg', 'Capsule', 45, 10, 14.00, 21.00],
            ['Celecoxib 200mg', 'Celecoxib', 'COX-2 inhibitor', '200mg', 'Capsule', 35, 10, 25.00, 38.00],
            ['Etoricoxib 60mg', 'Etoricoxib', 'Selective COX-2', '60mg', 'Tablet', 30, 10, 22.00, 35.00],
            ['Meloxicam 15mg', 'Meloxicam', 'NSAID for arthritis', '15mg', 'Tablet', 40, 10, 16.00, 24.00],
            ['Indomethacin 25mg', 'Indomethacin', 'Strong NSAID', '25mg', 'Capsule', 35, 10, 12.00, 18.00],
            ['Acetaminophen + Caffeine', 'Acetaminophen + Caffeine', 'Pain with alertness', '500mg + 65mg', 'Tablet', 60, 15, 5.50, 8.00],
            ['Paracetamol + Orphenadrine', 'Paracetamol + Orphenadrine', 'Muscle pain relief', '450mg + 35mg', 'Tablet', 50, 10, 8.00, 12.00],
            ['Paracetamol + Propyphenazone', 'Paracetamol + Propyphenazone', 'Combined analgesic', '250mg + 150mg', 'Tablet', 45, 10, 6.50, 10.00],
            ['Paracetamol + Chlorzoxazone', 'Paracetamol + Chlorzoxazone', 'Muscle relaxant combo', '450mg + 250mg', 'Tablet', 40, 10, 9.00, 14.00],
            ['Paracetamol + Phenyltoloxamine', 'Paracetamol + Phenyltoloxamine', 'Pain with sedation', '325mg + 30mg', 'Tablet', 35, 10, 7.50, 11.00],
        ];
        
        foreach ($analgesics as $index => $med) {
            $data[] = $generateMedicine($med[0], $med[1], 'Analgesics / Antipyretics', $med[2], $med[3], $med[4], $med[5], $med[6], $med[7], $med[8], $index);
        }
        
        // 2. Anti-inflammatory (NSAIDs) (30 medicines)
        $nsaids = [
            ['Ibuprofen 200mg', 'Ibuprofen', 'Mild NSAID', '200mg', 'Tablet', 100, 20, 4.00, 6.00],
            ['Ibuprofen 400mg', 'Ibuprofen', 'Standard NSAID', '400mg', 'Tablet', 80, 15, 5.50, 8.00],
            ['Ibuprofen 600mg', 'Ibuprofen', 'Strong NSAID', '600mg', 'Tablet', 60, 15, 7.00, 10.00],
            ['Ibuprofen 100mg/5ml', 'Ibuprofen', 'Pediatric suspension', '100mg/5ml', 'Syrup', 70, 15, 55.00, 80.00],
            ['Ibuprofen Gel 5%', 'Ibuprofen', 'Topical gel', '5%', 'Gel', 50, 10, 95.00, 140.00],
            ['Mefenamic Acid 250mg', 'Mefenamic Acid', 'NSAID for pain', '250mg', 'Capsule', 75, 15, 6.00, 9.00],
            ['Mefenamic Acid 500mg', 'Mefenamic Acid', 'Strong NSAID', '500mg', 'Capsule', 60, 15, 6.50, 9.00],
            ['Aspirin 80mg', 'Acetylsalicylic Acid', 'Low-dose aspirin', '80mg', 'Tablet', 100, 20, 3.00, 4.50],
            ['Aspirin 325mg', 'Acetylsalicylic Acid', 'Standard aspirin', '325mg', 'Tablet', 90, 20, 3.50, 5.00],
            ['Aspirin 500mg', 'Acetylsalicylic Acid', 'High-dose aspirin', '500mg', 'Tablet', 70, 15, 4.00, 6.00],
            ['Diclofenac Sodium 25mg', 'Diclofenac Sodium', 'NSAID tablet', '25mg', 'Tablet', 85, 15, 7.00, 10.00],
            ['Diclofenac Sodium 50mg', 'Diclofenac Sodium', 'Standard NSAID', '50mg', 'Tablet', 75, 15, 8.00, 12.00],
            ['Diclofenac Potassium 50mg', 'Diclofenac Potassium', 'Fast-acting NSAID', '50mg', 'Tablet', 65, 15, 9.00, 13.00],
            ['Diclofenac SR 75mg', 'Diclofenac Sodium', 'Sustained release', '75mg', 'Tablet', 55, 10, 12.00, 18.00],
            ['Diclofenac Gel 1%', 'Diclofenac Sodium', 'Topical application', '1%', 'Gel', 60, 10, 85.00, 125.00],
            ['Diclofenac Patch 100mg', 'Diclofenac Sodium', 'Transdermal patch', '100mg', 'Patch', 40, 10, 120.00, 180.00],
            ['Naproxen 250mg', 'Naproxen', 'NSAID tablet', '250mg', 'Tablet', 70, 15, 7.00, 10.00],
            ['Naproxen 500mg', 'Naproxen', 'Extended release', '500mg', 'Tablet', 55, 10, 12.00, 18.00],
            ['Naproxen Sodium 275mg', 'Naproxen Sodium', 'Fast-acting', '275mg', 'Tablet', 50, 10, 10.00, 15.00],
            ['Ketorolac 10mg', 'Ketorolac', 'Strong NSAID', '10mg', 'Tablet', 45, 10, 18.00, 28.00],
            ['Ketorolac 30mg/ml', 'Ketorolac', 'Injectable NSAID', '30mg/ml', 'Injection', 30, 5, 150.00, 220.00],
            ['Piroxicam 10mg', 'Piroxicam', 'Long-acting NSAID', '10mg', 'Capsule', 50, 10, 12.00, 18.00],
            ['Piroxicam 20mg', 'Piroxicam', 'Standard dose', '20mg', 'Capsule', 40, 10, 14.00, 21.00],
            ['Celecoxib 100mg', 'Celecoxib', 'COX-2 inhibitor', '100mg', 'Capsule', 40, 10, 20.00, 30.00],
            ['Celecoxib 200mg', 'Celecoxib', 'Standard COX-2', '200mg', 'Capsule', 35, 10, 25.00, 38.00],
            ['Etoricoxib 60mg', 'Etoricoxib', 'Selective NSAID', '60mg', 'Tablet', 35, 10, 22.00, 35.00],
            ['Etoricoxib 90mg', 'Etoricoxib', 'High-dose COX-2', '90mg', 'Tablet', 30, 10, 28.00, 42.00],
            ['Meloxicam 7.5mg', 'Meloxicam', 'Low-dose NSAID', '7.5mg', 'Tablet', 45, 10, 14.00, 21.00],
            ['Meloxicam 15mg', 'Meloxicam', 'Standard NSAID', '15mg', 'Tablet', 40, 10, 16.00, 24.00],
            ['Indomethacin 25mg', 'Indomethacin', 'Strong NSAID', '25mg', 'Capsule', 35, 10, 12.00, 18.00],
        ];
        
        foreach ($nsaids as $index => $med) {
            $data[] = $generateMedicine($med[0], $med[1], 'Anti-inflammatory (NSAIDs)', $med[2], $med[3], $med[4], $med[5], $med[6], $med[7], $med[8], $index);
        }
        
        // 3. Antibiotics (30 medicines)
        $antibiotics = [
            ['Amoxicillin 250mg', 'Amoxicillin', 'Penicillin antibiotic', '250mg', 'Capsule', 90, 20, 8.00, 12.00],
            ['Amoxicillin 500mg', 'Amoxicillin', 'Standard antibiotic', '500mg', 'Capsule', 75, 15, 10.00, 15.00],
            ['Amoxicillin 125mg/5ml', 'Amoxicillin', 'Pediatric suspension', '125mg/5ml', 'Syrup', 65, 15, 85.00, 125.00],
            ['Amoxicillin 250mg/5ml', 'Amoxicillin', 'Pediatric suspension', '250mg/5ml', 'Syrup', 60, 15, 95.00, 140.00],
            ['Amoxicillin + Clavulanate 625mg', 'Amoxicillin + Clavulanic Acid', 'Augmentin', '625mg', 'Tablet', 50, 10, 20.00, 28.00],
            ['Amoxicillin + Clavulanate 1g', 'Amoxicillin + Clavulanic Acid', 'High-dose Augmentin', '1g', 'Tablet', 40, 10, 25.00, 35.00],
            ['Amoxicillin + Clavulanate 228mg/5ml', 'Amoxicillin + Clavulanic Acid', 'Pediatric Augmentin', '228mg/5ml', 'Syrup', 45, 10, 120.00, 175.00],
            ['Ampicillin 250mg', 'Ampicillin', 'Penicillin antibiotic', '250mg', 'Capsule', 55, 15, 9.00, 13.00],
            ['Ampicillin 500mg', 'Ampicillin', 'Standard ampicillin', '500mg', 'Capsule', 45, 10, 12.00, 18.00],
            ['Ampicillin 1g', 'Ampicillin', 'IV/IM injection', '1g', 'Injection', 30, 5, 180.00, 260.00],
            ['Cefalexin 250mg', 'Cephalexin', 'First-gen cephalosporin', '250mg', 'Capsule', 70, 15, 10.00, 15.00],
            ['Cefalexin 500mg', 'Cephalexin', 'Standard cephalosporin', '500mg', 'Capsule', 60, 15, 12.00, 18.00],
            ['Cefalexin 125mg/5ml', 'Cephalexin', 'Pediatric suspension', '125mg/5ml', 'Syrup', 50, 10, 90.00, 130.00],
            ['Cefuroxime 250mg', 'Cefuroxime', 'Second-gen cephalosporin', '250mg', 'Tablet', 45, 10, 18.00, 26.00],
            ['Cefuroxime 500mg', 'Cefuroxime', 'Standard cefuroxime', '500mg', 'Tablet', 40, 10, 22.00, 32.00],
            ['Cefuroxime 750mg', 'Cefuroxime', 'IV/IM injection', '750mg', 'Injection', 30, 5, 200.00, 290.00],
            ['Ceftriaxone 250mg', 'Ceftriaxone', 'Third-gen cephalosporin', '250mg', 'Injection', 35, 5, 220.00, 320.00],
            ['Ceftriaxone 500mg', 'Ceftriaxone', 'Standard ceftriaxone', '500mg', 'Injection', 30, 5, 280.00, 400.00],
            ['Ceftriaxone 1g', 'Ceftriaxone', 'High-dose ceftriaxone', '1g', 'Injection', 25, 5, 350.00, 500.00],
            ['Azithromycin 250mg', 'Azithromycin', 'Macrolide antibiotic', '250mg', 'Tablet', 50, 10, 15.00, 22.00],
            ['Azithromycin 500mg', 'Azithromycin', 'Standard azithromycin', '500mg', 'Tablet', 45, 10, 18.00, 25.00],
            ['Azithromycin 200mg/5ml', 'Azithromycin', 'Pediatric suspension', '200mg/5ml', 'Syrup', 40, 10, 110.00, 160.00],
            ['Erythromycin 250mg', 'Erythromycin', 'Macrolide antibiotic', '250mg', 'Tablet', 55, 15, 12.00, 18.00],
            ['Erythromycin 500mg', 'Erythromycin', 'Standard erythromycin', '500mg', 'Tablet', 45, 10, 16.00, 24.00],
            ['Clarithromycin 250mg', 'Clarithromycin', 'Macrolide antibiotic', '250mg', 'Tablet', 50, 10, 20.00, 30.00],
            ['Clarithromycin 500mg', 'Clarithromycin', 'Standard clarithromycin', '500mg', 'Tablet', 40, 10, 25.00, 38.00],
            ['Ciprofloxacin 250mg', 'Ciprofloxacin', 'Fluoroquinolone', '250mg', 'Tablet', 50, 10, 12.00, 18.00],
            ['Ciprofloxacin 500mg', 'Ciprofloxacin', 'Standard ciprofloxacin', '500mg', 'Tablet', 45, 10, 14.00, 20.00],
            ['Levofloxacin 250mg', 'Levofloxacin', 'Fluoroquinolone', '250mg', 'Tablet', 40, 10, 18.00, 27.00],
            ['Levofloxacin 500mg', 'Levofloxacin', 'Standard levofloxacin', '500mg', 'Tablet', 35, 10, 22.00, 33.00],
        ];
        
        foreach ($antibiotics as $index => $med) {
            $data[] = $generateMedicine($med[0], $med[1], 'Antibiotics', $med[2], $med[3], $med[4], $med[5], $med[6], $med[7], $med[8], $index);
        }
        
        // 4. Antihistamines (30 medicines)
        $antihistamines = [
            ['Cetirizine 10mg', 'Cetirizine', 'Second-gen antihistamine', '10mg', 'Tablet', 100, 20, 5.00, 7.00],
            ['Cetirizine 5mg', 'Cetirizine', 'Low-dose cetirizine', '5mg', 'Tablet', 90, 20, 4.50, 6.50],
            ['Cetirizine 5mg/5ml', 'Cetirizine', 'Pediatric syrup', '5mg/5ml', 'Syrup', 70, 15, 75.00, 110.00],
            ['Loratadine 10mg', 'Loratadine', 'Non-drowsy antihistamine', '10mg', 'Tablet', 95, 20, 6.00, 8.50],
            ['Loratadine 5mg', 'Loratadine', 'Low-dose loratadine', '5mg', 'Tablet', 85, 20, 5.50, 8.00],
            ['Loratadine 5mg/5ml', 'Loratadine', 'Pediatric syrup', '5mg/5ml', 'Syrup', 65, 15, 80.00, 115.00],
            ['Fexofenadine 120mg', 'Fexofenadine', 'Third-gen antihistamine', '120mg', 'Tablet', 60, 15, 12.00, 18.00],
            ['Fexofenadine 180mg', 'Fexofenadine', 'Standard fexofenadine', '180mg', 'Tablet', 50, 10, 15.00, 22.00],
            ['Desloratadine 5mg', 'Desloratadine', 'Active metabolite', '5mg', 'Tablet', 55, 15, 10.00, 15.00],
            ['Levocetirizine 5mg', 'Levocetirizine', 'Active enantiomer', '5mg', 'Tablet', 60, 15, 8.00, 12.00],
            ['Chlorphenamine 4mg', 'Chlorpheniramine', 'First-gen antihistamine', '4mg', 'Tablet', 80, 20, 4.00, 5.50],
            ['Diphenhydramine 25mg', 'Diphenhydramine', 'Sedating antihistamine', '25mg', 'Capsule', 70, 15, 5.00, 7.50],
            ['Diphenhydramine 50mg', 'Diphenhydramine', 'Standard dose', '50mg', 'Capsule', 60, 15, 6.00, 9.00],
            ['Promethazine 25mg', 'Promethazine', 'Antihistamine + antiemetic', '25mg', 'Tablet', 55, 15, 7.00, 10.00],
            ['Promethazine 50mg', 'Promethazine', 'Standard promethazine', '50mg', 'Tablet', 45, 10, 9.00, 13.00],
            ['Promethazine Syrup', 'Promethazine', 'Pediatric syrup', '5mg/5ml', 'Syrup', 50, 10, 65.00, 95.00],
            ['Hydroxyzine 25mg', 'Hydroxyzine', 'Antihistamine + anxiolytic', '25mg', 'Tablet', 50, 10, 8.00, 12.00],
            ['Hydroxyzine 50mg', 'Hydroxyzine', 'Standard hydroxyzine', '50mg', 'Tablet', 40, 10, 10.00, 15.00],
            ['Cyproheptadine 4mg', 'Cyproheptadine', 'Antihistamine + appetite', '4mg', 'Tablet', 45, 10, 6.00, 9.00],
            ['Ebastine 10mg', 'Ebastine', 'Second-gen antihistamine', '10mg', 'Tablet', 50, 10, 9.00, 13.00],
            ['Bilastine 20mg', 'Bilastine', 'New gen antihistamine', '20mg', 'Tablet', 40, 10, 11.00, 16.00],
            ['Rupatadine 10mg', 'Rupatadine', 'Antihistamine + PAF', '10mg', 'Tablet', 35, 10, 13.00, 19.00],
            ['Emedastine 1mg', 'Emedastine', 'Ophthalmic antihistamine', '1mg/ml', 'Drops', 30, 10, 85.00, 125.00],
            ['Olopatadine 0.1%', 'Olopatadine', 'Ophthalmic antihistamine', '0.1%', 'Drops', 25, 10, 120.00, 175.00],
            ['Ketotifen 1mg', 'Ketotifen', 'Antihistamine + mast cell', '1mg', 'Tablet', 40, 10, 10.00, 15.00],
            ['Ketotifen 0.025%', 'Ketotifen', 'Ophthalmic solution', '0.025%', 'Drops', 30, 10, 95.00, 140.00],
            ['Azelastine 0.05%', 'Azelastine', 'Nasal spray', '0.05%', 'Spray', 35, 10, 110.00, 160.00],
            ['Triprolidine 2.5mg', 'Triprolidine', 'First-gen antihistamine', '2.5mg', 'Tablet', 45, 10, 5.00, 7.50],
            ['Pheniramine 22.75mg', 'Pheniramine', 'Antihistamine', '22.75mg', 'Tablet', 50, 10, 4.50, 6.50],
            ['Dexchlorpheniramine 2mg', 'Dexchlorpheniramine', 'Active enantiomer', '2mg', 'Tablet', 55, 10, 5.50, 8.00],
        ];
        
        foreach ($antihistamines as $index => $med) {
            $data[] = $generateMedicine($med[0], $med[1], 'Antihistamines', $med[2], $med[3], $med[4], $med[5], $med[6], $med[7], $med[8], $index);
        }
        
        // 5. Cough & Cold (Respiratory) (30 medicines)
        $respiratory = [
            ['Salbutamol 2mg', 'Salbutamol', 'Bronchodilator', '2mg', 'Tablet', 80, 20, 8.50, 12.00],
            ['Salbutamol 4mg', 'Salbutamol', 'Standard salbutamol', '4mg', 'Tablet', 70, 15, 10.00, 15.00],
            ['Salbutamol 100mcg', 'Salbutamol', 'Inhaler', '100mcg/dose', 'Inhaler', 60, 15, 180.00, 260.00],
            ['Salbutamol 2.5mg/2.5ml', 'Salbutamol', 'Nebulizer solution', '2.5mg/2.5ml', 'Nebulizer', 50, 10, 25.00, 38.00],
            ['Terbutaline 2.5mg', 'Terbutaline', 'Bronchodilator', '2.5mg', 'Tablet', 55, 15, 9.00, 13.00],
            ['Terbutaline 5mg', 'Terbutaline', 'Standard terbutaline', '5mg', 'Tablet', 45, 10, 12.00, 18.00],
            ['Terbutaline 0.5mg/ml', 'Terbutaline', 'Nebulizer solution', '0.5mg/ml', 'Nebulizer', 40, 10, 28.00, 42.00],
            ['Aminophylline 100mg', 'Aminophylline', 'Bronchodilator', '100mg', 'Tablet', 50, 10, 10.00, 15.00],
            ['Aminophylline 225mg', 'Aminophylline', 'Standard aminophylline', '225mg', 'Tablet', 40, 10, 14.00, 21.00],
            ['Theophylline 200mg', 'Theophylline', 'Sustained release', '200mg', 'Tablet', 45, 10, 11.00, 16.00],
            ['Theophylline 300mg', 'Theophylline', 'Extended release', '300mg', 'Tablet', 35, 10, 15.00, 22.00],
            ['Montelukast 10mg', 'Montelukast', 'Leukotriene receptor', '10mg', 'Tablet', 60, 15, 18.00, 27.00],
            ['Montelukast 5mg', 'Montelukast', 'Pediatric dose', '5mg', 'Chewable', 50, 10, 15.00, 22.00],
            ['Montelukast 4mg', 'Montelukast', 'Children granules', '4mg', 'Granules', 40, 10, 12.00, 18.00],
            ['Carbocisteine 250mg', 'Carbocisteine', 'Mucolytic', '250mg', 'Capsule', 70, 15, 6.00, 9.00],
            ['Carbocisteine 500mg', 'Carbocisteine', 'Standard mucolytic', '500mg', 'Capsule', 65, 15, 7.00, 10.00],
            ['Carbocisteine 250mg/5ml', 'Carbocisteine', 'Pediatric syrup', '250mg/5ml', 'Syrup', 55, 10, 75.00, 110.00],
            ['Acetylcysteine 200mg', 'Acetylcysteine', 'Mucolytic', '200mg', 'Tablet', 60, 15, 8.00, 12.00],
            ['Acetylcysteine 600mg', 'Acetylcysteine', 'Standard mucolytic', '600mg', 'Tablet', 50, 10, 12.00, 18.00],
            ['Bromhexine 8mg', 'Bromhexine', 'Mucolytic', '8mg', 'Tablet', 65, 15, 5.00, 7.50],
            ['Bromhexine 4mg/5ml', 'Bromhexine', 'Pediatric syrup', '4mg/5ml', 'Syrup', 55, 10, 65.00, 95.00],
            ['Ambroxol 15mg', 'Ambroxol', 'Mucolytic', '15mg', 'Tablet', 60, 15, 6.50, 9.50],
            ['Ambroxol 30mg', 'Ambroxol', 'Standard ambroxol', '30mg', 'Tablet', 50, 10, 8.00, 12.00],
            ['Ambroxol 15mg/5ml', 'Ambroxol', 'Pediatric syrup', '15mg/5ml', 'Syrup', 45, 10, 70.00, 100.00],
            ['Dextromethorphan 15mg', 'Dextromethorphan', 'Cough suppressant', '15mg', 'Tablet', 70, 15, 5.50, 8.00],
            ['Dextromethorphan 30mg', 'Dextromethorphan', 'Standard dose', '30mg', 'Tablet', 60, 15, 7.00, 10.00],
            ['Dextromethorphan 7.5mg/5ml', 'Dextromethorphan', 'Pediatric syrup', '7.5mg/5ml', 'Syrup', 50, 10, 60.00, 88.00],
            ['Guaifenesin 200mg', 'Guaifenesin', 'Expectorant', '200mg', 'Tablet', 65, 15, 4.50, 6.50],
            ['Guaifenesin 400mg', 'Guaifenesin', 'Standard expectorant', '400mg', 'Tablet', 55, 10, 6.00, 9.00],
            ['Guaifenesin 100mg/5ml', 'Guaifenesin', 'Pediatric syrup', '100mg/5ml', 'Syrup', 45, 10, 55.00, 80.00],
        ];
        
        foreach ($respiratory as $index => $med) {
            $data[] = $generateMedicine($med[0], $med[1], 'Cough & Cold (Respiratory)', $med[2], $med[3], $med[4], $med[5], $med[6], $med[7], $med[8], $index);
        }
        
        // 6. Gastrointestinal Medicines (30 medicines)
        $gastrointestinal = [
            ['Omeprazole 20mg', 'Omeprazole', 'Proton pump inhibitor', '20mg', 'Capsule', 80, 20, 8.50, 12.00],
            ['Omeprazole 40mg', 'Omeprazole', 'High-dose PPI', '40mg', 'Capsule', 70, 15, 12.00, 18.00],
            ['Lansoprazole 30mg', 'Lansoprazole', 'Proton pump inhibitor', '30mg', 'Capsule', 75, 15, 10.00, 15.00],
            ['Pantoprazole 40mg', 'Pantoprazole', 'Proton pump inhibitor', '40mg', 'Tablet', 70, 15, 11.00, 16.00],
            ['Esomeprazole 20mg', 'Esomeprazole', 'PPI', '20mg', 'Capsule', 65, 15, 13.00, 19.00],
            ['Esomeprazole 40mg', 'Esomeprazole', 'High-dose PPI', '40mg', 'Capsule', 55, 10, 18.00, 27.00],
            ['Rabeprazole 20mg', 'Rabeprazole', 'Proton pump inhibitor', '20mg', 'Tablet', 60, 15, 12.00, 18.00],
            ['Ranitidine 150mg', 'Ranitidine', 'H2 blocker', '150mg', 'Tablet', 85, 20, 6.00, 9.00],
            ['Ranitidine 300mg', 'Ranitidine', 'Standard H2 blocker', '300mg', 'Tablet', 75, 15, 8.00, 12.00],
            ['Famotidine 20mg', 'Famotidine', 'H2 blocker', '20mg', 'Tablet', 70, 15, 7.00, 10.00],
            ['Famotidine 40mg', 'Famotidine', 'Standard H2 blocker', '40mg', 'Tablet', 60, 15, 9.00, 13.00],
            ['Cimetidine 200mg', 'Cimetidine', 'H2 blocker', '200mg', 'Tablet', 65, 15, 5.00, 7.50],
            ['Cimetidine 400mg', 'Cimetidine', 'Standard H2 blocker', '400mg', 'Tablet', 55, 10, 7.00, 10.00],
            ['Antacid Suspension', 'Aluminum + Magnesium Hydroxide', 'Antacid', '200ml', 'Syrup', 60, 15, 32.00, 45.00],
            ['Antacid Tablets', 'Calcium Carbonate', 'Antacid tablets', '500mg', 'Tablet', 80, 20, 3.00, 4.50],
            ['Sucralfate 1g', 'Sucralfate', 'Gastric protectant', '1g', 'Tablet', 50, 10, 10.00, 15.00],
            ['Misoprostol 200mcg', 'Misoprostol', 'Gastric protectant', '200mcg', 'Tablet', 45, 10, 12.00, 18.00],
            ['Loperamide 2mg', 'Loperamide', 'Anti-diarrheal', '2mg', 'Capsule', 70, 20, 4.20, 6.00],
            ['Loperamide 4mg', 'Loperamide', 'Standard anti-diarrheal', '4mg', 'Capsule', 60, 15, 5.50, 8.00],
            ['Diphenoxylate + Atropine', 'Diphenoxylate + Atropine', 'Anti-diarrheal combo', '2.5mg + 0.025mg', 'Tablet', 50, 10, 8.00, 12.00],
            ['Metoclopramide 10mg', 'Metoclopramide', 'Antiemetic', '10mg', 'Tablet', 65, 15, 6.00, 9.00],
            ['Metoclopramide 5mg/5ml', 'Metoclopramide', 'Pediatric syrup', '5mg/5ml', 'Syrup', 55, 10, 70.00, 100.00],
            ['Domperidone 10mg', 'Domperidone', 'Antiemetic', '10mg', 'Tablet', 60, 15, 7.00, 10.00],
            ['Ondansetron 4mg', 'Ondansetron', '5-HT3 antagonist', '4mg', 'Tablet', 50, 10, 15.00, 22.00],
            ['Ondansetron 8mg', 'Ondansetron', 'Standard ondansetron', '8mg', 'Tablet', 45, 10, 18.00, 27.00],
            ['Ondansetron 4mg/2ml', 'Ondansetron', 'IV injection', '4mg/2ml', 'Injection', 40, 5, 120.00, 180.00],
            ['Hyoscine Butylbromide 10mg', 'Hyoscine Butylbromide', 'Antispasmodic', '10mg', 'Tablet', 70, 15, 10.50, 15.00],
            ['Dicyclomine 10mg', 'Dicyclomine', 'Antispasmodic', '10mg', 'Capsule', 60, 15, 8.00, 12.00],
            ['Mebeverine 135mg', 'Mebeverine', 'Antispasmodic', '135mg', 'Tablet', 55, 10, 11.00, 16.00],
            ['Simethicone 40mg', 'Simethicone', 'Antiflatulent', '40mg', 'Tablet', 65, 15, 3.50, 5.00],
        ];
        
        foreach ($gastrointestinal as $index => $med) {
            $data[] = $generateMedicine($med[0], $med[1], 'Gastrointestinal Medicines', $med[2], $med[3], $med[4], $med[5], $med[6], $med[7], $med[8], $index);
        }
        
        // 7. Cardiovascular Medicines (30 medicines)
        $cardiovascular = [
            ['Amlodipine 5mg', 'Amlodipine', 'Calcium channel blocker', '5mg', 'Tablet', 85, 20, 7.00, 10.00],
            ['Amlodipine 10mg', 'Amlodipine', 'High-dose CCB', '10mg', 'Tablet', 75, 15, 10.00, 15.00],
            ['Losartan 50mg', 'Losartan', 'ARB for hypertension', '50mg', 'Tablet', 80, 20, 10.50, 15.00],
            ['Losartan 100mg', 'Losartan', 'Standard ARB', '100mg', 'Tablet', 70, 15, 14.00, 21.00],
            ['Valsartan 80mg', 'Valsartan', 'ARB', '80mg', 'Tablet', 75, 15, 12.00, 18.00],
            ['Valsartan 160mg', 'Valsartan', 'Standard ARB', '160mg', 'Tablet', 65, 15, 16.00, 24.00],
            ['Irbesartan 150mg', 'Irbesartan', 'ARB', '150mg', 'Tablet', 60, 15, 13.00, 19.00],
            ['Irbesartan 300mg', 'Irbesartan', 'High-dose ARB', '300mg', 'Tablet', 50, 10, 18.00, 27.00],
            ['Telmisartan 40mg', 'Telmisartan', 'ARB', '40mg', 'Tablet', 55, 15, 15.00, 22.00],
            ['Telmisartan 80mg', 'Telmisartan', 'Standard ARB', '80mg', 'Tablet', 45, 10, 20.00, 30.00],
            ['Atenolol 50mg', 'Atenolol', 'Beta blocker', '50mg', 'Tablet', 70, 20, 8.40, 12.00],
            ['Atenolol 100mg', 'Atenolol', 'Standard beta blocker', '100mg', 'Tablet', 60, 15, 12.00, 18.00],
            ['Metoprolol 50mg', 'Metoprolol', 'Beta blocker', '50mg', 'Tablet', 65, 15, 9.00, 13.00],
            ['Metoprolol 100mg', 'Metoprolol', 'Standard beta blocker', '100mg', 'Tablet', 55, 10, 13.00, 19.00],
            ['Propranolol 40mg', 'Propranolol', 'Beta blocker', '40mg', 'Tablet', 60, 15, 7.00, 10.00],
            ['Propranolol 80mg', 'Propranolol', 'Standard beta blocker', '80mg', 'Tablet', 50, 10, 10.00, 15.00],
            ['Bisoprolol 5mg', 'Bisoprolol', 'Beta blocker', '5mg', 'Tablet', 55, 15, 11.00, 16.00],
            ['Bisoprolol 10mg', 'Bisoprolol', 'Standard beta blocker', '10mg', 'Tablet', 45, 10, 15.00, 22.00],
            ['Carvedilol 12.5mg', 'Carvedilol', 'Alpha-beta blocker', '12.5mg', 'Tablet', 50, 10, 14.00, 21.00],
            ['Carvedilol 25mg', 'Carvedilol', 'Standard dose', '25mg', 'Tablet', 40, 10, 18.00, 27.00],
            ['Enalapril 5mg', 'Enalapril', 'ACE inhibitor', '5mg', 'Tablet', 60, 15, 8.00, 12.00],
            ['Enalapril 10mg', 'Enalapril', 'Standard ACE inhibitor', '10mg', 'Tablet', 50, 10, 11.00, 16.00],
            ['Lisinopril 5mg', 'Lisinopril', 'ACE inhibitor', '5mg', 'Tablet', 55, 15, 9.00, 13.00],
            ['Lisinopril 10mg', 'Lisinopril', 'Standard ACE inhibitor', '10mg', 'Tablet', 45, 10, 12.00, 18.00],
            ['Ramipril 2.5mg', 'Ramipril', 'ACE inhibitor', '2.5mg', 'Capsule', 50, 10, 10.00, 15.00],
            ['Ramipril 5mg', 'Ramipril', 'Standard ACE inhibitor', '5mg', 'Capsule', 40, 10, 13.00, 19.00],
            ['Atorvastatin 10mg', 'Atorvastatin', 'Statin', '10mg', 'Tablet', 70, 20, 15.00, 22.00],
            ['Atorvastatin 20mg', 'Atorvastatin', 'Standard statin', '20mg', 'Tablet', 60, 15, 20.00, 30.00],
            ['Atorvastatin 40mg', 'Atorvastatin', 'High-dose statin', '40mg', 'Tablet', 50, 10, 25.00, 38.00],
            ['Simvastatin 20mg', 'Simvastatin', 'Statin', '20mg', 'Tablet', 65, 15, 12.00, 18.00],
        ];
        
        foreach ($cardiovascular as $index => $med) {
            $data[] = $generateMedicine($med[0], $med[1], 'Cardiovascular Medicines', $med[2], $med[3], $med[4], $med[5], $med[6], $med[7], $med[8], $index);
        }
        
        // 8. Diabetic Medicines (30 medicines)
        $diabetic = [
            ['Metformin 500mg', 'Metformin', 'Oral antidiabetic', '500mg', 'Tablet', 90, 20, 5.60, 8.00],
            ['Metformin 850mg', 'Metformin', 'Standard metformin', '850mg', 'Tablet', 80, 20, 7.00, 10.00],
            ['Metformin 1000mg', 'Metformin', 'High-dose metformin', '1000mg', 'Tablet', 70, 15, 8.50, 12.00],
            ['Metformin XR 500mg', 'Metformin', 'Extended release', '500mg', 'Tablet', 65, 15, 9.00, 13.00],
            ['Metformin XR 1000mg', 'Metformin', 'XR high-dose', '1000mg', 'Tablet', 55, 10, 12.00, 18.00],
            ['Glimepiride 1mg', 'Glimepiride', 'Sulfonylurea', '1mg', 'Tablet', 60, 15, 7.00, 10.00],
            ['Glimepiride 2mg', 'Glimepiride', 'Standard sulfonylurea', '2mg', 'Tablet', 55, 15, 8.40, 12.00],
            ['Glimepiride 4mg', 'Glimepiride', 'High-dose sulfonylurea', '4mg', 'Tablet', 45, 10, 11.00, 16.00],
            ['Glibenclamide 5mg', 'Glibenclamide', 'Sulfonylurea', '5mg', 'Tablet', 50, 10, 6.00, 9.00],
            ['Gliclazide 80mg', 'Gliclazide', 'Sulfonylurea', '80mg', 'Tablet', 60, 15, 7.50, 11.00],
            ['Gliclazide MR 30mg', 'Gliclazide', 'Modified release', '30mg', 'Tablet', 55, 15, 9.00, 13.00],
            ['Gliclazide MR 60mg', 'Gliclazide', 'MR standard dose', '60mg', 'Tablet', 45, 10, 12.00, 18.00],
            ['Glipizide 5mg', 'Glipizide', 'Sulfonylurea', '5mg', 'Tablet', 50, 10, 8.00, 12.00],
            ['Glipizide 10mg', 'Glipizide', 'Standard sulfonylurea', '10mg', 'Tablet', 40, 10, 11.00, 16.00],
            ['Pioglitazone 15mg', 'Pioglitazone', 'Thiazolidinedione', '15mg', 'Tablet', 45, 10, 18.00, 27.00],
            ['Pioglitazone 30mg', 'Pioglitazone', 'Standard TZD', '30mg', 'Tablet', 35, 10, 22.00, 33.00],
            ['Pioglitazone 45mg', 'Pioglitazone', 'High-dose TZD', '45mg', 'Tablet', 30, 10, 28.00, 42.00],
            ['Sitagliptin 50mg', 'Sitagliptin', 'DPP-4 inhibitor', '50mg', 'Tablet', 40, 10, 25.00, 38.00],
            ['Sitagliptin 100mg', 'Sitagliptin', 'Standard DPP-4', '100mg', 'Tablet', 35, 10, 30.00, 45.00],
            ['Vildagliptin 50mg', 'Vildagliptin', 'DPP-4 inhibitor', '50mg', 'Tablet', 38, 10, 22.00, 33.00],
            ['Linagliptin 5mg', 'Linagliptin', 'DPP-4 inhibitor', '5mg', 'Tablet', 36, 10, 28.00, 42.00],
            ['Empagliflozin 10mg', 'Empagliflozin', 'SGLT2 inhibitor', '10mg', 'Tablet', 32, 10, 35.00, 52.00],
            ['Empagliflozin 25mg', 'Empagliflozin', 'Standard SGLT2', '25mg', 'Tablet', 28, 10, 42.00, 63.00],
            ['Dapagliflozin 5mg', 'Dapagliflozin', 'SGLT2 inhibitor', '5mg', 'Tablet', 30, 10, 38.00, 57.00],
            ['Dapagliflozin 10mg', 'Dapagliflozin', 'Standard SGLT2', '10mg', 'Tablet', 25, 10, 45.00, 68.00],
            ['Acarbose 50mg', 'Acarbose', 'Alpha-glucosidase inhibitor', '50mg', 'Tablet', 40, 10, 12.00, 18.00],
            ['Acarbose 100mg', 'Acarbose', 'Standard AGI', '100mg', 'Tablet', 35, 10, 16.00, 24.00],
            ['Repaglinide 0.5mg', 'Repaglinide', 'Meglitinide', '0.5mg', 'Tablet', 42, 10, 14.00, 21.00],
            ['Repaglinide 1mg', 'Repaglinide', 'Standard meglitinide', '1mg', 'Tablet', 38, 10, 18.00, 27.00],
            ['Repaglinide 2mg', 'Repaglinide', 'High-dose meglitinide', '2mg', 'Tablet', 32, 10, 22.00, 33.00],
        ];
        
        foreach ($diabetic as $index => $med) {
            $data[] = $generateMedicine($med[0], $med[1], 'Diabetic Medicines', $med[2], $med[3], $med[4], $med[5], $med[6], $med[7], $med[8], $index);
        }
        
        // 9. Vitamins & Supplements (30 medicines)
        $vitamins = [
            ['Vitamin C 500mg', 'Ascorbic Acid', 'Ascorbic acid', '500mg', 'Tablet', 150, 30, 2.80, 4.00],
            ['Vitamin C 1000mg', 'Ascorbic Acid', 'High-dose vitamin C', '1000mg', 'Tablet', 120, 25, 4.50, 6.50],
            ['Vitamin C 250mg/5ml', 'Ascorbic Acid', 'Pediatric syrup', '250mg/5ml', 'Syrup', 100, 20, 65.00, 95.00],
            ['Vitamin B1 100mg', 'Thiamine', 'Thiamine', '100mg', 'Tablet', 110, 25, 3.00, 4.50],
            ['Vitamin B2 50mg', 'Riboflavin', 'Riboflavin', '50mg', 'Tablet', 105, 25, 3.50, 5.00],
            ['Vitamin B3 100mg', 'Niacin', 'Niacin', '100mg', 'Tablet', 100, 25, 4.00, 6.00],
            ['Vitamin B6 50mg', 'Pyridoxine', 'Pyridoxine', '50mg', 'Tablet', 95, 20, 3.00, 4.50],
            ['Vitamin B12 500mcg', 'Cyanocobalamin', 'Cyanocobalamin', '500mcg', 'Tablet', 90, 20, 5.00, 7.50],
            ['Vitamin B12 1000mcg', 'Cyanocobalamin', 'High-dose B12', '1000mcg', 'Tablet', 80, 15, 7.00, 10.00],
            ['Folic Acid 5mg', 'Folic Acid', 'Folic acid', '5mg', 'Tablet', 85, 20, 2.50, 3.75],
            ['Folic Acid 1mg', 'Folic Acid', 'Standard folic acid', '1mg', 'Tablet', 90, 20, 2.00, 3.00],
            ['Vitamin B Complex', 'B-Complex Vitamins', 'B-complex vitamins', 'Various', 'Tablet', 100, 25, 5.60, 8.00],
            ['Vitamin B Complex Injection', 'B-Complex Vitamins', 'Injectable B-complex', 'Various', 'Injection', 50, 10, 85.00, 125.00],
            ['Multivitamins', 'Multivitamin', 'Complete multivitamin', 'Various', 'Tablet', 95, 25, 8.40, 12.00],
            ['Multivitamins + Minerals', 'Multivitamin + Minerals', 'Complete formula', 'Various', 'Tablet', 90, 20, 10.00, 15.00],
            ['Calcium 500mg', 'Calcium Carbonate', 'Calcium supplement', '500mg', 'Tablet', 110, 25, 4.00, 6.00],
            ['Calcium + Vitamin D', 'Calcium + Cholecalciferol', 'Bone health', '500mg + 200IU', 'Tablet', 100, 25, 12.60, 18.00],
            ['Calcium + Vitamin D3', 'Calcium + Cholecalciferol', 'Bone health combo', '600mg + 400IU', 'Tablet', 90, 20, 15.00, 22.00],
            ['Iron 65mg', 'Ferrous Sulfate', 'Iron supplement', '65mg', 'Tablet', 85, 20, 4.50, 6.50],
            ['Iron + Folic Acid', 'Ferrous Sulfate + Folic Acid', 'For anemia', '60mg + 0.4mg', 'Tablet', 80, 20, 7.00, 10.00],
            ['Iron + Folic Acid + B12', 'Ferrous + Folic + B12', 'Complete anemia treatment', '60mg + 0.4mg + 7.5mcg', 'Tablet', 75, 15, 9.00, 13.00],
            ['Zinc 20mg', 'Zinc Sulfate', 'Zinc supplement', '20mg', 'Tablet', 70, 15, 5.00, 7.50],
            ['Zinc 50mg', 'Zinc Sulfate', 'High-dose zinc', '50mg', 'Tablet', 60, 15, 7.00, 10.00],
            ['Magnesium 250mg', 'Magnesium Oxide', 'Magnesium supplement', '250mg', 'Tablet', 65, 15, 6.00, 9.00],
            ['Magnesium 500mg', 'Magnesium Oxide', 'Standard magnesium', '500mg', 'Tablet', 55, 10, 8.00, 12.00],
            ['Vitamin D3 1000IU', 'Cholecalciferol', 'Vitamin D3', '1000IU', 'Capsule', 80, 20, 8.00, 12.00],
            ['Vitamin D3 2000IU', 'Cholecalciferol', 'High-dose D3', '2000IU', 'Capsule', 70, 15, 10.00, 15.00],
            ['Vitamin E 400IU', 'Tocopherol', 'Vitamin E', '400IU', 'Capsule', 75, 15, 9.00, 13.00],
            ['Vitamin A 10000IU', 'Retinol', 'Vitamin A', '10000IU', 'Capsule', 65, 15, 7.00, 10.00],
            ['Omega-3 1000mg', 'Omega-3 Fatty Acids', 'Fish oil', '1000mg', 'Capsule', 60, 15, 12.00, 18.00],
        ];
        
        foreach ($vitamins as $index => $med) {
            $data[] = $generateMedicine($med[0], $med[1], 'Vitamins & Supplements', $med[2], $med[3], $med[4], $med[5], $med[6], $med[7], $med[8], $index);
        }
        
        // 10. IV Fluids / Electrolytes (30 medicines)
        $ivFluids = [
            ['Normal Saline 0.9% 500ml', 'Sodium Chloride', 'IV fluid', '0.9%', 'Injection', 100, 25, 60.00, 85.00],
            ['Normal Saline 0.9% 1L', 'Sodium Chloride', 'IV fluid', '0.9%', 'Injection', 90, 20, 70.00, 100.00],
            ['Dextrose 5% 500ml', 'Dextrose', 'IV fluid', '5%', 'Injection', 95, 25, 65.00, 95.00],
            ['Dextrose 5% 1L', 'Dextrose', 'IV fluid', '5%', 'Injection', 85, 20, 84.00, 120.00],
            ['Dextrose 10% 500ml', 'Dextrose', 'IV fluid', '10%', 'Injection', 80, 20, 75.00, 110.00],
            ['Dextrose 10% 1L', 'Dextrose', 'IV fluid', '10%', 'Injection', 70, 15, 95.00, 140.00],
            ['D5LR 500ml', 'Dextrose + Lactated Ringer', 'IV fluid', '5% Dextrose + LR', 'Injection', 75, 20, 80.00, 115.00],
            ['D5LR 1L', 'Dextrose + Lactated Ringer', 'IV fluid', '5% Dextrose + LR', 'Injection', 65, 15, 100.00, 145.00],
            ['Lactated Ringer 500ml', 'Lactated Ringer', 'IV fluid', 'LR Solution', 'Injection', 80, 20, 70.00, 100.00],
            ['Lactated Ringer 1L', 'Lactated Ringer', 'IV fluid', 'LR Solution', 'Injection', 70, 15, 85.00, 125.00],
            ['Plasma-Lyte 500ml', 'Plasma-Lyte', 'IV fluid', 'Balanced electrolyte', 'Injection', 60, 15, 95.00, 140.00],
            ['Plasma-Lyte 1L', 'Plasma-Lyte', 'IV fluid', 'Balanced electrolyte', 'Injection', 50, 10, 120.00, 175.00],
            ['Hartmann Solution 500ml', 'Hartmann Solution', 'IV fluid', 'Ringer lactate', 'Injection', 55, 15, 75.00, 110.00],
            ['Hartmann Solution 1L', 'Hartmann Solution', 'IV fluid', 'Ringer lactate', 'Injection', 45, 10, 90.00, 130.00],
            ['D5NSS 500ml', 'Dextrose + Normal Saline', 'IV fluid', '5% Dextrose + 0.9% NaCl', 'Injection', 70, 20, 78.00, 115.00],
            ['D5NSS 1L', 'Dextrose + Normal Saline', 'IV fluid', '5% Dextrose + 0.9% NaCl', 'Injection', 60, 15, 95.00, 140.00],
            ['Half Normal Saline 0.45% 500ml', 'Sodium Chloride', 'IV fluid', '0.45% NaCl', 'Injection', 50, 15, 65.00, 95.00],
            ['Half Normal Saline 0.45% 1L', 'Sodium Chloride', 'IV fluid', '0.45% NaCl', 'Injection', 40, 10, 80.00, 115.00],
            ['Mannitol 20% 250ml', 'Mannitol', 'Osmotic diuretic', '20%', 'Injection', 35, 10, 180.00, 260.00],
            ['Mannitol 20% 500ml', 'Mannitol', 'Osmotic diuretic', '20%', 'Injection', 30, 5, 250.00, 360.00],
            ['Albumin 20% 50ml', 'Human Albumin', 'Plasma expander', '20%', 'Injection', 25, 5, 1200.00, 1750.00],
            ['Albumin 25% 50ml', 'Human Albumin', 'Plasma expander', '25%', 'Injection', 20, 5, 1400.00, 2000.00],
            ['Potassium Chloride 10mEq/10ml', 'Potassium Chloride', 'Electrolyte', '10mEq/10ml', 'Injection', 40, 10, 45.00, 65.00],
            ['Potassium Chloride 20mEq/10ml', 'Potassium Chloride', 'Electrolyte', '20mEq/10ml', 'Injection', 35, 10, 55.00, 80.00],
            ['Sodium Bicarbonate 8.4% 50ml', 'Sodium Bicarbonate', 'Alkalinizing agent', '8.4%', 'Injection', 30, 10, 85.00, 125.00],
            ['Calcium Gluconate 10% 10ml', 'Calcium Gluconate', 'Calcium supplement', '10%', 'Injection', 35, 10, 65.00, 95.00],
            ['Magnesium Sulfate 50% 2ml', 'Magnesium Sulfate', 'Magnesium supplement', '50%', 'Injection', 30, 10, 55.00, 80.00],
            ['Sodium Chloride 3% 100ml', 'Sodium Chloride', 'Hypertonic saline', '3%', 'Injection', 25, 10, 75.00, 110.00],
            ['Dextrose 50% 50ml', 'Dextrose', 'Hypertonic dextrose', '50%', 'Injection', 30, 10, 45.00, 65.00],
            ['Water for Injection 10ml', 'Water for Injection', 'Diluent', '10ml', 'Injection', 100, 30, 15.00, 22.00],
        ];
        
        foreach ($ivFluids as $index => $med) {
            $data[] = $generateMedicine($med[0], $med[1], 'IV Fluids / Electrolytes', $med[2], $med[3], $med[4], $med[5], $med[6], $med[7], $med[8], $index);
        }
        
        // 11. Emergency Drugs (30 medicines)
        $emergency = [
            ['Epinephrine 1mg/ml', 'Epinephrine', 'Emergency anaphylaxis', '1mg/ml', 'Injection', 50, 15, 105.00, 150.00],
            ['Epinephrine 0.1mg/ml', 'Epinephrine', 'Diluted epinephrine', '0.1mg/ml', 'Injection', 45, 10, 95.00, 140.00],
            ['Atropine 0.5mg/ml', 'Atropine', 'Anticholinergic', '0.5mg/ml', 'Injection', 40, 10, 85.00, 125.00],
            ['Atropine 1mg/ml', 'Atropine', 'Standard atropine', '1mg/ml', 'Injection', 35, 10, 95.00, 140.00],
            ['Adenosine 6mg/2ml', 'Adenosine', 'Antiarrhythmic', '6mg/2ml', 'Injection', 30, 5, 250.00, 360.00],
            ['Adenosine 12mg/4ml', 'Adenosine', 'High-dose adenosine', '12mg/4ml', 'Injection', 25, 5, 350.00, 500.00],
            ['Amiodarone 150mg/3ml', 'Amiodarone', 'Antiarrhythmic', '150mg/3ml', 'Injection', 28, 5, 280.00, 400.00],
            ['Lidocaine 2% 5ml', 'Lidocaine', 'Local anesthetic', '2%', 'Injection', 60, 15, 45.00, 65.00],
            ['Lidocaine 1% 20ml', 'Lidocaine', 'Local anesthetic', '1%', 'Injection', 55, 15, 55.00, 80.00],
            ['Lidocaine 2% 20ml', 'Lidocaine', 'Local anesthetic', '2%', 'Injection', 50, 10, 65.00, 95.00],
            ['Dopamine 200mg/5ml', 'Dopamine', 'Vasopressor', '200mg/5ml', 'Injection', 30, 5, 220.00, 320.00],
            ['Dopamine 400mg/5ml', 'Dopamine', 'High-dose vasopressor', '400mg/5ml', 'Injection', 25, 5, 280.00, 400.00],
            ['Dobutamine 250mg/20ml', 'Dobutamine', 'Inotrope', '250mg/20ml', 'Injection', 28, 5, 260.00, 380.00],
            ['Norepinephrine 4mg/4ml', 'Norepinephrine', 'Vasopressor', '4mg/4ml', 'Injection', 25, 5, 300.00, 430.00],
            ['Norepinephrine 8mg/4ml', 'Norepinephrine', 'High-dose vasopressor', '8mg/4ml', 'Injection', 20, 5, 380.00, 550.00],
            ['Phenylephrine 10mg/ml', 'Phenylephrine', 'Vasopressor', '10mg/ml', 'Injection', 30, 5, 180.00, 260.00],
            ['Vasopressin 20IU/ml', 'Vasopressin', 'Vasopressor', '20IU/ml', 'Injection', 22, 5, 450.00, 650.00],
            ['Naloxone 0.4mg/ml', 'Naloxone', 'Opioid antagonist', '0.4mg/ml', 'Injection', 35, 10, 150.00, 220.00],
            ['Naloxone 2mg/2ml', 'Naloxone', 'High-dose naloxone', '2mg/2ml', 'Injection', 30, 5, 200.00, 290.00],
            ['Flumazenil 0.5mg/5ml', 'Flumazenil', 'Benzodiazepine antagonist', '0.5mg/5ml', 'Injection', 25, 5, 320.00, 460.00],
            ['Calcium Chloride 10% 10ml', 'Calcium Chloride', 'Calcium supplement', '10%', 'Injection', 30, 10, 75.00, 110.00],
            ['Sodium Bicarbonate 8.4% 50ml', 'Sodium Bicarbonate', 'Alkalinizing agent', '8.4%', 'Injection', 35, 10, 85.00, 125.00],
            ['Magnesium Sulfate 50% 2ml', 'Magnesium Sulfate', 'Magnesium supplement', '50%', 'Injection', 32, 10, 55.00, 80.00],
            ['Furosemide 20mg/2ml', 'Furosemide', 'Loop diuretic', '20mg/2ml', 'Injection', 40, 10, 35.00, 50.00],
            ['Furosemide 40mg/4ml', 'Furosemide', 'Standard diuretic', '40mg/4ml', 'Injection', 35, 10, 45.00, 65.00],
            ['Methylprednisolone 40mg', 'Methylprednisolone', 'Corticosteroid', '40mg', 'Injection', 30, 10, 85.00, 125.00],
            ['Methylprednisolone 125mg', 'Methylprednisolone', 'High-dose steroid', '125mg', 'Injection', 25, 5, 150.00, 220.00],
            ['Hydrocortisone 100mg', 'Hydrocortisone', 'Corticosteroid', '100mg', 'Injection', 35, 10, 75.00, 110.00],
            ['Hydrocortisone 500mg', 'Hydrocortisone', 'High-dose steroid', '500mg', 'Injection', 28, 5, 180.00, 260.00],
            ['Dexamethasone 4mg/ml', 'Dexamethasone', 'Corticosteroid', '4mg/ml', 'Injection', 40, 10, 65.00, 95.00],
        ];
        
        foreach ($emergency as $index => $med) {
            $data[] = $generateMedicine($med[0], $med[1], 'Emergency Drugs', $med[2], $med[3], $med[4], $med[5], $med[6], $med[7], $med[8], $index);
        }
        
        // 12. Medical Supplies (30 items)
        $medicalSupplies = [
            ['Syringe 1ml', 'Disposable Syringe', '1ml syringe', '1ml', 'Medical Supply', 200, 50, 8.00, 12.00],
            ['Syringe 3ml', 'Disposable Syringe', '3ml syringe', '3ml', 'Medical Supply', 180, 50, 10.00, 15.00],
            ['Syringe 5ml', 'Disposable Syringe', '5ml syringe', '5ml', 'Medical Supply', 170, 50, 12.00, 18.00],
            ['Syringe 10ml', 'Disposable Syringe', '10ml syringe', '10ml', 'Medical Supply', 150, 40, 15.00, 22.00],
            ['Syringe 20ml', 'Disposable Syringe', '20ml syringe', '20ml', 'Medical Supply', 120, 30, 18.00, 27.00],
            ['Needle 21G', 'Disposable Needle', '21 gauge needle', '21G', 'Medical Supply', 200, 50, 5.00, 7.50],
            ['Needle 23G', 'Disposable Needle', '23 gauge needle', '23G', 'Medical Supply', 190, 50, 5.50, 8.00],
            ['Needle 25G', 'Disposable Needle', '25 gauge needle', '25G', 'Medical Supply', 180, 50, 6.00, 9.00],
            ['IV Cannula 18G', 'IV Cannula', '18 gauge IV', '18G', 'Medical Supply', 100, 30, 25.00, 38.00],
            ['IV Cannula 20G', 'IV Cannula', '20 gauge IV', '20G', 'Medical Supply', 110, 30, 22.00, 33.00],
            ['IV Cannula 22G', 'IV Cannula', '22 gauge IV', '22G', 'Medical Supply', 120, 30, 20.00, 30.00],
            ['IV Cannula 24G', 'IV Cannula', '24 gauge IV', '24G', 'Medical Supply', 100, 30, 18.00, 27.00],
            ['IV Set', 'IV Administration Set', 'IV drip set', 'Standard', 'Medical Supply', 80, 25, 35.00, 50.00],
            ['Blood Transfusion Set', 'Blood Transfusion Set', 'Blood administration', 'Standard', 'Medical Supply', 50, 15, 85.00, 125.00],
            ['Gauze 2x2', 'Sterile Gauze', '2x2 inch gauze', '2x2', 'Medical Supply', 150, 40, 2.50, 3.75],
            ['Gauze 4x4', 'Sterile Gauze', '4x4 inch gauze', '4x4', 'Medical Supply', 140, 40, 4.00, 6.00],
            ['Cotton Balls', 'Sterile Cotton', 'Cotton balls', 'Standard', 'Medical Supply', 200, 50, 1.50, 2.25],
            ['Adhesive Tape', 'Medical Tape', 'Adhesive tape', '1 inch', 'Medical Supply', 100, 30, 45.00, 65.00],
            ['Bandage 2 inch', 'Elastic Bandage', '2 inch bandage', '2 inch', 'Medical Supply', 80, 25, 35.00, 50.00],
            ['Bandage 4 inch', 'Elastic Bandage', '4 inch bandage', '4 inch', 'Medical Supply', 70, 20, 45.00, 65.00],
            ['Alcohol Swabs', 'Alcohol Swabs', 'Disinfectant swabs', '70%', 'Medical Supply', 300, 75, 0.50, 0.75],
            ['Betadine Solution 120ml', 'Povidone-Iodine', 'Antiseptic', '10%', 'Medical Supply', 60, 20, 60.00, 85.00],
            ['Betadine Solution 500ml', 'Povidone-Iodine', 'Antiseptic', '10%', 'Medical Supply', 40, 10, 180.00, 260.00],
            ['Alcohol 70% 500ml', 'Ethyl Alcohol', 'Rubbing alcohol', '70%', 'Medical Supply', 80, 25, 25.00, 35.00],
            ['Alcohol 70% 1L', 'Ethyl Alcohol', 'Rubbing alcohol', '70%', 'Medical Supply', 60, 15, 40.00, 58.00],
            ['Hydrogen Peroxide 120ml', 'Hydrogen Peroxide', 'Antiseptic', '3%', 'Medical Supply', 70, 20, 18.00, 25.00],
            ['Hydrogen Peroxide 500ml', 'Hydrogen Peroxide', 'Antiseptic', '3%', 'Medical Supply', 50, 15, 55.00, 80.00],
            ['Urinary Catheter 14F', 'Foley Catheter', 'Urinary catheter', '14F', 'Medical Supply', 40, 15, 85.00, 125.00],
            ['Urinary Catheter 16F', 'Foley Catheter', 'Urinary catheter', '16F', 'Medical Supply', 35, 10, 95.00, 140.00],
            ['Urinary Catheter 18F', 'Foley Catheter', 'Urinary catheter', '18F', 'Medical Supply', 30, 10, 105.00, 150.00],
        ];
        
        foreach ($medicalSupplies as $index => $med) {
            $data[] = $generateMedicine($med[0], $med[1], 'Medical Supplies', $med[2], $med[3], $med[4], $med[5], $med[6], $med[7], $med[8], $index);
        }
        
        // Insert all medicines
        $this->db->table('pharmacy')->insertBatch($data);
        
        echo "✅ Inserted " . count($data) . " medicines into pharmacy table\n";
        echo "   - Analgesics / Antipyretics: 30\n";
        echo "   - Anti-inflammatory (NSAIDs): 30\n";
        echo "   - Antibiotics: 30\n";
        echo "   - Antihistamines: 30\n";
        echo "   - Cough & Cold (Respiratory): 30\n";
        echo "   - Gastrointestinal Medicines: 30\n";
        echo "   - Cardiovascular Medicines: 30\n";
        echo "   - Diabetic Medicines: 30\n";
        echo "   - Vitamins & Supplements: 30\n";
        echo "   - IV Fluids / Electrolytes: 30\n";
        echo "   - Emergency Drugs: 30\n";
        echo "   - Medical Supplies: 30\n";
    }
}
