@extends('layouts.app') @section('title','Reglas de alerta') @section('header','Reglas de alerta') @section('content')<div class="page-title">
<div>
<h1>Reglas de alerta</h1>
<p>Defina umbrales específicos por dispositivo o por área.</p>
</div>
</div>
<div class="card">
<div class="card-header">
<h3>Nueva regla</h3>
</div>
<div class="card-body">
<form method="POST" action="{{ route('alert-rules.store') }}">@csrf<div class="form-grid">
<div class="form-group">
<label>Nombre</label>
<input class="form-control" name="name" required>
</div>
<div class="form-group">
<label>Variable</label>
<select class="form-select" name="variable">
<option value="temperature">Temperatura</option>
<option value="humidity">Humedad</option>
<option value="battery">Batería</option>
</select>
</div>
<div class="form-group">
<label>Dispositivo (opcional)</label>
<select class="form-select" name="device_id">
<option value="">—</option>@foreach($devices as $d)<option value="{{ $d->id }}">{{ $d->name }}</option>@endforeach</select>
</div>
<div class="form-group">
<label>Área (si no aplica a dispositivo)</label>
<select class="form-select" name="area_id">
<option value="">—</option>@foreach($areas as $a)<option value="{{ $a->id }}">{{ $a->name }}</option>@endforeach</select>
</div>
<div class="form-group">
<label>Mínimo</label>
<input class="form-control" type="number" step="0.1" name="min_value">
</div>
<div class="form-group">
<label>Máximo</label>
<input class="form-control" type="number" step="0.1" name="max_value">
</div>
<div class="form-group">
<label>Severidad</label>
<select class="form-select" name="severity">
<option value="low">Baja</option>
<option value="medium" selected>Media</option>
<option value="high">Alta</option>
<option value="critical">Crítica</option>
</select>
</div>
<div class="form-group">
<label>Estado</label>
<label>
<input type="checkbox" name="is_active" value="1" checked> Activa</label>
</div>
</div>
<div class="form-actions">
<button class="btn btn-primary">Crear regla</button>
</div>
</form>
</div>
</div>
<div class="card" style="margin-top:18px">
<div class="table-wrap">
<table class="table">
<thead>
<tr>
<th>Regla</th>
<th>Aplica a</th>
<th>Variable</th>
<th>Rango</th>
<th>Severidad</th>
<th>Estado</th>
<th>
</th>
</tr>
</thead>
<tbody>@foreach($rules as $r)<tr>
<td>{{ $r->name }}</td>
<td>{{ $r->device?->name ?? ($r->area?->name ?? 'General') }}</td>
<td>{{ ['temperature'=>'Temperatura','humidity'=>'Humedad','battery'=>'Batería'][$r->variable] ?? 'Desconocida' }}</td>
<td>{{ $r->min_value ?? '—' }} a {{ $r->max_value ?? '—' }}</td>
<td>
<span class="badge {{ in_array($r->severity,['high','critical'])?'badge-danger':'badge-warning' }}">{{ ['low'=>'Baja','medium'=>'Media','high'=>'Alta','critical'=>'Crítica'][$r->severity] ?? 'Desconocida' }}</span>
</td>
<td>{{ $r->is_active?'Activa':'Inactiva' }}</td>
<td>
<x-edit-modal :id="'edit-rule-'.$r->id" :title="'Editar regla: '.$r->name" :action="route('alert-rules.update',$r)">
    <x-edit-field name="name" label="Nombre" :value="$r->name" maxlength="120" required />
    <x-edit-field name="variable" label="Variable" type="select" :value="$r->variable" :options="['temperature'=>'Temperatura','humidity'=>'Humedad','battery'=>'Batería']" required />
    <x-edit-field name="device_id" label="Dispositivo (opcional)" type="select" :value="$r->device_id" :options="[''=>'Sin dispositivo específico'] + $devices->pluck('name','id')->all()" />
    <x-edit-field name="area_id" label="Área (opcional)" type="select" :value="$r->area_id" :options="[''=>'Sin área específica'] + $areas->pluck('name','id')->all()" />
    <x-edit-field name="min_value" label="Valor mínimo" type="number" :value="$r->min_value" step="any" />
    <x-edit-field name="max_value" label="Valor máximo" type="number" :value="$r->max_value" step="any" />
    <x-edit-field name="severity" label="Severidad" type="select" :value="$r->severity" :options="['low'=>'Baja','medium'=>'Media','high'=>'Alta','critical'=>'Crítica']" required />
    <x-edit-field name="is_active" label="Regla activa" type="checkbox" :value="$r->is_active" />
</x-edit-modal>
<form class="inline" method="POST" action="{{ route('alert-rules.destroy',$r) }}" onsubmit="return confirm('¿Eliminar regla?')">@csrf @method('DELETE')<button class="btn btn-danger btn-sm">Eliminar</button>
</form>
</td>
</tr>@endforeach</tbody>
</table>
</div>
</div>@endsection