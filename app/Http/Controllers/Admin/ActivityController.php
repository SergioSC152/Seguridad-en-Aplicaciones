<?php
namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use App\Http\Requests\SaveActivityRequest;
use App\Models\Activity;
use App\Models\Media;
use App\Models\Client;
use App\Models\SalesOpportunity;
use App\Services\ActivityService;
use Illuminate\Support\Facades\Gate;
class ActivityController extends Controller
{
    public function index() { Gate::authorize('viewAny',Activity::class); return $this->screen(); }
    public function edit(Activity $activity) { Gate::authorize('update',$activity); return $this->screen($activity); }
    private function screen(?Activity $record=null) {
        return view('admin.workspace.crud',['title'=>'Actividades y agenda','routeBase'=>'admin.activities','routeParameter'=>'activity','records'=>Activity::where('user_id',auth()->id())->latest()->paginate(15),'editing'=>$record,'fields'=>['title'=>['Actividad','text'], 'client_id'=>['Cliente','select',Client::where('user_id',auth()->id())->pluck('name','id')->all()], 'sales_opportunity_id'=>['Oportunidad','select',SalesOpportunity::where('user_id',auth()->id())->pluck('title','id')->all()], 'due_at'=>['Fecha y hora (UTC)','datetime-local'], 'status'=>['Estado','select',['pending'=>'Pendiente','done'=>'Completada','cancelled'=>'Cancelada']], 'notes'=>['Notas','textarea']]]);
    }
    public function store(SaveActivityRequest $request, ActivityService $service) { $service->save($request->user(),$request->validated()); return to_route('admin.activities.index')->with('success','Registro creado.'); }
    public function update(SaveActivityRequest $request, Activity $activity, ActivityService $service) { $service->save($request->user(),$request->validated(),$activity); return to_route('admin.activities.index')->with('success','Cambios guardados.'); }
    public function destroy(Activity $activity) { Gate::authorize('delete',$activity); $activity->delete(); return to_route('admin.activities.index')->with('success','Registro eliminado.'); }
}

