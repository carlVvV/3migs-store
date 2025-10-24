<?php

namespace App\Services;

use App\Models\PSGCRegion;
use App\Models\PSGCProvince;
use App\Models\PSGCCity;
use App\Models\PSGCBarangay;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

class PSGCService
{
    private $baseUrl = 'https://psgc.gitlab.io/api';
    private $cacheTimeout = 3600; // 1 hour

    /**
     * Get all regions
     */
    public function getRegions()
    {
        return Cache::remember('psgc_gitlab_regions', $this->cacheTimeout, function () {
            $response = Http::get("{$this->baseUrl}/regions");
            if ($response->successful()) {
                $regions = $response->json();
                $mappedRegions = array_map(function ($region) {
                    // Handle special cases for better display names
                    $displayName = $region['regionName'] . ' – ' . $region['name'];
                    
                    // Fix redundant MIMAROPA
                    if ($region['code'] === '170000000') {
                        $displayName = 'MIMAROPA Region';
                    }
                    // Fix NCR order
                    elseif ($region['code'] === '130000000') {
                        $displayName = 'NCR – National Capital Region';
                    }
                    // Fix CAR order
                    elseif ($region['code'] === '140000000') {
                        $displayName = 'CAR – Cordillera Administrative Region';
                    }
                    // Fix BARMM order
                    elseif ($region['code'] === '150000000') {
                        $displayName = 'BARMM – Bangsamoro Autonomous Region in Muslim Mindanao';
                    }
                    
                    return [
                        'code' => $region['code'],
                        'name' => $displayName,
                        'regionName' => $region['regionName'],
                        'islandGroupCode' => $region['islandGroupCode'],
                    ];
                }, $regions);
                
                // Add missing NIR (Negros Island Region) - not included in GitLab API
                $mappedRegions[] = [
                    'code' => '180000000',
                    'name' => 'NIR – Negros Island Region',
                    'regionName' => 'NIR',
                    'islandGroupCode' => 'visayas',
                ];
                
                return $mappedRegions;
            }
            return [];
        });
    }

    /**
     * Get provinces by region code
     */
    public function getProvincesByRegion($regionCode)
    {
        return Cache::remember("psgc_gitlab_provinces_{$regionCode}", $this->cacheTimeout, function () use ($regionCode) {
            // Handle NIR (Negros Island Region) - not available in GitLab API
            if ($regionCode === '180000000') {
                return [
                    [
                        'code' => '061900000',
                        'name' => 'Negros Occidental',
                        'regionCode' => '180000000',
                        'regionName' => 'NIR',
                    ],
                    [
                        'code' => '074600000',
                        'name' => 'Negros Oriental',
                        'regionCode' => '180000000',
                        'regionName' => 'NIR',
                    ],
                    [
                        'code' => '074800000',
                        'name' => 'Siquijor',
                        'regionCode' => '180000000',
                        'regionName' => 'NIR',
                    ],
                ];
            }
            
            $response = Http::get("{$this->baseUrl}/regions/{$regionCode}/provinces");
            if ($response->successful()) {
                $provinces = $response->json();
                return array_map(function ($province) {
                    return [
                        'code' => $province['code'],
                        'name' => $province['name'],
                        'regionCode' => $province['regionCode'],
                        'regionName' => $province['regionName'] ?? null,
                    ];
                }, $provinces);
            }
            return [];
        });
    }

    /**
     * Get cities/municipalities by province code
     */
    public function getCitiesByProvince($provinceCode)
    {
        return Cache::remember("psgc_gitlab_cities_{$provinceCode}", $this->cacheTimeout, function () use ($provinceCode) {
            // Handle Bulacan province with complete list of cities and municipalities
            if ($provinceCode === '031400000') {
                return [
                    ['code' => '031401000', 'name' => 'Municipality of Angat', 'type' => 'Municipality', 'zipCode' => null, 'districtCode' => '6th Congressional District', 'isCapital' => false],
                    ['code' => '031402000', 'name' => 'Municipality of Balagtas', 'type' => 'Municipality', 'zipCode' => null, 'districtCode' => '5th Congressional District', 'isCapital' => false],
                    ['code' => '031403000', 'name' => 'City of Baliwag', 'type' => 'City', 'zipCode' => null, 'districtCode' => '2nd Congressional District', 'isCapital' => false],
                    ['code' => '031404000', 'name' => 'Municipality of Bocaue', 'type' => 'Municipality', 'zipCode' => null, 'districtCode' => '5th Congressional District', 'isCapital' => false],
                    ['code' => '031405000', 'name' => 'Municipality of Bulakan', 'type' => 'Municipality', 'zipCode' => null, 'districtCode' => '1st Congressional District', 'isCapital' => false],
                    ['code' => '031406000', 'name' => 'Municipality of Bustos', 'type' => 'Municipality', 'zipCode' => null, 'districtCode' => '2nd Congressional District', 'isCapital' => false],
                    ['code' => '031407000', 'name' => 'Municipality of Calumpit', 'type' => 'Municipality', 'zipCode' => null, 'districtCode' => '1st Congressional District', 'isCapital' => false],
                    ['code' => '031408000', 'name' => 'Municipality of Dona Remedios Trinidad', 'type' => 'Municipality', 'zipCode' => null, 'districtCode' => '3rd Congressional District', 'isCapital' => false],
                    ['code' => '031409000', 'name' => 'Municipality of Guiguinto', 'type' => 'Municipality', 'zipCode' => null, 'districtCode' => '5th Congressional District', 'isCapital' => false],
                    ['code' => '031410000', 'name' => 'Municipality of Hagonoy', 'type' => 'Municipality', 'zipCode' => null, 'districtCode' => '1st Congressional District', 'isCapital' => false],
                    ['code' => '031411000', 'name' => 'City of Malolos', 'type' => 'City', 'zipCode' => null, 'districtCode' => '1st Congressional District', 'isCapital' => true],
                    ['code' => '031412000', 'name' => 'Municipality of Marilao', 'type' => 'Municipality', 'zipCode' => null, 'districtCode' => '4th Congressional District', 'isCapital' => false],
                    ['code' => '031413000', 'name' => 'City of Meycauayan', 'type' => 'City', 'zipCode' => null, 'districtCode' => '4th Congressional District', 'isCapital' => false],
                    ['code' => '031414000', 'name' => 'Municipality of Norzagaray', 'type' => 'Municipality', 'zipCode' => null, 'districtCode' => '6th Congressional District', 'isCapital' => false],
                    ['code' => '031415000', 'name' => 'Municipality of Obando', 'type' => 'Municipality', 'zipCode' => null, 'districtCode' => '4th Congressional District', 'isCapital' => false],
                    ['code' => '031416000', 'name' => 'Municipality of Pandi', 'type' => 'Municipality', 'zipCode' => null, 'districtCode' => '5th Congressional District', 'isCapital' => false],
                    ['code' => '031417000', 'name' => 'Municipality of Paombong', 'type' => 'Municipality', 'zipCode' => null, 'districtCode' => '1st Congressional District', 'isCapital' => false],
                    ['code' => '031418000', 'name' => 'Municipality of Plaridel', 'type' => 'Municipality', 'zipCode' => null, 'districtCode' => '2nd Congressional District', 'isCapital' => false],
                    ['code' => '031419000', 'name' => 'Municipality of Pulilan', 'type' => 'Municipality', 'zipCode' => null, 'districtCode' => '1st Congressional District', 'isCapital' => false],
                    ['code' => '031420000', 'name' => 'Municipality of San Ildefonso', 'type' => 'Municipality', 'zipCode' => null, 'districtCode' => '3rd Congressional District', 'isCapital' => false],
                    ['code' => '031421000', 'name' => 'City of San Jose Del Monte', 'type' => 'City', 'zipCode' => null, 'districtCode' => 'Lone District of San Jose del Monte', 'isCapital' => false],
                    ['code' => '031422000', 'name' => 'Municipality of San Miguel', 'type' => 'Municipality', 'zipCode' => null, 'districtCode' => '3rd Congressional District', 'isCapital' => false],
                    ['code' => '031423000', 'name' => 'Municipality of San Rafael', 'type' => 'Municipality', 'zipCode' => null, 'districtCode' => '3rd Congressional District', 'isCapital' => false],
                    ['code' => '031424000', 'name' => 'Municipality of Santa Maria', 'type' => 'Municipality', 'zipCode' => null, 'districtCode' => '6th Congressional District', 'isCapital' => false],
                ];
            }
            
            $response = Http::get("{$this->baseUrl}/provinces/{$provinceCode}/cities");
            if ($response->successful()) {
                $cities = $response->json();
                return array_map(function ($city) {
                    return [
                        'code' => $city['code'],
                        'name' => $city['name'],
                        'type' => $city['type'] ?? null,
                        'zipCode' => $city['zipCode'] ?? null,
                        'districtCode' => $city['districtCode'] ?? null,
                        'isCapital' => $city['isCapital'] ?? false,
                    ];
                }, $cities);
            }
            return [];
        });
    }

    /**
     * Get cities/municipalities by region code (for NCR)
     */
    public function getCitiesByRegion($regionCode)
    {
        return Cache::remember("psgc_gitlab_cities_region_{$regionCode}", $this->cacheTimeout, function () use ($regionCode) {
            $response = Http::get("{$this->baseUrl}/regions/{$regionCode}/cities");
            if ($response->successful()) {
                $cities = $response->json();
                return array_map(function ($city) {
                    return [
                        'code' => $city['code'],
                        'name' => $city['name'],
                        'type' => $city['type'] ?? null,
                        'zipCode' => $city['zipCode'] ?? null,
                        'districtCode' => $city['districtCode'] ?? null,
                        'isCapital' => $city['isCapital'] ?? false,
                        'regionCode' => $city['regionCode'],
                        'provinceCode' => $city['provinceCode'] ?? null,
                    ];
                }, $cities);
            }
            return [];
        });
    }

    /**
     * Get barangays by city/municipality code
     */
    public function getBarangaysByCity($cityCode)
    {
        return Cache::remember("psgc_gitlab_barangays_{$cityCode}", $this->cacheTimeout, function () use ($cityCode) {
            // Handle Bulacan cities and municipalities with complete barangay lists
            if ($cityCode === '031403000') { // City of Baliwag
                return [
                    ['code' => '031403001', 'name' => 'Bagong Nayon', 'cityCode' => '031403000', 'regionCode' => '030000000', 'provinceCode' => '031400000', 'subMunicipalityCode' => null],
                    ['code' => '031403002', 'name' => 'Barangca', 'cityCode' => '031403000', 'regionCode' => '030000000', 'provinceCode' => '031400000', 'subMunicipalityCode' => null],
                    ['code' => '031403003', 'name' => 'Calantipay', 'cityCode' => '031403000', 'regionCode' => '030000000', 'provinceCode' => '031400000', 'subMunicipalityCode' => null],
                    ['code' => '031403004', 'name' => 'Catulinan', 'cityCode' => '031403000', 'regionCode' => '030000000', 'provinceCode' => '031400000', 'subMunicipalityCode' => null],
                    ['code' => '031403005', 'name' => 'Concepcion', 'cityCode' => '031403000', 'regionCode' => '030000000', 'provinceCode' => '031400000', 'subMunicipalityCode' => null],
                    ['code' => '031403006', 'name' => 'Hinukay', 'cityCode' => '031403000', 'regionCode' => '030000000', 'provinceCode' => '031400000', 'subMunicipalityCode' => null],
                    ['code' => '031403007', 'name' => 'Makina', 'cityCode' => '031403000', 'regionCode' => '030000000', 'provinceCode' => '031400000', 'subMunicipalityCode' => null],
                    ['code' => '031403008', 'name' => 'Matangtubig', 'cityCode' => '031403000', 'regionCode' => '030000000', 'provinceCode' => '031400000', 'subMunicipalityCode' => null],
                    ['code' => '031403009', 'name' => 'Pagala', 'cityCode' => '031403000', 'regionCode' => '030000000', 'provinceCode' => '031400000', 'subMunicipalityCode' => null],
                    ['code' => '031403010', 'name' => 'Paitan', 'cityCode' => '031403000', 'regionCode' => '030000000', 'provinceCode' => '031400000', 'subMunicipalityCode' => null],
                    ['code' => '031403011', 'name' => 'Piel', 'cityCode' => '031403000', 'regionCode' => '030000000', 'provinceCode' => '031400000', 'subMunicipalityCode' => null],
                    ['code' => '031403012', 'name' => 'Pinagbarilan', 'cityCode' => '031403000', 'regionCode' => '030000000', 'provinceCode' => '031400000', 'subMunicipalityCode' => null],
                    ['code' => '031403013', 'name' => 'Poblacion', 'cityCode' => '031403000', 'regionCode' => '030000000', 'provinceCode' => '031400000', 'subMunicipalityCode' => null],
                    ['code' => '031403014', 'name' => 'Sabang', 'cityCode' => '031403000', 'regionCode' => '030000000', 'provinceCode' => '031400000', 'subMunicipalityCode' => null],
                    ['code' => '031403015', 'name' => 'San Jose', 'cityCode' => '031403000', 'regionCode' => '030000000', 'provinceCode' => '031400000', 'subMunicipalityCode' => null],
                    ['code' => '031403016', 'name' => 'San Roque', 'cityCode' => '031403000', 'regionCode' => '030000000', 'provinceCode' => '031400000', 'subMunicipalityCode' => null],
                    ['code' => '031403017', 'name' => 'Santa Barbara', 'cityCode' => '031403000', 'regionCode' => '030000000', 'provinceCode' => '031400000', 'subMunicipalityCode' => null],
                    ['code' => '031403018', 'name' => 'Santo Cristo', 'cityCode' => '031403000', 'regionCode' => '030000000', 'provinceCode' => '031400000', 'subMunicipalityCode' => null],
                    ['code' => '031403019', 'name' => 'Sulivan', 'cityCode' => '031403000', 'regionCode' => '030000000', 'provinceCode' => '031400000', 'subMunicipalityCode' => null],
                    ['code' => '031403020', 'name' => 'Tangos', 'cityCode' => '031403000', 'regionCode' => '030000000', 'provinceCode' => '031400000', 'subMunicipalityCode' => null],
                    ['code' => '031403021', 'name' => 'Tarcan', 'cityCode' => '031403000', 'regionCode' => '030000000', 'provinceCode' => '031400000', 'subMunicipalityCode' => null],
                    ['code' => '031403022', 'name' => 'Tiaong', 'cityCode' => '031403000', 'regionCode' => '030000000', 'provinceCode' => '031400000', 'subMunicipalityCode' => null],
                    ['code' => '031403023', 'name' => 'Tibag', 'cityCode' => '031403000', 'regionCode' => '030000000', 'provinceCode' => '031400000', 'subMunicipalityCode' => null],
                    ['code' => '031403024', 'name' => 'Tilapayong', 'cityCode' => '031403000', 'regionCode' => '030000000', 'provinceCode' => '031400000', 'subMunicipalityCode' => null],
                    ['code' => '031403025', 'name' => 'Virgen Delas Flores', 'cityCode' => '031403000', 'regionCode' => '030000000', 'provinceCode' => '031400000', 'subMunicipalityCode' => null],
                    ['code' => '031403026', 'name' => 'Wakas', 'cityCode' => '031403000', 'regionCode' => '030000000', 'provinceCode' => '031400000', 'subMunicipalityCode' => null],
                    ['code' => '031403027', 'name' => 'Wawa', 'cityCode' => '031403000', 'regionCode' => '030000000', 'provinceCode' => '031400000', 'subMunicipalityCode' => null],
                ];
            }
            elseif ($cityCode === '031411000') { // City of Malolos
                return [
                    ['code' => '031411001', 'name' => 'Atlag', 'cityCode' => '031411000', 'regionCode' => '030000000', 'provinceCode' => '031400000', 'subMunicipalityCode' => null],
                    ['code' => '031411002', 'name' => 'Bagna', 'cityCode' => '031411000', 'regionCode' => '030000000', 'provinceCode' => '031400000', 'subMunicipalityCode' => null],
                    ['code' => '031411003', 'name' => 'Bagong Bayan', 'cityCode' => '031411000', 'regionCode' => '030000000', 'provinceCode' => '031400000', 'subMunicipalityCode' => null],
                    ['code' => '031411004', 'name' => 'Balite', 'cityCode' => '031411000', 'regionCode' => '030000000', 'provinceCode' => '031400000', 'subMunicipalityCode' => null],
                    ['code' => '031411005', 'name' => 'Bambang', 'cityCode' => '031411000', 'regionCode' => '030000000', 'provinceCode' => '031400000', 'subMunicipalityCode' => null],
                    ['code' => '031411006', 'name' => 'Barihan', 'cityCode' => '031411000', 'regionCode' => '030000000', 'provinceCode' => '031400000', 'subMunicipalityCode' => null],
                    ['code' => '031411007', 'name' => 'Bungahan', 'cityCode' => '031411000', 'regionCode' => '030000000', 'provinceCode' => '031400000', 'subMunicipalityCode' => null],
                    ['code' => '031411008', 'name' => 'Caliligawan', 'cityCode' => '031411000', 'regionCode' => '030000000', 'provinceCode' => '031400000', 'subMunicipalityCode' => null],
                    ['code' => '031411009', 'name' => 'Canalate', 'cityCode' => '031411000', 'regionCode' => '030000000', 'provinceCode' => '031400000', 'subMunicipalityCode' => null],
                    ['code' => '031411010', 'name' => 'Caniogan', 'cityCode' => '031411000', 'regionCode' => '030000000', 'provinceCode' => '031400000', 'subMunicipalityCode' => null],
                    ['code' => '031411011', 'name' => 'Catmon', 'cityCode' => '031411000', 'regionCode' => '030000000', 'provinceCode' => '031400000', 'subMunicipalityCode' => null],
                    ['code' => '031411012', 'name' => 'Cofradia', 'cityCode' => '031411000', 'regionCode' => '030000000', 'provinceCode' => '031400000', 'subMunicipalityCode' => null],
                    ['code' => '031411013', 'name' => 'Dakila', 'cityCode' => '031411000', 'regionCode' => '030000000', 'provinceCode' => '031400000', 'subMunicipalityCode' => null],
                    ['code' => '031411014', 'name' => 'Guinhawa', 'cityCode' => '031411000', 'regionCode' => '030000000', 'provinceCode' => '031400000', 'subMunicipalityCode' => null],
                    ['code' => '031411015', 'name' => 'Ligas', 'cityCode' => '031411000', 'regionCode' => '030000000', 'provinceCode' => '031400000', 'subMunicipalityCode' => null],
                    ['code' => '031411016', 'name' => 'Liang', 'cityCode' => '031411000', 'regionCode' => '030000000', 'provinceCode' => '031400000', 'subMunicipalityCode' => null],
                    ['code' => '031411017', 'name' => 'Longos', 'cityCode' => '031411000', 'regionCode' => '030000000', 'provinceCode' => '031400000', 'subMunicipalityCode' => null],
                    ['code' => '031411018', 'name' => 'Mabolo', 'cityCode' => '031411000', 'regionCode' => '030000000', 'provinceCode' => '031400000', 'subMunicipalityCode' => null],
                    ['code' => '031411019', 'name' => 'Mambog', 'cityCode' => '031411000', 'regionCode' => '030000000', 'provinceCode' => '031400000', 'subMunicipalityCode' => null],
                    ['code' => '031411020', 'name' => 'Masile', 'cityCode' => '031411000', 'regionCode' => '030000000', 'provinceCode' => '031400000', 'subMunicipalityCode' => null],
                    ['code' => '031411021', 'name' => 'Matimbo', 'cityCode' => '031411000', 'regionCode' => '030000000', 'provinceCode' => '031400000', 'subMunicipalityCode' => null],
                    ['code' => '031411022', 'name' => 'Mojon', 'cityCode' => '031411000', 'regionCode' => '030000000', 'provinceCode' => '031400000', 'subMunicipalityCode' => null],
                    ['code' => '031411023', 'name' => 'Namayan', 'cityCode' => '031411000', 'regionCode' => '030000000', 'provinceCode' => '031400000', 'subMunicipalityCode' => null],
                    ['code' => '031411024', 'name' => 'Niugan', 'cityCode' => '031411000', 'regionCode' => '030000000', 'provinceCode' => '031400000', 'subMunicipalityCode' => null],
                    ['code' => '031411025', 'name' => 'Pamitan', 'cityCode' => '031411000', 'regionCode' => '030000000', 'provinceCode' => '031400000', 'subMunicipalityCode' => null],
                    ['code' => '031411026', 'name' => 'Panasahan', 'cityCode' => '031411000', 'regionCode' => '030000000', 'provinceCode' => '031400000', 'subMunicipalityCode' => null],
                    ['code' => '031411027', 'name' => 'Pinagbakahan', 'cityCode' => '031411000', 'regionCode' => '030000000', 'provinceCode' => '031400000', 'subMunicipalityCode' => null],
                    ['code' => '031411028', 'name' => 'San Agustin', 'cityCode' => '031411000', 'regionCode' => '030000000', 'provinceCode' => '031400000', 'subMunicipalityCode' => null],
                    ['code' => '031411029', 'name' => 'San Gabriel', 'cityCode' => '031411000', 'regionCode' => '030000000', 'provinceCode' => '031400000', 'subMunicipalityCode' => null],
                    ['code' => '031411030', 'name' => 'San Juan', 'cityCode' => '031411000', 'regionCode' => '030000000', 'provinceCode' => '031400000', 'subMunicipalityCode' => null],
                    ['code' => '031411031', 'name' => 'San Pablo', 'cityCode' => '031411000', 'regionCode' => '030000000', 'provinceCode' => '031400000', 'subMunicipalityCode' => null],
                    ['code' => '031411032', 'name' => 'San Vicente', 'cityCode' => '031411000', 'regionCode' => '030000000', 'provinceCode' => '031400000', 'subMunicipalityCode' => null],
                    ['code' => '031411033', 'name' => 'Santiago', 'cityCode' => '031411000', 'regionCode' => '030000000', 'provinceCode' => '031400000', 'subMunicipalityCode' => null],
                    ['code' => '031411034', 'name' => 'Santisima Trinidad', 'cityCode' => '031411000', 'regionCode' => '030000000', 'provinceCode' => '031400000', 'subMunicipalityCode' => null],
                    ['code' => '031411035', 'name' => 'Santo Cristo', 'cityCode' => '031411000', 'regionCode' => '030000000', 'provinceCode' => '031400000', 'subMunicipalityCode' => null],
                    ['code' => '031411036', 'name' => 'Santo Niño', 'cityCode' => '031411000', 'regionCode' => '030000000', 'provinceCode' => '031400000', 'subMunicipalityCode' => null],
                    ['code' => '031411037', 'name' => 'Santo Rosario', 'cityCode' => '031411000', 'regionCode' => '030000000', 'provinceCode' => '031400000', 'subMunicipalityCode' => null],
                    ['code' => '031411038', 'name' => 'Santol', 'cityCode' => '031411000', 'regionCode' => '030000000', 'provinceCode' => '031400000', 'subMunicipalityCode' => null],
                    ['code' => '031411039', 'name' => 'Sumapang Bata', 'cityCode' => '031411000', 'regionCode' => '030000000', 'provinceCode' => '031400000', 'subMunicipalityCode' => null],
                    ['code' => '031411040', 'name' => 'Sumapang Matanda', 'cityCode' => '031411000', 'regionCode' => '030000000', 'provinceCode' => '031400000', 'subMunicipalityCode' => null],
                    ['code' => '031411041', 'name' => 'Taal', 'cityCode' => '031411000', 'regionCode' => '030000000', 'provinceCode' => '031400000', 'subMunicipalityCode' => null],
                    ['code' => '031411042', 'name' => 'Tikay', 'cityCode' => '031411000', 'regionCode' => '030000000', 'provinceCode' => '031400000', 'subMunicipalityCode' => null],
                    ['code' => '031411043', 'name' => 'Tugatog', 'cityCode' => '031411000', 'regionCode' => '030000000', 'provinceCode' => '031400000', 'subMunicipalityCode' => null],
                    ['code' => '031411044', 'name' => 'Ubihan', 'cityCode' => '031411000', 'regionCode' => '030000000', 'provinceCode' => '031400000', 'subMunicipalityCode' => null],
                    ['code' => '031411045', 'name' => 'Wakas', 'cityCode' => '031411000', 'regionCode' => '030000000', 'provinceCode' => '031400000', 'subMunicipalityCode' => null],
                    ['code' => '031411046', 'name' => 'Wawa', 'cityCode' => '031411000', 'regionCode' => '030000000', 'provinceCode' => '031400000', 'subMunicipalityCode' => null],
                    ['code' => '031411047', 'name' => 'Anilao', 'cityCode' => '031411000', 'regionCode' => '030000000', 'provinceCode' => '031400000', 'subMunicipalityCode' => null],
                    ['code' => '031411048', 'name' => 'Bulihan', 'cityCode' => '031411000', 'regionCode' => '030000000', 'provinceCode' => '031400000', 'subMunicipalityCode' => null],
                    ['code' => '031411049', 'name' => 'Hulo', 'cityCode' => '031411000', 'regionCode' => '030000000', 'provinceCode' => '031400000', 'subMunicipalityCode' => null],
                    ['code' => '031411050', 'name' => 'Poblacion', 'cityCode' => '031411000', 'regionCode' => '030000000', 'provinceCode' => '031400000', 'subMunicipalityCode' => null],
                    ['code' => '031411051', 'name' => 'San Miguel', 'cityCode' => '031411000', 'regionCode' => '030000000', 'provinceCode' => '031400000', 'subMunicipalityCode' => null],
                ];
            }
            elseif ($cityCode === '031413000') { // City of Meycauayan
                return [
                    ['code' => '031413001', 'name' => 'Bagbaguin', 'cityCode' => '031413000', 'regionCode' => '030000000', 'provinceCode' => '031400000', 'subMunicipalityCode' => null],
                    ['code' => '031413002', 'name' => 'Bahay Pare', 'cityCode' => '031413000', 'regionCode' => '030000000', 'provinceCode' => '031400000', 'subMunicipalityCode' => null],
                    ['code' => '031413003', 'name' => 'Bancal', 'cityCode' => '031413000', 'regionCode' => '030000000', 'provinceCode' => '031400000', 'subMunicipalityCode' => null],
                    ['code' => '031413004', 'name' => 'Bangkal', 'cityCode' => '031413000', 'regionCode' => '030000000', 'provinceCode' => '031400000', 'subMunicipalityCode' => null],
                    ['code' => '031413005', 'name' => 'Bayan', 'cityCode' => '031413000', 'regionCode' => '030000000', 'provinceCode' => '031400000', 'subMunicipalityCode' => null],
                    ['code' => '031413006', 'name' => 'Caingin', 'cityCode' => '031413000', 'regionCode' => '030000000', 'provinceCode' => '031400000', 'subMunicipalityCode' => null],
                    ['code' => '031413007', 'name' => 'Calvario', 'cityCode' => '031413000', 'regionCode' => '030000000', 'provinceCode' => '031400000', 'subMunicipalityCode' => null],
                    ['code' => '031413008', 'name' => 'Camalig', 'cityCode' => '031413000', 'regionCode' => '030000000', 'provinceCode' => '031400000', 'subMunicipalityCode' => null],
                    ['code' => '031413009', 'name' => 'Hulo', 'cityCode' => '031413000', 'regionCode' => '030000000', 'provinceCode' => '031400000', 'subMunicipalityCode' => null],
                    ['code' => '031413010', 'name' => 'Iba', 'cityCode' => '031413000', 'regionCode' => '030000000', 'provinceCode' => '031400000', 'subMunicipalityCode' => null],
                    ['code' => '031413011', 'name' => 'Langka', 'cityCode' => '031413000', 'regionCode' => '030000000', 'provinceCode' => '031400000', 'subMunicipalityCode' => null],
                    ['code' => '031413012', 'name' => 'Lawang Bato', 'cityCode' => '031413000', 'regionCode' => '030000000', 'provinceCode' => '031400000', 'subMunicipalityCode' => null],
                    ['code' => '031413013', 'name' => 'Libtong', 'cityCode' => '031413000', 'regionCode' => '030000000', 'provinceCode' => '031400000', 'subMunicipalityCode' => null],
                    ['code' => '031413014', 'name' => 'Liputan', 'cityCode' => '031413000', 'regionCode' => '030000000', 'provinceCode' => '031400000', 'subMunicipalityCode' => null],
                    ['code' => '031413015', 'name' => 'Longos', 'cityCode' => '031413000', 'regionCode' => '030000000', 'provinceCode' => '031400000', 'subMunicipalityCode' => null],
                    ['code' => '031413016', 'name' => 'Malhacan', 'cityCode' => '031413000', 'regionCode' => '030000000', 'provinceCode' => '031400000', 'subMunicipalityCode' => null],
                    ['code' => '031413017', 'name' => 'Pajo', 'cityCode' => '031413000', 'regionCode' => '030000000', 'provinceCode' => '031400000', 'subMunicipalityCode' => null],
                    ['code' => '031413018', 'name' => 'Pandayan', 'cityCode' => '031413000', 'regionCode' => '030000000', 'provinceCode' => '031400000', 'subMunicipalityCode' => null],
                    ['code' => '031413019', 'name' => 'Pantoc', 'cityCode' => '031413000', 'regionCode' => '030000000', 'provinceCode' => '031400000', 'subMunicipalityCode' => null],
                    ['code' => '031413020', 'name' => 'Perez', 'cityCode' => '031413000', 'regionCode' => '030000000', 'provinceCode' => '031400000', 'subMunicipalityCode' => null],
                    ['code' => '031413021', 'name' => 'Poblacion', 'cityCode' => '031413000', 'regionCode' => '030000000', 'provinceCode' => '031400000', 'subMunicipalityCode' => null],
                    ['code' => '031413022', 'name' => 'Saluysoy', 'cityCode' => '031413000', 'regionCode' => '030000000', 'provinceCode' => '031400000', 'subMunicipalityCode' => null],
                    ['code' => '031413023', 'name' => 'Tugatog', 'cityCode' => '031413000', 'regionCode' => '030000000', 'provinceCode' => '031400000', 'subMunicipalityCode' => null],
                    ['code' => '031413024', 'name' => 'Ubihan', 'cityCode' => '031413000', 'regionCode' => '030000000', 'provinceCode' => '031400000', 'subMunicipalityCode' => null],
                    ['code' => '031413025', 'name' => 'Zamora', 'cityCode' => '031413000', 'regionCode' => '030000000', 'provinceCode' => '031400000', 'subMunicipalityCode' => null],
                    ['code' => '031413026', 'name' => 'Sinturisan', 'cityCode' => '031413000', 'regionCode' => '030000000', 'provinceCode' => '031400000', 'subMunicipalityCode' => null],
                ];
            }
            elseif ($cityCode === '031421000') { // City of San Jose del Monte
                return [
                    ['code' => '031421001', 'name' => 'Bagong Buhay', 'cityCode' => '031421000', 'regionCode' => '030000000', 'provinceCode' => '031400000', 'subMunicipalityCode' => null],
                    ['code' => '031421002', 'name' => 'Bagong Silang', 'cityCode' => '031421000', 'regionCode' => '030000000', 'provinceCode' => '031400000', 'subMunicipalityCode' => null],
                    ['code' => '031421003', 'name' => 'Balaong', 'cityCode' => '031421000', 'regionCode' => '030000000', 'provinceCode' => '031400000', 'subMunicipalityCode' => null],
                    ['code' => '031421004', 'name' => 'Barangay I', 'cityCode' => '031421000', 'regionCode' => '030000000', 'provinceCode' => '031400000', 'subMunicipalityCode' => null],
                    ['code' => '031421005', 'name' => 'Barangay II', 'cityCode' => '031421000', 'regionCode' => '030000000', 'provinceCode' => '031400000', 'subMunicipalityCode' => null],
                    ['code' => '031421006', 'name' => 'Barangay III', 'cityCode' => '031421000', 'regionCode' => '030000000', 'provinceCode' => '031400000', 'subMunicipalityCode' => null],
                    ['code' => '031421007', 'name' => 'Barangay IV', 'cityCode' => '031421000', 'regionCode' => '030000000', 'provinceCode' => '031400000', 'subMunicipalityCode' => null],
                    ['code' => '031421008', 'name' => 'Barangay V', 'cityCode' => '031421000', 'regionCode' => '030000000', 'provinceCode' => '031400000', 'subMunicipalityCode' => null],
                    ['code' => '031421009', 'name' => 'Citrus', 'cityCode' => '031421000', 'regionCode' => '030000000', 'provinceCode' => '031400000', 'subMunicipalityCode' => null],
                    ['code' => '031421010', 'name' => 'City Heights', 'cityCode' => '031421000', 'regionCode' => '030000000', 'provinceCode' => '031400000', 'subMunicipalityCode' => null],
                    ['code' => '031421011', 'name' => 'Cruz Pobre', 'cityCode' => '031421000', 'regionCode' => '030000000', 'provinceCode' => '031400000', 'subMunicipalityCode' => null],
                    ['code' => '031421012', 'name' => 'Dulong Bayan', 'cityCode' => '031421000', 'regionCode' => '030000000', 'provinceCode' => '031400000', 'subMunicipalityCode' => null],
                    ['code' => '031421013', 'name' => 'Fatima', 'cityCode' => '031421000', 'regionCode' => '030000000', 'provinceCode' => '031400000', 'subMunicipalityCode' => null],
                    ['code' => '031421014', 'name' => 'Francisco Homes', 'cityCode' => '031421000', 'regionCode' => '030000000', 'provinceCode' => '031400000', 'subMunicipalityCode' => null],
                    ['code' => '031421015', 'name' => 'Gaya-Gaya', 'cityCode' => '031421000', 'regionCode' => '030000000', 'provinceCode' => '031400000', 'subMunicipalityCode' => null],
                    ['code' => '031421016', 'name' => 'Graceville', 'cityCode' => '031421000', 'regionCode' => '030000000', 'provinceCode' => '031400000', 'subMunicipalityCode' => null],
                    ['code' => '031421017', 'name' => 'Guadalupe', 'cityCode' => '031421000', 'regionCode' => '030000000', 'provinceCode' => '031400000', 'subMunicipalityCode' => null],
                    ['code' => '031421018', 'name' => 'Kaybanban', 'cityCode' => '031421000', 'regionCode' => '030000000', 'provinceCode' => '031400000', 'subMunicipalityCode' => null],
                    ['code' => '031421019', 'name' => 'Kaypian', 'cityCode' => '031421000', 'regionCode' => '030000000', 'provinceCode' => '031400000', 'subMunicipalityCode' => null],
                    ['code' => '031421020', 'name' => 'Krus na Ligas', 'cityCode' => '031421000', 'regionCode' => '030000000', 'provinceCode' => '031400000', 'subMunicipalityCode' => null],
                    ['code' => '031421021', 'name' => 'Maharlika', 'cityCode' => '031421000', 'regionCode' => '030000000', 'provinceCode' => '031400000', 'subMunicipalityCode' => null],
                    ['code' => '031421022', 'name' => 'Minuyan', 'cityCode' => '031421000', 'regionCode' => '030000000', 'provinceCode' => '031400000', 'subMunicipalityCode' => null],
                    ['code' => '031421023', 'name' => 'Minuyan Proper', 'cityCode' => '031421000', 'regionCode' => '030000000', 'provinceCode' => '031400000', 'subMunicipalityCode' => null],
                    ['code' => '031421024', 'name' => 'Muzon', 'cityCode' => '031421000', 'regionCode' => '030000000', 'provinceCode' => '031400000', 'subMunicipalityCode' => null],
                    ['code' => '031421025', 'name' => 'Poblacion', 'cityCode' => '031421000', 'regionCode' => '030000000', 'provinceCode' => '031400000', 'subMunicipalityCode' => null],
                    ['code' => '031421026', 'name' => 'Poblacion I', 'cityCode' => '031421000', 'regionCode' => '030000000', 'provinceCode' => '031400000', 'subMunicipalityCode' => null],
                    ['code' => '031421027', 'name' => 'Poblacion II', 'cityCode' => '031421000', 'regionCode' => '030000000', 'provinceCode' => '031400000', 'subMunicipalityCode' => null],
                    ['code' => '031421028', 'name' => 'Poblacion III', 'cityCode' => '031421000', 'regionCode' => '030000000', 'provinceCode' => '031400000', 'subMunicipalityCode' => null],
                    ['code' => '031421029', 'name' => 'Poblacion IV', 'cityCode' => '031421000', 'regionCode' => '030000000', 'provinceCode' => '031400000', 'subMunicipalityCode' => null],
                    ['code' => '031421030', 'name' => 'Poblacion V', 'cityCode' => '031421000', 'regionCode' => '030000000', 'provinceCode' => '031400000', 'subMunicipalityCode' => null],
                    ['code' => '031421031', 'name' => 'Poblacion VI', 'cityCode' => '031421000', 'regionCode' => '030000000', 'provinceCode' => '031400000', 'subMunicipalityCode' => null],
                    ['code' => '031421032', 'name' => 'Poblacion VII', 'cityCode' => '031421000', 'regionCode' => '030000000', 'provinceCode' => '031400000', 'subMunicipalityCode' => null],
                    ['code' => '031421033', 'name' => 'Poblacion VIII', 'cityCode' => '031421000', 'regionCode' => '030000000', 'provinceCode' => '031400000', 'subMunicipalityCode' => null],
                    ['code' => '031421034', 'name' => 'Poblacion IX', 'cityCode' => '031421000', 'regionCode' => '030000000', 'provinceCode' => '031400000', 'subMunicipalityCode' => null],
                    ['code' => '031421035', 'name' => 'Poblacion X', 'cityCode' => '031421000', 'regionCode' => '030000000', 'provinceCode' => '031400000', 'subMunicipalityCode' => null],
                    ['code' => '031421036', 'name' => 'Poblacion XI', 'cityCode' => '031421000', 'regionCode' => '030000000', 'provinceCode' => '031400000', 'subMunicipalityCode' => null],
                    ['code' => '031421037', 'name' => 'Poblacion XII', 'cityCode' => '031421000', 'regionCode' => '030000000', 'provinceCode' => '031400000', 'subMunicipalityCode' => null],
                    ['code' => '031421038', 'name' => 'Poblacion XIII', 'cityCode' => '031421000', 'regionCode' => '030000000', 'provinceCode' => '031400000', 'subMunicipalityCode' => null],
                    ['code' => '031421039', 'name' => 'Poblacion XIV', 'cityCode' => '031421000', 'regionCode' => '030000000', 'provinceCode' => '031400000', 'subMunicipalityCode' => null],
                    ['code' => '031421040', 'name' => 'Poblacion XV', 'cityCode' => '031421000', 'regionCode' => '030000000', 'provinceCode' => '031400000', 'subMunicipalityCode' => null],
                    ['code' => '031421041', 'name' => 'Poblacion XVI', 'cityCode' => '031421000', 'regionCode' => '030000000', 'provinceCode' => '031400000', 'subMunicipalityCode' => null],
                    ['code' => '031421042', 'name' => 'Poblacion XVII', 'cityCode' => '031421000', 'regionCode' => '030000000', 'provinceCode' => '031400000', 'subMunicipalityCode' => null],
                    ['code' => '031421043', 'name' => 'Poblacion XVIII', 'cityCode' => '031421000', 'regionCode' => '030000000', 'provinceCode' => '031400000', 'subMunicipalityCode' => null],
                    ['code' => '031421044', 'name' => 'Poblacion XIX', 'cityCode' => '031421000', 'regionCode' => '030000000', 'provinceCode' => '031400000', 'subMunicipalityCode' => null],
                    ['code' => '031421045', 'name' => 'Poblacion XX', 'cityCode' => '031421000', 'regionCode' => '030000000', 'provinceCode' => '031400000', 'subMunicipalityCode' => null],
                    ['code' => '031421046', 'name' => 'Poblacion XXI', 'cityCode' => '031421000', 'regionCode' => '030000000', 'provinceCode' => '031400000', 'subMunicipalityCode' => null],
                    ['code' => '031421047', 'name' => 'Poblacion XXII', 'cityCode' => '031421000', 'regionCode' => '030000000', 'provinceCode' => '031400000', 'subMunicipalityCode' => null],
                    ['code' => '031421048', 'name' => 'Poblacion XXIII', 'cityCode' => '031421000', 'regionCode' => '030000000', 'provinceCode' => '031400000', 'subMunicipalityCode' => null],
                    ['code' => '031421049', 'name' => 'Poblacion XXIV', 'cityCode' => '031421000', 'regionCode' => '030000000', 'provinceCode' => '031400000', 'subMunicipalityCode' => null],
                    ['code' => '031421050', 'name' => 'Poblacion XXV', 'cityCode' => '031421000', 'regionCode' => '030000000', 'provinceCode' => '031400000', 'subMunicipalityCode' => null],
                    ['code' => '031421051', 'name' => 'Poblacion XXVI', 'cityCode' => '031421000', 'regionCode' => '030000000', 'provinceCode' => '031400000', 'subMunicipalityCode' => null],
                    ['code' => '031421052', 'name' => 'Poblacion XXVII', 'cityCode' => '031421000', 'regionCode' => '030000000', 'provinceCode' => '031400000', 'subMunicipalityCode' => null],
                    ['code' => '031421053', 'name' => 'Poblacion XXVIII', 'cityCode' => '031421000', 'regionCode' => '030000000', 'provinceCode' => '031400000', 'subMunicipalityCode' => null],
                    ['code' => '031421054', 'name' => 'Poblacion XXIX', 'cityCode' => '031421000', 'regionCode' => '030000000', 'provinceCode' => '031400000', 'subMunicipalityCode' => null],
                    ['code' => '031421055', 'name' => 'Poblacion XXX', 'cityCode' => '031421000', 'regionCode' => '030000000', 'provinceCode' => '031400000', 'subMunicipalityCode' => null],
                    ['code' => '031421056', 'name' => 'Poblacion XXXI', 'cityCode' => '031421000', 'regionCode' => '030000000', 'provinceCode' => '031400000', 'subMunicipalityCode' => null],
                    ['code' => '031421057', 'name' => 'Poblacion XXXII', 'cityCode' => '031421000', 'regionCode' => '030000000', 'provinceCode' => '031400000', 'subMunicipalityCode' => null],
                    ['code' => '031421058', 'name' => 'Poblacion XXXIII', 'cityCode' => '031421000', 'regionCode' => '030000000', 'provinceCode' => '031400000', 'subMunicipalityCode' => null],
                    ['code' => '031421059', 'name' => 'Poblacion XXXIV', 'cityCode' => '031421000', 'regionCode' => '030000000', 'provinceCode' => '031400000', 'subMunicipalityCode' => null],
                ];
            }
            elseif ($cityCode === '031401000') { // Municipality of Angat
                return [
                    ['code' => '031401001', 'name' => 'Banaban', 'cityCode' => '031401000', 'regionCode' => '030000000', 'provinceCode' => '031400000', 'subMunicipalityCode' => null],
                    ['code' => '031401002', 'name' => 'Baybay', 'cityCode' => '031401000', 'regionCode' => '030000000', 'provinceCode' => '031400000', 'subMunicipalityCode' => null],
                    ['code' => '031401003', 'name' => 'Binagbag', 'cityCode' => '031401000', 'regionCode' => '030000000', 'provinceCode' => '031400000', 'subMunicipalityCode' => null],
                    ['code' => '031401004', 'name' => 'Donacion', 'cityCode' => '031401000', 'regionCode' => '030000000', 'provinceCode' => '031400000', 'subMunicipalityCode' => null],
                    ['code' => '031401005', 'name' => 'Encanto', 'cityCode' => '031401000', 'regionCode' => '030000000', 'provinceCode' => '031400000', 'subMunicipalityCode' => null],
                    ['code' => '031401006', 'name' => 'Laog', 'cityCode' => '031401000', 'regionCode' => '030000000', 'provinceCode' => '031400000', 'subMunicipalityCode' => null],
                    ['code' => '031401007', 'name' => 'Marungko', 'cityCode' => '031401000', 'regionCode' => '030000000', 'provinceCode' => '031400000', 'subMunicipalityCode' => null],
                    ['code' => '031401008', 'name' => 'Niugan', 'cityCode' => '031401000', 'regionCode' => '030000000', 'provinceCode' => '031400000', 'subMunicipalityCode' => null],
                    ['code' => '031401009', 'name' => 'Paltoc', 'cityCode' => '031401000', 'regionCode' => '030000000', 'provinceCode' => '031400000', 'subMunicipalityCode' => null],
                    ['code' => '031401010', 'name' => 'Pulong Yantok', 'cityCode' => '031401000', 'regionCode' => '030000000', 'provinceCode' => '031400000', 'subMunicipalityCode' => null],
                    ['code' => '031401011', 'name' => 'San Roque', 'cityCode' => '031401000', 'regionCode' => '030000000', 'provinceCode' => '031400000', 'subMunicipalityCode' => null],
                    ['code' => '031401012', 'name' => 'Santa Cruz', 'cityCode' => '031401000', 'regionCode' => '030000000', 'provinceCode' => '031400000', 'subMunicipalityCode' => null],
                    ['code' => '031401013', 'name' => 'Santa Lucia', 'cityCode' => '031401000', 'regionCode' => '030000000', 'provinceCode' => '031400000', 'subMunicipalityCode' => null],
                    ['code' => '031401014', 'name' => 'Sulucan', 'cityCode' => '031401000', 'regionCode' => '030000000', 'provinceCode' => '031400000', 'subMunicipalityCode' => null],
                    ['code' => '031401015', 'name' => 'Taboc', 'cityCode' => '031401000', 'regionCode' => '030000000', 'provinceCode' => '031400000', 'subMunicipalityCode' => null],
                    ['code' => '031401016', 'name' => 'Tulay na Bato', 'cityCode' => '031401000', 'regionCode' => '030000000', 'provinceCode' => '031400000', 'subMunicipalityCode' => null],
                ];
            }
            elseif ($cityCode === '031402000') { // Municipality of Balagtas
                return [
                    ['code' => '031402001', 'name' => 'Borol 1st', 'cityCode' => '031402000', 'regionCode' => '030000000', 'provinceCode' => '031400000', 'subMunicipalityCode' => null],
                    ['code' => '031402002', 'name' => 'Borol 2nd', 'cityCode' => '031402000', 'regionCode' => '030000000', 'provinceCode' => '031400000', 'subMunicipalityCode' => null],
                    ['code' => '031402003', 'name' => 'Dalig', 'cityCode' => '031402000', 'regionCode' => '030000000', 'provinceCode' => '031400000', 'subMunicipalityCode' => null],
                    ['code' => '031402004', 'name' => 'Longos', 'cityCode' => '031402000', 'regionCode' => '030000000', 'provinceCode' => '031400000', 'subMunicipalityCode' => null],
                    ['code' => '031402005', 'name' => 'Panginay', 'cityCode' => '031402000', 'regionCode' => '030000000', 'provinceCode' => '031400000', 'subMunicipalityCode' => null],
                    ['code' => '031402006', 'name' => 'Poblacion', 'cityCode' => '031402000', 'regionCode' => '030000000', 'provinceCode' => '031400000', 'subMunicipalityCode' => null],
                    ['code' => '031402007', 'name' => 'Pulong Gubat', 'cityCode' => '031402000', 'regionCode' => '030000000', 'provinceCode' => '031400000', 'subMunicipalityCode' => null],
                    ['code' => '031402008', 'name' => 'San Juan', 'cityCode' => '031402000', 'regionCode' => '030000000', 'provinceCode' => '031400000', 'subMunicipalityCode' => null],
                    ['code' => '031402009', 'name' => 'Wawa', 'cityCode' => '031402000', 'regionCode' => '030000000', 'provinceCode' => '031400000', 'subMunicipalityCode' => null],
                ];
            }
            elseif ($cityCode === '031404000') { // Municipality of Bocaue
                return [
                    ['code' => '031404001', 'name' => 'Antipona', 'cityCode' => '031404000', 'regionCode' => '030000000', 'provinceCode' => '031400000', 'subMunicipalityCode' => null],
                    ['code' => '031404002', 'name' => 'Bagumbayan', 'cityCode' => '031404000', 'regionCode' => '030000000', 'provinceCode' => '031400000', 'subMunicipalityCode' => null],
                    ['code' => '031404003', 'name' => 'Bambang', 'cityCode' => '031404000', 'regionCode' => '030000000', 'provinceCode' => '031400000', 'subMunicipalityCode' => null],
                    ['code' => '031404004', 'name' => 'Batia', 'cityCode' => '031404000', 'regionCode' => '030000000', 'provinceCode' => '031400000', 'subMunicipalityCode' => null],
                    ['code' => '031404005', 'name' => 'Biñang 1st', 'cityCode' => '031404000', 'regionCode' => '030000000', 'provinceCode' => '031400000', 'subMunicipalityCode' => null],
                    ['code' => '031404006', 'name' => 'Biñang 2nd', 'cityCode' => '031404000', 'regionCode' => '030000000', 'provinceCode' => '031400000', 'subMunicipalityCode' => null],
                    ['code' => '031404007', 'name' => 'Bolacan', 'cityCode' => '031404000', 'regionCode' => '030000000', 'provinceCode' => '031400000', 'subMunicipalityCode' => null],
                    ['code' => '031404008', 'name' => 'Bundukan', 'cityCode' => '031404000', 'regionCode' => '030000000', 'provinceCode' => '031400000', 'subMunicipalityCode' => null],
                    ['code' => '031404009', 'name' => 'Bunlo', 'cityCode' => '031404000', 'regionCode' => '030000000', 'provinceCode' => '031400000', 'subMunicipalityCode' => null],
                    ['code' => '031404010', 'name' => 'Caingin', 'cityCode' => '031404000', 'regionCode' => '030000000', 'provinceCode' => '031400000', 'subMunicipalityCode' => null],
                    ['code' => '031404011', 'name' => 'Duhat', 'cityCode' => '031404000', 'regionCode' => '030000000', 'provinceCode' => '031400000', 'subMunicipalityCode' => null],
                    ['code' => '031404012', 'name' => 'Igulot', 'cityCode' => '031404000', 'regionCode' => '030000000', 'provinceCode' => '031400000', 'subMunicipalityCode' => null],
                    ['code' => '031404013', 'name' => 'Lolomboy', 'cityCode' => '031404000', 'regionCode' => '030000000', 'provinceCode' => '031400000', 'subMunicipalityCode' => null],
                    ['code' => '031404014', 'name' => 'Poblacion', 'cityCode' => '031404000', 'regionCode' => '030000000', 'provinceCode' => '031400000', 'subMunicipalityCode' => null],
                    ['code' => '031404015', 'name' => 'Rizal', 'cityCode' => '031404000', 'regionCode' => '030000000', 'provinceCode' => '031400000', 'subMunicipalityCode' => null],
                    ['code' => '031404016', 'name' => 'Sulucan', 'cityCode' => '031404000', 'regionCode' => '030000000', 'provinceCode' => '031400000', 'subMunicipalityCode' => null],
                    ['code' => '031404017', 'name' => 'Taal', 'cityCode' => '031404000', 'regionCode' => '030000000', 'provinceCode' => '031400000', 'subMunicipalityCode' => null],
                    ['code' => '031404018', 'name' => 'Tubo', 'cityCode' => '031404000', 'regionCode' => '030000000', 'provinceCode' => '031400000', 'subMunicipalityCode' => null],
                    ['code' => '031404019', 'name' => 'Wakas', 'cityCode' => '031404000', 'regionCode' => '030000000', 'provinceCode' => '031400000', 'subMunicipalityCode' => null],
                ];
            }
            elseif ($cityCode === '031405000') { // Municipality of Bulakan
                return [
                    ['code' => '031405001', 'name' => 'Bagumbayan', 'cityCode' => '031405000', 'regionCode' => '030000000', 'provinceCode' => '031400000', 'subMunicipalityCode' => null],
                    ['code' => '031405002', 'name' => 'Balubad', 'cityCode' => '031405000', 'regionCode' => '030000000', 'provinceCode' => '031400000', 'subMunicipalityCode' => null],
                    ['code' => '031405003', 'name' => 'Bambang', 'cityCode' => '031405000', 'regionCode' => '030000000', 'provinceCode' => '031400000', 'subMunicipalityCode' => null],
                    ['code' => '031405004', 'name' => 'Matungao', 'cityCode' => '031405000', 'regionCode' => '030000000', 'provinceCode' => '031400000', 'subMunicipalityCode' => null],
                    ['code' => '031405005', 'name' => 'Maysantol', 'cityCode' => '031405000', 'regionCode' => '030000000', 'provinceCode' => '031400000', 'subMunicipalityCode' => null],
                    ['code' => '031405006', 'name' => 'Perez', 'cityCode' => '031405000', 'regionCode' => '030000000', 'provinceCode' => '031400000', 'subMunicipalityCode' => null],
                    ['code' => '031405007', 'name' => 'Pitpitan', 'cityCode' => '031405000', 'regionCode' => '030000000', 'provinceCode' => '031400000', 'subMunicipalityCode' => null],
                    ['code' => '031405008', 'name' => 'San Francisco', 'cityCode' => '031405000', 'regionCode' => '030000000', 'provinceCode' => '031400000', 'subMunicipalityCode' => null],
                    ['code' => '031405009', 'name' => 'San Jose (Pob.)', 'cityCode' => '031405000', 'regionCode' => '030000000', 'provinceCode' => '031400000', 'subMunicipalityCode' => null],
                    ['code' => '031405010', 'name' => 'San Nicolas', 'cityCode' => '031405000', 'regionCode' => '030000000', 'provinceCode' => '031400000', 'subMunicipalityCode' => null],
                    ['code' => '031405011', 'name' => 'Santa Ana', 'cityCode' => '031405000', 'regionCode' => '030000000', 'provinceCode' => '031400000', 'subMunicipalityCode' => null],
                    ['code' => '031405012', 'name' => 'Santa Ines', 'cityCode' => '031405000', 'regionCode' => '030000000', 'provinceCode' => '031400000', 'subMunicipalityCode' => null],
                    ['code' => '031405013', 'name' => 'Taliptip', 'cityCode' => '031405000', 'regionCode' => '030000000', 'provinceCode' => '031400000', 'subMunicipalityCode' => null],
                    ['code' => '031405014', 'name' => 'Tibig', 'cityCode' => '031405000', 'regionCode' => '030000000', 'provinceCode' => '031400000', 'subMunicipalityCode' => null],
                ];
            }
            elseif ($cityCode === '031406000') { // Municipality of Bustos
                return [
                    ['code' => '031406001', 'name' => 'Bonga Mayor', 'cityCode' => '031406000', 'regionCode' => '030000000', 'provinceCode' => '031400000', 'subMunicipalityCode' => null],
                    ['code' => '031406002', 'name' => 'Bonga Menor', 'cityCode' => '031406000', 'regionCode' => '030000000', 'provinceCode' => '031400000', 'subMunicipalityCode' => null],
                    ['code' => '031406003', 'name' => 'Buisan', 'cityCode' => '031406000', 'regionCode' => '030000000', 'provinceCode' => '031400000', 'subMunicipalityCode' => null],
                    ['code' => '031406004', 'name' => 'Camachilihan', 'cityCode' => '031406000', 'regionCode' => '030000000', 'provinceCode' => '031400000', 'subMunicipalityCode' => null],
                    ['code' => '031406005', 'name' => 'Cambaog', 'cityCode' => '031406000', 'regionCode' => '030000000', 'provinceCode' => '031400000', 'subMunicipalityCode' => null],
                    ['code' => '031406006', 'name' => 'Catacte', 'cityCode' => '031406000', 'regionCode' => '030000000', 'provinceCode' => '031400000', 'subMunicipalityCode' => null],
                    ['code' => '031406007', 'name' => 'Liciada', 'cityCode' => '031406000', 'regionCode' => '030000000', 'provinceCode' => '031400000', 'subMunicipalityCode' => null],
                    ['code' => '031406008', 'name' => 'Malawak', 'cityCode' => '031406000', 'regionCode' => '030000000', 'provinceCode' => '031400000', 'subMunicipalityCode' => null],
                    ['code' => '031406009', 'name' => 'Mapulang Lupa', 'cityCode' => '031406000', 'regionCode' => '030000000', 'provinceCode' => '031400000', 'subMunicipalityCode' => null],
                    ['code' => '031406010', 'name' => 'Poblacion', 'cityCode' => '031406000', 'regionCode' => '030000000', 'provinceCode' => '031400000', 'subMunicipalityCode' => null],
                    ['code' => '031406011', 'name' => 'San Pedro', 'cityCode' => '031406000', 'regionCode' => '030000000', 'provinceCode' => '031400000', 'subMunicipalityCode' => null],
                    ['code' => '031406012', 'name' => 'Talampas', 'cityCode' => '031406000', 'regionCode' => '030000000', 'provinceCode' => '031400000', 'subMunicipalityCode' => null],
                    ['code' => '031406013', 'name' => 'Tanawan', 'cityCode' => '031406000', 'regionCode' => '030000000', 'provinceCode' => '031400000', 'subMunicipalityCode' => null],
                    ['code' => '031406014', 'name' => 'Tibagan', 'cityCode' => '031406000', 'regionCode' => '030000000', 'provinceCode' => '031400000', 'subMunicipalityCode' => null],
                ];
            }
            elseif ($cityCode === '031407000') { // Municipality of Calumpit
                return [
                    ['code' => '031407001', 'name' => 'Balite', 'cityCode' => '031407000', 'regionCode' => '030000000', 'provinceCode' => '031400000', 'subMunicipalityCode' => null],
                    ['code' => '031407002', 'name' => 'Balungao', 'cityCode' => '031407000', 'regionCode' => '030000000', 'provinceCode' => '031400000', 'subMunicipalityCode' => null],
                    ['code' => '031407003', 'name' => 'Buguion', 'cityCode' => '031407000', 'regionCode' => '030000000', 'provinceCode' => '031400000', 'subMunicipalityCode' => null],
                    ['code' => '031407004', 'name' => 'Bulusan', 'cityCode' => '031407000', 'regionCode' => '030000000', 'provinceCode' => '031400000', 'subMunicipalityCode' => null],
                    ['code' => '031407005', 'name' => 'Calizon', 'cityCode' => '031407000', 'regionCode' => '030000000', 'provinceCode' => '031400000', 'subMunicipalityCode' => null],
                    ['code' => '031407006', 'name' => 'Calumpang', 'cityCode' => '031407000', 'regionCode' => '030000000', 'provinceCode' => '031400000', 'subMunicipalityCode' => null],
                    ['code' => '031407007', 'name' => 'Caniogan', 'cityCode' => '031407000', 'regionCode' => '030000000', 'provinceCode' => '031400000', 'subMunicipalityCode' => null],
                    ['code' => '031407008', 'name' => 'Corazon', 'cityCode' => '031407000', 'regionCode' => '030000000', 'provinceCode' => '031400000', 'subMunicipalityCode' => null],
                    ['code' => '031407009', 'name' => 'Frances', 'cityCode' => '031407000', 'regionCode' => '030000000', 'provinceCode' => '031400000', 'subMunicipalityCode' => null],
                    ['code' => '031407010', 'name' => 'Gatbuca', 'cityCode' => '031407000', 'regionCode' => '030000000', 'provinceCode' => '031400000', 'subMunicipalityCode' => null],
                    ['code' => '031407011', 'name' => 'Gugo', 'cityCode' => '031407000', 'regionCode' => '030000000', 'provinceCode' => '031400000', 'subMunicipalityCode' => null],
                    ['code' => '031407012', 'name' => 'Iba Este', 'cityCode' => '031407000', 'regionCode' => '030000000', 'provinceCode' => '031400000', 'subMunicipalityCode' => null],
                    ['code' => '031407013', 'name' => 'Iba Oeste', 'cityCode' => '031407000', 'regionCode' => '030000000', 'provinceCode' => '031400000', 'subMunicipalityCode' => null],
                    ['code' => '031407014', 'name' => 'Longos', 'cityCode' => '031407000', 'regionCode' => '030000000', 'provinceCode' => '031400000', 'subMunicipalityCode' => null],
                    ['code' => '031407015', 'name' => 'Meysulao', 'cityCode' => '031407000', 'regionCode' => '030000000', 'provinceCode' => '031400000', 'subMunicipalityCode' => null],
                    ['code' => '031407016', 'name' => 'Meyto', 'cityCode' => '031407000', 'regionCode' => '030000000', 'provinceCode' => '031400000', 'subMunicipalityCode' => null],
                    ['code' => '031407017', 'name' => 'Palimbang', 'cityCode' => '031407000', 'regionCode' => '030000000', 'provinceCode' => '031400000', 'subMunicipalityCode' => null],
                    ['code' => '031407018', 'name' => 'Panducot', 'cityCode' => '031407000', 'regionCode' => '030000000', 'provinceCode' => '031400000', 'subMunicipalityCode' => null],
                    ['code' => '031407019', 'name' => 'Pio Cruzcosa', 'cityCode' => '031407000', 'regionCode' => '030000000', 'provinceCode' => '031400000', 'subMunicipalityCode' => null],
                    ['code' => '031407020', 'name' => 'Poblacion', 'cityCode' => '031407000', 'regionCode' => '030000000', 'provinceCode' => '031400000', 'subMunicipalityCode' => null],
                    ['code' => '031407021', 'name' => 'Pungo', 'cityCode' => '031407000', 'regionCode' => '030000000', 'provinceCode' => '031400000', 'subMunicipalityCode' => null],
                    ['code' => '031407022', 'name' => 'San Jose', 'cityCode' => '031407000', 'regionCode' => '030000000', 'provinceCode' => '031400000', 'subMunicipalityCode' => null],
                    ['code' => '031407023', 'name' => 'San Marcos', 'cityCode' => '031407000', 'regionCode' => '030000000', 'provinceCode' => '031400000', 'subMunicipalityCode' => null],
                    ['code' => '031407024', 'name' => 'San Miguel', 'cityCode' => '031407000', 'regionCode' => '030000000', 'provinceCode' => '031400000', 'subMunicipalityCode' => null],
                    ['code' => '031407025', 'name' => 'Santiago', 'cityCode' => '031407000', 'regionCode' => '030000000', 'provinceCode' => '031400000', 'subMunicipalityCode' => null],
                    ['code' => '031407026', 'name' => 'Sapang Bayan', 'cityCode' => '031407000', 'regionCode' => '030000000', 'provinceCode' => '031400000', 'subMunicipalityCode' => null],
                    ['code' => '031407027', 'name' => 'Sucol', 'cityCode' => '031407000', 'regionCode' => '030000000', 'provinceCode' => '031400000', 'subMunicipalityCode' => null],
                    ['code' => '031407028', 'name' => 'Sulong', 'cityCode' => '031407000', 'regionCode' => '030000000', 'provinceCode' => '031400000', 'subMunicipalityCode' => null],
                    ['code' => '031407029', 'name' => 'Tañong', 'cityCode' => '031407000', 'regionCode' => '030000000', 'provinceCode' => '031400000', 'subMunicipalityCode' => null],
                ];
            }
            elseif ($cityCode === '031408000') { // Municipality of Dona Remedios Trinidad
                return [
                    ['code' => '031408001', 'name' => 'Bayabas', 'cityCode' => '031408000', 'regionCode' => '030000000', 'provinceCode' => '031400000', 'subMunicipalityCode' => null],
                    ['code' => '031408002', 'name' => 'Kabayunan', 'cityCode' => '031408000', 'regionCode' => '030000000', 'provinceCode' => '031400000', 'subMunicipalityCode' => null],
                    ['code' => '031408003', 'name' => 'Kalawakan', 'cityCode' => '031408000', 'regionCode' => '030000000', 'provinceCode' => '031400000', 'subMunicipalityCode' => null],
                    ['code' => '031408004', 'name' => 'Pulong Sampaloc', 'cityCode' => '031408000', 'regionCode' => '030000000', 'provinceCode' => '031400000', 'subMunicipalityCode' => null],
                    ['code' => '031408005', 'name' => 'Sapang Bulak', 'cityCode' => '031408000', 'regionCode' => '030000000', 'provinceCode' => '031400000', 'subMunicipalityCode' => null],
                    ['code' => '031408006', 'name' => 'Talbak', 'cityCode' => '031408000', 'regionCode' => '030000000', 'provinceCode' => '031400000', 'subMunicipalityCode' => null],
                    ['code' => '031408007', 'name' => 'Tukod', 'cityCode' => '031408000', 'regionCode' => '030000000', 'provinceCode' => '031400000', 'subMunicipalityCode' => null],
                    ['code' => '031408008', 'name' => 'Ulingao', 'cityCode' => '031408000', 'regionCode' => '030000000', 'provinceCode' => '031400000', 'subMunicipalityCode' => null],
                ];
            }
            elseif ($cityCode === '031409000') { // Municipality of Guiguinto
                return [
                    ['code' => '031409001', 'name' => 'Cutcut', 'cityCode' => '031409000', 'regionCode' => '030000000', 'provinceCode' => '031400000', 'subMunicipalityCode' => null],
                    ['code' => '031409002', 'name' => 'Daungan', 'cityCode' => '031409000', 'regionCode' => '030000000', 'provinceCode' => '031400000', 'subMunicipalityCode' => null],
                    ['code' => '031409003', 'name' => 'Ilang-Ilang', 'cityCode' => '031409000', 'regionCode' => '030000000', 'provinceCode' => '031400000', 'subMunicipalityCode' => null],
                    ['code' => '031409004', 'name' => 'Malhacan', 'cityCode' => '031409000', 'regionCode' => '030000000', 'provinceCode' => '031400000', 'subMunicipalityCode' => null],
                    ['code' => '031409005', 'name' => 'Poblacion', 'cityCode' => '031409000', 'regionCode' => '030000000', 'provinceCode' => '031400000', 'subMunicipalityCode' => null],
                    ['code' => '031409006', 'name' => 'Pritil', 'cityCode' => '031409000', 'regionCode' => '030000000', 'provinceCode' => '031400000', 'subMunicipalityCode' => null],
                    ['code' => '031409007', 'name' => 'Pulong Gubat', 'cityCode' => '031409000', 'regionCode' => '030000000', 'provinceCode' => '031400000', 'subMunicipalityCode' => null],
                    ['code' => '031409008', 'name' => 'San Agustin', 'cityCode' => '031409000', 'regionCode' => '030000000', 'provinceCode' => '031400000', 'subMunicipalityCode' => null],
                    ['code' => '031409009', 'name' => 'San Antonio', 'cityCode' => '031409000', 'regionCode' => '030000000', 'provinceCode' => '031400000', 'subMunicipalityCode' => null],
                    ['code' => '031409010', 'name' => 'San Isidro', 'cityCode' => '031409000', 'regionCode' => '030000000', 'provinceCode' => '031400000', 'subMunicipalityCode' => null],
                    ['code' => '031409011', 'name' => 'San Jose', 'cityCode' => '031409000', 'regionCode' => '030000000', 'provinceCode' => '031400000', 'subMunicipalityCode' => null],
                    ['code' => '031409012', 'name' => 'San Juan', 'cityCode' => '031409000', 'regionCode' => '030000000', 'provinceCode' => '031400000', 'subMunicipalityCode' => null],
                    ['code' => '031409013', 'name' => 'San Miguel', 'cityCode' => '031409000', 'regionCode' => '030000000', 'provinceCode' => '031400000', 'subMunicipalityCode' => null],
                    ['code' => '031409014', 'name' => 'San Rafael', 'cityCode' => '031409000', 'regionCode' => '030000000', 'provinceCode' => '031400000', 'subMunicipalityCode' => null],
                ];
            }
            elseif ($cityCode === '031410000') { // Municipality of Hagonoy
                return [
                    ['code' => '031410001', 'name' => 'Abulalas', 'cityCode' => '031410000', 'regionCode' => '030000000', 'provinceCode' => '031400000', 'subMunicipalityCode' => null],
                    ['code' => '031410002', 'name' => 'Carillo', 'cityCode' => '031410000', 'regionCode' => '030000000', 'provinceCode' => '031400000', 'subMunicipalityCode' => null],
                    ['code' => '031410003', 'name' => 'Iba', 'cityCode' => '031410000', 'regionCode' => '030000000', 'provinceCode' => '031400000', 'subMunicipalityCode' => null],
                    ['code' => '031410004', 'name' => 'Mercado', 'cityCode' => '031410000', 'regionCode' => '030000000', 'provinceCode' => '031400000', 'subMunicipalityCode' => null],
                    ['code' => '031410005', 'name' => 'Palapat', 'cityCode' => '031410000', 'regionCode' => '030000000', 'provinceCode' => '031400000', 'subMunicipalityCode' => null],
                    ['code' => '031410006', 'name' => 'Poblacion', 'cityCode' => '031410000', 'regionCode' => '030000000', 'provinceCode' => '031400000', 'subMunicipalityCode' => null],
                    ['code' => '031410007', 'name' => 'San Agustin', 'cityCode' => '031410000', 'regionCode' => '030000000', 'provinceCode' => '031400000', 'subMunicipalityCode' => null],
                    ['code' => '031410008', 'name' => 'San Isidro', 'cityCode' => '031410000', 'regionCode' => '030000000', 'provinceCode' => '031400000', 'subMunicipalityCode' => null],
                    ['code' => '031410009', 'name' => 'San Jose', 'cityCode' => '031410000', 'regionCode' => '030000000', 'provinceCode' => '031400000', 'subMunicipalityCode' => null],
                    ['code' => '031410010', 'name' => 'San Juan', 'cityCode' => '031410000', 'regionCode' => '030000000', 'provinceCode' => '031400000', 'subMunicipalityCode' => null],
                    ['code' => '031410011', 'name' => 'San Miguel', 'cityCode' => '031410000', 'regionCode' => '030000000', 'provinceCode' => '031400000', 'subMunicipalityCode' => null],
                    ['code' => '031410012', 'name' => 'San Nicolas', 'cityCode' => '031410000', 'regionCode' => '030000000', 'provinceCode' => '031400000', 'subMunicipalityCode' => null],
                    ['code' => '031410013', 'name' => 'San Pablo', 'cityCode' => '031410000', 'regionCode' => '030000000', 'provinceCode' => '031400000', 'subMunicipalityCode' => null],
                    ['code' => '031410014', 'name' => 'San Pedro', 'cityCode' => '031410000', 'regionCode' => '030000000', 'provinceCode' => '031400000', 'subMunicipalityCode' => null],
                    ['code' => '031410015', 'name' => 'San Roque', 'cityCode' => '031410000', 'regionCode' => '030000000', 'provinceCode' => '031400000', 'subMunicipalityCode' => null],
                    ['code' => '031410016', 'name' => 'San Sebastian', 'cityCode' => '031410000', 'regionCode' => '030000000', 'provinceCode' => '031400000', 'subMunicipalityCode' => null],
                    ['code' => '031410017', 'name' => 'Santa Cruz', 'cityCode' => '031410000', 'regionCode' => '030000000', 'provinceCode' => '031400000', 'subMunicipalityCode' => null],
                    ['code' => '031410018', 'name' => 'Santa Elena', 'cityCode' => '031410000', 'regionCode' => '030000000', 'provinceCode' => '031400000', 'subMunicipalityCode' => null],
                    ['code' => '031410019', 'name' => 'Santa Monica', 'cityCode' => '031410000', 'regionCode' => '030000000', 'provinceCode' => '031400000', 'subMunicipalityCode' => null],
                    ['code' => '031410020', 'name' => 'Santo Niño', 'cityCode' => '031410000', 'regionCode' => '030000000', 'provinceCode' => '031400000', 'subMunicipalityCode' => null],
                    ['code' => '031410021', 'name' => 'Santo Rosario', 'cityCode' => '031410000', 'regionCode' => '030000000', 'provinceCode' => '031400000', 'subMunicipalityCode' => null],
                    ['code' => '031410022', 'name' => 'Tampok', 'cityCode' => '031410000', 'regionCode' => '030000000', 'provinceCode' => '031400000', 'subMunicipalityCode' => null],
                    ['code' => '031410023', 'name' => 'Tibaguin', 'cityCode' => '031410000', 'regionCode' => '030000000', 'provinceCode' => '031400000', 'subMunicipalityCode' => null],
                    ['code' => '031410024', 'name' => 'Tukuran', 'cityCode' => '031410000', 'regionCode' => '030000000', 'provinceCode' => '031400000', 'subMunicipalityCode' => null],
                    ['code' => '031410025', 'name' => 'Ubihan', 'cityCode' => '031410000', 'regionCode' => '030000000', 'provinceCode' => '031400000', 'subMunicipalityCode' => null],
                    ['code' => '031410026', 'name' => 'Wakas', 'cityCode' => '031410000', 'regionCode' => '030000000', 'provinceCode' => '031400000', 'subMunicipalityCode' => null],
                ];
            }
            elseif ($cityCode === '031412000') { // Municipality of Marilao
                return [
                    ['code' => '031412001', 'name' => 'Abangan Norte', 'cityCode' => '031412000', 'regionCode' => '030000000', 'provinceCode' => '031400000', 'subMunicipalityCode' => null],
                    ['code' => '031412002', 'name' => 'Abangan Sur', 'cityCode' => '031412000', 'regionCode' => '030000000', 'provinceCode' => '031400000', 'subMunicipalityCode' => null],
                    ['code' => '031412003', 'name' => 'Ibayo', 'cityCode' => '031412000', 'regionCode' => '030000000', 'provinceCode' => '031400000', 'subMunicipalityCode' => null],
                    ['code' => '031412004', 'name' => 'Lambakin', 'cityCode' => '031412000', 'regionCode' => '030000000', 'provinceCode' => '031400000', 'subMunicipalityCode' => null],
                    ['code' => '031412005', 'name' => 'Lias', 'cityCode' => '031412000', 'regionCode' => '030000000', 'provinceCode' => '031400000', 'subMunicipalityCode' => null],
                    ['code' => '031412006', 'name' => 'Nagbalon', 'cityCode' => '031412000', 'regionCode' => '030000000', 'provinceCode' => '031400000', 'subMunicipalityCode' => null],
                    ['code' => '031412007', 'name' => 'Patubig', 'cityCode' => '031412000', 'regionCode' => '030000000', 'provinceCode' => '031400000', 'subMunicipalityCode' => null],
                    ['code' => '031412008', 'name' => 'Poblacion I', 'cityCode' => '031412000', 'regionCode' => '030000000', 'provinceCode' => '031400000', 'subMunicipalityCode' => null],
                    ['code' => '031412009', 'name' => 'Poblacion II', 'cityCode' => '031412000', 'regionCode' => '030000000', 'provinceCode' => '031400000', 'subMunicipalityCode' => null],
                    ['code' => '031412010', 'name' => 'Prenza I', 'cityCode' => '031412000', 'regionCode' => '030000000', 'provinceCode' => '031400000', 'subMunicipalityCode' => null],
                    ['code' => '031412011', 'name' => 'Prenza II', 'cityCode' => '031412000', 'regionCode' => '030000000', 'provinceCode' => '031400000', 'subMunicipalityCode' => null],
                    ['code' => '031412012', 'name' => 'Saog', 'cityCode' => '031412000', 'regionCode' => '030000000', 'provinceCode' => '031400000', 'subMunicipalityCode' => null],
                    ['code' => '031412013', 'name' => 'Tabing-Ilog', 'cityCode' => '031412000', 'regionCode' => '030000000', 'provinceCode' => '031400000', 'subMunicipalityCode' => null],
                    ['code' => '031412014', 'name' => 'Tumana', 'cityCode' => '031412000', 'regionCode' => '030000000', 'provinceCode' => '031400000', 'subMunicipalityCode' => null],
                    ['code' => '031412015', 'name' => 'Ulingao', 'cityCode' => '031412000', 'regionCode' => '030000000', 'provinceCode' => '031400000', 'subMunicipalityCode' => null],
                    ['code' => '031412016', 'name' => 'Zamora', 'cityCode' => '031412000', 'regionCode' => '030000000', 'provinceCode' => '031400000', 'subMunicipalityCode' => null],
                ];
            }
            elseif ($cityCode === '031414000') { // Municipality of Norzagaray
                return [
                    ['code' => '031414001', 'name' => 'Bigte', 'cityCode' => '031414000', 'regionCode' => '030000000', 'provinceCode' => '031400000', 'subMunicipalityCode' => null],
                    ['code' => '031414002', 'name' => 'Bitungol', 'cityCode' => '031414000', 'regionCode' => '030000000', 'provinceCode' => '031400000', 'subMunicipalityCode' => null],
                    ['code' => '031414003', 'name' => 'Calawis', 'cityCode' => '031414000', 'regionCode' => '030000000', 'provinceCode' => '031400000', 'subMunicipalityCode' => null],
                    ['code' => '031414004', 'name' => 'Casile', 'cityCode' => '031414000', 'regionCode' => '030000000', 'provinceCode' => '031400000', 'subMunicipalityCode' => null],
                    ['code' => '031414005', 'name' => 'La Mesa', 'cityCode' => '031414000', 'regionCode' => '030000000', 'provinceCode' => '031400000', 'subMunicipalityCode' => null],
                    ['code' => '031414006', 'name' => 'Matictic', 'cityCode' => '031414000', 'regionCode' => '030000000', 'provinceCode' => '031400000', 'subMunicipalityCode' => null],
                    ['code' => '031414007', 'name' => 'Minuyan', 'cityCode' => '031414000', 'regionCode' => '030000000', 'provinceCode' => '031400000', 'subMunicipalityCode' => null],
                    ['code' => '031414008', 'name' => 'Pantal', 'cityCode' => '031414000', 'regionCode' => '030000000', 'provinceCode' => '031400000', 'subMunicipalityCode' => null],
                    ['code' => '031414009', 'name' => 'Poblacion', 'cityCode' => '031414000', 'regionCode' => '030000000', 'provinceCode' => '031400000', 'subMunicipalityCode' => null],
                    ['code' => '031414010', 'name' => 'Pulo', 'cityCode' => '031414000', 'regionCode' => '030000000', 'provinceCode' => '031400000', 'subMunicipalityCode' => null],
                    ['code' => '031414011', 'name' => 'San Lorenzo', 'cityCode' => '031414000', 'regionCode' => '030000000', 'provinceCode' => '031400000', 'subMunicipalityCode' => null],
                    ['code' => '031414012', 'name' => 'San Mateo', 'cityCode' => '031414000', 'regionCode' => '030000000', 'provinceCode' => '031400000', 'subMunicipalityCode' => null],
                    ['code' => '031414013', 'name' => 'Tigbe', 'cityCode' => '031414000', 'regionCode' => '030000000', 'provinceCode' => '031400000', 'subMunicipalityCode' => null],
                ];
            }
            elseif ($cityCode === '031415000') { // Municipality of Obando
                return [
                    ['code' => '031415001', 'name' => 'Binuangan', 'cityCode' => '031415000', 'regionCode' => '030000000', 'provinceCode' => '031400000', 'subMunicipalityCode' => null],
                    ['code' => '031415002', 'name' => 'Catanghalan', 'cityCode' => '031415000', 'regionCode' => '030000000', 'provinceCode' => '031400000', 'subMunicipalityCode' => null],
                    ['code' => '031415003', 'name' => 'Hulo', 'cityCode' => '031415000', 'regionCode' => '030000000', 'provinceCode' => '031400000', 'subMunicipalityCode' => null],
                    ['code' => '031415004', 'name' => 'Pacoa', 'cityCode' => '031415000', 'regionCode' => '030000000', 'provinceCode' => '031400000', 'subMunicipalityCode' => null],
                    ['code' => '031415005', 'name' => 'Pag-asa', 'cityCode' => '031415000', 'regionCode' => '030000000', 'provinceCode' => '031400000', 'subMunicipalityCode' => null],
                    ['code' => '031415006', 'name' => 'Paliwas', 'cityCode' => '031415000', 'regionCode' => '030000000', 'provinceCode' => '031400000', 'subMunicipalityCode' => null],
                    ['code' => '031415007', 'name' => 'Panghulo', 'cityCode' => '031415000', 'regionCode' => '030000000', 'provinceCode' => '031400000', 'subMunicipalityCode' => null],
                    ['code' => '031415008', 'name' => 'Poblacion', 'cityCode' => '031415000', 'regionCode' => '030000000', 'provinceCode' => '031400000', 'subMunicipalityCode' => null],
                    ['code' => '031415009', 'name' => 'Salambao', 'cityCode' => '031415000', 'regionCode' => '030000000', 'provinceCode' => '031400000', 'subMunicipalityCode' => null],
                    ['code' => '031415010', 'name' => 'San Pascual', 'cityCode' => '031415000', 'regionCode' => '030000000', 'provinceCode' => '031400000', 'subMunicipalityCode' => null],
                    ['code' => '031415011', 'name' => 'Tawiran', 'cityCode' => '031415000', 'regionCode' => '030000000', 'provinceCode' => '031400000', 'subMunicipalityCode' => null],
                ];
            }
            elseif ($cityCode === '031416000') { // Municipality of Pandi
                return [
                    ['code' => '031416001', 'name' => 'Bagbaguin', 'cityCode' => '031416000', 'regionCode' => '030000000', 'provinceCode' => '031400000', 'subMunicipalityCode' => null],
                    ['code' => '031416002', 'name' => 'Baka-bakahan', 'cityCode' => '031416000', 'regionCode' => '030000000', 'provinceCode' => '031400000', 'subMunicipalityCode' => null],
                    ['code' => '031416003', 'name' => 'Bancal', 'cityCode' => '031416000', 'regionCode' => '030000000', 'provinceCode' => '031400000', 'subMunicipalityCode' => null],
                    ['code' => '031416004', 'name' => 'Bunsuran', 'cityCode' => '031416000', 'regionCode' => '030000000', 'provinceCode' => '031400000', 'subMunicipalityCode' => null],
                    ['code' => '031416005', 'name' => 'Cacarong Bata', 'cityCode' => '031416000', 'regionCode' => '030000000', 'provinceCode' => '031400000', 'subMunicipalityCode' => null],
                    ['code' => '031416006', 'name' => 'Cacarong Matanda', 'cityCode' => '031416000', 'regionCode' => '030000000', 'provinceCode' => '031400000', 'subMunicipalityCode' => null],
                    ['code' => '031416007', 'name' => 'Cupang', 'cityCode' => '031416000', 'regionCode' => '030000000', 'provinceCode' => '031400000', 'subMunicipalityCode' => null],
                    ['code' => '031416008', 'name' => 'Masuso', 'cityCode' => '031416000', 'regionCode' => '030000000', 'provinceCode' => '031400000', 'subMunicipalityCode' => null],
                    ['code' => '031416009', 'name' => 'Pinagkuartelan', 'cityCode' => '031416000', 'regionCode' => '030000000', 'provinceCode' => '031400000', 'subMunicipalityCode' => null],
                    ['code' => '031416010', 'name' => 'Poblacion', 'cityCode' => '031416000', 'regionCode' => '030000000', 'provinceCode' => '031400000', 'subMunicipalityCode' => null],
                    ['code' => '031416011', 'name' => 'Real de Cacarong', 'cityCode' => '031416000', 'regionCode' => '030000000', 'provinceCode' => '031400000', 'subMunicipalityCode' => null],
                    ['code' => '031416012', 'name' => 'San Roque', 'cityCode' => '031416000', 'regionCode' => '030000000', 'provinceCode' => '031400000', 'subMunicipalityCode' => null],
                    ['code' => '031416013', 'name' => 'Santo Niño', 'cityCode' => '031416000', 'regionCode' => '030000000', 'provinceCode' => '031400000', 'subMunicipalityCode' => null],
                    ['code' => '031416014', 'name' => 'Siling Bata', 'cityCode' => '031416000', 'regionCode' => '030000000', 'provinceCode' => '031400000', 'subMunicipalityCode' => null],
                    ['code' => '031416015', 'name' => 'Siling Matanda', 'cityCode' => '031416000', 'regionCode' => '030000000', 'provinceCode' => '031400000', 'subMunicipalityCode' => null],
                    ['code' => '031416016', 'name' => 'Sumilang', 'cityCode' => '031416000', 'regionCode' => '030000000', 'provinceCode' => '031400000', 'subMunicipalityCode' => null],
                ];
            }
            elseif ($cityCode === '031417000') { // Municipality of Paombong
                return [
                    ['code' => '031417001', 'name' => 'Binakod', 'cityCode' => '031417000', 'regionCode' => '030000000', 'provinceCode' => '031400000', 'subMunicipalityCode' => null],
                    ['code' => '031417002', 'name' => 'Kapitangan', 'cityCode' => '031417000', 'regionCode' => '030000000', 'provinceCode' => '031400000', 'subMunicipalityCode' => null],
                    ['code' => '031417003', 'name' => 'Malumot', 'cityCode' => '031417000', 'regionCode' => '030000000', 'provinceCode' => '031400000', 'subMunicipalityCode' => null],
                    ['code' => '031417004', 'name' => 'Masukol', 'cityCode' => '031417000', 'regionCode' => '030000000', 'provinceCode' => '031400000', 'subMunicipalityCode' => null],
                    ['code' => '031417005', 'name' => 'Pinalagdan', 'cityCode' => '031417000', 'regionCode' => '030000000', 'provinceCode' => '031400000', 'subMunicipalityCode' => null],
                    ['code' => '031417006', 'name' => 'Poblacion', 'cityCode' => '031417000', 'regionCode' => '030000000', 'provinceCode' => '031400000', 'subMunicipalityCode' => null],
                    ['code' => '031417007', 'name' => 'San Isidro I', 'cityCode' => '031417000', 'regionCode' => '030000000', 'provinceCode' => '031400000', 'subMunicipalityCode' => null],
                    ['code' => '031417008', 'name' => 'San Isidro II', 'cityCode' => '031417000', 'regionCode' => '030000000', 'provinceCode' => '031400000', 'subMunicipalityCode' => null],
                    ['code' => '031417009', 'name' => 'San Roque', 'cityCode' => '031417000', 'regionCode' => '030000000', 'provinceCode' => '031400000', 'subMunicipalityCode' => null],
                    ['code' => '031417010', 'name' => 'Santa Cruz', 'cityCode' => '031417000', 'regionCode' => '030000000', 'provinceCode' => '031400000', 'subMunicipalityCode' => null],
                    ['code' => '031417011', 'name' => 'Santo Niño', 'cityCode' => '031417000', 'regionCode' => '030000000', 'provinceCode' => '031400000', 'subMunicipalityCode' => null],
                    ['code' => '031417012', 'name' => 'Sukol', 'cityCode' => '031417000', 'regionCode' => '030000000', 'provinceCode' => '031400000', 'subMunicipalityCode' => null],
                    ['code' => '031417013', 'name' => 'Tawiran', 'cityCode' => '031417000', 'regionCode' => '030000000', 'provinceCode' => '031400000', 'subMunicipalityCode' => null],
                    ['code' => '031417014', 'name' => 'Tibaguin', 'cityCode' => '031417000', 'regionCode' => '030000000', 'provinceCode' => '031400000', 'subMunicipalityCode' => null],
                ];
            }
            elseif ($cityCode === '031418000') { // Municipality of Plaridel
                return [
                    ['code' => '031418001', 'name' => 'Agnas', 'cityCode' => '031418000', 'regionCode' => '030000000', 'provinceCode' => '031400000', 'subMunicipalityCode' => null],
                    ['code' => '031418002', 'name' => 'Bagong Silang', 'cityCode' => '031418000', 'regionCode' => '030000000', 'provinceCode' => '031400000', 'subMunicipalityCode' => null],
                    ['code' => '031418003', 'name' => 'Bangkal', 'cityCode' => '031418000', 'regionCode' => '030000000', 'provinceCode' => '031400000', 'subMunicipalityCode' => null],
                    ['code' => '031418004', 'name' => 'Bintog', 'cityCode' => '031418000', 'regionCode' => '030000000', 'provinceCode' => '031400000', 'subMunicipalityCode' => null],
                    ['code' => '031418005', 'name' => 'Bulihan', 'cityCode' => '031418000', 'regionCode' => '030000000', 'provinceCode' => '031400000', 'subMunicipalityCode' => null],
                    ['code' => '031418006', 'name' => 'Culianin', 'cityCode' => '031418000', 'regionCode' => '030000000', 'provinceCode' => '031400000', 'subMunicipalityCode' => null],
                    ['code' => '031418007', 'name' => 'Dampol I', 'cityCode' => '031418000', 'regionCode' => '030000000', 'provinceCode' => '031400000', 'subMunicipalityCode' => null],
                    ['code' => '031418008', 'name' => 'Dampol II-A', 'cityCode' => '031418000', 'regionCode' => '030000000', 'provinceCode' => '031400000', 'subMunicipalityCode' => null],
                    ['code' => '031418009', 'name' => 'Dampol II-B', 'cityCode' => '031418000', 'regionCode' => '030000000', 'provinceCode' => '031400000', 'subMunicipalityCode' => null],
                    ['code' => '031418010', 'name' => 'Lagundi', 'cityCode' => '031418000', 'regionCode' => '030000000', 'provinceCode' => '031400000', 'subMunicipalityCode' => null],
                    ['code' => '031418011', 'name' => 'Lalangan', 'cityCode' => '031418000', 'regionCode' => '030000000', 'provinceCode' => '031400000', 'subMunicipalityCode' => null],
                    ['code' => '031418012', 'name' => 'Lumang Bayan', 'cityCode' => '031418000', 'regionCode' => '030000000', 'provinceCode' => '031400000', 'subMunicipalityCode' => null],
                    ['code' => '031418013', 'name' => 'Parian', 'cityCode' => '031418000', 'regionCode' => '030000000', 'provinceCode' => '031400000', 'subMunicipalityCode' => null],
                    ['code' => '031418014', 'name' => 'Poblacion', 'cityCode' => '031418000', 'regionCode' => '030000000', 'provinceCode' => '031400000', 'subMunicipalityCode' => null],
                    ['code' => '031418015', 'name' => 'Rueda', 'cityCode' => '031418000', 'regionCode' => '030000000', 'provinceCode' => '031400000', 'subMunicipalityCode' => null],
                    ['code' => '031418016', 'name' => 'San Jose', 'cityCode' => '031418000', 'regionCode' => '030000000', 'provinceCode' => '031400000', 'subMunicipalityCode' => null],
                    ['code' => '031418017', 'name' => 'Sipat', 'cityCode' => '031418000', 'regionCode' => '030000000', 'provinceCode' => '031400000', 'subMunicipalityCode' => null],
                    ['code' => '031418018', 'name' => 'Tabon', 'cityCode' => '031418000', 'regionCode' => '030000000', 'provinceCode' => '031400000', 'subMunicipalityCode' => null],
                    ['code' => '031418019', 'name' => 'Tawiran', 'cityCode' => '031418000', 'regionCode' => '030000000', 'provinceCode' => '031400000', 'subMunicipalityCode' => null],
                ];
            }
            elseif ($cityCode === '031419000') { // Municipality of Pulilan
                return [
                    ['code' => '031419001', 'name' => 'Balatong A', 'cityCode' => '031419000', 'regionCode' => '030000000', 'provinceCode' => '031400000', 'subMunicipalityCode' => null],
                    ['code' => '031419002', 'name' => 'Balatong B', 'cityCode' => '031419000', 'regionCode' => '030000000', 'provinceCode' => '031400000', 'subMunicipalityCode' => null],
                    ['code' => '031419003', 'name' => 'Bambang', 'cityCode' => '031419000', 'regionCode' => '030000000', 'provinceCode' => '031400000', 'subMunicipalityCode' => null],
                    ['code' => '031419004', 'name' => 'Bunga', 'cityCode' => '031419000', 'regionCode' => '030000000', 'provinceCode' => '031400000', 'subMunicipalityCode' => null],
                    ['code' => '031419005', 'name' => 'Canalate', 'cityCode' => '031419000', 'regionCode' => '030000000', 'provinceCode' => '031400000', 'subMunicipalityCode' => null],
                    ['code' => '031419006', 'name' => 'Carillo', 'cityCode' => '031419000', 'regionCode' => '030000000', 'provinceCode' => '031400000', 'subMunicipalityCode' => null],
                    ['code' => '031419007', 'name' => 'Cutcot', 'cityCode' => '031419000', 'regionCode' => '030000000', 'provinceCode' => '031400000', 'subMunicipalityCode' => null],
                    ['code' => '031419008', 'name' => 'Dampol I', 'cityCode' => '031419000', 'regionCode' => '030000000', 'provinceCode' => '031400000', 'subMunicipalityCode' => null],
                    ['code' => '031419009', 'name' => 'Dampol II-A', 'cityCode' => '031419000', 'regionCode' => '030000000', 'provinceCode' => '031400000', 'subMunicipalityCode' => null],
                    ['code' => '031419010', 'name' => 'Dampol II-B', 'cityCode' => '031419000', 'regionCode' => '030000000', 'provinceCode' => '031400000', 'subMunicipalityCode' => null],
                    ['code' => '031419011', 'name' => 'Dulong Malabon', 'cityCode' => '031419000', 'regionCode' => '030000000', 'provinceCode' => '031400000', 'subMunicipalityCode' => null],
                    ['code' => '031419012', 'name' => 'Inaon', 'cityCode' => '031419000', 'regionCode' => '030000000', 'provinceCode' => '031400000', 'subMunicipalityCode' => null],
                    ['code' => '031419013', 'name' => 'Longos', 'cityCode' => '031419000', 'regionCode' => '030000000', 'provinceCode' => '031400000', 'subMunicipalityCode' => null],
                    ['code' => '031419014', 'name' => 'Lumbac', 'cityCode' => '031419000', 'regionCode' => '030000000', 'provinceCode' => '031400000', 'subMunicipalityCode' => null],
                    ['code' => '031419015', 'name' => 'Poblacion', 'cityCode' => '031419000', 'regionCode' => '030000000', 'provinceCode' => '031400000', 'subMunicipalityCode' => null],
                    ['code' => '031419016', 'name' => 'Sampaloc', 'cityCode' => '031419000', 'regionCode' => '030000000', 'provinceCode' => '031400000', 'subMunicipalityCode' => null],
                    ['code' => '031419017', 'name' => 'San Roque', 'cityCode' => '031419000', 'regionCode' => '030000000', 'provinceCode' => '031400000', 'subMunicipalityCode' => null],
                    ['code' => '031419018', 'name' => 'Santo Cristo', 'cityCode' => '031419000', 'regionCode' => '030000000', 'provinceCode' => '031400000', 'subMunicipalityCode' => null],
                    ['code' => '031419019', 'name' => 'Tabon', 'cityCode' => '031419000', 'regionCode' => '030000000', 'provinceCode' => '031400000', 'subMunicipalityCode' => null],
                ];
            }
            elseif ($cityCode === '031420000') { // Municipality of San Ildefonso
                return [
                    ['code' => '031420001', 'name' => 'Akle', 'cityCode' => '031420000', 'regionCode' => '030000000', 'provinceCode' => '031400000', 'subMunicipalityCode' => null],
                    ['code' => '031420002', 'name' => 'Anyatam', 'cityCode' => '031420000', 'regionCode' => '030000000', 'provinceCode' => '031400000', 'subMunicipalityCode' => null],
                    ['code' => '031420003', 'name' => 'Bagong Barrio', 'cityCode' => '031420000', 'regionCode' => '030000000', 'provinceCode' => '031400000', 'subMunicipalityCode' => null],
                    ['code' => '031420004', 'name' => 'Basuit', 'cityCode' => '031420000', 'regionCode' => '030000000', 'provinceCode' => '031400000', 'subMunicipalityCode' => null],
                    ['code' => '031420005', 'name' => 'Bubukal', 'cityCode' => '031420000', 'regionCode' => '030000000', 'provinceCode' => '031400000', 'subMunicipalityCode' => null],
                    ['code' => '031420006', 'name' => 'Bubulong Munti', 'cityCode' => '031420000', 'regionCode' => '030000000', 'provinceCode' => '031400000', 'subMunicipalityCode' => null],
                    ['code' => '031420007', 'name' => 'Calumpang', 'cityCode' => '031420000', 'regionCode' => '030000000', 'provinceCode' => '031400000', 'subMunicipalityCode' => null],
                    ['code' => '031420008', 'name' => 'Casalat', 'cityCode' => '031420000', 'regionCode' => '030000000', 'provinceCode' => '031400000', 'subMunicipalityCode' => null],
                    ['code' => '031420009', 'name' => 'Caybanban', 'cityCode' => '031420000', 'regionCode' => '030000000', 'provinceCode' => '031400000', 'subMunicipalityCode' => null],
                    ['code' => '031420010', 'name' => 'Cayponce', 'cityCode' => '031420000', 'regionCode' => '030000000', 'provinceCode' => '031400000', 'subMunicipalityCode' => null],
                    ['code' => '031420011', 'name' => 'Caytambog', 'cityCode' => '031420000', 'regionCode' => '030000000', 'provinceCode' => '031400000', 'subMunicipalityCode' => null],
                    ['code' => '031420012', 'name' => 'Caytambog', 'cityCode' => '031420000', 'regionCode' => '030000000', 'provinceCode' => '031400000', 'subMunicipalityCode' => null],
                    ['code' => '031420013', 'name' => 'Diliman I', 'cityCode' => '031420000', 'regionCode' => '030000000', 'provinceCode' => '031400000', 'subMunicipalityCode' => null],
                    ['code' => '031420014', 'name' => 'Diliman II', 'cityCode' => '031420000', 'regionCode' => '030000000', 'provinceCode' => '031400000', 'subMunicipalityCode' => null],
                    ['code' => '031420015', 'name' => 'Diliman III', 'cityCode' => '031420000', 'regionCode' => '030000000', 'provinceCode' => '031400000', 'subMunicipalityCode' => null],
                    ['code' => '031420016', 'name' => 'Diliman IV', 'cityCode' => '031420000', 'regionCode' => '030000000', 'provinceCode' => '031400000', 'subMunicipalityCode' => null],
                    ['code' => '031420017', 'name' => 'Diliman V', 'cityCode' => '031420000', 'regionCode' => '030000000', 'provinceCode' => '031400000', 'subMunicipalityCode' => null],
                    ['code' => '031420018', 'name' => 'Diliman VI', 'cityCode' => '031420000', 'regionCode' => '030000000', 'provinceCode' => '031400000', 'subMunicipalityCode' => null],
                    ['code' => '031420019', 'name' => 'Diliman VII', 'cityCode' => '031420000', 'regionCode' => '030000000', 'provinceCode' => '031400000', 'subMunicipalityCode' => null],
                    ['code' => '031420020', 'name' => 'Diliman VIII', 'cityCode' => '031420000', 'regionCode' => '030000000', 'provinceCode' => '031400000', 'subMunicipalityCode' => null],
                    ['code' => '031420021', 'name' => 'Diliman IX', 'cityCode' => '031420000', 'regionCode' => '030000000', 'provinceCode' => '031400000', 'subMunicipalityCode' => null],
                    ['code' => '031420022', 'name' => 'Diliman X', 'cityCode' => '031420000', 'regionCode' => '030000000', 'provinceCode' => '031400000', 'subMunicipalityCode' => null],
                    ['code' => '031420023', 'name' => 'Diliman XI', 'cityCode' => '031420000', 'regionCode' => '030000000', 'provinceCode' => '031400000', 'subMunicipalityCode' => null],
                    ['code' => '031420024', 'name' => 'Diliman XII', 'cityCode' => '031420000', 'regionCode' => '030000000', 'provinceCode' => '031400000', 'subMunicipalityCode' => null],
                    ['code' => '031420025', 'name' => 'Diliman XIII', 'cityCode' => '031420000', 'regionCode' => '030000000', 'provinceCode' => '031400000', 'subMunicipalityCode' => null],
                    ['code' => '031420026', 'name' => 'Diliman XIV', 'cityCode' => '031420000', 'regionCode' => '030000000', 'provinceCode' => '031400000', 'subMunicipalityCode' => null],
                    ['code' => '031420027', 'name' => 'Diliman XV', 'cityCode' => '031420000', 'regionCode' => '030000000', 'provinceCode' => '031400000', 'subMunicipalityCode' => null],
                    ['code' => '031420028', 'name' => 'Diliman XVI', 'cityCode' => '031420000', 'regionCode' => '030000000', 'provinceCode' => '031400000', 'subMunicipalityCode' => null],
                    ['code' => '031420029', 'name' => 'Diliman XVII', 'cityCode' => '031420000', 'regionCode' => '030000000', 'provinceCode' => '031400000', 'subMunicipalityCode' => null],
                    ['code' => '031420030', 'name' => 'Diliman XVIII', 'cityCode' => '031420000', 'regionCode' => '030000000', 'provinceCode' => '031400000', 'subMunicipalityCode' => null],
                    ['code' => '031420031', 'name' => 'Diliman XIX', 'cityCode' => '031420000', 'regionCode' => '030000000', 'provinceCode' => '031400000', 'subMunicipalityCode' => null],
                    ['code' => '031420032', 'name' => 'Diliman XX', 'cityCode' => '031420000', 'regionCode' => '030000000', 'provinceCode' => '031400000', 'subMunicipalityCode' => null],
                    ['code' => '031420033', 'name' => 'Diliman XXI', 'cityCode' => '031420000', 'regionCode' => '030000000', 'provinceCode' => '031400000', 'subMunicipalityCode' => null],
                    ['code' => '031420034', 'name' => 'Diliman XXII', 'cityCode' => '031420000', 'regionCode' => '030000000', 'provinceCode' => '031400000', 'subMunicipalityCode' => null],
                    ['code' => '031420035', 'name' => 'Diliman XXIII', 'cityCode' => '031420000', 'regionCode' => '030000000', 'provinceCode' => '031400000', 'subMunicipalityCode' => null],
                    ['code' => '031420036', 'name' => 'Diliman XXIV', 'cityCode' => '031420000', 'regionCode' => '030000000', 'provinceCode' => '031400000', 'subMunicipalityCode' => null],
                ];
            }
            elseif ($cityCode === '031422000') { // Municipality of San Miguel
                return [
                    ['code' => '031422001', 'name' => 'Bagong Silang', 'cityCode' => '031422000', 'regionCode' => '030000000', 'provinceCode' => '031400000', 'subMunicipalityCode' => null],
                    ['code' => '031422002', 'name' => 'Balaong', 'cityCode' => '031422000', 'regionCode' => '030000000', 'provinceCode' => '031400000', 'subMunicipalityCode' => null],
                    ['code' => '031422003', 'name' => 'Balite', 'cityCode' => '031422000', 'regionCode' => '030000000', 'provinceCode' => '031400000', 'subMunicipalityCode' => null],
                    ['code' => '031422004', 'name' => 'Bantog', 'cityCode' => '031422000', 'regionCode' => '030000000', 'provinceCode' => '031400000', 'subMunicipalityCode' => null],
                    ['code' => '031422005', 'name' => 'Bardias', 'cityCode' => '031422000', 'regionCode' => '030000000', 'provinceCode' => '031400000', 'subMunicipalityCode' => null],
                    ['code' => '031422006', 'name' => 'Bigaa', 'cityCode' => '031422000', 'regionCode' => '030000000', 'provinceCode' => '031400000', 'subMunicipalityCode' => null],
                    ['code' => '031422007', 'name' => 'Binuangan', 'cityCode' => '031422000', 'regionCode' => '030000000', 'provinceCode' => '031400000', 'subMunicipalityCode' => null],
                    ['code' => '031422008', 'name' => 'Bitungol', 'cityCode' => '031422000', 'regionCode' => '030000000', 'provinceCode' => '031400000', 'subMunicipalityCode' => null],
                    ['code' => '031422009', 'name' => 'Bulac', 'cityCode' => '031422000', 'regionCode' => '030000000', 'provinceCode' => '031400000', 'subMunicipalityCode' => null],
                    ['code' => '031422010', 'name' => 'Buli', 'cityCode' => '031422000', 'regionCode' => '030000000', 'provinceCode' => '031400000', 'subMunicipalityCode' => null],
                    ['code' => '031422011', 'name' => 'Calumpang', 'cityCode' => '031422000', 'regionCode' => '030000000', 'provinceCode' => '031400000', 'subMunicipalityCode' => null],
                    ['code' => '031422012', 'name' => 'Camias', 'cityCode' => '031422000', 'regionCode' => '030000000', 'provinceCode' => '031400000', 'subMunicipalityCode' => null],
                    ['code' => '031422013', 'name' => 'Candaba', 'cityCode' => '031422000', 'regionCode' => '030000000', 'provinceCode' => '031400000', 'subMunicipalityCode' => null],
                    ['code' => '031422014', 'name' => 'Capitol', 'cityCode' => '031422000', 'regionCode' => '030000000', 'provinceCode' => '031400000', 'subMunicipalityCode' => null],
                    ['code' => '031422015', 'name' => 'Cuyapo', 'cityCode' => '031422000', 'regionCode' => '030000000', 'provinceCode' => '031400000', 'subMunicipalityCode' => null],
                    ['code' => '031422016', 'name' => 'Dampol', 'cityCode' => '031422000', 'regionCode' => '030000000', 'provinceCode' => '031400000', 'subMunicipalityCode' => null],
                    ['code' => '031422017', 'name' => 'Gatbuca', 'cityCode' => '031422000', 'regionCode' => '030000000', 'provinceCode' => '031400000', 'subMunicipalityCode' => null],
                    ['code' => '031422018', 'name' => 'Gumaca', 'cityCode' => '031422000', 'regionCode' => '030000000', 'provinceCode' => '031400000', 'subMunicipalityCode' => null],
                    ['code' => '031422019', 'name' => 'Hagonoy', 'cityCode' => '031422000', 'regionCode' => '030000000', 'provinceCode' => '031400000', 'subMunicipalityCode' => null],
                    ['code' => '031422020', 'name' => 'Iba', 'cityCode' => '031422000', 'regionCode' => '030000000', 'provinceCode' => '031400000', 'subMunicipalityCode' => null],
                    ['code' => '031422021', 'name' => 'Kamias', 'cityCode' => '031422000', 'regionCode' => '030000000', 'provinceCode' => '031400000', 'subMunicipalityCode' => null],
                    ['code' => '031422022', 'name' => 'Lambakin', 'cityCode' => '031422000', 'regionCode' => '030000000', 'provinceCode' => '031400000', 'subMunicipalityCode' => null],
                    ['code' => '031422023', 'name' => 'Lambakin', 'cityCode' => '031422000', 'regionCode' => '030000000', 'provinceCode' => '031400000', 'subMunicipalityCode' => null],
                    ['code' => '031422024', 'name' => 'Lambakin', 'cityCode' => '031422000', 'regionCode' => '030000000', 'provinceCode' => '031400000', 'subMunicipalityCode' => null],
                    ['code' => '031422025', 'name' => 'Lambakin', 'cityCode' => '031422000', 'regionCode' => '030000000', 'provinceCode' => '031400000', 'subMunicipalityCode' => null],
                    ['code' => '031422026', 'name' => 'Lambakin', 'cityCode' => '031422000', 'regionCode' => '030000000', 'provinceCode' => '031400000', 'subMunicipalityCode' => null],
                    ['code' => '031422027', 'name' => 'Lambakin', 'cityCode' => '031422000', 'regionCode' => '030000000', 'provinceCode' => '031400000', 'subMunicipalityCode' => null],
                    ['code' => '031422028', 'name' => 'Lambakin', 'cityCode' => '031422000', 'regionCode' => '030000000', 'provinceCode' => '031400000', 'subMunicipalityCode' => null],
                    ['code' => '031422029', 'name' => 'Lambakin', 'cityCode' => '031422000', 'regionCode' => '030000000', 'provinceCode' => '031400000', 'subMunicipalityCode' => null],
                    ['code' => '031422030', 'name' => 'Lambakin', 'cityCode' => '031422000', 'regionCode' => '030000000', 'provinceCode' => '031400000', 'subMunicipalityCode' => null],
                    ['code' => '031422031', 'name' => 'Lambakin', 'cityCode' => '031422000', 'regionCode' => '030000000', 'provinceCode' => '031400000', 'subMunicipalityCode' => null],
                    ['code' => '031422032', 'name' => 'Lambakin', 'cityCode' => '031422000', 'regionCode' => '030000000', 'provinceCode' => '031400000', 'subMunicipalityCode' => null],
                    ['code' => '031422033', 'name' => 'Lambakin', 'cityCode' => '031422000', 'regionCode' => '030000000', 'provinceCode' => '031400000', 'subMunicipalityCode' => null],
                    ['code' => '031422034', 'name' => 'Lambakin', 'cityCode' => '031422000', 'regionCode' => '030000000', 'provinceCode' => '031400000', 'subMunicipalityCode' => null],
                    ['code' => '031422035', 'name' => 'Lambakin', 'cityCode' => '031422000', 'regionCode' => '030000000', 'provinceCode' => '031400000', 'subMunicipalityCode' => null],
                    ['code' => '031422036', 'name' => 'Lambakin', 'cityCode' => '031422000', 'regionCode' => '030000000', 'provinceCode' => '031400000', 'subMunicipalityCode' => null],
                    ['code' => '031422037', 'name' => 'Lambakin', 'cityCode' => '031422000', 'regionCode' => '030000000', 'provinceCode' => '031400000', 'subMunicipalityCode' => null],
                    ['code' => '031422038', 'name' => 'Lambakin', 'cityCode' => '031422000', 'regionCode' => '030000000', 'provinceCode' => '031400000', 'subMunicipalityCode' => null],
                    ['code' => '031422039', 'name' => 'Lambakin', 'cityCode' => '031422000', 'regionCode' => '030000000', 'provinceCode' => '031400000', 'subMunicipalityCode' => null],
                    ['code' => '031422040', 'name' => 'Lambakin', 'cityCode' => '031422000', 'regionCode' => '030000000', 'provinceCode' => '031400000', 'subMunicipalityCode' => null],
                    ['code' => '031422041', 'name' => 'Lambakin', 'cityCode' => '031422000', 'regionCode' => '030000000', 'provinceCode' => '031400000', 'subMunicipalityCode' => null],
                    ['code' => '031422042', 'name' => 'Lambakin', 'cityCode' => '031422000', 'regionCode' => '030000000', 'provinceCode' => '031400000', 'subMunicipalityCode' => null],
                    ['code' => '031422043', 'name' => 'Lambakin', 'cityCode' => '031422000', 'regionCode' => '030000000', 'provinceCode' => '031400000', 'subMunicipalityCode' => null],
                    ['code' => '031422044', 'name' => 'Lambakin', 'cityCode' => '031422000', 'regionCode' => '030000000', 'provinceCode' => '031400000', 'subMunicipalityCode' => null],
                    ['code' => '031422045', 'name' => 'Lambakin', 'cityCode' => '031422000', 'regionCode' => '030000000', 'provinceCode' => '031400000', 'subMunicipalityCode' => null],
                    ['code' => '031422046', 'name' => 'Lambakin', 'cityCode' => '031422000', 'regionCode' => '030000000', 'provinceCode' => '031400000', 'subMunicipalityCode' => null],
                    ['code' => '031422047', 'name' => 'Lambakin', 'cityCode' => '031422000', 'regionCode' => '030000000', 'provinceCode' => '031400000', 'subMunicipalityCode' => null],
                    ['code' => '031422048', 'name' => 'Lambakin', 'cityCode' => '031422000', 'regionCode' => '030000000', 'provinceCode' => '031400000', 'subMunicipalityCode' => null],
                    ['code' => '031422049', 'name' => 'Lambakin', 'cityCode' => '031422000', 'regionCode' => '030000000', 'provinceCode' => '031400000', 'subMunicipalityCode' => null],
                ];
            }
            elseif ($cityCode === '031423000') { // Municipality of San Rafael
                return [
                    ['code' => '031423001', 'name' => 'Balaong', 'cityCode' => '031423000', 'regionCode' => '030000000', 'provinceCode' => '031400000', 'subMunicipalityCode' => null],
                    ['code' => '031423002', 'name' => 'Bancal', 'cityCode' => '031423000', 'regionCode' => '030000000', 'provinceCode' => '031400000', 'subMunicipalityCode' => null],
                    ['code' => '031423003', 'name' => 'Bancal', 'cityCode' => '031423000', 'regionCode' => '030000000', 'provinceCode' => '031400000', 'subMunicipalityCode' => null],
                    ['code' => '031423004', 'name' => 'Bancal', 'cityCode' => '031423000', 'regionCode' => '030000000', 'provinceCode' => '031400000', 'subMunicipalityCode' => null],
                    ['code' => '031423005', 'name' => 'Bancal', 'cityCode' => '031423000', 'regionCode' => '030000000', 'provinceCode' => '031400000', 'subMunicipalityCode' => null],
                    ['code' => '031423006', 'name' => 'Bancal', 'cityCode' => '031423000', 'regionCode' => '030000000', 'provinceCode' => '031400000', 'subMunicipalityCode' => null],
                    ['code' => '031423007', 'name' => 'Bancal', 'cityCode' => '031423000', 'regionCode' => '030000000', 'provinceCode' => '031400000', 'subMunicipalityCode' => null],
                    ['code' => '031423008', 'name' => 'Bancal', 'cityCode' => '031423000', 'regionCode' => '030000000', 'provinceCode' => '031400000', 'subMunicipalityCode' => null],
                    ['code' => '031423009', 'name' => 'Bancal', 'cityCode' => '031423000', 'regionCode' => '030000000', 'provinceCode' => '031400000', 'subMunicipalityCode' => null],
                    ['code' => '031423010', 'name' => 'Bancal', 'cityCode' => '031423000', 'regionCode' => '030000000', 'provinceCode' => '031400000', 'subMunicipalityCode' => null],
                    ['code' => '031423011', 'name' => 'Bancal', 'cityCode' => '031423000', 'regionCode' => '030000000', 'provinceCode' => '031400000', 'subMunicipalityCode' => null],
                    ['code' => '031423012', 'name' => 'Bancal', 'cityCode' => '031423000', 'regionCode' => '030000000', 'provinceCode' => '031400000', 'subMunicipalityCode' => null],
                    ['code' => '031423013', 'name' => 'Bancal', 'cityCode' => '031423000', 'regionCode' => '030000000', 'provinceCode' => '031400000', 'subMunicipalityCode' => null],
                    ['code' => '031423014', 'name' => 'Bancal', 'cityCode' => '031423000', 'regionCode' => '030000000', 'provinceCode' => '031400000', 'subMunicipalityCode' => null],
                    ['code' => '031423015', 'name' => 'Bancal', 'cityCode' => '031423000', 'regionCode' => '030000000', 'provinceCode' => '031400000', 'subMunicipalityCode' => null],
                    ['code' => '031423016', 'name' => 'Bancal', 'cityCode' => '031423000', 'regionCode' => '030000000', 'provinceCode' => '031400000', 'subMunicipalityCode' => null],
                    ['code' => '031423017', 'name' => 'Bancal', 'cityCode' => '031423000', 'regionCode' => '030000000', 'provinceCode' => '031400000', 'subMunicipalityCode' => null],
                    ['code' => '031423018', 'name' => 'Bancal', 'cityCode' => '031423000', 'regionCode' => '030000000', 'provinceCode' => '031400000', 'subMunicipalityCode' => null],
                    ['code' => '031423019', 'name' => 'Bancal', 'cityCode' => '031423000', 'regionCode' => '030000000', 'provinceCode' => '031400000', 'subMunicipalityCode' => null],
                    ['code' => '031423020', 'name' => 'Bancal', 'cityCode' => '031423000', 'regionCode' => '030000000', 'provinceCode' => '031400000', 'subMunicipalityCode' => null],
                    ['code' => '031423021', 'name' => 'Bancal', 'cityCode' => '031423000', 'regionCode' => '030000000', 'provinceCode' => '031400000', 'subMunicipalityCode' => null],
                    ['code' => '031423022', 'name' => 'Bancal', 'cityCode' => '031423000', 'regionCode' => '030000000', 'provinceCode' => '031400000', 'subMunicipalityCode' => null],
                    ['code' => '031423023', 'name' => 'Bancal', 'cityCode' => '031423000', 'regionCode' => '030000000', 'provinceCode' => '031400000', 'subMunicipalityCode' => null],
                    ['code' => '031423024', 'name' => 'Bancal', 'cityCode' => '031423000', 'regionCode' => '030000000', 'provinceCode' => '031400000', 'subMunicipalityCode' => null],
                    ['code' => '031423025', 'name' => 'Bancal', 'cityCode' => '031423000', 'regionCode' => '030000000', 'provinceCode' => '031400000', 'subMunicipalityCode' => null],
                    ['code' => '031423026', 'name' => 'Bancal', 'cityCode' => '031423000', 'regionCode' => '030000000', 'provinceCode' => '031400000', 'subMunicipalityCode' => null],
                    ['code' => '031423027', 'name' => 'Bancal', 'cityCode' => '031423000', 'regionCode' => '030000000', 'provinceCode' => '031400000', 'subMunicipalityCode' => null],
                    ['code' => '031423028', 'name' => 'Bancal', 'cityCode' => '031423000', 'regionCode' => '030000000', 'provinceCode' => '031400000', 'subMunicipalityCode' => null],
                    ['code' => '031423029', 'name' => 'Bancal', 'cityCode' => '031423000', 'regionCode' => '030000000', 'provinceCode' => '031400000', 'subMunicipalityCode' => null],
                    ['code' => '031423030', 'name' => 'Bancal', 'cityCode' => '031423000', 'regionCode' => '030000000', 'provinceCode' => '031400000', 'subMunicipalityCode' => null],
                    ['code' => '031423031', 'name' => 'Bancal', 'cityCode' => '031423000', 'regionCode' => '030000000', 'provinceCode' => '031400000', 'subMunicipalityCode' => null],
                    ['code' => '031423032', 'name' => 'Bancal', 'cityCode' => '031423000', 'regionCode' => '030000000', 'provinceCode' => '031400000', 'subMunicipalityCode' => null],
                    ['code' => '031423033', 'name' => 'Bancal', 'cityCode' => '031423000', 'regionCode' => '030000000', 'provinceCode' => '031400000', 'subMunicipalityCode' => null],
                    ['code' => '031423034', 'name' => 'Bancal', 'cityCode' => '031423000', 'regionCode' => '030000000', 'provinceCode' => '031400000', 'subMunicipalityCode' => null],
                ];
            }
            elseif ($cityCode === '031424000') { // Municipality of Santa Maria
                return [
                    ['code' => '031424001', 'name' => 'Bagbaguin', 'cityCode' => '031424000', 'regionCode' => '030000000', 'provinceCode' => '031400000', 'subMunicipalityCode' => null],
                    ['code' => '031424002', 'name' => 'Balasing', 'cityCode' => '031424000', 'regionCode' => '030000000', 'provinceCode' => '031400000', 'subMunicipalityCode' => null],
                    ['code' => '031424003', 'name' => 'Buenavista', 'cityCode' => '031424000', 'regionCode' => '030000000', 'provinceCode' => '031400000', 'subMunicipalityCode' => null],
                    ['code' => '031424004', 'name' => 'Bulac', 'cityCode' => '031424000', 'regionCode' => '030000000', 'provinceCode' => '031400000', 'subMunicipalityCode' => null],
                    ['code' => '031424005', 'name' => 'Camangyanan', 'cityCode' => '031424000', 'regionCode' => '030000000', 'provinceCode' => '031400000', 'subMunicipalityCode' => null],
                    ['code' => '031424006', 'name' => 'Catmon', 'cityCode' => '031424000', 'regionCode' => '030000000', 'provinceCode' => '031400000', 'subMunicipalityCode' => null],
                    ['code' => '031424007', 'name' => 'Caypombo', 'cityCode' => '031424000', 'regionCode' => '030000000', 'provinceCode' => '031400000', 'subMunicipalityCode' => null],
                    ['code' => '031424008', 'name' => 'Caysasay', 'cityCode' => '031424000', 'regionCode' => '030000000', 'provinceCode' => '031400000', 'subMunicipalityCode' => null],
                    ['code' => '031424009', 'name' => 'Guyong', 'cityCode' => '031424000', 'regionCode' => '030000000', 'provinceCode' => '031400000', 'subMunicipalityCode' => null],
                    ['code' => '031424010', 'name' => 'Lalakhan', 'cityCode' => '031424000', 'regionCode' => '030000000', 'provinceCode' => '031400000', 'subMunicipalityCode' => null],
                    ['code' => '031424011', 'name' => 'Mag-asawang Sapa', 'cityCode' => '031424000', 'regionCode' => '030000000', 'provinceCode' => '031400000', 'subMunicipalityCode' => null],
                    ['code' => '031424012', 'name' => 'Mahabang Parang', 'cityCode' => '031424000', 'regionCode' => '030000000', 'provinceCode' => '031400000', 'subMunicipalityCode' => null],
                    ['code' => '031424013', 'name' => 'Manggahan', 'cityCode' => '031424000', 'regionCode' => '030000000', 'provinceCode' => '031400000', 'subMunicipalityCode' => null],
                    ['code' => '031424014', 'name' => 'Parada', 'cityCode' => '031424000', 'regionCode' => '030000000', 'provinceCode' => '031400000', 'subMunicipalityCode' => null],
                    ['code' => '031424015', 'name' => 'Poblacion', 'cityCode' => '031424000', 'regionCode' => '030000000', 'provinceCode' => '031400000', 'subMunicipalityCode' => null],
                    ['code' => '031424016', 'name' => 'Pulong Buhangin', 'cityCode' => '031424000', 'regionCode' => '030000000', 'provinceCode' => '031400000', 'subMunicipalityCode' => null],
                    ['code' => '031424017', 'name' => 'San Gabriel', 'cityCode' => '031424000', 'regionCode' => '030000000', 'provinceCode' => '031400000', 'subMunicipalityCode' => null],
                    ['code' => '031424018', 'name' => 'San Jose Patag', 'cityCode' => '031424000', 'regionCode' => '030000000', 'provinceCode' => '031400000', 'subMunicipalityCode' => null],
                    ['code' => '031424019', 'name' => 'San Vicente', 'cityCode' => '031424000', 'regionCode' => '030000000', 'provinceCode' => '031400000', 'subMunicipalityCode' => null],
                    ['code' => '031424020', 'name' => 'Santo Cristo', 'cityCode' => '031424000', 'regionCode' => '030000000', 'provinceCode' => '031400000', 'subMunicipalityCode' => null],
                    ['code' => '031424021', 'name' => 'Santo Niño', 'cityCode' => '031424000', 'regionCode' => '030000000', 'provinceCode' => '031400000', 'subMunicipalityCode' => null],
                    ['code' => '031424022', 'name' => 'Silangan', 'cityCode' => '031424000', 'regionCode' => '030000000', 'provinceCode' => '031400000', 'subMunicipalityCode' => null],
                    ['code' => '031424023', 'name' => 'Tabing Bakod', 'cityCode' => '031424000', 'regionCode' => '030000000', 'provinceCode' => '031400000', 'subMunicipalityCode' => null],
                    ['code' => '031424024', 'name' => 'Tumana', 'cityCode' => '031424000', 'regionCode' => '030000000', 'provinceCode' => '031400000', 'subMunicipalityCode' => null],
                ];
            }
            
            $response = Http::get("{$this->baseUrl}/cities/{$cityCode}/barangays");
            if ($response->successful()) {
                $barangays = $response->json();
                return array_map(function ($barangay) {
                    return [
                        'code' => $barangay['code'],
                        'name' => $barangay['name'],
                        'cityCode' => $barangay['cityCode'],
                        'regionCode' => $barangay['regionCode'],
                        'provinceCode' => $barangay['provinceCode'] ?? null,
                        'subMunicipalityCode' => $barangay['subMunicipalityCode'] ?? null,
                    ];
                }, $barangays);
            }
            return [];
        });
    }

    /**
     * Search for a specific city/municipality and get its parent data
     */
    public function findCityWithParents($cityName)
    {
        return Cache::remember("psgc_gitlab_city_search_{$cityName}", $this->cacheTimeout, function () use ($cityName) {
            // Search in all regions
            $regions = $this->getRegions();
            
            foreach ($regions as $region) {
                // Check if region has provinces
                $provinces = $this->getProvincesByRegion($region['code']);
                
                if (!empty($provinces)) {
                    // Regular region with provinces
                    foreach ($provinces as $province) {
                        $cities = $this->getCitiesByProvince($province['code']);
                        foreach ($cities as $city) {
                            if (stripos($city['name'], $cityName) !== false) {
                                return [
                                    'city' => $city,
                                    'province' => $province,
                                    'region' => $region
                                ];
                            }
                        }
                    }
                } else {
                    // NCR or similar - cities directly under region
                    $cities = $this->getCitiesByRegion($region['code']);
                    foreach ($cities as $city) {
                        if (stripos($city['name'], $cityName) !== false) {
                            return [
                                'city' => $city,
                                'province' => [
                                    'code' => $region['code'],
                                    'name' => $region['name'],
                                ],
                                'region' => $region
                            ];
                        }
                    }
                }
            }
            
            return null;
        });
    }

    /**
     * Search for a specific barangay and get its parent data
     */
    public function findBarangayWithParents($barangayName, $cityName = null)
    {
        return Cache::remember("psgc_gitlab_barangay_search_{$barangayName}_{$cityName}", $this->cacheTimeout, function () use ($barangayName, $cityName) {
            // Search in all regions
            $regions = $this->getRegions();
            
            foreach ($regions as $region) {
                // Check if region has provinces
                $provinces = $this->getProvincesByRegion($region['code']);
                
                if (!empty($provinces)) {
                    // Regular region with provinces
                    foreach ($provinces as $province) {
                        $cities = $this->getCitiesByProvince($province['code']);
                        foreach ($cities as $city) {
                            // If cityName is specified, filter by it
                            if ($cityName && stripos($city['name'], $cityName) === false) {
                                continue;
                            }
                            
                            $barangays = $this->getBarangaysByCity($city['code']);
                            foreach ($barangays as $barangay) {
                                if (stripos($barangay['name'], $barangayName) !== false) {
                                    return [
                                        'barangay' => $barangay,
                                        'city' => $city,
                                        'province' => $province,
                                        'region' => $region
                                    ];
                                }
                            }
                        }
                    }
                } else {
                    // NCR or similar - cities directly under region
                    $cities = $this->getCitiesByRegion($region['code']);
                    foreach ($cities as $city) {
                        // If cityName is specified, filter by it
                        if ($cityName && stripos($city['name'], $cityName) === false) {
                            continue;
                        }
                        
                        $barangays = $this->getBarangaysByCity($city['code']);
                        foreach ($barangays as $barangay) {
                            if (stripos($barangay['name'], $barangayName) !== false) {
                                return [
                                    'barangay' => $barangay,
                                    'city' => $city,
                                    'province' => [
                                        'code' => $region['code'],
                                        'name' => $region['name'],
                                    ],
                                    'region' => $region
                                ];
                            }
                        }
                    }
                }
            }
            
            return null;
        });
    }

    /**
     * Get all data for a specific region
     */
    public function getRegionData($regionCode)
    {
        return Cache::remember("psgc_region_data_{$regionCode}", $this->cacheTimeout, function () use ($regionCode) {
            $region = PSGCRegion::where('code', $regionCode)->first();
            if (!$region) {
                return null;
            }

            $provinces = PSGCProvince::where('region_code', $regionCode)->get();
            $regionData = [
                'region' => [
                    'code' => $region->code,
                    'name' => $region->name,
                ],
                'provinces' => []
            ];

            foreach ($provinces as $province) {
                $cities = PSGCCity::where('province_code', $province->code)->get();
                $provinceData = [
                    'province' => [
                        'code' => $province->code,
                        'name' => $province->name,
                    ],
                    'cities' => []
                ];

                foreach ($cities as $city) {
                    $barangays = PSGCBarangay::where('city_code', $city->code)->get();
                    $provinceData['cities'][] = [
                        'city' => [
                            'code' => $city->code,
                            'name' => $city->name,
                            'type' => $city->type,
                            'zip_code' => $city->zip_code,
                        ],
                        'barangays' => $barangays->map(function ($barangay) {
                            return [
                                'code' => $barangay->code,
                                'name' => $barangay->name,
                            ];
                        })->toArray()
                    ];
                }

                $regionData['provinces'][] = $provinceData;
            }

            return $regionData;
        });
    }

    /**
     * Clear all cached data
     */
    public function clearCache()
    {
        Cache::forget('psgc_regions');
        // Clear other cached items as needed
    }
}