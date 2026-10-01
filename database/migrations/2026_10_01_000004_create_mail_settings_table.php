<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('mail_settings', function (Blueprint $table) {
            $table->id();
            $table->string('host', 255);
            $table->unsignedSmallInteger('port');
            $table->string('username', 255)->nullable();
            $table->text('password')->nullable(); // Laravel encrypted cast; ciphertext is longer than the secret.
            $table->string('encryption', 3)->default('tls');
            $table->string('from_address', 255);
            $table->string('from_name', 255);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('mail_settings');
    }
};
