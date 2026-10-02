<?php
namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use App\Http\Requests\SaveProductRequest;
use App\Models\Product;
use App\Models\Media;
use App\Models\Client;
use App\Models\SalesOpportunity;
use App\Services\ProductService;
use Illuminate\Support\Facades\Gate;
class ProductController extends Controller
{
    public function index() { Gate::authorize('viewAny',Product::class); return $this->screen(); }
    public function edit(Product $product) { Gate::authorize('update',$product); return $this->screen($product); }
    private function screen(?Product $record=null) {
        return view('admin.workspace.crud',['title'=>'Productos y servicios','routeBase'=>'admin.products','routeParameter'=>'product','records'=>Product::where('user_id',auth()->id())->latest()->paginate(15),'editing'=>$record,'fields'=>['name'=>['Nombre','text'], 'kind'=>['Tipo','select',['product'=>'Producto','service'=>'Servicio']], 'purpose'=>['Propósito','select',['cria'=>'Cría','ceba'=>'Ceba','leche'=>'Leche','genetica'=>'Genética']], 'price'=>['Precio COP','number'], 'description'=>['Descripción','textarea'], 'media_id'=>['Imagen de la biblioteca','select',Media::where('active',true)->where('mime_type','like','image/%')->pluck('name','id')->all()], 'published'=>['Publicar','select',[0=>'No',1=>'Sí']], 'active'=>['Activo','select',[1=>'Sí',0=>'No']]]]);
    }
    public function store(SaveProductRequest $request, ProductService $service) { $service->save($request->user(),$request->validated()); return to_route('admin.products.index')->with('success','Registro creado.'); }
    public function update(SaveProductRequest $request, Product $product, ProductService $service) { $service->save($request->user(),$request->validated(),$product); return to_route('admin.products.index')->with('success','Cambios guardados.'); }
    public function destroy(Product $product) { Gate::authorize('delete',$product); $product->delete(); return to_route('admin.products.index')->with('success','Registro eliminado.'); }
}

