@extends('layouts.app') @section('title','Dispositivos') @section('header','Dispositivos IoT') @section('content')<div class="page-title">
<div>
<h1>Dispositivos IoT</h1>
<p>Registro, asignación, credenciales y estado de los nodos Wemos D1 Mini.</p>
</div>
</div>
<div class="card">
<div class="card-header">
<h3>Registrar dispositivo</h3>
</div>
<div class="card-body">
<div class="section-note">Al guardar se genera un token secreto. Debe copiarlo al firmware del dispositivo; por seguridad solo se muestra una vez.</div>
<form method="POST" action="{{ route('devices.store') }}">@csrf<div class="form-grid">
<div class="form-group">
<label>Área</label>
<select class="form-select" name="area_id">@foreach($areas as $a)<option value="{{ $a->id }}">{{ $a->warehouse->name }} / {{ $a->name }}</option>@endforeach</select>
</div>
<div class="form-group">
<label>Nombre</label>
<input class="form-control" name="name" placeholder="Sensor Cámara 1" required>
</div>
<div class="form-group">
<label>Serial</label>
<input class="form-control mono" name="serial" placeholder="IOT-BOD-001" required>
</div>
<div class="form-group">
<label>Estado</label>
<select class="form-select" name="status">
<option value="active">Activo</option>
<option value="maintenance">Mantenimiento</option>
<option value="inactive">Inactivo</option>
</select>
</div>
<div class="form-group">
<label>Intervalo de lectura (min)</label>
<input class="form-control" type="number" name="reading_interval_minutes" min="1" max="1440" value="60">
</div>
<div class="form-group">
<label>Firmware</label>
<input class="form-control" name="firmware_version" placeholder="1.0.0">
</div>
</div>
<div class="form-group" style="margin-top:12px">
<label>Notas</label>
<textarea class="form-control" name="notes">
</textarea>
</div>
<div class="form-actions">
<button class="btn btn-primary">Registrar y generar token</button>
</div>
</form>
</div>
</div>
<div class="card" style="margin-top:18px">
<div class="card-header">
<h3>Dispositivos registrados</h3>
</div>
<div class="table-wrap">
<table class="table">
<thead>
<tr>
<th>Dispositivo</th>
<th>Ubicación</th>
<th>Última lectura</th>
<th>Última conexión</th>
<th>Token</th>
<th>Acciones</th>
</tr>
</thead>
<tbody>@foreach($devices as $d)<tr>
<td>
<a href="{{ route('devices.show',$d) }}">
<b>{{ $d->name }}</b>
</a>
<br>
<span class="mono muted">{{ $d->serial }}</span>
<br>
<span class="badge {{ $d->isOnline()?'badge-success':'badge-neutral' }}">{{ $d->isOnline()?'En línea':$d->status }}</span>
</td>
<td>{{ $d->area->warehouse->name }}<br>
<span class="muted">{{ $d->area->name }}</span>
</td>
<td>{{ $d->latestReading?->temperature ?? '—' }} °C / {{ $d->latestReading?->humidity ?? '—' }} %</td>
<td>{{ $d->last_seen_at?->format('d/m/Y H:i') ?? 'Nunca' }}</td>
<td class="mono">••••{{ $d->token_last4 }}</td>
<td>
<a class="btn btn-secondary btn-sm" href="{{ route('devices.show',$d) }}">Ver</a>
<form class="inline" method="POST" action="{{ route('devices.token',$d) }}" onsubmit="return confirm('El token anterior dejará de funcionar. ¿Continuar?')">@csrf<button class="btn btn-warning btn-sm">Nuevo token</button>
</form>
<x-edit-modal :id="'edit-device-'.$d->id" :title="'Editar dispositivo: '.$d->name" :action="route('devices.update',$d)">
    <x-edit-field name="area_id" label="Área" type="select" :value="$d->area_id" :options="$areas->mapWithKeys(fn($area) => [$area->id => $area->warehouse->name.' / '.$area->name])->put($d->area_id, $d->area->warehouse->name.' / '.$d->area->name)" :wide="true" required />
    <x-edit-field name="name" label="Nombre" :value="$d->name" maxlength="120" required />
    <x-edit-field name="serial" label="Serial" :value="$d->serial" maxlength="120" required />
    <x-edit-field name="status" label="Estado" type="select" :value="$d->status" :options="['active'=>'Activo','maintenance'=>'Mantenimiento','inactive'=>'Inactivo']" required />
    <x-edit-field name="reading_interval_minutes" label="Intervalo de lectura (min)" type="number" :value="$d->reading_interval_minutes" min="1" max="1440" required />
    <x-edit-field name="firmware_version" label="Versión del firmware" :value="$d->firmware_version" maxlength="50" />
    <x-edit-field name="notes" label="Notas" type="textarea" :value="$d->notes" maxlength="2000" :wide="true" />
</x-edit-modal>
</td>
</tr>@endforeach</tbody>
</table>
</div>
</div>@endsection