<?php
namespace Tests\Regression;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\{Http, Mail, Notification, Storage, Schema};
use App\Models\{User, Product, Order};
abstract class RegressionTestCase extends TestCase
{
    use RefreshDatabase { refreshDatabase as protected frameworkRefreshDatabase; }
    // Guard BEFORE RefreshDatabase can migrate or drop anything.
    public function refreshDatabase()
    {
        if (!app()->environment('testing') || config('database.default') !== 'sqlite'
            || config('database.connections.sqlite.database') !== ':memory:') {
            throw new \RuntimeException('Regression tests require testing + SQLite :memory:. Remove cached config in the isolated test checkout.');
        }
        $this->frameworkRefreshDatabase();
    }
    protected function setUp(): void
    {
        parent::setUp();
        Mail::fake(); Notification::fake(); Storage::fake('public');
        Http::fake(['*' => Http::response(['status'=>false,'message'=>'Blocked by regression harness'], 503)]);
        foreach (['orders','order_items','order_returns','order_cancellations','reviews'] as $table) {
            $this->assertTrue(Schema::hasTable($table), "Missing real migration for {$table}; use current backend source. Do not invent a passing schema.");
        }
    }
    protected function customer(string $role='user'): User
    {
        return User::factory()->create(['role'=>$role]);
    }
    protected function product(): Product
    {
        $supplier=$this->customer('supplier');
        return Product::forceCreate(['supplier_id'=>$supplier->id,'name'=>'REGRESSION Dummy appliance','price'=>10000,'stock'=>10,'is_active'=>true]);
    }
    protected function order(User $user, string $status='paid', string $method='cash', ?Product $product=null): Order
    {
        $product=$product ?: $this->product();
        $order=Order::create(['user_id'=>$user->id,'status'=>$status,'total'=>10000,'total_usd'=>10,
            'payment_method'=>$method,'payment_id'=>'regression_'.uniqid(),'reference'=>'regression_'.uniqid()]);
        $order->items()->create(['product_id'=>$product->id,'name'=>$product->name,'price'=>10000,'quantity'=>1]);
        return $order;
    }
    protected function assertDenied($response): void
    {
        $this->assertContains($response->status(),[403,404], 'Authenticated cross-account access must be denied, not redirected or rendered.');
    }
}
