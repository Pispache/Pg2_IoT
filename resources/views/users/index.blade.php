@extends('layouts.app') @section('title','Usuarios') @section('header','Usuarios y roles') @section('content')<div class="page-title">
<div>
<h1>Gestión de usuarios</h1>
<p>Administración de accesos y roles del sistema.</p>
</div>
</div>
<div class="grid grid-2">
<div class="card">
<div class="card-header">
<h3>Crear usuario</h3>
</div>
<div class="card-body">
<form method="POST" action="{{ route('users.store') }}">@csrf<div class="form-grid">
<div class="form-group">
<label>Nombre</label>
<input class="form-control" name="name" required>
</div>
<div class="form-group">
<label>Correo</label>
<input class="form-control" type="email" name="email" required>
</div>
<div class="form-group">
<label>Rol</label>
<select class="form-select" name="role_id">@foreach($roles as $r)<option value="{{ $r->id }}">{{ $r->display_name }}</option>@endforeach</select>
</div>
<div class="form-group">
<label>Estado</label>
<label>
<input type="checkbox" name="is_active" value="1" checked> Activo</label>
</div>
<div class="form-group">
<label>Contraseña</label>
<input class="form-control" type="password" name="password" required>
</div>
<div class="form-group">
<label>Confirmar contraseña</label>
<input class="form-control" type="password" name="password_confirmation" required>
</div>
</div>
<div class="form-actions">
<button class="btn btn-primary">Crear usuario</button>
</div>
</form>
</div>
</div>
<div class="card">
<div class="card-header">
<h3>Usuarios registrados</h3>
</div>
<div class="table-wrap">
<table class="table">
<thead>
<tr>
<th>Usuario</th>
<th>Rol</th>
<th>Estado</th>
<th>Acciones</th>
</tr>
</thead>
<tbody>@foreach($users as $u)<tr>
<td>
<b>{{ $u->name }}</b>
<br>
<span class="muted">{{ $u->email }}</span>
</td>
<td>{{ $u->role->display_name }}</td>
<td>
<span class="badge {{ $u->is_active?'badge-success':'badge-neutral' }}">{{ $u->is_active?'Activo':'Inactivo' }}</span>
</td>
<td>
<x-edit-modal :id="'edit-user-'.$u->id" :title="'Editar usuario: '.$u->name" :action="route('users.update',$u)">
    <x-edit-field name="name" label="Nombre" :value="$u->name" maxlength="120" required />
    <x-edit-field name="email" label="Correo electrónico" type="email" :value="$u->email" maxlength="190" required />
    <x-edit-field name="role_id" label="Rol" type="select" :value="$u->role_id" :options="$roles->pluck('display_name','id')" :wide="true" required />
    <x-edit-field name="password" label="Nueva contraseña" type="password" autocomplete="new-password" minlength="8" help="Deje este campo vacío para mantener la contraseña actual." />
    <x-edit-field name="password_confirmation" label="Confirmar nueva contraseña" type="password" autocomplete="new-password" minlength="8" />
    <x-edit-field name="is_active" label="Usuario activo" type="checkbox" :value="$u->is_active" :wide="true" />
</x-edit-modal>@if(auth()->id()!==$u->id)<form class="inline" method="POST" action="{{ route('users.destroy',$u) }}" onsubmit="return confirm('¿Eliminar usuario?')">@csrf @method('DELETE')<button class="btn btn-danger btn-sm">Eliminar</button>
</form>@endif</td>
</tr>@endforeach</tbody>
</table>
</div>
</div>
</div>
<div style="margin-top:12px">{{ $users->links() }}</div>@endsection