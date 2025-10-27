<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Category;
use App\Models\Order;
use App\Models\User;
use App\Models\Review;
use App\Models\BarongProduct;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use App\Http\Requests\StoreBarongProductRequest;
use App\Services\CloudinaryService;

class AdminController extends Controller
{
    protected $cloudinaryService;
    
    public function __construct(CloudinaryService $cloudinaryService)
    {
        $this->cloudinaryService = $cloudinaryService;
    }
    
    /**
     * Show admin dashboard
     */
    public function dashboard()
    {
        $stats = [
            'total_products' => BarongProduct::count(),
            'active_products' => BarongProduct::where('is_available', true)->count(),
            'total_categories' => Category::count(),
            'total_orders' => Order::count(),
            'pending_orders' => Order::where('status', 'pending')->count(),
            'completed_orders' => Order::where('status', 'completed')->count(),
            'total_users' => User::count(),
            'total_reviews' => Review::count(),

        ];
