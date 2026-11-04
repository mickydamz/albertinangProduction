<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\{Schema,DB};
return new class extends Migration {
 public function up(){Schema::table('states',function(Blueprint $t){$t->boolean('delivery_enabled')->default(false);});DB::table('states')->update(['delivery_enabled'=>DB::raw('is_active')]);}
 public function down(){Schema::table('states',function(Blueprint $t){$t->dropColumn('delivery_enabled');});}
};
