<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('leads', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('name', 160);
            $table->string('farm_name', 160)->nullable();
            $table->string('phone', 40)->nullable();
            $table->string('email', 255)->nullable();
            $table->string('municipality', 100)->nullable();
            $table->string('department', 100)->nullable();
            $table->string('source', 24)->default('manual');
            $table->string('status', 24)->default('new');
            $table->string('livestock_interest', 200)->nullable();
            $table->unsignedInteger('estimated_heads')->nullable();
            $table->date('follow_up_at')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->index(['user_id', 'status']);
            $table->index(['user_id', 'source']);
            $table->index(['user_id', 'created_at']);
        });

        DB::table('permissions')->insertOrIgnore([
            'code' => 'leads.manage',
            'label' => 'Administrar leads y prospectos',
            'description' => 'Registrar y dar seguimiento a prospectos de la cuenta.',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('leads');
        DB::table('permissions')->where('code', 'leads.manage')->delete();
    }
};
