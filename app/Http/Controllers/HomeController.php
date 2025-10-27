<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\BarongProduct;
use App\Models\Category;

class HomeController extends Controller
{
    /**
     * Display the homepage.
     */
    public function index()
    {
        // Get all available products
        $allProducts = BarongProduct::where('is_available', true)
            ->orderBy('created_at', 'desc')
            ->get();

        // Get featured barong products
        $featuredProducts = BarongProduct::where('is_featured', true)
            ->where('is_available', true)
            ->limit(8)
            ->get();

        // Get new arrivals
        $newArrivals = BarongProduct::where('is_new_arrival', true)
            ->where('is_available', true)
            ->orderBy('created_at', 'desc')
            ->limit(8)
            ->get();

        // Get best selling products for this month
        $bestSellingProducts = BarongProduct::where('is_available', true)
            ->orderBy('monthly_sales', 'desc')
            ->limit(8)
            ->get();

        // Get categories
        $categories = Category::where('is_active', true)
            ->orderBy('sort_order')
            ->withCount('barongProducts')
            ->get();

        return view('home', compact('allProducts', 'featuredProducts', 'newArrivals', 'bestSellingProducts', 'categories'));
    }

    /**
     * Display a test version of the homepage.
     */
    public function testHome()
    {
        // Get all available products (remove the limit)
        $allProducts = BarongProduct::with(['category'])
            ->where('is_available', true)
            ->orderBy('created_at', 'desc')
            ->get();

        // Get categories
        $categories = Category::active()
            ->ordered()
            ->withCount('barongProducts')
            ->get();

        return view('test-home', compact('allProducts', 'categories'));
    }

    /**
     * Display a simplified version of the homepage.
     */
    public function simpleHome()
    {
        // Get all available products (remove the limit)
        $allProducts = BarongProduct::with(['category'])
            ->where('is_available', true)
            ->orderBy('created_at', 'desc')
            ->get();

        // Get featured barong products (keep limit for featured section)
        $featuredProducts = BarongProduct::with(['category'])
            ->featured()
            ->limit(8)
            ->get();

        // Get categories
        $categories = Category::active()
            ->ordered()
            ->withCount('barongProducts')
            ->get();

        return view('simple-home', compact('allProducts', 'featuredProducts', 'categories'));
    }

    /**
     * Display a version of the homepage without any includes.
     */
    public function noIncludesHome()
    {
        // Get all available products (remove the limit)
        $allProducts = BarongProduct::with(['category'])
            ->where('is_available', true)
            ->orderBy('created_at', 'desc')
            ->get();

        // Get categories
        $categories = Category::active()
            ->ordered()
            ->withCount('barongProducts')
            ->get();

        return view('no-includes-home', compact('allProducts', 'categories'));
    }

    /**
     * Display a version of the homepage with only header include.
     */
    public function headerTestHome()
    {
        // Get all available products (remove the limit)
        $allProducts = BarongProduct::with(['category'])
            ->where('is_available', true)
            ->orderBy('created_at', 'desc')
            ->get();

        // Get categories
        $categories = Category::active()
            ->ordered()
            ->withCount('barongProducts')
            ->get();

        return view('header-test-home', compact('allProducts', 'categories'));
    }

    /**
     * Display a version of the homepage with header and categories sidebar.
     */
    public function categoriesTestHome()
    {
        // Get all available products (remove the limit)
        $allProducts = BarongProduct::with(['category'])
            ->where('is_available', true)
            ->orderBy('created_at', 'desc')
            ->get();

        // Get categories
        $categories = Category::active()
            ->ordered()
            ->withCount('barongProducts')
            ->get();

        return view('categories-test-home', compact('allProducts', 'categories'));
    }

    /**
     * Display a version of the homepage with header, categories, and notification system.
     */
    public function notificationTestHome()
    {
        // Get all available products (remove the limit)
        $allProducts = BarongProduct::with(['category'])
            ->where('is_available', true)
            ->orderBy('created_at', 'desc')
            ->get();

        // Get categories
        $categories = Category::active()
            ->ordered()
            ->withCount('barongProducts')
            ->get();

        return view('notification-test-home', compact('allProducts', 'categories'));
    }

    /**
     * Display a version of the homepage with all includes including MigsBot.
     */
    public function migsbotTestHome()
    {
        // Get all available products (remove the limit)
        $allProducts = BarongProduct::with(['category'])
            ->where('is_available', true)
            ->orderBy('created_at', 'desc')
            ->get();

        // Get categories
        $categories = Category::active()
            ->ordered()
            ->withCount('barongProducts')
            ->get();

        return view('migsbot-test-home', compact('allProducts', 'categories'));
    }

    /**
     * Display the cart page.
     */
    public function cart()
    {
        return view('cart');
    }

    /**
     * Display the checkout page.
     */
    public function checkout()
    {
        return view('checkout');
    }

    /**
     * Display the custom design page.
     */
    public function customDesign()
    {
        return view('custom-design');
    }

    /**
     * Display product details page.
     */
    public function productDetails($slug)
    {
        // Cache the product for 1 hour to reduce database calls
        $product = cache()->remember("product.{$slug}", 3600, function () use ($slug) {
            return BarongProduct::with(['category'])
                ->where('slug', $slug)
                ->available()
                ->first();
        });

        if (!$product) {
            abort(404);
        }

        // Cache related products for 30 minutes
        $relatedProducts = cache()->remember("product.{$slug}.related", 1800, function () use ($product) {
            return BarongProduct::with(['category'])
                ->where('category_id', $product->category_id)
                ->where('id', '!=', $product->id)
                ->available()
                ->limit(4)
                ->get();
        });

        return view('product-view', compact('product', 'relatedProducts'));
    }

    /**
     * Display the wishlist page.
     */
    public function wishlist()
    {
        return view('wishlist');
    }

    /**
     * Display the profile page.
     */
    public function profile()
    {
        return view('profile');
    }

    /**
     * Display the orders page.
     */
    public function orders()
    {
        // Get regular orders
        $regularOrders = auth()->user()->orders()->with(['orderItems.product'])->latest()->get();
        
        // Get custom design orders
        $customOrders = \App\Models\CustomDesignOrder::where('user_id', auth()->id())
            ->latest()
            ->get();
        
        // Combine and sort orders by creation date
        $allOrders = $regularOrders->concat($customOrders)->sortByDesc('created_at');
        
        return view('orders', compact('allOrders'));
    }

    /**
     * Display order details page.
     */
    public function orderDetails($id)
    {
        $order = auth()->user()->orders()->with(['orderItems.product'])->findOrFail($id);
        return view('order-details', compact('order'));
    }

    /**
     * Display category page with products.
     */
    public function category($slug)
    {
        $category = Category::where('slug', $slug)->where('is_active', true)->firstOrFail();
        
        $query = BarongProduct::where('category_id', $category->id)->available();
        
        // Apply filters using computed current price: COALESCE(sale_price, price)
        $min = request('min_price');
        $max = request('max_price');
        
        // Apply price range based on which bounds are present
        $minVal = ($min !== null && $min !== '' && is_numeric($min)) ? (float) $min : null;
        $maxVal = ($max !== null && $max !== '' && is_numeric($max)) ? (float) $max : null;
        
        if (!is_null($minVal) && !is_null($maxVal)) {
            // Both bounds provided
            if ($minVal > $maxVal) {
                [$minVal, $maxVal] = [$maxVal, $minVal];
            }
            $query->where(function($q) use ($minVal, $maxVal) {
                $q->where(function($subQ) use ($minVal, $maxVal) {
                    $subQ->whereNotNull('special_price')
                         ->whereBetween('special_price', [$minVal, $maxVal]);
                })->orWhere(function($subQ) use ($minVal, $maxVal) {
                    $subQ->whereNull('special_price')
                         ->whereBetween('base_price', [$minVal, $maxVal]);
                });
            });
        } elseif (!is_null($minVal)) {
            // Only min provided
            $query->where(function($q) use ($minVal) {
                $q->where(function($subQ) use ($minVal) {
                    $subQ->whereNotNull('special_price')
                         ->where('special_price', '>=', $minVal);
                })->orWhere(function($subQ) use ($minVal) {
                    $subQ->whereNull('special_price')
                         ->where('base_price', '>=', $minVal);
                });
            });
        } elseif (!is_null($maxVal)) {
            // Only max provided
            $query->where(function($q) use ($maxVal) {
                $q->where(function($subQ) use ($maxVal) {
                    $subQ->whereNotNull('special_price')
                         ->where('special_price', '<=', $maxVal);
                })->orWhere(function($subQ) use ($maxVal) {
                    $subQ->whereNull('special_price')
                         ->where('base_price', '<=', $maxVal);
                });
            });
        }
        
        // Apply sorting - prioritize price filtering over custom sorting
        $hasPriceFilter = (!is_null($minVal) || !is_null($maxVal));
        
        if (!$hasPriceFilter && request()->has('sort')) {
            // Only apply custom sorting when no price filter is active
            switch (request()->sort) {
                case 'price_low':
                    $query->orderByRaw('COALESCE(special_price, base_price) ASC');
                    break;
                case 'price_high':
                    $query->orderByRaw('COALESCE(special_price, base_price) DESC');
                    break;
                case 'newest':
                    $query->orderBy('created_at', 'desc');
                    break;
                case 'popular':
                    $query->orderBy('sales_count', 'desc');
                    break;
                default:
                    $query->orderBy('name', 'asc');
            }
        } else {
            // Default sorting when price filtering is active or no sort specified
            $query->orderBy('name', 'asc');
        }
        
        $barongProducts = $query->paginate(12)->withQueryString();
        $allCategories = Category::active()->ordered()->get();
        
        return view('category', compact('category', 'barongProducts', 'allCategories'));
    }
}
