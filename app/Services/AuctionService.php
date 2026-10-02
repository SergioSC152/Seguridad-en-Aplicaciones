<?php
namespace App\Services;
use App\Models\Auction;
use App\Models\Client;
use App\Models\LivestockBatch;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
class AuctionService
{
    public function create(User $user,array $data): Auction
    {
        return DB::transaction(function () use ($user,$data) {
            $batch=LivestockBatch::where('user_id',$user->id)->whereKey($data['livestock_batch_id'])->lockForUpdate()->firstOrFail();
            if ($batch->status!=='active' || Auction::where('livestock_batch_id',$batch->id)->whereIn('status',['draft','open'])->exists()) throw ValidationException::withMessages(['livestock_batch_id'=>'El lote no está disponible o ya tiene un remate abierto.']);
            $batch->update(['availability'=>'auction']);
            return Auction::create(['user_id'=>$user->id,'livestock_batch_id'=>$batch->id,'title'=>$data['title'],'reserve_cents'=>(int)round($data['reserve']*100)]);
        });
    }
    public function bid(Auction $auction,int $clientId,int $cents): void
    {
        DB::transaction(function () use ($auction,$clientId,$cents) {
            $auction=Auction::whereKey($auction->id)->lockForUpdate()->firstOrFail();
            if ($auction->status!=='open') throw ValidationException::withMessages(['bid'=>'El remate no está abierto.']);
            Client::where('user_id',$auction->user_id)->whereKey($clientId)->firstOrFail();
            $highest=(int)$auction->bids()->max('amount_cents');
            if ($cents < $auction->reserve_cents || ($highest && $cents <= $highest)) throw ValidationException::withMessages(['bid'=>'La puja debe superar la anterior y cubrir la reserva.']);
            $auction->bids()->create(['client_id'=>$clientId,'amount_cents'=>$cents]);
            $auction->batch->update(['availability'=>'bidding']);
        });
    }
    public function close(Auction $auction): void
    {
        DB::transaction(function () use ($auction) {
            $auction=Auction::whereKey($auction->id)->lockForUpdate()->firstOrFail();
            if ($auction->status!=='open') throw ValidationException::withMessages(['auction'=>'El remate no está abierto.']);
            $bid=$auction->bids()->orderByDesc('amount_cents')->first();
            $auction->update(['status'=>$bid?'awarded':'closed','winner_client_id'=>$bid?->client_id,'winning_cents'=>$bid?->amount_cents]);
            $auction->batch->update(['availability'=>$bid?'reserved':'available']);
        });
    }
}
