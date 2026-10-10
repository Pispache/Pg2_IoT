@extends('layouts.app') @section('title','Áreas') @section('header','Áreas de monitoreo') @section('content')<div class="page-title">
<div>
<h1>Áreas</h1>
<p>Configure los rangos ambientales de cada área.</p>
</div>
</div>
<div class="card">
<div class="card-header">
<h3>Nueva área</h3>
</div>
<div class="card-body">
<form method="POST" action="{{ route('areas.store') }}">@csrf<div class="form-grid">
<div class="form-group">
<label>Bodega</label>
<select class="form-select" name="warehouse_id">@foreach($warehouses as $w)<option value="{{ $w->id }}">{{ $w->name }}</option>@endforeach</select>
</div>
<div class="form-group">
<label>Nombre</label>
<input class="form-control" name="name" required>
</div>
<div class="form-group">
<label>Código</label>
<input class="form-control" name="code" required>
</div>
<div class="form-group">
<label>Descripción</label>
<input class="form-control" name="description">
</div>
<div class="form-group">
<label>Temperatura mínima °C</label>
<input class="form-control" type="number" step="0.1" name="min_temperature" value="15">
</div>
<div class="form-group">
<label>Temperatura máxima °C</label>
<input class="form-control" type="number" step="0.1" name="max_temperature" value="30">
</div>
<div class="form-group">
<label>Humedad mínima %</label>
<input class="form-control" type="number" step="0.1" name="min_humidity" value="30">
</div>
<div class="form-group">
<label>Humedad máxima %</label>
<input class="form-control" type="number" step="0.1" name="max_humidity" value="70">
</div>
</div>
<label style="display:block;margin-top:10px">
<input type="checkbox" name="is_active" value="1" checked> Activa</label>
<div class="form-actions">
<button class="btn btn-primary">Guardar área</button>
</div>
</form>
</div>
</div>
<div class="card" style="margin-top:18px">
<div class="card-header">
<h3>Áreas registradas</h3>
</div>
<div class="table-wrap">
<table class="table">
<thead>
<tr>
<th>Área</th>
<th>Bodega</th>
<th>Temperatura</th>
<th>Humedad</th>
<th>Dispositivos</th>
<th>
</th>
</tr>
</thead>
<tbody>@foreach($areas as $a)<tr>
<td>
<b>{{ $a->name }}</b>
<br>
<span class="mono muted">{{ $a->code }}</span>
</td>
<td>{{ $a->warehouse->name }}</td>
<td>{{ $a->min_temperature }}–{{ $a->max_temperature }} °C</td>
<td>{{ $a->min_humidity }}–{{ $a->max_humidity }} %</td>
<td>{{ $a->devices_count }}</td>
<td>
<x-edit-modal :id="'edit-area-'.$a->id" :title="'Editar área: '.$a->name" :action="route('areas.update',$a)">
    <x-edit-field name="warehouse_id" label="Bodega" type="select" :value="$a->warehouse_id" :options="$warehouses->pluck('name','id')->put($a->warehouse_id,$a->warehouse->name)" :wide="true" required />
    <x-edit-field name="name" label="Nombre" :value="$a->name" maxlength="120" required />
    <x-edit-field name="code" label="Código" :value="$a->code" maxlength="50" required />
    <x-edit-field name="description" label="Descripción" type="textarea" :value="$a->description" maxlength="2000" :wide="true" />
    <x-edit-field name="min_temperature" label="Temperatura mínima (°C)" type="number" :value="$a->min_temperature" step="any" min="-40" max="80" />
    <x-edit-field name="max_temperature" label="Temperatura máxima (°C)" type="number" :value="$a->max_temperature" step="any" min="-40" max="80" />
    <x-edit-field name="min_humidity" label="Humedad mínima (%)" type="number" :value="$a->min_humidity" step="any" min="0" max="100" />
    <x-edit-field name="max_humidity" label="Humedad máxima (%)" type="number" :value="$a->max_humidity" step="any" min="0" max="100" />
    <x-edit-field name="is_active" label="Área activa" type="checkbox" :value="$a->is_active" :wide="true" />
</x-edit-modal>
<form class="inline" method="POST" action="{{ route('areas.destroy',$a) }}" onsubmit="return confirm('¿Eliminar área?')">@csrf @method('DELETE')<button class="btn btn-danger btn-sm">Eliminar</button>
</form>
</td>
</tr>@endforeach</tbody>
</table>
</div>
</div>@endsection