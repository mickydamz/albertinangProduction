<?php
namespace Tests\Regression;
use App\Models\{Order,PendingCheckout,PaystackTransaction};
use Illuminate\Routing\Middleware\ThrottleRequests;
use Illuminate\Support\Facades\{Http,DB};
use App\Services\PaystackOrderService;
abstract class CriticalTestCase extends RegressionTestCase
{
    protected array $raceEvidence = [];
    protected function tearDown(): void
    {
        try {
            if ($this->hasFailed()) {
                $rows = [];
                foreach (['orders','order_items','pending_checkouts','order_returns','order_cancellations','coupons','coupon_usages'] as $table) {
                    $rows[$table] = DB::table($table)->get()->toArray();
                }
                $requests = Http::recorded()->map(fn($pair) => ['url'=>$pair[0]->url(),'body'=>$pair[0]->data(),'response_status'=>$pair[1] ? $pair[1]->status() : null,'response'=>$pair[1] ? $pair[1]->json() : null])->toArray();
                $dir = getenv('CRITICAL_EVIDENCE_DIR');
                if ($dir) {
                    if (!is_dir($dir)) mkdir($dir,0700,true);
                    file_put_contents($dir.'/'.$this->getName().'.json',json_encode(['test'=>$this->getName(),'failure'=>$this->getStatusMessage(),'gateway_is_synthetic'=>true,'tables'=>$rows,'gateway_requests'=>$requests,'concurrency'=>$this->raceEvidence],JSON_PRETTY_PRINT|JSON_UNESCAPED_SLASHES));
                }
            }
        } finally { parent::tearDown(); }
    }
    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutMiddleware(ThrottleRequests::class);
        config(['services.paystack.secret'=>'critical-synthetic-secret']);
    }
    protected function pending($user, array $overrides=[]): PendingCheckout
    {
        $p=$this->product();
        return PendingCheckout::create(array_merge([
            'reference'=>'critical_'.uniqid(), 'user_id'=>$user->id, 'customer_email'=>$user->email,
            'total_ngn'=>10000, 'items'=>[['id'=>$p->id,'name'=>$p->name,'basePriceNgn'=>10000,'effective_price_ngn'=>10000,'quantity'=>1]],
            'fulfillment'=>['method'=>'pickup','pickup_location'=>'REGRESSION HQ'], 'coupon'=>null,
        ],$overrides));
    }
    protected function verified(PendingCheckout $pending, array $overrides=[]): array
    {
        return ['status'=>true,'data'=>array_merge(['id'=>101,'reference'=>$pending->reference,'status'=>'success','amount'=>(int)round($pending->total_ngn*100),'currency'=>'NGN','customer'=>['email'=>$pending->customer_email]],$overrides)];
    }
    protected function fakePayment(PendingCheckout $pending, array $overrides=[]): void
    {
        Http::swap(new \Illuminate\Http\Client\Factory());Http::fake(['api.paystack.co/transaction/verify/*'=>Http::response($this->verified($pending,$overrides),200),'*'=>Http::response(['status'=>false],503)]);
    }
    protected function fulfil(PendingCheckout $pending): array {return app(PaystackOrderService::class)->fulfil($pending->reference,$pending->user_id);}
    protected function webhook(string $event,array $data)
    {
        $body=json_encode(['event'=>$event,'data'=>$data]);
        return $this->call('POST','/webhooks/paystack',[],[],[],['CONTENT_TYPE'=>'application/json','HTTP_X_PAYSTACK_SIGNATURE'=>hash_hmac('sha512',$body,'critical-synthetic-secret')],$body);
    }
    protected function assertOneOrder(PendingCheckout $pending): Order
    {
        $this->assertSame(1,Order::where('reference',$pending->reference)->count());
        $o=Order::where('reference',$pending->reference)->firstOrFail();
        $this->assertEquals($pending->total_ngn,$o->total);$this->assertSame('paid',$o->status);
        $this->assertSame(count($pending->items),$o->items()->count());
        $this->assertNotNull($pending->fresh()->fulfilled_at);
        $this->assertSame($o->id,PaystackTransaction::where('reference',$pending->reference)->value('order_id'));
        return $o;
    }
    /** Run real separate PHP processes against a disposable SQLite copy, never staging. */
    protected function race(array $pending): array
    {
        if(!function_exists('proc_open'))$this->markTestSkipped('Separate PHP processes are required for concurrency.');
        $dir=sys_get_temp_dir().'/critical-concurrency-'.bin2hex(random_bytes(8));mkdir($dir,0700);
        $db=$dir.'/isolated.sqlite';$copy=new \PDO('sqlite:'.$db);$copy->setAttribute(\PDO::ATTR_ERRMODE,\PDO::ERRMODE_EXCEPTION);
        $source=DB::connection()->getPdo();
        foreach($source->query("SELECT name,sql FROM sqlite_master WHERE type='table' AND name NOT LIKE 'sqlite_%'")->fetchAll(\PDO::FETCH_ASSOC) as $table){
            $copy->exec($table['sql']);$name='"'.str_replace('"','""',$table['name']).'"';
            foreach($source->query('SELECT * FROM '.$name)->fetchAll(\PDO::FETCH_ASSOC) as $row){
                $columns=implode(',',array_map(fn($k)=>'"'.str_replace('"','""',$k).'"',array_keys($row)));
                $stmt=$copy->prepare('INSERT INTO '.$name.' ('.$columns.') VALUES ('.implode(',',array_fill(0,count($row),'?')).')');$stmt->execute(array_values($row));
            }
        }
        foreach($source->query("SELECT sql FROM sqlite_master WHERE type='index' AND sql IS NOT NULL")->fetchAll(\PDO::FETCH_COLUMN) as $sql)$copy->exec($sql);
        $copy->exec('PRAGMA journal_mode=WAL');$copy=null;$processes=[];
        try {
            foreach($pending as $i=>$p){file_put_contents($dir."/fixture{$i}.json",json_encode(['reference'=>$p->reference,'user_id'=>$p->user_id,'verified'=>$this->verified($p)]));
                $proc=proc_open([PHP_BINARY,__DIR__.'/critical-worker.php',base_path(),$db,$dir."/fixture{$i}.json",$dir.'/go',$dir."/ready{$i}",$dir."/result{$i}.json"],[0=>['file','/dev/null','r'],1=>['file',$dir."/out{$i}",'w'],2=>['file',$dir."/err{$i}",'w']],$pipes);if(!is_resource($proc))throw new \RuntimeException('Cannot start concurrency worker');$processes[]=$proc;
            }
            $deadline=microtime(true)+15;
            while(count(glob($dir.'/ready*'))<count($pending)){if(microtime(true)>$deadline)throw new \RuntimeException('Concurrency workers did not reach payment barrier');usleep(50000);}
            touch($dir.'/go');
            $deadline=microtime(true)+20;
            while(count(glob($dir.'/result*.json'))<count($pending)){if(microtime(true)>$deadline)throw new \RuntimeException('Concurrency workers did not finish');usleep(50000);}
            $results=[];foreach($pending as $i=>$p)$results[]=json_decode(file_get_contents($dir."/result{$i}.json"),true);
            $pdo=new \PDO('sqlite:'.$db);$rows=[];foreach(['orders','order_items','products','coupon_usages','coupons'] as $table)$rows[$table]=$pdo->query('SELECT * FROM '.$table)->fetchAll(\PDO::FETCH_ASSOC);
            return $this->raceEvidence = ['workers'=>$results,'tables'=>$rows];
        } finally {foreach($processes as $proc){$status=proc_get_status($proc);if($status['running'])proc_terminate($proc);proc_close($proc);}foreach(glob($dir.'/*') as $file)unlink($file);rmdir($dir);}
    }
}
