@extends('layouts.app') @section('title','Bodegas') @section('header','Bodegas') @section('content')<div class="page-title">
<div>
<h1>Bodegas</h1>
<p>Ubicaciones físicas donde se realiza el monitoreo.</p>
</div>
</div>
<div class="grid grid-2">
<div class="card">
<div class="card-header">
<h3>Nueva bodega</h3>
</div>
<div class="card-body">
<form method="POST" action="{{ route('warehouses.store') }}">@csrf<div class="form-grid">
<div class="form-group">
<label>Nombre</label>
<input class="form-control" name="name" required>
</div>
<div class="form-group">
<label>Código</label>
<input class="form-control" name="code" required>
</div>
</div>
<div class="form-group" style="margin-top:12px">
<label>Dirección</label>
<input class="form-control" name="address">
</div>
<div class="form-group" style="margin-top:12px">
<label>Descripción</label>
<textarea class="form-control" name="description">
</textarea>
</div>
<label style="display:block;margin-top:10px">
<input type="checkbox" name="is_active" value="1" checked> Activa</label>
<div class="form-actions">
<button class="btn btn-primary">Guardar</button>
</div>
</form>
</div>
</div>
<div class="card">
<div class="card-header">
<h3>Listado</h3>
</div>
<div class="table-wrap">
<table class="table">
<thead>
<tr>
<th>Bodega</th>
<th>Áreas</th>
<th>Estado</th>
<th>
</th>
</tr>
</thead>
<tbody>@foreach($warehouses as $w)<tr>
<td>
<b>{{ $w->name }}</b>
<br>
<span class="muted mono">{{ $w->code }}</span>
<br>
<span class="muted">{{ $w->address }}</span>
</td>
<td>{{ $w->areas_count }}</td>
<td>
<span class="badge {{ $w->is_active?'badge-success':'badge-neutral' }}">{{ $w->is_active?'Activa':'Inactiva' }}</span>
</td>
<td>
<x-edit-modal :id="'edit-warehouse-'.$w->id" :title="'Editar bodega: '.$w->name" :action="route('warehouses.update',$w)">
    <x-edit-field name="name" label="Nombre" :value="$w->name" maxlength="120" required />
    <x-edit-field name="code" label="Código" :value="$w->code" maxlength="50" required />
    <x-edit-field name="address" label="Dirección" :value="$w->address" maxlength="255" :wide="true" />
    <x-edit-field name="description" label="Descripción" type="textarea" :value="$w->description" maxlength="2000" :wide="true" />
    <x-edit-field name="is_active" label="Bodega activa" type="checkbox" :value="$w->is_active" :wide="true" />
</x-edit-modal>
<form class="inline" method="POST" action="{{ route('warehouses.destroy',$w) }}" onsubmit="return confirm('¿Eliminar bodega?')">@csrf @method('DELETE')<button class="btn btn-danger btn-sm">Eliminar</button>
</form>
</td>
</tr>@endforeach</tbody>
</table>
</div>
</div>
</div>@endsection