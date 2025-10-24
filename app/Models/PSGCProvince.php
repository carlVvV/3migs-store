<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PSGCProvince extends Model
{
    use HasFactory;

    protected $table = 'psgc_provinces';
    
    protected $fillable = [
        'code',
        'name',
        'region_code',
        'region_name',
    ];

    public function region()
    {
        return $this->belongsTo(PSGCRegion::class, 'region_code', 'code');
    }

    public function cities()
    {
        return $this->hasMany(PSGCCity::class, 'province_code', 'code');
    }

    public function barangays()
    {
        return $this->hasManyThrough(PSGCBarangay::class, PSGCCity::class, 'province_code', 'city_code', 'code', 'code');
    }
}