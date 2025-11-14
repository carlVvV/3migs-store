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
                        $code = $city['city_code'] ?? '';
                        $zip = $city['zip_code'] ?? $city['zipCode'] ?? null;

                        // Try to get zip code from database if not provided by Node.js package
                        if (!$zip && $code) {
                            // Try exact match first
                            $zip = \App\Models\PSGCCity::where('code', $code)->value('zip_code');
                            
                            // If still no zip, try with different code formats
                            if (!$zip) {
                                // Try with 9-digit code (remove last digit if 10 digits)
                                if (strlen($code) === 10) {
                                    $code9 = substr($code, 0, 9);
                                    $zip = \App\Models\PSGCCity::where('code', $code9)->value('zip_code');
                                }
                                // Try with 9-digit code padded to 10
                                if (!$zip && strlen($code) === 9) {
                                    $code10 = $code . '0';
                                    $zip = \App\Models\PSGCCity::where('code', $code10)->value('zip_code');
                                }
                            }
                            
                        }

                        // If still no zip code, attempt to match by city name and province
                        if (!$zip && !empty($city['city_name'])) {
                            $query = \App\Models\PSGCCity::query()
                                ->whereRaw('LOWER(name) = ?', [strtolower($city['city_name'])]);

                            if (!empty($city['province_code'])) {
                                $query->whereRaw('LEFT(province_code, ?) = ?', [strlen($city['province_code']), $city['province_code']]);
                            }

                            $dbCity = $query->first();

                            if (!$dbCity && !empty($city['province_code'])) {
                                $dbCity = \App\Models\PSGCCity::where('province_code', 'like', $city['province_code'] . '%')
                                    ->where('name', 'like', $city['city_name'] . '%')
                                    ->first();
                            }

                            if ($dbCity) {
                                $zip = $dbCity->zip_code;
                            }
                        }

                        if (!$zip) {
                            Log::warning('Zip code still missing for city', [
                                'city_name' => $city['city_name'] ?? null,
                                'city_code' => $code,
                                'province_code' => $city['province_code'] ?? null
                            ]);
                        }

                        return [
                            'code' => $code,
                            'name' => $city['city_name'] ?? '',
                            'province_code' => $city['province_code'] ?? '',
                            'zipCode' => $zip,
                            'zip_code' => $zip,
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

