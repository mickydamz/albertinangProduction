<?php
// Synthetic gateway + temporary SQLite only. Never accepts remote DB configuration.
[$script,$root,$database,$fixture,$go,$ready,$result]=$argv;
if(!str_starts_with(realpath(dirname($database)),realpath(sys_get_temp_dir()).'/critical-concurrency-')||basename($database)!=='isolated.sqlite')throw new RuntimeException('Unsafe concurrency database path');
require $root.'/vendor/autoload.php';$app=require $root.'/bootstrap/app.php';$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();
config(['app.env'=>'testing','database.default'=>'sqlite','database.connections.sqlite.database'=>$database,'database.connections.sqlite.url'=>null,'services.paystack.secret'=>'critical-synthetic-secret','mail.default'=>'array','cache.default'=>'array','queue.default'=>'sync']);
Illuminate\Support\Facades\DB::purge('sqlite');Illuminate\Support\Facades\DB::connection()->statement('PRAGMA busy_timeout=5000');
Illuminate\Support\Facades\Mail::fake();Illuminate\Support\Facades\Notification::fake();
$data=json_decode(file_get_contents($fixture),true);$arrived=false;
Illuminate\Support\Facades\Http::fake(function($request)use($data,$ready,$go,&$arrived){
    if(!str_starts_with($request->url(),'https://api.paystack.co/transaction/verify/'))return Illuminate\Support\Facades\Http::response(['status'=>false],503);
    if(!$arrived){touch($ready);$arrived=true;$deadline=microtime(true)+15;while(!file_exists($go)){if(microtime(true)>$deadline)throw new RuntimeException('Barrier timeout');usleep(50000);}}
    return Illuminate\Support\Facades\Http::response($data['verified'],200);
});
try{$out=null;for($i=0;$i<3;$i++){$out=app(App\Services\PaystackOrderService::class)->fulfil($data['reference'],$data['user_id']);if($out['success']||($out['error_type']??null)!=='transient')break;usleep(100000);}
    $out['confirmation_count']=Illuminate\Support\Facades\Mail::getFacadeRoot()->sent(App\Mail\OrderConfirmation::class)->count();file_put_contents($result,json_encode($out));
}catch(Throwable $e){file_put_contents($result,json_encode(['success'=>false,'exception'=>$e->getMessage()]));}
