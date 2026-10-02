<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration {
    public function up(): void { Schema::create('whatsapp_messages',function(Blueprint $t){$t->id();$t->foreignId('user_id')->constrained()->cascadeOnDelete();$t->foreignId('lead_id')->nullable()->constrained()->nullOnDelete();$t->string('provider_id',255)->unique();$t->string('direction',20);$t->string('phone',40);$t->text('body');$t->string('status',20);$t->timestamps();}); }
    public function down(): void { Schema::dropIfExists('whatsapp_messages'); }
};
