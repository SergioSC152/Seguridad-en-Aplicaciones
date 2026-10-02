@extends('layouts.workspace')
@section('title',$title)
@section('content')
<section class="card p-4 mb-4"><h2 class="h5">{{ $editing ? 'Editar registro' : 'Nuevo registro' }}</h2>
<form method="POST" action="{{ $editing ? route($routeBase.'.update',[$routeParameter=>$editing->id]) : route($routeBase.'.store') }}" class="row g-3">@csrf @if($editing)@method('PUT')@endif
@foreach($fields as $key=>$field)
@php($value=old($key,$editing?->getAttribute($key)))
<div class="col-12 col-md-6"><label for="field-{{ $key }}" class="form-label">{{ $field[0] }}</label>
@if($field[1]==='select')<select class="form-select" id="field-{{ $key }}" name="{{ $key }}"><option value="">Seleccionar</option>@foreach($field[2] as $option=>$label)<option value="{{ $option }}" @selected((string)$value===(string)$option)>{{ $label }}</option>@endforeach</select>
@elseif($field[1]==='textarea')<textarea class="form-control" id="field-{{ $key }}" name="{{ $key }}" rows="3" maxlength="5000">{{ $value }}</textarea>
@else<input class="form-control" id="field-{{ $key }}" name="{{ $key }}" type="{{ $field[1] }}" @if($field[1]==='number') step="0.01" min="0" @endif value="{{ $value instanceof \DateTimeInterface ? $value->format('Y-m-d\TH:i') : $value }}">@endif
@error($key)<div class="text-danger small">{{ $message }}</div>@enderror</div>
@endforeach
<div class="col-12"><button class="btn btn-success">Guardar</button> @if($editing)<a href="{{ route($routeBase.'.index') }}" class="btn btn-outline-secondary">Cancelar</a>@endif</div></form></section>
<section class="card overflow-hidden"><div class="table-responsive"><table class="table mb-0"><thead><tr>@foreach(array_slice($fields,0,4,true) as $field)<th>{{ $field[0] }}</th>@endforeach<th>Acciones</th></tr></thead><tbody>
@forelse($records as $record)<tr>@foreach(array_slice($fields,0,4,true) as $key=>$field)<td>{{ $field[1]==='select' ? ($field[2][$record->getAttribute($key)] ?? '—') : ($record->getAttribute($key) ?? '—') }}</td>@endforeach<td class="text-nowrap"><a class="btn btn-sm btn-outline-success" href="{{ route($routeBase.'.edit',[$routeParameter=>$record->id]) }}">Editar</a><form class="d-inline" method="POST" action="{{ route($routeBase.'.destroy',[$routeParameter=>$record->id]) }}" onsubmit="return confirm('¿Eliminar el registro?')">@csrf @method('DELETE')<button class="btn btn-sm btn-outline-danger">Eliminar</button></form></td></tr>
@empty<tr><td colspan="5" class="text-center p-4">Todavía no hay registros.</td></tr>@endforelse
</tbody></table></div><div class="p-3">{{ $records->links('pagination::bootstrap-5') }}</div></section>
@endsection
