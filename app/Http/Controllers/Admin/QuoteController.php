<?php
namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use App\Http\Requests\SaveQuoteRequest;
use App\Models\Quote;
use App\Models\Client;
use App\Models\LivestockBatch;
use App\Services\QuoteService;
use Illuminate\Support\Facades\Gate;
use Throwable;
class QuoteController extends Controller
{
    public function index() { Gate::authorize('viewAny',Quote::class); return $this->screen(); }
    public function edit(Quote $quote) { Gate::authorize('update',$quote); return $this->screen($quote); }
    private function screen(?Quote $editing=null) { return view('admin.quotes.index',['quotes'=>Quote::where('user_id',auth()->id())->with('client')->latest()->paginate(15),'editing'=>$editing,'clients'=>Client::where('user_id',auth()->id())->orderBy('name')->get(),'batches'=>LivestockBatch::where('user_id',auth()->id())->orderBy('code')->get()]); }
    public function store(SaveQuoteRequest $r, QuoteService $s) { $s->save($r->user(),$r->validated()); return to_route('admin.quotes.index')->with('success','Cotización calculada y guardada.'); }
    public function update(SaveQuoteRequest $r, Quote $quote, QuoteService $s) { $s->save($r->user(),$r->validated(),$quote); return to_route('admin.quotes.index')->with('success','Cotización actualizada; cualquier enlace anterior quedó invalidado.'); }
    public function send(Quote $quote, QuoteService $s) { Gate::authorize('update',$quote); try { $s->sendContract($quote); } catch (\Illuminate\Validation\ValidationException $e) { throw $e; } catch (Throwable) { return back()->with('error','No se pudo enviar. Revisa la configuración SMTP.'); } return back()->with('success','Cotización enviada al correo del cliente.'); }
    public function sell(Quote $quote, QuoteService $s) { Gate::authorize('view',$quote); Gate::authorize('permission','sales.manage'); $s->recordSale($quote); return to_route('admin.sales.index')->with('success','Venta registrada sin duplicados.'); }
    public function destroy(Quote $quote) { Gate::authorize('delete',$quote); if ($quote->status==='accepted' || $quote->sale()->exists()) return back()->with('error','No se elimina una cotización aceptada o vinculada a una venta.'); $quote->delete(); return back()->with('success','Cotización eliminada.'); }
}
