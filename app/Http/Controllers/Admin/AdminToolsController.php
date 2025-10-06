<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Tool;
use App\Services\ImageCompressionService;
use App\Services\SimpleImageCompressionService;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Storage;

class AdminToolsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): View
    {
        $tools = Tool::ordered()->paginate(15);
        return view('admin.tools.index', compact('tools'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        $categories = [
            'أجهزة الخبز',
            'أدوات القياس',
            'قوالب الحلويات',
            'أدوات الخلط',
            'أدوات التزيين',
            'أدوات أخرى'
        ];
        
        return view('admin.tools.create', compact('categories'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        try {
            $request->validate([
                'name' => 'required|string|max:255',
                'description' => 'required|string',
                'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
                'amazon_url' => 'nullable|url',
                'affiliate_url' => 'nullable|url',
                'price' => 'required|numeric|min:0',
                'category' => 'required|string|max:255',
                'rating' => 'nullable|numeric|min:0|max:5',
                'features' => 'nullable|array',
                'features.*' => 'nullable|string|max:255',
                'is_active' => 'boolean',
                'sort_order' => 'nullable|integer|min:0'
            ]);

            $data = $request->all();

            // Handle image upload with compression
            if ($request->hasFile('image')) {
                // محاولة استخدام ضغط الصور المتقدم أولاً
                if (extension_loaded('gd')) {
                    $data['image'] = ImageCompressionService::compressAndStore(
                        $request->file('image'),
                        'tools',
                        80, // جودة 80%
                        1200, // أقصى عرض
                        1200  // أقصى ارتفاع
                    );
                } else {
                    // استخدام الحفظ المباشر إذا لم يكن GD متوفراً
                    $data['image'] = SimpleImageCompressionService::compressAndStore(
                        $request->file('image'),
                        'tools',
                        80
                    );
                }
            } elseif (!empty($data['extracted_image_url'])) {
                // Download and save extracted image with compression
                $data['image'] = $this->downloadAndSaveImage($data['extracted_image_url']);
            }

            // Clean and validate URLs
            if (!empty($data['amazon_url'])) {
                $data['amazon_url'] = trim($data['amazon_url']);
            }
            if (!empty($data['affiliate_url'])) {
                $data['affiliate_url'] = trim($data['affiliate_url']);
            }

            // Convert features array to JSON
            if ($request->has('features')) {
                $features = $request->input('features', []);
                // Filter out empty values and ensure all are strings
                $data['features'] = array_filter(array_map('trim', $features), function($feature) {
                    return !empty($feature) && is_string($feature);
                });
            } else {
                $data['features'] = [];
            }

            // Set default values
            $data['is_active'] = $request->boolean('is_active');
            $data['sort_order'] = $request->sort_order ?? 0;
            $data['rating'] = $request->rating ?? 0;

            // Debug: Log the data being saved
            \Log::info('Creating tool with data:', $data);

            $tool = Tool::create($data);

            \Log::info('Tool created successfully with ID:', ['id' => $tool->id]);

            return redirect()->route('admin.tools.index')
                ->with('success', 'تم إضافة الأداة بنجاح!');

        } catch (\Illuminate\Validation\ValidationException $e) {
            \Log::error('Validation error:', $e->errors());
            return redirect()->back()
                ->withErrors($e->errors())
                ->withInput();
        } catch (\Exception $e) {
            \Log::error('Error creating tool:', ['error' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
            return redirect()->back()
                ->with('error', 'حدث خطأ أثناء حفظ الأداة: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Tool $tool): View
    {
        return view('admin.tools.show', compact('tool'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Tool $tool): View
    {
        $categories = [
            'أجهزة الخبز',
            'أدوات القياس',
            'قوالب الحلويات',
            'أدوات الخلط',
            'أدوات التزيين',
            'أدوات أخرى'
        ];
        
        return view('admin.tools.edit', compact('tool', 'categories'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Tool $tool): RedirectResponse
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'amazon_url' => 'nullable|url',
            'affiliate_url' => 'nullable|url',
            'price' => 'required|numeric|min:0',
            'category' => 'required|string|max:255',
            'rating' => 'nullable|numeric|min:0|max:5',
            'features' => 'nullable|array',
            'features.*' => 'nullable|string|max:255',
            'is_active' => 'boolean',
            'sort_order' => 'nullable|integer|min:0'
        ]);

        $data = $request->all();

        // Handle image upload
        if ($request->hasFile('image')) {
            // Delete old image
            if ($tool->image && Storage::disk('public')->exists($tool->image)) {
                if (extension_loaded('gd')) {
                    ImageCompressionService::deleteCompressedImage($tool->image);
                } else {
                    SimpleImageCompressionService::deleteImage($tool->image);
                }
            }
            
            // محاولة استخدام ضغط الصور المتقدم أولاً
            if (extension_loaded('gd')) {
                $data['image'] = ImageCompressionService::compressAndStore(
                    $request->file('image'),
                    'tools',
                    80, // جودة 80%
                    1200, // أقصى عرض
                    1200  // أقصى ارتفاع
                );
            } else {
                // استخدام الحفظ المباشر إذا لم يكن GD متوفراً
                $data['image'] = SimpleImageCompressionService::compressAndStore(
                    $request->file('image'),
                    'tools',
                    80
                );
            }
        } elseif (!empty($data['extracted_image_url'])) {
            // Delete old image
            if ($tool->image && Storage::disk('public')->exists($tool->image)) {
                Storage::disk('public')->delete($tool->image);
            }
            // Download and save extracted image
            $data['image'] = $this->downloadAndSaveImage($data['extracted_image_url']);
        }

        // Clean and validate URLs
        if (!empty($data['amazon_url'])) {
            $data['amazon_url'] = trim($data['amazon_url']);
        }
        if (!empty($data['affiliate_url'])) {
            $data['affiliate_url'] = trim($data['affiliate_url']);
        }

        // Convert features array to JSON
        if ($request->has('features')) {
            $features = $request->input('features', []);
            // Filter out empty values and ensure all are strings
            $data['features'] = array_filter(array_map('trim', $features), function($feature) {
                return !empty($feature) && is_string($feature);
            });
        } else {
            $data['features'] = [];
        }

        // Set default values
        $data['is_active'] = $request->has('is_active');
        $data['sort_order'] = $request->sort_order ?? 0;
        $data['rating'] = $request->rating ?? 0;

        $tool->update($data);

        return redirect()->route('admin.tools.index')
            ->with('success', 'تم تحديث الأداة بنجاح!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Tool $tool): RedirectResponse
    {
        // Delete image if exists
        if ($tool->image && Storage::disk('public')->exists($tool->image)) {
            Storage::disk('public')->delete($tool->image);
        }

        $tool->delete();

        return redirect()->route('admin.tools.index')
            ->with('success', 'تم حذف الأداة بنجاح!');
    }

    /**
     * Toggle tool active status
     */
    public function toggle(Tool $tool): RedirectResponse
    {
        $tool->update(['is_active' => !$tool->is_active]);
        
        $status = $tool->is_active ? 'تفعيل' : 'إلغاء تفعيل';
        return redirect()->back()
            ->with('success', "تم {$status} الأداة بنجاح!");
    }

    /**
     * Extract data from Amazon URL
     */
    public function extractAmazonData(Request $request)
    {
        $request->validate([
            'url' => 'required|url'
        ]);

        $url = $request->url;
        
        try {
            // Handle shortened URLs first
            if (strpos($url, 'amzn.to') !== false || strpos($url, 'bit.ly') !== false) {
                $url = $this->resolveShortUrl($url);
                if (!$url) {
                    return response()->json([
                        'success' => false,
                        'message' => 'فشل في حل الرابط المختصر'
                    ]);
                }
            }
            
            // Use a simple web scraping approach
            $html = $this->fetchAmazonPage($url);
            
            if (!$html) {
                return response()->json([
                    'success' => false,
                    'message' => 'فشل في تحميل صفحة Amazon'
                ]);
            }

            $data = $this->parseAmazonData($html);
            
            return response()->json([
                'success' => true,
                'data' => $data
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ أثناء استخراج البيانات: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Resolve shortened URLs to get the final Amazon URL
     */
    private function resolveShortUrl($shortUrl)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $shortUrl);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, false); // Don't follow redirects automatically
        curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.124 Safari/537.36');
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_HEADER, true);
        curl_setopt($ch, CURLOPT_NOBODY, true);
        
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        
        if ($httpCode >= 300 && $httpCode < 400) {
            // Extract location header
            if (preg_match('/location:\s*(.+)/i', $response, $matches)) {
                $redirectUrl = trim($matches[1]);
                // If it's still a shortened URL, resolve it again
                if (strpos($redirectUrl, 'amzn.to') !== false || strpos($redirectUrl, 'bit.ly') !== false) {
                    return $this->resolveShortUrl($redirectUrl);
                }
                return $redirectUrl;
            }
        }
        
        return false;
    }

    /**
     * Fetch Amazon page content
     */
    private function fetchAmazonPage($url)
    {
        // Use cURL to fetch the page with better headers
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36');
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_ENCODING, ''); // Handle gzip
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,*/*;q=0.8',
            'Accept-Language: en-US,en;q=0.5',
            'Accept-Encoding: gzip, deflate',
            'Connection: keep-alive',
            'Upgrade-Insecure-Requests: 1',
        ]);
        
        $html = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $finalUrl = curl_getinfo($ch, CURLINFO_EFFECTIVE_URL);
        curl_close($ch);
        
        if ($httpCode !== 200 || !$html) {
            return false;
        }
        
        // Handle gzip decompression
        if (strpos($html, "\x1f\x8b") === 0) {
            $html = gzdecode($html);
        }
        
        // Check if we got a valid HTML response
        if (strlen($html) < 1000) {
            return false;
        }
        
        // Check for common Amazon error pages or captcha
        if (strpos($html, 'Robot Check') !== false || 
            strpos($html, 'captcha') !== false || 
            strpos($html, 'Access Denied') !== false ||
            strpos($html, 'Sorry, we just need to verify') !== false ||
            strpos($html, 'Enter the characters you see below') !== false) {
            return false;
        }
        
        // Check if it's actually an Amazon product page
        if (strpos($html, 'productTitle') === false && 
            strpos($html, 'a-price') === false && 
            strpos($html, 'amazon') === false) {
            return false;
        }
        
        return $html;
    }

    /**
     * Parse Amazon data from HTML
     */
    private function parseAmazonData($html)
    {
        $data = [];
        
        // Extract title - multiple patterns
        if (preg_match('/<span[^>]*id="productTitle"[^>]*>(.*?)<\/span>/s', $html, $matches)) {
            $data['name'] = trim(strip_tags($matches[1]));
        } elseif (preg_match('/<h1[^>]*class="a-size-large[^"]*"[^>]*>(.*?)<\/h1>/s', $html, $matches)) {
            $data['name'] = trim(strip_tags($matches[1]));
        } elseif (preg_match('/<title[^>]*>(.*?)<\/title>/s', $html, $matches)) {
            $title = trim(strip_tags($matches[1]));
            // Clean up Amazon title format
            $title = preg_replace('/\s*-\s*Amazon\.com.*$/', '', $title);
            $data['name'] = $title;
        }
        
        // Extract price - multiple patterns
        $price = null;
        
        // Pattern 1: a-price-whole (most common)
        if (preg_match('/<span[^>]*class="a-price-whole"[^>]*>([^<]+)<\/span>/', $html, $matches)) {
            $price = preg_replace('/[^0-9.]/', '', $matches[1]);
        }
        // Pattern 2: a-offscreen (screen reader price)
        elseif (preg_match('/<span[^>]*class="a-offscreen"[^>]*>\$([0-9.,]+)<\/span>/', $html, $matches)) {
            $price = str_replace(',', '', $matches[1]);
        }
        // Pattern 3: a-price-range
        elseif (preg_match('/<span[^>]*class="a-price-range"[^>]*>\$([0-9.,]+)[^<]*<\/span>/', $html, $matches)) {
            $price = str_replace(',', '', $matches[1]);
        }
        // Pattern 4: priceToPay
        elseif (preg_match('/<span[^>]*id="priceToPay"[^>]*>.*?\$([0-9.,]+).*?<\/span>/s', $html, $matches)) {
            $price = str_replace(',', '', $matches[1]);
        }
        // Pattern 5: a-price
        elseif (preg_match('/<span[^>]*class="a-price"[^>]*>.*?\$([0-9.,]+).*?<\/span>/s', $html, $matches)) {
            $price = str_replace(',', '', $matches[1]);
        }
        // Pattern 6: a-price-symbol
        elseif (preg_match('/<span[^>]*class="a-price-symbol"[^>]*>\$<\/span><span[^>]*class="a-price-whole"[^>]*>([^<]+)<\/span>/', $html, $matches)) {
            $price = preg_replace('/[^0-9.]/', '', $matches[1]);
        }
        // Pattern 7: Deal price
        elseif (preg_match('/<span[^>]*class="a-price-deal"[^>]*>.*?\$([0-9.,]+).*?<\/span>/s', $html, $matches)) {
            $price = str_replace(',', '', $matches[1]);
        }
        // Pattern 8: Price in data attributes
        elseif (preg_match('/data-price="([0-9.,]+)"/', $html, $matches)) {
            $price = str_replace(',', '', $matches[1]);
        }
        // Pattern 9: Price in JSON-LD structured data
        elseif (preg_match('/"price":\s*"([0-9.,]+)"/', $html, $matches)) {
            $price = str_replace(',', '', $matches[1]);
        }
        // Pattern 10: Generic price pattern with more flexibility
        elseif (preg_match('/\$([0-9]{1,3}(?:,[0-9]{3})*(?:\.[0-9]{2})?)/', $html, $matches)) {
            $price = str_replace(',', '', $matches[1]);
        }
        // Pattern 11: Price in AED (for Amazon.ae)
        elseif (preg_match('/AED\s*([0-9.,]+)/', $html, $matches)) {
            $price = str_replace(',', '', $matches[1]);
        }
        // Pattern 12: Price in AED with different format
        elseif (preg_match('/<span[^>]*class="a-price"[^>]*>.*?AED\s*([0-9.,]+).*?<\/span>/s', $html, $matches)) {
            $price = str_replace(',', '', $matches[1]);
        }
        // Pattern 13: Price in AED with symbol
        elseif (preg_match('/<span[^>]*class="a-price-symbol"[^>]*>AED<\/span><span[^>]*class="a-price-whole"[^>]*>([^<]+)<\/span>/', $html, $matches)) {
            $price = preg_replace('/[^0-9.]/', '', $matches[1]);
        }
        // Pattern 14: AED price in JSON data
        elseif (preg_match('/"priceAmount":([0-9.,]+)/', $html, $matches)) {
            $price = str_replace(',', '', $matches[1]);
        }
        // Pattern 15: AED price in displayPrice
        elseif (preg_match('/"displayPrice":"AED\s*([0-9.,]+)"/', $html, $matches)) {
            $price = str_replace(',', '', $matches[1]);
        }
        // Pattern 16: AED price in specific Amazon format
        elseif (preg_match('/AED\s*([0-9]{1,3}(?:,[0-9]{3})*(?:\.[0-9]{2})?)/', $html, $matches)) {
            $price = str_replace(',', '', $matches[1]);
        }
        // Pattern 17: Price in different currency formats
        elseif (preg_match('/<span[^>]*class="a-price"[^>]*>.*?([0-9.,]+).*?<\/span>/s', $html, $matches)) {
            $price = str_replace(',', '', $matches[1]);
        }
        // Pattern 18: AED in a-offscreen
        elseif (preg_match('/<span[^>]*class="a-offscreen"[^>]*>AED\s*([0-9.,]+)<\/span>/', $html, $matches)) {
            $price = str_replace(',', '', $matches[1]);
        }
        // Pattern 19: AED in aok-offscreen
        elseif (preg_match('/<span[^>]*class="aok-offscreen"[^>]*>AED\s*([0-9.,]+)<\/span>/', $html, $matches)) {
            $price = str_replace(',', '', $matches[1]);
        }
        // Pattern 20: AED in displayString
        elseif (preg_match('/"displayString"[^>]*value="AED\s*([0-9.,]+)"/', $html, $matches)) {
            $price = str_replace(',', '', $matches[1]);
        }
        // Pattern 21: AED in JSON data
        elseif (preg_match('/"displayPrice":"AED\s*([0-9.,]+)"/', $html, $matches)) {
            $price = str_replace(',', '', $matches[1]);
        }
        // Pattern 22: AED in hidden input
        elseif (preg_match('/name="[^"]*price[^"]*"[^>]*value="([0-9.,]+)"/', $html, $matches)) {
            $price = str_replace(',', '', $matches[1]);
        }
        
        if ($price) {
            $priceValue = floatval($price);
            
            // Check if it's already in AED (from Amazon.ae)
            if (strpos($html, 'AED') !== false || strpos($html, 'درهم') !== false) {
                $data['price'] = $priceValue;
                $data['original_price_aed'] = $priceValue;
            } else {
                // Convert USD to AED (1 USD = 3.67 AED approximately)
                $data['price'] = round($priceValue * 3.67, 2);
                $data['original_price_usd'] = $priceValue; // Keep original USD price for reference
            }
        }
        
        // Extract rating - multiple patterns
        if (preg_match('/<span[^>]*class="a-icon-alt"[^>]*>([0-9.]+) out of 5 stars<\/span>/', $html, $matches)) {
            $data['rating'] = floatval($matches[1]);
        } elseif (preg_match('/<span[^>]*class="a-icon-alt"[^>]*>([0-9.]+) من 5 نجوم<\/span>/', $html, $matches)) {
            $data['rating'] = floatval($matches[1]);
        } elseif (preg_match('/<span[^>]*class="a-icon-alt"[^>]*>([0-9.]+) de 5 estrellas<\/span>/', $html, $matches)) {
            $data['rating'] = floatval($matches[1]);
        } elseif (preg_match('/data-rating="([0-9.]+)"/', $html, $matches)) {
            $data['rating'] = floatval($matches[1]);
        } elseif (preg_match('/"ratingValue":\s*"([0-9.]+)"/', $html, $matches)) {
            $data['rating'] = floatval($matches[1]);
        }
        
        // Extract description (from bullet points)
        $description = '';
        if (preg_match_all('/<span[^>]*class="a-list-item"[^>]*>(.*?)<\/span>/s', $html, $matches)) {
            $bulletPoints = array_slice($matches[1], 0, 3); // Take first 3 bullet points
            $description = implode(' ', array_map('strip_tags', $bulletPoints));
        }
        
        if (empty($description) && preg_match('/<div[^>]*id="feature-bullets"[^>]*>(.*?)<\/div>/s', $html, $matches)) {
            $description = strip_tags($matches[1]);
            $description = preg_replace('/\s+/', ' ', $description);
        }
        
        $data['description'] = trim($description);
        
        // Extract image
        if (preg_match('/<img[^>]*id="landingImage"[^>]*src="([^"]+)"[^>]*>/', $html, $matches)) {
            $data['image'] = $matches[1];
        } elseif (preg_match('/<img[^>]*data-old-hires="([^"]+)"[^>]*>/', $html, $matches)) {
            $data['image'] = $matches[1];
        }
        
        return $data;
    }

    /**
     * Download and save image from URL
     */
    private function downloadAndSaveImage($imageUrl)
    {
        try {
            // محاولة استخدام ضغط الصور المتقدم أولاً
            if (extension_loaded('gd')) {
                return ImageCompressionService::compressFromUrl(
                    $imageUrl,
                    'tools',
                    80, // جودة 80%
                    1200, // أقصى عرض
                    1200  // أقصى ارتفاع
                );
            } else {
                // استخدام الحفظ المباشر إذا لم يكن GD متوفراً
                return SimpleImageCompressionService::compressFromUrl(
                    $imageUrl,
                    'tools'
                );
            }
            
        } catch (\Exception $e) {
            \Log::error('Failed to download and save image: ' . $e->getMessage());
            return null;
        }
    }
}
