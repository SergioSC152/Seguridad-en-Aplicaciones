<?php
namespace App\Http\Controllers;
use App\Http\Requests\CaptureLeadRequest;
use App\Models\LivestockBatch;
use App\Models\Product;
use App\Models\PortalBlock;
use App\Models\User;
use App\Services\LeadService;
use Illuminate\Http\Request;
class CatalogController extends Controller
{
    public function index(Request $r) {
        $filters=$r->validate(['q'=>['nullable','string','max:100'],'purpose'=>['nullable','in:cria,ceba,leche,genetica']]);
        return view('catalog.index',['batches'=>LivestockBatch::where('published',true)->where('status','active')->whereIn('availability',['available','auction','bidding'])->with('category')->when($filters['purpose']??null,fn($q,$p)=>$q->where('purpose',$p))->when($filters['q']??null,fn($q,$s)=>$q->where('code','like','%'.$s.'%'))->latest()->paginate(12)->withQueryString(),'products'=>Product::where('published',true)->where('active',true)->with('media')->orderBy('name')->get(),'blocks'=>PortalBlock::where('published',true)->with('media')->orderBy('position')->get(),'filters'=>$filters]);
    }
    public function capture(CaptureLeadRequest $r,LeadService $service) {
        $d=$r->validated(); $batch=isset($d['livestock_batch_id'])?LivestockBatch::where('published',true)->where('status','active')->find($d['livestock_batch_id']):null;
        $owner=$batch?->user ?? User::whereRaw('LOWER(email) = ?',[mb_strtolower((string)config('cowapp.mail_settings_admin_email'))])->first();
        if (!$owner) return back()->withErrors(['name'=>'No hay responsable configurado para recibir solicitudes. Contacta con CowApp.'])->withInput($r->except('_token'));
        unset($d['livestock_batch_id'],$d['consent']); $d['email']=mb_strtolower(trim($d['email']));
        $service->create($owner,$d+['source'=>'website','status'=>'new','livestock_interest'=>$batch?'Lote '.$batch->code:'Consignación / consulta pública']);
        return back()->with('success','Solicitud registrada. El equipo comercial podrá dar seguimiento.');
    }
}
