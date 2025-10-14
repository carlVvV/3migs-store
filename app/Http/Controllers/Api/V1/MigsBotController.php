<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use App\Models\Product;
use App\Models\Category;
use App\Models\Order;
use App\Services\OpenAIService;

class MigsBotController extends Controller
{
    /**
     * Simple, lightweight AI-like assistant for customer guidance.
     * Strategy:
     * - Handle FAQs and structured intents locally
     * - Query products/categories from DB
     * - Fallback to web search (DuckDuckGo Instant Answer API)
     */
    public function chat(Request $request)
    {
        $validated = $request->validate([
            'message' => 'required|string|max:2000',
            'context' => 'array',
        ]);

        $user = $request->user();
        $message = trim($validated['message']);

        // Normalize
        $normalized = mb_strtolower($message);

        // 1) FAQs / quick intents
        $faqAnswer = $this->answerFaq($normalized);
        if ($faqAnswer) {
            return response()->json([
                'success' => true,
                'data' => [
                    'reply' => $faqAnswer,
                    'source' => 'faq',
                ],
            ]);
        }

        // 2) Product / category guidance
        $productsResult = $this->searchProducts($normalized);
        if ($productsResult !== null) {
            return response()->json([
                'success' => true,
                'data' => [
                    'reply' => $productsResult['reply'],
                    'products' => $productsResult['products'],
                    'source' => 'catalog',
                ],
            ]);
        }

        // 3) Order status (requires auth)
        $orderReply = $this->maybeCheckOrderStatus($normalized, $user);
        if ($orderReply) {
            return response()->json([
                'success' => true,
                'data' => [
                    'reply' => $orderReply,
                    'source' => 'orders',
                ],
            ]);
        }

        // 4) Fallback to OpenAI ChatGPT (enhanced)
        try {
            $openai = new OpenAIService();
            
            // Check if OpenAI is configured
            if (!$openai->isConfigured()) {
                throw new \Exception('OpenAI not configured');
            }

            // Build context for ChatGPT
            $context = $this->buildContextForChatGPT($user, $message);
            
            // Generate response using enhanced ChatGPT
            $reply = $openai->generateMigsBotResponse($message, $context);
            
            if ($reply) {
                return response()->json([
                    'success' => true,
                    'data' => [
                        'reply' => $reply,
                        'source' => 'chatgpt',
                    ],
                ]);
            }
        } catch (\Throwable $e) {
            \Log::warning('ChatGPT fallback failed: ' . $e->getMessage());
        }

        // 5) Last fallback web search
        $web = $this->duckDuckGo($message);
        if ($web) {
            return response()->json([
                'success' => true,
                'data' => [
                    'reply' => $web['text'],
                    'links' => $web['links'],
                    'source' => 'web',
                ],
            ]);
        }

        return response()->json([
            'success' => true,
            'data' => [
                'reply' => "I couldn't find an answer right now. Could you rephrase or provide more details?",
                'source' => 'none',
            ],
        ]);
    }

    private function answerFaq(string $q): ?string
    {
        $faqs = [
            'shipping' => 'We offer free shipping on orders over ₱2,000. Standard shipping usually takes 2-5 business days.',
            'delivery' => 'We offer free shipping on orders over ₱2,000. Standard shipping usually takes 2-5 business days.',
            'return' => 'You can return unused items within 7 days of delivery. Please keep tags attached.',
            'refund' => 'Refunds are processed within 3-5 business days after we receive the returned item.',
            'payment' => 'We accept major credit cards, PayPal, and cash on delivery for eligible locations.',
            'sizes' => 'Sizing varies by product. See the Size Guide on product pages or ask me about a specific item.',
            'contact' => 'You can reach us via the Support section in the footer or by replying here.',
            'store hours' => 'Our online store is open 24/7. Support typically responds within a few hours.',
        ];

        foreach ($faqs as $key => $answer) {
            if (str_contains($q, $key)) {
                return $answer;
            }
        }

        // Greeting / help
        if (preg_match('/\b(hi|hello|hey)\b/i', $q)) {
            return 'Hi! I am MigsBot. I can help you find barong and gowns, check simple order info, or answer questions about shipping, returns, and more.';
        }
        if (str_contains($q, 'help')) {
            return 'I can search products, filter by category, explain shipping/returns, and guide you to checkout. What do you need?';
        }

        return null;
    }

    /**
     * Build context for ChatGPT based on user and message
     */
    private function buildContextForChatGPT($user, string $message): array
    {
        $context = [
            'store_name' => '3Migs Gowns & Barong',
            'location' => 'Pandi, Bulacan',
            'current_time' => now()->format('Y-m-d H:i:s'),
        ];

        // Add user context if authenticated
        if ($user) {
            $context['user'] = [
                'name' => $user->name,
                'email' => $user->email,
                'is_authenticated' => true,
            ];

            // Add recent orders context
            $recentOrders = Order::where('user_id', $user->id)
                ->latest()
                ->limit(3)
                ->get(['order_number', 'status', 'total_amount', 'created_at']);

            if ($recentOrders->isNotEmpty()) {
                $context['recent_orders'] = $recentOrders->map(function ($order) {
                    return [
                        'order_number' => $order->order_number,
                        'status' => $order->status,
                        'total_amount' => $order->total_amount,
                        'date' => $order->created_at->format('Y-m-d'),
                    ];
                })->toArray();
            }
        } else {
            $context['user'] = [
                'is_authenticated' => false,
            ];
        }

        // Add product context if message mentions products
        if (preg_match('/barong|gown|wedding|formal|casual|traditional|modern/', strtolower($message))) {
            $context['available_categories'] = Category::pluck('name')->toArray();
            $context['available_fabrics'] = ['Jusilyn', 'Hugo Boss', 'Piña Cocoon', 'Gusot Mayaman'];
        }

        return $context;
    }

    private function searchProducts(string $q): ?array
    {
        // heuristics: look for category-like words
        $categoryTerms = ['barong', 'gown', 'wedding', 'men', 'women', 'kids'];
        $shouldSearch = false;
        foreach ($categoryTerms as $term) {
            if (str_contains($q, $term)) { $shouldSearch = true; break; }
        }
        if (!$shouldSearch && !preg_match('/find|show|look for|search|price|cost|available|stock/', $q)) {
            return null;
        }

        // Try product name match first
        $productsQuery = Product::with('category')
            ->active()
            ->inStock()
            ->where(function ($builder) use ($q) {
                $builder->where('name', 'like', "%{$q}%")
                    ->orWhere('description', 'like', "%{$q}%");
            })
            ->limit(5)
            ->get();

        if ($productsQuery->isEmpty()) {
            // Try category-based
            $category = Category::where('name', 'like', "%{$q}%")->first();
            if ($category) {
                $productsQuery = $category->products()->active()->inStock()->limit(5)->get();
            }
        }

        $products = $productsQuery->map(function ($p) {
            return [
                'id' => $p->id,
                'name' => $p->name,
                'slug' => $p->slug,
                'price' => $p->current_price,
                'category' => optional($p->category)->name,
                'image' => method_exists($p, 'getFirstImageUrl') ? $p->getFirstImageUrl() : null,
                'url' => route('product.details', ['slug' => $p->slug], false),
            ];
        })->values()->all();

        if (empty($products)) {
            return null;
        }

        $reply = 'Here are a few items you might like:';
        return [
            'reply' => $reply,
            'products' => $products,
        ];
    }

    private function maybeCheckOrderStatus(string $q, $user): ?string
    {
        if (!preg_match('/(order|tracking).*?(#|no\.?|number)?\s*(\w{6,})/i', $q, $m)) {
            return null;
        }
        $orderNumber = $m[3] ?? null;
        if (!$orderNumber) {
            return null;
        }

        if (!$user) {
            return 'Please log in to check your order status.';
        }

        $order = Order::where('user_id', $user->id)
            ->where('order_number', $orderNumber)
            ->first();

        if (!$order) {
            return 'I could not find that order under your account.';
        }

        return "Order {$order->order_number} is currently '{$order->status}' with total ₱" . number_format((float)$order->total_amount, 2);
    }

    private function duckDuckGo(string $query): ?array
    {
        try {
            $url = 'https://api.duckduckgo.com/?q=' . urlencode($query) . '&format=json&no_redirect=1&skip_disambig=1';
            $ctx = stream_context_create([
                'http' => [
                    'timeout' => 4,
                    'header' => "Accept: application/json\r\n",
                ],
            ]);
            $raw = @file_get_contents($url, false, $ctx);
            if (!$raw) { return null; }
            $json = json_decode($raw, true);
            if (!$json) { return null; }

            $text = $json['AbstractText'] ?? '';
            $links = [];
            if (empty($text) && !empty($json['RelatedTopics'])) {
                foreach ($json['RelatedTopics'] as $rt) {
                    if (isset($rt['Text'], $rt['FirstURL'])) {
                        $text = $rt['Text'];
                        $links[] = $rt['FirstURL'];
                        break;
                    }
                    if (isset($rt['Topics']) && is_array($rt['Topics'])) {
                        foreach ($rt['Topics'] as $sub) {
                            if (isset($sub['Text'], $sub['FirstURL'])) {
                                $text = $sub['Text'];
                                $links[] = $sub['FirstURL'];
                                break 2;
                            }
                        }
                    }
                }
            }

            if (empty($text)) {
                return null;
            }

            // Limit links to 3
            $links = array_slice($links, 0, 3);
            return [
                'text' => $text,
                'links' => $links,
            ];
        } catch (\Throwable $e) {
            Log::warning('MigsBot web search failed: ' . $e->getMessage());
            return null;
        }
    }
}


