<?php

namespace App\Services;

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
        return Cache::remember("philippine_address_cities_{$provinceCode}", $this->cacheTimeout, function () use ($provinceCode) {
            try {
                $nodeCommand = "node -e \"const { cities } = require('./node_modules/select-philippines-address'); cities('{$provinceCode}').then(data => console.log(JSON.stringify(data)));\"";
                $output = shell_exec($nodeCommand);
                $data = json_decode($output, true);
                
                if ($data && is_array($data)) {
                    return array_map(function ($city) {
                        return [
                            'code' => $city['city_code'] ?? '',
                            'name' => $city['city_name'] ?? '',
                            'province_code' => $city['province_code'] ?? '',
                        ];
                    }, $data);
                }
            } catch (\Exception $e) {
                Log::error('Failed to fetch cities', ['error' => $e->getMessage(), 'province_code' => $provinceCode]);
            }
            
            return [];
        });
    }

    /**
     * Get barangays by city code
     */
    public function getBarangaysByCity($cityCode)
    {
        return Cache::remember("philippine_address_barangays_{$cityCode}", $this->cacheTimeout, function () use ($cityCode) {
            try {
                $nodeCommand = "node -e \"const { barangays } = require('./node_modules/select-philippines-address'); barangays('{$cityCode}').then(data => console.log(JSON.stringify(data)));\"";
                $output = shell_exec($nodeCommand);
                $data = json_decode($output, true);
                
                if ($data && is_array($data)) {
                    return array_map(function ($barangay) {
                        return [
                            'code' => $barangay['barangay_code'] ?? '',
                            'name' => $barangay['barangay_name'] ?? '',
                            'city_code' => $barangay['city_code'] ?? '',
                        ];
                    }, $data);
                }
            } catch (\Exception $e) {
                Log::error('Failed to fetch barangays', ['error' => $e->getMessage(), 'city_code' => $cityCode]);
            }
            
            return [];
        });
    }
}

