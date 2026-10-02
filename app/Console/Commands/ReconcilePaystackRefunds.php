<?php
namespace App\Console\Commands;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use App\Services\PaystackRefundService;
class ReconcilePaystackRefunds extends Command
{
    protected $signature='paystack:reconcile-refunds';
    protected $description='Check unresolved Paystack refunds without issuing another refund';
    public function handle(PaystackRefundService $service): int
    {
        $failed=0;
        DB::table('paystack_refunds')->where('status','!=','processed')->orderBy('id')->chunkById(100,function($rows) use ($service,&$failed) {
            foreach($rows as $row) {
                try { if (!$service->reconcile($row)) { $failed++; $this->warn('Refund '.$row->id.' still requires review.'); } }
                catch (\Throwable $e) { $failed++; $this->warn('Refund '.$row->id.' could not be checked.'); }
            }
        });
        $this->info('Reconciliation finished. Unresolved: '.$failed);
        return $failed ? 1 : 0;
    }
}
