<?php
namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreClientDocumentRequest;
use App\Models\Client;
use App\Models\ClientDocument;
use App\Models\Activity;
use App\Models\Product;
use App\Services\ClientDocumentService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
class ClientProfileController extends Controller
{
    public function show(Client $client) {
        Gate::authorize('update',$client); $u=auth()->user();
        $sales=$u->hasPermissionTo('sales.manage')?$client->sales()->with('quote.batch')->latest()->get():collect();
        $purposes=$sales->map(fn($sale)=>$sale->quote->batch?->purpose)->filter()->unique()->all();
        return view('admin.clients.profile',['client'=>$client,'documents'=>$client->documents()->latest()->get(),'sales'=>$sales,'quotes'=>$u->hasPermissionTo('quotes.manage')?$client->quotes()->latest()->get():collect(),'activities'=>$u->hasPermissionTo('activities.manage')?Activity::where('user_id',$u->id)->where('client_id',$client->id)->orderBy('due_at')->get():collect(),'recommendations'=>$u->hasPermissionTo('products.manage') && $purposes ? Product::where('user_id',$u->id)->where('active',true)->whereIn('purpose',$purposes)->get():collect()]);
    }
    public function store(StoreClientDocumentRequest $r, Client $client,ClientDocumentService $s) { $s->create($client,$r->validated()); return back()->with('success','Documento privado guardado; requiere revisión manual.'); }
    public function download(ClientDocument $document) { Gate::authorize('view',$document); return Storage::disk('local')->download($document->path,'documento-'.$document->id.'.'.pathinfo($document->path,PATHINFO_EXTENSION),['X-Content-Type-Options'=>'nosniff','Cache-Control'=>'no-store']); }
    public function review(Request $r, ClientDocument $document) { Gate::authorize('update',$document); $d=$r->validate(['review_status'=>['required',Rule::in(['pending','approved','rejected'])],'review_notes'=>['nullable','string','max:3000']]); $document->update($d); return back()->with('success','Revisión registrada. No equivale a validación oficial ICA/FEDEGÁN.'); }
    public function destroy(ClientDocument $document,ClientDocumentService $s) { Gate::authorize('delete',$document); $s->delete($document); return back()->with('success','Documento eliminado.'); }
}
