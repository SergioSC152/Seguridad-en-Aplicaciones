<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('livestock_batches', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('livestock_category_id')->constrained()->restrictOnDelete();
            $table->string('code', 50);
            $table->string('ear_tag', 80)->nullable();
            $table->unsignedInteger('head_count');
            $table->decimal('average_weight_kg', 8, 2)->nullable();
            $table->string('farm_name', 150)->nullable();
            $table->string('paddock', 100)->nullable();
            $table->string('status', 20)->default('active');
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->unique(['user_id', 'code']);
            $table->index(['user_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('livestock_batches');
    }
};
