<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sales_opportunities', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('client_id')->constrained()->restrictOnDelete();
            $table->string('title', 180);
            $table->string('livestock_summary', 200)->nullable();
            $table->unsignedInteger('head_count')->nullable();
            $table->decimal('estimated_value', 14, 2)->default(0);
            $table->string('stage', 24)->default('contact');
            $table->date('expected_close_date')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->index(['user_id', 'stage']);
            $table->index(['user_id', 'expected_close_date']);
        });

        DB::table('permissions')->insertOrIgnore([
            'code' => 'sales-pipeline.manage',
            'label' => 'Administrar Pipeline comercial',
            'description' => 'Gestionar oportunidades comerciales de clientes propios.',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('sales_opportunities');
        DB::table('permissions')->where('code', 'sales-pipeline.manage')->delete();
    }
};
