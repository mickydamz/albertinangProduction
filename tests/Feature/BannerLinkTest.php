<?php
namespace Tests\Feature;
use App\Models\Banner;
use Illuminate\Http\Request;
use Tests\TestCase;
class BannerLinkTest extends TestCase
{
    public function test_copied_links_stay_in_staging_without_changing_external_links()
    {
        foreach (['test.albertinang.com','testing.albertinang.com'] as $host) {
            $this->app->instance('request', Request::create('https://'.$host.'/'));
            $this->assertSame('https://'.$host.'/products?q=TV#offers', (new Banner(['link'=>'https://www.albertinang.com/products?q=TV#offers']))->link);
            $this->assertSame('https://example.com/', (new Banner(['link'=>'https://example.com/']))->link);
        }
        $this->app->instance('request', Request::create('https://albertinang.com/'));
        $this->assertSame('https://albertinang.com/', (new Banner(['link'=>'https://albertinang.com/']))->link);
    }
}
