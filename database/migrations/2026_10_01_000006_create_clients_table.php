<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('clients', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('name', 160);
            $table->string('client_type', 20)->default('individual');
            $table->string('contact_person', 160)->nullable();
            $table->string('document_number', 40)->nullable();
            $table->string('email', 255)->nullable();
            $table->string('phone', 40)->nullable();
            $table->string('municipality', 100)->nullable();
            $table->string('department', 100)->nullable();
            $table->string('address', 255)->nullable();
            $table->string('status', 20)->default('active');
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->unique(['user_id', 'document_number']);
            $table->unique(['user_id', 'email']);
            $table->index(['user_id', 'status']);
        });

        DB::table('permissions')->insertOrIgnore([
            'code' => 'clients.manage',
            'label' => 'Administrar clientes',
            'description' => 'Registrar y mantener clientes ganaderos de la cuenta.',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('clients');
        DB::table('permissions')->where('code', 'clients.manage')->delete();
    }
};
