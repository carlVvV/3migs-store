<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Services\PSGCService;
use Illuminate\Http\Request;

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
}
