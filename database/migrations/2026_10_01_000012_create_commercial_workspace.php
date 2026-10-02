<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', fn (Blueprint $t) => $t->boolean('mfa_enabled')->default(false));
        Schema::table('clients', function (Blueprint $t) {
            $t->string('ica_registration', 100)->nullable();
            $t->decimal('credit_limit', 14, 2)->default(0);
            $t->boolean('vip')->default(false);
        });
        Schema::table('leads', function (Blueprint $t) {
            $t->decimal('hectares', 10, 2)->nullable();
            $t->decimal('carrying_capacity', 8, 2)->nullable();
            $t->decimal('budget', 14, 2)->nullable();
            $t->string('purpose', 30)->nullable();
            $t->foreignId('converted_client_id')->nullable()->constrained('clients')->nullOnDelete();
            $t->foreignId('converted_opportunity_id')->nullable()->constrained('sales_opportunities')->nullOnDelete();
        });
        Schema::table('livestock_batches', function (Blueprint $t) {
            $t->string('purpose', 30)->default('ceba');
            $t->string('availability', 30)->default('available');
            $t->boolean('published')->default(false);
            $t->decimal('price_per_kg', 12, 2)->nullable();
            $t->string('rfid', 100)->nullable();
        });
        Schema::create('products', function (Blueprint $t) {
            $t->id(); $t->foreignId('user_id')->constrained()->cascadeOnDelete();
            $t->string('name', 160); $t->string('kind', 30); $t->string('purpose', 30)->nullable();
            $t->decimal('price', 12, 2); $t->text('description')->nullable();
            $t->foreignId('media_id')->nullable()->constrained('media')->nullOnDelete();
            $t->boolean('published')->default(false); $t->boolean('active')->default(true); $t->timestamps();
        });
        Schema::create('quotes', function (Blueprint $t) {
            $t->id(); $t->foreignId('user_id')->constrained()->cascadeOnDelete();
            $t->foreignId('client_id')->constrained()->restrictOnDelete();
            $t->foreignId('livestock_batch_id')->nullable()->constrained()->nullOnDelete();
            $t->string('number', 60); $t->string('title', 160);
            $t->decimal('gross_kg', 12, 3); $t->decimal('shrink_percent', 5, 2)->default(0);
            $t->decimal('price_per_kg', 12, 2); $t->decimal('withholding_percent', 5, 2)->default(0);
            $t->decimal('commission_percent', 5, 2)->default(3); $t->decimal('other_deduction', 14, 2)->default(0);
            $t->unsignedBigInteger('subtotal_cents'); $t->unsignedBigInteger('total_cents');
            $t->string('status', 20)->default('draft'); $t->date('valid_until'); $t->text('terms')->nullable();
            $t->string('contract_token_hash', 64)->nullable()->unique(); $t->unsignedBigInteger('contract_expires_epoch')->nullable();
            $t->string('acceptance_otp_hash')->nullable(); $t->unsignedBigInteger('acceptance_expires_epoch')->nullable();
            $t->unsignedTinyInteger('acceptance_attempts')->default(0);
            $t->timestamp('accepted_at')->nullable(); $t->string('accepted_ip', 45)->nullable();
            $t->string('accepted_document_hash', 64)->nullable(); $t->timestamps();
            $t->unique(['user_id', 'number']);
        });
        Schema::create('sales', function (Blueprint $t) {
            $t->id(); $t->foreignId('user_id')->constrained()->cascadeOnDelete();
            $t->foreignId('quote_id')->unique()->constrained()->restrictOnDelete();
            $t->foreignId('client_id')->constrained()->restrictOnDelete();
            $t->unsignedBigInteger('total_cents'); $t->unsignedBigInteger('paid_cents')->default(0);
            $t->date('sold_at'); $t->string('ica_guide', 100)->nullable(); $t->timestamp('dispatched_at')->nullable(); $t->timestamps();
        });
        Schema::create('activities', function (Blueprint $t) {
            $t->id(); $t->foreignId('user_id')->constrained()->cascadeOnDelete();
            $t->foreignId('client_id')->nullable()->constrained()->nullOnDelete();
            $t->foreignId('sales_opportunity_id')->nullable()->constrained()->nullOnDelete();
            $t->string('title', 160); $t->dateTime('due_at'); $t->string('status', 20)->default('pending');
            $t->text('notes')->nullable(); $t->timestamps();
        });
        Schema::create('portal_blocks', function (Blueprint $t) {
            $t->id(); $t->foreignId('user_id')->constrained()->cascadeOnDelete();
            $t->string('kind', 30); $t->string('title', 160); $t->text('body')->nullable();
            $t->foreignId('media_id')->nullable()->constrained('media')->nullOnDelete();
            $t->string('link_url', 2048)->nullable(); $t->unsignedInteger('position')->default(0);
            $t->boolean('published')->default(false); $t->timestamps();
        });
        Schema::create('client_documents', function (Blueprint $t) {
            $t->id(); $t->foreignId('user_id')->constrained()->cascadeOnDelete();
            $t->foreignId('client_id')->constrained()->cascadeOnDelete();
            $t->string('kind', 30); $t->string('reference', 100)->nullable(); $t->date('expires_at')->nullable();
            $t->string('path'); $t->string('mime_type', 100); $t->string('review_status', 20)->default('pending');
            $t->text('review_notes')->nullable(); $t->timestamps();
        });
        Schema::create('security_events', function (Blueprint $t) {
            $t->id(); $t->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $t->string('event', 100); $t->string('severity', 20); $t->string('ip', 45)->nullable();
            $t->string('route', 150)->nullable(); $t->unsignedSmallInteger('http_status')->nullable(); $t->timestamps();
        });
        Schema::create('auctions', function (Blueprint $t) {
            $t->id(); $t->foreignId('user_id')->constrained()->cascadeOnDelete();
            $t->foreignId('livestock_batch_id')->constrained()->restrictOnDelete();
            $t->string('title', 160); $t->unsignedBigInteger('reserve_cents'); $t->string('status', 20)->default('draft');
            $t->foreignId('winner_client_id')->nullable()->constrained('clients')->restrictOnDelete();
            $t->unsignedBigInteger('winning_cents')->nullable(); $t->timestamps();
        });
        Schema::create('auction_bids', function (Blueprint $t) {
            $t->id(); $t->foreignId('auction_id')->constrained()->cascadeOnDelete();
            $t->foreignId('client_id')->constrained()->restrictOnDelete(); $t->unsignedBigInteger('amount_cents'); $t->timestamps();
        });
        foreach (['products.manage'=>'Productos y servicios', 'quotes.manage'=>'Cotizaciones y contratos', 'sales.manage'=>'Ventas y despachos', 'activities.manage'=>'Actividades y agenda', 'auctions.manage'=>'Remates', 'audit.view'=>'Auditoría de seguridad'] as $code=>$label) {
            DB::table('permissions')->insertOrIgnore(['code'=>$code, 'label'=>$label, 'created_at'=>now(), 'updated_at'=>now()]);
        }
    }

    public function down(): void
    {
        foreach (['auction_bids', 'auctions', 'security_events', 'client_documents', 'portal_blocks', 'activities', 'sales', 'quotes', 'products'] as $table) Schema::dropIfExists($table);
        Schema::table('livestock_batches', fn (Blueprint $t) => $t->dropColumn(['purpose', 'availability', 'published', 'price_per_kg', 'rfid']));
        Schema::table('leads', function (Blueprint $t) { $t->dropConstrainedForeignId('converted_client_id'); $t->dropConstrainedForeignId('converted_opportunity_id'); $t->dropColumn(['hectares','carrying_capacity','budget','purpose']); });
        Schema::table('clients', fn (Blueprint $t) => $t->dropColumn(['ica_registration','credit_limit','vip']));
        Schema::table('users', fn (Blueprint $t) => $t->dropColumn('mfa_enabled'));
        DB::table('permissions')->whereIn('code',['products.manage','quotes.manage','sales.manage','activities.manage','auctions.manage','audit.view'])->delete();
    }
};
