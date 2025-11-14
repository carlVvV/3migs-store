<?php

namespace App\Services;

use App\Models\PSGCCity;
use App\Models\PSGCBarangay;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class PhilippineAddressService
{
    private $cacheTimeout = 86400; // 24 hours

    /**
     * Get all regions using select-philippines-address package
     */
    public function getRegions()
    {
        return Cache::remember('philippine_address_regions', $this->cacheTimeout, function () {
            try {
                $nodeCommand = "node -e \"const { regions } = require('./node_modules/select-philippines-address'); regions().then(data => console.log(JSON.stringify(data)));\"";
                $output = shell_exec($nodeCommand);
                $data = json_decode($output, true);
                
                if ($data && is_array($data)) {
                    return array_map(function ($region) {
                        return [
                            'code' => $region['region_code'] ?? '',
                            'name' => $region['region_name'] ?? '',
                        ];
                    }, $data);
                }
            } catch (\Exception $e) {
                Log::error('Failed to fetch regions', ['error' => $e->getMessage()]);
            }
            
            return [];
        });
    }

    /**
     * Get provinces by region code
     */
    public function getProvincesByRegion($regionCode)
    {
        return Cache::remember("philippine_address_provinces_{$regionCode}", $this->cacheTimeout, function () use ($regionCode) {
            try {
                $nodeCommand = "node -e \"const { provinces } = require('./node_modules/select-philippines-address'); provinces('{$regionCode}').then(data => console.log(JSON.stringify(data)));\"";
                $output = shell_exec($nodeCommand);
                $data = json_decode($output, true);
                
                if ($data && is_array($data)) {
                    return array_map(function ($province) {
                        return [
                            'code' => $province['province_code'] ?? '',
                            'name' => $province['province_name'] ?? '',
                            'region_code' => $province['region_code'] ?? '',
                        ];
                    }, $data);
                }
            } catch (\Exception $e) {
                Log::error('Failed to fetch provinces', ['error' => $e->getMessage(), 'region_code' => $regionCode]);
            }
            
            return [];
        });
    }

    /**
     * Get cities by province code
     */
    public function getCitiesByProvince($provinceCode)
    {
        // We will use the same cache key to avoid changing the controller
        $cacheKey = "philippine_address_cities_{$provinceCode}";
        
        // Cache for 24 hours
        return Cache::remember($cacheKey, 60 * 60 * 24, function () use ($provinceCode) {
            // --- NEW, SIMPLE LOGIC ---
            // 1. Get all cities/municipalities directly from our database.
            $cities = PSGCCity::where('province_code', $provinceCode)
                                ->orderBy('name', 'asc')
                                ->get();
            
            // 2. Format the data for the frontend (as per the documentation)
            return $cities->map(function ($city) {
                return [
                    'code'          => $city->code,
                    'name'          => $city->name,
                    'province_code' => $city->province_code,
                    'zipCode'       => $city->zip_code, // Set both for frontend
                    'zip_code'      => $city->zip_code  // compatibility
                ];
            })->toArray();
            // --- END OF NEW LOGIC ---
        });
    }

    /**
     * Get barangays by city code
     */
    public function getBarangaysByCity($cityCode)
    {
        $cacheKey = "philippine_address_barangays_{$cityCode}";
        
        return Cache::remember($cacheKey, 60 * 60 * 24, function () use ($cityCode) {
            // Query barangays directly from database
            $barangays = PSGCBarangay::where('city_code', $cityCode)
                                ->orderBy('name', 'asc')
                                ->get();
            
            // Format the data for the frontend
            return $barangays->map(function ($barangay) {
                return [
                    'code' => $barangay->code,
                    'name' => $barangay->name,
                    'city_code' => $barangay->city_code,
                ];
            })->toArray();
        });
    }
}

