<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration {
    public function up(): void {
        Schema::table('sales',fn(Blueprint $t)=>$t->dateTime('guide_recorded_at')->nullable());
        Schema::create('livestock_measurements',function(Blueprint $t){$t->id();$t->foreignId('user_id')->constrained()->cascadeOnDelete();$t->foreignId('livestock_batch_id')->constrained()->cascadeOnDelete();$t->decimal('average_weight_kg',8,2);$t->string('rfid',100)->nullable();$t->string('source',20);$t->timestamps();});
    }
    public function down(): void {Schema::dropIfExists('livestock_measurements');Schema::table('sales',fn(Blueprint $t)=>$t->dropColumn('guide_recorded_at'));}
};
