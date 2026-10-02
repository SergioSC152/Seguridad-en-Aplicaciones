<?php
namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use App\Models\Sale;
use App\Http\Requests\UpdateSaleRequest;
use App\Services\SaleService;
use Illuminate\Support\Facades\Gate;
class SaleController extends Controller
{
    public function index() { Gate::authorize('viewAny',Sale::class); return view('admin.sales.index',['sales'=>Sale::where('user_id',auth()->id())->with(['client','quote'])->latest()->paginate(15)]); }
    public function update(UpdateSaleRequest $r, Sale $sale, SaleService $s) { $s->update($sale,$r->validated()); return back()->with('success','Pago y despacho actualizados.'); }
}
