@extends('layouts.workspace')
@section('title','Ventas, cartera y despacho')
@section('content')
<p class="text-secondary">Las ventas se registran desde cotizaciones aceptadas. Los pagos y la guía se registran manualmente; no emite factura electrónica ni guía oficial.</p>
@forelse($sales as $sale)<section class="card p-4 mb-3"><div class="d-flex justify-content-between"><h2 class="h5">{{ $sale->quote->number }} · {{ $sale->client->name }}</h2><strong>$ {{ number_format($sale->total_cents/100,2,',','.') }}</strong></div><p class="small text-secondary">Saldo: $ {{ number_format(($sale->total_cents-$sale->paid_cents)/100,2,',','.') }}</p><form class="row g-3" method="POST" action="{{ route('admin.sales.update',$sale) }}">@csrf @method('PUT')
<div class="col-md-3"><label class="form-label">Total pagado COP<input class="form-control" type="number" name="paid" min="0" max="{{ $sale->total_cents/100 }}" step="0.01" value="{{ $sale->paid_cents/100 }}" required></label></div>
<div class="col-md-3"><label class="form-label">Fecha de venta<input class="form-control" type="date" name="sold_at" value="{{ $sale->sold_at->format('Y-m-d') }}" required></label></div>
<div class="col-md-3"><label class="form-label">Referencia guía ICA<input class="form-control" name="ica_guide" value="{{ $sale->ica_guide }}" maxlength="100"></label></div>
<div class="col-md-3"><label class="form-label">Despacho (UTC)<input class="form-control" type="datetime-local" name="dispatched_at" value="{{ $sale->dispatched_at?->format('Y-m-d\TH:i') }}"></label></div><div class="col-12"><button class="btn btn-success">Guardar</button></div></form></section>@empty<div class="card p-4">Todavía no hay ventas. Primero consigue la aceptación de una cotización.</div>@endforelse
{{ $sales->links('pagination::bootstrap-5') }}
@endsection
