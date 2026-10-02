<?php
namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use App\Http\Requests\SavePortalBlockRequest;
use App\Models\PortalBlock;
use App\Models\Media;
use App\Models\Client;
use App\Models\SalesOpportunity;
use App\Services\PortalBlockService;
use Illuminate\Support\Facades\Gate;
class PortalBlockController extends Controller
{
    public function index() { Gate::authorize('viewAny',PortalBlock::class); return $this->screen(); }
    public function edit(PortalBlock $block) { Gate::authorize('update',$block); return $this->screen($block); }
    private function screen(?PortalBlock $record=null) {
        return view('admin.workspace.crud',['title'=>'Contenido del portal','routeBase'=>'admin.portal-blocks','routeParameter'=>'block','records'=>PortalBlock::where('user_id',auth()->id())->latest()->paginate(15),'editing'=>$record,'fields'=>['kind'=>['Sección','select',['banner'=>'Banner','testimonial'=>'Testimonio','team'=>'Equipo','gallery'=>'Galería','contact'=>'Contacto']], 'title'=>['Título','text'], 'body'=>['Contenido','textarea'], 'media_id'=>['Imagen o video de la biblioteca','select',Media::where('active',true)->pluck('name','id')->all()], 'link_url'=>['Enlace HTTP/HTTPS','url'], 'position'=>['Orden','number'], 'published'=>['Publicado','select',[0=>'No',1=>'Sí']]]]);
    }
    public function store(SavePortalBlockRequest $request, PortalBlockService $service) { $service->save($request->user(),$request->validated()); return to_route('admin.portal-blocks.index')->with('success','Registro creado.'); }
    public function update(SavePortalBlockRequest $request, PortalBlock $block, PortalBlockService $service) { $service->save($request->user(),$request->validated(),$block); return to_route('admin.portal-blocks.index')->with('success','Cambios guardados.'); }
    public function destroy(PortalBlock $block) { Gate::authorize('delete',$block); $block->delete(); return to_route('admin.portal-blocks.index')->with('success','Registro eliminado.'); }
}

