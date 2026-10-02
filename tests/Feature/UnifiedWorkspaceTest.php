<?php
namespace Tests\Feature;
use App\Models\User;
use App\Models\Quote;
use App\Services\QuoteService;
use App\Services\LeadService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
class UnifiedWorkspaceTest extends TestCase
{
    use RefreshDatabase;
    private function admin(): User {$user=User::factory()->create();config(['cowapp.mail_settings_admin_email'=>$user->email]);return $user;}
    public function test_new_module_pages_render_for_admin_and_reject_a_user_without_permissions(): void {
        $admin=$this->admin();$this->actingAs($admin)->withoutVite();
        foreach(['admin.products.index','admin.quotes.index','admin.sales.index','admin.activities.index','admin.portal-blocks.index','admin.auctions.index','admin.insights.index','admin.security.index','admin.whatsapp.index'] as $route)$this->get(route($route))->assertOk();
        $this->get(route('catalog.index'))->assertOk();$other=User::factory()->create();$this->actingAs($other);
        foreach(['admin.products.index','admin.quotes.index','admin.sales.index','admin.activities.index','admin.portal-blocks.index','admin.auctions.index','admin.security.index','admin.whatsapp.index'] as $route)$this->get(route($route))->assertForbidden();
    }
    public function test_quote_uses_server_totals_and_rejects_foreign_client_and_record_access(): void {
        $admin=$this->admin();$client=$admin->clients()->create(['name'=>'Ganadero','client_type'=>'individual','status'=>'active']);
        $data=['client_id'=>$client->id,'title'=>'Venta ganado','gross_kg'=>1000,'shrink_percent'=>5,'price_per_kg'=>10000,'withholding_percent'=>1.5,'commission_percent'=>3,'other_deduction'=>0,'valid_until'=>today()->addDays(7)->format('Y-m-d')];
        $this->actingAs($admin)->post(route('admin.quotes.store'),$data+['total_cents'=>1,'user_id'=>999])->assertSessionHasNoErrors();
        $quote=Quote::firstOrFail();$this->assertSame(950000000,$quote->subtotal_cents);$this->assertSame(907250000,$quote->total_cents);$this->assertSame($admin->id,$quote->user_id);
        $other=User::factory()->create();$foreign=$other->clients()->create(['name'=>'Otro','client_type'=>'individual','status'=>'active']);
        $this->post(route('admin.quotes.store'),array_replace($data,['client_id'=>$foreign->id]))->assertSessionHasErrors('client_id');
        $this->actingAs($other)->get(route('admin.quotes.edit',$quote))->assertForbidden();
    }
    public function test_lead_conversion_and_accepted_quote_sale_are_idempotent(): void {
        $admin=$this->admin();$lead=$admin->leads()->create(['name'=>'Prospecto','source'=>'manual','status'=>'new','email'=>'farmer@example.com']);$leadService=app(LeadService::class);
        $one=$leadService->convert($lead);$two=$leadService->convert($lead);$this->assertSame($one->id,$two->id);$this->assertSame(1,$admin->clients()->count());
        $quoteService=app(QuoteService::class);$quote=$quoteService->save($admin,['client_id'=>$one->client_id,'title'=>'Oferta','gross_kg'=>100,'shrink_percent'=>0,'price_per_kg'=>1000,'withholding_percent'=>0,'commission_percent'=>0,'other_deduction'=>0,'valid_until'=>today()->addDays(7)]);
        $quote->update(['status'=>'accepted']);$sale1=$quoteService->recordSale($quote);$sale2=$quoteService->recordSale($quote);$this->assertSame($sale1->id,$sale2->id);
    }
    public function test_whatsapp_webhook_refuses_unsigned_requests(): void {
        config(['whatsapp.app_secret'=>'test-secret']);$this->postJson('/api/whatsapp/webhook',['entry'=>[]])->assertForbidden();
    }
}
