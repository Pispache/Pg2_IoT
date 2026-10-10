@props(['id', 'title', 'action'])
@php($hasErrors = $errors->any() && old('_edit_modal') === $id)
<button type="button" class="btn btn-secondary btn-sm" data-modal-open="{{ $id }}" aria-haspopup="dialog" aria-controls="{{ $id }}">Editar</button>
@push('modals')
<dialog id="{{ $id }}" class="edit-modal" aria-labelledby="{{ $id }}-title" @if($hasErrors) data-open-on-load @endif>
    <header class="modal-header">
        <div>
            <h2 id="{{ $id }}-title">{{ $title }}</h2>
            <p>Actualice la información y guarde los cambios.</p>
        </div>
        <button type="button" class="modal-close" data-modal-close aria-label="Cerrar edición">&times;</button>
    </header>
    <form method="POST" action="{{ $action }}" class="modal-form">
        @csrf
        @method('PUT')
        <input type="hidden" name="_edit_modal" value="{{ $id }}">
        <div class="modal-body">
            @if($hasErrors)
                <div class="alert-box alert-error" role="alert">
                    <b>Revise los campos antes de guardar.</b>
                    <ul>@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
                </div>
            @endif
            <div class="form-grid">{{ $slot }}</div>
        </div>
        <footer class="modal-footer">
            <button type="button" class="btn btn-secondary" data-modal-close>Cancelar</button>
            <button type="submit" class="btn btn-primary">Guardar cambios</button>
        </footer>
    </form>
</dialog>
@endpush
