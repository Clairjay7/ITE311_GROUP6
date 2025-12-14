// Philippine Address Data
const philippineAddresses = {
    provinces: [
        { code: 'NCR', name: 'Metro Manila (NCR)' },
        { code: 'ABR', name: 'Abra' },
        { code: 'AGN', name: 'Agusan del Norte' },
        { code: 'AGS', name: 'Agusan del Sur' },
        { code: 'AKL', name: 'Aklan' },
        { code: 'ALB', name: 'Albay' },
        { code: 'ANT', name: 'Antique' },
        { code: 'APA', name: 'Apayao' },
        { code: 'AUR', name: 'Aurora' },
        { code: 'BAS', name: 'Basilan' },
        { code: 'BAN', name: 'Bataan' },
        { code: 'BTN', name: 'Batanes' },
        { code: 'BTG', name: 'Batangas' },
        { code: 'BEN', name: 'Benguet' },
        { code: 'BIL', name: 'Biliran' },
        { code: 'BOH', name: 'Bohol' },
        { code: 'BUK', name: 'Bukidnon' },
        { code: 'BUL', name: 'Bulacan' },
        { code: 'CAG', name: 'Cagayan' },
        { code: 'CAN', name: 'Camarines Norte' },
        { code: 'CAS', name: 'Camarines Sur' },
        { code: 'CAM', name: 'Camiguin' },
        { code: 'CAP', name: 'Capiz' },
        { code: 'CAT', name: 'Catanduanes' },
        { code: 'CAV', name: 'Cavite' },
        { code: 'CEB', name: 'Cebu' },
        { code: 'COM', name: 'Compostela Valley' },
        { code: 'NCO', name: 'Cotabato' },
        { code: 'DAV', name: 'Davao del Norte' },
        { code: 'DAS', name: 'Davao del Sur' },
        { code: 'DAO', name: 'Davao Occidental' },
        { code: 'DAE', name: 'Davao Oriental' },
        { code: 'DIN', name: 'Dinagat Islands' },
        { code: 'EAS', name: 'Eastern Samar' },
        { code: 'GUI', name: 'Guimaras' },
        { code: 'IFU', name: 'Ifugao' },
        { code: 'ILN', name: 'Ilocos Norte' },
        { code: 'ILS', name: 'Ilocos Sur' },
        { code: 'ILI', name: 'Iloilo' },
        { code: 'ISA', name: 'Isabela' },
        { code: 'KAL', name: 'Kalinga' },
        { code: 'LUN', name: 'La Union' },
        { code: 'LAG', name: 'Laguna' },
        { code: 'LAN', name: 'Lanao del Norte' },
        { code: 'LAS', name: 'Lanao del Sur' },
        { code: 'LEY', name: 'Leyte' },
        { code: 'MAG', name: 'Maguindanao' },
        { code: 'MAD', name: 'Marinduque' },
        { code: 'MAS', name: 'Masbate' },
        { code: 'MSC', name: 'Misamis Occidental' },
        { code: 'MSR', name: 'Misamis Oriental' },
        { code: 'MOU', name: 'Mountain Province' },
        { code: 'NEC', name: 'Negros Occidental' },
        { code: 'NER', name: 'Negros Oriental' },
        { code: 'NSA', name: 'Northern Samar' },
        { code: 'NUE', name: 'Nueva Ecija' },
        { code: 'NUV', name: 'Nueva Vizcaya' },
        { code: 'MDC', name: 'Occidental Mindoro' },
        { code: 'MDR', name: 'Oriental Mindoro' },
        { code: 'PLW', name: 'Palawan' },
        { code: 'PAM', name: 'Pampanga' },
        { code: 'PAN', name: 'Pangasinan' },
        { code: 'QUE', name: 'Quezon' },
        { code: 'QUI', name: 'Quirino' },
        { code: 'RIZ', name: 'Rizal' },
        { code: 'ROM', name: 'Romblon' },
        { code: 'WSA', name: 'Samar' },
        { code: 'SAR', name: 'Sarangani' },
        { code: 'SIQ', name: 'Siquijor' },
        { code: 'SOR', name: 'Sorsogon' },
        { code: 'SCO', name: 'South Cotabato' },
        { code: 'SLE', name: 'Southern Leyte' },
        { code: 'SUK', name: 'Sultan Kudarat' },
        { code: 'SLU', name: 'Sulu' },
        { code: 'SUN', name: 'Surigao del Norte' },
        { code: 'SUR', name: 'Surigao del Sur' },
        { code: 'TAR', name: 'Tarlac' },
        { code: 'TAW', name: 'Tawi-Tawi' },
        { code: 'ZMB', name: 'Zambales' },
        { code: 'ZAN', name: 'Zamboanga del Norte' },
        { code: 'ZAS', name: 'Zamboanga del Sur' },
        { code: 'ZSI', name: 'Zamboanga Sibugay' }
    ],
    
    cities: {
        'NCR': [
            'Caloocan', 'Las Piñas', 'Makati', 'Malabon', 'Mandaluyong', 'Manila', 'Marikina', 
            'Muntinlupa', 'Navotas', 'Parañaque', 'Pasay', 'Pasig', 'Pateros', 'Quezon City', 
            'San Juan', 'Taguig', 'Valenzuela'
        ],
        'CAV': [
            'Bacoor', 'Cavite City', 'Dasmariñas', 'Imus', 'Tagaytay', 'Trece Martires'
        ],
        'LAG': [
            'Biñan', 'Calamba', 'San Pablo', 'Santa Rosa'
        ],
        'BUL': [
            'Malolos', 'Meycauayan', 'San Jose del Monte'
        ],
        'RIZ': [
            'Antipolo', 'Cainta', 'Taytay'
        ],
        'PAM': [
            'Angeles', 'San Fernando'
        ],
        'BAT': [
            'Batangas City', 'Lipa', 'Tanauan'
        ],
        'CEB': [
            'Cebu City', 'Lapu-Lapu', 'Mandaue', 'Talisay'
        ],
        'DAV': [
            'Davao City', 'Tagum'
        ],
        'ILO': [
            'Iloilo City', 'Passi'
        ],
        'NEC': [
            'Bacolod', 'Bago', 'Cadiz', 'Escalante', 'Himamaylan', 'Kabankalan', 'La Carlota', 
            'Sagay', 'San Carlos', 'Silay', 'Sipalay', 'Talisay', 'Victorias'
        ],
        'PAM': [
            'Angeles', 'San Fernando'
        ]
    },
    
    barangays: {
        // Common barangays - this is a simplified list
        // In production, you'd want a complete database
        'Manila': ['Binondo', 'Ermita', 'Intramuros', 'Malate', 'Paco', 'Pandacan', 'Quiapo', 'Sampaloc', 'San Andres', 'San Miguel', 'San Nicolas', 'Santa Ana', 'Santa Cruz', 'Santa Mesa', 'Tondo'],
        'Quezon City': ['Bagong Silangan', 'Balingasa', 'Balintawak', 'Bungad', 'Damar', 'Diliman', 'Katipunan', 'Loyola Heights', 'Project 4', 'Project 6', 'Project 7', 'Project 8', 'Roxas', 'San Antonio', 'Santo Domingo', 'Tandang Sora', 'UP Campus', 'West Triangle'],
        'Makati': ['Bel-Air', 'Cembo', 'Comembo', 'Dasmariñas', 'Forbes Park', 'Guadalupe Nuevo', 'Guadalupe Viejo', 'Kasilawan', 'Magallanes', 'Olympia', 'Palanan', 'Pembo', 'Pio del Pilar', 'Pitogo', 'Poblacion', 'Rizal', 'San Antonio', 'San Isidro', 'San Lorenzo', 'Singkamas', 'South Cembo', 'Tejeros', 'Urdaneta', 'Valenzuela', 'West Rembo'],
        'Pasig': ['Bagong Ilog', 'Bagong Katipunan', 'Bambang', 'Buting', 'Caniogan', 'Dela Paz', 'Kapitolyo', 'Manggahan', 'Maybunga', 'Ortigas', 'Palatiw', 'Pinagbuhatan', 'Rosario', 'San Antonio', 'San Joaquin', 'San Jose', 'San Miguel', 'San Nicolas', 'Santa Cruz', 'Santa Lucia', 'Santa Rosa', 'Santo Tomas', 'Santolan', 'Sumilang', 'Ugong']
    }
};

// Function to populate provinces
function populateProvinces(selectId) {
    const select = document.getElementById(selectId);
    if (!select) return;
    
    select.innerHTML = '<option value="">-- Select Province --</option>';
    philippineAddresses.provinces.forEach(province => {
        const option = document.createElement('option');
        option.value = province.code;
        option.textContent = province.name;
        select.appendChild(option);
    });
}

// Function to populate cities based on province
function populateCities(provinceCode, citySelectId) {
    const citySelect = document.getElementById(citySelectId);
    if (!citySelect) return;
    
    citySelect.innerHTML = '<option value="">-- Select City/Municipality --</option>';
    citySelect.disabled = true;
    
    if (!provinceCode) {
        return;
    }
    
    // Get cities for the selected province
    const cities = philippineAddresses.cities[provinceCode] || [];
    
    if (cities.length > 0) {
        cities.forEach(city => {
            const option = document.createElement('option');
            option.value = city;
            option.textContent = city;
            citySelect.appendChild(option);
        });
        citySelect.disabled = false;
    } else {
        // If no predefined cities, allow manual entry
        citySelect.innerHTML = '<option value="">-- Enter City/Municipality --</option>';
        citySelect.disabled = false;
    }
}

// Function to populate barangays based on city
function populateBarangays(cityName, barangaySelectId) {
    const barangaySelect = document.getElementById(barangaySelectId);
    if (!barangaySelect) return;
    
    barangaySelect.innerHTML = '<option value="">-- Select Barangay --</option>';
    barangaySelect.disabled = true;
    
    if (!cityName) {
        return;
    }
    
    // Get barangays for the selected city
    const barangays = philippineAddresses.barangays[cityName] || [];
    
    if (barangays.length > 0) {
        barangays.forEach(barangay => {
            const option = document.createElement('option');
            option.value = barangay;
            option.textContent = barangay;
            barangaySelect.appendChild(option);
        });
        barangaySelect.disabled = false;
    } else {
        // If no predefined barangays, allow manual entry
        barangaySelect.innerHTML = '<option value="">-- Enter Barangay --</option>';
        barangaySelect.disabled = false;
    }
}

