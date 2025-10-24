<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PSGCBarangay extends Model
{
    use HasFactory;

    protected $table = 'psgc_barangays';
    
    protected $fillable = [
        'code',
        'name',
        'region_code',
        'region_name',
        'province_code',
        'province_name',
        'city_code',
        'city_name',
    ];

    public function region()
    {
        return $this->belongsTo(PSGCRegion::class, 'region_code', 'code');
    }

    public function province()
    {
        return $this->belongsTo(PSGCProvince::class, 'province_code', 'code');
    }

    public function city()
    {
        return $this->belongsTo(PSGCCity::class, 'city_code', 'code');
    }
}