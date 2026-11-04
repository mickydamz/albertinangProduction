<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\{Schema,DB};
return new class extends Migration {
 public function up(): void {
  if(!Schema::hasColumn('pickup_points','state_id'))Schema::table('pickup_points',function(Blueprint $t){$t->unsignedBigInteger('state_id')->nullable()->index();});
  DB::table('pickup_points')->whereNull('state_id')->orderBy('id')->get()->each(function($p){$state=DB::table('locations')->where('id',$p->location_id)->value('state_id');if($state)DB::table('pickup_points')->where('id',$p->id)->update(['state_id'=>$state]);});
  // Preserve effective availability: separating collection must not activate previously hidden stores.
  DB::table('pickup_points')->whereIn('location_id', DB::table('locations')->where('is_active',false)->orWhereIn('state_id',DB::table('states')->where('is_active',false)->select('id'))->select('id'))->update(['is_active'=>false]);
  // Legacy location references and product links remain for rollback and old snapshots.
  if(DB::getDriverName()==='mysql'){
   $column=DB::selectOne("SHOW COLUMNS FROM pickup_points WHERE Field = 'location_id'");
   if($column->Null==='NO' && preg_match('/^(bigint|int)(\([0-9]+\))?( unsigned)?$/i',$column->Type))DB::statement('ALTER TABLE pickup_points MODIFY location_id '.$column->Type.' NULL');
  }
 }
 public function down(): void { Schema::table('pickup_points',fn(Blueprint $t)=>$t->dropColumn('state_id')); }
};
