<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class PharmacySeeder extends Seeder
{
    public function run()
    {
        $data = [
            // Pain Relievers
            ['item_name' => 'Paracetamol 500mg', 'description' => 'Pain reliever and fever reducer', 'quantity' => 150, 'price' => 5.00, 'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s')],
            ['item_name' => 'Biogesic 500mg', 'description' => 'Paracetamol tablet', 'quantity' => 120, 'price' => 6.50, 'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s')],
            ['item_name' => 'Ibuprofen 400mg', 'description' => 'Anti-inflammatory', 'quantity' => 80, 'price' => 8.00, 'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s')],
            ['item_name' => 'Mefenamic Acid 500mg', 'description' => 'Pain reliever', 'quantity' => 60, 'price' => 9.00, 'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s')],
            ['item_name' => 'Aspirin 80mg', 'description' => 'Low-dose aspirin', 'quantity' => 100, 'price' => 4.50, 'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s')],
            
            // Antibiotics
            ['item_name' => 'Amoxicillin 500mg', 'description' => 'Antibiotic capsule', 'quantity' => 75, 'price' => 15.00, 'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s')],
            ['item_name' => 'Cefalexin 500mg', 'description' => 'Cephalosporin antibiotic', 'quantity' => 50, 'price' => 18.00, 'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s')],
            ['item_name' => 'Azithromycin 500mg', 'description' => 'Macrolide antibiotic', 'quantity' => 40, 'price' => 25.00, 'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s')],
            ['item_name' => 'Ciprofloxacin 500mg', 'description' => 'Fluoroquinolone antibiotic', 'quantity' => 45, 'price' => 20.00, 'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s')],
            ['item_name' => 'Amoxicillin + Clavulanate 625mg', 'description' => 'Augmentin', 'quantity' => 35, 'price' => 28.00, 'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s')],
            
            // Antihistamines
            ['item_name' => 'Cetirizine 10mg', 'description' => 'Antihistamine', 'quantity' => 90, 'price' => 7.00, 'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s')],
            ['item_name' => 'Loratadine 10mg', 'description' => 'Non-drowsy antihistamine', 'quantity' => 85, 'price' => 8.50, 'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s')],
            ['item_name' => 'Chlorphenamine 4mg', 'description' => 'Antihistamine', 'quantity' => 70, 'price' => 5.50, 'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s')],
            
            // Cough & Cold
            ['item_name' => 'Carbocisteine 500mg', 'description' => 'Mucolytic', 'quantity' => 65, 'price' => 10.00, 'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s')],
            ['item_name' => 'Salbutamol 2mg', 'description' => 'Bronchodilator', 'quantity' => 55, 'price' => 12.00, 'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s')],
            ['item_name' => 'Dextromethorphan 15mg', 'description' => 'Cough suppressant', 'quantity' => 60, 'price' => 8.00, 'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s')],
            
            // Gastrointestinal
            ['item_name' => 'Omeprazole 20mg', 'description' => 'Proton pump inhibitor', 'quantity' => 70, 'price' => 12.00, 'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s')],
            ['item_name' => 'Loperamide 2mg', 'description' => 'Anti-diarrheal', 'quantity' => 45, 'price' => 6.00, 'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s')],
            ['item_name' => 'Buscopan 10mg', 'description' => 'Antispasmodic', 'quantity' => 50, 'price' => 15.00, 'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s')],
            ['item_name' => 'Antacid Suspension', 'description' => 'Aluminum + Magnesium Hydroxide', 'quantity' => 40, 'price' => 45.00, 'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s')],
            
            // Vitamins
            ['item_name' => 'Vitamin C 500mg', 'description' => 'Ascorbic acid', 'quantity' => 150, 'price' => 4.00, 'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s')],
            ['item_name' => 'Vitamin B Complex', 'description' => 'B-complex vitamins', 'quantity' => 100, 'price' => 8.00, 'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s')],
            ['item_name' => 'Multivitamins', 'description' => 'Complete multivitamin', 'quantity' => 90, 'price' => 12.00, 'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s')],
            ['item_name' => 'Calcium + Vitamin D', 'description' => 'Bone health', 'quantity' => 75, 'price' => 18.00, 'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s')],
            ['item_name' => 'Iron + Folic Acid', 'description' => 'For anemia', 'quantity' => 60, 'price' => 10.00, 'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s')],
            
            // Diabetes
            ['item_name' => 'Metformin 500mg', 'description' => 'Oral antidiabetic', 'quantity' => 80, 'price' => 8.00, 'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s')],
            ['item_name' => 'Glimepiride 2mg', 'description' => 'Sulfonylurea', 'quantity' => 50, 'price' => 12.00, 'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s')],
            
            // Hypertension
            ['item_name' => 'Amlodipine 5mg', 'description' => 'Calcium channel blocker', 'quantity' => 85, 'price' => 10.00, 'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s')],
            ['item_name' => 'Losartan 50mg', 'description' => 'ARB for hypertension', 'quantity' => 70, 'price' => 15.00, 'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s')],
            ['item_name' => 'Atenolol 50mg', 'description' => 'Beta blocker', 'quantity' => 60, 'price' => 12.00, 'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s')],
            
            // Topical
            ['item_name' => 'Betadine Solution 120ml', 'description' => 'Antiseptic', 'quantity' => 30, 'price' => 85.00, 'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s')],
            ['item_name' => 'Alcohol 70% 500ml', 'description' => 'Rubbing alcohol', 'quantity' => 50, 'price' => 35.00, 'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s')],
            ['item_name' => 'Hydrogen Peroxide 120ml', 'description' => 'Antiseptic', 'quantity' => 40, 'price' => 25.00, 'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s')],
            ['item_name' => 'Hydrocortisone Cream 1%', 'description' => 'Topical corticosteroid', 'quantity' => 35, 'price' => 45.00, 'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s')],
            
            // Emergency
            ['item_name' => 'Epinephrine 1mg/ml', 'description' => 'Emergency anaphylaxis', 'quantity' => 20, 'price' => 150.00, 'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s')],
            ['item_name' => 'Dextrose 5% 1L', 'description' => 'IV fluid', 'quantity' => 25, 'price' => 120.00, 'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s')],
            ['item_name' => 'Normal Saline 0.9% 1L', 'description' => 'IV fluid', 'quantity' => 30, 'price' => 100.00, 'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s')],
            
            // Low stock (for testing)
            ['item_name' => 'Insulin Regular 100IU/ml', 'description' => 'Short-acting insulin', 'quantity' => 8, 'price' => 250.00, 'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s')],
            ['item_name' => 'Nitroglycerin 0.5mg', 'description' => 'For angina', 'quantity' => 5, 'price' => 180.00, 'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s')],
            ['item_name' => 'Warfarin 5mg', 'description' => 'Anticoagulant', 'quantity' => 7, 'price' => 25.00, 'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s')],
            
            // Out of stock (for testing)
            ['item_name' => 'Morphine 10mg', 'description' => 'Opioid analgesic', 'quantity' => 0, 'price' => 200.00, 'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s')],
            ['item_name' => 'Diazepam 5mg', 'description' => 'Benzodiazepine', 'quantity' => 0, 'price' => 15.00, 'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s')],
        ];

        $this->db->table('pharmacy')->insertBatch($data);
        
        echo "✅ Inserted " . count($data) . " medicines into pharmacy table\n";
    }
}

