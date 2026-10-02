<?php
namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use App\Models\Client;
use App\Models\Quote;
use App\Models\Sale;
use App\Models\SalesOpportunity;
class InsightsController extends Controller
{
    public function index() {
        $user=auth()->user(); abort_unless($user->hasPermissionTo('sales.manage') || $user->hasPermissionTo('sales-pipeline.manage') || $user->hasPermissionTo('clients.manage'),403);
        $opportunities=$user->hasPermissionTo('sales-pipeline.manage')?$user->salesOpportunities()->get():collect();
        $sales=$user->hasPermissionTo('sales.manage')?Sale::where('user_id',$user->id)->with('quote')->get():collect();
        $cycles=$sales->filter(fn($s)=>filled($s->guide_recorded_at))->map(fn($s)=>$s->quote->created_at->diffInDays(\Illuminate\Support\Carbon::parse($s->guide_recorded_at)));
        $vip=$user->hasPermissionTo('clients.manage') && $user->hasPermissionTo('sales.manage') ? Client::where('user_id',$user->id)->where('vip',true)->where('status','active')->whereDoesntHave('sales',fn($q)=>$q->where('sold_at','>=',today()->subDays(45)))->get() : collect();
        $prices=$user->hasPermissionTo('quotes.manage')?Quote::where('user_id',$user->id)->where('status','accepted')->with('batch.category')->latest()->limit(500)->get()->filter(fn($q)=>$q->batch)->groupBy(fn($q)=>$q->created_at->format('Y-m').' / '.$q->batch->category->name)->map(fn($group)=>round($group->avg('price_per_kg'),2)):collect();
        return view('admin.insights.index',['opportunities'=>$opportunities,'sales'=>$sales,'cycles'=>$cycles,'vip'=>$vip,'prices'=>$prices]);
    }
}
