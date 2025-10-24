<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PSGCRegion extends Model
{
    use HasFactory;

    protected $table = 'psgc_regions';
    
    protected $fillable = [
        'code',
        'name',
    ];

    public function provinces()
    {
        return $this->hasMany(PSGCProvince::class, 'region_code', 'code');
    }

    public function cities()
    {
        return $this->hasManyThrough(PSGCCity::class, PSGCProvince::class, 'region_code', 'province_code', 'code', 'code');
    }

    public function barangays()
    {
        return $this->hasManyThrough(PSGCBarangay::class, PSGCCity::class, 'region_code', 'city_code', 'code', 'code');
    }
}