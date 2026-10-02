<?php
namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use App\Http\Requests\SaveAuctionRequest;
use App\Models\Auction;
use App\Models\Client;
use App\Models\LivestockBatch;
use App\Services\AuctionService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;
class AuctionController extends Controller
{
    public function index() { Gate::authorize('viewAny',Auction::class); return view('admin.auctions.index',['auctions'=>Auction::where('user_id',auth()->id())->with(['batch','winner'])->withMax('bids','amount_cents')->latest()->paginate(15),'batches'=>LivestockBatch::where('user_id',auth()->id())->where('status','active')->get(),'clients'=>Client::where('user_id',auth()->id())->orderBy('name')->get()]); }
    public function store(SaveAuctionRequest $r, AuctionService $s) { $s->create($r->user(),$r->validated()); return back()->with('success','Remate creado en borrador.'); }
    public function open(Auction $auction) { Gate::authorize('update',$auction); if ($auction->status!=='draft') return back()->with('error','Solamente se abre un borrador.'); $auction->update(['status'=>'open']); return back()->with('success','Remate abierto.'); }
    public function bid(Request $r, Auction $auction, AuctionService $s) { Gate::authorize('update',$auction); $d=$r->validate(['client_id'=>['required',Rule::exists('clients','id')->where('user_id',$r->user()->id)],'amount'=>['required','numeric','between:0.01,1000000000']]); $s->bid($auction,$d['client_id'],(int)round($d['amount']*100)); return back()->with('success','Puja registrada.'); }
    public function close(Auction $auction,AuctionService $s) { Gate::authorize('update',$auction); $s->close($auction); return back()->with('success','Remate cerrado; el lote queda reservado al adjudicatario. Formaliza la venta mediante cotización.'); }
    public function snapshot() { Gate::authorize('viewAny',Auction::class); return response()->json(Auction::where('user_id',auth()->id())->where('status','open')->withMax('bids','amount_cents')->get(['id','title','status'])); }
}
