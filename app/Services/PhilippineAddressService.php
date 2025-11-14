<?php

namespace App\Services;

use App\Models\PSGCCity;
use App\Models\PSGCBarangay;
use App\Models\PSGCRegion;
use App\Models\PSGCProvince;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class PhilippineAddressService
{
    private $cacheTimeout = 86400; // 24 hours

    /**
     * Get all regions from database
     */
    public function getRegions()
    {
        return Cache::remember('philippine_address_regions', $this->cacheTimeout, function () {
            // Query regions directly from database
            $regions = PSGCRegion::orderBy('name', 'asc')->get();
            
            // Format the data for the frontend
            return $regions->map(function ($region) {
                return [
                    'code' => $region->code,
                    'name' => $region->name,
                ];
            })->toArray();
        });
    }

    /**
     * Get provinces by region code from database
     */
    public function getProvincesByRegion($regionCode)
    {
        return Cache::remember("philippine_address_provinces_{$regionCode}", $this->cacheTimeout, function () use ($regionCode) {
            // Query provinces directly from database
            $provinces = PSGCProvince::where('region_code', $regionCode)
                                ->orderBy('name', 'asc')
                                ->get();
            
            // Format the data for the frontend
            return $provinces->map(function ($province) {
                return [
                    'code' => $province->code,
                    'name' => $province->name,
                    'region_code' => $province->region_code,
                ];
            })->toArray();
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
                // Handle null/empty zip codes - convert to empty string for consistency
                $zipCode = !empty($city->zip_code) ? $city->zip_code : '';
                
                return [
                    'code'          => $city->code,
                    'name'          => $city->name,
                    'province_code' => $city->province_code,
                    'zipCode'       => $zipCode, // Set both for frontend
                    'zip_code'      => $zipCode  // compatibility
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

