<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Http\Controllers\CartController;

class TestCartCount extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cart:test-count';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test the cart count functionality';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info("اختبار وظيفة عداد السلة...");
        
        try {
            $controller = new CartController();
            $response = $controller->count();
            
            $this->info("✅ تم استدعاء cart count بنجاح");
            $this->info("Response status: " . $response->getStatusCode());
            $this->info("Response content: " . $response->getContent());
            
            return Command::SUCCESS;
            
        } catch (\Exception $e) {
            $this->error("❌ خطأ في استدعاء cart count: " . $e->getMessage());
            $this->error("Stack trace: " . $e->getTraceAsString());
            return Command::FAILURE;
        }
    }
}