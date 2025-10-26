<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Services\PSGCService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class PSGCController extends Controller
{
    protected $psgcService;

    public function __construct(PSGCService $psgcService)
    {
        $this->psgcService = $psgcService;
    }

    /**
     * Get all regions
     */
    public function getRegions()
    {
        try {
            $regions = $this->psgcService->getRegions();
            return response()->json([
                'success' => true,
                'data' => $regions
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch regions',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get provinces by region
     */
    public function getProvincesByRegion(Request $request, $regionCode)
    {
        try {
            $provinces = $this->psgcService->getProvincesByRegion($regionCode);
            return response()->json([
                'success' => true,
                'data' => $provinces
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch provinces',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get cities by province
     */
    public function getCitiesByProvince(Request $request, $provinceCode)
    {
        try {
            $cities = $this->psgcService->getCitiesByProvince($provinceCode);
            return response()->json([
                'success' => true,
                'data' => $cities
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch cities',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get barangays by city
     */
    public function getBarangaysByCity(Request $request, $cityCode)
    {
        try {
            $barangays = $this->psgcService->getBarangaysByCity($cityCode);
            return response()->json([
                'success' => true,
                'data' => $barangays
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch barangays',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get cities/municipalities by region code (for NCR)
     */
    public function getCitiesByRegion($regionCode)
    {
        try {
            $cities = $this->psgcService->getCitiesByRegion($regionCode);
            return response()->json([
                'success' => true,
                'data' => $cities
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch cities: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Search for city and get parent data (reverse lookup)
     */
    public function searchCity(Request $request)
    {
        $request->validate([
            'name' => 'required|string|min:2'
        ]);

        try {
            $cityName = $request->input('name');
            $result = $this->psgcService->findCityWithParents($cityName);
            
            if ($result) {
                return response()->json([
                    'success' => true,
                    'data' => $result
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'City not found'
                ], 404);
            }
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to search city',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Search for barangay and get parent data (reverse lookup)
     */
    public function searchBarangay(Request $request)
    {
        $request->validate([
            'name' => 'required|string|min:2',
            'city' => 'nullable|string'
        ]);

        try {
            $barangayName = $request->input('name');
            $cityName = $request->input('city');
            $result = $this->psgcService->findBarangayWithParents($barangayName, $cityName);
            
            if ($result) {
                return response()->json([
                    'success' => true,
                    'data' => $result
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Barangay not found'
                ], 404);
            }
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to search barangay',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Test endpoint to check if POST requests work
     */
    public function testPost(Request $request)
    {
        return response()->json([
            'success' => true,
            'message' => 'POST request successful',
            'data' => $request->all()
        ]);
    }

    /**
     * Test endpoint to check PSGC service
     */
    public function testService()
    {
        try {
            $psgcService = app(\App\Services\PSGCService::class);
            $regions = $psgcService->getRegions();
            
            return response()->json([
                'success' => true,
                'message' => 'PSGC service is working',
                'regions_count' => count($regions),
                'sample_region' => $regions[0] ?? null
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'PSGC service error: ' . $e->getMessage(),
                'error' => $e->getTraceAsString()
            ], 500);
        }
    }

    /**
     * Debug endpoint to check Manila data
     */
    public function debugManila()
    {
        $manilaCities = \App\Models\PSGCCity::where('name', 'LIKE', '%Manila%')->get();
        
        return response()->json([
            'success' => true,
            'manila_cities' => $manilaCities->map(function ($city) {
                return [
                    'code' => $city->code,
                    'name' => $city->name,
                    'region_code' => $city->region_code,
                    'region_name' => $city->region_name,
                    'province_code' => $city->province_code,
                    'province_name' => $city->province_name,
                ];
            })
        ]);
    }

    /**
     * Get single city by code
     */
    public function getCity($code)
    {
        try {
            // Try database first
            $city = \App\Models\PSGCCity::where('code', $code)->first();
            
            if ($city) {
                return response()->json([
                    'success' => true,
                    'data' => [
                        'code' => $city->code,
                        'name' => $city->name,
                        'region_code' => $city->region_code,
                        'region_name' => $city->region_name,
                        'province_code' => $city->province_code,
                        'province_name' => $city->province_name,
                    ]
                ]);
            }
            
            // If not in database, fetch from API
            // Extract province code from city code
            // PSGC city code: 031411000 (Malolos)
            // Province code: 031400000 (Bulacan) - 9 digits total
            // The pattern is: take first 4 digits (0314), add "00" to make 6 digits (031400), then add "0000" to make it 10 digits? No.
            // Actually looking at the code: 031411000
            // Province is: 031400000
            // So we take positions 0-4 (0314), add "00", then "000" to get 031400000 (9 digits total)
            $provinceCode = substr($code, 0, 4) . '00' . '000'; // 031411000 → 031400000
            
            // Fetch cities from the province
            $cities = $this->psgcService->getCitiesByProvince($provinceCode);
            
            \Log::info('Looking for city', [
                'code' => $code,
                'provinceCode' => $provinceCode,
                'citiesCount' => count($cities),
                'sampleCity' => $cities[0] ?? null
            ]);
            
            if ($cities) {
                foreach ($cities as $cityData) {
                    if ($cityData['code'] === $code) {
                        return response()->json([
                            'success' => true,
                            'data' => [
                                'code' => $cityData['code'],
                                'name' => $cityData['name'],
                                'region_code' => null,
                                'region_name' => null,
                                'province_code' => $provinceCode,
                                'province_name' => null,
                            ]
                        ]);
                    }
                }
            }
            
            return response()->json([
                'success' => false,
                'message' => 'City not found',
                'debug' => [
                    'code' => $code,
                    'provinceCode' => $provinceCode,
                    'citiesCount' => count($cities)
                ]
            ], 404);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch city',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get single barangay by code
     */
    public function getBarangay($code)
    {
        try {
            // Try database first
            $barangay = \App\Models\PSGCBarangay::where('code', $code)->first();
            
            if ($barangay) {
                return response()->json([
                    'success' => true,
                    'data' => [
                        'code' => $barangay->code,
                        'name' => $barangay->name,
                        'city_code' => $barangay->city_code,
                        'city_name' => $barangay->city_name,
                    ]
                ]);
            }
            
            // If not in database, fetch from API
            // Extract city code from barangay code
            // Barangay code: 031411002 (9 digits)
            // City code should be: 031411000 (9 digits)
            // The pattern is that barangay codes end with the last 3 digits as the barangay number
            // So for 031411002, the city is 031411000
            // We replace the last 3 digits with '000'
            $cityCode = substr($code, 0, 6) . '000'; // Take first 6 digits and add '000' to get city code
            
            // Fetch barangays from the city
            $barangays = $this->psgcService->getBarangaysByCity($cityCode);
            
            \Log::info('Looking for barangay', [
                'code' => $code,
                'cityCode' => $cityCode,
                'barangaysCount' => count($barangays),
                'sampleBarangay' => $barangays[0] ?? null
            ]);
            
            if ($barangays) {
                foreach ($barangays as $barangayData) {
                    if ($barangayData['code'] === $code) {
                        return response()->json([
                            'success' => true,
                            'data' => [
                                'code' => $barangayData['code'],
                                'name' => $barangayData['name'],
                                'city_code' => $cityCode,
                                'city_name' => null,
                            ]
                        ]);
                    }
                }
            }
            
            return response()->json([
                'success' => false,
                'message' => 'Barangay not found',
                'debug' => [
                    'code' => $code,
                    'cityCode' => $cityCode,
                    'barangaysCount' => count($barangays)
                ]
            ], 404);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch barangay',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
