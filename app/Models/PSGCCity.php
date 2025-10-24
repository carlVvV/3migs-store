<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PSGCCity extends Model
{
    use HasFactory;

    protected $table = 'psgc_cities';
    
    protected $fillable = [
        'code',
        'name',
        'type',
        'district',
        'zip_code',
        'region_code',
        'region_name',
        'province_code',
        'province_name',
    ];

    public function region()
    {
        return $this->belongsTo(PSGCRegion::class, 'region_code', 'code');
    }

    public function province()
    {
        return $this->belongsTo(PSGCProvince::class, 'province_code', 'code');
    }

    public function barangays()
    {
        return $this->hasMany(PSGCBarangay::class, 'city_code', 'code');
    }
}